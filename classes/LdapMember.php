<?php 

namespace HeimrichHannot;

class LdapMember extends Ldap
{
	protected static $arrAttributes = array('uid', 'mail', 'cn', 'givenname', 'sn', 'c');
	
	/**
	 * Always reset the password on login to random value, otherwise checkCredentialsHook will not be triggered
	 * @param String - Input Username $username
	 * @param String - Input Password $password
	 * @param unknown $objMember
	 * @return boolean
	 */
	public static function resetPassword(&$objMember, $strUsername)
	{
		// store randomized password, so contao will always trigger the checkCredentials HOOK
		$objMember->password = md5(time() . $strUsername);
		$objMember->save();
	}
	
	public static function doImportMember($strUsername, $arrSelectedLdapMemberGroups)
	{
		$objLdapUser = LdapMemberModel::findLdapMember($strUsername);
		
		$arrSkipUids = trimsplit(',', $GLOBALS['TL_CONFIG']['ldap_uid_skip']);
		
		// skip uids from config
		if (in_array($objLdapUser->uid[0], $arrSkipUids)) return false;

		// no null check necessary (already checked beforehands)
		LdapMemberModel::addMember(
			$objLdapUser->givenname['count'] > 0 ? $objLdapUser->givenname[0] : '',
			$objLdapUser->sn['count'] > 0 ? $objLdapUser->sn[0] : '',
			$objLdapUser->uid['count'] > 0 ? $objLdapUser->uid[0] : '',
			$objLdapUser->mail['count'] > 0 ? $objLdapUser->mail[0] : '',
			$strUsername,
			LdapMemberGroupModel::getLocalMemberGroupIds(
				array_intersect(LdapMemberModel::getLdapMemberGroupsByUid($objLdapUser->uid[0]), $arrSelectedLdapMemberGroups)
			),
			$objLdapUser->c[0] ? strtolower($objLdapUser->c[0]) : 'de'
		);
	}
	
	public static function doUpdateMember($objMember, $uid, $mail, $firstname, $lastname, $arrSelectedLdapMemberGroups)
	{
		$arrMemberGroups = deserialize($objMember->groups, true);
		$objLocalLdapMemberGroups = \MemberGroupModel::findBy(array('(tl_member_group.ldapGid > 0)'), null, array());
		$arrLdapMemberGroups = LdapMemberModel::getLdapMemberGroupsByUid($uid);
		if ($objLocalLdapMemberGroups !== null)
		{
			$arrLocalLdapMemberGroups = $objLocalLdapMemberGroups->fetchEach('ldapGid');
			$objMember->ldapUid = $uid;
			$objMember->email = $mail;
			$objMember->firstname = $firstname;
			$objMember->lastname = $lastname;
			$objMember->username = $GLOBALS['TL_CONFIG']['ldap_uid'] == 'mail' ? $objMember->email : $objMember->ldapUid;
			// merge non ldap contao groups with assigned (and active!) remote ldap groups
			$objMember->groups = serialize(array_merge(
				array_diff($arrMemberGroups, array_keys($arrLocalLdapMemberGroups)), // non ldap contao groups
				LdapMemberGroupModel::getLocalMemberGroupIds(array_intersect($arrLdapMemberGroups, $arrSelectedLdapMemberGroups))
			));
			
			if (isset($GLOBALS['TL_HOOKS']['ldapUpdateMember']) && is_array($GLOBALS['TL_HOOKS']['ldapUpdateMember']))
			{
				foreach ($GLOBALS['TL_HOOKS']['ldapUpdateMember'] as $callback)
				{
					$objMember = call_user_func(array($callback[0], $callback[1]), $objMember);
				}
			}
			
			$objMember->save();
		}
	}
	
	public static function authenticateLdapMember($strUsername, $strPassword)
	{
		$objLdapUser = LdapMemberModel::findLdapMember($strUsername);
		if ($objLdapUser) {
			if (!@ldap_bind(Ldap::getConnection(), $objLdapUser->dn, $strPassword))
			{
				$errno = ldap_errno(Ldap::getConnection());
					
				switch($errno)
				{
					case static::LDAP_INVALID_CREDENTIALS:
						return false;
				}
					
				return false;
			}
				
			// ldap account requires an valid email and uid
			if ($objLdapUser->uid['count'] == 0 || $objLdapUser->mail['count'] == 0)
			{
				\Message::addError($GLOBALS['TL_LANG']['MSC']['ldap']['emailUidMissing']);
				return false;
			}
				
			return true;
		} else
			return false;
	}
	
	public static function updateMembers($arrSelectedLdapMemberGroups)
	{
		// ldap members
		$objLdapMembers = LdapMemberModel::getLdapMembers();
	
		if (!$objLdapMembers)
			return false;
		
		// add/update new members
		$foundLdapMemberUids = array();
		
		// search filter for mail wildcard and store domain
		preg_match('#@(?P<domain>[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5})#is', $GLOBALS['TL_CONFIG']['ldap_filter_person'], $matches);
		$mailDomain = $matches['domain'];
		
		foreach ($objLdapMembers as $k => $v)
		{
			if (isset($v[$GLOBALS['TL_CONFIG']['ldap_uid']]) && isset($v[$GLOBALS['TL_CONFIG']['ldap_uid']][0]))
			{
				$uid = $v['uid'][0];
				
				$mail = $v['mail'][0];
				$uidMail = $v[$GLOBALS['TL_CONFIG']['ldap_uid']][0];
				
				// multiple emails found
				if($v['mail']['count'] > 1 && !empty($mailDomain))
				{
					$arrMailFilter = preg_grep("#(.*)$mailDomain#", $v['mail']); // perform maildomain rgxp against $mailDomain
					$mail = current($arrMailFilter); // take first mail, that fits domain regxp
					$uidMail = $mail;
				}
				
				$firstname = $v['givenname'][0];
				$lastname = $v['sn'][0];
				
				$foundLdapMemberUids[] = $uid;
				
				$objMember = \MemberModel::findBy(array("email = '$mail' OR ldapUid = '$uid'"), array());
				if ($objMember === null)
				{
					static::doImportMember($uidMail, $arrSelectedLdapMemberGroups);
				}
				else
				{
					static::doUpdateMember($objMember, $uid, $mail, $firstname, $lastname, $arrSelectedLdapMemberGroups);
				}
			}
		}
		
		// mark remotely non existing local ldap members as disabled
		$objLocalMembers = \MemberModel::findAll();
		if ($objLocalMembers !== null)
		{
			while ($objLocalMembers->next())
			{
				if ($objLocalMembers->ldapUid && !in_array($objLocalMembers->ldapUid, $foundLdapMemberUids))
				{
					$objLocalMembers->disable = true;
					$objLocalMembers->save();
				} else {
					$objLocalMembers->disable = false;
					$objLocalMembers->save();
				}
			}
		}
	}
	
	public static function getAttributes()
	{
		return static::$arrAttributes;
	}
}
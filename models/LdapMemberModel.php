<?php 

namespace HeimrichHannot;

class LdapMemberModel extends \MemberModel
{
	public static function getLdapMembers()
	{
		if (Ldap::getConnection())
		{
			$query = ldap_search(Ldap::getConnection(), 'CN=users,' . $GLOBALS['TL_CONFIG']['ldap_base'], $GLOBALS['TL_CONFIG']['ldap_filter_person'], LdapMember::getAttributes());
				
			if (!$query)
				return false;
				
			$found = ldap_get_entries(Ldap::getConnection(), $query);
			
			// groups not found
			if (!is_array($found) || count($found) <= 0)
				return false;
				
			return $found;
		} else
			return false;
	}
	
	public static function addMember($strFirstname, $strLastname, $strUid, $strEmail, $strUsername, $arrGroups, $strLanguage)
	{
		$objMember = new \MemberModel();
		// store randomized password, so contao will always trigger the checkCredentials hook
		$time = time();
		$objMember->tstamp = $time;
		$objMember->dateAdded = $time;
		$objMember->firstname = $strFirstname;
		$objMember->lastname = $strLastname;
		$objMember->ldapUid = $strUid;
		$objMember->email = $strEmail;
		$objMember->login = true;
		$objMember->username = $strUsername;
		$objMember->password = md5($time . $strUsername);
		$objMember->groups = serialize($arrGroups);
		$objMember->language = $strLanguage;
		$objMember->save();
		
		if (isset($GLOBALS['TL_HOOKS']['ldapAddMember']) && is_array($GLOBALS['TL_HOOKS']['ldapAddMember']))
		{
			foreach ($GLOBALS['TL_HOOKS']['ldapAddMember'] as $callback)
			{
				$objMember = call_user_func(array($callback[0], $callback[1]), $objMember);
			}
		}
		
		$objMember->save();
	}
	
	public static function findLdapMember($strUsername)
	{
		if (Ldap::getConnection()) {
			$user_name_filter = $GLOBALS['TL_CONFIG']['ldap_uid'] . '=' . $strUsername;
			
			$filter = '(&(' . $user_name_filter . ')' . $GLOBALS['TL_CONFIG']['ldap_filter_person'] . ')';
			
			// search by username
			$query = ldap_search(Ldap::getConnection(), $GLOBALS['TL_CONFIG']['ldap_base'], $filter, LdapMember::getAttributes());
			
			if (!$query)
				return null;
			
			$found = ldap_get_entries(Ldap::getConnection(), $query);
			
			// user not found
			if (!is_array($found) || count($found) <= 0)
				return null;
			
			$found = (object) $found[0];
			
			return $found;
		} else
			return null;
	}
	
	public static function getLdapMemberGroupsByUid($uid)
	{
		$arrMemberGroups = LdapMemberGroupModel::getLdapMemberGroups();
	
		$arrOptions = array();
	
		if(!is_array($arrMemberGroups) || empty($arrMemberGroups))
			return $arrOptions;
	
		foreach($arrMemberGroups as $key => $group)
		{
			if(!isset($group['memberuid']) || !is_array($group['memberuid'])) continue;
	
			$idx = array_search($uid, $group['memberuid']);
	
			if(!$idx || !isset($group['gidnumber'][0])) continue;
	
			$arrOptions[] = $group['gidnumber'][0];
		}
	
		return $arrOptions;
	}
}
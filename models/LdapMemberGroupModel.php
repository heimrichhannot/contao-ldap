<?php 

namespace HeimrichHannot;

class LdapMemberGroupModel extends \MemberGroupModel
{
	public static function getLdapMemberGroups()
	{
		if (Ldap::getConnection())
		{
			$query = ldap_search(Ldap::getConnection(), 'CN=groups,' . $GLOBALS['TL_CONFIG']['ldap_base'], "(objectClass=*)", LdapMemberGroup::getAttributes());
			
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
	
	public static function getLdapMemberGroupsAsOptions()
	{
		$arrGroups = static::getLdapMemberGroups();
	
		$arrOptions = array();
	
		if(!is_array($arrGroups) || empty($arrGroups)) return $arrOptions;
	
		foreach($arrGroups as $key => $group)
		{
			$cn = $group['cn'][0];
	
			if($key == 'count' || $cn == 'groups') continue;
				
			$arrOptions[$group['gidnumber'][0]] = $group['cn'][0];
		}
	
		return $arrOptions;
	}
	
	public static function getLocalMemberGroupIds($arrLdapMemberGroupIds) {
		$arrResult = array();
		foreach ($arrLdapMemberGroupIds as $currentGid)
		{
			$objMemberGroup = \MemberGroupModel::findBy('ldapGid', $currentGid);
			if ($objMemberGroup !== null)
			{
				$arrResult[] = $objMemberGroup->id;
			}
		}
		return $arrResult;
	}

}
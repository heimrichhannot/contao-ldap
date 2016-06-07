<?php 

namespace HeimrichHannot;

class LdapMemberGroup extends Ldap
{
	protected static $arrAttributes = array('gidnumber', 'cn', 'memberuid');
	
	/**
	 * Create/Update ldap group as tl_member_group
	 * @param serialized array $varValue
	 * @return serialized array $varValue
	 */
	public static function updateMemberGroups($varValue)
	{
		$arrSelectedLdapMemberGroups = deserialize($varValue, true);
	
		if(!empty($arrSelectedLdapMemberGroups))
		{
			$arrLdapMemberGroups = LdapMemberGroupModel::getLdapMemberGroups();
			
			if (!is_array($arrLdapMemberGroups) || empty($arrLdapMemberGroups))
				return $varValue;
			
			// ldap groups
			foreach ($arrLdapMemberGroups as $k => $v)
			{
				// selected ldap groups in settings
				foreach ($arrSelectedLdapMemberGroups as $gid)
				{
					if (isset($v['gidnumber']) && $v['gidnumber'][0] == $gid)
					{
						$objMemberGroup = \MemberGroupModel::findBy('ldapGid', $gid);
						
						if($objMemberGroup === null)
						{
							$objMemberGroup = new \MemberGroupModel();
							$objMemberGroup->ldapGid = $gid;
						}
						
						$objMemberGroup->tstamp = time();
						// name
						if (isset($v['cn']))
							$objMemberGroup->name = $v['cn'][0];
						else
							$objMemberGroup->name = $gid;
						$objMemberGroup->save();
					}
				}
			}
			LdapMember::updateMembers($arrSelectedLdapMemberGroups);
		}
	
		return $varValue;
	}
	
	public static function getAttributes()
	{
		return static::$arrAttributes;
	}
}
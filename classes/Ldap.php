<?php 

namespace HeimrichHannot;

class Ldap extends \System
{
	protected static $connection;
	const LDAP_INVALID_CREDENTIALS = 49;
	// ldap property => member property
	public static $loginProperties = array('uid' => 'ldapUid', 'mail' => 'email');
	
	public static function connect()
	{
		if (isset($GLOBALS['TL_CONFIG']['ldap_host']) && isset($GLOBALS['TL_CONFIG']['ldap_port'])
			&& isset($GLOBALS['TL_CONFIG']['ldap_binddn']) && isset($GLOBALS['TL_CONFIG']['ldap_password']))
		{
			$connection = ldap_connect($GLOBALS['TL_CONFIG']['ldap_host'], $GLOBALS['TL_CONFIG']['ldap_port']) or die ("Could not connect to LDAP server.");
			
			if (!is_resource($connection)) return false;
			
			ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
			
			// check if bind dn can connect to ldap server
			if(!@ldap_bind($connection, $GLOBALS['TL_CONFIG']['ldap_binddn'], $GLOBALS['TL_CONFIG']['ldap_password']))
			{
				return false;
			}
			
			static::$connection = $connection;
			
			return true;
		} else
			return false;
	}
	
	/**
	 * Check Credentials, ldap password != contao password
	 * @param String - Input Username $username
	 * @param String - Input Password $password
	 * @param unknown $objMember
	 */
	public function checkCredentialsHook($strUsername, $strPassword, $objMember)
	{
		// store randomized password, so contao will always trigger the checkCredentials HOOK
		$objMember1 = \MemberModel::findByPk($objMember->id);
		LdapMember::resetPassword($objMember1, $strUsername);
		if (LdapMember::authenticateLdapMember($strUsername, $strPassword))
		{
			LdapMember::doUpdateMember($objMember1, $objMember1->ldapUid, $objMember1->email, $objMember1->firstname, $objMember1->lastname, deserialize($GLOBALS['TL_CONFIG']['ldap_groups'], true));
			return true;
		} else
			return false;
	}
	
	/**
	 * Import User from LDAP
	 * @param String - Input Username $username
	 * @param String - Input Password $password
	 * @param String - Parent Table $strTable
	 * @return boolean login status
	 */
	public function importUserHook($strUsername, $strPassword, $strTable)
	{
		if (LdapMember::authenticateLdapMember($strUsername, $strPassword)) {
			LdapMember::doImportMember($strUsername, deserialize($GLOBALS['TL_CONFIG']['ldap_groups'], true));
			return true;
		} else
			return false;
	}
	
	public static function getConnection()
	{
		if (static::$connection)
			return static::$connection;
		else
		{
			static::connect();
			return static::$connection;
		}
	}
}
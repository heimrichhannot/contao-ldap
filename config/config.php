<?php

/**
 * Hooks
 */
if($GLOBALS['TL_CONFIG']['ldap'])
{
	$GLOBALS['TL_HOOKS']['importUser'][] = array('HeimrichHannot\Ldap', 'importUserHook');
	$GLOBALS['TL_HOOKS']['checkCredentials'][] = array('HeimrichHannot\Ldap', 'checkCredentialsHook');
	
	/**
	 * FE Modules
	 */
	$GLOBALS['FE_MOD']['user']['login'] = 'HeimrichHannot\ModuleLdapLogin';
}
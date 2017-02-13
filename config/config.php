<?php

/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['user'][\HeimrichHannot\Ldap\Ldap::MODULE_LDAP_LOGIN] = 'HeimrichHannot\Ldap\ModuleLdapLogin';

/**
 * Hooks
 */
if (TL_MODE == 'FE' && \Config::get('addLdapForMembers'))
{
    // order is correct
    $GLOBALS['TL_HOOKS']['importUser'][]       = ['HeimrichHannot\Ldap\Backend\LdapMember', 'importPersonFromLdap'];
    $GLOBALS['TL_HOOKS']['checkCredentials'][] = ['HeimrichHannot\Ldap\Backend\LdapMember', 'authenticateAgainstLdap'];
}

if (TL_MODE == 'BE' && \Config::get('addLdapForUsers'))
{
    // order is correct
    $GLOBALS['TL_HOOKS']['importUser'][]       = ['HeimrichHannot\Ldap\Backend\LdapUser', 'importPersonFromLdap'];
    $GLOBALS['TL_HOOKS']['checkCredentials'][] = ['HeimrichHannot\Ldap\Backend\LdapUser', 'authenticateAgainstLdap'];
}
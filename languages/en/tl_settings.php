<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_settings'];

/**
 * Fields
 */
$arrLang['addLdapForMembers']   = ['Enable LDAP for members', 'Activate the login using LDAP for members.'];
$arrLang['addLdapForUsers']     = ['Enable LDAP for users', 'Activate the login using LDAP for users.'];
$arrLang['host']                = ['Host', 'IP address or DNS name of the LDAP server.'];
$arrLang['port']                = ['Port', 'Standard port: 389, SSL port: 636.'];
$arrLang['personBase']          = ['Base DN (users)', 'Base distinguished name for users (e.g. cn=users,dc=company,dc=com).'];
$arrLang['personFilter'][0]     = 'User filter';
$arrLang['personFilter'][1]     =
    'Note: Please, ensure that the username attribute occurs only once in the results otherwise the first listed will be used (e.g. "(&(objectClass=person)(objectClass=posixAccount)(mail=*@domain.com))").';
$arrLang['personFieldMapping']  = [
    'Mapping of LDAP attributes to Contao user fields (username is already defined above)',
    'Define how the LDAP attributes are mapped to Contao fields (e.g. givenName -> firstname).'
];
$arrLang['ldapField']           = ['LDAP attribute', 'Single attribute or pattern (e.g. "%givenName% %sn%").'];
$arrLang['contaoField']         = ['Contao field', ''];
$arrLang['defaultPersonValues'] = [
    'Default values',
    'Default values set to new users.'
];
$arrLang['field']               = ['Field', ''];
$arrLang['defaultValue']        = ['Value', ''];
$arrLang['groupBase']           = ['Base DN (Groups)', 'Base distinguished name for Groups (e.g. cn=groups,dc=company,dc=com).'];
$arrLang['groupFilter']         = ['Group filter', 'e.g. "(&(objectClass=group))".'];
$arrLang['groupFieldMapping']   = [
    'Mapping of LDAP attributes to Contao group fields',
    'Defined how the LDAP attributes are mapped to Contao fields (e.g. ou -> name).'
];
$arrLang['ldapUsernameField']   = ['LDAP username attribute', 'Name of the attribute used for username (standard: uid).'];
$arrLang['binddn']              = [
    'Bind user DN',
    'Distinguished name of the LDAP user used to bind to the LDAP server (e.g. cn=ldapadmin,cn=users,dc=domain,dc=com; for AD &lt;Domain&gt;\&lt;User&gt; can be used).'
];
$arrLang['password']            = ['Bind user password', 'Enter the password for the bind user.'];
$arrLang['groups']              = ['Groups to import', 'Define which LDAP member groups should be available.'];
$arrLang['skipLdapUsernames']   = [
    'Skip LDAP usernames',
    'Ignore one or more users with specified usernames (separated by comma) based on the value in the attribute defined as the LDAP username attribute.'
];

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_settings']['ldap_legend'] = 'LDAP';

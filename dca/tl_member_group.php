<?php 

$dc = &$GLOBALS['TL_DCA']['tl_member_group'];

/**
 * Additional Fields
 */
// ldap group id
$dc['fields']['ldapGid'] = array
(
	'sql'		=> "int(10) unsigned NOT NULL default '0'"
);

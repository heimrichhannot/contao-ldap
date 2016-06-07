<?php 

$dc = &$GLOBALS['TL_DCA']['tl_member'];

/**
 * Additional Fields
 */
// ldap user id
$dc['fields']['ldapUid'] = array
(
	'sql' => "varchar(255) NOT NULL default ''",
);
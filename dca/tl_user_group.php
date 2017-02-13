<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_user_group'];

/**
 * Fields
 */
$arrDca['fields']['ldapGid'] = [
    'sql' => "int(10) unsigned NOT NULL default '0'"
];
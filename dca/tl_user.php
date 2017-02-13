<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_user'];

/**
 * Fields
 */
$arrDca['fields']['ldapUid'] = [
    'sql' => "varchar(255) NOT NULL default ''",
];
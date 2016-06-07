<?php 

$dc = &$GLOBALS['TL_DCA']['tl_settings'];

/**
 * Palettes
 */
$replace = '{ldap_legend},ldap;';

$dc['palettes']['__selector__'][] = 'ldap';

if(extension_loaded('ldap')) {
	$dc['palettes']['default'] = str_replace('{chmod_legend', $replace . '{chmod_legend', $dc['palettes']['default']);
}
else {
	\Message::addInfo('LDAP PHP Extension not enabled, open your php.ini and uncomment "extension = php_ldap.dll".');
}


/**
 * Subpalettes
 */

$dc['subpalettes']['ldap'] = 'ldap_host,ldap_base,ldap_port,ldap_filter_person,ldap_filter_group,ldap_uid,ldap_method,ldap_binddn,ldap_password,ldap_groups,ldap_uid_skip';

/**
 * Fields
 */

$arrFields = array
(
	'ldap'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
	),
	'ldap_host'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_host'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'decodeEntities' => true, 'tl_class'=>'w50'),
	),
	'ldap_base'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_base'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'decodeEntities' => true, 'tl_class'=>'w50'),
	),
	'ldap_port'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_port'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'default'				  => 389,
			'eval'                    => array('mandatory' => true, 'maxlength'=>5, 'rgxp'=>'digit', 'tl_class'=>'w50'),
	),
	'ldap_filter_person'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_filter_person'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'default'				  => '(&(objectClass=person)(objectClass=posixAccount))',
			'eval'                    => array('mandatory'=>true, 'decodeEntities' => true, 'tl_class'=>'w50'),
	),
	'ldap_filter_group'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_filter_group'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'default'				 					=> '(&(objectClass=group))',
			'eval'                    => array('mandatory'=>true, 'decodeEntities' => true, 'tl_class'=>'w50'),
	),
	'ldap_uid'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_uid'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'default'									=> 'uid',
			'options'                 => array_keys(\HeimrichHannot\Ldap::$loginProperties),
			'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50'),
	),
	'ldap_method'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_method'],
			'default'                 => 'plain',
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('plain', 'ssl'),
			'eval'										=> array('tl_class'=>'w50'),
	),
	'ldap_binddn'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_binddn'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'decodeEntities' => true, 'tl_class'=>'long clr'),
	),
	'ldap_password'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_password'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'decodeEntities' => true, 'tl_class'=>'w50'),
	),
	'ldap_groups' 	=> 	array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_groups'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkboxWizard',
			'eval'                    => array('multiple'=>true, 'tl_class'=>'long clr'),
			'options_callback'			=> array('HeimrichHannot\LdapMemberGroupModel', 'getLdapMemberGroupsAsOptions'),
			'save_callback'						=> array
			(
				array('HeimrichHannot\LdapMemberGroup', 'updateMemberGroups'),
			)
	),
	'ldap_uid_skip'	=> array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['ldap_uid_skip'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'decodeEntities' => true, 'tl_class'=>'long clr'),
	),
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);
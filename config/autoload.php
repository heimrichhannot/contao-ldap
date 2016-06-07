<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Ldap
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'HeimrichHannot\Ldap'                 => 'system/modules/ldap/classes/Ldap.php',
	'HeimrichHannot\LdapMember'           => 'system/modules/ldap/classes/LdapMember.php',
	'HeimrichHannot\LdapMemberGroup'      => 'system/modules/ldap/classes/LdapMemberGroup.php',

	// Models
	'HeimrichHannot\LdapMemberGroupModel' => 'system/modules/ldap/models/LdapMemberGroupModel.php',
	'HeimrichHannot\LdapMemberModel'      => 'system/modules/ldap/models/LdapMemberModel.php',

	// Modules
	'HeimrichHannot\ModuleLdapLogin'      => 'system/modules/ldap/modules/ModuleLdapLogin.php',
));

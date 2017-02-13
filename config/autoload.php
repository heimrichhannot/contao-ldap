<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
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
	// Modules
	'HeimrichHannot\Ldap\ModuleLdapLogin'         => 'system/modules/ldap/modules/ModuleLdapLogin.php',

	// Classes
	'HeimrichHannot\Ldap\Backend\LdapUser'        => 'system/modules/ldap/classes/backend/LdapUser.php',
	'HeimrichHannot\Ldap\Backend\LdapPerson'      => 'system/modules/ldap/classes/backend/LdapPerson.php',
	'HeimrichHannot\Ldap\Backend\LdapPersonGroup' => 'system/modules/ldap/classes/backend/LdapPersonGroup.php',
	'HeimrichHannot\Ldap\Backend\LdapUserGroup'   => 'system/modules/ldap/classes/backend/LdapUserGroup.php',
	'HeimrichHannot\Ldap\Backend\LdapMember'      => 'system/modules/ldap/classes/backend/LdapMember.php',
	'HeimrichHannot\Ldap\Backend\LdapMemberGroup' => 'system/modules/ldap/classes/backend/LdapMemberGroup.php',
	'HeimrichHannot\Ldap\Ldap'                    => 'system/modules/ldap/classes/Ldap.php',

	// Models
	'HeimrichHannot\Ldap\LdapPersonGroupModel'    => 'system/modules/ldap/models/LdapPersonGroupModel.php',
	'HeimrichHannot\Ldap\LdapPersonModel'         => 'system/modules/ldap/models/LdapPersonModel.php',
	'HeimrichHannot\Ldap\LdapMemberGroupModel'    => 'system/modules/ldap/models/LdapMemberGroupModel.php',
	'HeimrichHannot\Ldap\LdapUserGroupModel'      => 'system/modules/ldap/models/LdapUserGroupModel.php',
	'HeimrichHannot\Ldap\LdapUserModel'           => 'system/modules/ldap/models/LdapUserModel.php',
	'HeimrichHannot\Ldap\LdapMemberModel'         => 'system/modules/ldap/models/LdapMemberModel.php',
));

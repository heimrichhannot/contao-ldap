<?php

namespace HeimrichHannot\Ldap;

class LdapUserGroupModel extends LdapPersonGroupModel
{
    protected static $strPrefix          = 'User';
    protected static $strLocalModel      = '\UserModel';
    protected static $strLocalGroupModel = '\UserGroupModel';
    protected static $strLdapModel       = 'HeimrichHannot\Ldap\LdapUserModel';
    protected static $strLdapGroupModel  = 'HeimrichHannot\Ldap\LdapUserGroupModel';
}
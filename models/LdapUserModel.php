<?php

namespace HeimrichHannot\Ldap;

class LdapUserModel extends LdapPersonModel
{
    protected static $strPrefix          = 'User';
    protected static $strLocalModel      = '\UserModel';
    protected static $strLocalGroupModel = '\UserGroupModel';
    protected static $strLdapModel       = 'HeimrichHannot\Ldap\LdapUserModel';
    protected static $strLdapGroupModel  = 'HeimrichHannot\Ldap\LdapUserGroupModel';
}
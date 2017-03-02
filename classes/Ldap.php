<?php

namespace HeimrichHannot\Ldap;

class Ldap extends \System
{
    const LDAP_INVALID_CREDENTIALS = 49;

    const MODULE_LDAP_LOGIN = 'ldapLogin';

    const MODE_MEMBER = 'member';
    const MODE_USER   = 'user';

    protected static $arrConnections = [];

    public static function connect($strMode)
    {
        $strPrefix = 'ldap' . ucfirst($strMode);

        if (($strHost = \Config::get($strPrefix . 'Host'))
            && ($strPort = \Config::get($strPrefix . 'Port'))
            && ($strBindDn = \Config::get($strPrefix . 'Binddn'))
            && ($strPassword = \Config::get($strPrefix . 'Password'))
        )
        {
            $strAuthMethod = \Config::get($strPrefix . 'AuthMethod');
            $objConnection = ldap_connect(($strAuthMethod == 'ssl' ? 'ldaps://' : 'ldap://') . $strHost, $strPort)
            or die ('Could not connect to LDAP server.');

            if (!is_resource($objConnection))
            {
                return false;
            }

            ldap_set_option($objConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($objConnection, LDAP_OPT_REFERRALS, 0);

            // check if bind dn can connect to ldap server
            if (!ldap_bind($objConnection, $strBindDn, $strPassword))
            {
                return false;
            }

            static::$arrConnections[$strMode] = $objConnection;

            return $objConnection;
        }
        else
        {
            throw new \Exception('Please fully configure your LDAP connection at first.');
        }
    }

    public static function getConnection($strMode)
    {
        if (isset(static::$arrConnections[$strMode]))
        {
            return static::$arrConnections[$strMode];
        }

        return static::connect($strMode);
    }

    public static function usernameIsEmail()
    {
        $blnEmail2UsernameExtensionActive = in_array(
            'email2username',
            \ModuleLoader::getActive()
        );

        $blnMailUsernameExtensionActive = in_array(
            'mailusername',
            \ModuleLoader::getActive()
        );

        return $blnEmail2UsernameExtensionActive || $blnMailUsernameExtensionActive;
    }
}
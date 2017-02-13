<?php

namespace HeimrichHannot\Ldap;

class Ldap extends \System
{
    const LDAP_INVALID_CREDENTIALS = 49;

    const MODULE_LDAP_LOGIN = 'ldapLogin';

    protected static $objMemberConnection;
    protected static $objUserConnection;

    public static function connect($blnUserMode = false)
    {
        $strPrefix = 'ldap' . ($blnUserMode ? 'User' : 'Member');

        if (($strHost = \Config::get($strPrefix . 'Host'))
            && ($strPort = \Config::get($strPrefix . 'Port'))
            && ($strBindDn = \Config::get($strPrefix . 'Binddn'))
            && ($strPassword = \Config::get($strPrefix . 'Password'))
        )
        {
            $objConnection = ldap_connect($strHost, $strPort) or die ("Could not connect to LDAP server.");

            if (!is_resource($objConnection))
            {
                return false;
            }

            ldap_set_option($objConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($objConnection, LDAP_OPT_REFERRALS, 0);

            // check if bind dn can connect to ldap server
            if (!@ldap_bind($objConnection, $strBindDn, $strPassword))
            {
                return false;
            }

            if ($blnUserMode)
            {
                return static::$objUserConnection = $objConnection;
            }
            else
            {
                return static::$objMemberConnection = $objConnection;
            }
        }
        else
        {
            throw new \Exception('Please fully configure your LDAP connection at first.');
        }
    }

    public static function getConnection($blnUserMode = false)
    {
        if (static::$objUserConnection && $blnUserMode)
        {
            return static::$objUserConnection;
        }

        if (static::$objMemberConnection && !$blnUserMode)
        {
            return static::$objMemberConnection;
        }

        return static::connect($blnUserMode);
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
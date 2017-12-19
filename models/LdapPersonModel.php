<?php

namespace HeimrichHannot\Ldap;

abstract class LdapPersonModel extends \Model
{
    protected static $arrRequiredAttributes = ['uid'];
    protected static $strPrefix             = '';
    protected static $strLdapModel          = '';
    protected static $strLocalModel         = '';
    protected static $strLdapGroupModel     = '';
    protected static $strLocalGroupModel    = '';

    public static function findAll(array $arrOptions = [])
    {
        if ($objConnection = Ldap::getConnection(strtolower(static::$strPrefix)))
        {
            $arrAttributes = static::$arrRequiredAttributes;
            $arrAttributes = static::addAttributes($arrAttributes);

            $strQuery = ldap_search(
                $objConnection,
                \Config::get('ldap' . static::$strPrefix . 'PersonBase'),
                \Config::get('ldap' . static::$strPrefix . 'PersonFilter'),
                $arrAttributes
            );

            if (!$strQuery)
            {
                return false;
            }

            $arrResult = ldap_get_entries($objConnection, $strQuery);

            if (!is_array($arrResult))
            {
                return false;
            }

            return $arrResult;
        }
        else
        {
            return false;
        }
    }

    public static function findByUsername($strUsername)
    {
        if ($objConnection = Ldap::getConnection(strtolower(static::$strPrefix)))
        {
            $strFilter = '(&(' . \Config::get('ldap' . static::$strPrefix . 'LdapUsernameField') . '=' . $strUsername . ')' . \Config::get(
                    'ldap' . static::$strPrefix . 'PersonFilter'
                ) . ')';

            $arrAttributes = static::$arrRequiredAttributes;
            $arrAttributes = static::addAttributes($arrAttributes);

            // search by username
            $strQuery = ldap_search($objConnection, \Config::get('ldap' . static::$strPrefix . 'Base'), $strFilter, $arrAttributes);

            if (!$strQuery)
            {
                return null;
            }

            $arrResult = ldap_get_entries($objConnection, $strQuery);

            if (!is_array($arrResult) || empty($arrResult))
            {
                return null;
            }

            return $arrResult[0];
        }
        else
        {
            return null;
        }
    }

    private static function addAttributes($arrAttributes)
    {
        foreach (deserialize(\Config::get('ldap' . static::$strPrefix . 'PersonFieldMapping'), true) as $arrMapping)
        {
            if (strpos($arrMapping['ldapField'], '%') !== false)
            {
                preg_match_all('@%[^%]*%@i', $arrMapping['ldapField'], $arrMatches);

                foreach ($arrMatches[0] as $strTag)
                {
                    $arrAttributes[] = rtrim(ltrim($strTag, '%'), '%');
                }
            }
            else
            {
                $arrAttributes[] = $arrMapping['ldapField'];
            }
        }

        return $arrAttributes;
    }

    public static function getRemoteLdapGroupIdsByUid($strUid)
    {
        $strLdapGroupModelClass = static::$strLdapGroupModel;

        $arrRemoteLdapGroups = $strLdapGroupModelClass::findAll();

        $arrGroups = [];

        if (!is_array($arrRemoteLdapGroups))
        {
            return $arrGroups;
        }

        foreach ($arrRemoteLdapGroups as $strId => $arrGroup)
        {
            if ($strId == 'count' || array_search($strUid, $arrGroup['persons']) === false)
            {
                continue;
            }

            $arrGroups[] = $strId;
        }

        return $arrGroups;
    }
}
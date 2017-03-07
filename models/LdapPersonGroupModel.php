<?php

namespace HeimrichHannot\Ldap;

abstract class LdapPersonGroupModel extends \Model
{
    protected static $arrRequiredAttributes = ['gidnumber', 'cn', 'memberuid'];
    protected static $strPrefix             = '';
    protected static $strLdapModel          = '';
    protected static $strLocalModel         = '';
    protected static $strLdapGroupModel     = '';
    protected static $strLocalGroupModel    = '';

    public static function findAll()
    {
        $objConnection = Ldap::getConnection(strtolower(static::$strPrefix));

        if ($objConnection)
        {
            $strQuery = ldap_search(
                $objConnection,
                'CN=groups,' . \Config::get('ldap' . static::$strPrefix . 'Base'),
                "(objectClass=*)",
                static::$arrRequiredAttributes
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
            $arrGroups = [];
            foreach ($arrResult as $strKey => $arrGroup)
            {
                if ($strKey == 'count')
                {
                    continue;
                }
                if ($arrGroup['gidnumber']['count'] > 0)
                {
                    $arrGroups[$arrGroup['gidnumber'][0]] = [
                        'label'   => $arrGroup['cn']['count'] > 0 ? $arrGroup['cn'][0] : $arrGroup['gidnumber'][0],
                        'persons' => $arrGroup['memberuid']['count'] > 0 ? $arrGroup['memberuid'] : []
                    ];
                }
            }
            return $arrGroups;
        }
        else
        {
            return false;
        }
    }

    public static function getLocalLdapGroupIds($arrRemoteLdapGroupIds)
    {
        $arrResult = [];
        foreach ($arrRemoteLdapGroupIds as $currentGid)
        {
            $strLocalGroupModelClass = static::$strLocalGroupModel;

            $objGroup = $strLocalGroupModelClass::findBy('ldapGid', $currentGid);
            if ($objGroup !== null)
            {
                $arrResult[] = $objGroup->id;
            }
        }

        return $arrResult;
    }

}
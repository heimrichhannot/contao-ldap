<?php

namespace HeimrichHannot\Ldap;

abstract class LdapPersonGroupModel extends \Model
{
    protected static $arrRequiredAttributes = ['gidnumber', 'cn', 'memberuid'];
    protected static $blnUserMode           = false;
    protected static $strPrefix             = '';
    protected static $strLdapModel          = '';
    protected static $strLocalModel         = '';
    protected static $strLdapGroupModel     = '';
    protected static $strLocalGroupModel    = '';

    public static function findAll()
    {
        if (Ldap::getConnection(static::$blnUserMode))
        {
            $strQuery = ldap_search(
                Ldap::getConnection(),
                'CN=groups,' . \Config::get('ldap' . static::$strPrefix . 'Base'),
                "(objectClass=*)",
                static::$arrRequiredAttributes
            );

            if (!$strQuery)
            {
                return false;
            }

            $arrResult = ldap_get_entries(Ldap::getConnection(), $strQuery);

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
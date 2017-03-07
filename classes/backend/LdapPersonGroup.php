<?php

namespace HeimrichHannot\Ldap\Backend;

class LdapPersonGroup
{
    protected static $strPrefix          = '';
    protected static $strLdapModel       = '';
    protected static $strLocalModel      = '';
    protected static $strLdapGroupModel  = '';
    protected static $strLocalGroupModel = '';

    public static function getLdapPersonGroupsAsOptions()
    {
        $arrGroups = [];

        if (!is_array($arrGroups))
        {
            return [];
        }

        $strLdapGroupModelClass = static::$strLdapGroupModel;
        $arrRemoteLdapGroups    = $strLdapGroupModelClass::findAll();

        if (!is_array($arrRemoteLdapGroups))
        {
            return [];
        }

        foreach ($arrRemoteLdapGroups as $strId => $arrGroup)
        {
            $arrGroups[$strId] = $arrGroup['label'];
        }

        asort($arrGroups);

        return $arrGroups;
    }

    /**
     * Add local member groups as representation of remote ldap groups
     *
     * @param $varValue
     *
     * @return mixed
     */
    public static function updatePersonGroups($varValue)
    {
        if (!\Config::get('addLdapFor' . static::$strPrefix . 's'))
        {
            return $varValue;
        }

        $arrSelectedGroups = deserialize($varValue, true);

        if (!empty($arrSelectedGroups))
        {
            $strLdapGroupModel = static::$strLdapGroupModel;
            $arrGroups         = $strLdapGroupModel::findAll();

            if (!is_array($arrGroups) || empty($arrGroups))
            {
                return $varValue;
            }

            $strLocalGroupModel = static::$strLocalGroupModel;
            foreach ($arrSelectedGroups as $intSelectedId)
            {
                if (in_array($intSelectedId, array_keys($arrGroups)))
                {
                    if (($objGroup = $strLocalGroupModel::findByLdapGid($intSelectedId)) === null)
                    {
                        $objGroup          = new $strLocalGroupModel();
                        $objGroup->ldapGid = $intSelectedId;
                    }

                    $objGroup->tstamp = time();
                    $objGroup->name   = $GLOBALS['TL_LANG']['MSC']['ldapGroupPrefix'] . $arrGroups[$intSelectedId]['label'];

                    $objGroup->save();
                }
            }

            $strClass = 'HeimrichHannot\Ldap\Backend\Ldap' . static::$strPrefix;

            $strClass::updatePersons($arrSelectedGroups);
        }

        return $varValue;
    }
}
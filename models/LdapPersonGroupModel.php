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
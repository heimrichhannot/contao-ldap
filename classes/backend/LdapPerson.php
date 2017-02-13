<?php

namespace HeimrichHannot\Ldap\Backend;

use HeimrichHannot\Ldap\Ldap;

class LdapPerson
{
    protected static $blnUserMode        = false;
    protected static $strPrefix          = '';
    protected static $strLdapModel       = '';
    protected static $strLocalModel      = '';
    protected static $strLdapGroupModel  = '';
    protected static $strLocalGroupModel = '';

    /**
     * importUser hook
     */
    public function importPersonFromLdap($strUsername, $strPassword, $strTable)
    {
        if (static::authenticateLdapPerson($strUsername, $strPassword))
        {
            $strLdapModelClass = static::$strLdapModel;
            static::createOrUpdatePerson(null, $strLdapModelClass::findByUsername($strUsername), $strUsername);

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * check credentials hook -> ldap password != contao password
     */
    public function authenticateAgainstLdap($strUsername, $strPassword, $objPerson)
    {
        if (static::authenticateLdapPerson($strUsername, $strPassword))
        {
            // update since groups and/or mapped fields could have changed remotely
            $strLdapModelClass = static::$strLdapModel;
            static::createOrUpdatePerson($objPerson, $strLdapModelClass::findByUsername($strUsername), $strUsername);

            return true;
        }
        else
        {
            return false;
        }
    }

    public static function authenticateLdapPerson($strUsername, $strPassword)
    {
        $strLdapModelClass = static::$strLdapModel;
        $arrPerson         = $strLdapModelClass::findByUsername($strUsername);

        if ($arrPerson)
        {
            if (!ldap_bind(Ldap::getConnection(), $arrPerson['dn'], $strPassword))
            {
                return false;
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    public static function updatePersons($arrSelectedGroups)
    {
        $strLdapModelClass = static::$strLdapModel;
        $arrLdapPersons    = $strLdapModelClass::findAll();

        if (!is_array($arrLdapPersons))
        {
            return;
        }

        $arrFoundUids = [];

        $arrSkipUsernames = trimsplit(',', \Config::get('ldap' . static::$strPrefix . 'SkipLdapUsernames'));

        $strLocalModelClass = static::$strLocalModel;

        foreach ($arrLdapPersons as $strKey => $arrPerson)
        {
            if ($strKey == 'count' || $arrPerson['uid']['count'] < 1
                || $arrPerson[\Config::get(
                    'ldap' . static::$strPrefix . 'LdapUsernameField'
                )]['count'] < 1
            )
            {
                continue;
            }

            $strUid = $arrPerson['uid'][0];

            if (in_array($strUid, $arrSkipUsernames))
            {
                continue;
            }

            // should be maximum 1 -> else a better filter has to be set
            $strUsername = $arrPerson[\Config::get('ldap' . static::$strPrefix . 'LdapUsernameField')][0];

            if (Ldap::usernameIsEmail() && !\Validator::isEmail($strUsername))
            {
                continue;
            }

            // mark remotely missing persons as disabled
            $arrFoundUids[] = $strUid;

            $objPerson = $strLocalModelClass::findBy(['username=? OR ldapUid=?'], [$strUsername, $strUid]);

            $objPerson = static::createOrUpdatePerson($objPerson, $arrPerson, $strUsername, $arrSelectedGroups);

            $objPerson->save();
        }

        // mark remotely missing persons as disabled
        if (($objPersons = $strLocalModelClass::findAll()) !== null)
        {
            while ($objPersons->next())
            {
                if ($objPersons->ldapUid && !in_array($objPersons->ldapUid, $arrFoundUids))
                {
                    $objPersons->disable = true;
                    $objPersons->save();
                }
                else
                {
                    $objPersons->disable = false;
                    $objPersons->save();
                }
            }
        }
    }

    public static function createOrUpdatePerson($objPerson, $arrPerson, $strUsername, $arrSelectedGroups = null)
    {
        $arrSelectedGroups  = $arrSelectedGroups ?: deserialize(\Config::get('ldap' . static::$strPrefix . 'Groups'), true);
        $strLocalModelClass = static::$strLocalModel;

        // create the person initially
        if ($objPerson === null)
        {
            $arrSkipUsernames = trimsplit(',', \Config::get('ldap' . static::$strPrefix . 'SkipLdapUsernames'));

            if (!is_array($arrPerson) || in_array($arrPerson['uid'][0], $arrSkipUsernames))
            {
                return false;
            }

            $objPerson = new $strLocalModelClass();

            $objPerson->tstamp   = $objPerson->dateAdded = time();
            $objPerson->login    = true;
            $objPerson->username = $strUsername;
            $objPerson->ldapUid  = $arrPerson['uid'][0];
            // store randomized password, so contao will always trigger the checkCredentials hook
            $objPerson->password = md5(time() . $strUsername);

            if (TL_MODE == 'BE')
            {
                $objPerson->showHelp = true;
                $objPerson->useRTE = true;
                $objPerson->useCE = true;
                $objPerson->thumbnails = true;
                $objPerson->backendTheme = 'flexible';
            }

            static::addGroups($objPerson, $arrSelectedGroups);
            static::applyFieldMapping($objPerson, $arrPerson);
            static::applyDefaultValues($objPerson);

            if (isset($GLOBALS['TL_HOOKS']['ldapAddPerson']) && is_array($GLOBALS['TL_HOOKS']['ldapAddPerson']))
            {
                foreach ($GLOBALS['TL_HOOKS']['ldapAddPerson'] as $callback)
                {
                    $callback[0]->{$callback[1]}($objPerson, $arrSelectedGroups);
                }
            }
        }
        else
        {
            static::addGroups($objPerson, $arrSelectedGroups);
            static::applyFieldMapping($objPerson, $arrPerson);
            static::applyDefaultValues($objPerson);

            // store randomized password, so contao will always trigger the checkCredentials hook
            $objPerson->password = md5(time() . $strUsername);

            if (isset($GLOBALS['TL_HOOKS']['ldapUpdatePerson']) && is_array($GLOBALS['TL_HOOKS']['ldapUpdatePerson']))
            {
                foreach ($GLOBALS['TL_HOOKS']['ldapUpdatePerson'] as $callback)
                {
                    $callback[0]->{$callback[1]}($objPerson, $arrSelectedGroups);
                }
            }
        }

        $objPerson->save();

        return $objPerson;
    }

    public static function applyFieldMapping($objPerson, $arrRemoteLdapPerson)
    {
        // if a certain domain is specified in the person filter, this should be the reference if the person has multiple email entries
        preg_match(
            '#@(?P<domain>[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5})#is',
            \Config::get('ldap' . static::$strPrefix . 'PersonFilter'),
            $arrMatches
        );
        $strDomain = $arrMatches['domain'];

        foreach (deserialize(\Config::get('ldap' . static::$strPrefix . 'PersonFieldMapping'), true) as $arrMapping)
        {
            // special case email -> only one-to-one mapping possible
            if ($arrMapping['contaoField'] == 'email' && $strDomain)
            {
                if ($arrRemoteLdapPerson[$arrMapping['ldapField']]['count'] < 1)
                {
                    continue;
                }

                $arrMailFilter = preg_grep('#(.*)' . $strDomain . '#i', $arrRemoteLdapPerson[$arrMapping['ldapField']]);

                if (is_array($arrMailFilter) && !empty($arrMailFilter) && \Validator::isEmail($arrMailFilter[0]))
                {
                    // take first mail, that fits domain regxp
                    $objPerson->email = $arrMailFilter[0];
                }
            }
            else
            {
                $objPerson->{$arrMapping['contaoField']} = static::getLdapField($arrRemoteLdapPerson, $arrMapping['ldapField']);
            }
        }
    }

    private static function getLdapField($arrRemoteLdapPerson, $strLdapField)
    {
        if (strpos($strLdapField, '%') !== false)
        {
            return preg_replace_callback(
                '@%[^%]*%@i',
                function ($arrPattern) use ($arrRemoteLdapPerson, $strLdapField)
                {
                    $strPattern = $arrPattern[0];
                    $strTag = rtrim(ltrim($strPattern, '%'), '%');

                    if ($arrRemoteLdapPerson[$strTag]['count'] > 0)
                    {
                        return $arrRemoteLdapPerson[$strTag][0];
                    }

                    return $strPattern;
                },
                $strLdapField
            );
        }
        else
        {
            if ($arrRemoteLdapPerson[$strLdapField]['count'] > 0)
            {
                return $arrRemoteLdapPerson[$strLdapField][0];
            }
        }

        return $strLdapField;
    }

    public static function applyDefaultValues($objPerson)
    {
        foreach (deserialize(\Config::get('ldap' . static::$strPrefix . 'DefaultPersonValues'), true) as $arrMapping)
        {
            $objPerson->{$arrMapping['field']} = $arrMapping['defaultValue'];
        }
    }

    /**
     * Adds active remote ldap group's local representation keeping the non ldap contao groups
     *
     * @param       $objPerson
     * @param       $arrSelectedGroups
     */
    public static function addGroups($objPerson, $arrSelectedGroups)
    {
        $strLocalGroupClass = static::$strLocalGroupModel;
        $strLdapGroupClass  = static::$strLdapGroupModel;
        $strLdapModelClass  = static::$strLdapModel;

        $arrGroups           = deserialize($objPerson->groups, true);
        $objLocalLdapGroups  = $strLocalGroupClass::findBy(['(ldapGid > 0)'], null);
        $arrRemoteLdapGroups = $strLdapModelClass::getRemoteLdapGroupIdsByUid($objPerson->ldapUid);

        if ($objLocalLdapGroups !== null)
        {
            $arrLocalLdapPersonGroups = $objLocalLdapGroups->fetchEach('ldapGid');

            $objPerson->groups = serialize(
                array_merge(
                    array_diff($arrGroups, $arrLocalLdapPersonGroups), // non ldap local contao groups
                    $strLdapGroupClass::getLocalLdapGroupIds(array_intersect($arrRemoteLdapGroups, $arrSelectedGroups))
                )
            );
        }
    }
}
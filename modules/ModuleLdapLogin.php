<?php

namespace HeimrichHannot\Ldap;

use HeimrichHannot\Request\Request;

class ModuleLdapLogin extends \ModuleLogin
{
    public function generate()
    {
        if (Request::getPost('FORM_SUBMIT') == 'tl_login')
        {
            if (Request::getPost('username') && Request::getPost('password'))
            {
                $objMember = \MemberModel::findBy('username', Request::getPost('username'));

                if ($objMember !== null)
                {
                    // always reset the password to a random value, otherwise checkCredentialsHook will never be triggered
                    $objMember->password = md5(time() . Request::getPost('username'));
                    $objMember->save();
                }

                // validate email
                if (Ldap::usernameIsEmail() && !\Validator::isEmail(Request::getPost('username')))
                {
                    \Message::addError($GLOBALS['TL_LANG']['ERR']['email']);
                    \Controller::reload();
                }
            }
        }

        $strParent = parent::generate();

        return $strParent;
    }

    protected function compile()
    {
        parent::compile();

        if (Ldap::usernameIsEmail())
        {
            $this->Template->username = $GLOBALS['TL_LANG']['MSC']['usernamemail'];
        }
    }


}
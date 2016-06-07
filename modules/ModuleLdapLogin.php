<?php

namespace HeimrichHannot;

class ModuleLdapLogin extends \ModuleLogin
{
	protected $isUserNameEmail = false;

	/**
	 * Store Login Module ID in Session, required by LdapAuth (Module config)
	 * @return string
	 */
	public function generate()
	{
		// Login
		if (\Input::post('FORM_SUBMIT') == 'tl_login')
		{
			if (\Input::post('username', true) && \Input::post('password', true))
			{
				$objMember = \MemberModel::findBy('username', \Input::post('username', true));

				if($objMember !== null)
				{
					// always reset the password to a random value, otherwise checkCredentialsHook will never be triggered
					LdapMember::resetPassword($objMember, \Input::post('username', true));
				}
			}
			
			// validate email
			if($GLOBALS['TL_CONFIG']['ldap_uid'] == 'mail' && !\Validator::isEmail(\Input::post('username', true)))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['email']);
				$this->reload();
			}
		}

		$strParent = parent::generate();

		return $strParent;
	}

	protected function compile()
	{
		parent::compile();

		if($GLOBALS['TL_CONFIG']['ldap_uid'] == 'mail')
		{
			$this->Template->username = $GLOBALS['TL_LANG']['MSC']['usernamemail'];
		}
	}


}
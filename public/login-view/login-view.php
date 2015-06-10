<?php
if (! class_exists ('LoginView'))
{
	/**
	 * @class LoginView
	 *
	 * Displays a login form and manages logging in voting users.
	 */
	
	class LoginView
	{
		const _USERNAME_FIELD = 'username';
		const _PASSWORD_FIELD = 'password';
		
		
		// The database connection.
		private $_auth_manager  = null;
		// Any errors from the form submission.
		private $_errors = false;
		
		
		public function __construct (&$auth_manager)
		{
			$this->_auth_manager = $auth_manager;
		}
		
		public function show ()
		{
			$this->_handle_form_submission ();
			$this->_print_html ();
		}
		
		private function _handle_form_submission ()
		{
			$this->_errors = false;
			
			if (isset ($_POST [self::_USERNAME_FIELD]) && 
				isset ($_POST [self::_PASSWORD_FIELD]))
			{
				if (empty ($_POST [self::_USERNAME_FIELD]))
				{
					$this->_errors ['username_empty'] = 'Please enter your username';
				}
				else if (empty ($_POST [self::_PASSWORD_FIELD]))
				{
					$this->_errors ['password_empty'] = 'Please enter your password';
				}
				else if ($this->_auth_manager->authenticate (
					$_POST [self::_USERNAME_FIELD],
					$_POST [self::_PASSWORD_FIELD]))
				{
					// Reload the current page
					header ('Location: ' . $_SERVER ['REQUEST_URI']);
				}
				else
				{
					$this->_errors ['invalid'] = 'The username or password you entered is incorrect';
				}
			}
		}
		
		private function _print_html ()
		{
			$form_errors = &$this->_errors;
			$form_values = array (
				self::_USERNAME_FIELD => isset ($_POST [self::_USERNAME_FIELD]) ? $_POST [self::_USERNAME_FIELD] : '',
			);
			
			require 'login.php';
		}
	}
}
?>

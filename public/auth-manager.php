<?php
if (! class_exists ('AuthManager'))
{
	/**
	 * @class AuthManager
	 *
	 * A generic base class that manages authenticating a user with a
	 * username and password, and storing the authenticated information
	 * in the session.
	 */
	
	abstract class AuthManager
	{
		const _SESSION_AUTH_KEY = 'authenticated_object';
		const _SESSION_ACTIVITY_KEY = 'last_activity';
		
		const _SESSION_TTL = 1800; // in seconds
		
		
		protected $_auth_object = false;
		
		
		/**
		 * Starts the session, and retrieves any persistent
		 * information already in the session.
		 */
		public function __construct ()
		{
			session_start ();
			
			if (isset ($_SESSION [self::_SESSION_AUTH_KEY]))
			{
				if ($this->_correct_auth_type ($_SESSION [self::_SESSION_AUTH_KEY]))
				{
					$this->_get_auth_object_from_session ($_SESSION [self::_SESSION_AUTH_KEY]);
					$this->_check_activity ();
				}
				else
				{
					$this->deauthenticate ();
				}
			}
		}
		
		/**
		 * Returns the user data associated with the given username
		 * and password, or false if authentication failed.
		 */
		abstract protected function _get_auth_session_data ($user, $pass);
		
		/**
		 * Sets the _auth_object member variable from the given session data.
		 */
		abstract protected function _get_auth_object_from_session ($session_data);
		
		/**
		 * Returns whether the authentication information in the session is
		 * for the correct type of user.  If a user tries to navigate to the
		 * admin page after logging in as a voting user, then this prevents
		 * them from being recognized as authenticated.
		 */
		abstract protected function _correct_auth_type ($session_data);
		
		/**
		 * Returns whether the user has already been
		 * authenticated for the current session.
		 */
		public function is_authenticated ()
		{
			return ($this->_auth_object != false);
		}
		
		/**
		 * Clears any local authenticated user information, deletes the
		 * session cookie, and unsets and destroys the current session.
		 */
		public function deauthenticate ()
		{
			$this->_auth_object = false;
			
			$params = session_get_cookie_params ();
			setcookie (
				session_name (),
				'',
				time () - 42000,
				$params ['path'],
				$params ['domain'],
				$params ['secure'],
				$params ['httponly']
			);
			
			session_unset ();
			session_destroy ();
		}
		
		/**
		 * @returns 	true if the user was successfully authenticated,
		 * 				and false otherwise.
		 *
		 * Attempts to authenticates the user for the current session
		 * using the given username and password.
		 */
		public function authenticate ($user, $pass)
		{
			$session_data = $this->_get_auth_session_data ($user, $pass);
			
			if ($session_data != false)
			{
				$_SESSION [self::_SESSION_AUTH_KEY] = $session_data;
				$this->_get_auth_object_from_session ($session_data);
			}
			else
			{
				unset ($_SESSION [self::_SESSION_AUTH_KEY]);
				$this->_auth_object = false;
			}
			
			return $this->is_authenticated ();
		}
		
		/**
		 * Determines if the currently authenticated user has been idle
		 * for longer than the session time-to-live. If so, the user is
		 * deauthenticated.
		 */
		private function _check_activity ()
		{
			if (isset ($_SESSION [self::_SESSION_ACTIVITY_KEY]) && (time () - $_SESSION [self::_SESSION_ACTIVITY_KEY] > self::_SESSION_TTL))
			{
				// last request was more than 30 minutes ago
				$this->deauthenticate ();
			}
			$_SESSION [self::_SESSION_ACTIVITY_KEY] = time (); // update last activity time stamp
		}
	}
}
?>

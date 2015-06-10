<?php
if (! class_exists ('AdminAuthManager'))
{
	require '../auth-manager.php';
	require 'models/auth-admin.php';
	
	/**
	 * @class AdminAuthManager
	 *
	 * A class that manages the authentication and
	 * database management of administrators.
	 */
	
	class AdminAuthManager extends AuthManager
	{
		const AUTH_TYPE       = 'auth_type';
		const ADMIN_AUTH_TYPE = 'auth_type_admin';
		
		const ADMIN_ID        = 'admin_id';
		const ADMIN_USERNAME  = 'admin_username';
		
		
		private $_database = null;
		
		
		/**
		 * Initializes the base class and stores the given database connection.
		 */
		public function __construct (&$database)
		{
			$this->_database = $database;
			
			parent::__construct ();
		}
		
		/**
		 * Returns the authorized admin object.
		 */
		public function auth_admin ()
		{
			return $this->_auth_object;
		}
		
		/**
		 * Returns the user data associated with the given username
		 * and password, or false if authentication failed.
		 */
		/* override */ protected function _get_auth_session_data ($user, $pass)
		{
			$session_data = false;
			
			if ($salt = $this->_get_salt_from_username ($user))
			{
				$hash = hash ('sha256', $pass . $salt);
				
				$user_stmt = $this->_database->prepare (
					'SELECT admin_id, admin_username '.
					'FROM   admins '.
					'WHERE  admin_username=:user '.
					'AND    admin_password=:hash'
				);
				$user_stmt->bindValue (':user', $user, \PDO::PARAM_STR);
				$user_stmt->bindValue (':hash', $hash, \PDO::PARAM_STR);
				
				$user_stmt->execute ();
				
				if ($admin_record = $user_stmt->fetch (\PDO::FETCH_ASSOC))
				{
					$session_data = array (
						self::AUTH_TYPE      => self::ADMIN_AUTH_TYPE,
						self::ADMIN_ID       => $admin_record ['admin_id'],
						self::ADMIN_USERNAME => $admin_record ['admin_username'],
					);
				}
			}
			
			return $session_data;
		}
		
		/**
		 * Sets the _auth_object member variable from the given session data.
		 */
		/* override */ protected function _get_auth_object_from_session ($session_data)
		{
			$this->_auth_object = new AuthAdmin (
				$session_data [self::ADMIN_ID],
				$session_data [self::ADMIN_USERNAME]
			);
		}
		
		/**
		 * Returns whether the authentication information in the session is
		 * for an administrator.
		 */
		/* override */ protected function _correct_auth_type ($session_data)
		{
			return (isset ($session_data [self::AUTH_TYPE]) && $session_data [self::AUTH_TYPE] == self::ADMIN_AUTH_TYPE);
		}
		
		/**
		 * Returns the id associated with the given admin username.
		 */
		private function _get_id_from_username ($user)
		{
			$admin_stmt = $this->_database->prepare (
				'SELECT admin_id '.
				'FROM   admins '.
				'WHERE  admin_username=:user'
			);
			$admin_stmt->bindValue (':user', $user, \PDO::PARAM_STR);
			$admin_stmt->execute ();
			
			if ($admin_record = $admin_stmt->fetch (\PDO::FETCH_ASSOC))
				return $admin_record ['admin_id'];
			
			return false;
		}
		
		/**
		 * Returns the salt associated with the given admin username.
		 */
		private function _get_salt_from_username ($user)
		{
			if ($admin_id = $this->_get_id_from_username ($user))
			{
				$salt_stmt = $this->_database->prepare (
					'SELECT salt '.
					'FROM   admin_salts '.
					'WHERE  admin_id=:id'
				);
				$salt_stmt->bindValue (':id', $admin_id, \PDO::PARAM_INT);
				$salt_stmt->execute ();
				
				if ($salt_record = $salt_stmt->fetch (\PDO::FETCH_ASSOC))
					return $salt_record ['salt'];
			}
			
			return false;
		}
	}
}
?>

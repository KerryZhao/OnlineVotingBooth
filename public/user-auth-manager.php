<?php
if (! class_exists ('UserAuthManager'))
{
	require 'auth-manager.php';
	require 'models/auth-user.php';
	require 'models/user-vote.php';
	
	/**
	 * @class UserAuthManager
	 *
	 * A class that manages the authentication and
	 * database management of voting users.
	 */
	
	class UserAuthManager extends AuthManager
	{
		const AUTH_TYPE      = 'auth_type';
		const USER_AUTH_TYPE = 'auth_type_user';
		
		const USER_ID             = 'user_id';
		const USER_USERNAME       = 'user_username';
		const USER_FIRST_NAME     = 'user_first_name';
		const USER_LAST_NAME      = 'user_last_name';
		const USER_NEED_VERIFY    = 'user_need_verify';
		const USER_VOTE           = 'user_vote';
		const VOTE_CANDIDATE_ID   = 'vote_candidate_id';
		const VOTE_CANDIDATE_NAME = 'vote_candidate_name';
		
		
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
		 * Returns the authorized user object.
		 */
		public function auth_user ()
		{
			return $this->_auth_object;
		}
		
		/**
		 * Returns whether the user has already voted.
		 */
		public function user_has_voted ()
		{
			return (($this->_auth_object != false) && ($this->_auth_object->vote () != false));
		}
		
		/**
		 * Sets the currently authenticated user's vote.
		 * This is only set locally not in the database.
		 */
		public function set_user_vote ($candidate_id, $candidate_name)
		{
			$_SESSION [parent::_SESSION_AUTH_KEY] [self::USER_VOTE] = array (
				self::VOTE_CANDIDATE_ID   => $candidate_id,
				self::VOTE_CANDIDATE_NAME => $candidate_name,
			);
		}
		
		/**
		 * Clears the currently authenticated user's vote.
		 * This is only set locally not in the database.
		 */
		public function reset_user_vote ()
		{
			$_SESSION [parent::_SESSION_AUTH_KEY] [self::USER_VOTE] = false;
		}
		
		public function user_verified ()
		{
			$_SESSION [parent::_SESSION_AUTH_KEY] [self::USER_NEED_VERIFY] = false;
		}
		
		public function cutoff_has_passed ()
		{
			$statement = $this->_database->prepare (
				'SELECT cutoff_datetime FROM cutoff_date LIMIT 1'
			);
			if ($statement->execute())
			{
				$cutoff_date = $statement->fetch( \PDO::FETCH_ASSOC );
				return intval($cutoff_date['cutoff_datetime']) < time();
			}
		}	
		
		public function has_security_questions ()
		{
			$question_check = false;

			$statement = $this->_database->prepare (
				'SELECT COUNT (user_id) ' .
				'FROM answers WHERE user_id=:user '
			);
			$cheese = $this->auth_user()->id();
			$statement->bindValue (':user', $cheese, \PDO::PARAM_INT);

			$statement->execute ();

			if ($first_login = $statement->fetch (\PDO::FETCH_NUM)) 
			{
				if ($first_login[0] == 0) {
					$question_check = true;
				}
			}

			return $question_check;

		}

		public function ask_question_time () 
		{
			$ask_question = false;

			$statement = $this->_database->prepare (
				'SELECT login_attempt_ip, pass_status FROM login_attempts ' .
				'WHERE login_attempt_ip=:ip ' .
				'ORDER BY login_attempt_timestamp DESC'
			);

			$cheese = $_SERVER ['REMOTE_ADDR'];
			$statement->bindValue (':ip', $cheese, \PDO::PARAM_STR);

			$statement->execute ();

			if ($ip_array = $statement->fetch (\PDO::FETCH_ASSOC)) 
			{
				if (! $ip_array['pass_status'])
				{
					$ask_question = true;
				}
			}
			
			if (! $ask_question && $this->auth_user ())
			{
				$statement = $this->_database->prepare (
					'SELECT login_attempt_ip FROM login_attempts ' .
					'WHERE user_id=:user ' .
					'ORDER BY login_attempt_timestamp DESC'
				);
				
				$bread = $this->auth_user ()->id ();
				$statement->bindValue (':user', $bread, \PDO::PARAM_INT);

				$statement->execute ();

				if ($ip_array = $statement->fetch (\PDO::FETCH_ASSOC)) 
				{
					if ($ip_array['login_attempt_ip'] != $_SERVER['REMOTE_ADDR'])
					{
						$ask_question = true;
					}
				}
			}
			
			return $ask_question;
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
					'SELECT user_id, user_username, user_first_name, user_last_name '.
					'FROM   users '.
					'WHERE  user_username=:user '.
					'AND    user_password=:hash'
				);
				$user_stmt->bindValue (':user', $user, \PDO::PARAM_STR);
				$user_stmt->bindValue (':hash', $hash, \PDO::PARAM_STR);
				
				$user_stmt->execute ();
				
				if ($user_record = $user_stmt->fetch (\PDO::FETCH_ASSOC))
				{
					$vote_stmt = $this->_database->prepare (
						'SELECT votes.candidate_id, candidates.candidate_name '.
						'FROM   votes '.
						'INNER JOIN candidates ON candidates.candidate_id=votes.candidate_id ' .
						'WHERE  votes.user_id=:user_id'
					);
					$vote_stmt->bindValue (':user_id', $user_record ['user_id'], \PDO::PARAM_INT);
					
					$vote_stmt->execute ();
					
					$session_data = array (
						self::AUTH_TYPE       => self::USER_AUTH_TYPE,
						self::USER_ID         => $user_record ['user_id'],
						self::USER_USERNAME   => $user_record ['user_username'],
						self::USER_FIRST_NAME => $user_record ['user_first_name'],
						self::USER_LAST_NAME  => $user_record ['user_last_name'],
						self::USER_NEED_VERIFY => false,
						self::USER_VOTE       => false,
					);
					
					if ($vote_record = $vote_stmt->fetch (\PDO::FETCH_ASSOC))
					{
						$session_data [self::USER_VOTE] = array (
							self::VOTE_CANDIDATE_ID   => $vote_record ['candidate_id'],
							self::VOTE_CANDIDATE_NAME => $vote_record ['candidate_name'],
						);
					}
				}
			}
			
			if ($session_data != false)
				$session_data [self::USER_NEED_VERIFY] = $this->ask_question_time ();
			
			//
			if (isset($session_data[self::USER_ID]))
			{
				$attempt_stmt = $this->_database->prepare (
				'SELECT pass_status FROM login_attempts ' .
				'WHERE login_attempt_ip=:ip ' . //AND user_id=:user ' .
				'ORDER BY login_attempt_timestamp DESC LIMIT 1'
			);
			//$attempt_stmt->bindValue (':user', $session_data[self::USER_ID], \PDO::PARAM_INT); 
			} 
			else 
			{
				$attempt_stmt = $this->_database->prepare (
				'SELECT pass_status FROM login_attempts ' .
				'WHERE login_attempt_ip=:ip './/AND user_id IS NULL ' .
				'ORDER BY login_attempt_timestamp DESC LIMIT 1'
			);
			}
			$attempt_stmt->bindValue (':ip', 
       			$ipaddress = $_SERVER['REMOTE_ADDR'], \PDO::PARAM_STR);

			$attempt_stmt->execute ();
			

			$status = false;
			if ($session_data == false) 
			{
				$status = false;
			}
			else
			{
				$status = true;
			}


			$same_status = false;
			if ($fail = $attempt_stmt->fetch (\PDO::FETCH_ASSOC))
			{
				if ($fail['pass_status'] == $status) 
				{
					$same_status = true;
				}
			}

			if ($same_status == false)
			{
				$login_stmt = $this->_database->prepare (
					'INSERT INTO login_attempts (user_id, ' .
					'login_attempt_ip, login_attempt_timestamp, pass_status) ' .
					'VALUES (:user, :ip, ' .
					':timestamp, :status) '
				);	

				if (isset($session_data[self::USER_ID]))
				{
				$login_stmt->bindValue (':user', $session_data[self::USER_ID], \PDO::PARAM_INT); 
				} 
				else 
				{
					$login_stmt->bindValue (':user', null, \PDO::PARAM_NULL);
				}
				$login_stmt->bindValue (':ip', 
    	   			$ipaddress = $_SERVER['REMOTE_ADDR'], \PDO::PARAM_STR);
				$login_stmt->bindValue (':timestamp', time(), \PDO::PARAM_INT);


				$login_stmt->bindValue (':status', $status, \PDO::PARAM_BOOL);
				
				$login_stmt->execute ();
			}

			return $session_data;
		}
		
		/**
		 * Sets the _auth_object member variable from the given session data.
		 */
		/* override */ protected function _get_auth_object_from_session ($session_data)
		{
			$user_vote = false;
			if ($session_data [self::USER_VOTE] != false)
			{
				$user_vote = new UserVote (
					$session_data [self::USER_ID],
					$session_data [self::USER_VOTE] [self::VOTE_CANDIDATE_ID],
					$session_data [self::USER_VOTE] [self::VOTE_CANDIDATE_NAME]
				);
			}
			
			$this->_auth_object = new AuthUser (
				$session_data [self::USER_ID],
				$session_data [self::USER_USERNAME],
				$session_data [self::USER_FIRST_NAME],
				$session_data [self::USER_LAST_NAME],
				$user_vote
			);
		}
		
		/**
		 * Returns whether the authentication information in the session is
		 * for a voting user.
		 */
		/* override */ protected function _correct_auth_type ($session_data)
		{
			return (isset ($session_data [self::AUTH_TYPE]) && $session_data [self::AUTH_TYPE] == self::USER_AUTH_TYPE);
		}
		
		/**
		 * Returns the id associated with the given username.
		 */
		private function _get_id_from_username ($user)
		{
			$user_stmt = $this->_database->prepare (
				'SELECT user_id '.
				'FROM   users '.
				'WHERE  user_username=:user'
			);
			$user_stmt->bindValue (':user', $user, \PDO::PARAM_STR);
			$user_stmt->execute ();
			
			if ($user_record = $user_stmt->fetch (\PDO::FETCH_ASSOC))
				return $user_record ['user_id'];
			
			return false;
		}
		
		/**
		 * Returns the salt associated with the given username.
		 */
		private function _get_salt_from_username ($user)
		{
			if ($user_id = $this->_get_id_from_username ($user))
			{
				$salt_stmt = $this->_database->prepare (
					'SELECT salt '.
					'FROM   salts '.
					'WHERE  user_id=:id'
				);
				$salt_stmt->bindValue (':id', $user_id, \PDO::PARAM_INT);
				$salt_stmt->execute ();
				
				if ($salt_record = $salt_stmt->fetch (\PDO::FETCH_ASSOC))
					return $salt_record ['salt'];
			}
			
			return false;
		}
	}
}
?>

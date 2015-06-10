<?php
if (! class_exists ('AdminView'))
{
	class AdminView
	{
		const _CREATE_USER_FIELD = 'create-user';
		const _CREATE_CANDIDATE_FIELD = 'create-candidate';
		const _RESET_VOTE_FIELD = 'reset-vote';
		const _SET_CUTOFF_DATE_FIELD = 'set-cutoff-date';
		
		const _FIRSTNAME_FIELD = 'firstname';
		const _LASTNAME_FIELD = 'lastname';
		const _USERNAME_FIELD = 'username';
		const _NAME_FIELD = 'name';
		
		const _CUTOFF_DATE_FIELD = 'cutoff-date';
	
		private $_database = null;
		private $_errors = false;
		
		public function __construct (&$database)
		{
			$this->_database = $database;
		}
		
		public function show()
		{
			$this->_handle_form_submission ();
			$this->_print_html ();
		}
		
		private function _handle_form_submission ()
		{
			$this->_errors = false;
			
			if (isset ($_POST [self::_CREATE_USER_FIELD]))
			{
				if (! isset ($_POST [self::_FIRSTNAME_FIELD]) or
					! isset ($_POST [self::_LASTNAME_FIELD]) or
					! isset ($_POST [self::_USERNAME_FIELD]))
				{
					$this->_errors ['missing_field'] = 'Fill out all fields';
				}
				else
				{
					$random_password = bin2hex(openssl_random_pseudo_bytes(16));
					$random_salt = openssl_random_pseudo_bytes(2);
					$digest = hash("sha256", $random_password . $random_salt);
					
					$statement = $this->_database->prepare (
						'INSERT INTO users (user_first_name, user_last_name, user_username, user_password) '.
						'VALUES (:user_first_name, :user_last_name, :user_username, :user_password)'
					);
					$statement->bindValue (':user_first_name', $_POST [self::_FIRSTNAME_FIELD], \PDO::PARAM_STR);
					$statement->bindValue (':user_last_name', $_POST [self::_LASTNAME_FIELD], \PDO::PARAM_STR);
					$statement->bindValue (':user_username', $_POST [self::_USERNAME_FIELD], \PDO::PARAM_STR);
					$statement->bindValue (':user_password', $digest, \PDO::PARAM_STR);
					
					if ($statement->execute ())
					{
						$statement = $this->_database->prepare (
							'INSERT INTO salts (user_id, salt) '.
							'VALUES ( (SELECT user_id FROM users WHERE user_username=:user_username LIMIT 1), :salt)'
						);
						$statement->bindValue (':user_username', $_POST [self::_USERNAME_FIELD], \PDO::PARAM_STR);
						$statement->bindValue (':salt', $random_salt, \PDO::PARAM_STR);
						
						$statement->execute();
					}
				}
				
					echo $random_password;
			}
			
			else if (isset ($_POST [self::_CREATE_CANDIDATE_FIELD]))
			{
				if (! isset ($_POST [self::_NAME_FIELD]))
				{
					$this->_errors ['missing_field'] = 'Fill out all fields';
				}
				else
				{
					$statement = $this->_database->prepare (
						'INSERT INTO candidates (candidate_name) '.
						'VALUES (:candidate_name)'
					);
					$statement->bindValue (':candidate_name', $_POST [self::_NAME_FIELD], \PDO::PARAM_STR);
					
					if ($statement->execute ())
					{
						echo 'Candidate created.';
					}
				}
			}
			
			else if (isset ($_POST [self::_RESET_VOTE_FIELD]))
			{
				$statement = $this->_database->prepare (
					'TRUNCATE votes, candidates'
				);
				if ($statement->execute())
				{
					echo 'Votes and Candidates cleared.';
				}
			}
			
			else if (isset ($_POST [self::_SET_CUTOFF_DATE_FIELD]))
			{
				$statement = $this->_database->prepare (
					'TRUNCATE cutoff_date'
				);
				if ($statement->execute())
				{
					echo 'Vote cutoff date reset.';
					$statement = $this->_database->prepare (
						'INSERT INTO cutoff_date (cutoff_datetime) '.
						'VALUES (:cutoff_datetime)'
					);
					$timestr = strtotime( $_POST [self::_CUTOFF_DATE_FIELD]);
					$statement->bindValue (':cutoff_datetime', $timestr, \PDO::PARAM_INT);
					if ($statement->execute())
					{
						echo 'Vote cutoff date set.';
					}
				}
			}
		}
		
		private function _print_html ()
		{
			global $auth_manager;
			$form_errors = &$this->_errors;
			
			require 'admin.php';
		}
	}
}
?>
<?php
if (! class_exists ('VoteView'))
{
	class VoteView
	{
		const _SUBMIT_FIELD    = 'submit';
		const _CANDIDATE_FIELD = 'candidate';
		
		
		// The database connection.
		private $_database = null;
		// Any errors from the form submission.
		private $_errors = false;
		
		
		public function __construct (&$database)
		{
			$this->_database = $database;
		}
		
		public function show ()
		{
			$this->_handle_form_submission ();
			$this->_print_html ();
		}
		
		private function _handle_form_submission ()
		{
			global $auth_manager;
			$this->_errors = false;
			
			if (isset ($_POST [self::_SUBMIT_FIELD]))
			{
				if (! isset ($_POST [self::_CANDIDATE_FIELD]))
				{
					$this->_errors ['candidate_empty'] = 'Please choose a starter pokemon';
				}
				else
				{
					$statement = $this->_database->prepare (
						'INSERT INTO votes (user_id, candidate_id, vote_timestamp) '.
						'VALUES (:user_id, :candidate_id, :timestamp)'
					);
					$statement->bindValue (':user_id', $auth_manager->auth_user ()->id (), \PDO::PARAM_INT);
					$statement->bindValue (':candidate_id', $_POST [self::_CANDIDATE_FIELD], \PDO::PARAM_INT);
					$statement->bindValue (':timestamp', time (), \PDO::PARAM_INT);
					
					$statement->execute ();
					
					$auth_manager->set_user_vote ($_POST [self::_CANDIDATE_FIELD], $_POST ['candidate_name']);
					
					// Reload the current page
					header ('Location: ' . $_SERVER ['REQUEST_URI']);
				}
			}
			else if (isset ($_POST ['logout']))
			{
				$auth_manager->deauthenticate();
				
				// Reload the current page
				header ('Location: ' . $_SERVER ['REQUEST_URI']);
			}
		}
		
		private function _print_html ()
		{
			$form_errors = &$this->_errors;
			$candidates = array ();
			global $auth_manager;
			
			$result = $this->_database->query (
				'SELECT candidate_id, candidate_name '.
				'FROM   candidates'
			);
			
			if ($result)
			{
				while ($record = $result->fetch (PDO::FETCH_ASSOC))
				{
					if ($record)
					{
						$candidates [] = array (
							'id'   => $record ['candidate_id'],
							'name' => $record ['candidate_name'],
						);
					}
				}
			}
			
			require 'vote.php';
		}
	}
}
?>

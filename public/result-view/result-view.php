<?php
if (! class_exists ('ResultView'))
{
	class ResultView
	{	
		
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
			global $auth_manager;
			if (isset ($_POST ['logout']))
			{
				$auth_manager->deauthenticate();
				
				// Reload the current page
				header ('Location: ' . $_SERVER ['REQUEST_URI']);
			}
			$this->_print_html ();
		}
		
		private function _print_html ()
		{
			$form_errors = &$this->_errors;
			$votes = array ();
			global $auth_manager;
			
			$result = $this->_database->query (
				'SELECT candidates.candidate_id as id, candidates.candidate_name as name, count(votes.candidate_id) as tally ' .
				'FROM   candidates ' .
				'LEFT JOIN votes ON candidates.candidate_id = votes.candidate_id ' .
				'GROUP BY candidates.candidate_id ' .
				'ORDER BY candidates.candidate_id'
			);
			
			if ($result)
			{
				while ($record = $result->fetch (PDO::FETCH_ASSOC))
				{
					if ($record)
					{
						$votes [] = array (
							'id'   => $record ['id'],
							'name' => $record ['name'],
							'tally' => $record ['tally'],
						);
					}
				}
			}
			
			require 'result.php';
		}
	}
}
?>

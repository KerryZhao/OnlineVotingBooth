<?php
if (! class_exists ('StatusView'))
{
	class StatusView
	{
		// The database connection.
		private $_auth_manager = null;
		private $_database = null;
		
		
		public function __construct (&$auth_manager, &$database)
		{
			$this->_auth_manager = $auth_manager;
			$this->_database = $database;
		}
		
		public function show ()
		{
			$this->_handle_form_submission ();
			$this->_print_html ();
		}
		
		private function _handle_form_submission ()
		{
			if (isset ($_POST ['clear']))
			{
				$statement = $this->_database->prepare (
					'DELETE FROM votes '.
					'WHERE user_id=:user_id'
				);
				$statement->bindValue (':user_id', $this->_auth_manager->auth_user ()->id (), \PDO::PARAM_INT);
				
				$statement->execute ();
				
				$this->_auth_manager->reset_user_vote ();
				
				// Reload the current page
				header ('Location: ' . $_SERVER ['REQUEST_URI']);
			}
		}
		
		private function _print_html ()
		{
			$auth_manager = &$this->_auth_manager;
			
			require 'status.php';
		}
	}
}
?>

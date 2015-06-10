<?php
if (! class_exists ('TwoStepAuthView'))
{
	/**
	 * @class TwoStepAuthView
	 *
	 */
	
	class TwoStepAuthView
	{
		const _SUBMIT_ANSWERS_FIELD = 'submit';
		
		
		// The database connection.
		private $_auth_manager  = null;
		// Any errors from the form submission.
		private $_errors = false;
		
		private $_database;
		private $_questions;
		
		
		public function __construct (&$auth_manager, &$database)
		{
			$this->_auth_manager = $auth_manager;
			$this->_database = $database;
			
			$statement = $this->_database->prepare (
				'SELECT question_id, question FROM questions'
			);
			$statement->execute();
			$this->_questions = $statement->fetchAll (\PDO::FETCH_ASSOC);
		}
		
		public function show ()
		{
			$this->_handle_form_submission ();
			$this->_print_html ();
		}
		
		private function _handle_form_submission ()
		{
			$this->_errors = false;
			
			if (isset ($_POST [self::_SUBMIT_ANSWERS_FIELD]))
			{
				if (isset ($_POST ['question_value']))
				{
					if(! empty($_POST ['question_value']))
					{
						if(strlen($_POST['question_value']) <= 256)
						{
							$statement = $this->_database->prepare (
								'SELECT answer FROM answers WHERE question_id=:question_id'
							);
							$statement->bindValue(':question_id', $_POST['question_id'], \PDO::PARAM_INT);
							
							$statement->execute();
							
							if ($answer = $statement->fetch (\PDO::FETCH_ASSOC))
							{
								if ($answer ['answer'] == $_POST ['question_value'])
								{
									$this->_auth_manager->user_verified ();
								}
							}
						}
						else
							$this->_errors ['exceed answer size'] = 'Answer too long';
					}
				}
				
				header('Location: ' . $_SERVER ['REQUEST_URI']);
			}
		}
		
		private function _print_html ()
		{
			$question = $this->_questions [rand (0, count ($this->_questions) - 1)];
			$form_errors = &$this->_errors;
			
			require 'two-step-auth.php';
		}
	}
}
?>

<?php
if (! class_exists ('QuestionsView'))
{
	/**
	 * @class QuestionsView
	 *
	 */
	
	class QuestionsView
	{
		const _ANSWER_1_FIELD = 'answer1';
		const _ANSWER_2_FIELD = 'answer2';
		const _ANSWER_3_FIELD = 'answer3';
		const _ANSWER_4_FIELD = 'answer4';
		const _ANSWER_5_FIELD = 'answer5';
		const _ANSWER_6_FIELD = 'answer6';
		const _ANSWER_7_FIELD = 'answer7';
		const _ANSWER_8_FIELD = 'answer8';
		const _ANSWER_9_FIELD = 'answer9';
		const _ANSWER_10_FIELD = 'answer10';
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
			$this->_questions = $statement->fetchAll(\PDO::FETCH_ASSOC);
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
				foreach($this->_questions as $question)
				{
					if (isset ($_POST [$question['question_id']]))
					{
						if(! empty($_POST [$question['question_id']]))
						{
							if(strlen($_POST[$question['question_id']]) <= 256)
							{
								$statement = $this->_database->prepare (
									'INSERT INTO answers (user_id, question_id, answer) '.
									'VALUES (:user_id, :question_id, :answer)'
								);
								$statement->bindValue(':user_id', $this->_auth_manager->auth_user()->id(), \PDO::PARAM_INT);
								$statement->bindValue(':question_id', $question['question_id'], \PDO::PARAM_INT);
								$statement->bindValue(':answer', $_POST[$question['question_id']], \PDO::PARAM_STR);
								
								$statement->execute();
							}
							else
								$this->_errors ['exceed answer size'] = 'Answer too long';
						}
					}
				}
				
				header('Location: ' . $_SERVER ['REQUEST_URI']);
				
			}
		}
		
		private function _print_html ()
		{
			$questions = $this->_questions;
			$form_errors = &$this->_errors;
			
			require 'questions.php';
		}
	}
}
?>

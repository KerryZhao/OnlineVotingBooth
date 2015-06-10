<?php
if (! class_exists ('UserVote'))
{
	class UserVote
	{
		private $_user;
		private $_candidate_id;
		private $_candidate_name;
		
		
		public function __construct ($user, $candidate_id, $candidate_name)
		{
			$this->_user = $user;
			$this->_candidate_id = $candidate_id;
			$this->_candidate_name = $candidate_name;
		}
		
		public function user ()         { return $this->_user; }
		public function candidate_id ()   { return $this->_candidate_id; }
		public function candidate_name () { return $this->_candidate_name; }
	}
}
?>

<?php
if (! class_exists ('AuthUser'))
{
	class AuthUser
	{
		private $_id;
		private $_username;
		private $_first_name;
		private $_last_name;
		private $_vote;
		
		
		public function __construct ($id, $username, $first_name, $last_name, $vote)
		{
			$this->_id = $id;
			$this->_username = $username;
			$this->_first_name = $first_name;
			$this->_last_name = $last_name;
			$this->_vote = $vote;
		}
		
		public function id ()         { return $this->_id; }
		public function username ()   { return $this->_username; }
		public function first_name () { return $this->_first_name; }
		public function last_name ()  { return $this->_last_name; }
		public function vote ()       { return $this->_vote; }
	}
}
?>

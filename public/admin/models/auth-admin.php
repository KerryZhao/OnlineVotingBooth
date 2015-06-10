<?php
if (! class_exists ('AuthAdmin'))
{
	class AuthAdmin
	{
		private $_id;
		private $_username;
		
		
		public function __construct ($id, $username)
		{
			$this->_id = $id;
			$this->_username = $username;
		}
		
		public function id ()       { return $this->_id; }
		public function username () { return $this->_username; }
	}
}
?>

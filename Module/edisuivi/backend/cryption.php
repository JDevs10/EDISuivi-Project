<?php

class Cryption {
	
	public $db;
	private $hash_;

	function __construct($db_){ 
		$this->db = $db_;
	}
	
	public function returnHash(){
		return $this->hash_;
	}
	
	public function encryption($password){
		$this->hash_ = password_hash($password, PASSWORD_DEFAULT);
	}
	
	public function verify($password, $hash){
		if (password_verify($password, $hash)) {
			return true;
		}
		return false;
	}
}

//print("<pre>".print_r($xxx, true)."</pre>");
?>
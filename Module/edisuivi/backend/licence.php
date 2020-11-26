<?php

class Licence {
	
	public $db;
	private $EDISUIVI_KEY = "NaN";

	
	function __construct($db_){ 
		$this->db = $db_;
	}
	
	
	public function return_Data(){
		return $this->EDISUIVI_KEY;
	}
	
	public function getKey(){
		// get licence key
		$sql = "SELECT api_key FROM llx_user where login = 'edisuivi' AND lastname = 'edisuivi' AND admin = 1";
		$res = $this->db->query($sql);


		if ($res->num_rows > 0) {
			$row = $this->db->fetch_array($sql);

			$this->EDISUIVI_KEY = $row['api_key'];
		}
	}
}

//print("<pre>".print_r($xxx, true)."</pre>");
?>
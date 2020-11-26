<?php

class Check {
	
	public $db;
	private $folder = "/backend";
	private $scripts = array(
		'generate-new-entreprise-ref.php',
		'generate-new-utilisateur-ref.php',
		'check-db.php',
		'check.php',
	);

	
	function __construct($db_){ 
		$this->db = $db_;
	}
	
	
	public function check_scrips(){
		// Check all backend scripts before loading...
		$res = true;
		$error_msg = "";
		
		for($i=0; $i < count($this->scripts); $i++){
			//print("<p>".getcwd().$this->folder."/".$this->scripts[$i]."</p>");
			
			if(!is_file(getcwd().$this->folder."/".$this->scripts[$i])){
				$res = false;
				$error_msg .= "Backend script ' ".$this->folder."/".$this->scripts[$i]." ' is missing! <br>";
			}else{
				if(filesize(getcwd().$this->folder."/".$this->scripts[$i]) < 1){
					$res = false;
					$error_msg .= "Backend script ' ".$this->folder."/".$this->scripts[$i]." ' is empty! <br>";
				}
			}
		}
		
		if($res){
			return array('STATUS' => 'true', 'MESSAGE' => '');
		}else{
			return array('STATUS' => 'false', 'MESSAGE' => $error_msg);
		}
	}
}

//print("<pre>".print_r($xxx, true)."</pre>");
?>
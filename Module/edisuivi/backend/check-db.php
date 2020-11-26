<?php

class CheckDB {
	
	public $db;
	
	function __construct($db_){ 
		$this->db = $db_;
	}
	
	
	function find_classes(){
		$path = getcwd()."/class";
		$j=0;
		$res = [];
		$files = scandir($path);
		
		for($i=0; $i < count($files); $i++){
			if($files[$i] != "." && $files[$i] != ".."){
				//print "<p>files => $files[$i]</p>";
				if(!strpos($files[$i], ".class.php.back") && (strpos($files[$i], "api_") !== 0)){  
					//print "<p>files => explode('.', $files[$i])[0]</p>";
					$res[$j] = explode(".", $files[$i])[0];
					$j++;
				}
			}
		}
		return $res;
	}
	
	function check_tables(){
		$foundTables = [];
		$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.tables WHERE TABLE_NAME LIKE 'llx_edisuivi_%' AND TABLE_NAME NOT LIKE '%_extrafields'";
		$res = $this->db->query($sql);
		
		if ($res->num_rows > 0) {
			
			$i =0;
			while($row = $this->db->fetch_array($sql)){
				$foundTables[$i] = explode("_", $row['TABLE_NAME'])[2];
				$i++;
			}
		}
		return $foundTables;
	}
	
	public function checking(){
		$ctp = 0;
		$res = "false";
		$file_names = $this->find_classes();
		$table_names = $this->check_tables();
		
		//print("<pre>".print_r($file_names, true)."</pre>");
		//print("<pre>".print_r($table_names, true)."</pre>");
		
		if(count($file_names) == 0){ return "false";}
		if(count($table_names) == 0){return "false";}
		
		for($i=0; $i < count($file_names); $i++){
			for($j=0; $j < count($table_names); $j++){
				
				if($file_names[$i] == $table_names[$j]){
					$ctp++;
					break;
				}
			}
		}
		
		//print "((".count($file_names)." + ".count($table_names).")) / 2) == ".$ctp;
		if( ((count($file_names) + count($table_names)) / 2) == $ctp ){
			$res="true";
		}
		
		return $res;

	}
	
}
//print("<pre>".print_r($xxx, true)."</pre>");

?>
<?php
class GenerateNewUtilisateurRef {
	
	public $db;
	public $_MASK = "USER-";
	private $_NEW_REF_VALUE = 1;
	private $_REF_LENGHT = 8;
	private $_MASK_REF_LENGHT = 12;
	private $_NEW_REF;
	
	public function __construct($db_){ 
		$this->db = $db_;
	}
	
	public function return_Data(){
		return $this->_NEW_REF;
	}
	
	// generate new ref
	public function generate(){
		
		// get latest ref
		$new_ref = "";
		$splited_ref = "";
		$old_ref = "";
		$old_mask_ref = $this->getLastRef();
		
		if($old_mask_ref != null || $old_mask_ref != ""){
			
			// generate next ref
			// exp: USER-00000010
			$old_ref = explode('-', $old_mask_ref)[1];
			$old_ref = (int)$old_ref;
			$old_ref++;
			
			$this->_NEW_REF = $this->_MASK;
			for($i=strlen($this->_NEW_REF."".$old_ref); $i<$this->_MASK_REF_LENGHT; $i++)
			{
				$this->_NEW_REF .= "0";
			}
			$this->_NEW_REF .= "".$old_ref;
		}else{
			
			// generate first ref
			$this->_NEW_REF = $this->_MASK;
			for($i=strlen($this->_NEW_REF."".$this->_NEW_REF_VALUE); $i<$this->_MASK_REF_LENGHT; $i++)
			{
				$this->_NEW_REF .= "0";
			}
			$this->_NEW_REF .= $this->_NEW_REF_VALUE;
		}
	}

	
	// get latest ref from db
	function getLastRef(){
		$endRes = null;
		$sql = "SELECT * FROM llx_edisuivi_utilisateur as ent ORDER BY ent.ref DESC LIMIT 1";
		$res = $this->db->query($sql);
		
		if ($res->num_rows > 0) {
			
			while($row = $this->db->fetch_array($sql)){

				$endRes = $row['ref'];
			}
			//print("<pre>".print_r($this->account_data, true)."</pre>");
		}
		return $endRes;
	}
	
}

//$load = new Load_User_Statistic();
//print("<pre>".print_r($load->Load_Data(), true)."</pre>");
?>
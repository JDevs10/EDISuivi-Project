<?php
class AddComment {
	
	public $db;
	private $NEW_COMMENT = null;
	private $ALL_COMMENTS = null;

	
	function __construct($db_){ 
		$this->db = $db_;
	}
	
	public function setComment($origin_id, $message, $fk_soc, $user){
		return $this->NEW_COMMENT = array(
			'origin_id'=> $origin_id,
			'message'=> $message,
			'fk_soc'=> $fk_soc,
			'user'=> $user
		);
	}
	
	public function addComment(){
		$sql = "INSERT llx_edisuivi_commentaire (rowid, origin_id, date_creation, text, date_modification, edited, fk_soc, fk_user) ";
		$sql .= "VALUES (null, ".$this->NEW_COMMENT['origin_id'].", CURRENT_TIMESTAMP, '".str_replace("'", "''", $this->NEW_COMMENT['message'])."', null, null, ".$this->NEW_COMMENT['fk_soc'].", ".$this->NEW_COMMENT['user'].")";
		$res = $this->db->query($sql);
		header("Location: ".$_SERVER['PHP_SELF']."?orderId=".$this->NEW_COMMENT['origin_id']); 
	}
	
	public function getAllCommentsOfOrderById($orderId){
		
		$sql = "SELECT cmt.rowid, cmt.origin_id, cmt.date_creation, cmt.text, cmt.date_modification, cmt.edited, (SELECT s.nom FROM llx_societe as s WHERE s.rowid = cmt.fk_soc) as fk_soc, (SELECT u.lastname FROM llx_user as u WHERE u.rowid = cmt.fk_user) as fk_user ";
		$sql .= "FROM llx_edisuivi_commentaire as cmt ";
		$sql .= "WHERE cmt.origin_id = $orderId";
		$res = $this->db->query($sql);
		
		if($res->num_rows > 0){
			$index=0;
			while($row = $this->db->fetch_array($sql)){
				//print("<pre>".print_r($row,true)."</pre>");
				
				$this->ALL_COMMENTS[$index]['rowid'] = $row['rowid'];
				$this->ALL_COMMENTS[$index]['origin_id'] = $row['origin_id'];
				$this->ALL_COMMENTS[$index]['date_creation'] = $row['date_creation'];
				$this->ALL_COMMENTS[$index]['text'] = $row['text'];
				$this->ALL_COMMENTS[$index]['date_modification'] = $row['date_modification'];
				$this->ALL_COMMENTS[$index]['edited'] = $row['edited'];
				$this->ALL_COMMENTS[$index]['fk_soc'] = $row['fk_soc'];
				$this->ALL_COMMENTS[$index]['fk_user'] = $row['fk_user'];
				$index++;
			}
		}
		
		return $this->ALL_COMMENTS;
	}
	
	public function convertDateTimeToFrench($dateTime){
		/*
		<?php print strftime("%Hh%M", strtotime($val['date_creation'])); ?>
		print("<pre>".print_r($row,true)."</pre>");
		*/
	}
}
<?php
/* Copyright (C) 2015   Jean-FranÃ§ois Ferry     <jfefe@aternatik.fr>
 * Copyright (C) 2020 SuperAdmin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */


use Luracast\Restler\RestException;
use Luracast\Restler\Format\UploadFormat;

dol_include_once('/edisuivi/class/entreprise.class.php');
dol_include_once('/edisuivi/class/utilisateur.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

 

/**
 * \file    edisuivi/class/api_edisuivi.class.php
 * \ingroup edisuivi
 * \brief   File for API management of entreprise.
 */

/**
 * API class for edisuivi entreprise
 *
 * @access protected
 * @class  DolibarrApiAccess {@requires user,external}
 */
class EDISuiviApi extends DolibarrApi
{
    /**
     * @var Entreprise $entreprise {@type Entreprise}
     */
    public $entreprise;
	public $utilisateur;

    /**
     * Constructor
     *
     * @url     GET /
     *
     */
    public function __construct()
    {
        global $db, $conf;
        $this->db = $db;
        $this->entreprise = new Entreprise($this->db);
		$this->utilisateur = new Utilisateur($this->db);
    }
	
	
	
	/*##########################################################################################################################*/
	/*#############################################  Gestion Api Login  ########################################################*/
    
    /**
	 * Login
	 *
	 * Request the API token for a couple username / password.
	 * Using method POST is recommanded for security reasons (method GET is often logged by default by web servers with parameters so with login and pass into server log file).
	 * Both methods are provided for developer conveniance. Best is to not use at all the login API method and enter directly the "DOLAPIKEY" into field at the top right of page. Note: The API key (DOLAPIKEY) can be found/set on the user page.
	 *
	 * @param   string  $login			User login
	 * @param   string  $password		User password
	 * @param   string  $entity			Entity (when multicompany module is used). '' means 1=first company.
	 * @param   int     $reset          Reset token (0=get current token, 1=ask a new token and canceled old token. This means access using current existing API token of user will fails: new token will be required for new access)
     * @return  array                   Response status and user token
     *
	 * @throws 200
	 * @throws 403
	 * @throws 500
	 *
	 * @url GET /login/
	 * @url POST /login/
	 */
	 
	public function login($login, $password, $entity='', $reset=0) 
	{	
		$sql_ = "SELECT * FROM llx_edisuivi_utilisateur WHERE identifiant = '".$login."'";
		$res = $this->db->query($sql_);
		
		$identifiant;
		$pass;
		$pass_crypted;
		
		if ($res->num_rows > 0) {
			
			while($row = $this->db->fetch_array($sql_)){
				//print("<pre>".print_r($row, true)."</pre>");
				$identifiant = $row['identifiant'];
				$pass = $row['pass'];
				$pass_crypted = $row['pass_crypted'];
			}
		}else{
			return array(
				'error' => array(
					'code' => 401,
					'message' => "Le compte saisi est incorrect. Veuillez reessayer."
				)
			);
		}
		
		/*
		if($pass_crypted == null){
			return array(
				'error' => array(
					'code' => 401,
					'message' => 'Mot de passe incorrect. Veuillez reessayer.')
				)
			);
		}
		*/
		
		$sql_  = "SELECT user.ref, user.identifiant, user.last_connexion, user.user_type, ent.label, ent.nb_commandes, soc.rowid as socid, soc.nom, soc.code_client, soc.code_fournisseur, u.api_key ";
		$sql_ .= "FROM llx_edisuivi_utilisateur as user, llx_edisuivi_entreprise as ent, llx_societe as soc, llx_user as u ";
		$sql_ .= "WHERE user.identifiant = '".$login."' AND user.fk_entreprise = ent.rowid AND ent.fk_soc = soc.rowid AND u.login = 'edisuivi' AND u.lastname = 'edisuivi'";
		//print "SQL => ".$sql_; 
		
		$res = $this->db->query($sql_);
		$login_info;
		
		if ($res->num_rows > 0) {
			
			while($row = $this->db->fetch_array($sql_)){
				//print("<pre>".print_r($row, true)."</pre>");
				$login_info = array(
					'ref_user_EDISuivi' => $row['ref'],
					'identifiant_EDISuivi' => $row['identifiant'],
					'last_connexion' => $row['last_connexion'],
					'user_type_EDISuivi' => $row['user_type'],
					'nom_entreprise_EDISuivi' => $row['label'],
					'nb_commandes' => $row['nb_commandes'],
					'socid' => $row['socid'],
					'nom_entreprise' => $row['nom'],
					'code_client' => $row['code_client'],
					'code_fournisseur' => $row['code_fournisseur'],
					'token_EDISuivi' => $row['api_key']
				);
			}
			
			$date = new DateTime();
			$last_connexion__ = strftime("%Y-%m-%d %H:%M:%S", $date->getTimestamp() );
			$sql__ = "UPDATE llx_edisuivi_utilisateur SET last_connexion = '$last_connexion__' WHERE identifiant = '$login'";
			$res__ = $this->db->query($sql__);
			
			return array(
					'success' => array(
						'ref_user_EDISuivi' => $login_info['ref_user_EDISuivi'],
						'identifiant_EDISuivi' => $login_info['identifiant_EDISuivi'],
						'last_connexion' => $login_info['last_connexion'],
						'user_type_EDISuivi' => $login_info['user_type_EDISuivi'],
						'nom_entreprise_EDISuivi' => $login_info['nom_entreprise_EDISuivi'],
						'nb_commandes' => $login_info['nb_commandes'],
						'socid' => $login_info['socid'],
						'nom_entreprise' => $login_info['nom_entreprise'],
						'code_client' => $login_info['code_client'],
						'code_fournisseur' => $login_info['code_fournisseur'],
						'token_EDISuivi' => $login_info['token_EDISuivi']
					)
				);
				
		}else{
			return array(
				'error' => array(
					'code' => 402,
					'message' => "User info, user company and user EDICloud info was not found!"
				)
			);
		}
		
		//print("<pre>".print_r($resql,true)."</pre>"); 
	}
	
	
	
	/*##########################################################################################################################*/
	/*########################################  Gestion Api Charts  ##########################################################*/
	
	
	/**
     * Get numbre of orders per month, this and last year
     *
     * Return an objet with chart informations
     *
     * @param 	int 	$socId 			ID of entreprise
     * @return 	array|mixed 			data without useless information
     *
     * @url	GET chart/nb-orders-2-years
     * @throws 	RestException
     */
	public function getNbOrdersByMonthOfTwoYears($socId){
		
		$months = 12;
		$date = new DateTime();
		$currentYear = strftime("%Y", $date->getTimestamp() );
		
		$index = 0;
		$chartData = null;
		$chartDataLabels = null;
		
		for($y=($currentYear-2); $y < $currentYear; $y++){
			
			$chartDataLabels[$index] = ($y+1);
			for($x=0; $x < $months; $x++){
				$z = (strlen(($x+1)) == 1 ? "0".($x+1) : ($x+1));
				$sql  = "SELECT COUNT(*) as nb, date_creation ";
				$sql .= "FROM llx_commande WHERE date_creation LIKE '%".($y+1)."-$z%' ";
				$sql .= "AND fk_soc=$socId GROUP BY date_creation , rowid";
				
				//print("<pre>".print_r($sql, true)."</pre>");
				
				$res = $this->db->query($sql);
				$rows = $res->num_rows;
				$chartData[$index][$x] = $rows;
			}
			$index++;
		}
		
		
		return array(
			'success' => array(
				'title' => $chartDataLabels,
				'data' => $chartData
			)
		);
	}
	
	/**
	 *	Return Top 5 most ordered products
	 *
	 *  @url	GET chart/top-5-most-ordered-products/
     *  @throws 	RestException
	 */
	public function getTop5MostOrderedProducts(){
		$result;
		$sql  = "SELECT p.label, counting_best.n from (SELECT fk_product, count(fk_product) as n FROM `llx_commandedet` WHERE fk_product is not NULL GROUP by fk_product ORDER BY n desc LIMIT 5) as counting_best, llx_product as p ";
		$sql .= "WHERE counting_best.fk_product = p.rowid";
		
		$index = 0;
		$res = $this->db->query($sql);
		if ($res->num_rows > 0) {
			while($row = $this->db->fetch_array($sql_)){
				//print("<pre>".print_r($row, true)."</pre>");
				
				$result[$index]['label'] = $row['label'];
				$result[$index]['nb'] = $row['n'];
				
				$index++;
			}
		}
		
		return array(
			"success" => array(
				"data" => $result,
			)
		);
	}
	
	
	/**
	 *	Return Top 5 most ordered categories
	 *
	 *  @url	GET chart/top-5-most-ordered-categories/
     *  @throws 	RestException
	 */
	 public function getTop5MostOrderedCategories(){
		$result;
		$sql  = "SELECT p.ref, counting_best.n from (SELECT fk_product, count(fk_product) as n FROM `llx_commandedet` WHERE fk_product is not NULL GROUP by fk_product ORDER BY n desc LIMIT 5) as counting_best, llx_product as p ";
		$sql .= "WHERE counting_best.fk_product = p.rowid";
		
		$index = 0;
		$res = $this->db->query($sql);
		if ($res->num_rows > 0) {
			while($row = $this->db->fetch_array($sql)){
				//print("<pre>".print_r($row, true)."</pre>");
				
				$ref = str_replace("C","",$row['ref']);	// remove C in str
				$ref = str_replace("P","",$row['ref']); // remove P in str
				$sql_ = "SELECT c.label FROM llx_categorie as c, llx_categorie_product as cp, llx_product as p where c.rowid = cp.fk_categorie and cp.fk_product = p.rowid and p.ref = $ref";
				$res_ = $this->db->query($sql_);
				while($row_ = $this->db->fetch_array($sql_)){
					$result[$index]['label'] = $row_['label'];
				}
				
				$result[$index]['nb'] = $row['n'];
				$index++;
			}
		}
		
		return array(
			"success" => array(
				"data" => $result,
			)
		); 
	 }
	 
	 
	 /**
	 *	Return Le chiffre d'affaire par mois de deux ans
	 *
	 *  @url	GET chart/nb-budjet-last-2-years/
     *  @throws 	RestException
	 */
	 public function getNbBudjetLast2Years(){
		 $result;
		 $months = 11; //12 months
		 $currentYear = explode("-", date('Y-m-d'))[0];
		 
		 for($x=($currentYear-2); $x < $currentYear; $x++){
			for($y=0; $y < $months; $y++){
				$month = (strlen($y) == 1 ? "0".($y+1):($y+1));
				$sql  = "SELECT SUM(total_ttc) as total FROM llx_facture WHERE datec LIKE '$x-".$month."%'";
				
				
				while($row = $this->db->fetch_array($sql)){
					$result[$index]['month'] = $month;
					$result[$index]['total'] = $row['total'];
				}
				$index++;
			}
		 }
		 
		 return array(
			"success" => array(
				"data" => $result,
			)
		);
	 }
		 
		 
	/*##########################################################################################################################*/
	/*########################################  Gestion Api Commande  ##########################################################*/
	
	/**
	 *	Return label of status
	 *
	 *	@param		int		$status      	  Id status
	 *  @param      int		$billed    		  If invoiced
	 *	@param      int		$mode        	  0=Long label, 1=Short label
	 *  @param      int     $donotshowbilled  Do not show billed status after order status
	 *  @return     string					  Label of status
	 */
	function getStatusLabel($id, $billed, $mode, $lang = "FR"){
		
		$STOCK_NOT_ENOUGH_FOR_ORDER = -3; 	// ERR Not enough stock
		$STATUS_CANCELED = -1; 			// Canceled status
		$STATUS_DRAFT = 0; 				// Draft status
		$STATUS_VALIDATED = 1; 			// Validated status
		$STATUS_SHIPMENTONPROCESS = 2; 	// Shipment on process
		$STATUS_CLOSED = 3; 				// Closed (Sent, billed or not)
		
		global $langs, $conf;

		$labelStatus = "";
		$labelStatusShort = "";
		
		switch ($id) {
			case $STOCK_NOT_ENOUGH_FOR_ORDER:
				if($lang == "FR"){
					$labelStatus = "Commande Annule";
					$labelStatusShort = "Annule";
				}else{
					$labelStatus = "Order Canceled";
					$labelStatusShort = "Canceled";
				}
				break;
			case $STATUS_CANCELED:
				if($lang == "FR"){
					$labelStatus = "Commande Annule";
					$labelStatusShort = "Annule";
				}else{
					$labelStatus = "Order Canceled";
					$labelStatusShort = "Canceled";
				}
				break;
			case $STATUS_DRAFT:
				if($lang == "FR"){
					$labelStatus = "Broullion";
					$labelStatusShort = "Broullion";
				}else{
					$labelStatus = "Draft (needs to be validated)";
					$labelStatusShort = "Draft";
				}
				break;
			case $STATUS_VALIDATED:
				if($lang == "FR"){
					$labelStatus = "Commande Valide";
					$labelStatusShort = "Valide";
				}else{
					$labelStatus = "Order Validated";
					$labelStatusShort = "Validated";
				}
				break;
			case $STATUS_SHIPMENTONPROCESS:
				if($lang == "FR"){
					$labelStatus = "Commande en cours";
					$labelStatusShort = "En cours";
				}else{
					$labelStatus = "Order Shipment in process";
					$labelStatusShort = "Order In process";
				}
				break;
			case $STATUS_CLOSED:
				if($billed != -99){
					if(!$billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT)){
						if($lang == "FR"){
							$labelStatus = "Commande livree";
							$labelStatusShort = "Livree";
						}else{
							$labelStatus = "Order Delivered";
							$labelStatusShort = "Delivered";
						}
					}
					elseif ($billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT)){
						if($lang == "FR"){
							$labelStatus = "Commande Traitee";
							$labelStatusShort = "Traite";
						}else{
							$labelStatus = "Order Processed";
							$labelStatusShort = "Processed";
						}
					}
					elseif (!empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT)){
						if($lang == "FR"){
							$labelStatus = "Commande livree";
							$labelStatusShort = "Livree";
						}else{
							$labelStatus = "Order Delivered";
							$labelStatusShort = "Delivered";
						}
					}
				}else {
					if($lang == "FR"){
						$labelStatus = "Commande livree";
						$labelStatusShort = "Livree";
					}else{
						$labelStatus = "Order Delivered";
						$labelStatusShort = "Delivered";
					}
				}
				break;
			default:
				$labelStatus = "";
				$labelStatusShort = "";
		}
		
		$mode = ($mode == 0 ? true : false);
		if($mode){
			return $labelStatus;
		}else{
			return $labelStatusShort;
		}
	}
	
	public function getStatusLabel_v2($id, $lang){
		$RECEPTION_PARTIELLE = 0; 				// 
		$RECEPTION_COMPLETE = 1; 				// 
		$COMMANDE_NON_PLANIFIEE = 2; 			// 
		$COMMANDE_PLANIFIEE = 3; 				// 
		$COMMANDE_LIVREE_SANS_RESERVES = 4; 	// 
		$COMMANDE_LIVREE_AVEC_RESERVES = 5; 	//
		$ANNULEE = 6; 							// annulée


		$labelStatus = "";
		
		if($id == null || $id == "null" || $id == ""){
			return "";
		}
		
		switch ($id) {
			case $RECEPTION_PARTIELLE:
				if($lang == "FR"){
					$labelStatus = "RECEPTION PARTIELLE";
				}
				if($lang == "EN"){
					$labelStatus = "PARTIAL RECEPTION";
				}
				break;
			case $RECEPTION_COMPLETE:
				if($lang == "FR"){
					$labelStatus = "RECEPTION COMPLETE";
				}
				if($lang == "EN"){
					$labelStatus = "FULL RECEPTION";
				}
				break;
			case $COMMANDE_NON_PLANIFIEE:
				if($lang == "FR"){
					$labelStatus = "COMMANDE NON PLANIFIEE";
				}
				if($lang == "EN"){
					$labelStatus = "UNPLANNED ORDER";
				}
				break;
			case $COMMANDE_PLANIFIEE:
				if($lang == "FR"){
					$labelStatus = "COMMANDE PLANIFIEE";
				}
				if($lang == "EN"){
					$labelStatus = "SCHEDULED ORDER";
				}
				break;
			case $COMMANDE_LIVREE_SANS_RESERVES:
				if($lang == "FR"){
					$labelStatus = "COMMANDE LIVREE SANS RESERVES";
				}
				if($lang == "EN"){
					$labelStatus = "ORDER DELIVERED WITHOUT RESERVATIONS";
				}
				break;
			case $COMMANDE_LIVREE_AVEC_RESERVES:
				if($lang == "FR"){
					$labelStatus = "COMMANDE LIVREE AVEC RESERVES";
				}
				if($lang == "EN"){
					$labelStatus = "ORDER DELIVERED WITH RESERVATIONS";
				}
				break;
			case $ANNULEE:
				if($lang == "FR"){
					$labelStatus = "ANNULEE";
				}
				if($lang == "EN"){
					$labelStatus = "CANCELED";
				}
				break;
			default:
				$labelStatus = "";
				$labelStatusShort = "";
		}
		
		return $labelStatus;
	}
	
	
	/**
     * Get a list of orders
     *
     * Return an array with orders informations
     *
     * @param 	int 	$socId 			ID of entreprise
	 * @param 	int 	$status_mode 	0 = Long label, 1 = Short label
	 * @param 	string	$sortfield	    Sort field
     * @param 	string	$sortorder	    Sort order
	 * @param 	int		$limit		    Limit for list
     * @param 	int		$page		    Page number
     * @return 	array|mixed 			data without useless information
     *
     * @url	GET orders/of-user/
     * @throws 	RestException
     */
    public function getOrdersOfUser($socId, $status_mode = 1, $sortfield = "cmd.rowid", $sortorder = 'ASC', $limit = 25, $page = 0)
    {
		$result;
		/*
		$sql_v1 = "SELECT cmd.rowid, cmd.ref, cmd.date_creation, cmd.date_livraison, soc.zip, soc.town, exp.billed, cmd.fk_statut ";
		$sql_v1 .= "FROM llx_commande as cmd, llx_societe as soc, llx_expedition as exp, llx_element_element as el ";
		$sql_v1 .= "WHERE cmd.fk_soc = soc.rowid AND el.fk_source = cmd.rowid AND el.fk_target = exp.rowid AND el.targettype = 'shipping' ";
		//$sql_v1 .= "AND cmd.fk_soc = $socId ";
		*/
		
		//v2
		$sql = "SELECT cmd.rowid, cmd.ref, cmd.date_creation, cmd.date_livraison, soc.zip, soc.town, cmd.total_ttc, cmd.fk_statut ";
		$sql .= "FROM llx_commande as cmd, llx_societe as soc ";
		$sql .= "WHERE cmd.fk_soc = soc.rowid ";
		$sql .= "AND cmd.fk_soc = $socId ";
		
		
		$sql.= $this->db->order($sortfield, $sortorder);
		$sql_ = $sql;
		if ($limit)	{
            if ($page < 0) {
                $page = 0;
            }
            $offset = $limit * $page;
			//print " || limit: ".($limit)." & offset: ".$offset."|| ";

            $sql.= $this->db->plimit($limit, $offset);
        }
		
		//print("<pre>".print_r($sql, true)."</pre>");
		//die();
		
		$res = $this->db->query($sql); 
		
		$index = 0;
		$total_cmd = $res->num_rows;
		if ($total_cmd > 0) {
			
			while($row = $this->db->fetch_array($sql)){
				//print("<pre>".print_r($row, true)."</pre>"); 
				
				
				// sql_v2
				$result[$index]['rowid'] = $row['rowid'];
				$result[$index]['ref'] = $row['ref'];
				$result[$index]['date_creation'] = $row['date_creation'];
				$result[$index]['date_livraison'] = $row['date_livraison'];
				$result[$index]['userCreated'] = $row['userCreated'];
				$result[$index]['userCreated'] = $row['userCreated'];
				//$result[$index]['zip'] = $row['zip'];
				//$result[$index]['town'] = $row['town'];
				$result[$index]['total_ttc'] = round($row['total_ttc'], 3);
				$result[$index]['statut'] = $this->getStatusLabel($row['fk_statut'], -99, $status_mode);
				
				$index++;
			}
		}
		else{
			return array(
			"error" => array(
				"message" => "Aucune commande trouve.",
			)
		);
		}
		
		//print("<pre>".print_r($result, true)."</pre>");
		
		return array(
			"success" => array(
				"total_cmd" => $total_cmd,
				"limit" => $limit,
				"current_page" => $page,
				"total_pages" => $this->getTotalPages($sql_, $limit),
				"cmds" => $result
			)
		);
    }
	
	/**
     * Get a list of orders
     *
     * Return an array with orders informations
     *
     * @param 	int 	$socId 			ID of entreprise
	 * @param 	int 	$status_mode 	0 = Long label, 1 = Short label
	 * @param 	string	$sortfield	    Sort field
     * @param 	string	$sortorder	    Sort order
	 * @param 	int		$limit		    Limit for list
     * @param 	int		$page		    Page number
	 * @param 	string	$filter		 	Exp:{"ref":"","ref_client":"","town":"","zip":"","creation_date":"","delivery_date":"","total_ht":"","total_tva":"","total_ttc":"","statut":"","billed":"","limit":"25"}
     * @return 	array|mixed 			data without useless information
     *
     * @url	GET orders/of-user/v3
     * @throws 	RestException
     */
    public function getOrdersOfUser_v3($socId, $status_mode = 1, $sortfield = "c.rowid", $sortorder = 'ASC', $limit = 25, $page = 0, $filter = '{"ref":"","ref_client":"","town":"","zip":"","creation_date":"","delivery_date":"","total_ht":"","total_tva":"","total_ttc":"","statut":"","billed":"","limit":"25"}')
    {	// "{'test': 1, 'test2': 'Hey', 'test3': 15.66}"
		// {"ref":"","ref_client":"JL","town":"","zip":"","creation_date":"","delivery_date":"2020-12-28","total_ht":"","total_tva":"","total_ttc":"","statut":"1","billed":"0","limit":"25"}
		// {"ref":"","ref_client":"","town":"","zip":"","creation_date":"","delivery_date":"","total_ht":"","total_tva":"","total_ttc":"","statut":"","billed":"","limit":"25"}
		// {"ref":"","ref_client":"","client1":"","userCreated":"","assigned":"","creation_date":"","delivery_date":"","total_ht":"","total_tva":"","total_ttc":"","statut":"","billed":"","limit":"25"}
		
		$filter = json_decode($filter, true);
	
		$isFirst = false;
		$str_filter = "";
		
		if($filter['ref'] == ""){
			$str_filter .= "";
		}else {
			$isFirst = true;
			$str_filter .= "c.ref LIKE '%".$filter['ref']."%' AND ";
		}
		
		if($filter['ref_client'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND c.ref_client LIKE '%".$filter['ref_client']."%' ";
			}else{
				$isFirst = true;
				$str_filter .= "c.ref_client LIKE '%".$filter['ref_client']."%' AND ";
			}
		}
		
		
		if($filter['client1'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND (SELECT s.nom FROM llx_societe as s WHERE s.rowid = c.fk_soc) LIKE '%".$filter['client1']."%' ";
			}else{
				$isFirst = true;
				$str_filter .= "(SELECT s.nom FROM llx_societe as s WHERE s.rowid = c.fk_soc) LIKE '%".$filter['client1']."%' AND ";
			}
		}
		
		if($filter['userCreated'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND (SELECT u.lastname FROM llx_user as u WHERE u.rowid = c.fk_user_author) LIKE '%".$filter['userCreated']."%' ";
			}else{
				$isFirst = true;
				$str_filter .= "(SELECT u.lastname FROM llx_user as u WHERE u.rowid = c.fk_user_author) LIKE '%".$filter['userCreated']."%' AND ";
			}
		}
		
		if($filter['assigned'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND (SELECT u.lastname FROM llx_user as u, llx_element_contact as ele_c__ WHERE u.rowid = ele_c__.fk_socpeople AND ele_c__.element_id = c.rowid ORDER BY ele_c__.rowid DESC LIMIT 1) LIKE '%".$filter['assigned']."%' ";
			}else{
				$isFirst = true;
				$str_filter .= "(SELECT u.lastname FROM llx_user as u, llx_element_contact as ele_c__ WHERE u.rowid = ele_c__.fk_socpeople AND ele_c__.element_id = c.rowid ORDER BY ele_c__.rowid DESC LIMIT 1) LIKE '%".$filter['assigned']."%' AND ";
			}
		}
		
		if($filter['town'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND s.town = '".$filter['town']."' ";
			}else{
				$isFirst = true;
				$str_filter .= "s.town = '".$filter['town']."' AND ";
			}
		}
		
		if($filter['creation_date'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND c.date_creation LIKE '".$filter['creation_date']." %' ";
			}else{
				$isFirst = true;
				$str_filter .= "c.date_creation LIKE '".$filter['creation_date']." %' AND ";
			}
		}
		
		if($filter['delivery_date'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND c.date_livraison  LIKE '".$filter['delivery_date']." %' ";
			}else{
				$isFirst = true;
				$str_filter .= "c.date_livraison LIKE '".$filter['delivery_date']." %' AND ";
			}
		}
		
		if($filter['total_ht'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND c.total_ht = ".$filter['total_ht']." ";
			}else{
				$isFirst = true;
				$str_filter .= "c.total_ht = ".$filter['total_ht']." AND ";
			}
		}
		
		if($filter['total_tva'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND c.tva = ".$filter['total_tva']." ";
			}else{
				$isFirst = true;
				$str_filter .= "c.tva = ".$filter['total_tva']." AND ";
			}
		}
		
		if($filter['total_ttc'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND c.total_ttc = ".$filter['total_ttc']." ";
			}else{
				$isFirst = true;
				$str_filter .= "c.total_ttc = ".$filter['total_ttc']." AND ";
			}
		}
		
		/* Version 1
		if($filter['statut'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND c.fk_statut = ".$filter['statut']." ";
			}else{
				$isFirst = true;
				$str_filter .= "c.fk_statut = ".$filter['statut']." AND ";
			}
		}
		*/
		
		// Version 2
		if($filter['statut'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND (SELECT status FROM llx_commande_extrafields WHERE fk_object = c.rowid) = ".$filter['statut']." ";
			}else{
				$isFirst = true;
				$str_filter .= "(SELECT status FROM llx_commande_extrafields WHERE fk_object = c.rowid) = ".$filter['statut']." AND ";
			}
		}
		
		if($filter['billed'] == ""){
			$str_filter .= "";
		}else {
			if($isfirst){
				$str_filter .= "AND c.facture = ".$filter['billed'];
			}else{
				$isFirst = true;
				$str_filter .= "c.facture = ".$filter['billed']." AND";
			}
		}
		
		$limit = $filter['limit'];
		
		//print("<pre>".print_r($str_filter, true)."</pre>");
		//die();
		
		$result;
		
		$sql  = "SELECT s.rowid as socid, s.nom as name, s.email, s.town, s.zip, s.fk_pays, s.client, s.code_client, typent.code as typent_code, state.code_departement as state_code, state.nom as state_name, c.rowid, c.ref, ";
		$sql .= "c.total_ht, c.tva as total_tva, c.total_ttc, c.ref_client, c.date_valid, c.date_commande, c.note_private, c.date_livraison as date_delivery, c.fk_statut, c.facture as billed, c.date_creation as date_creation, ";
		$sql .= "c.tms as date_update, c.date_cloture as date_cloture, p.rowid as project_id, p.ref as project_ref, p.title as project_label, ";
		$sql .= "(SELECT s.nom FROM llx_societe as s WHERE s.rowid = c.fk_soc) as client1, (SELECT u.lastname FROM llx_user as u WHERE u.rowid = c.fk_user_author) as fk_user_author, (SELECT u.lastname FROM llx_user as u, llx_element_contact as ele_c__ WHERE u.rowid = ele_c__.fk_socpeople AND ele_c__.element_id = c.rowid ORDER BY ele_c__.rowid DESC LIMIT 1) as assign, ";
		$sql .= "(SELECT status FROM llx_commande_extrafields WHERE fk_object = c.rowid) as status ";
		$sql .= "FROM llx_societe as s LEFT JOIN llx_c_country as country on (country.rowid = s.fk_pays) LEFT JOIN llx_c_typent as typent on (typent.id = s.fk_typent) LEFT JOIN llx_c_departements as state on (state.rowid = s.fk_departement), ";
		$sql .= "llx_commande as c LEFT JOIN llx_projet as p ON p.rowid = c.fk_projet ";
		$sql .= "WHERE c.fk_soc = s.rowid AND c.fk_soc = $socId AND $str_filter c.entity IN (1) "; 
		
		$sql.= $this->db->order($sortfield, $sortorder);
		$sql_ = $sql;
		if ($limit)	{
            if ($page < 0) { 
                $page = 0;
            }
            $offset = $limit * $page;
			//print " || limit: ".($limit)." & offset: ".$offset."|| ";

            $sql.= $this->db->plimit($limit, $offset);
        }
		
		//print("<pre>".print_r($sql, true)."</pre>");
		//die();
		
		$res = $this->db->query($sql); 
		
		$index = 0;
		$total_cmd = $res->num_rows;
		if ($total_cmd > 0) {
			
			while($row = $this->db->fetch_array($sql)){
				//print("<pre>".print_r($row, true)."</pre>"); 
				//die();
				
				// sql_v3
				$result[$index]['rowid'] = $row['rowid'];
				$result[$index]['ref'] = $row['ref'];
				$result[$index]['client_name'] = $row['name'];
				$result[$index]['client1'] = $row['client1'];
				$result[$index]['userCreated'] = $row['fk_user_author'];
				$result[$index]['assign'] = $row['assign'];
				$result[$index]['ref_client'] = $row['ref_client'];
				$result[$index]['town'] = $row['town'];
				$result[$index]['zip'] = $row['zip'];
				$result[$index]['date_creation'] = ($row['date_creation'] == "" ? : strftime("%d-%m-%Y %Hh%M", strtotime($row['date_creation'])) );
				$result[$index]['date_livraison'] = ($row['date_delivery'] == "" ? "" : strftime("%d-%m-%Y %Hh%M", strtotime($row['date_delivery'])) );
				$result[$index]['total_ht'] = round($row['total_ht'], 3);
				$result[$index]['total_tva'] = round($row['total_tva'], 3);
				$result[$index]['total_ttc'] = round($row['total_ttc'], 3);
				//$result[$index]['statut'] = $this->getStatusLabel($row['fk_statut'], $row['billed'], $status_mode);
				$result[$index]['statut_'] = $this->getStatusLabel_v2($row['status'], "FR");
				$result[$index]['billed'] = ($row['billed'] == 0 ? "Non" : $row['billed'] == 1 ? "Oui" : "NaN");
				
				$index++;
			}
		}
		else{
			return array(
			"error" => array(
				"message" => "Aucune commande trouve.", 
				)
			);
		}
		
		return array(
			"success" => array(
				"total_cmd" => $total_cmd,
				"limit" => $limit,
				"current_page" => $page,
				"total_pages" => $this->getTotalPages($sql_, $limit),
				"cmds" => $result
			)
		);
    }
	
	private function getOrderAssignByOrderId($id){
		$sql_3 = "SELECT u.lastname FROM llx_user as u, llx_element_contact as ele_c__ WHERE u.rowid = ele_c__.fk_socpeople AND ele_c__.element_id = $id";
		
		$result = "";
		$res = $this->db->query($sql_3);
		$rows = $res->num_rows;
		if($rows > 0) {
			while($row = $this->db->fetch_array($sql_3)){
				$result = ($row['lastname'] == null || $row['lastname'] == "" ? "" : $row['lastname']);
			}
		}
		
		return $result;
	}
	
	private function getTotalPages($sql, $limit){
		
		$res = $this->db->query($sql);
		$total_cmd = $res->num_rows;
		if ($total_cmd > 0) {
			
			return (($total_cmd / $limit) < 0 ? 1 : round($total_cmd / $limit));
		}
		return 0;
	}
	
	private function getOrderDeliveryAddressZip($id){
		$sql = "SELECT spp.zip ";
		$sql .= "FROM llx_element_contact as c, llx_c_type_contact as tc, llx_socpeople as spp, llx_c_departements as depart, llx_c_regions as regions, llx_c_country as country ";
		$sql .= "WHERE c.element_id = $id AND c.fk_c_type_contact = tc.rowid AND c.fk_socpeople = spp.rowid AND tc.element = 'commande' AND tc.code = 'SHIPPING' ";
		$sql .= "AND spp.fk_departement = depart.rowid AND depart.fk_region = regions.rowid AND spp.fk_pays = country.rowid";
		
		$result = "";
		$res = $this->db->query($sql);
		$rows = $res->num_rows;
		if($rows > 0) {
			while($row = $this->db->fetch_array($sql)){
				$result = $row['zip'];
				break;
			}
		}
		return $result;
	}
	
	private function getOrderDeliveryAddressTown($id){
		$sql = "SELECT depart.nom ";
		$sql .= "FROM llx_element_contact as c, llx_c_type_contact as tc, llx_socpeople as spp, llx_c_departements as depart, llx_c_regions as regions, llx_c_country as country ";
		$sql .= "WHERE c.element_id = $id AND c.fk_c_type_contact = tc.rowid AND c.fk_socpeople = spp.rowid AND tc.element = 'commande' AND tc.code = 'SHIPPING' ";
		$sql .= "AND spp.fk_departement = depart.rowid AND depart.fk_region = regions.rowid AND spp.fk_pays = country.rowid";
		
		$result = "";
		$res = $this->db->query($sql);
		$rows = $res->num_rows;
		if($rows > 0) {
			while($row = $this->db->fetch_array($sql)){
				$result = $row['nom'];
				break;
			}
		}
		return $result;
	}

	
	private function getOrderDeliveryAddress($id){
		
		$sql = "SELECT CONCAT(spp.civility, ' ', spp.lastname, ' ', spp.firstname, ', ', spp.address, ', ', spp.zip, ', ', country.label) as adress ";
		$sql .= "FROM llx_element_contact as c, llx_c_type_contact as tc, llx_socpeople as spp, llx_c_country as country ";
		$sql .= "WHERE c.element_id = $id AND c.fk_c_type_contact = tc.rowid AND c.fk_socpeople = spp.rowid AND tc.element = 'commande' AND tc.code = 'SHIPPING' AND spp.fk_pays = country.rowid";
		
		$result = "";
		$res = $this->db->query($sql);
		$rows = $res->num_rows;
		if($rows > 0) {
			while($row = $this->db->fetch_array($sql)){
				$result = $row['adress'];
				break;
			}
		}
		return $result;
	}
	
	private function getSocieteAddress($id){
		
		$sql = "SELECT CONCAT(s.nom, ',', s.address, ', ', s.town, ', ', s.zip, ', ', country.label) as adress ";
		$sql .= "FROM llx_societe as s, llx_c_country as country ";
		$sql .= "WHERE s.rowid = $id AND s.fk_pays = country.rowid";
		
		//print("<pre>".print_r($sql, true)."</pre>");
		//die();
		
		$result = "";
		$res = $this->db->query($sql);
		$rows = $res->num_rows;
		if($rows > 0) {
			while($row = $this->db->fetch_array($sql)){
				$result = $row['adress'];
				break;
			}
		}
		return $result;
	}
	
	private function getOrderInvoiceAddress($id){ 
		
		$sql = "SELECT CONCAT(spp.civility, ' ', spp.lastname, ' ', spp.firstname, ', ', spp.address, ', ', spp.zip, ', ', country.label) as adress ";
		$sql .= "FROM llx_element_contact as c, llx_c_type_contact as tc, llx_socpeople as spp, llx_c_country as country ";
		$sql .= "WHERE c.element_id = $id AND c.fk_c_type_contact = tc.rowid AND c.fk_socpeople = spp.rowid AND tc.element = 'commande' AND tc.code = 'BILLING' AND spp.fk_pays = country.rowid";
		
		
		$result = "";
		$res = $this->db->query($sql);
		$rows = $res->num_rows;
		if($rows > 0) {
			while($row = $this->db->fetch_array($sql)){
				$result = $row['adress'];
				break;
			}
		}
		return $result;
	}
	
	private function getOrderProductWeight($id){
		$sql = "SELECT short_label FROM llx_c_units WHERE unit_type = 'weight' AND scale = $id";
		
		$result = "";
		$res = $this->db->query($sql);
		$rows = $res->num_rows;
		//print("<pre>".print_r($rows, true)."</pre>");
		
		if($rows > 0) {
			while($row = $this->db->fetch_array($sql)){
				$result = $row['short_label'];
				//print("<pre>".print_r($row, true)."</pre>");
			}
		}
		//print("<pre>".print_r($result, true)."</pre>");
		return $result;
	}
	
	private function getOrderProductVolume($id){
		$sql = "SELECT short_label FROM llx_c_units WHERE unit_type = 'volume' AND scale = $id";
		
		$result = "";
		$res = $this->db->query($sql);
		$rows = $res->num_rows;
		//print("<pre>".print_r($rows, true)."</pre>");
		if($rows > 0) {
			while($row = $this->db->fetch_array($sql)){
				//print("<pre>".print_r($row, true)."</pre>");
				$result = $row['short_label'];
			}
		}
		//print("<pre>".print_r($result, true)."</pre>");
		return $result;
	}
	
	private function getOrderProductWarehouse($fk_default_warehouse){
		$result = "";
		
		//print("<pre>".print_r($fk_default_warehouse, true)."</pre>");
		if($fk_default_warehouse != null) {
			
			$sql = "SELECT ent.ref FROM llx_entrepot as ent WHERE ent.rowid = $fk_default_warehouse";
			
			$res = $this->db->query($sql);
			$rows = $res->num_rows;
			
			//print("<pre>".print_r($rows, true)."</pre>");
			if($rows > 0) {
				while($row = $this->db->fetch_array($sql)){
					$result = $row['ref'];
					//print("<pre>".print_r($result, true)."</pre>");
				}
			}
		}
		//print("<pre>".print_r($result, true)."</pre>");
		return $result;
	}


	/**
     * Get order
     *
     * Return an order object information
     *
     * @param 	int 	$id 			ID of order
     * @return 	array|mixed 			data without useless information
     *
     * @url	GET order/id/
     * @throws 	RestException
     */
	public function getOrderById($id){
		
		//error_reporting(E_ALL);
		//ini_set('display_errors', '1');
		 
		 $sql = "SELECT c.rowid, c.entity, c.date_creation, c.ref, c.fk_soc as fk_soc_id, (SELECT s.nom FROM llx_societe as s WHERE s.rowid = c.fk_soc) as fk_soc, (SELECT u.lastname FROM llx_user as u, llx_element_contact as ele_c__ WHERE u.rowid = ele_c__.fk_socpeople AND ele_c__.element_id = $id ORDER BY ele_c__.rowid DESC LIMIT 1) as fk_socpeople, (SELECT u.lastname FROM llx_user as u WHERE u.rowid = c.fk_user_author) as fk_user_author, (SELECT u.lastname FROM llx_user as u WHERE u.rowid = c.fk_user_valid) as fk_user_valid, ";
		 $sql .= "(SELECT status FROM llx_commande_extrafields WHERE fk_object = $id) as status, c.amount_ht, c.total_ht, c.total_ttc, c.tva as total_tva, c.localtax1 as total_localtax1, c.localtax2 as total_localtax2, c.fk_cond_reglement, c.fk_mode_reglement, c.fk_availability, c.fk_input_reason, c.fk_account, c.date_commande, c.date_valid, c.tms, c.date_livraison, c.fk_shipping_method, c.fk_warehouse, c.fk_projet as fk_project, c.remise_percent, c.remise, c.remise_absolue, c.source, c.facture as billed, c.note_private, c.note_public, c.ref_client, c.ref_ext, c.ref_int, c.model_pdf, c.last_main_doc, c.fk_delivery_address, c.extraparams, c.fk_incoterms, c.location_incoterms, c.fk_multicurrency, c.multicurrency_code, c.multicurrency_tx, c.multicurrency_total_ht, c.multicurrency_total_tva, c.multicurrency_total_ttc, c.module_source, c.pos_source, i.libelle as label_incoterms, p.code as mode_reglement_code, p.libelle as mode_reglement_libelle, cr.code as cond_reglement_code, cr.libelle as cond_reglement_libelle, cr.libelle_facture as cond_reglement_libelle_doc, ca.code as availability_code, ca.label as availability_label, dr.code as demand_reason_code ";
		 $sql .= "FROM llx_commande as c LEFT JOIN llx_c_payment_term as cr ON c.fk_cond_reglement = cr.rowid LEFT JOIN llx_c_paiement as p ON c.fk_mode_reglement = p.id LEFT JOIN llx_c_availability as ca ON c.fk_availability = ca.rowid LEFT JOIN llx_c_input_reason as dr ON c.fk_input_reason = dr.rowid LEFT JOIN llx_c_incoterms as i ON c.fk_incoterms = i.rowid ";
		 $sql .= "WHERE c.rowid=$id";
		 
		 //print("<pre>".print_r($sql, true)."</pre>");
		 //die();
		
		$res = $this->db->query($sql); 
		
		$index = 0;
		$total_cmd = $res->num_rows;
		$cmd = array();
		
		
		if ($total_cmd > 0) {
			
			while($row = $this->db->fetch_array($sql)){
				$cmd = array(
					"rowid" => $row['rowid'],
					"ref" => $row['ref'],
					"client1" => $row['fk_soc'],
					"client2" => $row['ref_client'],
					"assign" => ($row['fk_socpeople'] == null || $row['fk_socpeople'] == "" ? "" : $row['fk_socpeople']),
					"userCreated" => $row['fk_user_author'],
					"userValidated" => $row['fk_user_valid'],
					"createDate" => strftime("%d-%m-%Y %Hh%M", strtotime($row['date_creation'])),
					"modifyDate" => strftime("%d-%m-%Y %Hh%M", strtotime($row['tms'])),
					"validDate" => strftime("%d-%m-%Y %Hh%M", strtotime($row['date_valid'])),
					"deliveryDate" => strftime("%d-%m-%Y %Hh%M", strtotime($row['date_livraison'])),
					"deliveryAddress" => $this->getOrderDeliveryAddress($id),
					"invoiceAddress" => $this->getSocieteAddress($row['fk_soc_id']),
					"benefitAmout" => "15.0",
					"htAmout" => round($row['total_ht'], 3),
					"tvaAmount" => round($row['total_tva'], 3),
					"ttcAmout" => round($row['total_ttc'], 3),
					"comment" => $row['note_public'],
					"anomaly" => $row['note_private'],
					//"status" => $this->getStatusLabel($row['fk_statut'], $row['billed'], 1),
					"status" => $this->getStatusLabel_v2($row['status'], "FR"),
					"documents" => null,
					"lines" => array(),
				);
			}
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// get files data /////////////////////////////////////////////////////////////////////////////////////////
			$cmd['documents'] = $this->downloadOrderDocs($id);
			
			
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// get extrafields data /////////////////////////////////////////////////////////////////////////////////////////
			$sql_extra = "SELECT * FROM llx_commande_extrafields WHERE fk_object = $id";
			$res = $this->db->query($sql_extra);
			$total_extra = $res->num_rows;
			$extrafields_data = null; 
			
			if($total_extra > 0){
				$row = $this->db->fetch_array($sql_extra);
				
				//print("<pre>".print_r($sql_extra, true)."</pre>");
				//die();
				
				$extrafields_data = array(
					"receptionDate_custom" => strftime("%d-%m-%Y %Hh%M", strtotime($row['dateentry'])),				// date de reception
					"deliveryAddress_custom" => $row['adresseship']		// adress de reception de la commande
				);
			}
			
			
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// get CMD lines ////////////////////////////////////////////////////////////////////////////////////////////////
			$sql = "SELECT l.rowid, l.fk_product, l.fk_parent_line, l.product_type, l.fk_commande, l.label as custom_label, l.description, l.price, l.qty, l.vat_src_code, l.tva_tx, l.localtax1_tx, l.localtax2_tx, l.localtax1_type, l.localtax2_type, l.fk_remise_except, l.remise_percent, l.subprice, l.fk_product_fournisseur_price as fk_fournprice, l.buy_price_ht as pa_ht, l.rang, l.info_bits, l.special_code, l.total_ht, l.total_ttc, l.total_tva, l.total_localtax1, l.total_localtax2, l.date_start, l.date_end, l.fk_unit, l.fk_multicurrency, l.multicurrency_code, l.multicurrency_subprice, l.multicurrency_total_ht, l.multicurrency_total_tva, l.multicurrency_total_ttc, p.ref as product_ref, p.barcode, p.description as product_desc, p.fk_product_type, p.label as product_label, p.tobatch as product_tobatch, p.weight, p.weight_units, p.volume, p.volume_units, p.fk_default_warehouse ";
			$sql .= "FROM llx_commandedet as l LEFT JOIN llx_product as p ON (p.rowid = l.fk_product) ";
			$sql .= "WHERE l.fk_commande = $id ORDER BY l.rang, l.rowid";
			
			//print("<pre>".print_r($sql, true)."</pre>");
			//die();
			
			
			$res_lines = $this->db->query($sql);
			$total_cmd_lines = $res_lines->num_rows;
			$lines = array();
			
			//print("<pre>".print_r($res_lines)."</pre>"); 
			//print("<pre>".print_r($res_lines->num_rows, true)."</pre>");
			
			if ($total_cmd_lines > 0) {
				while($row = $this->db->fetch_array($sql)){
					
					//print("<pre>".print_r($row, true)."</pre>"); 
					
					$line;
					$line['rowid']            = $row['rowid'];
					$line['id']               = $row['rowid'];
					$line['fk_commande']      = $row['fk_commande'];
					$line['commande_id']      = $row['fk_commande'];
					$line['barcode']      	  = $row['barcode'];
					$line['label']            = $row['custom_label'];
					$line['desc']             = $row['description'];
					$line['description']      = $row['description']; // Description line
					$line['product_type']     = $row['product_type'];
					$line['qty']              = $row['qty'];
					$line['vat_src_code']     = $row['vat_src_code'];
					$line['tva_tx']           = $row['tva_tx'];
					$line['localtax1_tx']     = $row['localtax1_tx'];
					$line['localtax2_tx']     = $row['localtax2_tx'];
					$line['localtax1_type']	  = $row['localtax1_type'];
					$line['localtax2_type']	  = $row['localtax2_type'];
					$line['total_ht']         = round($row['total_ht'], 3);
					$line['total_ttc']        = round($row['total_ttc'], 3);
					$line['total_tva']        = round($row['total_tva'], 3);
					$line['total_localtax1']  = $row['total_localtax1'];
					$line['total_localtax2']  = $row['total_localtax2'];
					$line['subprice']         = $row['subprice'];
					$line['fk_remise_except'] = $row['fk_remise_except'];
					$line['remise_percent']   = $row['remise_percent'];
					$line['price']            = round($row['total_ttc'], 3);
					$line['fk_product']       = $row['fk_product'];
					$line['fk_fournprice'] 	  = $row['fk_fournprice'];
					//$marginInfos 			= getMarginInfos($row['subprice'], $row['remise_percent'], $row['tva_tx'], $row['localtax1_tx'], $row['localtax2_tx'], $line['fk_fournprice'], $row['pa_ht']);
					//$line['pa_ht 				= $marginInfos[0];
					$line['pa_ht'] 			  = null; // define by JL
					/*
					$line['marge_tx']		  = $marginInfos[1];
					$line['marque_tx']		  = $marginInfos[2];
					*/
					$line['rang']             = $row['rang'];
					$line['info_bits']        = $row['info_bits'];
					$line['special_code'] 	  = $row['special_code'];
					$line['fk_parent_line']   = $row['fk_parent_line'];
					$line['ref'] 			  = $row['product_ref'];
					$line['product_ref'] 	  = $row['product_ref'];
					$line['libelle'] 		  = $row['product_label'];
					$line['product_label'] 	  = $row['product_label'];
					$line['product_desc']     = $row['product_desc'];
					$line['product_tobatch']  = $row['product_tobatch'];
					$line['fk_product_type']  = $row['fk_product_type']; // Produit ou service
					$line['fk_unit']          = $row['fk_unit'];
					$line['weight']           = $row['weight'];
					$line['weight_units']     = $row['weight_units']; 
					//$line['weight_units_']     = $this->getOrderProductWeight($row['weight_units']); 
					$line['volume']           = $row['volume'];
					$line['volume_units']     = $row['volume_units'];
					//$line['volume_units_']     = $this->getOrderProductVolume($row['volume_units']);
					$line['date_start']       = $this->db->jdate($row['date_start']);
					$line['date_end']         = $this->db->jdate($row['date_end']);

					// Multicurrency
					$line['fk_multicurrency']   		= $row['fk_multicurrency'];
					$line['multicurrency_code'] 		= $row['multicurrency_code'];
					$line['multicurrency_subprice'] 	= $row['multicurrency_subprice'];
					$line['multicurrency_total_ht'] 	= $row['multicurrency_total_ht'];
					$line['multicurrency_total_tva'] 	= $row['multicurrency_total_tva'];
					$line['multicurrency_total_ttc'] 	= $row['multicurrency_total_ttc'];
					
					$line['fk_default_warehouse'] 	= $row['fk_default_warehouse'];
					$line['default_warehouse'] = $this->getOrderProductWarehouse($row['fk_default_warehouse']);
					
					//print("<pre>".print_r($line, true)."</pre>");
					
					array_push($lines, $line);
				}
				
			}
			
			//print("<pre>".print_r($lines, true)."</pre>");
			$cmd['extrafields_data'] = $extrafields_data; 
			$cmd['lines'] = $lines;
			
			
		}
		else{
			return array(
				"status" => "error",
				"message" => "Aucune commande trouve.",
				"order" => null
			);
		}
		
		//$json = json_encode($cmd);
		//print("<pre>".print_r($cmd, true)."</pre>");
		
		 return array(
			"status" => "success",
			"message" => "Commande ".$cmd['ref']." trouve.",
			"order" => $cmd
		);
	 }
	 


	 
	 /**
	 * Get all documents of order id.
	 *
	 * Note that, this API is similar to using the wrapper link "documents.php" to download a file (used for
	 * internal HTML links of documents into application), but with no need to have a session cookie (the token is used instead).
	 *
	 * @param 	int		$id				Order id
	 * @return  array                   List of documents
	 *
	 * @throws 400
	 * @throws 401
	 * @throws 404
	 * @throws 200
	 *
	 * @url GET /download/order/documents
	 */
	public function downloadOrderDocs($id)
	{
		global $conf, $langs;
		
		$sql = "SELECT rowid, src_object_type, filepath, filename, date_c, date_m FROM llx_ecm_files WHERE src_object_id = $id";
		$res = $this->db->query($sql); 
		$result;
		$index = 0;
		
		if ($res->num_rows > 0) {
			
			while($row = $this->db->fetch_array($sql)){
				$original_file_tmp = $row['filepath'] ."/". $row['filename'];
				$original_file = explode("/", $original_file_tmp)[1] ."/". explode("/", $original_file_tmp)[2];
					
				$result[$index]['rowid'] = $row['rowid'];
				$result[$index]['type'] = $row['src_object_type'];
				$result[$index]['filepath'] = $row['filepath'];
				$result[$index]['filename'] = $row['filename'];
				$result[$index]['original_file'] = $original_file;
				$result[$index]['date_c'] = $row['date_c'];
				$result[$index]['date_m'] = $row['date_m'];
				
				
				if (empty($row['src_object_type'])) {
						throw new RestException(400, 'bad value for parameter modulepart');
				}
				if (empty($original_file)) {
					throw new RestException(400, 'bad value for parameter original_file');
				}

				//--- Finds and returns the document
				$entity = $conf->entity;

				$check_access = dol_check_secure_access_document($row['src_object_type'], $original_file, $entity, DolibarrApiAccess::$user, '', 'read');
				$accessallowed = $check_access['accessallowed'];
				$sqlprotectagainstexternals = $check_access['sqlprotectagainstexternals'];
				$original_file = $check_access['original_file'];

				if (preg_match('/\.\./', $original_file) || preg_match('/[<>|]/', $original_file)) {
					throw new RestException(401);
				}
				if (!$accessallowed) {
					throw new RestException(401);
				}

				$filename = basename($original_file);
				$original_file_osencoded = dol_osencode($original_file); // New file name encoded in OS encoding charset

				if (!file_exists($original_file_osencoded))
				{
					//print("<pre>".print_r($original_file, true)."</pre>");
					//die();
					dol_syslog("Try to download not found file ".$original_file_osencoded, LOG_WARNING);
					throw new RestException(404, 'File not found');
				}

				$file_content = file_get_contents($original_file_osencoded);
				$result[$index]['data'] = array(
					'filename'=>$filename, 
					'contentType' => dol_mimetype($filename), 
					'filectime'=>strftime("%d/%m/%Y %Hh%M", filectime($original_file)), 
					'filemtime'=>strftime("%d/%m/%Y %Hh%M", filemtime($original_file)), 
					'filesize'=>$this->formatBytes(filesize($original_file)), 
					'content'=>base64_encode($file_content), 
					'encoding'=>'base64'
				); 
			
	
	
				$index++;
			}
			
			return array(
				"files" => $result
				);
		}else{
			
			return array(
				"files" => []
				);
		}
		
		
	}
	
	
	private function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 

		// Uncomment one of the following alternatives
		$bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow)); 

		return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 
	
	
	/**
	 * Download a document.
	 *
	 * Note that, this API is similar to using the wrapper link "documents.php" to download a file (used for
	 * internal HTML links of documents into application), but with no need to have a session cookie (the token is used instead).
	 *
	 * @param   string  $modulepart     Name of module or area concerned by file download ('facture', ...)
	 * @param   string  $original_file  Relative path with filename, relative to modulepart (for example: IN201701-999/IN201701-999.pdf)
	 * @return  array                   List of documents
	 *
	 * @throws 400
	 * @throws 401
	 * @throws 404
	 * @throws 200
	 *
	 * @url GET /download
	 */
	public function download($modulepart, $original_file = '')
	{
		global $conf, $langs;

		if (empty($modulepart)) {
				throw new RestException(400, 'bad value for parameter modulepart');
		}
		if (empty($original_file)) {
			throw new RestException(400, 'bad value for parameter original_file');
		}

		//--- Finds and returns the document
		$entity = $conf->entity;

		$check_access = dol_check_secure_access_document($modulepart, $original_file, $entity, DolibarrApiAccess::$user, '', 'read');
		$accessallowed = $check_access['accessallowed'];
		$sqlprotectagainstexternals = $check_access['sqlprotectagainstexternals'];
		$original_file = $check_access['original_file'];

		if (preg_match('/\.\./', $original_file) || preg_match('/[<>|]/', $original_file)) {
			throw new RestException(401);
		}
		if (!$accessallowed) {
			throw new RestException(401);
		}

		$filename = basename($original_file);
		$original_file_osencoded = dol_osencode($original_file); // New file name encoded in OS encoding charset

		if (!file_exists($original_file_osencoded))
		{
			dol_syslog("Try to download not found file ".$original_file_osencoded, LOG_WARNING);
			throw new RestException(404, 'File not found');
		}

		$file_content = file_get_contents($original_file_osencoded);
		return array(
			'filename'=>$filename, 
			'contentType' => dol_mimetype($filename), 
			'filectime'=>strftime("%d/%m/%Y %Hh%M", filectime($original_file)), 
			'filemtime'=>strftime("%d/%m/%Y %Hh%M", filemtime($original_file)), 
			'filesize'=>$this->formatBytes(filesize($original_file)), 
			'content'=>base64_encode($file_content), 
			'encoding'=>'base64'
		); 
	}


	/*##########################################################################################################################*/
	/*########################################  Gestion Api Entreprise  ########################################################*/

    /**
     * Get properties of a entreprise object
     *
     * Return an array with entreprise informations
     *
     * @param 	int 	$id ID of entreprise
     * @return 	array|mixed data without useless information
     *
     * @url	GET entreprises/{id}
     * @throws 	RestException
     */
    public function get($id)
    {
        if (! DolibarrApiAccess::$user->rights->edisuivi->entreprise->read) {
            throw new RestException(401);
        }

        $result = $this->entreprise->fetch($id);
        if (! $result) {
            throw new RestException(404, 'Entreprise not found');
        }

        if (! DolibarrApi::_checkAccessToResource('entreprise', $this->entreprise->id, 'edisuivi_entreprise')) {
            throw new RestException(401, 'Access to instance id='.$this->entreprise->id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
        }

        return $this->_cleanObjectDatas($this->entreprise);
    }


    /**
     * List entreprises
     *
     * Get a list of entreprises
     *
     * @param string	       $sortfield	        Sort field
     * @param string	       $sortorder	        Sort order
     * @param int		       $limit		        Limit for list
     * @param int		       $page		        Page number
     * @param string           $sqlfilters          Other criteria to filter answers separated by a comma. Syntax example "(t.ref:like:'SO-%') and (t.date_creation:<:'20160101')"
     * @return  array                               Array of order objects
     *
     * @throws RestException
     *
     * @url	GET /entreprises/
     */
    public function index($sortfield = "t.rowid", $sortorder = 'ASC', $limit = 100, $page = 0, $sqlfilters = '')
    {
        global $db, $conf;

        $obj_ret = array();
        $tmpobject = new Entreprise($db);

        if(! DolibarrApiAccess::$user->rights->edisuivi->entreprise->read) {
            throw new RestException(401);
        }

        $socid = DolibarrApiAccess::$user->socid ? DolibarrApiAccess::$user->socid : '';

        $restrictonsocid = 0;	// Set to 1 if there is a field socid in table of object

        // If the internal user must only see his customers, force searching by him
        $search_sale = 0;
        if ($restrictonsocid && ! DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) $search_sale = DolibarrApiAccess::$user->id;

        $sql = "SELECT t.rowid";
        if ($restrictonsocid && (!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql .= ", sc.fk_soc, sc.fk_user"; // We need these fields in order to filter by sale (including the case where the user can only see his prospects)
        $sql.= " FROM ".MAIN_DB_PREFIX.$tmpobject->table_element." as t";

        if ($restrictonsocid && (!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc"; // We need this table joined to the select in order to filter by sale
        $sql.= " WHERE 1 = 1";

        // Example of use $mode
        //if ($mode == 1) $sql.= " AND s.client IN (1, 3)";
        //if ($mode == 2) $sql.= " AND s.client IN (2, 3)";

        if ($tmpobject->ismultientitymanaged) $sql.= ' AND t.entity IN ('.getEntity('entreprise').')';
        if ($restrictonsocid && (!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql.= " AND t.fk_soc = sc.fk_soc";
        if ($restrictonsocid && $socid) $sql.= " AND t.fk_soc = ".$socid;
        if ($restrictonsocid && $search_sale > 0) $sql.= " AND t.rowid = sc.fk_soc";		// Join for the needed table to filter by sale
        // Insert sale filter
        if ($restrictonsocid && $search_sale > 0) {
            $sql .= " AND sc.fk_user = ".$search_sale;
        }
        if ($sqlfilters)
        {
            if (! DolibarrApi::_checkFilters($sqlfilters)) {
                throw new RestException(503, 'Error when validating parameter sqlfilters '.$sqlfilters);
            }
            $regexstring='\(([^:\'\(\)]+:[^:\'\(\)]+:[^:\(\)]+)\)';
            $sql.=" AND (".preg_replace_callback('/'.$regexstring.'/', 'DolibarrApi::_forge_criteria_callback', $sqlfilters).")";
        }

        $sql.= $db->order($sortfield, $sortorder);
        if ($limit)	{
            if ($page < 0) {
                $page = 0;
            }
            $offset = $limit * $page;

            $sql.= $db->plimit($limit + 1, $offset);
        }

        $result = $db->query($sql);
        if ($result)
        {
            $num = $db->num_rows($result);
            while ($i < $num)
            {
                $obj = $db->fetch_object($result);
                $entreprise_static = new Entreprise($db);
                if($entreprise_static->fetch($obj->rowid)) {
                    $obj_ret[] = $this->_cleanObjectDatas($entreprise_static);
                }
                $i++;
            }
        }
        else {
            throw new RestException(503, 'Error when retrieving entreprise list: '.$db->lasterror());
        }
        if( ! count($obj_ret)) {
            throw new RestException(404, 'No entreprise found');
        }
        return $obj_ret;
    }

    /**
     * Create entreprise object
     *
     * @param array $request_data   Request datas
     * @return int  ID of entreprise
     *
     * @url	POST entreprises/
     */
    public function post($request_data = null)
    {
        if(! DolibarrApiAccess::$user->rights->edisuivi->entreprise->write) {
            throw new RestException(401);
        }
        // Check mandatory fields
        $result = $this->_validate($request_data);

        foreach($request_data as $field => $value) {
            $this->entreprise->$field = $value;
        }
        if( ! $this->entreprise->create(DolibarrApiAccess::$user)) {
            throw new RestException(500, "Error creating Entreprise", array_merge(array($this->entreprise->error), $this->entreprise->errors));
        }
        return $this->entreprise->id;
    }

    /**
     * Update entreprise
     *
     * @param int   $id             Id of entreprise to update
     * @param array $request_data   Datas
     * @return int
     *
     * @url	PUT entreprises/{id}
     */
    public function put($id, $request_data = null)
    {
        if(! DolibarrApiAccess::$user->rights->edisuivi->entreprise->write) {
            throw new RestException(401);
        }

        $result = $this->entreprise->fetch($id);
        if( ! $result ) {
            throw new RestException(404, 'Entreprise not found');
        }

        if( ! DolibarrApi::_checkAccessToResource('entreprise', $this->entreprise->id, 'edisuivi_entreprise')) {
            throw new RestException(401, 'Access to instance id='.$this->entreprise->id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
        }

        foreach($request_data as $field => $value) {
            if ($field == 'id') continue;
            $this->entreprise->$field = $value;
        }

        if ($this->entreprise->update($id, DolibarrApiAccess::$user) > 0)
        {
            return $this->get($id);
        }
        else
        {
            throw new RestException(500, $this->entreprise->error);
        }
    }

    /**
     * Delete entreprise
     *
     * @param   int     $id   Entreprise ID
     * @return  array
     *
     * @url	DELETE entreprises/{id}
     */
    public function delete($id)
    {
        if (! DolibarrApiAccess::$user->rights->edisuivi->entreprise->delete) {
            throw new RestException(401);
        }
        $result = $this->entreprise->fetch($id);
        if (! $result) {
            throw new RestException(404, 'Entreprise not found');
        }

        if (! DolibarrApi::_checkAccessToResource('entreprise', $this->entreprise->id, 'edisuivi_entreprise')) {
            throw new RestException(401, 'Access to instance id='.$this->entreprise->id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
        }

        if (! $this->entreprise->delete(DolibarrApiAccess::$user))
        {
            throw new RestException(500, 'Error when deleting Entreprise : '.$this->entreprise->error);
        }

        return array(
            'success' => array(
                'code' => 200,
                'message' => 'Entreprise deleted'
            )
        );
    }
	
	
	/*##########################################################################################################################*/
	/*########################################  Gestion Api Utilisateur  ##################################################*/

    /**
     * Get properties of a utilisateur object
     *
     * Return an array with utilisateur informations
     *
     * @param 	int 	$id ID of utilisateur
     * @return 	array|mixed data without useless information
     *
     * @url	GET utilisateur/{id}
     * @throws 	RestException
     */
    public function getUtilisateur($id)
    {
        if (! DolibarrApiAccess::$user->rights->edisuivi->utilisateur->read) {
            throw new RestException(401);
        }

        $result = $this->utilisateur->fetch($id);
        if (! $result) {
            throw new RestException(404, 'Utilisateur not found');
        }

        if (! DolibarrApi::_checkAccessToResource('utilisateur', $this->utilisateur->id, 'edisuivi_entreprise')) {
            throw new RestException(401, 'Access to instance id='.$this->utilisateur->id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
        }

        return $this->_cleanObjectDatas($this->utilisateur);
    }


    /**
     * List utilisateur
     *
     * Get a list of utilisateur
     *
     * @param string	       $sortfield	        Sort field
     * @param string	       $sortorder	        Sort order
     * @param int		       $limit		        Limit for list
     * @param int		       $page		        Page number
     * @param string           $sqlfilters          Other criteria to filter answers separated by a comma. Syntax example "(t.ref:like:'SO-%') and (t.date_creation:<:'20160101')"
     * @return  array                               Array of order objects
     *
     * @throws RestException
     *
     * @url	GET /utilisateur/
     */
    public function indexUtilisateur($sortfield = "t.rowid", $sortorder = 'ASC', $limit = 100, $page = 0, $sqlfilters = '')
    {
        global $db, $conf;

        $obj_ret = array();
        $tmpobject = new Utilisateur($db);

        if(! DolibarrApiAccess::$user->rights->edisuivi->utilisateur->read) {
            throw new RestException(401);
        }

        $socid = DolibarrApiAccess::$user->socid ? DolibarrApiAccess::$user->socid : '';

        $restrictonsocid = 0;	// Set to 1 if there is a field socid in table of object

        // If the internal user must only see his customers, force searching by him
        $search_sale = 0;
        if ($restrictonsocid && ! DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) $search_sale = DolibarrApiAccess::$user->id;

        $sql = "SELECT t.rowid";
        if ($restrictonsocid && (!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql .= ", sc.fk_soc, sc.fk_user"; // We need these fields in order to filter by sale (including the case where the user can only see his prospects)
        $sql.= " FROM ".MAIN_DB_PREFIX.$tmpobject->table_element." as t";

        if ($restrictonsocid && (!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc"; // We need this table joined to the select in order to filter by sale
        $sql.= " WHERE 1 = 1";

        // Example of use $mode
        //if ($mode == 1) $sql.= " AND s.client IN (1, 3)";
        //if ($mode == 2) $sql.= " AND s.client IN (2, 3)";

        if ($tmpobject->ismultientitymanaged) $sql.= ' AND t.entity IN ('.getEntity('utilisateur').')';
        if ($restrictonsocid && (!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql.= " AND t.fk_soc = sc.fk_soc";
        if ($restrictonsocid && $socid) $sql.= " AND t.fk_soc = ".$socid;
        if ($restrictonsocid && $search_sale > 0) $sql.= " AND t.rowid = sc.fk_soc";		// Join for the needed table to filter by sale
        // Insert sale filter
        if ($restrictonsocid && $search_sale > 0) {
            $sql .= " AND sc.fk_user = ".$search_sale;
        }
        if ($sqlfilters)
        {
            if (! DolibarrApi::_checkFilters($sqlfilters)) {
                throw new RestException(503, 'Error when validating parameter sqlfilters '.$sqlfilters);
            }
            $regexstring='\(([^:\'\(\)]+:[^:\'\(\)]+:[^:\(\)]+)\)';
            $sql.=" AND (".preg_replace_callback('/'.$regexstring.'/', 'DolibarrApi::_forge_criteria_callback', $sqlfilters).")";
        }

        $sql.= $db->order($sortfield, $sortorder);
        if ($limit)	{
            if ($page < 0) {
                $page = 0;
            }
            $offset = $limit * $page;

            $sql.= $db->plimit($limit + 1, $offset);
        }

        $result = $db->query($sql);
        if ($result)
        {
            $num = $db->num_rows($result);
            while ($i < $num)
            {
                $obj = $db->fetch_object($result);
                $entreprise_static = new Utilisateur($db);
                if($entreprise_static->fetch($obj->rowid)) {
                    $obj_ret[] = $this->_cleanObjectDatas($entreprise_static);
                }
                $i++;
            }
        }
        else {
            throw new RestException(503, 'Error when retrieving utilisateur list: '.$db->lasterror());
        }
        if( ! count($obj_ret)) {
            throw new RestException(404, 'No utilisateur found');
        }
        return $obj_ret;
    }

    /**
     * Create utilisateur object
     *
     * @param array $request_data   Request datas
     * @return int  ID of utilisateur
     *
     * @url	POST utilisateur/
     */
    public function postUtilisateur($request_data = null)
    {
        if(! DolibarrApiAccess::$user->rights->edisuivi->utilisateur->write) {
            throw new RestException(401);
        }
        // Check mandatory fields
        $result = $this->_validate($request_data);

        foreach($request_data as $field => $value) {
            $this->utilisateur->$field = $value;
        }
        if( ! $this->utilisateur->create(DolibarrApiAccess::$user)) {
            throw new RestException(500, "Error creating Utilisateur", array_merge(array($this->utilisateur->error), $this->utilisateur->errors));
        }
        return $this->utilisateur->id;
    }

    /**
     * Update utilisateur
     *
     * @param int   $id             Id of utilisateur to update
     * @param array $request_data   Datas
     * @return int
     *
     * @url	PUT utilisateur/{id}
     */
    public function putUtilisateur($id, $request_data = null)
    {
        if(! DolibarrApiAccess::$user->rights->edisuivi->utilisateur->write) {
            throw new RestException(401);
        }

        $result = $this->utilisateur->fetch($id);
        if( ! $result ) {
            throw new RestException(404, 'Utilisateur not found');
        }

        if( ! DolibarrApi::_checkAccessToResource('utilisateur', $this->utilisateur->id, 'edisuivi_entreprise')) {
            throw new RestException(401, 'Access to instance id='.$this->utilisateur->id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
        }

        foreach($request_data as $field => $value) {
            if ($field == 'id') continue;
            $this->utilisateur->$field = $value;
        }

        if ($this->utilisateur->update($id, DolibarrApiAccess::$user) > 0)
        {
            return $this->get($id);
        }
        else
        {
            throw new RestException(500, $this->utilisateur->error);
        }
    }

    /**
     * Delete utilisateur
     *
     * @param   int     $id   Utilisateur ID
     * @return  array
     *
     * @url	DELETE utilisateur/{id}
     */
    public function deleteUtilisateur($id)
    {
        if (! DolibarrApiAccess::$user->rights->edisuivi->utilisateur->delete) {
            throw new RestException(401);
        }
        $result = $this->utilisateur->fetch($id);
        if (! $result) {
            throw new RestException(404, 'Utilisateur not found');
        }

        if (! DolibarrApi::_checkAccessToResource('utilisateur', $this->utilisateur->id, 'edisuivi_utilisateur')) {
            throw new RestException(401, 'Access to instance id='.$this->utilisateur->id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
        }

        if (! $this->utilisateur->delete(DolibarrApiAccess::$user))
        {
            throw new RestException(500, 'Error when deleting Utilisateur : '.$this->utilisateur->error);
        }

        return array(
            'success' => array(
                'code' => 200,
                'message' => 'Utilisateur deleted'
            )
        );
    }


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
    /**
     * Clean sensible object datas
     *
     * @param   object  $object    Object to clean
     * @return    array    Array of cleaned object properties
     */
    protected function _cleanObjectDatas($object)
    {
        // phpcs:enable
    	$object = parent::_cleanObjectDatas($object);

    	unset($object->rowid);
    	unset($object->canvas);

    	/*unset($object->name);
    	unset($object->lastname);
    	unset($object->firstname);
    	unset($object->civility_id);
    	unset($object->statut);
    	unset($object->state);
    	unset($object->state_id);
    	unset($object->state_code);
    	unset($object->region);
    	unset($object->region_code);
    	unset($object->country);
    	unset($object->country_id);
    	unset($object->country_code);
    	unset($object->barcode_type);
    	unset($object->barcode_type_code);
    	unset($object->barcode_type_label);
    	unset($object->barcode_type_coder);
    	unset($object->total_ht);
    	unset($object->total_tva);
    	unset($object->total_localtax1);
    	unset($object->total_localtax2);
    	unset($object->total_ttc);
    	unset($object->fk_account);
    	unset($object->comments);
    	unset($object->note);
    	unset($object->mode_reglement_id);
    	unset($object->cond_reglement_id);
    	unset($object->cond_reglement);
    	unset($object->shipping_method_id);
    	unset($object->fk_incoterms);
    	unset($object->label_incoterms);
    	unset($object->location_incoterms);
		*/

    	// If object has lines, remove $db property
    	if (isset($object->lines) && is_array($object->lines) && count($object->lines) > 0)  {
    		$nboflines = count($object->lines);
    		for ($i=0; $i < $nboflines; $i++)
    		{
    			$this->_cleanObjectDatas($object->lines[$i]);

    			unset($object->lines[$i]->lines);
    			unset($object->lines[$i]->note);
    		}
    	}

        return $object;
    }

    /**
     * Validate fields before create or update object
     *
     * @param	array		$data   Array of data to validate
     * @return	array
     *
     * @throws	RestException
     */
    private function _validate($data)
    {
        $entreprise = array();
        foreach ($this->entreprise->fields as $field => $propfield) {
            if (in_array($field, array('rowid', 'entity', 'date_creation', 'tms', 'fk_user_creat')) || $propfield['notnull'] != 1) continue;   // Not a mandatory field
            if (!isset($data[$field]))
                throw new RestException(400, "$field field missing");
            $entreprise[$field] = $data[$field];
        }
        return $entreprise;
    }
}

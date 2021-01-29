<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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

/**
 *   	\file       commentaire_card.php
 *		\ingroup    edisuivi
 *		\brief      Page to create/edit/view commentaire
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB','1');					// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER','1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC','1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN','1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION','1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION','1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK','1');					// Do not check CSRF attack (test on referer + on token if option MAIN_SECURITY_CSRF_WITH_TOKEN is on).
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL','1');				// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK','1');				// Do not check style html tag into posted data
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU','1');				// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML','1');				// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX','1');       	  	// Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                  define("NOLOGIN",'1');						// If this page is public (can be called outside logged session). This include the NOIPCHECK too.
//if (! defined('NOIPCHECK'))                define('NOIPCHECK','1');					// Do not check IP defined into conf $dolibarr_main_restrict_ip
//if (! defined("MAIN_LANG_DEFAULT"))        define('MAIN_LANG_DEFAULT','auto');					// Force lang to a particular value
//if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE','aloginmodule');		// Force authentication handler
//if (! defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN',1);		// The main.inc.php does not make a redirect if not logged, instead show simple error message
//if (! defined("FORCECSP"))                 define('FORCECSP','none');					// Disable all Content Security Policies


// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { $i--; $j--; }
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
dol_include_once('/edisuivi/class/commentaire.class.php');
dol_include_once('/edisuivi/lib/edisuivi_commentaire.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("edisuivi@edisuivi", "other"));

// Get parameters
$orderId = GETPOST('orderId', 'int');
//$lineid   = GETPOST('lineid', 'int');

// Initialize technical objects
$object = new Commentaire($db);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

// Load rights
$permissiontoread = $user->rights->edisuivi->commentaire->read;
$permissiontoadd = $user->rights->edisuivi->commentaire->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
$permissiontodelete = $user->rights->edisuivi->commentaire->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
$permissionnote = $user->rights->edisuivi->commentaire->write; // Used by the include of actions_setnotes.inc.php
$permissiondellink = $user->rights->edisuivi->commentaire->write; // Used by the include of actions_dellink.inc.php
$upload_dir = $conf->edisuivi->multidir_output[isset($object->entity) ? $object->entity : 1];


/*
 * Actions
 */

if( !file_exists('backend/addComment.php')){
	die("[ERREUR::1001] => échec du chargement des fichiers!");
}

require_once ('backend/addComment.php');
$_comment_ = new AddComment($db);

//print("<pre>".print_r($_POST,true)."</pre>");


if(isset($_POST['origin_id']) && isset($_POST['fk_soc']) && isset($_POST['user']) && isset($_POST['comment'])){
	if(!empty($_POST['comment'])){
		$_comment_->setComment($_POST['origin_id'], $_POST['comment'], $_POST['fk_soc'], $_POST['user']);
		$_comment_->addComment();
	}
}

$commentList = $_comment_->getAllCommentsOfOrderById($orderId);
//print("<pre>".print_r($commentList,true)."</pre>");


/*
 * View
 *
 * Put here all code to build page
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader('', $langs->trans('Commentaire'), '');


?>
<style>
#main-container{
  width: 100%;
  margin-left: auto;
  margin-right: auto;
}
.hr {
  width: 45%;
  height: 1px;
  color: #000;
  background-color: #000;
}
.commenter-name{
  font-size: 20px;
  font-weight: bold;
}
.commenter-time{
  font-size: 13px;
}
.commenter-msg{
  width: 100%;
}
.commenter-msg > p{
  margin-top: 5px;
}
#commenting{
  width: 100%;
  border: solid;
  border-width: 1px;
  border-radius: 5px;
  display: flex;
  flex-direction: row;
  text-align: center;
}
#comments-container > p{
    margin: 30px auto;
    width: 250px;
}
.my-row{
  display: flex;
  width: 100%;
}
.my-row > div{
  padding: 7px;
}
#cnt-cmt-icon{
/*   background-color: #f1f1f1; */
  flex: 20px;
}
#cnt-cmt-msg{
/*   background-color: dodgerblue; */
  flex: 80%;
}
#cnt-cmt-msg > textarea{
	width: 100%;
	/* padding: 0px 5px 0px 5px; */
	/* border: none; */
	border: dashed;
	border-width: thin;
	font-size: 20px;
}
#end-cmt-btn{
/*   background-color: red; */
  display: flex;
  flex-direction: row;
  flex: 10%;
}
#end-cmt-btn > div{
  padding-left: 15px;
  margin: auto;
}
#end-cmt-btn > div > img{
	width: 40px;
}
#end-cmt-btn > div:hover{
	cursor: pointer;
}
#submitComment{
	cursor: pointer;
	border: none;
	width: 60px;
	height: 40px;
	/* background-image: url('https://source.unsplash.com/random/40x40'); */
	/* background: url(img/send.png) no-repeat center center fixed; */
	color: white;
	background-color: rgb(43,104,155);
	font-weight: bold;
}
#submitReSyncComment{
	cursor: pointer;
	border: none;
	height: 40px;
	color: white;
	background-color: rgb(43,104,155);
	font-weight: bold;
}
.my-comment{
	width: 50%;
	margin-left: auto;
}
.not-my-comment{
	width: 50%;
	margin-right: auto;
}

/* responsive */
@media (max-width: 1500px) {
  .hr {
	  width: 40%;
	}
}
@media (max-width: 1000px) {
  .hr {
	  width: 35%;
	}
}
@media (max-width: 500px) {
  .hr {
	  width: 30%;
	}
}
@media (max-width: 375px) {
  .hr {
	  width: 25%;
	}
}
@media (max-width: 320px) {
  .hr {
	  width: 20%;
	}
}
@media (max-width: 300px) {
  .flex-container {
    flex-direction: column;
  }
}

</style>

<div id="main-container">

<?php
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
if (!empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}
$id = $orderId;
$usercancreate = $user->rights->commande->creer;
//$result = restrictedArea($user, 'commande', $id);

$object = new Commande($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once

//print("<pre>".print_r($object,true)."</pre>");

if ($object->id < 1) {
	print "Hello World ko ";
	return; 
}
$soc = new Societe($db);
$soc->fetch($object->socid);

$res = $object->fetch_optionals();
		
$head = commande_prepare_head($object);
dol_fiche_head($head, $active = 'Commentaire', 'order', $langs->trans("CustomerOrder"), -1, 'order');

// Order card
$linkback = "<a href='".DOL_URL_ROOT."/commande/card.php?id=$id'>Retour commande</a>";


$morehtmlref = '<div class="refidno">';
// Ref customer
$morehtmlref .= $form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, $usercancreate, 'string', '', 0, 1);
$morehtmlref .= $form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, $usercancreate, 'string', '', null, null, '', 1);

//print("<pre>".print_r($soc,true)."</pre>");

// Thirdparty
$morehtmlref .= '<br>'.$langs->trans('ThirdParty').' : '.$soc->getNomUrl(1);
if (empty($conf->global->MAIN_DISABLE_OTHER_LINK) && $object->thirdparty->id > 0){ 
	$morehtmlref .= ' (<a href="'.DOL_URL_ROOT.'/commande/list.php?socid='.$object->thirdparty->id.'&search_societe='.urlencode($object->thirdparty->name).'">Autres commandes</a>)';
}

// Project
if (!empty($conf->projet->enabled))
{
	$langs->load("projects");
	$morehtmlref .= '<br>'.$langs->trans('Project').' ';
	if ($usercancreate)
	{
		if ($action != 'classify')
			$morehtmlref .= '<a class="editfielda" href="'.$_SERVER['PHP_SELF'].'?action=classify&amp;id='.$object->id.'">'.img_edit($langs->transnoentitiesnoconv('SetProject')).'</a> : ';
		if ($action == 'classify') {
			//$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
			$morehtmlref .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
			$morehtmlref .= '<input type="hidden" name="action" value="classin">';
			$morehtmlref .= '<input type="hidden" name="token" value="'.newToken().'">';
			$morehtmlref .= $formproject->select_projects($object->socid, $object->fk_project, 'projectid', 0, 0, 1, 0, 1, 0, 0, '', 1, 0, 'maxwidth500');
			$morehtmlref .= '<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
			$morehtmlref .= '</form>';
		} else {
			$morehtmlref .= $form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1);
		}
	} else {
		if (!empty($object->fk_project)) {
			$proj = new Project($db);
			$proj->fetch($object->fk_project);
			$morehtmlref .= '<a href="'.DOL_URL_ROOT.'/projet/card.php?id='.$object->fk_project.'" title="'.$langs->trans('ShowProject').'">';
			$morehtmlref .= $proj->ref;
			$morehtmlref .= '</a>';
		} else {
			$morehtmlref .= '';
		}
	}
}

$morehtmlref .= '</div>';


dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);
		
?>

  <div id="allTheComments">
	<?php
		$tmp_dateTime = null;
		if(count($commentList) == 0){
			?>
			<div id="comments-container">
				<p>Aucun commentaire trouvé...</p>
			</div>
			<?php
		}
		
		foreach($commentList as $key => $val){
			if($tmp_dateTime != strftime("%d-%m-%Y", strtotime($val['date_creation']))){
				?>
					<div class="my-row">
					  <hr class="hr"><span> <?php print strftime("%A, %B %d", strtotime($val['date_creation'])); ?> </span><hr class="hr">
					</div>
				<?php
				$tmp_dateTime = strftime("%d-%m-%Y", strtotime($val['date_creation']));
				//print("<pre>".print_r($tmp_dateTime,true)."</pre>");
				//print("<pre>".print_r(strftime("%d-%m-%Y", strtotime($val['date_creation'])),true)."</pre>");
			}
			
			if($val['fk_user'] != null){
				?>
				<div class="my-comment">
					<div class="my-row">
					  <div><img src="img/user_anonymous.png" alt="user image" width="50" height="50"></div>
					  <div>
						<div>
						  <span class="commenter-name"><?php print $val['fk_user']; ?> </span><span class="commenter-time"><?php print strftime("%Hh%M", strtotime($val['date_creation'])); ?></span>
						</div>
						<div class="commenter-msg">
						  <p><?php print $val['text']; ?></p>
						</div>
					  </div>
					</div>
				</div>
				<?php
			}else if($val['fk_soc'] != null){
				?>
				<div class="not-my-comment">
					<div class="my-row">
					  <div><img src="img/user_anonymous.png" alt="user image" width="50" height="50"></div>
					  <div>
						<div>
						  <span class="commenter-name"><?php print $val['fk_soc']; ?> </span><span class="commenter-time"><?php print strftime("%Hh%M", strtotime($val['date_creation'])); ?></span>
						</div>
						<div class="commenter-msg">
						  <p><?php print $val['text']; ?></p>
						</div>
					  </div>
					</div>
				</div>
				<?php
			}
		}
	?>
  </div>
  <div id="commenting">
	<form  class="my-row" action="commentaire_detail.php?orderId=<?php print $orderId ?>" method="POST">
		<div id="cnt-cmt-icon">
			<!--<img src="https://source.unsplash.com/random/20x20" alt="">-->
			<input type="hidden" id="origin_id" name="origin_id" value="<?php print $orderId ?>">
			<input type="hidden" id="fk_soc" name="fk_soc" value="null">
			<input type="hidden" id="user" name="user" value="<?php print $user->id ?>">
		</div>
		<div id="cnt-cmt-msg">
			<textarea name="comment" id="comment" placeholder="<?php print ($user->lastname != null || $user->lastname != "" ? $user->lastname : $user->login); ?>......." cols="20"></textarea>
		</div>
		<div id="end-cmt-btn">
			<!--
			<div><img src="img/emoji.png" alt="emoji icon "></div>
			<div><img id="test-img" src="img/file-folder.png" alt="file attachment icon"></div>
			-->
			<div><input id="submitComment" type="submit" value="Envoyer" title="Envoyer les commentaires"></div>
			<div><button id="submitReSyncComment" type="submit" onclick="reSyncComments()" title="Synchroniser les commentaires">Synchroniser</button></div>
		</div>
	</form>
  </div>
</div>

<!--
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: "#comment",
    plugins: "emoticons",
    toolbar: "emoticons",
    toolbar_location: "bottom",
    menubar: true
  });
</script>
-->

<script>

window.addEventListener('load', () => {
	const mainContainer = document.getElementById("main-container");
	
	if(mainContainer.clientHeight > window.screen.height){
		const allTheComments = document.getElementById("allTheComments");
		const commenting = document.getElementById("commenting");
		const newHeight = window.screen.height - (commenting.clientHeight + commenting.clientHeight + 50 + 250); // +50 for dolibarr navbar, +180 for dolibarr fiche cmd header
		
		allTheComments.style.maxHeight = newHeight+"px";
		allTheComments.style.overflowY = "scroll";
		allTheComments.scrollTop = allTheComments.scrollHeight;
	}
});



const test = document.getElementById("test-img");
test.addEventListener('click', sendComment, false);

function reSyncComments(){
	window.location.reload();
}
</script>



<?php

//print("<pre>".print_r($user,true)."</pre>");

// End of page
llxFooter();
$db->close();

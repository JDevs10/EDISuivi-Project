<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2020 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    edisuivi/admin/setup.php
 * \ingroup edisuivi
 * \brief   EDISuivi setup page.
 */

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
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once '../lib/edisuivi.lib.php';
//require_once "../class/myclass.class.php";

// Translations
$langs->loadLangs(array("admin", "edisuivi@edisuivi"));

// Access control
if (!$user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');

$arrayofparameters = array(
	'EDISUIVI_MYPARAM1'=>array('css'=>'minwidth200', 'enabled'=>1),
	'EDISUIVI_MYPARAM2'=>array('css'=>'minwidth500', 'enabled'=>1)
);



/*
 * Actions
 */

if ((float) DOL_VERSION >= 6)
{
	include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
}

/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
*/

// JL
/*
if( !file_exists('../backend/licence.php')){
	die("[ERREUR::1001] => échec du chargement des fichiers!");
}
*/

require_once ('../backend/licence.php');
$_key_ = new Licence($db);
$_key_->getKey();


/*
 * View
 */

$page_name = "EDISuiviSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'object_edisuivi@edisuivi');

// Configuration header
$head = edisuiviAdminPrepareHead();
dol_fiche_head($head, 'settings', '', -1, "edisuivi@edisuivi");

// Setup page goes here

?>
<form id="form_edisuivi_key_d" name="form_edisuivi_key_d" method="POST" action="setup.php">

	<h3>La clé lience : </h3>
	<div style="display: flex;">
		<input style="margin-right: 20px; width: 250px;" type="text" name="edisuivi_key_d" maxlength="29" value="<?php print $_key_->return_Data(); ?>" readonly="readonly"/>
	</div>
	
</form>
<br>
<br>
<br>
 
<?php

// Page end
dol_fiche_end();

llxFooter();
$db->close();

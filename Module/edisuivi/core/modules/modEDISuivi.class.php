<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019       Frédéric France         <frederic.france@netlogic.fr>
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

/**
 * 	\defgroup   edisuivi     Module EDISuivi
 *  \brief      EDISuivi module descriptor.
 *
 *  \file       htdocs/edisuivi/core/modules/modEDISuivi.class.php
 *  \ingroup    edisuivi
 *  \brief      Description and activation file for module EDISuivi
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module EDISuivi
 */
class modEDISuivi extends DolibarrModules
{
    /**
     * Constructor. Define names, constants, directories, boxes, permissions
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        global $langs, $conf;
        $this->db = $db;

        // Id for module (must be unique).
        // Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
        $this->numero = 9999109; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module
        // Key text used to identify module (for permissions, menus, etc...)
        $this->rights_class = 'edisuivi';
        // Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
        // It is used to group modules by family in module setup page
        $this->family = "other";
        // Module position in the family on 2 digits ('01', '10', '20', ...)
        $this->module_position = '90';
        // Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
        //$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
        // Module label (no space allowed), used if translation string 'ModuleEDISuiviName' not found (EDISuivi is name of module).
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        // Module description, used if translation string 'ModuleEDISuiviDesc' not found (EDISuivi is name of module).
        $this->description = "EDISuiviDescription";
        // Used only if file README.md and README-LL.md not found.
        $this->descriptionlong = "EDISuivi description (Long)";
        $this->editor_name = 'JDevs';
        $this->editor_url = 'https://github.com/JDevs10';
        // Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
        $this->version = 'development 1.0';
        // Url to the file with your last numberversion of this module
        //$this->url_last_version = 'http://www.example.com/versionmodule.txt';

        // Key used in llx_const table to save module status enabled/disabled (where EDISUIVI is value of property name of module in uppercase)
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        // Name of image file used for this module.
        // If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
        // If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
        $this->picto = 'generic';
        // Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
        $this->module_parts = array(
            // Set this to 1 if module has its own trigger directory (core/triggers)
            'triggers' => 0,
            // Set this to 1 if module has its own login method file (core/login)
            'login' => 0,
            // Set this to 1 if module has its own substitution function file (core/substitutions)
            'substitutions' => 0,
            // Set this to 1 if module has its own menus handler directory (core/menus)
            'menus' => 0,
            // Set this to 1 if module overwrite template dir (core/tpl)
            'tpl' => 0,
            // Set this to 1 if module has its own barcode directory (core/modules/barcode)
            'barcode' => 0,
            // Set this to 1 if module has its own models directory (core/modules/xxx)
            'models' => 1,
            // Set this to 1 if module has its own theme directory (theme)
            'theme' => 0,
            // Set this to relative path of css file if module has its own css file
            'css' => array(
                //    '/edisuivi/css/edisuivi.css.php',
            ),
            // Set this to relative path of js file if module must load a js on all pages
            'js' => array(
                //   '/edisuivi/js/edisuivi.js.php',
            ),
            // Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
            'hooks' => array(
                //   'data' => array(
                //       'hookcontext1',
                //       'hookcontext2',
                //   ),
                //   'entity' => '0',
            ),
            // Set this to 1 if features of module are opened to external users
            'moduleforexternal' => 0,
        );
        // Data directories to create when module is enabled.
        // Example: this->dirs = array("/edisuivi/temp","/edisuivi/subdir");
        $this->dirs = array("/edisuivi/temp");
        // Config pages. Put here list of php page, stored into edisuivi/admin directory, to use to setup module.
        $this->config_page_url = array("setup.php@edisuivi");
        // Dependencies
        // A condition to hide module
        $this->hidden = false;
        // List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
        $this->depends = array();
        $this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
        $this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)
        $this->langfiles = array("edisuivi@edisuivi");
        $this->phpmin = array(5, 5); // Minimum version of PHP required by module
        $this->need_dolibarr_version = array(11, -3); // Minimum version of Dolibarr required by module
        $this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
        $this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
        //$this->automatic_activation = array('FR'=>'EDISuiviWasAutomaticallyActivatedBecauseOfYourCountryChoice');
        //$this->always_enabled = true;								// If true, can't be disabled

        // Constants
        // List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
        // Example: $this->const=array(1 => array('EDISUIVI_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
        //                             2 => array('EDISUIVI_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
        // );
        $this->const = array(
            // 1 => array('EDISUIVI_MYCONSTANT', 'chaine', 'avalue', 'This is a constant to add', 1, 'allentities', 1)
        );

        // Some keys to add into the overwriting translation tables
        /*$this->overwrite_translation = array(
            'en_US:ParentCompany'=>'Parent company or reseller',
            'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
        )*/

        if (!isset($conf->edisuivi) || !isset($conf->edisuivi->enabled)) {
            $conf->edisuivi = new stdClass();
            $conf->edisuivi->enabled = 0;
        }

        // Array to add new pages in new tabs
        $this->tabs = array();
        // Example:
        // $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@edisuivi:$user->rights->edisuivi->read:/edisuivi/mynewtab1.php?id=__ID__');  					// To add a new tab identified by code tabname1
        // $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@edisuivi:$user->rights->othermodule->read:/edisuivi/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
        // $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
        $this->tabs[] = array('data'=>'order:+Commentaire:Commentaire:mylangfile@edisuivi:$user->rights->edisuivi->commentaire->read:/edisuivi/commentaire_detail.php?orderId=__ID__');
		//
        // Where objecttype can be
        // 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
        // 'contact'          to add a tab in contact view
        // 'contract'         to add a tab in contract view
        // 'group'            to add a tab in group view
        // 'intervention'     to add a tab in intervention view
        // 'invoice'          to add a tab in customer invoice view
        // 'invoice_supplier' to add a tab in supplier invoice view
        // 'member'           to add a tab in fundation member view
        // 'opensurveypoll'	  to add a tab in opensurvey poll view
        // 'order'            to add a tab in customer order view
        // 'order_supplier'   to add a tab in supplier order view
        // 'payment'		  to add a tab in payment view
        // 'payment_supplier' to add a tab in supplier payment view
        // 'product'          to add a tab in product view
        // 'propal'           to add a tab in propal view
        // 'project'          to add a tab in project view
        // 'stock'            to add a tab in stock view
        // 'thirdparty'       to add a tab in third party view
        // 'user'             to add a tab in user view

        // Dictionaries
        $this->dictionaries = array();
        /* Example:
        $this->dictionaries=array(
            'langs'=>'edisuivi@edisuivi',
            // List of tables we want to see into dictonnary editor
            'tabname'=>array(MAIN_DB_PREFIX."table1", MAIN_DB_PREFIX."table2", MAIN_DB_PREFIX."table3"),
            // Label of tables
            'tablib'=>array("Table1", "Table2", "Table3"),
            // Request to select fields
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),
            // Sort order
            'tabsqlsort'=>array("label ASC", "label ASC", "label ASC"),
            // List of fields (result of select to show dictionary)
            'tabfield'=>array("code,label", "code,label", "code,label"),
            // List of fields (list of fields to edit a record)
            'tabfieldvalue'=>array("code,label", "code,label", "code,label"),
            // List of fields (list of fields for insert)
            'tabfieldinsert'=>array("code,label", "code,label", "code,label"),
            // Name of columns with primary key (try to always name it 'rowid')
            'tabrowid'=>array("rowid", "rowid", "rowid"),
            // Condition to show each dictionary
            'tabcond'=>array($conf->edisuivi->enabled, $conf->edisuivi->enabled, $conf->edisuivi->enabled)
        );
        */

        // Boxes/Widgets
        // Add here list of php file(s) stored in edisuivi/core/boxes that contains a class to show a widget.
        $this->boxes = array(
              0 => array(
                  'file' => 'edisuiviwidget1.php@edisuivi',
                  'note' => 'Widget provided by EDISuivi',
                  //'enabledbydefaulton' => 'Home',
              ),
            //  ...
        );

        // Cronjobs (List of cron jobs entries to add when module is enabled)
        // unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
        $this->cronjobs = array(
            //  0 => array(
            //      'label' => 'MyJob label',
            //      'jobtype' => 'method',
            //      'class' => '/edisuivi/class/entreprise.class.php',
            //      'objectname' => 'Entreprise',
            //      'method' => 'doScheduledJob',
            //      'parameters' => '',
            //      'comment' => 'Comment',
            //      'frequency' => 2,
            //      'unitfrequency' => 3600,
            //      'status' => 0,
            //      'test' => '$conf->edisuivi->enabled',
            //      'priority' => 50,
            //  ),
        );
        // Example: $this->cronjobs=array(
        //    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'$conf->edisuivi->enabled', 'priority'=>50),
        //    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'$conf->edisuivi->enabled', 'priority'=>50)
        // );

        // Permissions provided by this module
        $this->rights = array();
        $r = 0;
        // Add here entries to declare new permissions
        /* BEGIN MODULEBUILDER PERMISSIONS */
        // EDISuivi Global
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Read generale of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'edisuivi'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Create/Update generale of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'edisuivi'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Delete generale of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'edisuivi'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        
        // Entreprise
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Read Entreprise of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'entreprise'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Create/Update Entreprise of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'entreprise'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Delete Entreprise of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'entreprise'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        
        // Utilisateur
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Read Utilisateur of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'utilisateur'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Create/Update Utilisateur of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'utilisateur'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Delete Utilisateur of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'utilisateur'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        
        // Commentaire
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Read Comments of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'commentaire'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Create/Update Comments of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'commentaire'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Delete Comments of EDISuivi'; // Permission label
        $this->rights[$r][4] = 'commentaire'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->edisuivi->level1->level2)
        $r++;
        // END MODULEBUILDER PERMISSIONS


        // Main menu entries to add
        $this->menu = array();
        $r = 0;
        // Add here entries to declare new menus
        /* BEGIN MODULEBUILDER TOPMENU */
        $this->menu[$r++] = array(
            'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'top', // This is a Top menu entry
            'titre'=>'EDISuivi',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'',
            'url'=>'/edisuivi/edisuiviindex.php',
            'langs'=>'edisuivi@edisuivi', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000 + $r,
            'enabled'=>'$conf->edisuivi->enabled', // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled.
            'perms'=>'$user->rights->edisuivi->edisuivi->read', // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
        );
        /* END MODULEBUILDER TOPMENU */
        
        // BEGIN MODULEBUILDER LEFTMENU ENTREPRISE
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',                          // This is a Top menu entry
            'titre'=>'Entreprise',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_entreprise',
            // 'url'=>'/edisuivi/utilisateur_page.php?action=create',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled.
            'perms'=>'$user->rights->edisuivi->entreprise->read',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi,fk_leftmenu=edisuivi_entreprise',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',			                // This is a Left menu entry
            'titre'=>'Nouvelle Entreprise',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_entreprise_new',
            'url'=>'/edisuivi/entreprise_card.php?action=create',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms'=>'$user->rights->edisuivi->utilisateur->write',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi,fk_leftmenu=edisuivi_entreprise',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',			                // This is a Left menu entry
            'titre'=>'Liste',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_entreprise_list',
            'url'=>'/edisuivi/entreprise_list.php',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms'=>'$user->rights->edisuivi->entreprise->read',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );

		/* END MODULEBUILDER LEFTMENU ENTREPRISE */
        
        // BEGIN MODULEBUILDER LEFTMENU UTILISATEUR
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',                          // This is a Top menu entry
            'titre'=>'Utilisateur',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_utilisateur',
            // 'url'=>'/edisuivi/utilisateur_page.php?action=create',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled.
            'perms'=>'$user->rights->edisuivi->utilisateur->read',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi,fk_leftmenu=edisuivi_utilisateur',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',			                // This is a Left menu entry
            'titre'=>'Nouveau Utilisateur',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_utilisateur_new',
            'url'=>'/edisuivi/utilisateur_card.php?action=create',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms'=>'$user->rights->edisuivi->utilisateur->write',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi,fk_leftmenu=edisuivi_utilisateur',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',			                // This is a Left menu entry
            'titre'=>'Liste',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_utilisateur_list',
            'url'=>'/edisuivi/utilisateur_list.php',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms'=>'$user->rights->edisuivi->utilisateur->read',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );
        // END MODULEBUILDER LEFTMENU UTILISATEUR
        
        // BEGIN MODULEBUILDER LEFTMENU COMMENTAIRE
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',                          // This is a Top menu entry
            'titre'=>'Commentaire',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_commentaire',
            // 'url'=>'/edisuivi/commentaire_page.php?action=create',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled.
            'perms'=>'$user->rights->edisuivi->commentaire->read',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi,fk_leftmenu=edisuivi_commentaire',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',			                // This is a Left menu entry
            'titre'=>'Nouveau Commentaire',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_commentaire_new',
            'url'=>'/edisuivi/commentaire_card.php?action=create',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms'=>'$user->rights->edisuivi->commentaire->write',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );
        $this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=edisuivi,fk_leftmenu=edisuivi_commentaire',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'left',			                // This is a Left menu entry
            'titre'=>'Liste',
            'mainmenu'=>'edisuivi',
            'leftmenu'=>'edisuivi_commentaire_list',
            'url'=>'/edisuivi/commentaire_list.php',
            'langs'=>'edisuivi@edisuivi',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>9000+$r,
            'enabled'=>'$conf->edisuivi->enabled',  // Define condition to show or hide menu entry. Use '$conf->edisuivi->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms'=>'$user->rights->edisuivi->commentaire->read',			                // Use 'perms'=>'$user->rights->edisuivi->level1->level2' if you want your menu with a permission rules
            'target'=>'',
            'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
        );
        // END MODULEBUILDER LEFTMENU COMMENTAIRE

        

        // Exports profiles provided by this module
        $r = 1;
        /* BEGIN MODULEBUILDER EXPORT ENTREPRISE */
        /*
        $langs->load("edisuivi@edisuivi");
        $this->export_code[$r]=$this->rights_class.'_'.$r;
        $this->export_label[$r]='EntrepriseLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
        $this->export_icon[$r]='entreprise@edisuivi';
        // Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
        $keyforclass = 'Entreprise'; $keyforclassfile='/mymobule/class/entreprise.class.php'; $keyforelement='entreprise@edisuivi';
        include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
        //$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
        //unset($this->export_fields_array[$r]['t.fieldtoremove']);
   		//$keyforclass = 'EntrepriseLine'; $keyforclassfile='/edisuivi/class/entreprise.class.php'; $keyforelement='entrepriseline@edisuivi'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
        $keyforselect='entreprise'; $keyforaliasextra='extra'; $keyforelement='entreprise@edisuivi';
        include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
        //$keyforselect='entrepriseline'; $keyforaliasextra='extraline'; $keyforelement='entrepriseline@edisuivi';
        //include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
        //$this->export_dependencies_array[$r] = array('entrepriseline'=>array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
        //$this->export_special_array[$r] = array('t.field'=>'...');
        //$this->export_examplevalues_array[$r] = array('t.field'=>'Example');
        //$this->export_help_array[$r] = array('t.field'=>'FieldDescHelp');
        $this->export_sql_start[$r]='SELECT DISTINCT ';
        $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'entreprise as t';
        //$this->export_sql_end[$r]  =' LEFT JOIN '.MAIN_DB_PREFIX.'entreprise_line as tl ON tl.fk_entreprise = t.rowid';
        $this->export_sql_end[$r] .=' WHERE 1 = 1';
        $this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('entreprise').')';
        $r++; */
        /* END MODULEBUILDER EXPORT ENTREPRISE */

        // Imports profiles provided by this module
        $r = 1;
        /* BEGIN MODULEBUILDER IMPORT ENTREPRISE */
        /*
         $langs->load("edisuivi@edisuivi");
         $this->export_code[$r]=$this->rights_class.'_'.$r;
         $this->export_label[$r]='EntrepriseLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
         $this->export_icon[$r]='entreprise@edisuivi';
         $keyforclass = 'Entreprise'; $keyforclassfile='/mymobule/class/entreprise.class.php'; $keyforelement='entreprise@edisuivi';
         include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
         $keyforselect='entreprise'; $keyforaliasextra='extra'; $keyforelement='entreprise@edisuivi';
         include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
         //$this->export_dependencies_array[$r]=array('mysubobject'=>'ts.rowid', 't.myfield'=>array('t.myfield2','t.myfield3')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
         $this->export_sql_start[$r]='SELECT DISTINCT ';
         $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'entreprise as t';
         $this->export_sql_end[$r] .=' WHERE 1 = 1';
         $this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('entreprise').')';
         $r++; */
        /* END MODULEBUILDER IMPORT ENTREPRISE */
    }

    /**
     *  Function called when module is enabled.
     *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     *  It also creates data directories
     *
     *  @param      string  $options    Options when enabling module ('', 'noboxes')
     *  @return     int             	1 if OK, 0 if KO
     */
    public function init($options = '')
    {
        global $conf, $langs;

        $result = $this->_load_tables('/edisuivi/sql/');
        if ($result < 0) return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')

        // Create extrafields during init
        //include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
        //$extrafields = new ExtraFields($this->db);
        //$result1=$extrafields->addExtraField('myattr1', "New Attr 1 label", 'boolean', 1,  3, 'thirdparty',   0, 0, '', '', 1, '', 0, 0, '', '', 'edisuivi@edisuivi', '$conf->edisuivi->enabled');
        //$result2=$extrafields->addExtraField('myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project',      0, 0, '', '', 1, '', 0, 0, '', '', 'edisuivi@edisuivi', '$conf->edisuivi->enabled');
        //$result3=$extrafields->addExtraField('myattr3', "New Attr 3 label", 'varchar', 1, 10, 'bank_account', 0, 0, '', '', 1, '', 0, 0, '', '', 'edisuivi@edisuivi', '$conf->edisuivi->enabled');
        //$result4=$extrafields->addExtraField('myattr4', "New Attr 4 label", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Val1','code2'=>'Val2','code3'=>'Val3')), 1,'', 0, 0, '', '', 'edisuivi@edisuivi', '$conf->edisuivi->enabled');
        //$result5=$extrafields->addExtraField('myattr5', "New Attr 5 label", 'text',    1, 10, 'user',         0, 0, '', '', 1, '', 0, 0, '', '', 'edisuivi@edisuivi', '$conf->edisuivi->enabled');

        // Permissions
        $this->remove($options);

        $sql = array();

        // ODT template
        /*
        $src=DOL_DOCUMENT_ROOT.'/install/doctemplates/edisuivi/template_entreprises.odt';
        $dirodt=DOL_DATA_ROOT.'/doctemplates/edisuivi';
        $dest=$dirodt.'/template_entreprises.odt';

        if (file_exists($src) && ! file_exists($dest))
        {
            require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
            dol_mkdir($dirodt);
            $result=dol_copy($src, $dest, 0, 0);
            if ($result < 0)
            {
                $langs->load("errors");
                $this->error=$langs->trans('ErrorFailToCopyFile', $src, $dest);
                return 0;
            }
        }

		*/
        $sql = array(
            "DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = '".$this->db->escape($this->const[0][2])."' AND type = 'edisuivi' AND entity = ".$conf->entity,
            "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('".$this->db->escape($this->const[0][2])."','edisuivi',".$conf->entity.")"
        );
        
		
		// Create module
		$this->_init($sql, $options);
		
		// Create User edisuivi
        $EdiSuivi_login = "edisuivi";
        $EdiSuivi_mdp = "anexys1";
        $EdiSuivi_api_key = "3-8-13-12-7-8-24-8";
        $query1 = "INSERT INTO llx_user (`rowid`, `entity`, `ref_ext`, `ref_int`, `employee`, `fk_establishment`, `datec`, `tms`, `fk_user_creat`, `fk_user_modif`, `login`, `pass`, `pass_crypted`, `pass_temp`, `api_key`, `gender`, `civility`, `lastname`, `firstname`, `address`, `zip`, `town`, `fk_state`, `fk_country`, `job`, `skype`, `office_phone`, `office_fax`, `user_mobile`, `personal_mobile`, `email`, `personal_email`, `socialnetworks`, `signature`, `admin`, `module_comm`, `module_compta`, `fk_soc`, `fk_socpeople`, `fk_member`, `fk_user`, `fk_user_expense_validator`, `fk_user_holiday_validator`, `note_public`, `note`, `model_pdf`, `datelastlogin`, `datepreviouslogin`, `egroupware_id`, `ldap_sid`, `openid`, `statut`, `photo`, `lang`, `color`, `barcode`, `fk_barcode_type`, `accountancy_code`, `nb_holiday`, `thm`, `tjm`, `salary`, `salaryextra`, `dateemployment`, `dateemploymentend`, `weeklyhours`, `import_key`, `birth`, `pass_encoding`, `default_range`, `default_c_exp_tax_cat`, `twitter`, `facebook`, `instagram`, `snapchat`, `googleplus`, `youtube`, `whatsapp`, `linkedin`, `fk_warehouse`, `iplastlogin`, `ippreviouslogin`) 
					VALUES (NULL, '1', NULL, NULL, '1', '0', NULL, CURRENT_TIMESTAMP, NULL, NULL, '$EdiSuivi_login', '$EdiSuivi_mdp,', NULL, NULL, '$EdiSuivi_api_key', NULL, NULL, '$EdiSuivi_login', NULL, NULL, NULL, NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, '0', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		
		$res = $this->db->query($query1);
		// Get User edisuivi id
		$userId = -1;
		$query2 = "SELECT rowid FROM llx_user where login = '$EdiSuivi_login' AND lastname = '$EdiSuivi_login'";
		
		$res = $this->db->query($query2);
		while($row = $this->db->fetch_array($query2)){
			$userId = $row['rowid'];
		}
		
		// get dolibar permissions
		if($userId > 0){
			$rightsTable = [];
			//$query3 = "SELECT * FROM llx_rights_def WHERE module = 'edisuivi'";
			$query3 = "SELECT * FROM `llx_rights_def` WHERE module = 'edisuivi' OR module = 'commande' AND perms = 'lire' AND entity = 1";
			$res = $this->db->query($query3);
			
			if ($res->num_rows > 0) {
				$i=0;
				while($row = $this->db->fetch_array($query3)){
					$rightsTable[$i] = array(
						"fk_user" => $userId,
						"fk_id" => $row['id']
					);
					$i++;
				}
				
				for($z=0; $z<($i+1); $z++){
					$query4 = "INSERT INTO llx_user_rights (rowid, entity, fk_user, fk_id) VALUES (null, 1, ".$rightsTable[$z]['fk_user'].", ".$rightsTable[$z]['fk_id'].")";
					$res = $this->db->query($query4);
				}
			}
		}
		
		
		
		/*
		$sql = array(
            $query1
        );
		*/

        return;
    }

    /**
     *  Function called when module is disabled.
     *  Remove from database constants, boxes and permissions from Dolibarr database.
     *  Data directories are not deleted
     *
     *  @param      string	$options    Options when enabling module ('', 'noboxes')
     *  @return     int                 1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = array();
		
		// Get User edisuivi id
		$EdiSuivi_login = "edisuivi";
		$userId = -1;
		$query1 = "SELECT rowid FROM llx_user where login = '$EdiSuivi_login' AND lastname = '$EdiSuivi_login'";
		
		$res = $this->db->query($query1);
		if ($res->num_rows > 0) {
			while($row = $this->db->fetch_array($query1)){
				$userId = $row['rowid'];
			}
			
			$query2 = "DELETE FROM llx_user_rights where fk_user = ".$userId;
			$query3 = "DELETE FROM llx_user where login = '$EdiSuivi_login' AND lastname = '$EdiSuivi_login'";
			$sql = array(
				$query2,
				$query3
			);
		}
		
        return $this->_remove($sql, $options);
    }
}

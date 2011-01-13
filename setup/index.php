<?php
/**************************************************************
 * 
 * etempus setup
 * (c) 2009 by cyrill von wattenwyl, protagonist gmbh
 * 
 * ***********************************************************/

//benötigte definitionen & klassen laden
session_start();
define("ET_DEFAULT_LANG","de");
include("../class/tpl.class.php");
include("class/setup.class.php");
include("class/sql_import.class.php");
include("config/setup.php");
if (is_dir("../pear/")){
	ini_set('include_path',ini_get('include_path').':pear/:../pear/:../:');
}


//sprache laden
global $txt;
if ($_SERVER['HTTP_ACCEPT_LANGUAGE']) $_SESSION['lang']=$_SERVER['HTTP_ACCEPT_LANGUAGE'];
else $_SESSION['lang']=ET_DEFAULT_LANG;
if (is_file("text/text.{$_SESSION['lang']}.php")) include("text/text.{$_SESSION['lang']}.php");
else include("text/text.".ET_DEFAULT_LANG.".php");
if (is_file("../text/text.{$_SESSION['lang']}.php")) include("../text/text.{$_SESSION['lang']}.php");
else include("../text/text.".ET_DEFAULT_LANG.".php");


//sprachstring laden
function __($str){
	global $txt;
	return $txt[$str];
}


//setup-objekt erzeugen
$setup = new etempus_setup;


//einzelne schritte abarbeiten
switch ($_REQUEST['step']){
		
		//schritt 1: begrüssung, datenbanktyp, ftp-daten
		default:
			$setup->step1();
			break;
			
		// schritt 2: rechte prüfen & setzen, login und limits auswählen
		case "2":
			$setup->step2();
			break;
		
		//schritt 3: datenbankstruktur importieren, conf-files schreiben 
		case "3":
			$setup->step3();
			break;
		
		//schritt 4 abschluss, admin-user erstellen, setup-dateien löschen wenn nötig
		case "4":
			$setup->step4();
			break;		
}

?>

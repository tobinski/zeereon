<?php
session_start();
if (is_dir("pear/")){
	ini_set('include_path',ini_get('include_path').':pear/:');
}

$dn=dirname(__FILE__);


include("{$dn}/config/constants.php");
include("{$dn}/class/etempus.class.php");
include("{$dn}/class/et_config.class.php");
include("{$dn}/class/tpl.class.php");
include("{$dn}/class/auswertung.class.php");
include("{$dn}/class/out.class.php");
include("{$dn}/class/langloader.class.php");
include("{$dn}/class/xml.functions.php");
include("{$dn}/class/database.functions.php");
db_init();

// goto setup
if (!db_make()){ header("Location: setup/index.php"); die; }


//sprache laden
$et=new etempus;
$user_arr=$et->table_item_array("user",$et->get_user_id());
if ($user_arr['lang']){
	$_SESSION['lang']=$user_arr['lang'];
} else {
	$_SESSION['lang']=ET_DEFAULT_LANG;
}
global $txt;
include("text/text.{$_SESSION['lang']}.php");

function __($str){
	global $txt;
	return $txt[$str];
}


?>

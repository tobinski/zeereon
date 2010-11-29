<?php

include("../../../class/etempus.class.php");
include("../abo.class.php");

// bypass etempus-constructor damit user nicht überprüft wird
class et_beleg_abo_upd_bp extends et_beleg_abo {
	public function __construct(){
		$this->db=db_make();
	}
}

//db init mit angepasstem pfad
function db_init(){
	require_once('DB.php');
	require_once('../../../config/db.php');
	$GLOBALS['etempus_db_config_obj']=new etempus_db_config;
}

//db-make für updater
function db_make(){
	$et_db=$GLOBALS['etempus_db_config_obj']; 
	$db = DB::connect($et_db->dsn, $et_db->options);
	if (PEAR::isError($db)) {
		return false;
	}
	unset($et_db);
	return $db;
}

//datenbank init
db_init();

//beleg-abos updaten
$etempus=new et_beleg_abo_upd_bp;
$etempus->update_abos();
$num=count($etempus->updated_entrys);
die(("etempus-200-$num-{$etempus->update_cont}-".time())."\n");

?>

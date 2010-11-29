<?php

function db_init(){
		require_once('DB.php');
		require_once('config/db.php');
		$GLOBALS['etempus_db_config_obj']=new etempus_db_config;
}

function db_make(){
		$et_db=$GLOBALS['etempus_db_config_obj']; 
		$db = DB::connect($et_db->dsn, $et_db->options);
		if (PEAR::isError($db)) {
				return false;
		}
		unset($et_db);
		return $db;
}

?>

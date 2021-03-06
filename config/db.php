<?php
/**************************************************
 * 
 * eTempus datenbank-konfiguration
 * (c) 2009 cyrill v.wattenwyl, protagonist gmbh
 * 
 * ************************************************/
class etempus_db_config {
		
		/*
		 * dsn-array
		 * mehr unter: http://pear.php.net/manual/de/package.database.db.intro-dsn.php
		 */
// 		public $dsn = false;
		
		/*
		 * optionen-array
		 * mehr unter: http://pear.php.net/manual/de/package.database.db.db-common.setoption.php
		 */
		public $dsn = array(	'phptype'  => "sqlite");

		public $options = array('debug'       => 2,
    							'portability' => DB_PORTABILITY_ALL,
							   );
}
?>

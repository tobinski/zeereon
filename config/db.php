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
		public $dsn = array(	'phptype'  => "mysql",
								'mode'	   => false,
								'dbsyntax' => false,
								'username' => "etempus",
								'password' => "fgKJnfoFtn9",
								'protocol' => false,
								'hostspec' => "localhost",
								'port'     => false,
								'socket'   => false,
								'database' => "protago_etempus"								
							);
		
		/*
		 * optionen-array
		 * mehr unter: http://pear.php.net/manual/de/package.database.db.db-common.setoption.php
		 */
		public $options = array('debug'       => 2,
    							'portability' => DB_PORTABILITY_ALL,
							   );
	
}
?>

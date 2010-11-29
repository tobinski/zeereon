<?php
/**************************************************
 * 
 * eTempus konfiguration
 * (c) 2009 cyrill v.wattenwyl, protagonist gmbh
 * 
 * ************************************************/


//kommentar wird benötigt (true/false)
define("ET_COMMENT_REQUIRED"		,	false);

//maximal-user (* für unbeschränkt)
define("ET_MAX_USER"			,	"*" );

//standartsprache
define("ET_DEFAULT_LANG"		,	"de" ); 

//prozentsatz wenn warnung für kostendach
define("ET_KOSTENDACH_WARNUNG"		,	"85" );



/***************************************
datacenter (remote-anbindung)
***************************************/
//senden einschalten
define("ET_DATACENTER_SEND"		,	true );

//empfangen einschalten
define("ET_DATACENTER_RCV"		,	true );

//löschen von einträgen übers datacenter erlauben/verbieten
define("ET_DATACENTER_DEL"		,	true );

//datenbank-dump zulassen
define("ET_DATACENTER_DBDUMP"	,	true );

?>

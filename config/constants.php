<?php
/**************************************************
 * 
 * eTempus konfiguration
 * (c) 2009 cyrill v.wattenwyl, protagonist gmbh
 * 
 * ************************************************/


//maximal-user (* für unbeschränkt)
define("ET_MAX_USER"			,	"*" );

//standartsprache
define("ET_DEFAULT_LANG"		,	"de" ); 

//standartlayout
define("ET_DEFAULT_STYLE"		,	"et-eni" ); 

//prozentsatz wenn warnung für kostendach
define("ET_KOSTENDACH_WARNUNG"		,	"85" );

//anzahl nachrichten auf der startseite
define("ET_START_MSGNUM"		,	"15" );

//erlaubte html-tags für input-filter
define("ETEMPUS_ALLOWED_HTML"	,	"<a><p><br><span><img><strong><i><b><big><small>" );		

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

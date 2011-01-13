<?php

//plugin registrieren
$plugin['name'] 		= 	"oo_export";
$plugin['place'] 		=	"auswerten";
$plugin['menu_str']		=	"{oo_export}";
$plugin['class']		=	"et_oo_rechnung";
$plugin['admin_only']	=	false;

//texte deutsch
$lang['de']['oo_export']		=	"Rechnungs-Export";
$lang['de']['oo_export_created']=	"Erstellt am %s";
$lang['de']['oo_export_fname']	=	"rechnung_%s_%s.odt";
$lang['de']['oder']				=	"oder";
$lang['de']['alles_auswerten']	=	"Alles auswerten";
$lang['de']['oo_export_all_einleitung']="Bitte alle Eintr&auml;ge w&auml;hlen ";
$lang['de']['check_all']		= "Alles ausw&auml;hlen";
$lang['de']['uncheck_all']		= "Auswahl entfernen";
$lang['de']['oo_export_fname_all']		= "eTempus_rechnungen_%s.zip"; 
$lang['de']['download_zip_file']= "Archiv herunterladen (.zip)";

$lang['de']['oo_export_fname_d']	=	"rechnung_%s_%s.doc";
$lang['de']['einleitung']	=	"Willkommen beim Rechnungs-Export-Plugin.<br />Die Auswahlmaske unten passt sich automatisch den Anforderungen an.  ";
$lang['de']['art_auswaehlen']	=	"Rechnungsart";
$lang['de']['einzelne_rechnung']	= "Rechnung von einzenlem Kunden";
$lang['de']['alle_rechnungen']	=	"Rechnung von allen aktiven Kunden innerhalb des gew&auml;hlten Zeitraums in zip-Archiv";
$lang['de']['von_typ']	=	"Rechnungstyp";
$lang['de']['kundenbasiert']	=	"Rechnung pro Kunde";
$lang['de']['projektbasiert']	=	"Rechnung pro Projekt";
$lang['de']['format']	=	"Datei-Format";
$lang['de']['odt']	=	"OpenOffice (.odt)";
$lang['de']['doc']	=	"Microsoft Word (.doc)";
$lang['de']['button_oo_erstellen']	=	"Rechnung erstellen";
$lang['de']['button_oo_erstellen_m']	= "Rechnungen erstellen";	



//texte english
$lang['en']['oo_export']		=	"Bill-Export";
$lang['en']['oo_export_created']=	"Created on %s";
$lang['en']['oo_export_fname']	=	"bill_%s_%s.odt";
$lang['en']['oder']				=	"or";
$lang['en']['alles_auswerten']	=	"Evaluate all";
$lang['en']['check_all']		= "Check all";
$lang['en']['uncheck_all']		= "Uncheck";
$lang['en']['oo_export_fname_all']	= "eTempus_bills_%s.zip"; 
$lang['en']['download_zip_file']= "Download Archive (.zip)";
$lang['en']['oo_export_fname_d']	=	"bill_%s_%s.doc";
$lang['en']['einleitung']	=	" ";
$lang['en']['art_auswaehlen']	=	"Bill-Type";
$lang['en']['einzelne_rechnung']	= "Bill for a single Customer";
$lang['en']['alle_rechnungen']	=	"Bills of all active Customers in selected Time in an zip-Archive";
$lang['en']['von_typ']	=	"Type";
$lang['en']['kundenbasiert']	=	"1 Bill per Client";
$lang['en']['projektbasiert']	=	"1 Bill per Project";
$lang['en']['format']	=	"File-Format";
$lang['en']['odt']	=	"OpenOffice (.odt)";
$lang['en']['doc']	=	"Microsoft Word (.doc)";
$lang['en']['button_oo_erstellen']	=	"Create Bill";
$lang['en']['button_oo_erstellen_m']	= "Create Bills";	




//plugin-klassen laden
include_once("zip.class.php");
include_once("oo_table.class.php");
include_once("doc_table.class.php");
include_once("et_oo_rechnung.class.php");

?>

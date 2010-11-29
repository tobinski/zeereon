<?php

//plugin registrieren
$plugin['name'] 	= 	"oo_export_conf";
$plugin['place'] 	=	"einstellungen";
$plugin['menu_str']	=	"{oo_export}";
$plugin['class']	=	"et_oo_rechnung_conf";
$plugin['admin_only']=true;	

//texte deutsch
$lang['de']['oo_export']		=	"Rechnungs-Export";
$lang['de']['oo_export_conf']	=	"Einstellungen OpenOffice-Rechnungen";
$lang['de']['links']			=	"links";
$lang['de']['oben']				=	"oben";
$lang['de']['oo_export_logo']	=	"Logo";
$lang['de']['oo_export_logo_f']	=	"Logodatei";
$lang['de']['oo_export_pos_addr']=	"Position Adresse";
$lang['de']['oo_export_logo_pos']=	"Position Logo";
$lang['de']['oo_export_foot']	=	"Footer-Text";
$lang['de']['oo_export_logo_prev']=	"Logovorschau";
$lang['de']['oo_export_titel']		="Titel der Rechnung";
$lang['de']['oo_export_text']		="Einf&uuml;hrungstext";


//texte english
$lang['en']['oo_export']		=	"Bill-Export";
$lang['en']['oo_export_conf']	=	"Configuration OpenOffice-Bills";
$lang['en']['links']			=	"left";
$lang['en']['oben']				=	"top";
$lang['en']['oo_export_logo']	=	"Logo";
$lang['en']['oo_export_logo_f']	=	"Logo-File";
$lang['en']['oo_export_pos_addr']=	"Position Adress";
$lang['en']['oo_export_logo_pos']=	"Position Logo";
$lang['en']['oo_export_foot']	=	"Footer-Text";
$lang['en']['oo_export_logo_prev']=	"Logo preview";
$lang['en']['oo_export_titel']		="Bill Title";
$lang['en']['oo_export_text']		="Text";

//plugin-klasse laden
include_once("et_oo_rechnung_conf.class.php");

?>

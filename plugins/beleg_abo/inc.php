<?php

//plugin registrieren
$plugin['name'] 	= 	"beleg_abo";
$plugin['place'] 	=	"zeit";
$plugin['menu_str']	=	"abo";
$plugin['class']	=	"et_beleg_abo";



//plugin-klasse laden
@include_once("abo.class.php");
@include("lang.php");
$lang['de']=$de;
$lang['en']=$en;


?>

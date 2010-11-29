<?php

	  
 
//
// tabs section zeit
//
$tabs_zeit = array(	"text"=>"topmenu_zeit",
					"image"=>"css/screen/images/22x22/time.png",
					"show"=>true,
					
					array(	"function"	=> "index",
							"image"		=> false,
							"text"		=> "zeit",
							"onclick"	=> false,
							"req"		=> false ),
							
					array(	"function"	=> "index",
							"image"		=> false,
							"text"		=> "belege",
							"onclick"	=> false,
							"req"		=> array("beleg"=>"true")
							 ),
					);	
					

//
// tabs section auswertung
//			
$tabs_ausw = array(	"text"=>"topmenu_auswerten",
					"image"=>"css/screen/images/22x22/bill.png",
					"show"=>true,
					
					array(	"function"	=> "index",
							"image"		=> false,
							"text"		=> "button_auswerten",
							"onclick"	=> false ) );
							
							
							
//
// tabs section einstellungen
//			
$tabs_einst = array("text"=>"topmenu_einstellungen",
					"image"=>"css/screen/images/22x22/preference.png",
					"show"=>true,
					
					array(	"function"	=> "index",
							"text"		=> "einstellungen",
							"req"		=> array("modul"=>"start")),
				
					array(	"function"	=> "build",
							"text"		=> "config_user",
							"admin_only"=> true,
							"req"		=> array("modul"=>"user") ),
							
					array(	"function"	=> "build",
							"text"		=> "config_ansatz",
							"admin_only"=> true,
							"req"		=> array("modul"=>"ansatz") ),		

							
							 );
						
							

$tabs['zeit']=$tabs_zeit;
$tabs['auswerten']=$tabs_ausw;
$tabs['einstellungen']=$tabs_einst;
			
							
							
							
							
							
 
?>

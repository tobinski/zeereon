<?php
class etempus_auswerten extends etempus {

		public $layout=true;
		
		
		function index(){
				
				//auswerten
				if ($this->getvar('art')){
						$eta=new etempus_auswertung;
						$eta->auswerten();
						if ($this->getvar('format')=='html_new'){
							$this->layout=false;
							echo $this->template->go("auswerten_html_newwin.tpl",array("data"=>$eta->html));
						} else {
							echo $this->template->go("auswerten_html.tpl",array("data"=>$eta->html));
						}
				} 
				//maske anzeigen
				else {
						$js1="onchange='lade_projekt_liste(this.value);'";
						$this->template->assign(	array(	"kunde_dd_liste_1"	=>	$this->kunden_dd_liste(false,false,false,true,'kunde'),
															"kunde_dd_liste_2"	=>	$this->kunden_dd_liste(false,$js1,false,false,'kunde'),
															"monate_dd_liste"	=>	$this->letzte_monate_dd(),
															"letzte_jahre_dd"	=>	$this->letzte_jahre_dd(),  		
															"user_dd_liste"		=> 	$this->user_dd_liste(),
															"js_loader"			=>	$js_loader,));
						echo $this->template->go("auswerten.tpl");
				}
		}
}
 
?>

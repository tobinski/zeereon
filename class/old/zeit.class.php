<?php

class etempus_zeit extends etempus {

		/**********************************************
		 submenu / tabs
		 "Beschriftung"=>"modul"
		***********************************************/
		
		public function __construct(){
				$this->submenu = array(	"{zeit_eintragen}" 	=> 	"",
										"{beleg}"			=>	"beleg",
										"{overhead}"		=>	"overhead" );
		}
		
		
		
		/**********************************************
		 einhängepunkt kunde
		***********************************************/

		public function build(){
				//modul auswählen
				switch ($this->getvar("modul")) {

					//startseite
					default:
						$this->start();
						break;

					//zeit eintragen
					case "eintragen":
						$this->eintragen();
						break;

					//eintrag löschen
					case "del":
						$this->del();
						break;
						
					//mehrere einträge löschen
					case "multidel":
						$this->multidel();
						break;
					
					//eintrag ändern
					case "eintrag_edit":
						$this->eintrag_edit();
						break;
					
					//beleg
					case "beleg":
						$this->beleg();
						break;
						
					//beleg_eintragen
					case "beleg_eintragen":
						$this->beleg_eintragen();
						break;
						
					//beleg löschen
					case "del_beleg":
						$this->del_beleg();
						break;
						
					//mehrere belege löschen
					case "beleg_multidel":
						$this->beleg_multidel();
						break;
						
					//beleg editieren
					case "beleg_edit":
						$this->beleg_edit();
						break;
						
					//overhead start
					case "overhead":
						$this->overhead();
						break;
						
					
				}
				return;
		}




		/**********************************************
		 startseite zeit
		***********************************************/

		private function start($message=false,$warning=false){

				$js1 = "onclick=\"lade_projekt_liste(this.value);\" id=\"kid\" ";
				$js2="";

				//wenn ausgewählter kunde/projekt js-loader erstellen, der die dd-boxen richtig setzt
				$kid=$this->getvar("kunden_id",true);
				$pid=$this->getvar("projekt_id",true);
				//wenn nur kunden-id angegeben
				if ($kid){
						//prüfen ob eine gültige id angegeben wurde
						if (!$this->check_id("kunden", $kid)){
						 	$this->fail("error_id_fails");
						 	return;
						}
						$js2="lade_projekt_liste({$kid});";
				//wenn projekt-id angegeben alles setzen
				} elseif ($pid){
						//prüfen ob eine gültige id angegeben wurde
						if (!$this->check_id("projekte", $pid)){
						 	$this->fail("error_id_fails");
						 	return;
						}
						$kid=$this->pid_to_kid($pid);
						$js2="lade_projekt_liste({$kid});lade_ansatz_liste({$pid});";
						//session für ajax.php
						$_SESSION['ajax_projekt_id']=$pid;
				} else {
						$js2="lade_projekt_liste($('kid').value);";
				}

				if ($message)
					$msg=$this->nachricht($message);
				if ($warning)
					$msg=$this->warnung($message);
				
				//kostendach prüfen
				if ($pid){
					if ($this->check_kostendach($pid)){
						$proj=$this->table_item_array("projekte",$pid);
						$txt=sprintf(__("warning_kostendach"),$proj['kostendach'],$this->projekt_name($pid),ET_KOSTENDACH_WARNUNG);
						$msg.=$this->warnung($txt);
					}
				}

				//letzte einträge zusammenstellen
				include("html/zeit_div.php");
				$eintrag_liste="";
				foreach ($this->user_time_list() as $eintrag){
					$cb="<input type='checkbox' name='cb_del[]' id='cb_{$eintrag['id']}' value='{$eintrag['id']}' onclick='void(0);'/>";
					$liste=new template;
					$liste->auto_assign($tpl['eintrag_liste'],true);
					$liste->assign( 	array(	"checkbox"			=> $cb,
												"itemid"			=> $eintrag['id'],
												"zeit_formatiert"	=> $eintrag['zeit_formatiert'],
												"projekt_name"		=> $eintrag['projekt'],
												"str_start"			=> $eintrag['str_start'],
												"str_ende"			=> $eintrag['str_ende'],
												"tdesc"				=> $eintrag['tdesc'],
												"kommentar"			=> $eintrag['beschreibung']	) );
												 
					$eintrag_liste.=$liste->parse($tpl['eintrag_liste'],true);
					unset($liste);
				}

				
				$user_id=$this->get_user_id();
				//letzte eingetragene zeit in formular setzen
				$res=$this->db->query("SELECT zeit_ende FROM zeit WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
				$larr=$res->fetchRow();
				$datum_start_form =	date("d.m.Y",$larr[0]);
				$zeit_start_form  = date("H:i:s",$larr[0]);

				$tpl=new template;
				$tpl->auto_assign("zeit_start.tpl");
				$tpl->assign( array(	"datum_form_start"=>$datum_start_form,
										"zeit_form_start"=>$zeit_start_form,
										"last_entrys"	=> 	$eintrag_liste,
										"num_entry"		=>	$_SESSION['conf_zeit_eintragnum'],
										"message"		=> 	$msg,
										"kundenliste"	=>	$this->kunden_dd_liste($kid,$js1),
										"js_loader"		=> 	$js2	) );

				$this->html=$tpl->parse("zeit_start.tpl");
		}


		/**********************************************
		 zeit eintragen
		***********************************************/

		private function eintragen(){
				$_REQUEST['modul']="";
				//prüfen ob eine gültige projekt-id & ansatz-id angegeben wurde
				if (!$this->check_id("projekte", $this->getvar("projekt",true)) || !$this->check_id("ansatz", $this->getvar("ansatz",true)) ){
						$this->fail("error_id_fails");
						return;
				}

				//timestamps berechnen
				$ts_start = strtotime($this->getvar("datum_start")." ".$this->getvar("zeit_start"));
				$ts_ende = strtotime($this->getvar("datum_ende")." ".$this->getvar("zeit_ende"));
				if (!$ts_start || !$ts_ende){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->start(__("error_timestamp"),true);
						return;
				}
				//prüfen ende grösser als start
				if ($ts_ende<$ts_start){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->start(__("error_time_weird"),true);
						return;
				}
				//prüfen ob bereits zeit eingetragen
				$user_id = $this->get_user_id();
				$res=$this->db->query("SELECT * FROM zeit WHERE user_id='$user_id' AND zeit_start>=$ts_start AND zeit_ende<=$ts_ende");
				if ($res->numRows()>0){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->start(__("error_zeit_existiert"),true);
						return;
				}

				$this->db->query("INSERT INTO zeit VALUES (	NULL,
														'".$this->getvar("projekt",true)."',
														'$user_id',
														'".$this->getvar("ansatz",true)."',
														'$ts_start',
														'$ts_ende',
														'".$this->getvar("kommentar",true)."'
													  );"
							);




				//damit bei start der loader & nachricht angezeigt wird
				$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
				$sekunden=$ts_ende-$ts_start;
				$msg= sprintf(__("zeit_eingetragen"),$this->formatiere_zeit($sekunden),$this->projekt_name($this->getvar("projekt",true)) );
				$this->start($msg);
				return;
		}
		
		
		/**********************************************
		 zeiteintrag ändern
		************************************************/
		
		private function eintrag_edit(){
				//prüfen ob eine gültige eintrag-id & projekt-id & ansatz-id angegeben wurde
				if (!$this->check_id("zeit",$this->getid()) || !$this->check_id("projekte", $this->getvar("projekt",true)) || !$this->check_id("ansatz", $this->getvar("ansatz",true)) ){
						$this->fail("error_id_fails");
						return;
				}
				//kommentar prüfen
				if (!$this->getvar("kommentar")){
						$this->start(__("zeit_comment_empty"),true);
						return;
				}

				//timestamps berechnen
				$ts_start = strtotime($this->getvar("datum_start")." ".$this->getvar("zeit_start"));
				$ts_ende = strtotime($this->getvar("datum_ende")." ".$this->getvar("zeit_ende"));
				if (!$ts_start || !$ts_ende){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->start(__("error_timestamp"),true);
						return;
				}
				
				//prüfen ende grösser als start
				if ($ts_ende<$ts_start){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->start(__("error_time_weird"),true);
						return;
				}
				$id = $this->getid();
				
				$query = "	UPDATE zeit SET	projekt_id	=	'".$this->getvar("projekt",true)."',
											ansatz_id	=	'".$this->getvar("ansatz",true)."',
											zeit_start	=	'$ts_start',
											zeit_ende	=	'$ts_ende',
											beschreibung=	'".$this->getvar("kommentar",true)."'		 	
							WHERE ID='$id';";
				$this->db->query($query);
				$_REQUEST['modul']="";
				$this->start(__("eintrag_edit_ok"));
				
		}
		
		
		/**********************************************
		 einträge löschen
		***********************************************/	
			
		//einzelner eintrag löschen
		private function del() {
				//prüfen ob eine gültige projekt-id & ansatz-id angegeben wurde
				if (!$this->check_id("zeit", $this->getvar("id",true)) ){
						$this->fail("error_id_fails");
						return;
				}
				$this->db->query("DELETE FROM zeit WHERE id=".$this->getid().";");
				$_REQUEST['modul']="";
				$this->start(__("eintrag_del"));
				return;
		}
		
		//mehrere einträge löschen
		private function multidel(){
				//wenn nicht bestätigt fragen
				if ($this->getvar("bestaetigt")!="true"){
					if (!$this->getvar("cb_del")){
						$this->start();
						return;
					}
					$_SESSION['items']=$this->getvar("cb_del");
					$tpl=new template;
					$tpl->auto_assign("zeit_del.tpl");
					$this->html=$tpl->parse("zeit_del.tpl");
				} 
				//sonst löschen
				else {	
					foreach ($_SESSION['items'] as $item){
						$i = $this->db->escapeSimple($item);
						$res=$this->db->query("DELETE FROM zeit WHERE id={$i};");
					}
					$_REQUEST['modul']="";
					$this->start(sprintf(__("eintrag_del_multi"),count($_SESSION['items'])));
				}
		}
		
		
		
		
		
		/**************************************************
		  
		  belege
		  
		***************************************************/
		
		private function beleg($message=false,$warning=false){
				
				$js1 = "onclick=\"lade_projekt_liste(this.value);\" id=\"kid\" ";
				$js2="lade_projekt_liste($('kid').value);\nlade_ansatz_liste(false)\n";
				
				//wenn ausgewählter kunde/projekt js-loader erstellen, der die dd-boxen richtig setzt
				$kid=$this->getvar("kunden_id",true);
				$pid=$this->getvar("projekt_id",true);
				//wenn nur kunden-id angegeben
				if ($kid){
						//prüfen ob eine gültige id angegeben wurde
						if (!$this->check_id("kunden", $kid)){
						 	$this->fail("error_id_fails");
						 	return;
						}
						$js2.="lade_projekt_liste({$kid});";
				//wenn projekt-id angegeben alles setzen
				} elseif ($pid){
						//prüfen ob eine gültige id angegeben wurde
						if (!$this->check_id("projekte", $pid)){
						 	$this->fail("error_id_fails");
						 	return;
						}
						$kid=$this->pid_to_kid($pid);
						$js2.="lade_projekt_liste({$kid});";
						//session für ajax.php
						$_SESSION['ajax_projekt_id']=$pid;
				}
				
				include("html/zeit_div.php");
				$eintrag_liste="";
				foreach ($this->user_beleg_list() as $eintrag){
					$cb="<input type='checkbox' name='cb_del[]' id='cb_{$eintrag['id']}' value='{$eintrag['id']}' onclick='void(0);'/>";
					$liste=new template;
					$liste->auto_assign($tpl['beleg_liste'],true);
					$liste->assign( 	array(	"checkbox"			=> $cb,
												"itemid"			=> $eintrag['id'],
												"projekt_name"		=> $eintrag['projekt'],
												"betrag"			=> $eintrag['betrag'],
												"tdesc"				=> $eintrag['tdesc'],
												"kommentar"			=> $eintrag['beschreibung']	) );
												 
					$eintrag_liste.=$liste->parse($tpl['beleg_liste'],true);
					unset($liste);
				}
				
				if ($message)
					$msg=$this->nachricht($message);
				if ($warning)
					$msg=$this->warnung($message);
				
				//kostendach
				if ($pid){
					if ($this->check_kostendach($pid)){
						//$proj=$this->table_item_array("projekte",$pid);
						//$txt=sprintf(__("warning_kostendach"),$proj['kostendach'],$this->projekt_name($pid),ET_KOSTENDACH_WARNUNG);
						//$msg.=$this->warnung($txt);
					}
				}
											
				$tpl=new template;
				$tpl->auto_assign("zeit_beleg.tpl");
				$tpl->assign( array(	"last_entrys"	=> 	$eintrag_liste,
										"num_entry"		=>	$_SESSION['conf_beleg_eintragnum'],
										"today"			=>	date("d.m.Y"),
										"message"		=> 	$msg,
										"kundenliste"	=>	$this->kunden_dd_liste($kid,$js1),
										"js_loader"		=> 	$js2,
										"msg"			=>	$msg	) );
										
				$this->html=$tpl->parse("zeit_beleg.tpl");
		}
		
		
		/************************************
		 beleg eintragen
		*************************************/
		
		private function beleg_eintragen(){
				
				$_REQUEST['modul']="beleg";
				$_REQUEST['kunden_id']=$this->getvar("kunde");
				$_REQUEST['projekt_id']=$this->getvar("projekt");
				//projekt-id prüfen
				if ( !$this->check_id("projekte", $this->getvar("projekt",true)) ){
						$this->fail("error_id_fails");
						return;
				}
				//wenn betrag nicht angegeben
				if (!is_numeric($this->getvar("betrag"))){
						$this->beleg(__("beleg_nur_ganzzahl"),true);
						return;
				}
				//timestamp ausrechnen
				$ts= strtotime($this->getvar("datum_start")." 12:00:00");
				if (!$ts){
						$this->beleg(__("error_timestamp"),true);
						return;
				}
				//ab hier ok->in datenbank eintragen
				$user_id = $this->get_user_id();
				$query="INSERT INTO beleg VALUES (	NULL, 
													'".$this->getvar('projekt',true)."',
													'$user_id',
													'$ts',
													'".$this->getvar('kommentar',true)."',
													'".$this->getvar('betrag',true)."'
												  )";
				$this->db->query($query);
				$msg=sprintf(__("zeit_beleg_eintragen"),$this->getvar('betrag',true),$this->projekt_name($this->getvar("projekt",true)) );
				$this->beleg($msg);
				return;
		}
		
		/**********************************************
		  belege löschen
		***********************************************/	
			
		//einzelner eintrag löschen
		private function del_beleg() {
				//prüfen ob eine gültige projekt-id & ansatz-id angegeben wurde
				if (!$this->check_id("beleg", $this->getvar("id",true)) ){
						$this->fail("error_id_fails");
						return;
				}
				$this->db->query("DELETE FROM beleg WHERE id=".$this->getid()." ;");
				$_REQUEST['modul']="beleg";
				$this->beleg(__("eintrag_del"));
				return;
		}
		
		//mehrere einträge löschen
		private function beleg_multidel(){
				$_REQUEST['modul']="beleg";
				//wenn nicht bestätigt fragen
				if ($this->getvar("bestaetigt")!="true"){
					if (!$this->getvar("cb_del")){
						$this->beleg();
						return;
					}
					$_SESSION['items']=$this->getvar("cb_del");
					$tpl=new template;
					$tpl->auto_assign("zeit_beleg_del.tpl");
					$this->html=$tpl->parse("zeit_beleg_del.tpl");
				} 
				//sonst löschen
				else {	
					foreach ($_SESSION['items'] as $item){
						$i = $this->db->escapeSimple($item);
						$this->db->query("DELETE FROM beleg WHERE id={$i};");
					}
					$this->beleg(sprintf(__("eintrag_del_multi"),count($_SESSION['items'])));
				}
		}
		
		/*************************************
		 beleg ändern
		*************************************/
		private function beleg_edit(){
				$_REQUEST['modul']="beleg";
				//prüfen ob eine gültige eintrag-id & projekt-id & ansatz-id angegeben wurde
				if (!$this->check_id("beleg",$this->getid()) || !$this->check_id("projekte", $this->getvar("projekt",true)) ){
						$this->fail("error_id_fails");
						return;
				}
				//kommentar prüfen
				if (!$this->getvar("kommentar")){
						$this->beleg(__("zeit_comment_empty"),true);
						return;
				}
				//wenn betrag nicht angegeben
				if (!is_numeric($this->getvar("betrag"))){
						$this->beleg(__("beleg_nur_ganzzahl"),true);
						return;
				}
				//timestamps berechnen
				$ts = strtotime($this->getvar("datum_start")." ".$this->getvar("zeit_start"));
				if (!$ts){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->beleg(__("error_timestamp"),true);
						return;
				}
				$id = $this->getid();
				$query = "	UPDATE beleg SET projekt_id		=	'".$this->getvar("projekt",true)."',
											 zeit			=	'$ts',
											 beschreibung	=	'".$this->getvar("kommentar",true)."',
											 betrag			=	'".$this->getvar("betrag",true)."'		 	
							WHERE ID='$id';";
				$this->db->query($query);
				$this->beleg(__("eintrag_edit_ok"));
		}
		
		
		/**********************************************
		 startseite overhead
		***********************************************/
		public function overhead($message=false,$warning=false,$extra_msg=""){
				
				if ($this->getvar("eintragen")=="true"){
					$this->overhead_eintragen();
					return;
				}
				
				//startzeit setzen
				$user_id=$this->get_user_id();
				$res=$this->db->query("SELECT zeit_ende FROM zeit WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1;");
				$larr=$res->fetchRow();
				$datum_start_form =	date("d.m.Y",$larr[0]);
				$zeit_start_form  = date("H:i:s",$larr[0]);
				
				if ($message)
					$msg=$this->nachricht($message);
				if ($warning)
					$msg=$this->warnung($message);
				$msg.=$extra_msg;
				
				$tpl=new template;
				$tpl->assign( array(	"db_ansatz_dd"			=>  $this->ansatz_dd_liste(false,false,true),
										"db_kunde_overdead_dd"	=>	$this->kunden_dd_liste(false,false,true),
										"datum_form_start"		=>	$datum_start_form,
										"zeit_form_start"		=>	$zeit_start_form,
										"msg"					=>	$msg									
										) );
				$tpl->auto_assign("zeit_overhead.tpl");
				$this->html=$tpl->parse("zeit_overhead.tpl");
		}
		
		/**************************
		 overhead eintragen
		**************************/
		private function overhead_eintragen(){
				$_REQUEST['eintragen']=false;
				
				//prüfen ob eine gültige projekt-id & ansatz-id angegeben wurde
				if (!$this->check_id("kunden", $this->getvar("kunde",true)) || !$this->check_id("ansatz", $this->getvar("ansatz",true)) ){
						$this->fail("error_id_fails");
						return;
				}
				//timestamps berechnen
				$ts_start = strtotime($this->getvar("datum_start")." ".$this->getvar("zeit_start"));
				$ts_ende = strtotime($this->getvar("datum_ende")." ".$this->getvar("zeit_ende"));
				if (!$ts_start || !$ts_ende){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->overhead(__("error_timestamp"),true);
						return;
				}
				//prüfen ende grösser als start
				if ($ts_ende<$ts_start){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->overhead(__("error_time_weird"),true);
						return;
				}
				//prüfen ob bereits zeit eingetragen
				$user_id = $this->get_user_id();
				$res=$this->db->query("SELECT * FROM zeit WHERE user_id='$user_id' AND zeit_start>=$ts_start AND zeit_ende<=$ts_ende");
				if ($res->numRows()>0){
						$_REQUEST['projekt_id'] = $this->getvar("projekt",true);
						$this->overhead(__("error_zeit_existiert"),true);
						return;
				}
				$sekunden=$ts_ende-$ts_start;
				
				//alle projekte durchlaufen und buchen
				$res = mysql_query("SELECT id,overhead FROM projekte WHERE kunden_id =".$this->getvar("kunde",true).";");
				if ($res){
						//prüfen ob anzahl projekte grösser als 0
						if ($res->numRows()>0){
								$num=$res->numRows();
								$anteil = round($sekunden/$num);
								$i=$prozent=0;
								$arr=array();
								$startzeit=$ts_start;
								$extra_msg="";
								while ($projekt = $res->fetchRow(DB_FETCHMODE_ASSOC)){
										$arr[$i]['pid']=$projekt['id'];
										$arr[$i]['ov']=$projekt['overhead']; 
										$arr[$i]['anteil']=round(($sekunden/100)*$projekt['overhead']);
										$arr[$i]['zeit_start']=$startzeit;
										$arr[$i]['zeit_ende']=$startzeit=$startzeit+$arr[$i]['anteil'];
										$this->db->query("INSERT INTO zeit VALUES (	NULL,
																				'{$projekt['id']}',
																				'$user_id',
																				'".$this->getvar("ansatz",true)."',
																				'{$arr[$i]['zeit_start']}',
																				'{$arr[$i]['zeit_ende']}',
																				'".$this->getvar("kommentar",true)."'
																			  );");

										$prozent=$prozent+$projekt['overhead'];
										
										//kostendach prüfen
										if ($this->check_kostendach($projekt['id'])){
											$proj=$this->table_item_array("projekte",$projekt['id']);
											$txt=sprintf(__("warning_kostendach"),$proj['kostendach'],$this->projekt_name($projekt['id']),ET_KOSTENDACH_WARNUNG);
											$extra_msg.=$this->warnung($txt);
										}
										$i++;
								}
								//nachricht formatieren
								$msg_list = "";
								foreach ($arr as $item){
										$msg_list.= sprintf(__("overhead_ok_list"),
															$this->formatiere_zeit($item['anteil']),
															$this->projekt_name($item['pid']));
															
								}
								$message = sprintf(__("overhead_ok"),$num,$msg_list,$this->formatiere_zeit($sekunden),$prozent);
								$this->overhead($message,false,$extra_msg);
						
				//wenn projekte =0 fehlermeldung ausgeben		
						} else {
								$this->overhead(__("overhead_error_empty"),true);
						}
				} else {
					$this->overhead(__("overhead_error_empty"),true);
				}
				return;
		}
		

}

?>

<?php

class etempus_kunde extends etempus {
		
		/**********************************************
		 submenu / tabs
		 "Beschriftung"=>"modul"
		***********************************************/
		
		public function __construct(){
				$this->submenu = array(	"{start}" 			=> 	"",
										"{kunde_neu}"		=>	"neu" );
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
						
					//neuer kunde
					case "neu":
						$this->neu();
						break;
					
					//details einzelner kunde	
					case "detail":
						$this->detail();
						break;
						
					//kundendaten editieren
					case "edit":
						$this->edit();
						break;
				}
				
		}
		
		
		
		
		/**********************************************
		 startseite kunden
		***********************************************/
		
		private function start(){
				$this->db=db_make();
				//kundenliste zusammenstellen
				include ("html/kunde_div.php");
				
				foreach ($this->table_array("kunden") as $row){
						$item=new template;
						
						$item->assign(	array(	"name"	=> $row['name'],
												"id"	=> $row['id'],
												"detail"=> __('button_detail')		));
						$list.=$item->parse($tpl['kunde_liste'],true);
						unset($item);
				}
				
				$table=new template;
				$table->assign(array("items"=>$list));
				$table->auto_assign($tpl['kunde_liste_table'],true);
				$list = $table->parse($tpl['kunde_liste_table'],true);
				
				//seite zusammenstellen
				$tpl=new template;
				$tpl->assign(	array( 	"titel"	=> __("kunde") ,
										"neu"	=> __("kunde_neu"),
										"list"	=> $list			));
				$this->html=$tpl->parse("kunde_start.tpl");
							

		}
		
		
		
		/**********************************************
		  neuer kunde anlegen
		***********************************************/
		
		private function neu($fail=false){
				$this->db=db_make();
				//wenn keine daten angekommen sind eingabemaske anzeigen
				if ($this->getvar("step")!="step2"){
						$tpl=new template;
						$tpl->assign(	array( 	"titel"		=> __("kunde_neu") ,
												"name"		=> __("kunde_name"),
												"adresse"	=> __("kunde_adresse"),
												"plz"		=> __("kunde_plz"),
												"ort"		=> __("kunde_ort"),
												"ansprech"	=> __("kunde_ansprech"),
												"tel"		=> __("kunde_telefon"),
												"mail"		=> __("kunde_email"),
												"erstellen"	=> __("button_erstellen"),
												"fail"		=> $fail	));
						$html=$tpl->parse("kunde_neu.tpl");
				} 
				
				//sonst eingaben prüfen und in datenbank schreiben
				else {
						if ($this->getvar("name")) {
								
								$this->db->query( "INSERT INTO kunden VALUES (	NULL,	
																		'".$this->getvar("name",true)."',
																		'".$this->getvar("adresse",true)."',
																		'".$this->getvar("plz",true)."',
																		'".$this->getvar("ort",true)."',
																		'".$this->getvar("ansprech",true)."',
																		'".$this->getvar("tel",true)."',
																		'".$this->getvar("mail",true)."',
																		'0');");
								$this->start();
								return;
								
						} else {
								$_REQUEST["step"]="1";
								$this->neu($this->warnung(__("error_noname")));
								return;
						}
				}
							
				
				$this->html= $html;
		}
		
		
		
		
		/**********************************************
		 detailseite kunden
		***********************************************/
		
		private function detail(){
				$this->db=db_make();
				//prüfen ob eine gültige id angegeben wurde
				if (!$this->check_id("kunden", $this->getid())){
				 	$this->fail("error_id_fails");
				 	return;
				}
				//overhead speichern
				if ($this->getvar("overhead_change")=="true"){
					$val = ($this->getvar("overhead")=="on") ? "1":"0";
					$this->db->query("UPDATE kunden SET overhead=$val WHERE id=".$this->getid()." ;");
				}
				
				//projektliste erstellen
				include ("html/kunde_div.php");
				foreach ($this->kunden_projekte($this->getid()) as $row){
						$item=new template;
						$item->assign(	array(	"name"	=> $row['name'],
												"id"	=> $row['id'],
												"detail"=> __('button_detail'),	
												"kunde" => $this->kunde_name($row['kunden_id']),
												"del"	=> __("button_del") 	));
						$list.=$item->parse($tpl['projekt_liste'],true);
						unset($item);
				}		
				$table=new template;
				$table->assign(array("items"=>$list));
				$list = $table->parse($tpl['projekt_liste_table'],true);
				
				
				
				//seite assemblieren
				$tbl=$this->table_item_array("kunden",$this->getid());
				$db_checked = ($tbl['overhead']=="1") ? "checked" : "";
				
				$tpl=new template;
				$tpl->assign($tbl);
				$tpl->assign(	array(	"neues_projekt"		=> __("projekt_neu"),
										"auswertung"		=> __("kunde_auswertung"),
										"titel"				=> __("kunde_detail"),
										"ansprech"			=> __("kunde_ansprech"),
										"tel"				=> __("kunde_telefon"),
										"mail"				=> __("kunde_email"),
										"activity"			=> __("kunde_activ"),
										"edit"				=> __("kunde_edit"),
										"projekte"			=> __("projekt"),
										"projekt_liste"		=> $list,
										"addr"				=>__("kunde_adresse"),
										"db_checked"		=>$db_checked	));
				$tpl->auto_assign("kunde_details.tpl");
				$this->html=$tpl->parse("kunde_details.tpl");
				
		}
		
		
		
		
		
		/**********************************************
		 kundendaten editieren
		***********************************************/		
		
		private function edit($fail=""){
				$this->db=db_make();
				//prüfen ob eine gültige id angegeben wurde
				if (!$this->check_id("kunden", $this->getid())){
					 $this->fail("error_id_fails");
					 return;
				}
				
				//wenn keine daten angekommen sind eingabemaske anzeigen
				if ($this->getvar("step")!="2"){
						$_SESSION['edit_id']=$this->getid();
						$tpl=new template;
						$tpl->assign( array( 	"titel"		=> __("kunde_edit") ,
												"name0"		=> __("kunde_name"),
												"adresse0"	=> __("kunde_adresse"),
												"plz0"		=> __("kunde_plz"),
												"ort0"		=> __("kunde_ort"),
												"ansprech"	=> __("kunde_ansprech"),
												"tel"		=> __("kunde_telefon"),
												"mail"		=> __("kunde_email"),
												"edit"		=> __("button_edit"),
												"fail"		=> $fail				) );
												
						$tpl->assign($this->table_item_array("kunden",$this->getid()));
						
						$tpl->assign(array( "fail"=>$fail));
						$this->html=$tpl->parse("kunde_edit.tpl");
				} 
				
				
				//sonst daten einfüllen und zurück zum detail
				else {
						//prüfen ob edit-id gesetzt ist und mit id aus form übereinstimmt
						if ($_SESSION['edit_id']!=$this->getid()){
						 	$this->fail("error_session_chk");
						 	return;
						}
						
						//einzelne felder prüfen und updaten
						if ($this->getvar("name"))
							$this->update_db_field("kunden",$_SESSION['edit_id'],"name",$this->getvar("name",true));
						if ($this->getvar("adresse"))
							$this->update_db_field("kunden",$_SESSION['edit_id'],"adresse",$this->getvar("adresse",true));
						if ($this->getvar("plz"))
							$this->update_db_field("kunden",$_SESSION['edit_id'],"plz",$this->getvar("plz",true));
						if ($this->getvar("ort"))
							$this->update_db_field("kunden",$_SESSION['edit_id'],"ort",$this->getvar("ort",true));
						if ($this->getvar("ansprech"))
							$this->update_db_field("kunden",$_SESSION['edit_id'],"ansprechpartner",$this->getvar("ansprech",true));
						if ($this->getvar("tel"))
							$this->update_db_field("kunden",$_SESSION['edit_id'],"telefon",$this->getvar("tel",true));
						if ($this->getvar("mail"))
							$this->update_db_field("kunden",$_SESSION['edit_id'],"email",$this->getvar("mail",true));
						
						//session leeren
						$_SESSION['edit_id']=false;
						
						//zurück zu den details
						$this->detail();
						
				}
		}
		
		
}





		/**********************************************
		
		***********************************************/

?>

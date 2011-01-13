<?php

class etempus_projekt extends etempus {

		/**********************************************
		 submenu / tabs
		 "Beschriftung"=>"modul"
		***********************************************/
		
		public function __construct(){
				$this->submenu = array(	"{start}" 			=> 	"",
										"{projekt_neu}"		=>	"neu" );
		}

		/**********************************************
		 einhängepunkt projekte
		***********************************************/

		public function build(){
				
				//modul auswählen
				switch ($this->getvar("modul")) {

					//startseite
					default:
						$this->start();
						break;

					//neues projekt anlegen
					case "neu":
						$this->neu();
						break;

					//projektdetails
					case "detail":
						$this->detail();
						break;

					//projekt löschen
					case "del":
						$this->del();
						break;

					//einstellungen
					case "edit":
						$this->edit();
						break;

				}
				return;

		}




		/**********************************************
		 startseite projekte
		***********************************************/

		private function start(){
				$this->get_user_id();

				//sortierung
				if ($this->getvar("orderby")=="name" || $this->getvar("orderby")=="kunden_id"){
					$orderby=$this->getvar("orderby");
					if ($_SESSION["orderby"]==$this->getvar("orderby")){
						$_SESSION["orderby"]=$this->getvar("orderby")."DESC";
						$desc=true;
					} else {
						$desc=false;
						$_SESSION["orderby"]=$this->getvar("orderby");
					}
				} else {
					$orderby=false;
					$desc=false;
				}


				//projektliste zusammenstellen
				include ("html/projekt_div.php");
				foreach ($this->table_array("projekte",$orderby,$desc) as $row){
						$item=new template;
						$item->assign(	array(	"name"	=> $row['name'],
												"id"	=> $row['id'],
												"detail"=> __('button_detail'),
												"zeit_eintragen"=>__("zeit_eintragen"),
												"kunde" => $this->kunde_name($row['kunden_id']),
												"kunden_id"=>$row['kunden_id'],
												"del"	=> __("button_del") 	));
						$list.=$item->parse($tpl['projekt_liste'],true);
						unset($item);
				}
				$table=new template;
				$table->assign(array("items"=>$list));
				$list = $table->parse($tpl['projekt_liste_table'],true);

				//rest der seite
				$tpl=new template;
				$tpl->assign(array(	"liste"			=> $list ,
									"titel"			=> __("projekt"),
									"neues_projekt"	=> __("projekt_neu"),
									"name"			=> __("projekt_s"),
									"kunde"			=> __("kunde_s"),
									"sortierung"	=> __("sortierung")			));
				$this->html=$tpl->parse("projekt_start.tpl");
		}






		/**********************************************
		details projekte
		***********************************************/

		private function detail(){

				//prüfen ob eine gültige id angegeben wurde
				if (!$this->check_id("projekte", $this->getid())){
						$this->start();
						return;
				}
				$zeit=$this->projekt_zeit($this->getid());


				$tpl=new template;
				$tpl->auto_assign("projekt_detail.tpl");
				$tpl->assign_id();
				$tpl->assign(array( "projekt_name"	=>	$this->projekt_name($this->getid()),
									"gesamtzeit"	=>	$this->formatiere_zeit($zeit) ));
				$this->html=$tpl->parse("projekt_detail.tpl");

		}





		/**********************************************
		 neues projekt anlegen
		***********************************************/
		private function neu($msg=false){
				//wenn keine daten angekommen sind eingabemaske anzeigen
				if ($this->getvar("step")!="2"){
						$tpl=new template;
						$tpl->assign( array("titel"			=> __("projekt_neu"),
											"nachricht"		=> $msg,
											"kundenliste"	=> $this->kunden_dd_liste($this->getvar("kunde")),
											"kunde"			=>__("kunde_s"),
											"name"			=>__("kunde_name"),
											"neu"			=>__("button_erstellen")			));

						$this->html=$tpl->parse("projekt_neu.tpl") ;
						//var_dump($msg);
						return;
				}

				//sonst daten prüfen und einfüllen
				else {
						//prüfen ob eine gültige kunden-id angegeben wurde
						if (!$this->check_id("kunden", $this->getvar("kunde",true))){
							 	$this->start();
								return;
						}

						//prüfen ob alle felder ausgefüllt wurden
						if (!$this->getvar("projekt_name") || !$this->getvar("kunde") ){
								$_REQUEST['step'] = "1";
								$this->neu($this->warnung(__("error_empty")));
								return;
						}


						$neu = $this->db->query("INSERT INTO projekte VALUES (	NULL,
																			".$this->getvar("kunde",true).",
																			'".$this->getvar("projekt_name",true)."',
																			'*',
																			'*',
																			'0',
																			'0'
																		);");
						//zu den projekt details
						$_REQUEST['id'] = $this->db->nextId('projekt')-1;
						$this->edit();
						return;
				}


		}




		/**********************************************
		 projekt löschen
		***********************************************/

		private function del(){

				//prüfen ob eine gültige id angegeben wurde
				if (!$this->check_id("projekte", $this->getid())){
						$this->fail("error_id_fails");
						return;
				}


				//prüfen ob schon bestätigt, sonst bestätigen
				if ($this->getvar("step")!="2"){
						$_SESSION['del_id'] = $this->getid();
						$tpl=new template;
						$tpl->assign( array(	"ja"		=> __("ja"),
												"nein"		=> __("nein"),
												"titel"		=> __("confirm_del"),
												"location"	=> "?",
												"id"		=> $this->getid(),
												"nachricht"	=> sprintf(__("projekt_del_confirm"),$this->projekt_name($this->getid()))

												) );
						$this->html=$tpl->parse("frage_proj.tpl");
				}

				//sonst sid prüfen und wenn gut löschen
				else {
						//prüfen ob id in der session die selbe wie in der form ist
						if ($_SESSION['del_id']!=$this->getid()){
						 	$this->fail("error_session_chk");
						 	return;
						}

						//löschen
						$this->db->query("DELETE FROM projekte WHERE id={$_SESSION['del_id']} ;");
						$this->db->query("DELETE FROM zeit WHERE projekt_id={$_SESSION['del_id']};");
						$_SESSION['del_id']=false;

						//zurück zum start
						$this->start();
						return;

				}


		}


		/**********************************************
			einstellungen
		***********************************************/

		private function edit(){
				$msg=false;
				
				//id prüfen
				if (!$this->check_id("projekte", $this->getid())){
						$this->start();
						return;
				}

				//wenn daten angekommen sind, diese verarbeiten //hier kein session-chk
				if ($this->getvar("step")=="2"){


						//wenn ansätze ausgewählt
						if ($this->getvar("ansatz")=="spec" and is_array($this->getvar("ansatz_liste")) ){
							$ansatz="";
							foreach ($this->getvar("ansatz_liste") as $item){
								$ansatz.="{$item},";
							}
							$ansatz=substr($ansatz,0,-1);
						} else {
							$ansatz="*";
						}
						$this->update_db_field("projekte",$this->getid(),"ansatz",$ansatz);

						//wenn benutzer ausgewählt
						if ($this->getvar("user")=="spec" and is_array($this->getvar("user_liste")) ){
							$user="1";
							foreach ($this->getvar("user_liste") as $item){
								$user.=",{$item}";
							}
							//$user=substr($user,0,-1);
						} else {
							$user="*";
						}
						$this->update_db_field("projekte",$this->getid(),"user",$user);


						//wenn namensfeld nicht leer ist
						if ($this->getvar("name")){
								$this->update_db_field("projekte",$this->getid(),"name",$this->getvar("name",true));
						}
						
						//wenn kostendach angegeben ist
						if (is_numeric($this->getvar("kostendach",true))){
							$this->update_db_field("projekte",$this->getid(),"kostendach",$this->getvar("kostendach",true));
						}
						
						//wenn overhead angegeben ist
						if (is_numeric($this->getvar("overhead",true))){
							$this->update_db_field("projekte",$this->getid(),"overhead",$this->getvar("overhead",true));
						}

						$msg=$this->nachricht(__("prefs_saved"));
						
				}


				//ansätze und mitarbeiter zusammenstellen,
				//sorry für html, aber sonst währe es viiiel zu stressig für nichts
				$item = $this->table_item_array("projekte",$this->getid());
				$ansatzliste = "";

				foreach ($this->table_array("ansatz","name",false,"aktiv","1") as $ansatz ){
						$res = preg_match("/{$ansatz['id']}/",$item['ansatz']);
						if ($res)
							$chk=" checked ";
						else
							$chk=" ";
						$ansatzliste.= "<input type='checkbox'{$chk}name='ansatz_liste[]' value='{$ansatz['id']}'> {$ansatz["name"]}<br />";
						//print_r($ansatz,true);
				}

				$userliste = "";
				foreach ($this->table_array("user") as $user ){
						$res = preg_match("/{$user['id']}/",$item['user']);
						if ($res)
							$chk=" checked ";
						else
							$chk=" ";
						if ($user['id']==1)
							$chk=" checked disabled ";
						$userliste.= "<input type='checkbox'{$chk}name='user_liste[]' value='{$user['id']}'> {$user["name"]}<br />";
						//print_r($ansatz,true);
				}



				//checkboxen richtig setzen
				if ($item['ansatz']=="*"){
					$ansatz_all = " checked";$ansatz_single="";
				} else {
					$ansatz_all = "";$ansatz_single=" checked";
				}
				if ($item['user']=="*"){
					$user_all = " checked";$user_single="";
				} else {
					$user_all = "";$user_single=" checked";
				}

				//overhead-box erstellen wenn nötig
				$overhead_box="";
				if ($this->is_overhead($this->pid_to_kid($this->getid()))){
					include ("html/projekt_div.php");
					$ov = new template;
					$ov->auto_assign($tpl['projekt_edit_overhead'],true);
					$ov->assign(array("db_overhead"=>$this->get_overhead($this->getid())));
					$ov->assign_id();
					$overhead_box=$ov->parse($tpl['projekt_edit_overhead'],true);
				}
				
				$tpl=new template;
				$tpl->auto_assign("projekt_edit.tpl");
				$tpl->assign_id();
				$tpl->assign( array("msg"=>$msg,
									"db_overhead"=>$overhead_box,
									"db_kostendach"=>$this->get_kostendach($this->getid()),
									"proj_name"=>$this->projekt_name($this->getid()),
									"ansatz_liste"=>$ansatzliste,
									"mitarbeiter_liste"=>$userliste,
									"ansatz_all"=>$ansatz_all,
									"ansatz_single"=>$ansatz_single,
									"user_all"=>$user_all,
									"user_single"=>$user_single ) );
				$this->html=$tpl->parse("projekt_edit.tpl");
		}



}





		/**********************************************

		***********************************************/

?>

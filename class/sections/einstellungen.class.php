<?php

class etempus_einstellungen extends etempus {

		public $layout=true;
		
		/**********************************************
		 submenu / tabs
		 "Beschriftung"=>"modul"
		***********************************************/		
		public function __construct(){
				$this->uid=($this->get_user_id());
		}


		/**********************************************
		 einhängepunkt auswertungen
		***********************************************/
		public function build(){
				//modul auswählen
				switch ($this->getvar("modul")) {

					//passwort & sprache 
					default:
						$this->start();
						break;
					
					//ansätze
					case "ansatz":
						$this->ansatz();
						break;
					//ansatz löschen
					case "ansatz_del":
						$this->ansatz_del();
						break;
					//ansatz aktivieren/deaktivieren
					case "ansatz_toggle":
						$this->ansatz_toggle();
						break;
						
					//benutzerverwaltung
					case "user":
						$this->user_start();
						break;
					//neuer user
					case "user_new":
						$this->user_new();
						break;
					//benutzer löschen
					case "user_del":
						$this->user_del();
						break;
					//benutzer-details:
					case "user_detail":
						$this->user_detail();
						break;

				}
				return;
		}
		
		
		
		
		/**************************************
		  passwort & sprache
		**************************************/
		
		function index() { $this->start(); }
		
		
		public function start($message=false,$fail=false){
				
				//sprache speichern
				if ($this->getvar("lang",true)){
						$lang = $this->getvar("lang",true);
						$user_id = $this->get_user_id();
						$this->update_db_field("user",$user_id,"lang",$lang);
						//sprachstrings neu laden (noch ein wenig unsauber)
						unset($GLOBALS['txt']);
						$_SESSION['lang']=$lang;
						include("text/text.{$lang}.php");
						$GLOBALS['txt'] = $txt;
				}
				
				//passwort speichern
				if ($this->getvar("oldpw") and $this->getvar("newpw") and $this->getvar("newpw2")){
						//prüfen ob altes passwort korrekt
						$ht = $this->load_ht_file();
						$user = $this->user_login($this->get_user_id());
						$oldpw_crypt = crypt($this->getvar("oldpw"));
						$salt = substr($this->getvar("oldpw"), 0, 8);
						$encrypted = crypt( $salt,  $ht[$user] );
						//altes passwort nicht korrekt eingegeben
						if ($ht[$user] != $encrypted){
								$_REQUEST['oldpw']=false;
								$this->start(__("pw_not_ok"),true);
								return;
						}
						//neue passwörter stimmen nicht überein
						if ($this->getvar("newpw") != $this->getvar("newpw2")){
								$_REQUEST['oldpw']=false;
								$this->start(__("pw_not_match"),true);
								return;
						}	
						//ab hier speichern
						$_REQUEST['oldpw']=false;
						$ht[$user] = crypt($this->getvar("newpw"));
						$this->save_ht_file($ht);
						$this->start(__("pw_changed"));
						return;
				}	
				
				//fehlerboxen
				$msg="";
				if ($message)
					$msg=$this->nachricht($message);
				if ($fail)
					$msg=$this->warnung($message);
					
				//sprache felder laden
				$lang = new etempus_lang_loader;
				$langlist = $this->make_ddlist($lang->lang,"lang",$_SESSION['lang']);
				
				//seite ausgeben
				$tpl_arr = array( "dd_lang"=>$langlist,
								  "msg"=>$msg );
				$tpl=new template;
				echo $tpl->go("config_start.tpl",$tpl_arr);	
		}
		
		
		
		
		
		
		/******************************************
		
		 ansätze
		
		*******************************************/
		private function ansatz($message=false,$fail=false){
				$_REQUEST=$this->utf8_array_decode($_REQUEST);
				
				//nur für admin erlaubt
				if ($this->uid!="1"){
					$_REQUEST['modul']="";
					$this->start(__("not_allowed"),true);
					return;
				}
				
				//ansatz erstellen
				if ($this->getvar("ansatz_name") and $this->getvar("ansatz_wert")){
						//wenn ansatz keine zahl
						if (!is_numeric($this->getvar("ansatz_wert"))){
								$message=__("beleg_nur_ganzzahl");
								$fail=true;
						} else {
								$q="INSERT INTO ansatz VALUES (NULL,
																		'".$this->getvar("ansatz_name",true)."',
																		'".$this->getvar("ansatz_wert",true)."',
																		'1');";
								$this->db->query($q);
								$message=__("config_ansatz_ok");
								$fail=false;
						}
				} 
				//ansatz ändern
				elseif ($this->getvar("edit")=="true") {
						if (!$this->getvar("edit_wert") || !$this->getvar("edit_name") ){
								$_REQUEST['edit']=false;
								$this->ansatz(__("error_empty"),true);
								return;
						}
						if (!is_numeric($this->getvar("edit_wert"))){
								$_REQUEST['edit']=false;
								$this->ansatz(__("beleg_nur_ganzzahl"),true);
								return;
						}
						if ($this->check_id("ansatz",$this->getid())){
								$this->db->query("UPDATE ansatz 	SET 	name	='".$this->getvar("edit_name",true)."', 
																	wert	='".$this->getvar("edit_wert",true)."' 
													   		WHERE 	id		='".$this->getid()."';");
								$message=__("config_ansatz_editok");
								$fail=false;
						}
				}
				
				
				//ansatzliste zusammenstellen
				$trc='dark';
				include ("html/config_ansatz_div.php");
				foreach ($this->table_array("ansatz") as $row){
						$row=$this->utf8_array_encode($row);
						$aktiv = (!$row['aktiv']) ? __("button_aktivieren") : __("button_deaktivieren");
						$aktiv_link = "<a href='?section=einstellungen&function=build&modul=ansatz_toggle&id={$row['id']}'>$aktiv</a>";
						$trc = ($trc=='light') ? 'dark' : 'light';
						$item=new template;
						$item->assign(	array(	"trc"=>$trc,
												"db_aktiv"	=> $aktiv_link,
												"db_name"	=> $row['name'],
												"id"		=> $row['id'],
												"db_wert"	=> $row['wert'], ));
						$item->auto_assign($tpl['ansatz_liste'],true);
						$list.=$item->parse($tpl['ansatz_liste'],true);
						unset($item);
				}
				$table=new template;
				$table->assign(array("items"=>$list));
				$table->auto_assign($tpl['ansatz_liste_table'],true);
				$list = $table->parse($tpl['ansatz_liste_table'],true);
				
				//fehlerboxen
				$msg="";
				if ($message)
					$msg=$this->nachricht($message);
				if ($fail)
					$msg=$this->warnung($message);
					
				$tpl=new template;
				$tpl->auto_assign("config_ansatz.tpl");
				$tpl->assign( array (	"ansatz_liste"	=>$list,
										"msg"			=> $msg,));
				echo $tpl->parse("config_ansatz.tpl");
		}
		
		/*************************************
		 ansatz aktivieren / deaktivieren
		**************************************/
		private function ansatz_toggle(){
				
				//id prüfen
				if (!$this->check_id("ansatz", $this->getid()) ){
						$this->fail("error_id_fails");
						return;
				}
				$ans = $this->table_item_array("ansatz",$this->getid());
				if ($ans['aktiv'])
					$val="0";
				else 
					$val="1";
				$this->db->query("UPDATE ansatz SET aktiv={$val} WHERE id='".$this->getid()."' ;");
				$this->ansatz(__("config_ansatz_editok"));
		}
		
		/************************************
		 ansatz löschen
		*************************************/
		private function ansatz_del(){
				//nur für admin erlaubt
				if ($this->uid!="1"){
					$_REQUEST['modul']="";
					$this->start(__("not_allowed"),true);
					return;
				}
				
				$_REQUEST['modul']="ansatz";
				$ansatz_id= $this->getid();
				//wenn nicht bestätigt fragen
				if ($this->getvar("bestaetigt")!="true"){
					//session-check
					$_SESSION['del_as_id']=$this->getid();
					$tpl=new template;
					$tpl->assign(array(	"message"=>__("ansatz_eintrag_del")));
					$tpl->auto_assign("config_ansatz_del.tpl");
					$tpl->assign_id();
					$this->html=$tpl->parse("config_ansatz_del.tpl");
				}
				//sonst löschen
				else {
						//prüfen ob id in der session die selbe wie in der form ist
						if ($_SESSION['del_as_id']!=$this->getid()){
						 	$this->fail("error_session_chk");
						 	return;
						}
						$this->db->query("DELETE FROM ansatz WHERE id=$ansatz_id ;");
						$this->db->query("DELETE FROM zeit WHERE ansatz_id=$ansatz_id;");
						$this->ansatz(__("config_ansatz_del"));
				}
				return;
		}
		
		function ansatz_tt(){
				$_REQUEST=$this->utf8_array_decode($_REQUEST);
				$this->layout=false;
				$arr = $this->table_item_array("ansatz",$this->getid());
				$tpl=new template;
				$tpl->auto_assign("config_ansatz_edit.tpl");
				$tpl->assign_id();
				$tpl->assign(	array( 	"db_name"		=> utf8_encode($arr['name']),
										"db_wert"		=> $arr['wert']
							  ));
				$ret=$tpl->parse("config_ansatz_edit.tpl");
				echo $ret;
		}
		


		/***************************************
		  
		  benutzerverwaltung
		  
		**************************************/
		private function user_start($message=false,$fail=false){
				
				//nur für admin
				if ($this->uid!="1"){
					$_REQUEST['modul']="";
					$this->start(__("not_allowed"),true);
					return;
				}
				
				//fehlerboxen
				$msg="";
				if ($message)
					$msg=$this->nachricht($message);
				if ($fail)
					$msg=$this->warnung($message);
				
				//userliste zusammenstellen
				include ("html/config_user_div.php");
				$trc='dark';
				foreach ($this->table_array("user") as $row){
						$trc = ($trc=='light') ? 'dark' : 'light';
						if ($row['id']!=1){
							$del = new template;
							$del->assign(array("id"	=> $row['id']));
							$del->auto_assign($tpl['user_del_button'],true);
							$del_button=$del->parse($tpl['user_del_button'],true);
						} else $del_button="";
						$item=new template;
						$item->assign(	array(	"trc"=>$trc,
												"name"	=> $row['name'],
												"id"	=> $row['id'],
												"login"	=> $row['login'],
												"detail"=> __('button_detail'),
												"del_button"=>$del_button,
														));
						$list.=$item->parse($tpl['user_liste'],true);
						unset($item);
				}
				$table=new template;
				$table->assign(array(	"items"		=> $list));
				$table->auto_assign($tpl['user_liste_table'],true);
				$list = $table->parse($tpl['user_liste_table'],true);
				
				$tpl=new template;
				$tpl->auto_assign("config_user_start.tpl");
				$tpl->assign( array (	"db_user_list"	=>$list,
										"msg"			=> $msg,));
				echo $tpl->parse("config_user_start.tpl");
		}
		
		
		/***************************************
		neuer Benutzer erstellen
		****************************************/
		
		private function user_new($fail=false){
				//nur admin
				if ($this->uid!="1"){
					$_REQUEST['modul']="";
					$this->start(__("not_allowed"),true);
					return;
				}
				
				$_REQUEST['modul']="user";
				//wenn beschränkung benuter				
				if (is_numeric(ET_MAX_USER)){
						$res = $this->db->query("SELECT id FROM user;");
						$num = $res->numRows();
						if ($num>=ET_MAX_USER){
							$this->user_start(sprintf(__("error_user_max"),ET_MAX_USER),true);
							return;
						} 
				}
				//wenn daten nicht ausgefüllt formular anzeigen
				if ($this->getvar("step")!="2"){
						$msg= ($fail) ? $this->warnung($fail) : "";
						$_REQUEST['modul']="user";
						$tpl= new template;
						$tpl->auto_assign("config_user_new.tpl");
						$tpl->assign(array("fail"=>$msg));
						echo $tpl->parse("config_user_new.tpl");
				}
				
				//sonst user erstellen
				elseif ($this->getvar("login") and $this->getvar("pw1") and $this->getvar("pw2") and $this->getvar("name")){
						//passwort abgleich
						if ($this->getvar("pw1") != $this->getvar("pw2")){
							$_REQUEST['step']=1;
							$this->user_new(__("pw_not_match"));
							return;
						}
						//htpasswd von leeren linien säubern
						$pw_file = file_get_contents(".htpasswd");
						$line_array=explode("\n",$pw_file);
						$pw_file="";
						foreach ($line_array as $line){
							if (!empty($line)) {
								$pw_file.="{$line}\n";
							}
						}
						//neuer user ins htpasswd-file schreiben
						$pw_file.=$this->getvar("login",true).":".crypt($this->getvar("pw1"));
						file_put_contents(".htpasswd",$pw_file);
						//user in datenbank schreiben
						$this->db->query("INSERT INTO user VALUES (	NULL,
																'".$this->getvar("login",true)."',
																'".$this->getvar("name",true)."',
																'".ET_DEFAULT_LANG."',
																'".ET_DEFAULT_STYLE."')");
						$this->user_start();
			} else {
						$_REQUEST['step']=1;
						$this->user_new(__("error_empty"));
			}
		
		}
		
		/**************************************
		 benutzer löschen
		***************************************/
		
		public function user_del(){
				//admin only
				if ($this->uid!="1"){
					$_REQUEST['modul']="";
					$this->start(__("not_allowed"),true);
					return;
				}
				
				$_REQUEST['modul']="user";
				$user_id= $this->getid();
				//wenn nicht bestätigt fragen
				if ($this->getvar("bestaetigt")!="true"){
					//session-check
					$_SESSION['del_user_id']=$this->getid();
					$str = $this->user_name($user_id) . " (". $this->user_login($user_id) . ")";
					
					$tpl=new template;
					$tpl->assign(array(	"message"=>sprintf(__("config_user_del"),$str),
									));
					$tpl->auto_assign("config_user_del.tpl");
					$tpl->assign_id();
					echo $this->html=$tpl->parse("config_user_del.tpl");
				}
				//sonst löschen
				else {
						//prüfen ob id in der session die selbe wie in der form ist
						if ($_SESSION['del_user_id']!=$this->getid()){
						 	$this->fail("error_session_chk");
						 	return;
						}
						$name=$this->user_name($user_id);
						//.htaccess neu zusammensetzen
						$pw_file = file_get_contents(".htpasswd");
						$line_array=explode("\n",$pw_file);
						$ht_file="";
						foreach ($line_array as $line){
								if (!empty($line)) {
										$arr = explode(":",$line);
										if ($arr[0]!=$this->user_login($user_id)){
												$ht_file.=$line."\n";
										}
								}
						}
						file_put_contents(".htpasswd",$ht_file);
						$this->db->query("DELETE FROM user WHERE id=$user_id ;");
						$this->db->query("DELETE FROM beleg WHERE user_id=$user_id;");
						$this->db->query("DELETE FROM zeit WHERE user_id=$user_id;");
						$_SESSION['del_user_id']="";
						$this->user_start(sprintf(__("conf_user_del_ok"),$name));
				}
		}
		
		/***********************************
		 benutzer-details
		************************************/
		public function user_detail($message=false,$fail=false){
				//admin only
				if ($this->uid!="1"){
					$_REQUEST['modul']="";
					$this->start(__("not_allowed"),true);
					return;
				}
				
				$_REQUEST['modul']="user";
				//prüfen ob eine gültige id angegeben wurde
				if (!$this->check_id("user", $this->getid())){
						$this->fail("error_id_fails");
						return;
				}
				$user_id=$this->getid();
				$msg="";
				
				
				//wenn passwort-änderung
				if ($this->getvar("pw1") and $this->getvar("pw2")){
						//prüfen ob passwörter stimmen
						if ($this->getvar("pw1") != $this->getvar("pw2")){
								$message=__("pw_not_match");
								$fail=true;
						} else {
								//ab hier ändern
								$pw_file = file_get_contents(".htpasswd");
								$line_array=explode("\n",$pw_file);
								$ht_file="";
								foreach ($line_array as $line){
										if (!empty($line)) {
												$arr = explode(":",$line);
												if ($arr[0]==$this->user_login($user_id)){
														$ht_file.=$arr[0].":".crypt($this->getvar("pw1"))."\n";
												} else {
														$ht_file.=$line."\n";
												}
										}
								}
								file_put_contents(".htpasswd",$ht_file);
								$message=__("pw_changed");
								$fail=false;
						}
				} 
				
				//wenn name & lögin edit
				elseif ($this->getvar("userlogin") and $this->getvar("username")){
								//ab hier ändern
								$pw_file = file_get_contents(".htpasswd");
								$line_array=explode("\n",$pw_file);
								$ht_file="";
								foreach ($line_array as $line){
										if (!empty($line)) {
												$arr = explode(":",$line);
												if ($arr[0]==$this->user_login($user_id)){
														$ht_file.=$this->getvar("userlogin",true).":".$arr[1]."\n";
												} else {
														$ht_file.=$line."\n";
												}
										}
								}
								$this->db->query("UPDATE user SET 	login	='".$this->getvar("userlogin",true)."', 
																name	='".$this->getvar("username",true)."' 
														 WHERE id='$user_id';");
								file_put_contents(".htpasswd",$ht_file);
								$message=__("data_changed");
								$fail=false;
				}
				
				
				
				if ($message)
					$msg=$this->nachricht($message);
				if ($fail)
					$msg=$this->warnung($message);
					
				$tpl=new template;
				$tpl->assign(array(	"msg"				=>	$msg,
									"db_user_login"		=>	$this->user_login($user_id),
									"db_user_fullname"	=>	$this->user_name($user_id)
									));
				$tpl->auto_assign("config_user_detail.tpl");
				$tpl->assign_id();
				echo $tpl->parse("config_user_detail.tpl");
		
		
		}
		
}

?>

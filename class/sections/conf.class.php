<?php

class etempus_conf extends etempus {

		
		
		
		/************************************
		 * 
		 * kundendaten editieren 
		 * 
		 ************************************/
		
		function kunde_edit(){
				$this->layout=false;
				$this->db=db_make();
				//prüfen ob eine gültige id angegeben wurde
				if (!$this->check_id("kunden", $this->getid())){
					 echo $this->fail("error_id_fails");
					 return;
				}
				$_SESSION['edit_id']=$this->getid();
				//wenn keine daten angekommen sind eingabemaske anzeigen
				if ($this->getvar("step")=="2"){
						
						//prüfen ob edit-id gesetzt ist und mit id aus form übereinstimmt
						if ($_SESSION['edit_id']!=$this->getid()){
								echo $this->fail("error_session_chk");
								return;
						}
						$_REQUEST=$this->utf8_array_decode($_REQUEST);
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
						$msg=$this->nachricht(__("eintrag_edit_ok"));
						
				} else {
					$msg="";
				}
				$tpl=new template;
				$tpl->assign( array( 	"titel"		=> __("kunde_edit") ,
										"msg"=>$msg,
										"name0"		=> __("kunde_name"),
										"adresse0"	=> __("kunde_adresse"),
										"plz0"		=> __("kunde_plz"),
										"ort0"		=> __("kunde_ort"),
										"ansprech"	=> __("kunde_ansprech"),
										"tel"		=> __("kunde_telefon"),
										"mail"		=> __("kunde_email"),
										"edit"		=> __("button_edit"),
										"fail"		=> $fail				) );
				$k=$this->utf8_array_encode($this->table_item_array("kunden",$this->getid()));
				$a['name_db']=$k['name'];
				
				$tpl->assign(array_merge($k,$a));		
				$tpl->assign(array( "fail"=>$fail));
				echo $tpl->go("kunde_edit.tpl");
		}
		
		
		
		
		/******************************
		 * neues projekt
		 *****************************/
		function projekt_neu(){
				$this->layout=false;
				//prüfen ob eine gültige id angegeben wurde
				if (!$this->check_id("kunden", $this->getid())){
					 echo $this->fail("error_id_fails");
					 return;
				}
				$ansatzliste = "";
				foreach ($this->table_array("ansatz","name",false,"aktiv","1") as $ansatz ){
						$ansatzliste.= "<input type='checkbox'{$chk}name='ansatz_liste[]' value='{$ansatz['id']}' id='chk_a_{$ansatz['id']}'><label for='chk_a_{$ansatz['id']}'> {$ansatz["name"]}</label><br />";
				}
				$ansatz_all = " checked ";$ansatz_single=" ";
				$this->template->assign( array(	"msg"=>$msg,
												"ansatz_liste"=>utf8_encode($ansatzliste),
												"kname"=>utf8_encode($this->kunde_name($this->getid()))
										  ) );			
				echo $this->template->go("projekt_neu.tpl",array());
		}
		
		/******************************
		 * neues projekt erstellen
		 *****************************/
		function projekt_neu_create(){	
				$_REQUEST=$this->utf8_array_decode($_REQUEST);
				$this->layout=false;
				//prüfen ob eine gültige id angegeben wurde
				if (!$this->check_id("kunden", $this->getid())){
					 echo __("error_id_fails");
					 return;
				}
				if ($this->getvar('ansatz')=='all'){
					$ans="*";
				} else {
					$ans="";
					foreach ($this->getvar("ansatz_liste") as $a){ $ans.="$a,"; }
					$ans=substr($ans,0,-1);
				}
				$kd_tmp=$this->getvar('kostendach',true);
				$kd=(is_numeric($kd_tmp)) ? $kd_tmp : "0" ;
				
				//this is really not a nice q-string :(
				$n=$this->db->query("INSERT INTO projekte VALUES (	
										NULL, ".$this->getid().",'".$this->getvar("name",true)."',
										'{$ans}','*','{$kd}','0');");
				if ($n){
					echo $this->getlastid('projekte');
				} else {
					echo 00;
				}
		}
		
		/******************************
		 * projekt editieren
		 *****************************/
		function projekt_edit(){
				$msg="";
				$_REQUEST=$this->utf8_array_decode($_REQUEST);
				
				if (!$this->check_id("projekte",$this->getid())) { die(__('error_id_fails')); }
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
				
				$item = $this->table_item_array("projekte",$this->getid());
				foreach ($this->table_array("ansatz","name",false,"aktiv","1") as $ansatz ){
						$ansatz=$this->utf8_array_encode($ansatz);
						$res = preg_match("/{$ansatz['id']}/",$item['ansatz']);
						$chk=($this->match_num_list($ansatz['id'],$item['ansatz'])) ? " checked " : "" ;
						$ansatzliste.= "<input type='checkbox'{$chk}name='ansatz_liste[]' value='{$ansatz['id']}'> {$ansatz["name"]}<br />";
				}
				
				
				//checkboxen richtig setzen
				if ($item['ansatz']=="*"){
					$ansatz_all = " checked";$ansatz_single="";$disp_box="display:none;";
				} else {
					$ansatz_all = "";$ansatz_single=" checked";$disp_box="display:block;";
				}
				
				$a=array(	"msg"=>$msg,
							"ansatz_liste"=>$ansatzliste,
							"kname"=>utf8_encode($this->kunde_name($this->pid_to_kid($this->getid()))),
							"ansatz_all"=>$ansatz_all,
							"ansatz_single"=>$ansatz_single,
							"db_kostendach"=>$this->get_kostendach($this->getid()),
							"proj_name"=>utf8_encode($this->projekt_name($this->getid())),
							"disp_box"=>$disp_box,
							"msg"=>$msg,
						
						);
				echo $this->template->go("projekt_detail.tpl",$a);
		}


		/*************************
		 * neuer kunde & projekt layout
		 ************************/
		
		function kunde_proj_new(){
				$ansatzliste = "";
				foreach ($this->table_array("ansatz","name",false,"aktiv","1") as $ansatz ){
						$ansatzliste.= "<input type='checkbox'{$chk}name='ansatz_liste[]' value='{$ansatz['id']}' id='chk_a_{$ansatz['id']}'><label for='chk_a_{$ansatz['id']}'> {$ansatz["name"]}</label><br />";
				}
				echo $this->template->go("kunde_projekt_neu.tpl",array("ansatz_liste"=>utf8_encode($ansatzliste)));
		}
		
		
		/*************************
		 * neuer kunde & projekt anlegen
		 ************************/
		
		function kunde_projekt_neu_create(){
					$this->db->query( "INSERT INTO kunden VALUES (	NULL, '".utf8_decode($this->getvar("kname",true))."','','','','','','','0');");
					$_REQUEST['id']=$kid=$this->getlastid('kunden');
					echo $kid;
					ob_start();
					$this->projekt_neu_create();
					ob_end_clean();
					
					
					
		}
		
		
		
		
		/**************************
		 * kunde löschen
		 **************************/
		
		function kunde_del(){
				$id=$this->getid();
				if (!$this->check_id("kunden", $id)){
					 echo __("error_id_fails");
					 return;
				}
				$kname=utf8_encode($this->kunde_name($id));
				// frage
				if ($_REQUEST['step']!="2"){
						echo $this->frage(__('confirm_del'), sprintf(__('delete_kunde_ask'),$kname), "", "");
				}
				//löschen
				else {
						//projekte & zeit löschen
						$proj=$this->kunden_projekte($id);
						if ($proj){
								foreach ($proj as $projekt){
										$this->db->query("DELETE FROM zeit WHERE projekt_id='{$projekt['id']}'");
										$this->db->query("DELETE FROM beleg WHERE projekt_id='{$projekt['id']}'");
										$this->db->query("DELETE FROM projekte WHERE id='{$projekt['id']}'");	
								}
							
						}
						//kunde löschen
						$this->db->query("DELETE FROM kunden WHERE id='{$id}'");	
						//nachricht
						echo $this->nachricht(sprintf(__('delete_kunde_ok'),$kname));
				}
		}
		
		
		/**************************
		 * projekt löschen
		 **************************/	
		
		function projekt_del(){
				$id=$this->getid();
				if (!$this->check_id("projekte", $id)){
					 echo __("error_id_fails");
					 return;
				}
				$pname=utf8_encode($this->projekt_name($id));
				// frage
				if ($_REQUEST['step']!="2"){
						echo $this->frage(__('confirm_del'), sprintf(__('projekt_del_confirm'),$pname), "", "");
				}
				//löschen
				else {
						//query ausführen
						$this->db->query("DELETE FROM zeit WHERE projekt_id='{$id}'");
						$this->db->query("DELETE FROM beleg WHERE projekt_id='{$id}'");
						$this->db->query("DELETE FROM projekte WHERE id='{$id}'");	
						//nachricht
						echo $this->nachricht(sprintf(__('delete_projekt_ok'),$pname));	
				}
			
		}
		
		
}

?>

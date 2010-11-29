<?php
/***************************************************
 * 
 * eTempus Belege-Abos
 * (c) 2009 by Cyrill von Wattenwyl, protagonist GmbH
 * alle Rechte vorbehalten
 * 
 ***************************************************/

class et_beleg_abo extends etempus {

		//
		// einhängepunkt für etempus
		//
		public function build (){
				$this->dn=dirname(__FILE__)."/";
				$this->url="http://".$_SERVER['HTTP_HOST'].str_replace("index.php","",$_SERVER['SCRIPT_NAME']).$this->dir;
				$this->test_table();
				$this->build_output();
		}
		
		
		//
		// ausgabe für etempus erstellen
		//
		function build_output($msg=false){
				
				if ($_REQUEST['ajax_tt']){
						echo "hohoho";
						die;
				}
				
				// neues abo
				if ($_REQUEST['create_new']){
						$m=$this->create_new();
						if ($m===true){ $msg = $this->nachricht(__("abo_erstellt")); } 
						else { $msg = $this->warnung($m); }
				}
				
				//abo löschen
				if (is_numeric($_REQUEST['del_id'])){
						$did=$this->getvar("del_id",true);
						if ($this->db->query("DELETE FROM belge_abo WHERE id={$did}")==1){
							$msg = $this->nachricht(__("abo_geloescht"));
						}
				}
				
				//alle abos updaten
				$this->update_abos();
				if ($this->update_cont>1){
						$msg=$this->nachricht(__("abo_entry_update"));
				}
				
				
				$js1 = "onclick=\"lade_projekt_liste(this.value);\" id=\"kid\"";
				$js_loader="lade_projekt_liste($('kid').value);";
				
				//tabelle erstellen
				$abos = $this->table_array("belge_abo");				
				
				$db_abo="";
				if ($abos){
						$a=new template($this->dir."tpl/");
						$trc='dark';
						foreach ($abos as $abo){
								$trc = ($trc=='light') ? 'dark' : 'light';
								$kunde=$this->kunde_name($this->pid_to_kid($abo['projekt_id']));
								$projekt=$this->projekt_name($abo['projekt_id']);
								$la = unserialize($abo['layout_data']);
								switch ($la['type']){
										case "w": $z_txt = __("woechentlich"); break;
										case "m": $z_txt = __("monatlich"); break;
										case "j": $z_txt = __("jaehrlich"); break;
										case "c": $z_txt = __("alle") . " {$la['value']} ".__("tage"); break;
									 
								}
								$last=@date("d.m.Y",$abo['last_book']);
								if ($last=="01.01.1970" || !$last) { $last = "---"; }
								$style=" ";
								if ($this->updated_entrys) { 
									foreach ($this->updated_entrys as $pos) {
										if ($pos==$abo['id']) { $style='style="background:#FEABAB ! important;"'; break; }
									}
								}
								
								
								$db_abo.=$a->go("beleg_abo_item.tpl",array("db_kunde"	=>$this->kunde_name($this->pid_to_kid($abo['projekt_id'])),
																		   "db_projekt"	=>$this->projekt_name($abo['projekt_id']),
																		   "db_betrag"	=>$abo['betrag'],
																		   "db_z"		=>$z_txt,
																		   "db_last"	=>$last,
																		   "db_id"		=>$abo['id'],
																		   "db_style"	=>$style,
																		   "trc"	=>$trc,
																		   ));
						}
				}
				
				$arr = array("kunde_dd_liste"	=>	$this->kunden_dd_liste(false,$js1),
							 "msg"				=>	$msg,
							 "js_loader"		=>	$js_loader, 
							 "uurl"				=> 	$this->url."cron/index.php",
							 "db_abos"			=>	$db_abo,
							 );

				$tpl=new template($this->dir."tpl/");
				$this->html.=$tpl->go("beleg_abo.tpl",$arr);
				
		}
		
		
		//
		// neuer eintrag erstellen
		//
		function create_new(){
				if (!is_numeric($_REQUEST['betrag'])) { return __("beleg_nur_ganzzahl"); }
				$now=time(mktime($_REQUEST['startDatum']));
				switch ($_REQUEST['timespan']){
						case "w":
							$next = strtotime("+1 week",$now);
							$ld=array("type"=>"w");
							break;
							
						case "m":
							$next = strtotime("+1 month",$now);
							$ld=array("type"=>"m");
							break;
							
						case "j":
							$next = strtotime("+1 year",$now);
							$ld=array("type"=>"j");
							break;
							
						case "c":
							$e = ($_REQUEST['c_days']==1) ? "" : "s";
							$next = strtotime("+{$_REQUEST['c_days']} day{$e}",$now);
							$ld=array("type"=>"c","value"=>$_REQUEST['c_days']);
							break;
							
						default: $next = false; break;
					
				}
				
				if (!$next || !is_numeric($next) ){ return __("no_ts_selected"); }
				
				$diff = $next-$now;
				$ld=serialize($ld);
				$pid=$this->getvar("projekt",true);
				$desc=$this->getvar("desc",true);
				$betrag=$this->getvar("betrag",true);
				$q="INSERT INTO belge_abo VALUES (NULL,{$pid},{$betrag},{$now},{$diff},{$next},'{$ld}','','{$desc}');";
				$user_id = 1;
				$beleg_q="INSERT INTO beleg VALUES (NULL, {$pid}, '{$user_id}','$now','{$desc}','{$betrag}');";
				$this->db->query($beleg_q);
				$this->db->query($q);
				return true;
		}
		
		
		
		//
		// alle abos prüfen und wenn nötig buchen
		//
		public function update_abos(){
			$abos = $this->table_array("belge_abo");
				$this->update_cont=0;
				$this->updated_entrys=array();
				if ($abos){
						foreach ($abos as $abo){
								$now=time();
								// muss gebucht werden ?
								if (($abo['last_book'] + $abo['diff']) < $now){
			
										// wenn eintrag erstellt wurde und noch nicht gebucht werden darf
										if (!$abo['last_book']) { if (($abo['start'] + $abo['diff']) > $now ) { continue; } }
										
										//wie viel mal muss gebucht werden ?
										$tdiff = $now - $abo['last_book'];
										$num = ceil(($tdiff/$abo['diff']));
										$num = ($num===1) ? 1 : $num-1;
										//echo $num;
										
										//vorbereiten & buchen
										$a['id']=$abo['id'];
										$a['pid']=$abo['projekt_id'];
										$a['desc']=$abo['desc'];
										$a['betrag']=$abo['betrag'];
										$a['diff']=$abo['diff'];
										if (is_numeric($num) and $num > 0) { 
												array_push($this->updated_entrys,$abo['id']); 								
												for ($i=1;$i<=$num;$i++){
														$a['i']=$i;
														$a['time']=($now+$abo['diff']) - ($i * $abo['diff']);	
														$this->book_item($a);
														
												}
										}
										
								}
						}
				}
		}
		
		//
		// eintrag buchen
		//
		private function book_item($array){
				$this->update_cont++;
				$a=$array;
				$now=time();
				$beleg_q="INSERT INTO beleg VALUES (NULL, {$a['pid']}, '1','{$a['time']}','{$a['desc']}','{$a['betrag']}');";
				$abo_q="UPDATE belge_abo SET last_book='{$now}',next='{$a['diff']}' WHERE id={$a['id']};";
				$this->db->query($beleg_q);
				$this->db->query($abo_q);
		}
		
		
		
		
		//
		// belege-tabelle prüfen und wenn nicht vorhanden erstellen
		//
		private function test_table(){
				$res = $this->db->query("SELECT * FROM belge_abo");
				$a=class_parents($res);
				//tabelle scheint nicht zu existieren
				if ($a['PEAR_Error']=='PEAR_Error'){
						$c=new etempus_db_config;
						$sql_query=file_get_contents("{$this->dn}sql/{$c->dsn["phptype"]}.sql");
						$r=$this->db->query($sql_query);
						if (!$r){
								$this->fail("db_err");
								echo $this->html;
								die;
						} else {
								$this->html=$this->nachricht(__("abo_db_created"));
						}
				}
		}

}

?>

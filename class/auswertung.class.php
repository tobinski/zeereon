<?php

class etempus_auswertung extends etempus {


		public $table;
		
		/**********************************************
		 submenu / tabs
		 "Beschriftung"=>"modul"
		***********************************************/		
		public function __construct(){
				$this->submenu = array(	"{auswertung_kunde}" 	=> 	"",
										"{auswertung_projekt}" 	=> 	"projekt",
										"{auswertung_user}" 	=> 	"user",
										);
		}


		/**********************************************
		 einhängepunkt auswertungen
		***********************************************/
		public function build(){
				//modul auswählen
				switch ($this->getvar("modul")) {

					//startseite
					default:
						$this->start();
						break;
					
					//auswerten
					case "auswerten":
						$this->auswerten();
						break;

				}
				return;
		}
		
		
		/**************************************
		 formulare anzeigen
		**************************************/
		
		private function start(){
				$js1="";$js_loader="";
				
				//welche auswertungsart (kunde/projekt/user) ist gewählt
				switch ($this->getvar('modul')){
						default:
							$kall=true;
							$tpl_file="auswertung_kunde.tpl";
							break;
						case "projekt":
							$kall=false;
							$tpl_file="auswertung_projekt.tpl";
							$js1 = "onclick=\"lade_projekt_liste(this.value,true);\" id=\"kid\"";
							$js_loader="lade_projekt_liste($('kid').value,true);";
							break;
						case "user":
							$kall=false;
							$tpl_file="auswertung_user.tpl";
							$js1 = "onclick=\"lade_projekt_liste(this.value,true);\" id=\"kid\"";
							$js_loader="lade_projekt_liste($('kid').value,true);";
							break;
				}
				
				//gewünschte seite assignen und ausgeben
				$tpl=new template;
				$tpl->auto_assign($tpl_file);
				$tpl->assign(	array(	"kunde_dd_liste"	=>	$this->kunden_dd_liste(false,$js1,false,$kall),
										"monate_dd_liste"	=>	$this->letzte_monate_dd(),
										"letzte_jahre_dd"	=>	$this->letzte_jahre_dd(),  		
										"user_dd_liste"		=> 	$this->user_dd_liste(),
										"js_loader"			=>	$js_loader,
										));
				$this->html=$tpl->parse($tpl_file);
		}
		



		
		/************************************
		 auswerten einhängepunkt
		*************************************/
		
		public function auswerten($no_out=false){
				if (!$this->db) $this->db=db_make();
				
				//zuerst zeitraum ausrechnen
				switch ($this->getvar("zeitraum_typ")){
						case "monat":
							$ts_von = $this->getvar('monat');
							$ts_bis = strtotime("+1 month",$ts_von);
							break;
						case "jahr":
							$ts_von = $this->getvar('jahr');
							$ts_bis = strtotime("+1 year",$ts_von);
							break;
						case "user":
							$ts_von	= strtotime($this->getvar('von')." 00:00:00");
							$ts_bis = strtotime($this->getvar('bis')." 23:59:59");
							break;
				}
				
				//wenn zeitraum nicht ausrechenbar zurück zum start
				if (!$ts_von || !$ts_bis) {
							$this->start();
							return;
				}
				
				//art der auswertung, query "bauen"
				switch ($this->getvar("art")){
						
						// auswertung kunde
						case "kunde":
							$kunden_id=$this->getvar('kunde');
							$kid_query="";
							$kid_q1="WHERE kunden_id={$kunden_id}";
							if ($kunden_id=="*") $kid_q1="";
							$res = $this->db->query("SELECT id FROM projekte {$kid_q1}");
							
							while ($row=$res->fetchRow($res)){
								$kid_query.="projekt_id={$row[0]} OR ";
							}
							$kid_query=substr($kid_query,0,-3);
							$query = "SELECT * FROM zeit WHERE zeit_start>={$ts_von} AND zeit_ende<={$ts_bis} AND ({$kid_query}) ORDER BY zeit_start;";
							$query2= "SELECT * FROM beleg WHERE zeit>={$ts_von} AND zeit <={$ts_bis} AND ({$kid_query}) ORDER BY zeit;";
							break;
							
						//auswertung projekt
						case "projekt":
							if (!$this->getvar('projekt')){
									$_REQUEST['modul']="projekt";
									$this->start();
									return;
							}
							$extra_q="";
							if ($this->getvar('user_only')){
									$extra_q="AND user_id='".$this->getvar('user',true)."' ";
							}
							$pid = $this->getvar('projekt',true);
							$pid_q1=" AND projekt_id='{$pid}'";
							if ($pid=="*") { 
									$pid_q0=$pid_q1="";
									$kunden_id=$this->getvar('kunde');
									$rall=$this->db->query("SELECT id FROM projekte WHERE kunden_id='{$kunden_id}';");
									while ($row=$rall->fetchRow()) $pid_q0.="projekt_id={$row[0]} OR ";
									$pid_q0=substr($pid_q0,0,-3);
									$pid_q1 = " AND ({$pid_q0}) ";
							}
							$query = "SELECT * FROM zeit WHERE zeit_start>={$ts_von} AND zeit_ende<={$ts_bis}{$pid_q1} {$extra_q}ORDER BY zeit_start;";
							$query2= "SELECT * FROM beleg WHERE zeit>={$ts_von} AND zeit <={$ts_bis}{$pid_q1} {$extra_q}ORDER BY zeit;";
							//echo $query;
							break;
						
						//auswertung benutzer
						case "user":
							if (!$this->getvar("user")){
									$_REQUEST['modul']="user";
									$this->start();
									return;
							}
							$extra_q="";
							//wenn nur kunde
							if ($this->getvar("kunde_only")){
									$kunden_id=$this->getvar('kunde');
									$kid_query="";
									$res=$this->db->query("SELECT id FROM projekte WHERE kunden_id={$kunden_id}");
									while ($row=$res->fetchRow()) $kid_query.="projekt_id={$row[0]} OR ";
									$kid_query=substr($kid_query,0,-3);
									$extra_q = "AND ({$kid_query}) ";
							}
							//wenn nur projekt
							if ($this->getvar("projekt_only")){
									$extra_q = "AND projekt_id='".$this->getvar("projekt",true)."' ";
							}
							$uid = $this->getvar('user',true);
							$query = "SELECT * FROM zeit WHERE zeit_start>={$ts_von} AND zeit_ende<={$ts_bis} AND user_id='{$uid}' {$extra_q}ORDER BY zeit_start;";
							$query2= "SELECT * FROM beleg WHERE zeit>={$ts_von} AND zeit <={$ts_bis} AND user_id='{$uid}' {$extra_q}ORDER BY zeit;";
							break;
						
						//wenn art unbekannt zurück zum start
						default:
							$this->start();
							return;
							break;
				}
				
				//query ausführen und ergebnis formatieren
				$res = $this->query_array($query);
				$res2 = $this->query_array($query2);
				

				$this->res_arr = $this->formatiere_zeit_array($res);
				$this->res_arr_beleg = $this->formatiere_beleg_array($res2);
					
				if (!$no_out){			
						//ausgabeformat switchen
						switch ($this->getvar("format")){
								//html in diesem fenster
								default:
									$this->build_html_table();
									$this->html=$this->table;
									break;
								//csv-datei
								case "csv":
									$this->build_csv($no_out);
									break; 
						}
				}
				return;
		}
		
		/***************************
		 html-tabelle
		****************************/
		
		private function build_html_table(){
				$this->tbl_tr_s="<tr>";
				$this->tbl_tr_e="</tr>";
				$this->tbl_td_s="<td> ";
				$this->tbl_td_sc="<td colspan='%s'> ";
				$this->tbl_td_e="</td>";
				$this->b_s="<b>";
				$this->b_e="</b>";
				$this->br="<br />";
				$this->table_end="</table>";
				$this->table = "<table id='table_auswertung_html'>".$this->tbl_tr_s;
				$this->build_table();
		}
		
		
		/***************************
		 csv-datei ausgeben
		****************************/
		
		private function build_csv($no_out){
				//tabelle erstellen
				$this->tbl_tr_s="";
				$this->tbl_tr_e="\n";
				$this->tbl_td_s="\"";
				$this->tbl_td_sc="\"";
				//csv-typ
				switch ($this->getvar("csv_type")){
					default: $this->tbl_td_e="\","; break;
					case "ms": $this->tbl_td_e="\";"; break;
				}
				$this->b_s="";
				$this->b_e="";
				$this->br="";
				$this->table_end="";
				$this->table = "";
				$this->build_table();
				//resultat säubern
				$arr = explode("\n",$this->table);
				$csv="";
				foreach ($arr as $line){
					$csv.= substr($line,0,-1) . "\n";
				}
				$csv= substr($csv,0,-1);
				//datei ausgeben und ende
				if (!$no_out){
					$filename=sprintf(__("csv_dateiname"),date('YmdHis')).".csv";
					header('Content-type: text/csv');
					header('Content-Disposition: attachment; filename="'.$filename.'"');
					echo $csv;
					die();
				} else {
					$this->table=$csv;
				}
		}
		
		
		
		/*****************************
		
		tabelle bauen (html+csv)
			
		******************************/
		
		private function build_table(){
				
				if ($this->getvar("r_fix") and $this->getvar("v_fix")){
					$fix=$this->getvar("v_fix");
				} else {
					$fix=false;
				}
				//anzahl reihen herausfinden
				$td_rows = 0;
				if ($this->getvar('s_datum')) $td_rows++;
				if ($this->getvar('s_zeit')) $td_rows++;
				if ($this->getvar('s_projekt')) $td_rows++;
				if ($this->getvar('s_kunde')) $td_rows++;
				if ($this->getvar('s_kommentar')) $td_rows++;
				if ($this->getvar('s_user')) $td_rows++;
				if ($this->getvar('s_kosten')) $td_rows++;
				if ($this->getvar('s_ansatz')) $td_rows++;
								
				//tabellen-kopf
				$this->table.= ($this->getvar('s_datum')) ? $this->tbl_td_s.$this->b_s.__("datum").$this->b_e.$this->tbl_td_e : "";
				$this->table.= ($this->getvar('s_zeit')) ? $this->tbl_td_s.$this->b_s.__("zeit").$this->b_e.$this->tbl_td_e : "";
				$this->table.= ($this->getvar('s_csvzeit')) ? $this->tbl_td_s.$this->b_s.__("zeit_csv").$this->b_e.$this->tbl_td_e : "";
				$this->table.= ($this->getvar('s_projekt')) ? $this->tbl_td_s.$this->b_s.__("projekt_s").$this->b_e.$this->tbl_td_e : "";
				$this->table.= ($this->getvar('s_kunde')) ? $this->tbl_td_s.$this->b_s.__("kunde_s").$this->b_e.$this->tbl_td_e : "";
				$this->table.= ($this->getvar('s_kommentar')) ? $this->tbl_td_s.$this->b_s.__("kommentar").$this->b_e.$this->tbl_td_e : "";	
				$this->table.= ($this->getvar('s_user')) ? $this->tbl_td_s.$this->b_s.__("benutzer").$this->b_e.$this->tbl_td_e : "";
				$this->table.= ($this->getvar('s_ansatz')) ? $this->tbl_td_s.$this->b_s.__("ansatz").$this->b_e.$this->tbl_td_e : "";
				$this->table.= ($this->getvar('s_kosten')) ? $this->tbl_td_s.$this->b_s.__("kosten").$this->b_e.$this->tbl_td_e : "";
				$this->table.= $this->tbl_tr_e;
				
				//datenteil zeiteinträge
				$total_kosten=0;
				$sekunden=0;
				if (is_array($this->res_arr)){
						foreach ($this->res_arr as $item){
								$item=$this->utf8_array_encode($item);
								if (!$fix) {
									$total_kosten=$total_kosten+$item['kosten'];
									$kosten=$item['kosten'];
								} else {
									$kosten = round(($item['sekunden']/3600)*$fix,2);
									$kosten = number_format(round($kosten*20)/20,2,".","");
									$total_kosten=$total_kosten+$kosten;
								}
								
								$this->table.=$this->tbl_tr_s;
								$this->table.= ($this->getvar('s_datum')) ? $this->tbl_td_s. $item['tdesc'] .$this->tbl_td_e : "";
								$this->table.= ($this->getvar('s_zeit')) ? $this->tbl_td_s. $item['zeit_formatiert'] .$this->tbl_td_e : "";
								$this->table.= ($this->getvar('s_csvzeit')) ? $this->tbl_td_s. $this->formatiere_zeit_csv($item['sekunden']) .$this->tbl_td_e : "";
								$this->table.= ($this->getvar('s_projekt')) ? $this->tbl_td_s.$item['projekt'].$this->tbl_td_e : "";
								$this->table.= ($this->getvar('s_kunde')) ? $this->tbl_td_s.$item['kunden_name'].$this->tbl_td_e : "";								
								$this->table.= ($this->getvar('s_kommentar')) ? $this->tbl_td_s.$item['beschreibung'].$this->tbl_td_e : "";	
								$this->table.= ($this->getvar('s_user')) ? $this->tbl_td_s.$item['user_name'].$this->tbl_td_e : "";
								$this->table.= ($this->getvar('s_ansatz')) ? $this->tbl_td_s.$item['ansatz'].$this->tbl_td_e : "";
								$this->table.= ($this->getvar('s_kosten')) ? $this->tbl_td_s.$kosten.$this->tbl_td_e : "";
								$this->table.=$this->tbl_tr_e;
								$sekunden=$sekunden+$item['sekunden'];
						}
				}
				
				if ($this->getvar("r_beleg")){
						//datenteil belege
						if (count($this->res_arr_beleg)>0 and $this->res_arr_beleg){
								$td_string0=sprintf($this->tbl_td_sc,$td_rows);
								$this->table.=$this->tbl_tr_s . $td_string0 . $this->b_s. $this->br. __("belege") . $this->b_e. $this->tbl_td_e . $this->tbl_tr_e;
								foreach ($this->res_arr_beleg as $item){
										$item=$this->utf8_array_encode($item);
										$this->table.=$this->tbl_tr_s;
										$this->table.= ($this->getvar('s_datum')) ? $this->tbl_td_s. $item['tdesc'] .$this->tbl_td_e : "";
										$this->table.= ($this->getvar('s_zeit')) ? $this->tbl_td_s .$this->tbl_td_e : "";
										$this->table.= ($this->getvar('s_projekt')) ? $this->tbl_td_s.$item['projekt'].$this->tbl_td_e : "";
										$this->table.= ($this->getvar('s_kunde')) ? $this->tbl_td_s.$item['kunden_name'].$this->tbl_td_e : "";								
										$this->table.= ($this->getvar('s_kommentar')) ? $this->tbl_td_s.$item['beschreibung'].$this->tbl_td_e : "";	
										$this->table.= ($this->getvar('s_user')) ? $this->tbl_td_s.$item['user_name'].$this->tbl_td_e : "";
										$this->table.= ($this->getvar('s_ansatz')) ? $this->tbl_td_s.$this->tbl_td_e : "";
										$this->table.= ($this->getvar('s_kosten')) ? $this->tbl_td_s.$item['betrag'].$this->tbl_td_e : "";
										$this->table.=$this->tbl_tr_e;
										$total_kosten=$total_kosten+$item['betrag'];
								}
						}
				}
			
				$td_string=sprintf($this->tbl_td_sc,$td_rows-1);
				
				//reihe totalzeit
				if ($this->getvar('r_totalzeit')){
						$total_time = $this->formatiere_zeit($sekunden);	
						$this->table.=$this->tbl_tr_s . $td_string . $this->br. __("totalzeit") . " : ". $this->b_s. $total_time. $this->b_e. $this->tbl_td_e .$this->tbl_td_s.$this->tbl_td_e.$this->tbl_tr_e;
				}
				
				//folgende reihen nur ausgeben wenn kosten ausgewählt sind
				if ($this->getvar('s_kosten')){
						//reihe mwst
						if ($this->getvar('mwst')){
							//$mwst_total=0;
							$mwst_total = round(($total_kosten/100)*$this->getvar('mwst_satz'),2);
							$mwst_total = number_format(round($mwst_total*20)/20,2,".","");
							$total_kosten = $mwst_total+$total_kosten;
							$this->table.=$this->tbl_tr_s. $td_string .$this->br .$this->b_s. __("mwst") ." (".$this->getvar("mwst_satz")." %)". $this->b_e . $this->tbl_td_e . $this->tbl_td_s . $this->b_s .$this->br. $mwst_total . $this->b_e . $this->tbl_td_e . $this->tbl_tr_e ;
						}
						//reihe total-kosten
						$total_kosten=number_format($total_kosten,2,".","");
						$this->table.= $this->tbl_tr_s. $td_string . $this->b_s . $this->br . __("total") . $this->b_e. $this->tbl_td_e . $this->tbl_td_s . $this->b_s . $this->br . $total_kosten . $this->b_e . $this->tbl_td_e . $this->tbl_tr_e ;
				}
				
				//tabellen-abschluss
				$this->table.=$this->table_end;
		}
		
}

?>

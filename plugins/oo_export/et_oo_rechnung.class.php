<?php

/***********************************************************************
 * 
 * eTempus-Plugin Rechnungsexport
 * v0.3: OpenOffice.org und MS Word final
 * (c) 2009 Cyrill v.Wattenwyl, protagonist.ch < 5@e23.ch >
 * Alle Rechte vorbehalten
 * 
 **********************************************************************/


class et_oo_rechnung extends etempus {
		
		public $html;
		
		/***********************************
		 * config laden
		 * ********************************/
		 		
		function __construct(){
				//config-keys erstellen falls nicht vorhanden
				$conf = new et_config;
				if (!$conf->load_key("oo_rechnung_titel")){ $conf->make_key("oo_rechnung_titel","eTempus-Rechnung"); }
				if (!$conf->load_key("oo_rechnung_text")){ $conf->make_key("oo_rechnung_text"," "); }
				if (!$conf->load_key("oo_rechnung_footer")){ $conf->make_key("oo_rechnung_footer","generated by etempus"); }
				if (!$conf->load_key("oo_rechnung_pos_logo_left")){ $conf->make_key("oo_rechnung_pos_logo_left","15cm"); }
				if (!$conf->load_key("oo_rechnung_pos_logo_top")){ $conf->make_key("oo_rechnung_pos_logo_top","1cm"); }
				if (!$conf->load_key("oo_rechnung_pos_addr_left")){ $conf->make_key("oo_rechnung_pos_addr_left","15cm"); }
				if (!$conf->load_key("oo_rechnung_pos_addr_top")){ $conf->make_key("oo_rechnung_pos_addr_top","8cm"); }
				$this->uid=($this->get_user_id());
		}
		
		
		/*********************************
		 *  einhängepunkt
		 * *******************************/
		
		function build($message=false){
				$js1 = "onclick=\"lade_projekt_liste(this.value);\" id=\"kid\"";
				$js_loader="lade_projekt_liste($('kid').value,true);";
				$msg="";
				//auswerten
				if ($this->getvar("make")=="true"){
						$this->auswerten();
						return;
				} 
				$arr = array("kunde_dd_liste"	=>	$this->kunden_dd_liste(false,$js1,false),
							 "monate_dd_liste"	=>	$this->letzte_monate_dd(),
							 "letzte_jahre_dd"	=>	$this->letzte_jahre_dd(),
							 "msg"				=>	$msg,
							 "js_loader"		=>	$js_loader, );
				$tpl=new template($this->dir);
				$this->html=$tpl->go("site.tpl",$arr);
		}
		
		
		/************************************
		 *  auswerten
		 * **********************************/
		
		function auswerten(){
				if  ($this->getvar("alles")=="true"){
						if ($this->getvar("go")=="true" and $this->getvar("cb_go"))
							$this->alles_auswerten_go();
						else
							$this->alles_auswerten();
				} else { 
						
						switch ($_REQUEST['data_type']){
								
								case "doc":
									$this->type="doc";
									$this->make_doc_file();
									break;
						
								default:
									$this->type="odt";
									$this->make_odt_file();
									break;
						}
				}
		}
		
		
		/************************************
		 *  alles auswerten dialog
		 * *********************************/
		
		function alles_auswerten(){

				
				include("tpl_misc.php");
				//timestamp herausfinden
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
				
				//array mit allen pid's von diesem zeitraum erstellen
				$res = $this->db->query("SELECT DISTINCT projekt_id FROM zeit WHERE zeit_start>={$ts_von} AND zeit_ende<={$ts_bis};");
				$pid_arr=array();
				while ($r=$res->fetchRow()) array_push($pid_arr,$r[0]);
				
				//belege
				$res1 = $this->db->query("SELECT DISTINCT projekt_id FROM beleg WHERE zeit>={$ts_von} AND zeit<={$ts_bis};");
				while ($r=$res1->fetchRow()){
						if (array_search($r[0],$pid_arr)===false){
								array_push($pid_arr,$r[0]);
						}
				}
				
				
				//wenn kundenbasierte abrechnung
				if ($this->getvar("art")=="kunde"){
						$kid_arr=$kid_arr0=array();
						foreach ($pid_arr as $projekt){
								$kid=$this->pid_to_kid($projekt);
								$kid_arr0[$kid]=$kid;
						}
						foreach ($kid_arr0 as $v) array_push($kid_arr,$v);
						$t=new template;
						$liste=$t->go($tpl['liste_kunde_h'],false,true);
						$trc='dark';
						foreach ($kid_arr as $kunde){
								$trc = ($trc=='light') ? 'dark' : 'light';
								$knam = utf8_encode($this->kunde_name($kunde));
								$liste.=$t->go($tpl['liste_kunde'],array("db_nam"=>$knam,"db_val"=>$kunde,"trc"=>$trc),true);
						}
						
				//wenn projektbasierte abrechnung
				} else {
						$t=new template;
						$liste=$t->go($tpl['liste_projekt_h'],false,true);
						$trc='dark';
						foreach ($pid_arr as $projekt){	
								$trc = ($trc=='light') ? 'dark' : 'light';
								$kid = $this->pid_to_kid($projekt);
								$knam = utf8_encode($this->kunde_name($kid));
								$pnam = utf8_encode($this->projekt_name($projekt));
								$liste.=$t->go(	$tpl['liste_projekt'],
												array("db_pnam"=>$pnam,"db_knam"=>$knam,"db_val"=>$projekt,"trc"=>$trc),
												true);
						}
				}
				
				//einstellungen von vorherigem request kopieren
				$r_arr = array('make','kunde','art','projekt','zeitraum_typ','monat','jahr','von','bis','s_datum','s_zeit','s_projekt','s_kommentar','s_user','s_ansatz','s_kosten','mwst','mwst_satz','r_totalzeit','alles','data_type');
				$req_copy = $this->copy_request($r_arr);
				
				//seite ausgeben
				$arr = array(	"req_str"	=> $req_copy,
								"db_liste"	=> $liste  		);
				$tpl=new template($this->dir);
				$this->html=$tpl->go("auswertung_alles.tpl",$arr);
		}
		
		
		/**************************************
		 *  alles auswerten  - archiv erstellen und ausgeben
		 * **********************************/
		
		function alles_auswerten_go(){
				
				$this->duid = uniqid();
				$dir=$this->dir."tmp/".$this->duid;
				mkdir($dir);
				
				//kundenbasiert
				if ($this->getvar("art")=="kunde"){
						foreach ($this->getvar("cb_go",true) as $kunde){
								$knam = $this->kunde_name($kunde);
								$_REQUEST['kunde']=$kunde;
								switch ($_REQUEST['data_type']){
									case "doc":
										$this->type="doc";
										$filename=sprintf(__("oo_export_fname_d"),$this->dir_name($knam)."_-_".$this->dir_name($pnam),date("Y-m-d"));
										$this->make_doc_file(false,$dir."/".$filename);
										break;
							
									default:
										$this->type="odt";
										$filename=sprintf(__("oo_export_fname"),$this->dir_name($knam)."_-_".$this->dir_name($pnam),date("Y-m-d"));
										$this->make_odt_file(false,$dir."/".$filename);
										break;
								}
								
						}
				}
				//projektbasiert
				if ($this->getvar("art")=="projekt"){
						foreach ($this->getvar("cb_go",true) as $projekt){
								$kunde=$this->pid_to_kid($projekt);
								$pnam=$this->projekt_name($projekt);
								$knam = $this->kunde_name($kunde);								
								
								$_REQUEST['kunde']=$kunde;
								$_REQUEST['projekt']=$projekt;
								switch ($_REQUEST['data_type']){
									case "doc":
										$filename=sprintf(__("oo_export_fname_d"),$this->dir_name($knam)."_-_".$this->dir_name($pnam),date("Y-m-d"));
										$this->make_doc_file(false,$dir."/".$filename);
										break;
							
									default:
										$filename=sprintf(__("oo_export_fname"),$this->dir_name($knam)."_-_".$this->dir_name($pnam),date("Y-m-d"));
										$this->make_odt_file(false,$dir."/".$filename);
										break;
								}
						}
				}
				
				//ausgeben
				$flist = self::listdir($dir);
				$zip=new zipfile; 
				foreach ($flist as $file){
						$zip_path = str_replace($dir."/","",$file);
						$zip->addFile(file_get_contents($file),$zip_path,time());
				}

				//datei ausgeben und ende
				$fname = sprintf(__("oo_export_fname_all"),date("Y-m-d"));
				header("Content-type: application/zip");
				header("Content-Disposition: attachment; filename=\"{$fname}\"");
				echo $zip->file();
				self::unlink_recursive($this->dir."tmp/",false);
				die;				
		}
		
		
		
		/*************************************
		 * 
		 * openoffice-datei erstellen
		 * (xml)
		 * 
		 * ***********************************/
		
		function make_odt_file($out=true,$fname=false){
				
				$et = new etempus_auswertung;
				$et->auswerten(true);
				
				//template-dateien in temporärer ordner kopieren
				$this->uid = uniqid();
				mkdir($this->dir."tmp/".$this->uid);
				self::recurse_file_copy($this->dir."odt_tpl",$this->dir."tmp/".$this->uid);
				
				//logo kopieren
				copy($this->dir."../oo_export_conf/img/logo.jpg",$this->dir."tmp/".$this->uid."/Pictures/logo.jpg");
				
				//kundenadresse zuasmmenflicken:
				$txt = '<text:span text:style-name="T2">%s</text:span>';
				$txt_b	= '<text:span text:style-name="T3">%s</text:span>';
				$br = '<text:line-break/>';
				
				
				$kunde = $this->table_item_array("kunden",$this->getvar("kunde"));
				$addr = sprintf($txt_b,$this->xml_escape_string($kunde["name"])).$br;
				if ($arr["ansprechpartner"]) $addr.= sprintf($txt,$this->xml_escape_string($kunde["ansprechpartner"])).$br;
				$addr.= sprintf($txt,$this->xml_escape_string($kunde["adresse"])).$br;
				$addr.= sprintf($txt,$this->xml_escape_string($kunde["plz"])."  ".$this->xml_escape_string($kunde["ort"]));				
				
				// tabellen bauen
				$table_long = $this->build_big_table($et);
				$table_short= $this->build_short_table($et);
				
				// xml-bauen
				$conf=new et_config;
				$tpl = new template($this->dir);
				$content_array = array(	"table_long"		=> $table_long,
										"table_short"		=> $table_short,
										"createdate"		=> sprintf(__("oo_export_created"),date("d.m.Y")),
										"adresse_kunde"		=> $addr,
										"logo_pos_x"		=> $conf->load_key("oo_rechnung_pos_logo_left"),
										"logo_pos_y"		=> $conf->load_key("oo_rechnung_pos_logo_top"),
										"addr_pos_x"		=> $conf->load_key("oo_rechnung_pos_addr_left"),
										"addr_pos_y"		=> $conf->load_key("oo_rechnung_pos_addr_top"),
										"titel"				=> $this->xml_escape_string($conf->load_key("oo_rechnung_titel")),
										"text"				=> $this->xml_escape_string($conf->load_key("oo_rechnung_text")),
										
										);
										
				// 
				$style_array=array(		"footer"			=> $this->xml_escape_string($conf->load_key("oo_rechnung_footer")))	;					
				
				//xml schreiben
				$content_xml = $tpl->go("tmp/{$this->uid}/content.xml",$content_array);
				file_put_contents($this->dir."tmp/{$this->uid}/content.xml",$content_xml);
				$style_xml = $tpl->go("tmp/{$this->uid}/styles.xml",$style_array);
				file_put_contents($this->dir."tmp/{$this->uid}/styles.xml",$style_xml);
				
				//zip-archiv erstellen
				$flist = self::listdir($this->dir."tmp/{$this->uid}");
				$zip=new zipfile; 
				foreach ($flist as $file){
						$zip_path = str_replace($this->dir."tmp/{$this->uid}/","",$file);
						$zip->addFile(file_get_contents($file),$zip_path,time());
				}

				//datei ausgeben und ende
				if ($out){
						$fname = sprintf(__("oo_export_fname"),$kunde['name'],date("Y-m-d"));
						header("Content-type: application/vnd.oasis.opendocument.text");
						header("Content-Disposition: attachment; filename=\"{$fname}\"");
						echo $zip->file();
						self::unlink_recursive($this->dir."tmp/",false);
						die;
				}
				//datei speichern 
				elseif ($fname){
						@file_put_contents($fname,$zip->file());
				}
		}
		
		
		
		/**************************************************
		 * 
		 * micro$oft-word-doc-export 
		 * (html-like)
		 * 
		 **************************************************/ 	
		 	
		 function make_doc_file($out=true,$fname=false){
			 
				$conf=new et_config;
				$uid=uniqid();
				
				//kundenadresse zuasmmenflicken:
				$txt = '<span>%s</span>';
				$txt_b	= '<b>%s</b>';
				$br = '<br />';
				$page_break="<br clear=all style='mso-special-character:line-break;page-break-before: always'>";
				
				$kunde = $this->table_item_array("kunden",$this->getvar("kunde"));
				$addr = sprintf($txt_b,$this->xml_escape_string($kunde["name"])).$br;
				if ($arr["ansprechpartner"]) $addr.= sprintf($txt,$this->xml_escape_string($kunde["ansprechpartner"])).$br;
				$addr.= sprintf($txt,$this->xml_escape_string($kunde["adresse"])).$br;
				$addr.= sprintf($txt,$this->xml_escape_string($kunde["plz"])."  ".$this->xml_escape_string($kunde["ort"]));				

				//auswerten
				$et = new etempus_auswertung;
				$et->auswerten(true);
				$table_short= $this->build_short_table($et);
				$table_long = $this->build_big_table($et);
				$titel= $this->xml_escape_string($conf->load_key("oo_rechnung_titel"));
				$text= $this->xml_escape_string($conf->load_key("oo_rechnung_text"));
				
				//doc-zeugs
				$fn = sprintf(__("oo_export_fname_d"),$kunde['name'],date("Y-m-d"));
				$bound = '==_NextPart_'.md5($uid);
				
				
				//logo zu base64
				$logo_d = file_get_contents($this->dir."../oo_export_conf/img/logo.jpg");
				$logo_data = chunk_split(base64_encode($logo_d));
				
				//doc-datei erstellen
				$content_array = array( "bound"				=> $bound,
										"uid"				=> $uid,
										"table_long"		=> $table_long,
										"table_short"		=> $table_short,
										"createdate"		=> sprintf(__("oo_export_created"),date("d.m.Y")),
										"adresse_kunde"		=> $this->sonderz($addr),
										"logo_pos_x"		=> (str_replace("cm","",$conf->load_key("oo_rechnung_pos_logo_left"))+3 . "cm"),
										"logo_pos_y"		=> $conf->load_key("oo_rechnung_pos_logo_top"),
										"addr_pos_x"		=> $conf->load_key("oo_rechnung_pos_addr_left"),
										"addr_pos_y"		=> $conf->load_key("oo_rechnung_pos_addr_top"),
										"titel"				=> $this->xml_escape_string($conf->load_key("oo_rechnung_titel")),
										"text"				=> $this->xml_escape_string($conf->load_key("oo_rechnung_text")),
										"foot"				=> $this->xml_escape_string($conf->load_key("oo_rechnung_footer")),
										"logo_base64_data"	=> $logo_data,);
										
				$tpl=new template($this->dir);
				$doc = $tpl->go("doc_tpl/word_document.tpl",$content_array);
				
				//dirty fix
				$doc=$this->sonderz($doc);

				
	
				
				//ausgeben oder speichern
				if ($out){
						$fname = sprintf(__("oo_export_fname_d"),$kunde['name'],date("Y-m-d"));
						header("Content-type: application/application/vnd.ms-word");
						header("Content-Disposition: attachment; filename=\"{$fname}\"");
						echo $doc;
						die;
				} elseif ($fname){
						@file_put_contents($fname,$doc);
				}
		}
		
		
		
		
		
		
		
		/*****************************
		 *  kurze tabelle (seite 1) erstellen
		 * ***************************/
		
		function build_short_table($et_obj){
				
				$cols = 3;
				switch ($_REQUEST['data_type']){
						
						case "doc":	
							$this->type="doc";
							$br = '<br />';
							$tbl = new doc_table($cols);
							break;
							
						case "odt": default: 
							$this->type="odt";
							$br = '<text:line-break/>';
							$tbl = new oo_table($cols);
							break;
				}
				
				
			    //daten auslesen
				if (is_array($et_obj->res_arr)){
						foreach ($et_obj->res_arr as $item){
								$arr[$item['projekt_id']]['kosten']	= $arr[$item['projekt_id']]['kosten']   + $item['kosten'];
								$arr[$item['projekt_id']]['sekunden'] = $arr[$item['projekt_id']]['sekunden'] + $item['sekunden'];
								$total_kosten=$total_kosten+$item['kosten'];
								$sekunden=$sekunden+$item['sekunden'];
						}
				}
				if (count($et_obj->res_arr_beleg)>0 and $et_obj->res_arr_beleg){		
						foreach ($et_obj->res_arr_beleg as $item){
								$arr[$item['projekt_id']]['kosten']	= $arr[$item['projekt_id']]['kosten']   + $item['betrag'];
								$total_kosten=$total_kosten+$item['betrag'];
						}
				}
				//tabellen-header
				$tbl->add_row();
				$tbl->add_cell($br.__("projekt_s").$br,true);
				$tbl->add_cell($br.__("zeit").$br,true);
				$tbl->add_cell($br.__("kosten").$br,true);
				$tbl->end_row();	
				//tabelle datenteil 
				if ($arr){
				foreach ($arr as $key=>$item){
						//cleanen für xml
						$projekt = utf8_encode(htmlspecialchars($this->projekt_name($key)));
						$tbl->add_row();
						$tbl->add_cell($projekt);
						$tbl->add_cell($this->formatiere_zeit($item['sekunden']));
						$tbl->add_cell($item['kosten']);
						$tbl->end_row();
				}
				}
				//totalzeit
				if ($this->getvar('r_totalzeit')){
						$total_time = $this->formatiere_zeit($sekunden);	
						$tbl->add_row();
						$tbl->add_cell( $br. __("totalzeit"));
						$tbl->add_cell( $br. $total_time);
						$tbl->add_cell("");
						$tbl->end_row();
				}
				//mwst
				if ($this->getvar('mwst')){
						$mwst_total = round(($total_kosten/100)*$this->getvar('mwst_satz'),2);
						$mwst_total = number_format(round($mwst_total*20)/20,2,".","");
						$tbl->add_row();
						$tbl->add_cell( $br. __("mwst") ." (".$this->getvar("mwst_satz")." %)");
						$tbl->add_cell("");
						$tbl->add_cell( $br. $mwst_total);
						$tbl->end_row();
				}
				//total-kosten
				$tbl->add_row();
				$tbl->add_cell( $br. __("total") . $br , true , true);
				$tbl->add_cell("",true,true);
				$tbl->add_cell( $br. $total_kosten , true ,true);
				$tbl->end_row();
				//ende und ausgeben	
				$tbl->end_table();
				return $tbl->table;
		}
		
		
		
		/***********************************
		 * lange tabelle (folgeseiten) erstellen
		 * ********************************/
		
		function build_big_table($et_obj){
		
				$br = '<text:line-break/>';
				
				
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
				
				
				//odt/doc
				switch ($_REQUEST['data_type']){
						
						case "doc":
							$br = '<br />';
							$tbl = new doc_table($td_rows);
							break;
							
						case "odt": default: 
							$br = '<text:line-break/>';
							$tbl = new oo_table($td_rows);
							break;
				}


								
				//tabellen-kopf
				$tbl->add_row();
				if ($this->getvar('s_datum')) 		$tbl->add_cell($br.__("datum").$br,true);
				if ($this->getvar('s_zeit')) 		$tbl->add_cell($br.__("zeit").$br,true);
				if ($this->getvar('s_projekt')) 	$tbl->add_cell($br.__("projekt_s").$br,true);
				if ($this->getvar('s_kunde')) 		$tbl->add_cell($br.__("kunde_s").$br,true);
				if ($this->getvar('s_kommentar')) 	$tbl->add_cell($br.__("kommentar").$br,true);	
				if ($this->getvar('s_user')) 		$tbl->add_cell($br.__("benutzer").$br,true);
				if ($this->getvar('s_ansatz')) 		$tbl->add_cell($br.__("ansatz").$br,true);
				if ($this->getvar('s_kosten')) 		$tbl->add_cell($br.__("kosten").$br,true);
				$tbl->end_row();
				
				//datenteil zeiteinträge
				$total_kosten=0;
				$sekunden=0;
				if (is_array($et_obj->res_arr)){
						foreach ($et_obj->res_arr as $item){
								//cleanen für xml
								foreach ($item as $k=>$v) $item[$k]=utf8_encode(htmlspecialchars($v));
								$tbl->add_row();
								if ($this->getvar('s_datum'))		$tbl->add_cell($item['tdesc'] );
								if ($this->getvar('s_zeit'))		$tbl->add_cell($item['zeit_formatiert'] );
								if ($this->getvar('s_projekt'))		$tbl->add_cell($item['projekt']);
								if ($this->getvar('s_kunde'))		$tbl->add_cell($item['kunden_name']);								
								if ($this->getvar('s_kommentar'))	$tbl->add_cell($item['beschreibung']);	
								if ($this->getvar('s_user'))		$tbl->add_cell($item['user_name']);
								if ($this->getvar('s_ansatz'))		$tbl->add_cell($item['ansatz']);
								if ($this->getvar('s_kosten'))		$tbl->add_cell($item['kosten']);
								$tbl->end_row();
								$total_kosten=$total_kosten+$item['kosten'];
								$sekunden=$sekunden+$item['sekunden'];
						}
				}
				
				//datenteil belege
				if (count($et_obj->res_arr_beleg)>0 and $et_obj->res_arr_beleg){		
						$tbl->add_row();
						$tbl->add_cell( $br. __("belege") .$br, true,false,$td_rows-1);
						$tbl->add_cell( "" );
						$tbl->end_row();
						foreach ($et_obj->res_arr_beleg as $item){
								//cleanen für xml
								foreach ($item as $k=>$v) $item[$k]=utf8_encode(htmlspecialchars($v));
								$tbl->add_row();
								if ($this->getvar('s_datum'))		$tbl->add_cell($item['tdesc']);
								if ($this->getvar('s_zeit'))		$tbl->add_cell("");
								if ($this->getvar('s_projekt'))		$tbl->add_cell($item['projekt']);
								if ($this->getvar('s_kunde'))		$tbl->add_cell($item['kunden_name']);								
								if ($this->getvar('s_kommentar'))	$tbl->add_cell($item['beschreibung']);	
								if ($this->getvar('s_user'))		$tbl->add_cell($item['user_name']);
								if ($this->getvar('s_ansatz'))		$tbl->add_cell("");
								if ($this->getvar('s_kosten'))		$tbl->add_cell($item['betrag']);
								$tbl->end_row();
								$total_kosten=$total_kosten+$item['betrag'];
						}
				}		
							
				//reihe totalzeit	
				if ($this->getvar('r_totalzeit')){
						$total_time = $this->formatiere_zeit($sekunden);	
						$tbl->add_row();
						$tbl->add_cell( $br. __("totalzeit") .$br, true,false,$td_rows-1);
						$tbl->add_cell( $br. $total_time .$br, true,false);
						$tbl->end_row();
				}		
				
				//folgende reihen nur ausgeben wenn kosten ausgewählt sind
				if ($this->getvar('s_kosten')){
						//reihe mwst
						if ($this->getvar('mwst')){
							$mwst_total = round(($total_kosten/100)*$this->getvar('mwst_satz'),2);
							$mwst_total = number_format(round($mwst_total*20)/20,2,".","");
							$total_kosten = $mwst_total+$total_kosten;
							$tbl->add_row();
							$tbl->add_cell( $br. __("mwst") ." (".$this->getvar("mwst_satz")." %)" .$br, false,false,$td_rows-1);
							$tbl->add_cell( $br. $mwst_total .$br, true, false );
							$tbl->end_row();
						}
						//reihe total-kosten
						$tbl->add_row();
						$tbl->add_cell( $br. __("total") . $br, true, true, $td_rows-1);
						$tbl->add_cell( $br. $total_kosten .$br, true, true );
						$tbl->end_row();
				}
				
				//tabellen-abschluss
				$tbl->end_table();
				return $tbl->table;
		}
		
		
		
		
		
		
		// ordner und alles darin kopieren
		static function recurse_file_copy($src,$dst) {
			$dir = opendir($src);
			@mkdir($dst);
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
				    if ( is_dir($src . '/' . $file) ) {
				        self::recurse_file_copy($src . '/' . $file,$dst . '/' . $file);
				    }
				    else {
				        copy($src . '/' . $file,$dst . '/' . $file);
				    }
				}
			}
			closedir($dir);
		}
		
		
		//verhält sich wie rm -rf
		static function unlink_recursive($dir, $delete_root=false){
			if(!$dh = @opendir($dir)){
				return;
			}
			while (false !== ($obj = readdir($dh))){
				if($obj == '.' || $obj == '..'){
				    continue;
				}

				if (!@unlink($dir . '/' . $obj)){
				    self::unlink_recursive($dir.'/'.$obj, true);
				}
			}
			closedir($dh);
			if ($delete_root){
				@rmdir($dir);
			}
			return;
		}
		
		
		//ordnerstruktur rekursiv in array laden
		static function listdir($start_dir='.') {
			  $files = array();
			  if (is_dir($start_dir)) {
					$fh = opendir($start_dir);
					while (($file = readdir($fh)) !== false) {
						  if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
						  $filepath = $start_dir . '/' . $file;
						  if ( is_dir($filepath) )
							$files = array_merge($files, self::listdir($filepath));
						  else
							array_push($files, $filepath);
					}
					closedir($fh);
			  } else {
					$files = false;
			  }
			  return $files;
		}
		
		//cleanen für xml
		function xml_escape_string($string){
				
				$string=$this->autoencode($string);
				if ($this->type=="odt"){
					return (htmlspecialchars($string));
				} else {
					$string=($string);
					return $string; //$this->extrawurst(utf8_decode($string));
				}
		}
		
		
		function autoencode($string){
        		if ($this->check_utf8($string)){ return ($string); }
                return utf8_encode($string);
        }
                
        function check_utf8($str) {
                $len = strlen($str);
                for($i = 0; $i < $len; $i++){
                      $c = ord($str[$i]);
                      if ($c > 128) {
                           if (($c > 247)) return false;
                           elseif ($c > 239) $bytes = 4;
                           elseif ($c > 223) $bytes = 3;
                           elseif ($c > 191) $bytes = 2;
                           else return false;
                           if (($i + $bytes) > $len) return false;
                           while ($bytes > 1) {
                               $i++;
                               $b = ord($str[$i]);
                               if ($b < 128 || $b > 191) return false;
                                  $bytes--;
                               }
                           }
                        }
                        return true;
          }
		
		
		
		function sonderz($string){
				$string=str_replace("ö","&ouml;",$string);
				$string=str_replace("ä","&auml;",$string);
				$string=str_replace("ü","&uuml;",$string);
				$string=str_replace("Ä","&Auml;",$string);
				$string=str_replace("Ü","&Uuml;",$string);
				$string=str_replace("Ö","&Ouml;",$string);
				return $string;
		}
		
}
?>

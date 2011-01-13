<?php

class etempus_zeit_ajax extends etempus {
		
		public $layout=false;
		
		
		function index(){
				die('nothing to do');
		}
		
		
		
		/****************************
		 * strtotime für js 
		 ****************************/
		function str_to_time() {
				echo strtotime($_REQUEST['ts']);
		}
		
		function time_manual(){
				$start=strtotime($_REQUEST['date_s']);
				$stop =strtotime($_REQUEST['date_e']);
				$secounds=$stop-$start;
				echo $secounds;
		}
		
		//
		// letzte buchungszeit
		//
		function get_last_book_time(){
				$arr=$this->query_array("SELECT * FROM zeit WHERE user_id={$this->userid} ORDER BY id DESC LIMIT 1");
				//$this->pdd($arr[0]["zeit_ende"]);
				echo date("d.m.Y||H:i:s", $arr[0]["zeit_ende"]);
		}
		
		
		/****************************
		 * xml-struktur aller kunden/projekte
		 ***************************/
		function klist(){
				header ("content-type: text/xml");
				echo '<?xml  version="1.0" encoding="utf-8" ?>';
				echo "<liste>";
				$karr=$this->utf8_array_encode($this->table_array("kunden","upper(name)"));
				
				foreach ($karr as $kunde){
						echo "<kunde>";
						
						echo "<name>{$kunde['name']}</name><id>{$kunde['id']}</id><projekte>";
						
						foreach($this->kunden_projekte($kunde['id']) as $proj){
								$proj=$this->utf8_array_encode($proj);
								echo "<projekt><proj_name>{$proj['name']}</proj_name><proj_id>{$proj['id']}</proj_id></projekt>";
								//$proj_a[]=array(	"name"=>$proj['name'], "id"=>$proj['id']  );
						}
						
						echo "</projekte></kunde>";
				}
			echo "</liste>";
		}
		
		
		/*****************************
		 * letzte/meist verwendete
		 *****************************/
		function lastmost(){
				$uid=$this->get_user_id();
				//anzahl elemente
				$count=4;
				
				header ("content-type: text/xml");
				echo '<?xml  version="1.0" encoding="utf-8" ?>';
				echo "<liste>";
			
				//zuletzt verwendete
				$last_proj = $this->query_array('SELECT DISTINCT projekt_id FROM zeit WHERE user_id='.$uid.' ORDER BY id DESC LIMIT '.$count);
				foreach ($last_proj as $proj){
						$id=$proj['projekt_id'];
						echo "<last_proj><name>".utf8_encode($this->projekt_name($id)).
							 "</name><id>".$id."</id><kunde>".utf8_encode($this->kunde_name($this->pid_to_kid($id)))."</kunde></last_proj>";
				}
				
				//meist verwendete
				$days = 321;
				$prev = time() - (86400*$days);
				$res = $res=$this->db->query("SELECT projekt_id  FROM zeit WHERE zeit_start>=$prev AND user_id=$uid");
				while ($row=$res->fetchRow()){
						$r[$row[0]]++;
				}
				arsort($r);
				if ($r){
						$i=0;
						foreach ($r as $id=>$num) {
								if ($i==$count){ break; }
								echo "<most_proj><name>".utf8_encode($this->projekt_name($id)).
									 "</name><id>".$id."</id><kunde>".utf8_encode($this->kunde_name($this->pid_to_kid($id)))."</kunde></most_proj>";
								$i++;
						}
				}
				echo "</liste>";
		}
		
		
		
		
		/****************************
		 * javascript sprachhdatei
		 ***************************/
		function js_lang(){
				
		
				echo "etempus.comment_required=".((ET_COMMENT_REQUIRED) ? 'true' : 'false').";\n";
				
				
				include("text/text.{$_SESSION['lang']}.php");
				echo "etempus.lang=new Object();\n";
				echo "etempus.locale='{$_SESSION['lang']}';\n";
				foreach ($txt as $name=>$string){
						if (is_array($string)) { 
								$string=$this->phpArrayToJsArray($string); 
								echo "etempus.lang.{$name}={$string}\n";
						} else {
							echo "etempus.lang.{$name}=\"{$string}\";\n";
						}
				}
				
				//locales für datepicker
				echo "\njQuery(function($){
						$.datepicker.regional['{$_SESSION['lang']}'] = {
							closeText: '".__("close")."',
							prevText: '".__("prev")."',
							nextText: '".__("next")."',
							currentText: '".__("cal_today")."',
							monthNames: ".$this->phpArrayToJsArray(__("monate")).",
							monthNamesShort: ".$this->phpArrayToJsArray(__("monate_short")).",
							dayNames: ".$this->phpArrayToJsArray(__("tage_array")).",
							dayNamesShort: ".$this->phpArrayToJsArray(__("tage_short")).",
							dayNamesMin: ".$this->phpArrayToJsArray(__("tage_short")).",
							weekHeader: '".__("cal_week_header")."',
							dateFormat: '".__("cal_date_format")."',
							firstDay: 1,
							isRTL: ".__("cal_is_rtl").",
							showMonthAfterYear: false,
							yearSuffix: ''};
						$.datepicker.setDefaults($.datepicker.regional['{$_SESSION['lang']}']);
					});";
					
				
				
		}
		
		
		protected function phpArrayToJsArray($arr){
				if (!is_array($arr)) { return 'false';}
				$ret="[ " ;
				foreach ($arr as $item){ $ret.="'{$item}',"; }
				$ret=substr($ret,0,-1);
				$ret.=" ]";
				return $ret;
		}
		
		
		/**************************
		 ansatzliste dd von proj
		***************************/
		function ansatzliste(){
				if ($this->getvar("projekt_id")=="") die;
				if (!$this->check_id("projekte",$this->getvar("projekt_id"))) die;
				$arr = $this->table_item_array("projekte",$this->getvar("projekt_id",true));
				
				$ret="<select name='ansatz' id='dd_ansatz'>";
				foreach ($this->table_array("ansatz","name") as $ansatz){
						if ($this->match_num_list($ansatz['id'],$arr['ansatz']) or $arr['ansatz']=="*"){
								$ansatz=$this->utf8_array_encode($ansatz);
								$chk=($_REQUEST['chk']==$ansatz['id']) ? " selected " : " ";
								$ret.="<option{$chk}value='{$ansatz['id']}'>{$ansatz['name']}</option>";
						}
				}
				
				$ret.="</select>";
				echo $ret;
		}
		
		
		
		/**************************
		 projektliste dd von projekte
		***************************/
		function projektliste(){
				//kunden-id prüfen
				if (!$this->check_id("kunden",$this->getvar("kunden_id"))) { die; }
				if ($this->getvar("kunden_id")=="") die;
				$ret = "<select name='projekt' onchange=\"".stripslashes($this->getvar("onchange"))."\" id='dd_pid'>";
				if ($this->getvar("showall")){
					$ret.="<option value=\"*\">".__("alle_projekte")."</option>";
				}
				
				foreach ($this->kunden_projekte($this->getvar("kunden_id")) as $projekt){
						$projekt=$this->utf8_array_encode($projekt);
						$chk=" ";
						if ($projekt['id']==$_REQUEST['chk']){ $chk=" selected "; }
						$ret.="<option{$chk}value='{$projekt['id']}'>{$projekt['name']}</option>";
				}
				$ret.="</select>";
				echo $ret;
		}
		
		
		/**************************
		 projektliste dd von kunden
		***************************/
		function kundenliste(){
				//kunden-id prüfen
				$ret = "<select name='kunde' onchange='".stripslashes($this->getvar("onchange"))."' id='dd_kid'>";
				if ($this->getvar("showall")){
					$ret.="<option value=\"*\">".__("alle_projekte")."</option>";
				}
				
				foreach ($this->table_array("kunden","name") as $kunde){
						$kunde=$this->utf8_array_encode($kunde);
						$chk=" ";
						if ($kunde['id']==$_REQUEST['chk']){ $chk=" selected "; }
						$ret.="<option{$chk}value='{$kunde['id']}'>{$kunde['name']}</option>";
				}
				$ret.="</select>";
				echo $ret;
		}
		
		
		/************************
		 * letzte zeiteinträge
		 ************************/
		
		function zeit_last(){
				$days=($_REQUEST['days']) ? $_REQUEST['days'] : 3;
				$res=$this->db->query("SELECT * FROM zeit WHERE user_id={$this->userid} ORDER BY zeit_start DESC");
				$arr=array();
				$arr_res=array();
				$i=0;
				$day_last="";
				$day_count=0;
				while ($row=$res->fetchRow(DB_FETCHMODE_ASSOC)){
						foreach ($row as $key=>$var){
							$arr[$i][$key]=$var;
						}
						$d=date("dmY",$arr[$i]['zeit_start']);
						$arr[$i]['day']=$d;
						if ($d!=$day_last){
							$day_last=$d;
							$day_count++;
							if ($day_count==($days+1)){ break; }
						}
						$arr_res[$d]['total'] = $arr_res[$d]['total'] + $row['zeit_ende']-$row['zeit_start'];
						$arr_res[$d][]=$arr[$i];
						$i++;
				}
				//xml erstellen
				header ("content-type: text/xml");
				echo '<?xml  version="1.0" encoding="utf-8" ?>';
				echo "<liste>";
					foreach ($arr_res as $day=>$a){
							echo "<day><total>".$this->formatiere_zeit_csv($a['total'])."</total>";
							foreach ($a as $item){
									if ($item['id']){
										echo "<item>";
										echo "<id>{$item['id']}</id>";
										echo "<projekt_id>{$item['projekt_id']}</projekt_id>";
										echo "<projekt>".xmlspecialchars(utf8_encode($this->projekt_name($item['projekt_id'])))."</projekt>";
										echo "<ansatz_id>{$item['ansatz_id']}</ansatz_id>";
										echo "<ansatz>".xmlspecialchars(utf8_encode($this->ansatz_name($item['ansatz_id'])))."</ansatz>";
										echo "<kommentar>".xmlspecialchars(utf8_encode($item['beschreibung']))."</kommentar>";
										echo "<zeit>".$this->formatiere_zeit_csv($item['zeit_ende']-$item['zeit_start'])."</zeit>";
										echo "<datum>".date("d.m.Y",$item['zeit_start'])."</datum>";
										echo "<kunden_id>".$this->pid_to_kid($item['projekt_id'])."</kunden_id>";
										echo "</item>";
										
									}
							}
							echo "</day>";
					}	
				echo "</liste>";	
			
		}
		
		
		/*******************************
		 * zeiteintrag edit auf tabelle speichern
		 ******************************/
		
		function table_time_edit_save(){
				//misc
				$tarr=explode(",",$_REQUEST['id_data']);
				$eintrag_id=$tarr[1];
				$old=$this->zeiteintrag($eintrag_id);
				$ts_start=$old['zeit_start'];
				
				//datum
				$dif = floor((strtotime($this->getvar('datum'))-$ts_start)/86400)+1;
				$ts_start= strtotime("+$dif days",$ts_start);
				
				//ts
				$za=explode(":",$this->getvar('zeit'));
				$ts_ende=$ts_start+(($za[0]*60)*60)+($za[1]*60);
				echo $ts_ende-$ts_start;
				//var_dump(date("d.m.Y",$ts_start));
				
				//save
				if (is_numeric($ts_start) and is_numeric($ts_ende)){
						$query="UPDATE zeit SET zeit_start='$ts_start',
												zeit_ende='$ts_ende',
												projekt_id='".$this->getvar('projekt',true)."',
												ansatz_id='".$this->getvar('ansatz',true)."',
												beschreibung='".utf8_decode($this->getvar('kommentar',true))."'
								WHERE id={$eintrag_id}";
								
						$this->db->query($query);	
						echo $query;	
				} else {
					var_dump($_REQUEST);
				}	
				
				
			
		}
		
		/********************************
		 * zeiteinträge löschen 
		 *******************************/
		
		function zeit_eintrag_del(){
				// frage
				if ($_REQUEST['step']!="2"){
						$_SESSION['multidel_items']=$_REQUEST['item'];
						echo $this->frage(__('confirm_del'), 
										  sprintf(__('zeit_eintrag_multidel'),count($_REQUEST['item'])), 
										  "", 
										  "");
				}
				//löschen
				else {
						if($_SESSION['multidel_items']){
								// einzelne items löschen
								foreach	($_SESSION['multidel_items'] as $item){
										//prüfen, ob eintrag user gehört
										$eintrag=$this->zeiteintrag($item);
										if ($eintrag['user_id']==$this->userid){
												//wenn ja löschen
												$this->db->query("DELETE FROM zeit WHERE id='{$item}';");
										}
								}
						}
				}
		}
		
		
				
		/************************
		 * letzte belegeinträge
		 ************************/
		
		function beleg_last(){

				$num=($_REQUEST['num']) ? $_REQUEST['num'] : 10;
				
				$res=$this->db->query("SELECT * FROM beleg WHERE user_id={$this->userid} ORDER BY zeit DESC LIMIT {$num}");
				
				
				header ("content-type: text/xml");
				echo '<?xml  version="1.0" encoding="utf-8" ?>';
				echo "<liste>";
				while ($row=$res->fetchRow(DB_FETCHMODE_ASSOC)){
						echo "<item>";
						echo "<id>{$row['id']}</id>";
						echo "<projekt_id>{$row['projekt_id']}</projekt_id>";
						echo "<projekt>".xmlspecialchars(utf8_encode($this->projekt_name($row['projekt_id'])))."</projekt>";
						echo "<kommentar>".xmlspecialchars(utf8_encode($row['beschreibung']))."</kommentar>";
						echo "<betrag>".$row['betrag']."</betrag>";
						echo "<datum>".date("d.m.Y",$row['zeit'])."</datum>";
						$kid=$this->pid_to_kid($row['projekt_id']);
						echo "<kunde>".xmlspecialchars(utf8_encode($this->kunde_name($kid)))."</kunde>";
						echo "<kunden_id>".$kid."</kunden_id>";
						echo "</item>";
						
				}
				echo "</liste>";			
		}
		
		/***********************
		 * belegeintrag ändern
		 ***********************/
		
		function table_beleg_edit_save(){
				
				//misc
				$tarr=explode(",",$_REQUEST['id_data']);
				$eintrag_id=$tarr[1];
				$old=$this->belegeintrag($eintrag_id);
				
				//daten 'putzen'
				$d_tmp=strtotime($_REQUEST['datum']);
				$datum = ($d_tmp) ? $d_tmp : $old['zeit'];
				$b_tmp=$this->getvar('betrag',true);
				$betrag=(is_numeric($b_tmp)) ? $b_tmp : $old['betrag'];
				$beschreibung=$this->getvar('beschreibung',true);

				//nur wenn eintrag user gehört
				if ($old['user_id']==$this->userid){
						$query="UPDATE beleg SET    projekt_id='".$this->getvar('projekt',true)."',
													zeit='$datum',
													betrag='$betrag',
													beschreibung='".utf8_decode($this->getvar('kommentar',true))."'
										WHERE id={$eintrag_id};";
						$this->db->query($query);
				}	
		}
		
		
		
		/********************************
		 * belegeinträge löschen 
		 *******************************/
		function beleg_eintrag_del(){
				// frage
				if ($_REQUEST['step']!="2"){
						$_SESSION['multidel_items']=$_REQUEST['item'];
						echo $this->frage(__('confirm_del'), 
										  sprintf(__('zeit_eintrag_multidel'),count($_REQUEST['item'])), 
										  "", 
										  "");
				}
				//löschen
				else {
						if($_SESSION['multidel_items']){
								// einzelne items löschen
								foreach	($_SESSION['multidel_items'] as $item){
										//prüfen, ob eintrag user gehört
										$eintrag=$this->belegeintrag($item);
										if ($eintrag['user_id']==$this->userid){
												//wenn ja löschen
												$this->db->query("DELETE FROM beleg WHERE id='{$item}';");
										}
								}
						}
				}
		}
		
		

}
                function xmlspecialchars($string) {
                       return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
                } 
                



?>

<?php


class etempus {

		public $html;

		//prüfen ob htaccess-user auch in datenbank ist
		public function __construct(){
			if (ET_PLUG_DBG){
				error_reporting(E_ALL ^ E_NOTICE);
			}	
			$this->template=new template;
			$this->db=db_make();
			$this->userid=$this->get_user_id();
			if (!$this->userid){
				die("403");
			}
			
		}
		
		//variable holen
		public static function getvar($var,$mysql_clean=false){
				$r=$_REQUEST[$var];
				$v=stripslashes($r);
				$v=strip_tags($r,ETEMPUS_ALLOWED_HTML);
				if ($v=="") {
					return false;
				} elseif  (is_array($r)){
					return $r;
				} elseif ($mysql_clean){
				 	return DB_common::escapeSimple($v);
				} else {
					return $v;
				}
		}
		
		
		//id holen (und mysql-cleanen)
		protected function getid(){
				if (!$this->db) $this->db=db_make();
				if ($_REQUEST["id"]=="") return false;
				return $this->db->escapeSimple($_REQUEST["id"]);
		}
		
		protected function getlastid($table){
				$res=$this->db->query("SELECT id FROM $table ORDER BY id DESC LIMIT 1");
				if (!$res) return false;
				$row = $res->fetchRow();
				return $row[0];
		}


		//debug-fuktion
		public static function pdd($var){
				echo "<pre>";
				var_dump($var);
				echo "</pre>";
				die;
		}

		//prüfen ob id in datenbank existiert
		protected function check_id($table,$id){
				if (!$this->db) $this->db=db_make();
				$res = $this->db->query("SELECT id FROM $table WHERE id=$id;");
				if (!$res) return false;
				$a=class_parents($res);
				if ($a['PEAR_Error']=='PEAR_Error') return false;
				if ($res->numRows()>0) return true;
				else return false;
		}

		//komplette tabelle in array zurückgeben
		function table_array($table,$order_by=false,$order_desc=false,$limit_key=false,$limit_value=false){
				if (!$this->db) $this->db=db_make();
				$q2="";
				if ($limit_key and $limit_value){
					$q2.=" WHERE {$limit_key}='{$limit_value}'";
				}
				if ($order_by){
						$q2.= " ORDER BY $order_by";
						if ($order_desc)
							$q2.=" DESC";
				}
				$res=$this->db->query("SELECT * FROM $table{$q2};");
				$arr=array();
				while ($row=$res->fetchRow(DB_FETCHMODE_ASSOC)){
						array_push($arr,$row);
				}
				return ($arr);
		}

		protected function query_array($query){
				$res=$this->db->query($query);
				$arr=array();
				if (!$res) return false;
				$a=class_parents($res);
				if ($a['PEAR_Error']=='PEAR_Error') return false;
				while ($row=$res->fetchRow(DB_FETCHMODE_ASSOC)){
						array_push($arr,$row);
				}
				return ($arr);
		}
 		protected function utf8_array_decode($input){
				$array_temp = array();
				foreach($input as $name => $value)
				{
					if(is_array($value))
					  $array_temp[(mb_detect_encoding($name." ",'UTF-8,ISO-8859-1') == 'UTF-8' ? utf8_decode($name) : $name )] = self::utf8_array_decode($value);
					else
					  $array_temp[(mb_detect_encoding($name." ",'UTF-8,ISO-8859-1') == 'UTF-8' ? utf8_decode($name) : $name )] = (mb_detect_encoding($value." ",'UTF-8,ISO-8859-1') == 'UTF-8' ? utf8_decode($value) : $value );
				}
				return $array_temp;
        }
        
        
        protected function utf8_array_encode($input){
				$return = array();

    			foreach ($input as $key => $val){
					if (is_array($val)){
						$return[$key] = self::utf8_array_encode($val);
					}
					else {
						$return[$key] = utf8_encode($val);
					}
				}
				return $return;          
		} 

		//alle felder von index $id in array zurückgeben
		function table_item_array($table,$id){
				$res=$this->db->query("SELECT * FROM $table WHERE id=$id;");
				return ($res->fetchRow(DB_FETCHMODE_ASSOC));
		}

		//datenbankfeld updaten
		protected function update_db_field($table,$id,$field,$new_value){
				//echo "UPDATE $table SET $field='$new_value' WHERE id=$id LIMIT 1;<br />";
				if ($this->db->query("UPDATE $table SET $field='$new_value' WHERE id=$id;")) return true;
				else return false;
		}


		/*****************************************
			ausgabe html
		******************************************/

		//fehler ausgeben
		public function fail($fail_str){
				
				$tpl=new template;
				$tpl->assign( array("titel"		=> __("error"),
									"nachricht"	=> $this->warnung(__($fail_str))		));
				return $tpl->parse("fehler.tpl");
				
		}

		public function nachricht($msg){
				$tpl=new template;
				$tpl->auto_assign("nachricht.tpl");
				$tpl->assign(array("msg"=>$msg));
				return $tpl->parse("nachricht.tpl");
		}

		public function warnung($msg){
				$tpl=new template;
				$tpl->auto_assign("warnung.tpl");
				$tpl->assign(array("msg"=>$msg));
				return $tpl->parse("warnung.tpl");
		}

		//dropdown-liste von allen kunden zurückgeben, wenn kundenid angegeben diese vorauswählen
		protected function kunden_dd_liste($selected=false,$extra_attr=false,$only_overhead=false,$all=false,$name='kunde',$dec=true){
				$select="<select name='{$name}' $extra_attr>";
				$ov=($only_overhead)?"1":false;
				if ($all) {
						$select.="<option value=\"*\">".__("alle_kunden")."</option>";
				}
				$a=$this->table_array("kunden","name",false,"overhead",$ov);
				if ($dec)
					$a= $this->utf8_array_encode($this->table_array("kunden","name",false,"overhead",$ov));
				
				foreach ($a as $kunde){
					if ($kunde["id"]==$selected) $sel = " selected "; else $sel = " ";
					$select.="<option{$sel}value='{$kunde['id']}'>{$kunde['name']}</option>";
				}
				$select.="</select>";
				return $select;
		}
		
		//dropdown-projektliste
		protected function projekt_dd_liste($kunden_id,$selected=false,$extra_arr=false,$dec=false){
				//liste zusammenstellen
				$ret = "<select name='projekt' $extra_attr>";
				$a=$this->kunden_projekte($kunden_id);
				if ($dec)
					$a=$this->utf8_array_encode($this->kunden_projekte($kunden_id));
				foreach ($a as $projekt){
						$sel=($projekt['id']==$selected)?" selected ":" ";
						$ret.="<option{$sel}value='{$projekt['id']}'>{$projekt['name']}</option>";
				}
				$ret.="</select>";
				return $ret;
		}
		
		//dropdown-ansatzliste
		protected function ansatz_dd_liste($projekt_id,$selected=false,$all=false){
				if ($all) { 
					$arr['ansatz']="*";
				} else {
					$arr = $this->table_item_array("projekte",$projekt_id);
				}
				$ret="<select name='ansatz' id='ansatz_dd'>";
				//alle ansätze
				if ($arr['ansatz']=="*"){
						foreach ($this->table_array("ansatz","name",false,"aktiv","1") as $ansatz){
								$ansatz=$this->utf8_array_decode($ansatz);
								$sel = ($ansatz['id']==$selected)?" selected ":" ";
								$ret.="<option{$sel}value='{$ansatz['id']}'>{$ansatz['name']}</option>";
						}
				}
				//ausgewähle ansätze
				else {
						$ansatz_array = explode(",",$arr['ansatz']);
						foreach ($ansatz_array as $ansatz){
								$name =utf8_decode($this->ansatz_name($ansatz));
								$sel = ($ansatz==$selected)?" selected ":" ";
								$ret.="<option{$sel}value='{$ansatz}'>{$name}</option>";
						}
				}
				$ret.="</select>";
				return $ret;
		}
		
		
		
		//dropdown von letzten monaten
		protected function letzte_monate_dd($anz=60){	
				$this_month=strtotime(date("F Y"));
				$monate=__("monate");
				$ret ="";
				for ($i = 0; $i <= $anz-1; $i++) {
						$endung=($i>1)?"s":"";
						$ts=strtotime("-$i month{$endung}",$this_month);
						$act=date("n",$ts);
						$str = $monate[$act] ." ". date("Y",$ts);
						$ret.="<option value='$ts'>$str</option>\n";
						
				}
				return "<select name='monat'>$ret</select>";
		}
		//dropdown von letzten jahren
		protected function letzte_jahre_dd($anz=12){
				$ret ="";
				for ($i = 0; $i <= $anz-1; $i++) {
						$endung=($i>1)?"s":"";
						$ts=strtotime("-$i year{$endung}");
						$str = date("Y",$ts);
						$y_now = strtotime("01.01.$str");
						$ret.="<option value='$y_now'>$str</option>\n";
						
				}
				return "<select name='jahr'>$ret</select>";
		}
		
		//dropdown von allen usern
		protected function user_dd_liste($selected=false,$extra_attr=false){
				$select="<select name='user' $extra_attr>";
				foreach ($this->table_array("user") as $user){
					$sel= ($user["id"]==$selected)?" selected ":" ";
					$select.="<option{$sel}value='{$user['id']}'>{$user['name']}</option>";
				}
				$select.="</select>";
				return $select;
		}
		
		//universal-dropdown-liste
		protected function make_ddlist($arr,$name,$chk=false){
			$list="<select name=\"$name\">\n";
			foreach ($arr as $key=>$name){
				$sel= ($key==$chk) ? " selected " : " ";
				$list.="	<option{$sel}value=\"$key\">$name</option>\n";
			}
			$list.="</select>";
			return $list;
		}
		
		//dropdown aller styles
		protected function build_style_dd(){
				$user_arr=$this->table_item_array("user",$this->get_user_id());
				if ($user_arr['layout']){
					$this->tpl_name=$user_arr['layout'];
				} else {
					$this->tpl_name=ET_DEFAULT_STYLE;
				}
				$dd="<form id='layout_choose' action='' method='POST'>
						<select name=\"new_style\" onchange=\"$('layout_choose').submit();\">";
				foreach (glob("style/*",GLOB_ONLYDIR) as $item) {
						if (is_file($item."/style.php") and is_file($item."/etempus.html")){
								@include($item."/style.php");
								$str = str_replace("style/","",$item);
								$sel=($str==$this->tpl_name) ? " selected " : " ";
								$dd.="<option{$sel}value='$str'>{$style["name"]}</option>";
							
						}
				}
				$dd.="</select></form>";
				$this->style_dd=$dd;
				return $dd;
    	}
		
		//$request kopieren
		protected function copy_request($array_ok){
				$ret="";
				foreach ($_REQUEST as $key=>$value){
						foreach ($array_ok as $pos){
								if ($pos==$key){
										$ret.="<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\" />";
								}
						}
				}
				return $ret;
		}
		
		//frage-box
		function frage($titel, $text, $click_ja, $click_nein, $extra_html=false){
				
				$this->template->assign(array(	"ask_title"		=> $titel ,
												"ask_msg"		=> $text ,
												"ask_action_no"	=> $click_nein,
												"ask_action_yes"=> $click_ja,
												"ask_more"		=> $extra_html		));
												
				return $this->template->go("frage_ajax.tpl");
			
		}
		



		/**********************************************
		 datenbankzugriff shortcuts
		************************************************/

		protected function user_time_list($limit=false) {
				//if (!$_SESSION['conf_zeit_eintragnum']) $_SESSION['conf_zeit_eintragnum']=10;
				//if (is_numeric($this->getvar("num_entrys",true)))  $_SESSION['conf_zeit_eintragnum']=$this->getvar("num_entrys",true);
				$q2="";
				if ($limit){
					$q2="LIMIT {$limit}";
				}
				$user_id = $this->userid;
				$res=$this->db->query("SELECT * FROM zeit WHERE user_id=$user_id ORDER BY id DESC {$q2};");
				$arr=array();
				$i=0;
				while ($row=$res->fetchRow(DB_FETCHMODE_ASSOC)){
					foreach ($row as $key=>$var)
						$arr[$i][$key]=$var;
					$arr[$i]['projekt'] = $this->projekt_name($row['projekt_id']);
					$arr[$i]['sekunden'] = $row['zeit_ende']-$row['zeit_start'];
					$arr[$i]['str_start']=date("d.m.Y H:i:s",$row['zeit_start']);
					$arr[$i]['str_ende']=date("d.m.Y H:i:s",$row['zeit_ende']);
					$arr[$i]['tdesc']=date("d.m.Y H:i",$row['zeit_start'])." - ".date("H:i",$row['zeit_ende']); 
					$arr[$i]['zeit_formatiert'] = $this->formatiere_zeit($arr[$i]['sekunden']);
					$ansatz=$this->table_item_array("ansatz",$row['ansatz_id']);
					$arr[$i]['ansatz']=$ansatz['name'];
					$kosten=round( ($arr[$i]['sekunden']*($ansatz['wert']/3600)), 2) ;
					$arr[$i]['kosten']=number_format(round($kosten*20)/20,2,".","");
					$arr[$i]['kunden_id']=$this->pid_to_kid($row['projekt_id']);
					$arr[$i]['kunden_name']=$this->kunde_name($arr[$i]['kunden_id']);
					$i++;
				}
				return $arr;
		}
		
		
		protected function user_beleg_list() {
				if (!$_SESSION['conf_beleg_eintragnum']) $_SESSION['conf_beleg_eintragnum']=10;
				if (is_numeric($this->getvar("num_entrys",true)))  $_SESSION['conf_beleg_eintragnum']=$this->getvar("num_entrys",true);
				$user_id = $this->get_user_id();
				$res=$this->db->query("SELECT * FROM beleg WHERE user_id=$user_id ORDER BY id DESC LIMIT {$_SESSION['conf_beleg_eintragnum']}");
				$arr=array();
				$i=0;
				while ($row=$res->fetchRow(DB_FETCHMODE_ASSOC)){
					foreach ($row as $key=>$var)
						$arr[$i][$key]=$var;
					$arr[$i]['user_name']=$this->user_name($row['user_id']);
					$arr[$i]['projekt'] = $this->projekt_name($row['projekt_id']);
					$arr[$i]['tdesc']=date("d.m.Y",$row['zeit']);
					$arr[$i]['kunden_id']=$this->pid_to_kid($row['projekt_id']);
					$arr[$i]['kunden_name']=$this->kunde_name($arr[$i]['kunden_id']);
					$i++;
				}
				return $arr;
		}

		//prüfen ob kostendach von projekt erreicht ist, gibt true zurück wenn wahr
		protected function check_kostendach($projekt_id){
				
				//rechne kosten zusammen
				$kosten=0;
				//zeiteinträge
				$res=$this->db->query("SELECT * FROM zeit WHERE projekt_id=$projekt_id;");
				if ($res and $res->numRows()>0){
						$i=0;
						while ($item = $res->fetchRow(DB_FETCHMODE_ASSOC)){
								$arr[$i]['sekunden'] = $item['zeit_ende']-$item['zeit_start'];
								$ansatz=$this->table_item_array("ansatz",$item['ansatz_id']);
								$arr[$i]['ansatz']=$ansatz['name'];
								$sub_k=round( ($arr[$i]['sekunden']*($ansatz['wert']/3600)), 2) ;
								$kosten=$kosten+$sub_k;
								$i++;
						}
				}
				
				
				//belege
				
				$res2=$this->db->query("SELECT * FROM beleg WHERE projekt_id=$projekt_id;");
				if ($res2 and $res2->numRows()>0){
						while ($item = $res2->fetchRow(DB_FETCHMODE_ASSOC)){
								$kosten=$kosten+$item['betrag'];
						}
				}
							
				
				$projekt=$this->table_item_array("projekte",$projekt_id);
				if ($projekt['kostendach']){
						$kostend = round(($projekt['kostendach']/100)*ET_KOSTENDACH_WARNUNG);
						if ($kosten>=$kostend)
							return true;
				}
				return false;
		}
		

		//alle projekte von einem kunden in array zurückgeben
		protected function kunden_projekte($kunden_id){
				$res=$this->db->query("SELECT * FROM projekte WHERE kunden_id=$kunden_id ORDER BY name");
				if (!$res) return false;
				$arr=array();
				while ($row=$res->fetchRow(DB_FETCHMODE_ASSOC)){
						array_push($arr,$row);
				}
				return $arr;
		}

		//kunden name über id herausfinden
		protected function kunde_name($kunden_id){
				$res=$this->db->query("SELECT name FROM kunden WHERE id=$kunden_id;");
				if (!$res) return false;
				//var_dump($res);
				$a=class_parents($res);
				if ($a['PEAR_Error']=='PEAR_Error') return false;
				$row = $res->fetchRow();
				return $row[0];
		}

		//projektname über id herausfinden
		protected function projekt_name($projekt_id){
				$res= $this->db->query("SELECT name FROM projekte WHERE id=$projekt_id;");
				if (!$res) return false;
				$a=class_parents($res);
				if ($a['PEAR_Error']=='PEAR_Error') return false;
				$row = $res->fetchRow();
				return $row[0];
		}
		//kostendach von projekt 
		protected function get_kostendach($projekt_id){
				$res= $this->db->query("SELECT kostendach FROM projekte WHERE id=$projekt_id;");
				if (!$res) return false;
				$a=class_parents($res);
				if ($a['PEAR_Error']=='PEAR_Error') return false;
				$row = $res->fetchRow();
				return $row[0];
		}
		//prüfen ob overhead bei kunde aktiviert ist
		protected function is_overhead($kunden_id){
				$res= $this->db->query("SELECT overhead FROM kunden WHERE id=$kunden_id;");
				if (!$res) return false;
				$row = $res->fetchRow();
				return ($row[0]) ? true : false;
		}
		
		//overhead von projekt holen
		protected function get_overhead($projekt_id){
				$res= $this->db->query("SELECT overhead FROM projekte WHERE id=$projekt_id;");
				if (!$res) return false;
				$row = $res->fetchRow();
				return $row[0];
		}

		//kundenid über projektid herausfinden
		protected function pid_to_kid($pid){
				$this->db=db_make();
				$res= $this->db->query("SELECT kunden_id FROM projekte WHERE id=$pid;");
				if (!$res) return false;
				$row = $res->fetchRow();
				return $row[0];
		}

		//ansatzname über id herausfinden
		protected function ansatz_name($ansatz_id){
				$res= $this->db->query("SELECT name FROM ansatz WHERE id=$ansatz_id;");
				if (!$res) return false;
				$row = $res->fetchRow();
				return $row[0];
		}
		
		//zeiteintrag aus datenbank holen
		protected function zeiteintrag($id){
				$res= $this->db->query("SELECT * FROM zeit WHERE id={$id};");
				if ($res){
					$ret = $res->fetchRow(DB_FETCHMODE_ASSOC);
					return $ret;
				} else return false;
		}
		//belegeintrag aus db fischen
		protected function belegeintrag($id){
				$res= $this->db->query("SELECT * FROM beleg WHERE id={$id};");
				if ($res){
					$ret = $res->fetchRow(DB_FETCHMODE_ASSOC);
					return $ret;
				} else return false;
		}

		/**********************************************
			login-&user funktionen
		***********************************************/

		//id von eingeloggtem user herausfinden
		public function get_user_id(){
				if (!$this->db) $this->db=db_make();
				$auth=$_SERVER['PHP_AUTH_USER'];
				if (!$auth) $auth=$_SERVER["REMOTE_USER"];
				$res=$this->db->query("SELECT id FROM user WHERE login='{$auth}';");
				if (!$res) return false;
				$arr = $res->fetchRow();
				return $arr[0];
		}
		
		//username über userid herausfinden
		public function user_name($userid){
				$res=$this->db->query("SELECT name FROM user WHERE id='{$userid}';");
				if (!$res) return false;
				$row = $res->fetchRow();
				return $row[0];
		}
		
		//user-login herausfinden
		public function user_login($userid){
				$res=$this->db->query("SELECT login FROM user WHERE id='{$userid}';");
				if (!$res) return false;
				$row = $res->fetchRow();
				return $row[0];
		}
		
		//htaccess-datei in array laden
		protected function load_ht_file(){
				$pw_file = file_get_contents(".htpasswd");
				$line_array=explode("\n",$pw_file);
				$pw_file="";
				foreach ($line_array as $line){
						if (!empty($line)) {
								$a = explode(":",$line);
								$arr[$a[0]]=$a[1];
						}
				}
				return $arr;
		}
		
		//htaccess-array speichern
		protected function save_ht_file($array){
				$file = "";
				foreach ($array as $user=>$pw){
						$file.="{$user}:$pw\n";
				}
				$file=substr($file,0,-1);
				return file_put_contents(".htpasswd",$file);
		}
		
		
		//dateiname säubern
		protected function dir_name($str){
				$t0 = str_ireplace("ü","ue",$str);
				$t0 = str_ireplace("ö","oe",$t0);
				$t0 = str_ireplace("ä","ae",$t0);				
				$t0 = str_ireplace(" ","_",$t0);
				$pattern = "/[^a-zA-Z0-9_]+/";
				return preg_replace($pattern, '', $t0);
		}

		/**********************************************
			zeitfunktionen
		***********************************************/

		//zeit formatierem
		public function formatiere_zeit($sekunden){

				$minuten = $sekunden/60;
				$stunden = $minuten/60;
				$minuten_rest = $minuten%60;
				$stunden = floor($stunden);
				$minuten = floor($minuten_rest);
				if ($minuten>1) $min = __("minuten");
				else $min = __("minute");
				if ($stunden>1) $h = __("stunden");
				else $h = __("stunde");
				return "$stunden $h $minuten $min";
		}

		public function formatiere_zeit_csv($sekunden){
				$minuten = $sekunden/60;
				$stunden = $minuten/60;
				$minuten_rest = $minuten%60;
				$stunden = floor($stunden);
				$minuten = floor($minuten_rest);
				$minuten=str_pad($minuten,2,0, STR_PAD_LEFT);
				$stunden=str_pad($stunden,2,0, STR_PAD_LEFT);
				return "$stunden:$minuten";
		}


		//gesamtzeit von $projekt_id in sekunden zurückgeben
		protected function projekt_zeit($projekt_id){
				$res=$this->db->query("SELECT zeit_start,zeit_ende FROM zeit WHERE projekt_id=$projekt_id");
				$sekunden = 0;
				while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)){
						$sekunden=$sekunden+($row['zeit_ende']-$row['zeit_start']);

				}
				return $sekunden;
		}
		
		//auswertungs-array formatieren
		protected function formatiere_zeit_array($array){
				$arr=array();
				$i=0;
				if (!is_array($array)) return false;
				foreach ($array as $row){
					foreach ($row as $key=>$var)
						$arr[$i][$key]=$var;
					$arr[$i]['projekt'] = $this->projekt_name($row['projekt_id']);
					$arr[$i]['sekunden'] = $row['zeit_ende']-$row['zeit_start'];
					$arr[$i]['str_start']=date("d.m.Y H:i:s",$row['zeit_start']);
					$arr[$i]['str_ende']=date("d.m.Y H:i:s",$row['zeit_ende']);
					$arr[$i]['tdesc']=date("d.m.y H:i",$row['zeit_start'])." - ".date("H:i",$row['zeit_ende']); 
					$arr[$i]['zeit_formatiert'] = $this->formatiere_zeit($arr[$i]['sekunden']);
					$ansatz=$this->table_item_array("ansatz",$row['ansatz_id']);
					$arr[$i]['ansatz']=$ansatz['name'];
					$kosten=round( ($arr[$i]['sekunden']*($ansatz['wert']/3600)), 2) ;
					$arr[$i]['kosten']=number_format(round($kosten*20)/20,2,".","");
					$arr[$i]['user_name']=$this->user_name($row['user_id']);
					$arr[$i]['kunden_id']=$this->pid_to_kid($row['projekt_id']);
					$arr[$i]['kunden_name']=$this->kunde_name($arr[$i]['kunden_id']);
					$i++;
				}
				return $arr;
		}
		
		//beleg-array formatieren
		protected function formatiere_beleg_array($array){
				$arr=array();
				$i=0;
				if (!is_array($array)) return false;
				foreach ($array as $row){
					foreach ($row as $key=>$var)
						$arr[$i][$key]=$var;
					$arr[$i]['user_name']=$this->user_name($row['user_id']);
					$arr[$i]['projekt'] = $this->projekt_name($row['projekt_id']);
					$arr[$i]['tdesc']=date("d.m.Y",$row['zeit']);
					$arr[$i]['kunden_id']=$this->pid_to_kid($row['projekt_id']);
					$arr[$i]['kunden_name']=$this->kunde_name($arr[$i]['kunden_id']);
					$i++;
				}
				return $arr;
		}
		
		
		//cleaner funktion für js-serialize
		function js_serialize_escape($str) {
				$str = str_replace(array('\\', "'"), array("\\\\", "\\'"), $str);
				$str = preg_replace('#([\x00-\x1F])#e', '"\x" . sprintf("%02x", ord("\1"))', $str);
				return $str;
		}
    
		function match_num_list($number,$list){
				if ($arr=explode(",",$list)){ foreach($arr as $pos){ if ($pos==$number){ return true; } } } return false;
		}









		function plugin(){
				$this->layout=true;
				$name = $this->getvar("plugin_name");
				$inc = "plugins/{$name}/inc.php";
				$plugin = false;
				if (ET_PLUG_DBG){ include($inc); } else { @include($inc); }					
				if ($plugin and class_exists($plugin['class'])){
						if ($plugin['admin_only'] and $this->get_user_id()!=1) { 
								$content=$this->warnung(__('not_allowed')); 
						} else {
								$GLOBALS['txt']=array_merge($GLOBALS['txt'],$lang[$_SESSION['lang']]);
								$p = new $plugin['class'];
								$p->dir="plugins/{$name}/";
								$p->build();
								$content = $p->html;
						}
				
				} else {
						$content = $this->warnung(sprintf(__("plugin_error"),$this->getvar("plugin_name")).
												  "<br /><br />Debug:<br /><pre>\n".print_r($plugin,true)."</pre>");
				}
				echo $content;
			
		}



}





?>

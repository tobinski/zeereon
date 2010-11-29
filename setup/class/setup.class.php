<?php
/**************************************************************
 * 
 * etempus setup-funktionen
 * (c) 2009 by cyrill von wattenwyl, protagonist gmbh
 * 
 * ***********************************************************/


class etempus_setup {



		/*******************************
		 *  beim erstellen der klasse
		 * *****************************/
		
		function __construct(){
				$this->tpl=new template;
		}
		


		/*******************************
		 *  setup schritt 1
		 * *****************************/
		
		function step1($msg="",$script=""){
				
				//wenn seite ein zewites mal angezeigt wird
				if ($msg) $msg=$this->warning($msg);
				$this->request_emptyset('mysql_login');
				$this->request_emptyset('mysql_db');
				$this->request_emptyset('ftp_login');
				$chk['chk_sqlite'] = ($_REQUEST['dbtype']=="sqlite") ? "checked" : "";
				$chk['chk_mysql'] = ($_REQUEST['dbtype']=="mysql") ? "checked" : "";
				$chk['chk_manual'] = ($_REQUEST['right']=="manual") ? "checked" : "";
				$chk['chk_ftp'] = ($_REQUEST['right']=="ftp") ? "checked" : "";
				
				//ausgeben
				$this->head();
				echo $this->tpl->go("step1.tpl",array_merge($_REQUEST,array("msg"=>$msg,"script"=>$script),$chk));
				$this->foot();

		}
		


		/*******************************
		 *  setup schritt 2
		 * *****************************/
		
		function step2($msg="",$bypass=false){
			
				if ($_REQUEST['bypass']) $bypass=true;
				
				//daten überprüfen
				$fail="";
				$open="";
				if ( !$_REQUEST['dbtype'] || !$_REQUEST['right'] ) $fail.="- ".__("error_empty")."<br />";
				if ($_REQUEST['dbtype']=="mysql" ){
						if (!$_REQUEST['mysql_host']  || !$_REQUEST['mysql_login'] || !$_REQUEST['mysql_pass'] || !$_REQUEST['mysql_db']){
							$fail.="- ".__("error_mysql_empty")."<br />";
							$open.="<script type='text/javascript'>$('mysql_acc').style.display='block';</script>";				
						} else { 
								//mysql-verbindungscheck
								$link = @mysql_connect($_REQUEST["mysql_host"], $_REQUEST["mysql_login"], $_REQUEST["mysql_pass"]);
								if (!$link){
										$fail.="- ".__("error_mysql_empty")." : <i>".mysql_error()."</i><br />";
										$open.="<script type='text/javascript'>$('mysql_acc').style.display='block';</script>";	
								} else {
										$db_selected = @mysql_select_db($_REQUEST['mysql_db'], $link);
										if (!$db_selected) {
												$fail.="- ".__("error_mysql_empty")." : <i>".mysql_error()."</i><br />";
												$open.="<script type='text/javascript'>$('mysql_acc').style.display='block';</script>";	
										}
								}
						}
				}
				if ($_REQUEST['right']=="ftp" ){
						if (!$_REQUEST['ftp_host']  || !$_REQUEST['ftp_login'] || !$_REQUEST['ftp_pass'] ){
							$fail.="- ".__("error_ftp_empty")."<br />";
							$open.="<script type='text/javascript'>$('ftp_acc').style.display='block';</script>";				
						} else {
								//checke ftp-server
								$conn_id = @ftp_connect($_REQUEST['ftp_host']);
								$login_result = @ftp_login($conn_id, $_REQUEST['ftp_login'], $_REQUEST['ftp_pass']);
								if ((!$conn_id) || (!$login_result)) {
										$fail.="- ".__("error_ftp_empty")." : <i>".__("login_fail")."</i><br />";
										$open.="<script type='text/javascript'>$('ftp_acc').style.display='block';</script>";
								}
						}
				}
				//wenn fehler gehe zurück zu schritt 1
				if ($fail and !$bypass){
						$this->step1($fail,$open);
						return;
				}
				
				
				//eingaben speichern (ab hier gültig)
				if (!$bypass) {
						$this->req_session_copy("dbtype");
						$this->req_session_copy("right");
						$this->req_session_copy("mysql_host");
						$this->req_session_copy("mysql_login");
						$this->req_session_copy("mysql_pass");
						$this->req_session_copy("mysql_db");
						$this->req_session_copy("ftp_host");
						$this->req_session_copy("ftp_login");
						$this->req_session_copy("ftp_pass");
				}
				
				//wenn seite ein zweites mal ausgegeben wird
				if ($msg) $msg=$this->warning($msg);

				//ftp-rechte setzen
				if ($_SESSION['right']=="ftp"){
						$ftp_ret=$this->set_ftp_rights();
						if ($ftp_ret['fname']){ $msg.=$this->info(sprintf(__("ftp_found"),$ftp_ret['fname'],$ftp_ret['list'])); }
				}
				
				//rechte überprüfen
				$this->right_check();
				
				
				//ausgeben
				$this->head();
				echo $this->tpl->go("step2.tpl",array("msg"=>$msg));
				$this->foot();
	
		}
		
		

		/*******************************
		 *  setup schritt 3
		 * *****************************/
		
		function step3($msg=""){
			
				if ($msg) $msg=$this->warning($msg);
				
				//daten überprüfen
				$fail="";
				$open="";
				if ( !$_REQUEST['admin_login'] || !$_REQUEST['fullname'] || !$_REQUEST['pass'] || !$_REQUEST['pass2'] || !$_REQUEST['max_user']){
					$fail.="- ".__("error_empty")."<br />";
				}
				if ($_REQUEST['pass']!=$_REQUEST['pass2']){
					$fail.="- ".__("pw_not_match")."<br />";
				}
				//wenn fehler gehe zurück zu schritt 2
				if ($fail){
						$this->step2($fail,true);
						return;
				}
				
				//eingaben speichern (ab hier gültig)
				$this->req_session_copy("admin_login");
				$this->req_session_copy("fullname");
				$this->req_session_copy("pass","admin_pass");
				$this->req_session_copy("max_user");
				$this->req_session_copy("remote");

				
				//dateien & datenbank erstellen
				$this->make_db();
				$this->make_config_files();
				
				$msg.=$this->info(__("step3_msg"));
				
				
				//ausgeben
				$this->head();
				echo $this->tpl->go("step3.tpl",array("msg"=>$msg));
				$this->foot();
		
		}



		/*******************************
		 *  setup schritt 4
		 * *****************************/
		
		function step4(){
			
				//admin-login erstellen
				$this->make_admin();
				
				//db-id erstellen
				$db_id=uniqid();
				include("../class/database.functions.php");
				include("../class/et_config.class.php");
				$conf = new et_config;
				$conf->	make_key("setup_db_id",$db_id);
				
				//setup-nachricht auf startseite setzen
				$query="INSERT INTO home_msg VALUES (NULL,'1','".sprintf(__("home_msg_setup"),$db_id)."','".time()."');";
				switch ($_SESSION['dbtype']){
					case "sqlite":
						$this->query($query);
						break;
					case "mysql":
						$sql_str="USE {$_SESSION['mysql_db']};\n{$query}";
						file_put_contents("sql/mysql_tmp.sql",$sql_str);
						$importer=new sqlImport($_SESSION['mysql_host'], $_SESSION['mysql_login'], $_SESSION['mysql_pass'], "sql/mysql_tmp.sql");;
						$importer->import(); 
						file_put_contents("sql/mysql_tmp.sql","");
						break;
				}
				
				//setup-ordner löschen
				if (ET_SETUP_DEL_FOLDER){
						$this->rm_rf("../".ET_SETUP_FOLDER);
				}
				//weiterleitung zu etempus, setup ist hier fertig
				header("Location: ../index.php");
				die;
		
		}



		/*******************************
		 *  ftp-rechte setzen
		 * *****************************/
		function set_ftp_rights(){
				
				$mode=ET_SETUP_FTP_CHMODE;
				
				//bei ftp-server einloggen
				$ftpstream = @ftp_connect($_SESSION['ftp_host']);
				$ftp_l = @ftp_login($ftpstream, $_SESSION['ftp_login'], $_SESSION['ftp_pass']);
				$ret=array();
				
				//etempus-dateien suchen
				$root_list = explode("/",$_SERVER["DOCUMENT_ROOT"]);
				$ftplist = ftp_nlist($ftpstream,".");
				if (!empty($ftplist)){
						foreach ($ftplist as $folder){
								if (!empty($root_list)){
										foreach ($root_list as $root_path){
												if ($root_path==$folder){
														$res = $folder;
												}
										}
								}
						}
				}
				if (!$res) {
						return $ret;
				}
				
				//strings vorbereiten
				preg_match("/\/{$res}(.*)/is",$_SERVER["DOCUMENT_ROOT"],$res1);
				$ret['fname']=$res1[0];
				$ret['list']="";
				
				//rechte setzen
				$w_files=$this->files_right();
				foreach ($w_files as $file){
						
						//wenn datei
						if (is_file("../".$file)){
								$r2 = @ftp_site($ftpstream, "CHMOD $mode {$res1[0]}/{$file}");
						}
						
						//wenn ordner
						if (is_dir("../".$file)){
								$r1 = @ftp_site($ftpstream, "CHMOD $mode {$res1[0]}/{$file}");
								$arr=$this->listdir("../".$file);
								foreach ($arr as $subitem){
										$subitem=str_replace("//","/",$subitem);
										$subitem=str_replace("../","",$subitem);
										$r2 = @ftp_site($ftpstream, "CHMOD $mode {$res1[0]}/{$subitem}");
								}						
						}
						
						$ret['list'].="{$file}<br />";
				}
				return $ret;
		}
		
		

		/*******************************
		 *  rechte-check (schauen ob dateien beschreibbar sind)
		 * *****************************/
		
		function right_check(){
				$w_files=$this->files_right();
				//dateien checken
				$fail="";
				foreach ($w_files as $file){
						if (!is_writable("../".$file)){
							$fail.=$file."<br />";
						}
				}
				
				//wenn fehler ausgeben und ende
				if ($fail) {
					echo $this->head();
					echo $this->tpl->go("fail_rights.tpl",array("files"=>$fail));
					echo $this->foot();
					die;
				}
		}



		/*******************************
		 *  datenbank erstellen
		 * *****************************/
		
		function make_db(){
			
				//datenbanktyp
				switch ($_SESSION['dbtype']){
						case "sqlite":
							$sql_str=file_get_contents("sql/sqlite.sql");
							$this->query($sql_str);
							break;
						
						case "mysql":							
							$sql_file=file_get_contents("sql/mysql.sql");
							$sql_str = "CREATE DATABASE IF NOT EXISTS {$_SESSION['mysql_db']};\nUSE {$_SESSION['mysql_db']};\n".$sql_file;
							file_put_contents("sql/mysql_tmp.sql",$sql_str);
							$importer=new sqlImport($_SESSION['mysql_host'], $_SESSION['mysql_login'], $_SESSION['mysql_pass'], "sql/mysql_tmp.sql");;
							$importer->import(); 
							break;
				}
								
				
		}
		


		/*******************************
		 *  config-files erstellen
		 * *****************************/
		
		function make_config_files(){
				
				//default-dateien auslesen
				$db_config = file_get_contents("config/db.php.default");
				$constants_config=file_get_contents("config/constants.php.default");
				
				//config-strings setzen
				$db_mode = ($_SESSION['dbtype']=="sqlite") ? "'0666'" : "false";
				$db_user = ($_SESSION['dbtype']=="sqlite") ? "false" : "'{$_SESSION['mysql_login']}'";
				$db_pass = ($_SESSION['dbtype']=="sqlite") ? "false" : "'{$_SESSION['mysql_pass']}'";
				$db_host = ($_SESSION['dbtype']=="sqlite") ? "false" : "'{$_SESSION['mysql_host']}'";
				$db_db =   ($_SESSION['dbtype']=="sqlite") ? "\"".str_replace(ET_SETUP_FOLDER."class/setup.class.php","db/".ET_SETUP_SQLITE_FILE,__FILE__)."\"" : "'{$_SESSION['mysql_db']}'";
				$remote = ($_SESSION['remote']) ? "true" : "false";
				$arr_db = array("dbtype"	=> $_SESSION['dbtype'],
								"db_mode"	=> $db_mode,
								"db_user"	=> $db_user,
								"db_pass"	=> $db_pass,
								"db_host"	=> $db_host,
								"db_db"		=> $db_db);
				$arr_const = array(	"max_users"	=> $_SESSION['max_user'],
									"remote"	=> $remote);
				
				//config-dateien erstellen
				$db_ok = new template;
				$db_ok->assign($arr_db);
				$db_ok=$db_ok->parse($db_config,true);
				$const_ok = new template;
				$const_ok->assign($arr_const);
				$const_ok=$const_ok->parse($constants_config,true);
				
				//config-dateien schreiben
				file_put_contents("../config/constants.php",$const_ok);
				file_put_contents("../config/db.php",$db_ok);
				
		}



		/*******************************
		 *  admin erstellen
		 * *****************************/
		
		function make_admin(){				
				
				//.htaccess auslesen
				$ht_file=file_get_contents("config/htaccess.default");
				$place = str_replace(ET_SETUP_FOLDER."class/setup.class.php","",__FILE__);
				
				//dateien erstellen
				$hta = new template;
				$hta->assign(array("auth_file_place"=>$place.".htpasswd"));
				$hta = $hta->parse($ht_file,true);
				$htp = $_SESSION['admin_login'].":".crypt($_SESSION['admin_pass']);
				
				//dateien schreiben
				file_put_contents("../.htaccess",$hta);
				file_put_contents("../.htpasswd",$htp);

				//query erstellen
				include("../config/constants.php");
				$query = "INSERT INTO user VALUES (1, '{$_SESSION['admin_login']}', '{$_SESSION['fullname']}','".ET_DEFAULT_LANG."','".ET_DEFAULT_STYLE."');";
				
				//je nach datenbanktyp in db schreiben
				switch ($_SESSION['dbtype']){
						case "sqlite":
							$this->query($query);
							break;
						case "mysql":
							$sql_str="USE {$_SESSION['mysql_db']};\n{$query}";
							file_put_contents("sql/mysql_tmp.sql",$sql_str);
							$importer=new sqlImport($_SESSION['mysql_host'], $_SESSION['mysql_login'], $_SESSION['mysql_pass'], "sql/mysql_tmp.sql");;
							$importer->import(); 
							file_put_contents("sql/mysql_tmp.sql","");
							break;
				}
		}
		
		

		
		/**************************************************************
		 * 
		 * 	ab hier hilfsfunktionen
		 *  
		 * ***********************************************************/
		

		
		/*******************************
		 * dateiliste mit allen 0666-dateien
		 * *****************************/
		
		function files_right(){
				
				//dateien
				$w_files = array(".htaccess",".htpasswd","config/db.php","config/constants.php");
				if ($_SESSION['dbtype']=="sqlite") array_push($w_files,"db/","db/".ET_SETUP_SQLITE_FILE);
				if ($_SESSION['dbtype']=="mysql") array_merge($w_files,ET_SETUP_FOLDER."sql/mysql_tmp.sql");
				if (ET_SETUP_DEL_FOLDER) array_push($w_files,"setup/");
				return $w_files;
		}
		
		
		/*******************************
		 * datenbank-query ausführen
		 * *****************************/

		function query($query){
				switch ($_SESSION['dbtype']){
							case "sqlite":
								$this->db=@sqlite_open('../db/'.ET_SETUP_SQLITE_FILE, 0666, $sqliteerror);
								@sqlite_query($query,$this->db);
								break;
							case "mysql":
								$this->db = @mysql_connect($_REQUEST["mysql_host"], $_REQUEST["mysql_login"], $_REQUEST["mysql_pass"]);
								@mysql_select_db($_REQUEST['mysql_db'], $this->db);
								@mysql_query($query,$this->db);
								break;
					}
					//echo $query;
		}
		
		
		/*******************************
		 * html: header ausgeben
		 * *****************************/

		function head(){
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo $this->tpl->go("head.tpl");
		}

		
		/*******************************
		 *  html: footer ausgeben
		 * *****************************/
		
		function foot(){
				echo $this->tpl->go("foot.tpl");
		}
		
		
		/*******************************
		 *  html: warnung zurückgeben
		 * *****************************/
		
		function warning($message){
				return $this->tpl->go("warnung.tpl",array("msg"=>$message));
		}
		
		
		/*******************************
		 *  html: nachricht zurückgeben
		 * *****************************/
		
		function info($message){
				return $this->tpl->go("nachricht.tpl",array("msg"=>$message));
		}
	
	
		/*******************************
		 *  request string setzen wenn nicht vorhanden
		 * *****************************/

		function request_emptyset($varname){	
				if (!$_REQUEST[$varname]) $_REQUEST[$varname]="";
		}
		
		
		/*******************************
		 *  kopiere $_request in $session
		 * *****************************/

		function req_session_copy($varname,$session_varname=false){	
				if (!$session_varname) $session_varname=$varname;
				$_SESSION[$session_varname]=$_REQUEST[$varname];
		}
		
		
		/*******************************
		 *  verzeichnis ekursiv löschen (rm -rf)
		 * *****************************/

		function rm_rf($dir) {
				if (!file_exists($dir)) return true;
				if (!is_dir($dir) || is_link($dir)) return @unlink($dir);
				foreach (scandir($dir) as $item) {
						if ($item == '.' || $item == '..') continue;
						if (!self::rm_rf($dir . "/" . $item)) {
							@chmod($dir . "/" . $item, 0777);
							if (!self::rm_rf($dir . "/" . $item)) return false;
						};
				}
				return @rmdir($dir);
    	} 
		
		
		/********************************
		 * ordnerstruktur rekursiv in array laden
		*********************************/
		
		static function listdir($start_dir='.') {
			  $files = array();
			  if (is_dir($start_dir)) {
					$fh = @opendir($start_dir);
					while (($file = @readdir($fh)) !== false) {
						  if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
						  $filepath = $start_dir . '/' . $file;
						  if ( is_dir($filepath) ){
						  	array_push($files, $filepath); //ordner auflisten
							$files = array_merge($files, self::listdir($filepath));
						  } else {
							array_push($files, $filepath);
						  }
					}
					@closedir($fh);
			  } else {
					$files = false;
			  }
			  return $files;
		}
		
		
}
?>

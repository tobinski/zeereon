<?php

class et_oo_rechnung_conf extends etempus {
		
		public $html;
		
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
		
		
		function build($message=false,$fail=false){
				$conf = new et_config;
				
				//werte speichern
				if ($this->getvar("save")=="true"){
						if ($this->getvar("top_titel"))
							$conf->set_key("oo_rechnung_titel",$this->getvar("top_titel"));
						if ($this->getvar("top_text"))
							$conf->set_key("oo_rechnung_text",$this->getvar("top_text"));
						if ($this->getvar("footer"))
							$conf->set_key("oo_rechnung_footer",$this->getvar("footer"));
						if ($this->getvar("logo_left"))
							$conf->set_key("oo_rechnung_pos_logo_left",$this->getvar("logo_left"));
						if ($this->getvar("logo_top"))
							$conf->set_key("oo_rechnung_pos_logo_top",$this->getvar("logo_top"));
						if ($this->getvar("addr_left"))
							$conf->set_key("oo_rechnung_pos_addr_left",$this->getvar("addr_left"));
						if ($this->getvar("addr_top"))
							$conf->set_key("oo_rechnung_pos_addr_top",$this->getvar("addr_top"));
						//logodatei speichern
						if ($_FILES['logo_file']['tmp_name']){
							$dn = dirname(__FILE__);
							move_uploaded_file($_FILES['logo_file']['tmp_name'],$dn."/img/logo.jpg");							
							chmod($dn."/img/logo.jpg",0666);
						}
						$message=__("prefs_saved");
				}
				
				//fehlerboxen
				$msg="";
				if ($message)
					$msg=$this->nachricht($message);
				if ($fail)
					$msg=$this->warnung($message);
					
				//seite assemblieren und ausgeben
				$arr = array(	"unix_time"				=> time(),
								"msg"					=> $msg,
								"plugdir"				=> $this->dir,
								"oo_export_db_titel"	=> $conf->load_key("oo_rechnung_titel"),
								"oo_export_db_text"		=> $conf->load_key("oo_rechnung_text"),
								"oo_export_db_foot"		=> $conf->load_key("oo_rechnung_footer"),
								"oo_export_db_logo_top" => $conf->load_key("oo_rechnung_pos_logo_top"),
								"oo_export_db_logo_left"=> $conf->load_key("oo_rechnung_pos_logo_left"),
								"oo_export_db_addr_top"	=> $conf->load_key("oo_rechnung_pos_addr_top"),
								"oo_export_db_addr_left"=> $conf->load_key("oo_rechnung_pos_addr_left"),);
				$tpl=new template($this->dir);
				$this->html=$tpl->go("conf.tpl",$arr);
		}

}
?>

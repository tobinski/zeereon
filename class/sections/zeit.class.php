<?php

class etempus_zeit extends etempus {
	
		/***********************************
		 * overall
		 ***********************************/
		public function index(){
				$this->layout=true;
				echo $this->template->go("zeit_start.tpl");
				
		}
		
		
		
		
		/***********************************
		 * zeit buchen ausgabe
		 ***********************************/
		public function buchen(){
				$id=$this->getid();
				if (!$this->check_id("projekte",$id)){
					die("wrong id");
				}
				$msg="";
				if ($this->check_kostendach($id)){
						$proj=$this->table_item_array("projekte",$id);
						$txt=sprintf(__("warning_kostendach"), ET_KOSTENDACH_WARNUNG, $proj['kostendach'],utf8_encode($this->projekt_name($id)));
						$msg.=$this->warnung($txt);
				}
				
				$ts_timestart= (is_numeric($_COOKIE['etempus_timer_start'])) ? $_COOKIE['etempus_timer_start'] : time();
				$this->layout=false;
				$kid=$this->pid_to_kid($id);
				
				$arr=array(	"ansatz_liste"=>$this->ansatz_dd_liste($this->getid()),
							"date_now"=>date("d.m.Y",$ts_timestart),
							"time_now"=>date("H:i:s",$ts_timestart),
							"proj_name"=>$this->projekt_name($id),
							"kname"=>$this->kunde_name($kid),
							"msg"=>$msg,
							"msg_cancel_first"=>$this->warnung(__('cancel_first')),
							);
				
				echo $this->template->go("zeit_buchen.tpl",$this->utf8_array_encode($arr));
		}
		
		
		/***********************************
		* zeit buchen abschliessen
		***********************************/
		public function buchen_go(){
				$id=$this->getid();
				$err=false;
				if (!$this->check_id("projekte",$id)){
						$msg = __("error_id_fails");
						$err=true;
				}
				if ($_REQUEST['mode']=="manual"){
						$ts_start = strtotime($this->getvar("dstart")." ".$this->getvar("tstart"));
						$ts_ende = strtotime($this->getvar("dend")." ".$this->getvar("tend"));
				} else {
						$ts_ende=time();
						$ts_start = $ts_ende-$_REQUEST["secounds"];
				}
				if (!$ts_start || !$ts_ende){
						$msg=__("error_timestamp");
						$err=true;
				}
				//prüfen ende grösser als start
				if ($ts_ende<$ts_start){
						$msg=__("error_time_weird");
						$err=true;
				}
				//prüfen ob bereits zeit eingetragen
				$user_id = $this->get_user_id();
				$res=$this->db->query("SELECT * FROM zeit WHERE user_id='$user_id' AND zeit_start>=$ts_start AND zeit_ende<=$ts_ende");
				if ($res->numRows()>0){
						$msg=__("error_zeit_existiert");
						$err=true;
				}
				
				if ($err){
					echo $this->warnung($msg);
					die;
				}
				
				
				$this->db->query("INSERT INTO zeit VALUES (	NULL,
														'".$id."',
														'$user_id',
														'".$this->getvar("ansatz",true)."',
														'$ts_start',
														'$ts_ende',
														'".utf8_decode($this->getvar("txt",true))."');");
				$sekunden=$ts_ende-$ts_start;
				$msg= sprintf(__("zeit_eingetragen"),$this->formatiere_zeit($sekunden),utf8_encode($this->projekt_name($id)) );
				
				echo $this->nachricht($msg);
				
				die;

		}
		
		/***********************************
		* beleg buchen layout
		***********************************/
		function beleg_layout(){
				$id=$this->getid();
				if (!$this->check_id("projekte",$id)){
					die("wrong id");
				}
				$msg="";
				if ($this->check_kostendach($id)){
						$proj=$this->table_item_array("projekte",$id);
						$txt=sprintf(__("warning_kostendach"), ET_KOSTENDACH_WARNUNG, $proj['kostendach'],utf8_encode($this->projekt_name($id)));
						$msg.=$this->warnung($txt);
				}
				$this->layout=false;
				$kid=$this->pid_to_kid($id);
				echo $this->template->go("zeit_beleg.tpl",array("msg"=>$msg,
																"proj_name"=>utf8_encode($this->projekt_name($id)),
																"kname"=>utf8_encode($this->kunde_name($kid)),
																"today"=>date("d.m.Y")
																));
		}
		
		/***********************************
		* beleg buchen 
		***********************************/
		function beleg_buchen(){
				$this->layout=false;
				$id=$this->getid();
				$err=false;
				if (!$this->check_id("projekte",$id)){
						$msg = __("error_id_fails");
						$err=true;
				}
				
				//daten 'putzen'
				$d_tmp=strtotime($_REQUEST['datum']);
				$datum = ($d_tmp) ? $d_tmp : time();
				$b_tmp=intval($this->getvar('betrag',true));
				$betrag= (is_int($b_tmp)) ? $b_tmp : 0 ;
				$txt = $this->getvar('kommentar',true);
				
				$query="INSERT INTO beleg VALUES (	NULL, 
													'{$id}',
													'{$this->userid}',
													'$datum',
													'{$txt}',
													'{$betrag}' )";
				$this->db->query($query);
				$msg=sprintf(__("zeit_beleg_eintragen"),$betrag,utf8_encode($this->projekt_name($id)) );
				echo $this->nachricht($msg);
				
				
				
		}
		
	
}




?>

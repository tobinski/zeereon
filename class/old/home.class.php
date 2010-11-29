<?php
class etempus_home extends etempus {


		/**********************************************
		 submenu / tabs
		 "Beschriftung"=>"modul"
		***********************************************/		
		public function __construct(){
				$this->submenu = array();
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
					

				}
				return;
		}
		
		private function start(){
				
				//monatsarbeitszeit
				$month_start = strtotime("01" . date(".m.Y") . " 00:00:00");
				$month_end = strtotime("+1 month",$month_start);
				$uid=$this->get_user_id();
				$n=1;
				$arr=array();
				for ($i = $month_start+86400; $i <= $month_end; $i=$i+86400) {
					$start = $i - 86400;
					$res=$this->db->query("SELECT * FROM zeit WHERE zeit_start>={$start} AND zeit_ende<={$i} AND user_id='{$uid}' ORDER BY zeit_start;");
					$arr[$n]=0;
					if ($res){
						while ($row=$res->fetchRow(DB_FETCHMODE_ASSOC)){
							$sekunden=$row['zeit_ende']-$row['zeit_start'];
							$arr[$n]=$arr[$n]+$sekunden;
						}
						$arr[$n] = round($arr[$n]/3600,2);
					}
					$n++;
				}
				$monat=__("monate");
				$graph['data']=$arr;
				$graph['y_title']=__("zeit_in_stunden");
				$graph['x_title']=$monat[date("n")]." ".date("Y");
				$graphdata= base64_encode(serialize($graph));

				//wenn nachricht angekommen
				if ($this->getvar("home_msg",true)){
						$res=$this->db->query("INSERT INTO home_msg VALUES (NULL,'".$this->get_user_id()."','".$this->getvar("home_msg",true)."','".time()."');");
				}

				//nachrichten
				$msg_list="";
				include("html/home_div.php");
				$i=0;
				foreach ($this->table_array("home_msg","id",true) as $msg){
						$l = new template;
						$uname = $this->user_name($msg['user_id']);
						$l->assign( array(	"db_datum"	=>	date("d.m.Y H:i",$msg['zeit']),
											"db_name"	=>	$uname,
											"db_msg"	=>	$msg['message']));
						$l->auto_assign($tpl['home_msg_liste'],true);
						$msg_list.=$l->parse($tpl['home_msg_liste'],true);
						$i++;
						if ($i==ET_START_MSGNUM) break;
				}
				$uid = $this->get_user_id();
				//tagcloud
				$days = 321;
				$prev = time() - (86400*$days);
				$res = $res=$this->db->query("SELECT projekt_id  FROM zeit WHERE zeit_start>=$prev AND user_id=$uid");
				while ($row=$res->fetchRow()){
						$r[$row[0]]++;
				}
				if ($r){
						$cloud = new wordCloud;
						foreach ($r as $pid=>$num){
								$kid=$this->pid_to_kid($pid);
								$kname = $this->kunde_name($kid);
								$cloud->addWord("<a href=\"zeit.php?projekt_id={$pid}\"><nobr>".$this->projekt_name($pid)." ($kname)</nobr></a>",$num);
						}
						$tags = $cloud->showCloud();
				} else {
						$tags="";	
				}
				
				$username = $this->user_name($uid);
				
				$tpl=new template;
				$tpl->auto_assign("home.tpl");
				$tpl->assign(	array(	"db_tagcloud"	=>	$tags,
										"db_msg_list"	=>	$msg_list,
										"db_user"		=>	$username,
										"db_graphdata"	=>	$graphdata));
				$this->html=$tpl->parse("home.tpl");
		}
}

?>

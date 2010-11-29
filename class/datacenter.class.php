<?php

/********************************************************

 eTempus datacenter
 stellt eine api für upater-clients zur verfügung

*********************************************************/

class etempus_datacenter extends etempus {
	
	//einhänge-funktion datacenter
	public function build(){
			switch ($this->getvar("t")){
					case "send":
						$this->send();
						break;
						
					case "rcv":
						$this->recive();
						break;
						
					default: die(); break;
			}
			die();
	}

	//switcher senden
	private function send(){
			//wenn keine berechtigung
			if (!ET_DATACENTER_SEND) die("403");
			switch ($this->getvar("type")){
					case "xml":
						$this->send_xml();
						break;
					case "raw":
						$this->send_raw();
						break;
					case "dump":
						$this->send_dump();
						break;
					case "test":
						echo "etempus.datacenter.answer";
						break;
			}
	}
	
	private function send_xml(){
		$xml=array();
		switch ($this->getvar("data")){
				
				default:
					die();
					break;
		}
	}
	
	private function send_raw(){
			switch ($this->getvar("data")){
			
				default:
					$data['timelist']	=	$this->user_time_list(true);
					$data['kunden']		=	$this->table_array("kunden");
					$data['projekte']	=	$this->table_array("projekte");
					$data['ansatz']		=	$this->table_array("ansatz");
					break;
				
				case "get_user_id":
					echo $this->get_user_id();
					die;
				
				case "projekt_liste":
					if (!$this->check_id("kunden",$this->getid())) die("404"); 
					$data = $this->kunden_projekte($this->getid());
					break;
					
				case "ansatz_liste":
					if (!$this->check_id("projekte",$this->getid())) die("404"); 
					$arr = $this->table_item_array("projekte",$this->getid());
					//alle ansätze
					$i=0;
					if ($arr['ansatz']=="*"){
							foreach ($this->table_array("ansatz","name",false,"aktiv","1") as $ansatz){
								$data[$i]['name']	= $this->ansatz_name($ansatz['id']);
								$data[$i]['id']	= $ansatz['id'];
								$i++;
							}
					}
					//ausgewähle ansätze
					else {
							$ansatz_array = explode(",",$arr['ansatz']);
							foreach ($ansatz_array as $ansatz){
									$data[$i]['name'] = $this->ansatz_name($ansatz);
									$data[$i]['id']	= $ansatz;
									$i++;
							}
					}
					break;
					

			}
			echo gzcompress(serialize($data));
	}
	
	private function send_dump(){
			if (!ET_DATACENTER_DBDUMP) die("403");
			$data['beleg']		=		$this->table_array("beleg");	
			$data['kunden']		=		$this->table_array("kunden");
			$data['projekte']	=		$this->table_array("projekte");
			$data['zeit']		=		$this->table_array("zeit");
			$data['ansatz']		=		$this->table_array("ansatz");
			$data['user']		=		$this->table_array("user");
			$data['home_msg']	=		$this->table_array("home_msg");
			
			switch ($this->getvar("data")){
					case "xml":
						$this->xml_out($data,$function="etempus.datacenter.xmldump");
						break;
					case "raw":
						echo gzcompress(serialize($data));
						break;
					case "xml-gz":
						$d = gz_serialize($data);
						$this->xml_out($d,$function="etempus.datacenter.dump","gz_serialize");
						break;
			}
			
	}
	
	private function xml_out($array,$function="etempus.datacenter.xml",$compression="none",$extra_info=false){
			$xml['etempus']['info']['function']		= $function;
			$xml['etempus']['info']['compression']	= $compression;
			$xml['etempus']['info']['datatype']		= "xml";
			$xml['etempus']['info']['createdate']	= time();
			$xml['etempus']['info']['uid']			= uniqid();
			if (is_array($extra_info)){
				foreach ($extra_info as $k=>$v)
					$xml['etempus']['info'][$k]=$v;
			}
			$xml['etempus']['data'] = $array;
			header("Content-type: text/xml");
			echo arr2xml($xml);
	}
	
	
	
	
	/*******************************************************
	
	 daten empfangen (achtung, kann noch unsicher sein)
	 
	 beispiel abfrage (SELECT id,kommentar FROM zeit WHERE ansatz_id=2 AND user_id=1;)
	 $arr['tbl_name']  = "zeit";
	 $arr['function']  = "select";
	 $arr['key']       = "id,kommentar"; //wenn false alles
	 $arr['values']    = array("ansatz_id"=>"2","user_id"=>"1"); 
	 $arr['dadatype']  = "raw" (default) oder "xml" oder "xml-gz"
	
	 //beispiel insert (false ergibt null, z.b für ai)
	 $arr['tbl_name']  = "user_msg";
	 $arr['function']  = "insert";
	 $arr['values']    = array(false,"1","message","142356423"); 
	
	 beispiel update: (UPDATE user_msg SET message='neue nachricht' WHERE id=22;)
	 $arr['tbl_name']  = "user_msg";
	 $arr['function']  = "update";
	 $arr['key']	   = 22
	 $arr['values']    = array("message"=>"neue nachricht"); 
	 
	 beispiel delete:
	 $arr['tbl_name']  = "zeit";
	 $arr['function']  = "delete";
	 $arr['key']	   = 4

	********************************************************/
	private function recive(){
			if (!ET_DATACENTER_RCV) die("403");
			$arr = @gz_unserialize($_REQUEST['data']);
			if (!is_array($arr) || !$arr['tbl_name']) die("no_array");
			$arr['tbl_name'] = $this->db->escapeSimple($arr['tbl_name']);
			switch (strtolower($arr['function'])){
					case "update":
						if (!$arr["key"]) die("no_key");
						$query="UPDATE {$arr['tbl_name']} SET ";
						foreach ($arr["values"] as $key=>$value){
							$key=$this->db->escapeSimple($key);
							$value=$this->db->escapeSimple($value);
							$query.= "$key='$value',";
						}
						$query=substr($query,0,-1) . " WHERE id={$arr['key']} ;";
						break;
					case "insert":
						$query="INSERT INTO {$arr['tbl_name']} VALUES ( ";
						foreach ($arr["values"] as $value){
							$val = ($value) ? "'".$this->db->escapeSimple($value)."'" : "NULL";
							$query.="{$val},";
						}
						$query=substr($query,0,-1) . ");";
						break;
						
						
					//select	
					case "select":
						if (!$arr["key"]) $arr['key']="*";
						$key=$this->db->escapeSimple($arr['key']);
						$query = "SELECT {$key} FROM {$arr['tbl_name']}";
						//wenn where und and
						if (is_array($arr['values'])){
							$i=1;
							foreach($arr['values'] as $key=>$value){
								$key=$this->db->escapeSimple($key);
								$value=$this->db->escapeSimple($value);
								$word=($i==1) ? "WHERE" : "AND";
								$query.=" {$word} {$key}='{$value}'";
								$i++;
							}
						}
						$query.=";";
						$res = $this->db->query($query);
						if (!$res) die("db_fail: $query");
						$result=array();
						$i=0;
						//resultat in array laden
						while ($res->fetchRow(DB_FETCHMODE_ASSOC)){
							foreach ($row as $k=>$v)
								$result[$i][$k]=$v;
							$i++;
						}
						
						//ausgabeart switchen
						switch($arr['datatype']){
								//xml-sheet
								case "xml":
									$this->xml_out($result,"etempus.datacenter.queryresult","none",array("query"=>$query));
									break;	
								//xml-tgz
								case "xml-gz":
									$d = gz_serialize($result);
									$this->xml_out($d,$function="etempus.datacenter.queryresult","gz_serialize",array("query"=>$query));
									break;
								//wenn nix angegeben: raw
								default:
									echo gzcompress(serialize($result));
									break;
						
						}
						die;
						break;
					
					case "delete":
						if (!ET_DATACENTER_DEL || $arr['tbl_name']=="user") die("403");
						if (!$arr["key"]) die("no_key");
						$key=$this->db->escapeSimple($arr['key']);
						$query="DELETE FROM {$arr['tbl_name']} WHERE id=$key ;";
						break;
					
					default:
						die("no_word");
					
			}
			if ($query){
				$res=$this->db->query($query);
				if ($res) echo "ok"; else echo "db_fail: $query";
			}
	}

}


/************************************************

Funktionen

*************************************************/



//array in xml-wandeln 4 level tief
function arr2xml($arr,$entry="entry"){
	$xml="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
	foreach ($arr as $key1=>$val1){
		if (is_numeric($key1)) $key1=$entry;
	   $xml.="<$key1>";
	   if (is_array($val1)){
	      foreach ($val1 as $key2=>$val2){
	         if (is_numeric($key2)) $key2=$entry;
	         $xml.="<$key2>";
	         if (is_array($val2)){
	            foreach ($val2 as $key3=>$val3){
	                if (is_numeric($key3)) $key3=$entry;
	                $xml.="<$key3>";
	                if (is_array($val3)){
					    foreach ($val3 as $key4=>$val4){
					    		if (is_numeric($key4)) $key4=$entry;
							   $xml.="<$key4>";
							   if (is_array($val4)){
					    			foreach ($val4 as $key5=>$val5){
					    				if (is_numeric($key5)) $key5=$entry;
					    				$xml.="<$key5>";
					    				if (is_array($val5)){
											foreach ($val5 as $key6=>$val6){
												if (is_numeric($key6)) $key6=$entry;
												$xml.="<$key6>$val6</$key6>";
											}
										} else {
											$xml.=$val5;
										}
										 $xml.="</$key5>";
					    			}
					    		} else {
					    			$xml.=$val4;
					    		}
							   $xml.="</$key4>";
						}
					 } else {
					    $xml.=$val3;
					 }
	               $xml.="</$key3>";
	             }
	         } else {
	            $xml.=$val2;
	         }
	         $xml.="</$key2>";
	      }
	   } else {
	      $xml.=$val1;
	   }
	   $xml.="</$key1>";
	}
	return $xml;
}

function gz_serialize($string){
    return strtr(base64_encode(addslashes(gzcompress(serialize($string),9))), '+/=', '-_,');
}
function gz_unserialize($encoded){
    return unserialize(gzuncompress(stripslashes(base64_decode(strtr($encoded, '-_,', '+/=')))));
}

?>

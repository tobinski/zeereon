<?php

class et_config {

		function __construct(){
				db_init();
				$this->db=db_make();
		}

		function load_key($keyname){
				$keyname=$this->db->escapeSimple($keyname);
				$res = $this->db->query("SELECT value FROM config WHERE name='{$keyname}'");
				if ($res){
					$a=$res->fetchRow($res);
					if (is_array($a)){
						return $a[0];
					}
					else { 
						return false;
					}
				} else {
					return false;
				}
				
		}
		
		function set_key($keyname,$value){
				$keyname=$this->db->escapeSimple($keyname);
				$value=$this->db->escapeSimple($value);	
				$q = $this->db->query("UPDATE config SET value='{$value}' WHERE name='{$keyname}' ;");
				if ($q) {
					return true;
				} else {
					return false;
				}
		}
		
		function make_key($keyname,$value){
				$keyname=$this->db->escapeSimple($keyname);	
				$value=$this->db->escapeSimple($value);
				$q = $this->db->query("INSERT INTO config VALUES (NULL,'{$keyname}','{$value}')");
				if ($q) {
					return true;
				} else {
					return false;
				}
		}

}
?>

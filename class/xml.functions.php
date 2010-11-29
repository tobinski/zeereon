<?php
/***********************************************************************
 * 
 * {$ID}
 * xml-functions for eecms 2.2
 * (c) 2009 Cyrill von Wattenwyl, e23.ch
 * License: GNU GPL
 * 
 **********************************************************************/ 



/*******************************************************
 * array in xml-string wandeln
 * @param arr das array
 * @param entry xml-tag wenn schlüssel numerisch ist
 * @return xml string
 * ****************************************************/

function arr2xml($arr,$entry="entry"){
		$xml="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
		$xml.=arr2xml_sub($arr,$entry);
		return $xml;
}


/*******************************************************
 * interne funktion für arr2xml, ruft sich selbst auf
*******************************************************/

function arr2xml_sub($arr,$entry){
		foreach ($arr as $key=>$val){
				if (is_numeric($key)) $key=$entry;
				$xml.="<$key>"; 
				if (is_array($val)){
					$xml.=arr2xml_sub($val,$entry);
				} else {
					$xml.=$val;
				}
				$xml.="</$key>\n";
		}
		return $xml;
}




/*******************************************************
 * array in xml-string wandeln
 * @param s string
 * @return array xml_array
 * ****************************************************/

function xml2arr($s) {
		if ($Atmp1=explode('>',$s,2)){
				xml2arr_sub($A,$Atmp1[1]);
		}
		return $A;
}


/*******************************************************
 * interne funktion für xml2arr, ruft sich selbst auf
*******************************************************/

function xml2arr_sub(&$A,$s) {
		global $error_msg;
		for ($c=0;ereg('<([^<>/ ]*)( [^<>]*)?/>(.*)',$s,$Atmp1) 
			 or   ereg('<([^<>/ ]*)( [^<>]*)?>(.*)',$s,$Atmp1) 
			 and  $Atmp2=explode('</'.$Atmp1[1].'>',$Atmp1[3],2);++$c) {
				$E=array();
				$tag=$Atmp1[1];
				if ($Atmp1[2]) {
						$Atmp3=explode(' ',substr($Atmp1[2],1));
						foreach ($Atmp3 as $d) {
								$Atmp4=explode('=',$d,2);
								$Atmp4[1]=ereg_replace('^"(.*)"$','\1',$Atmp4[1]);
								$E[$Atmp4[0]]=$Atmp4[1];
						}
				}
				if ($Atmp2) {
						if (!xml2arr_sub($E,$Atmp2[0])) {
							$E=$Atmp2[0];
						}
						$s=$Atmp2[1]; 
						$Atmp2=false;
				} else {
						$s=$Atmp1[3]; 
				}
				if ($A[$tag]) {
						if (!is_array($A[$tag]) or !$A[$tag][0]) {
								$Atag=$A[$tag]; 
								unset($A[$tag]);
								$A[$tag][]=$Atag;
						}
						if ($E) {
								$A[$tag][]=$E;
						}
				}
				else {
						if ($E) {
								$A[$tag]=$E;
								if ($tag=='$ErrorMsg') $error_msg=$E;
						}
				}
		}
		return $c;
}

?>

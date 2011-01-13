<?php

/********************************
sprachlader-klasse für etempus
bitte nichts ändern
********************************/

class etempus_lang_loader {
	function __construct(){	
			$this->lang=array();
			foreach (glob(dirname(__FILE__)."/../text/*.php") as $file) {
					$et_lang=false;
					@include($file);
					if ($et_lang){
							unset($txt);
							$f = str_replace("text.","",basename($file));
							$f = str_replace(".php","",$f);
							$this->lang[$f]=$et_lang;
					}
			}
	}
	
}


?>

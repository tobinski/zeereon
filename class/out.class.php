<?php

class etempus_out extends etempus {
	
	
    
    function __construct($etempus_object) {
    
    		$this->submenu_tmp = $etempus_object->submenu;
    		$this->user_id=$this->get_user_id();
    		$this->menu_array = array( 	"index.php"			=>	"<img src='style/icon/16/home.png' />{home}",
    									"zeit.php"			=>	"<img src='style/icon/16/zeit.png' />{zeit}",
										"kunde.php"			=>	"<img src='style/icon/16/kunde.png' />{kunde}",
    									"projekt.php"		=>	"<img src='style/icon/16/projekt.png' />{projekt}",
										"auswertung.php"	=>	"<img src='style/icon/16/auswertung.png' />{auswertung}",
										"einstellungen.php"	=>	"<img src='style/icon/16/edit.png' />{einstellungen}",
										"logout.php"		=>	"<img src='style/icon/16/exit.png' />{logout}");
			$this->load_style();
			$this->load_plugs();
			$this->build_menu();
			$this->build_submenu($this->submenu_tmp);
			$this->build_style_dd();
			$this->build_site();
			$this->assign_content($etempus_object->html);
    }

    function load_style(){
			
		//wenn user style ?ndert
    	$user_arr=$this->table_item_array("user",$this->get_user_id());
    	if ($this->getvar("new_style")){
					$this->tpl_name=$this->getvar("new_style");
					$user_id=$this->get_user_id();
					$this->db->query("UPDATE user SET layout='".$this->getvar("new_style",true)."' WHERE id=$user_id ;");
    	} elseif ($user_arr['layout']){
    		$this->tpl_name=$user_arr['layout'];
    	} else {
    		$this->tpl_name=ET_DEFAULT_STYLE;
    	}
    	
    	include("style/{$this->tpl_name}/style.php");
    	$this->html_menu_0 		= $style["menu_0"];
    	$this->html_menu_0_a	= $style["menu_0_a"];
    	$this->html_menu_1 		= $style["menu_1"];
    	$this->html_menu_1_a	= $style["menu_1_a"];
    	$this->active_menu0		= basename($_SERVER['SCRIPT_FILENAME']);
    	$this->layout			= file_get_contents("style/{$this->tpl_name}/etempus.html");
    	$this->stylepath		= "style/{$this->tpl_name}/";
    }

    function build_menu(){
    	$this->menu="";
    	foreach ($this->menu_array as $href=>$txt){
    		preg_match_all("/\{([a-zA-Z0-9_-]*)\}/U",$txt,$res_arr,PREG_PATTERN_ORDER);
    		if ($href==$this->active_menu0){
    			$lang_string=str_replace("{".$res_arr[1][0]."}",__($res_arr[1][0]),$txt);
    			$menu=str_replace("{text}",$lang_string,$this->html_menu_0_a);
    			$this->menu.=str_replace("{location}",$href,$menu);
    		} else {
    			$lang_string=str_replace("{".$res_arr[1][0]."}",__($res_arr[1][0]),$txt);
    			$menu=str_replace("{text}",$lang_string,$this->html_menu_0);
    			$this->menu.=str_replace("{location}",$href,$menu);
    		}
    	}
    }
    
    function build_submenu($menu_array){
    	$this->submenu="";
    	if ($menu_array){
			foreach ($menu_array as $text=>$modul){
					preg_match_all("/\{([a-zA-Z0-9_-]*)\}/U",$text,$res_arr,PREG_PATTERN_ORDER);
					$lang_string=str_replace("{".$res_arr[1][0]."}",__($res_arr[1][0]),$text);
					$location="?";
					if ($modul) $location.= "modul={$modul}";
					if ($this->getid()) $location.="&id=".$this->getid();
					$chk2=$this->getvar("modul")."&plugin_name=".$this->getvar("plugin_name");
					if ($this->getvar("modul")==$modul or $chk2==$modul){
						$menu=str_replace("{text}",$lang_string,$this->html_menu_1_a);
					} else {
						$menu=str_replace("{text}",$lang_string,$this->html_menu_1);
					}
					$this->submenu.=str_replace("{location}",$location,$menu);
			}
    	} else {
    		$this->submenu="&nbsp;";
    	}
    }

    function build_site(){
				$tpl=new template;
				$tpl->assign( array("menu"			=>$this->menu,
									"submenu"		=>$this->submenu,
									"style_path"	=>$this->stylepath
						     ));
				$this->out= $tpl->parse($this->layout,true);

    }


    function assign_content($content){
			//plugin-inhalt ausgeben
			if ($this->getvar("modul")=="plugin"){
					$name = $this->getvar("plugin_name");
					$inc = "plugins/{$name}/inc.php";
					$plugin = false;
					if (ET_PLUG_DBG){ include($inc); } else { @include($inc); }					
					if ($plugin and class_exists($plugin['class'])){
							if ($plugin['admin_only'] and $this->user_id!=1) { 
									$content=$this->warnung(__('not_allowed')); 
							} else {
									$p = new $plugin['class'];
									$p->dir="plugins/{$name}/";
									$p->build();
									$content = $p->html;
							}
					
					} else {
							$content = $this->warnung(sprintf(__("plugin_error"),$this->getvar("plugin_name")).
													  "<br /><br />Debug:<br /><pre>\n".print_r($plugin,true)."</pre>");
					}
			}
			$this->out= str_replace("{top_text}","",/* $this->style_dd,*/ $this->out);    
			$this->out = str_replace("{content}", $content, $this->out);
    }

    function flush(){
    	@header('Content-Type: text/html; charset=ISO-8859-1');
    	echo $this->out;
    	flush();
    }
    
    
    //plugins laden
    function load_plugs(){
    		$now_open = str_replace(".php","",$this->active_menu0);
    		foreach ( glob(dirname(__FILE__)."/../plugins/*",GLOB_ONLYDIR) as $plug){
    				$plugin=false;
    				$lang=false;
    				$pfile=$plug."/inc.php";
    				if (is_file($pfile)) { 
							if (ET_PLUG_DBG){
								include($plug."/inc.php");
							} else {
								@include($plug."/inc.php");
							} 
					}
    				//wenn plugin existiert
    				if ($plugin){
    						
							if ($plugin['admin_only']){ if ($this->user_id!=1) { continue; } }
							
							//wenn plugin an aktuellem platz
    						if ($now_open==$plugin['place']){
    								//neue texte laden
    								$GLOBALS['txt']=array_merge($GLOBALS['txt'],$lang[$_SESSION['lang']]);
    								//in menu linken
    								$this->submenu_tmp[$plugin['menu_str']]="plugin&plugin_name={$plugin['name']}";
    						}
							
    						
       				}
    		}
    		
    }


}

?>

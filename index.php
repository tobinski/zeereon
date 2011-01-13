<?php


/*
section={zeit|auswertung|config}
*/


do_etempus();



function do_etempus(){
		include('inc.php');
		$dn=dirname(__FILE__);
		if (!$_REQUEST['section']){ $_REQUEST['section']="zeit"; }
		if (!$_REQUEST['function']){ $_REQUEST['function']="index"; }
		$classfile="{$dn}/class/sections/{$_REQUEST['section']}.class.php";
		if (file_exists($classfile)){
			include($classfile);
			$c="etempus_".$_REQUEST['section'];
			$func=$_REQUEST['function'];
			if (class_exists($c)){
				$obj=new $c;
				if (method_exists($obj,$func)){
						ob_start();
							$obj->$func();
							$content=ob_get_contents();
						ob_end_clean();
						if ($obj->layout===true){
								etempus_build_output($content);
						} else {
								echo $content;
						}
				} else {
					etempus_build_output_err("this function () does not exists");
				}
			} else {
				etempus_build_output_err('this class does not exists');
			}
		} else {
				etempus_build_output_err("this section does not exists ({$classfile})");
		}
		
}

//Edit tobinski
function etempus_build_output($content){
	$tpl=new template;
	$menu=etempus_build_menu();
	$arr=array(	"content"	=>	$content,
				"tabs_top"	=>	$menu['top'],
				"tab_submenu"=>	$menu['sub'],
				"username" => get_username());
	echo $tpl->go("layout.tpl",$arr);
	
}

function etempus_build_output_err($content){
	$et=new etempus();
	etempus_build_output($et->warnung($content));
}

//Edit tobinski
function get_username ()
{
	$et=new etempus;
	return $et->user_name($et->get_user_id());
}


function etempus_build_menu(){
		$dn=dirname(__FILE__);
		$et=new etempus;
		if (!$_REQUEST['section']){ $_REQUEST['section']="zeit"; }
		$topmenu="";
		$submenu="";
		include("tabs.php");
		
		foreach ($tabs as $mod=>$val){
			
				//top-tab nur wenn eingestellt
				if ($val['show']){
						$html="";
						$active=($mod==$_REQUEST['section']) ? "active" : "inactive" ;
						if ($val['image']){ $html.="<img src='{$val['image']}' />"; }
						$topmenu.= "<a class='{$active}' href='?section={$mod}'>{$html}".__($val['text'])."</a>";	
				}
				
				//subtabs bei aktivem top immer erstellen
				if ($mod==$_REQUEST['section']){
						//plugins laden
						$val=array_merge($val,etempus_load_plugs($mod));
						//subtabs erstellen
						foreach ($val as $item){
								if (is_array($item)){
										//nur admin
										if ($item['admin_only']){ if ($et->userid!=1) { continue; } }
										
										$active=false;
										//wenn plugin aktiv 
										if ($_REQUEST['function']=="plugin" and $_REQUEST["plugin_name"]==$item['plugin_name']){
											$active=true;
										} 
										// sonst prüfen ob aktiv
										else {
											if ($_REQUEST['function']==$item['function']){
												$active=true;
												if ($_REQUEST['modul']){
													$active=false;
													if ($_REQUEST['modul']==$item['req']['modul']){
														$active=true;
													}
												}
											}
										}
										
										$act="";
										if ($active){ $act=" class=\"active\""; }
										$img="";
										if ($item['image']){ $img="<img src=\"{$item['image']}\" />"; }
										$oncl="";
										if ($item['onclick']){ $oncl=" onclick=\"{$item['onclick']}\""; }
										
										$href="?section={$mod}&function={$item['function']}";
										if (is_array($item['req'])){ $href.="&".http_build_query($item['req']); }
										if ($item['plugin_name']) { $href.="&plugin_name=".$item['plugin_name']; }
										
										$submenu.="<li{$act}{$oncl}><a href='{$href}'>{$img}".__($item['text'])."</a></li>";
								}
						}
				}
		}
		return array('top'=>$topmenu,
					 'sub'=>$submenu );
	
}






function etempus_load_plugs($place){
		$ret=array();
		$et=new etempus;
		foreach ( glob(dirname(__FILE__)."/plugins/*",GLOB_ONLYDIR) as $plug){
				$plugin=false;
    			$lang=false;
    			$pfile=$plug."/inc.php";
    			if (is_file($pfile)) { 
						if (ET_PLUG_DBG){
							include($plug."/inc.php");
						} else {
							include($plug."/inc.php");
						} 
				}
    			//wenn plugin existiert
				if ($plugin){
						if ($plugin['admin_only']){ if ($et->userid!=1) { continue; } }
						//wenn plugin an aktuellem platz
    					if ($place==$plugin['place']){
								//neue texte laden								
    							$GLOBALS['txt']=array_merge($GLOBALS['txt'],$lang[$_SESSION['lang']]);
    							//in menu linken
								$a=array(	"function"	=> "plugin",
											"section"	=> $place,
											"plugin_name"=>$plugin['name'],
											"image"		=> $plugin['menu_img'],
											"text"		=> str_replace(array("{","}"),"" ,$plugin['menu_str']),
											"onclick"	=> $plugin['menu_img'] );
								array_push($ret,$a);
								
    					}
       			}
    	}
		return $ret;
 
 }






?>

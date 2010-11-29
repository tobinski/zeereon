<?php

session_start();
$site = "http://".uniqid().":".uniqid()."@".$_SERVER['HTTP_HOST'];
$root_path=str_replace("index.php","",$_SERVER['SCRIPT_NAME']);
$logout_url=$site.$root_path;
$root_a=explode("@",str_replace("logout/","",$site.$root_path));
$root="http://{$root_a[1]}";

if (!$_SESSION['logout_fwd']){
	$_SESSION['logout_fwd']=true;
	header("Location: $logout_url");
	exit;
} else {
	$_SESSION['logout_fwd']=false;
	et_logout_site($root);
}


function et_logout_site($root){
		global $txt;
		include("../text/text.{$_SESSION['lang']}.php");
		include("../class/tpl.class.php");
		function __($str){
			global $txt;
			return $txt[$str];
		}
		$a=array('url_root'=>$root);
		$t=new template;
		$t->dir="../html/";
		$content=$t->go("logout.tpl",$a);
		$tpl=new template;
		$tpl->dir="../html/";
		$arr=array(	"content"	=>	$content,
					"tabs_top"	=>	"",
					"tab_submenu"=>	"");
		$c=str_replace("./css/","../css/",$tpl->go("layout.tpl",$arr));
		echo $c;
}

?>

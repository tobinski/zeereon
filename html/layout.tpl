<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>eTempus 1.3</title>
<!-- (en) Add your meta data here -->
<!-- (de) Fügen Sie hier Ihre Meta-Daten ein -->


<link href="./css/central_etempus.css" rel="stylesheet" type="text/css"/>
<link href="./css/dev.css" rel="stylesheet" type="text/css"/>
<link type="text/css" href="./css/custom-theme/jquery-ui-1.8.custom.css" rel="Stylesheet" />	

<!--[if lte IE 7]>
<link href="./css/patches/patch_etempus.css" rel="stylesheet" type="text/css" />
<![endif]-->
		
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery.plugins.js"></script>
		<script type="text/javascript" src="js/jquery.roundcorner.js"></script>
		<script type="text/javascript" src="js/etempus.js"></script>
		<script type="text/javascript" src="js/etempus.timer.js"></script>
		<script type="text/javascript" src="js/etempus.list.js"></script>
		<script type="text/javascript" src="index.php?section=zeit_ajax&function=js_lang"></script>	
	</head>
<body>
<!-- skip link navigation -->

<div class="page_margins">
  <div class="page">
    <div id="header" role="banner">
      <div id="topnav" role="contentinfo">
        <span>Guten Tag {username} <a href="logout/" id="logout">{logout}</a></span>
      </div>
      <h1>&nbsp;</h1>
	  
	  <div id="ttop">{tabs_top}</div>
    </div>
	
	
    <!-- begin: main navigation #nav -->
    <div id="nav" role="navigation">
      <div class="hlist">
        <ul id="submenu">
          {tab_submenu}
        </ul>
      </div>
    </div>
    <!-- end: main navigation -->
    <!-- begin: content area #main -->
    <div id="main">
      <!-- begin: #overall - folder over the columns -->
      <div id="overall">
	    <div id="overall_content" class="clear">
			{content}
	    </div>
      </div>
      <!-- end: #overall -->


    </div>
    <!-- end: #main -->
	
	
    <!-- begin: #footer -->
    <div id="footer" role="contentinfo"></div>
    <!-- end: #footer -->
  </div>
</div>
<!-- full skiplink functionality in webkit browsers -->
<script src="./yaml/core/js/webkit-focusfix.js" type="text/javascript"></script>
</body>
</html>


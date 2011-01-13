<h1>{alles_auswerten}</h1>
<!--{oo_export_all_einleitung}!-->
<br />
<script type="text/javascript">
	function toggle_elm(obj){
		var id = obj.id.split('_');
		var c=$('#cb_'+id[1]).get(0);		
		if (c.checked==true){ c.checked=false; $(obj).removeClass('selected'); } else { c.checked=true; $(obj).addClass('selected'); }
		
	}
	
	function toggle_cb(c){
		var id = c.id.split('_');
		var obj=$('#e_'+id[1]);
		if (c.checked==true){ c.checked=false; obj.removeClass('selected'); } else { c.checked=true; obj.addClass('selected'); }
	}
	
</script>

<form id="form1" name="form1" method="POST">
	
	
	<table class="struct">
		
		{db_liste}
	
	</table>
	
	{req_str}
	<div class="base">
		<img src="images/icon/arrow_ltr.png" class="base" />
		<a href="javascript:void(0)" onclick="$('#form1 input').attr('checked',true);$('tr.light,tr.dark').addClass('selected');">{check_all}</a> / 
		<a href="javascript:void(0)" onclick="$('#form1 input').attr('checked',false);$('tr.light,tr.dark').removeClass('selected');">{uncheck_all}</a> 
	</div>
	<br /><br />
	<input type="hidden" name="go" value="true" />
	<input type="submit" value="{download_zip_file}" />
</form>




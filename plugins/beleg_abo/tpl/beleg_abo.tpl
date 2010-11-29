<script type="text/javascript">
	function del_item(itemid){
		var chk = confirm("{zeit_eintrag_del}");
		if (chk){
			$('#del_id').val(itemid);
			$('#delform').get(0).submit();
		} else { return false; }
	}
</script>
<table>
	<tr>
		<td><img src="plugins/beleg_abo/img/icon.png" style='margin-right:10px;' /></td>
		<td><h1>{abo}</h1>
		</td>
	</tr>
</table>

{msg}

{einleitung}<br /><br />
<b><a href="#" onclick="$('#box_new_abo').toggle('fast');">{neues_abo}</a> | <a href="#" onclick="$('#box_uurl').toggle('fast');">{show_updater_url}</a></b><br />
<br />

<div class="box_normal" id="box_uurl" style="display:none;">
	<h3>{updater_url}<div style="float:right;"><img src="plugins/beleg_abo/img/del.png" style="cursor:pointer;" onclick="$('#box_uurl').toggle('fast');" /></div></h3>
	<div class="boxbox">
		<pre style="font-size:14px;font-weight:bold;">{uurl}</pre><br />
		<input style="font-size:12px;font-weight:bold;" type="submit" value="{button_ok}" onclick="toggle_uurl();"/>
	</div>
</div>



<form method="POST">
	<div class="box_normal" id="box_new_abo" style="display:none;">
		<h3>{neues_abo}<div style="float:right;"><img src="plugins/beleg_abo/img/del.png" style="cursor:pointer;" onclick="$('#box_new_abo').toggle('fast');" /></div></h3>
		<div class="boxbox">
			<h4 style="margin-bottom:5px;">{kunde_s}/{projekt_s}</h4>
			<div style="width:100px;float:left">{kunde_s} : </div>{kunde_dd_liste}<br />
			<div style="width:100px;float:left">{projekt_s}: </div><div id="projekt_liste"><br /></div>
			<br />
			<h4 style="margin-bottom:5px;">{wiederhole}:</h4>
			<input type="radio" name="timespan" value="w" id="ts_w"  /><label for="ts_w">{woechentlich}</label><br />
			<input type="radio" name="timespan" value="m" id="ts_m" checked /><label for="ts_m">{monatlich}</label><br />
			<input type="radio" name="timespan" value="j" id="ts_j" /><label for="ts_j">{jaehrlich}</label><br />
			<input type="radio" name="timespan" value="c" id="ts_c" /><label for="ts_c">{alle}</label> <input type="text" size="3" name="c_days"/> <label for="ts_c">{tage}</label><br />
			<br />
			<h4 style="margin-bottom:5px;">{betrag}</h4>
			<input type="text" size="5" name="betrag" id="betrag" /><br />
			<br /><br />
			<h4 style="margin-bottom:5px;">{kommentar}</h4>
			<textarea name="desc"></textarea>
			<br /><br />
			
			<h4 style="margin-bottom:5px;">{erste_buchung}</h4>
			<input type="radio" name="book_now" value="1" id="bn1" /><label for="bn1">{jetzt_buchen}</label>
			<input type="radio" name="book_now" value="0" id="bn0" checked /><label for="bn0">{buche_spaeter}</label>
			
			<br /><br />
			<input style="font-size:12px;font-weight:bold;" type="submit" value="{button_erstellen}" name="create_new" />
		</div>
	</div>
</form>

<br />
<h3>{vorhandene_abos}</h3>
<br />

<table class="struct">
		<tr>
			<th>{kunde_s}</th>
			<th>{projekt_s}</th>
			<th>{betrag}</th>
			<th>{wiederhole}</th>
			<th>{last}</th>
			<th>&nbsp;</th>
		</tr>
		{db_abos}
</table>

<form method="POST" id="delform">
	<input type="hidden" name="del_id" id="del_id" value=""/>
</form>

<script type="text/javascript">
	{js_loader}
</script>

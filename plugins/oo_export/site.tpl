<table>
		<tr>
			<td><img src="plugins/oo_export/icon_calc.jpg" style='margin-right:10px;' /></td>
			<td><h1>{oo_export}</h1>
			</td>
		</tr>
	</table>
	{einleitung}<br /><br />
	
{msg}
<script language="JavaScript" id="jscal1x">
		

		var opened=false;
		var type_all=false;
		var type_single=false;
		
		function show_single(){
				$('#step1_1').show();
				if (opened==false){ $('step2_1').hide(); }
				type_all=false;
				type_single=true;
				$('#list_a').show();
				$('#button_go_single').show();
				$('#button_go_all').hide();
		}
		
		function show_all(){
				$('#step1_1').show();
				if (opened==false){ $('step2_1').hide(); }
				type_all=true;
				type_single=false;
				$('#list_a').hide();
				$('#button_go_single').hide();
				$('#button_go_all').show();
		}
		
		function show_kbased(){
				opened=true;
				$('#step2_1').show();
				$('#list_k').show();
				$('#list_p').hide();
		}
		
		function show_pbased(){
				opened=true;
				$('#step2_1').show();
				$('#list_k').show();
				$('#list_p').show();
		}
		$(document).ready(function () { 
				$("input#von").datepicker( { showOn: 'button', buttonImageOnly: true, buttonImage: 'images/form_datepicker.png' });
				$("input#bis").datepicker( { showOn: 'button', buttonImageOnly: true, buttonImage: 'images/form_datepicker.png' });
				lade_projekt_liste($('select[name=kunde]').val());
		});

		
</script>


<form method="POST" id="form1">
	
	
	<div class="box_normal">
		<h3>{art_auswaehlen}</h3>
		<div class="boxbox">
			<input type="radio" name="XX" value="kunde" onclick='show_single();' id="ll01" /><label for="ll01">{einzelne_rechnung}</label><br />
			<input type="radio" name="XX" value="projekt" onclick='show_all();' id="ll02" /><label for="ll02">{alle_rechnungen}</label> <br />
			<br />
			<div id="step1_1" style="display:none;">
				{von_typ}:
				<input type="radio" name="art" value="kunde" onclick='show_kbased();' id="ll03" /><label for="ll03">{kundenbasiert}</label>
				<input type="radio" name="art" value="projekt" onclick='show_pbased();' id="ll04" /><label for="ll04">{projektbasiert}</label> <br />
			</div>
		</div>
	</div>
	
	<div id="step2_1" style="display:none;">
	
	
	<div class="box_normal" style="display:none;" id='list_a'>
		<h3>{kunde_waehlen} / {projekt_waehlen}</h3>
		<div class="boxbox">
			<div style="display:none;" id="list_k">
				<div style="width:100px;float:left">
				{kunde_s} : </div>{kunde_dd_liste}<br />
			</div>
			<div style="display:none;" id="list_p">
				<div style="width:100px;float:left">
				{projekt_s} : </div> <div id="projekt_liste"></div><br />
			</div>
		</div>
	</div>
	
	
		
	<div class="box_normal">
		<h3>{zeitraum_waehlen}</h3>
		<div class="boxbox">
			<table width="100%">
				<tr>
					<td><input type="radio" checked name="zeitraum_typ" value="monat" id="ll05" /><label for="ll05"> {monat}</label></td>
					<td>{monate_dd_liste}</td>
				</tr>
				<tr>
					<td><input type="radio" name="zeitraum_typ" value="jahr" id="ll06" /><label for="ll06"> {jahr}</label></td>
					<td>{letzte_jahre_dd}</td>
				</tr>
				<tr>
					<td width="25%"><input type="radio" name="zeitraum_typ" value="user" id="ll07" /><label for="ll07"> {benutzerdefiniert}</label></td>
					<td><div style="width:40px;float:left;">{von}:</div> 
					<input type="text" name="von" size="10" id="von" /> <br />
					<div style="width:40px;float:left;">{bis}:</div> 
					<input type="text" name="bis" size="10" id="bis" />	<br />		
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="box_normal">
		<h3>{details_waehlen}</h3>
		<div class="boxbox">
			<b>{spalten}: </b>
			<input type="checkbox" checked name="s_datum" id="ll08" /> <label for="ll08">{datum}</label>
			<input type="checkbox" checked name="s_zeit" id="ll09" /> <label for="ll09">{zeit}</label>
			<input type="checkbox" checked name="s_projekt" id="ll10" /> <label for="ll10">{projekt_s}</label>
			<input type="checkbox" checked name="s_kommentar" id="ll11" /> <label for="ll11">{kommentar}</label>
			<input type="checkbox" checked name="s_user" id="ll12" /> <label for="ll12">{benutzer}</label>
			<input type="checkbox" checked name="s_ansatz" id="ll13" /> <label for="ll13">{ansatz}</label>
			<input type="checkbox" checked name="s_kosten" id="ll14" /> <label for="ll14">{kosten}</label>
			<br /><br />
			<b>{mwst}:</b>
			<input type="checkbox" checked name="mwst" id="ll15" /> <label for="ll15">{mwst_mitrechnen}</label>
			{mwst_satz}:<input type="text" name="mwst_satz" value="8" size="3" /> %
			<br /><br />
			<b>{sonstiges}:</b>
			<input type="checkbox" checked name="r_totalzeit" id="ll16" /> <label for="ll16">{totalzeit}</label>
		</div>
	</div>
	<input type="hidden" name="make" value="true" />
	<input type="hidden" name="alles" id="form1_alles" value="false" />
	
	<div class="box_normal">
		<h3>{format}</h3>
		<input type="radio" checked name="data_type" value="odt" id="ll17" /> <label for="ll17">{odt}</label><br />
		<input type="radio" name="data_type" value="doc"  id="ll18"/> <label for="ll18">{doc}</label>
	</div>
	
	
	<br />
	<input style="font-size:14px;font-weight:bold;" type="submit" value="{button_oo_erstellen}" id="button_go_single" style="display:none;" />
	<input style="font-size:14px;font-weight:bold;" type="submit" value="{button_oo_erstellen_m}" onclick="$('#form1_alles').get(0).value='true';$('#form1').get(0).submit();" id="button_go_all" style="display:none;"  />
	<br />
	
	</div><br />

	

	<script type="text/javascript">
	{js_loader}
	</script>
</form>

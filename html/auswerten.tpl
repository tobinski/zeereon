<script type="text/javascript">
		$(document).ready(function () { 
				$("input#von").datepicker( { showOn: 'button', buttonImageOnly: true, buttonImage: 'images/form_datepicker.png' });
				$("input#bis").datepicker( { showOn: 'button', buttonImageOnly: true, buttonImage: 'images/form_datepicker.png' });
				lade_projekt_liste($('#typ_proj select[name=kunde]').val());
				toggle_auswertung=function(obj){
						switch (obj.value){
								case "kunde":
									//$('#').hide();
									$('#typ_user').hide();
									$('#typ_proj').hide();
									$('#typ_kunde').show();
									break;
									
								case "projekt":
									$('#typ_user').hide();
									$('#typ_proj').show();
									$('#typ_kunde').hide();
									break;
								
								
								case "user":
									$('#typ_user').show();
									$('#typ_proj').hide();
									$('#typ_kunde').hide();
									break;
						}
						$('#step2').show();
				}	
				
				auswertung_send=function(form_obj){
						$('#ausw_form').attr('target','');
						if ($('#ausw_k').attr('checked')){
							$('#typ_proj').html('');
						} else {
							$('#typ_kunde').html('');
						}
						//html in neuem fenster
						if ($('#opennewwin').attr('checked')==true){
								$('#ausw_form').attr('target','newwin');
								window.open(form_obj.href, form_obj.target, "width=900,height=900,status=yes,scrollbars=yes,resizable=yes");
								return true;
						} 
				}
				//select=new Object();
				//select.value="kunde";
				//toggle_auswertung(select);
		});

</script>


<h1>{auswerten}</h1>
<form method="POST" onsubmit="auswertung_send(this);" id="ausw_form">
<input type="radio" name="art" onclick="toggle_auswertung(this);" value="kunde" id="ausw_k" /><label for="ausw_k"> {auswertung_kunde}</label><br />
<input type="radio" name="art" onclick="toggle_auswertung(this);" value="projekt" id="ausw_p" /><label for="ausw_p"> {auswertung_projekt}</label><br />
<input type="radio" name="art" onclick="toggle_auswertung(this);" value="user" id="ausw_u" /><label for="ausw_u"> {auswertung_user}</label><br />

	<div class="box_normal nodisp" id="typ_kunde">
		<h3>1. {kunde_waehlen}</h3>
		<div class="boxbox">
			{kunde_dd_liste_1}
		</div>
	</div>
	
	<div class="box_normal nodisp" id="typ_proj">
		<h3>1. {projekt_waehlen}</h3>
		<div class="boxbox">
			<div style="width:100px;float:left">{kunde_s} : </div>{kunde_dd_liste_2}<br />
			<div style="width:100px;float:left">{projekt_s} : </div><div id="projekt_liste"></div><br />
		</div>
	</div>
	
	<div class="box_normal nodisp" id="typ_user">
		<h3>1. {user_waehlen}</h3>
		<div class="boxbox">
			{user_dd_liste}
		</div>
	</div>
	
	<div id="step2" class="nodisp">
	
	<div class="box_normal">
		<h3>2. {zeitraum_waehlen}</h3>
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
		<h3>3. {details_waehlen}</h3>
		<div class="boxbox">
			<b>{spalten}: </b>
			<input type="checkbox" checked name="s_datum" id="ll08" /> <label for="ll08">{datum}</label>
			<input type="checkbox" checked name="s_zeit" id="ll09" /> <label for="ll09">{zeit}</label>
			<input type="checkbox"  name="s_csvzeit" id="ll30" /> <label for="ll30">{zeit_csv}</label>
			<input type="checkbox" checked name="s_projekt" id="ll10" /> <label for="ll10">{projekt_s}</label>
			<input type="checkbox" checked name="s_kommentar" id="ll11" /> <label for="ll11">{kommentar}</label>
			<input type="checkbox" checked name="s_user" id="ll12" /> <label for="ll12">{benutzer}</label>
			<input type="checkbox" checked name="s_ansatz" id="ll13" /> <label for="ll13">{ansatz}</label>
			<input type="checkbox" checked name="s_kosten" id="ll14" /> <label for="ll14">{kosten}</label>
			<input type="checkbox" name="s_kunde" id="ll31" /> <label for="ll31">{kunde_s}</label>

			<br /><br />
			<b>{mwst}:</b>
			<input type="checkbox" checked name="mwst" id="ll15" /> <label for="ll15">{mwst_mitrechnen}</label>
			{mwst_satz}:<input type="text" name="mwst_satz" value="8" size="3" /> %
			<br /><br />
			<b>{sonstiges}:</b>
			<input type="checkbox" checked name="r_beleg" id="ll33" /> <label for="ll33">{belege}</label> 
			<input type="checkbox" checked name="r_totalzeit" id="ll16" /> <label for="ll16">{totalzeit}</label>
			<input type="checkbox" name="r_fix" id="ll34" /> <label for="ll34">{auswertung_fix}:</label> <input type="text" name="v_fix" size="3"> / {stunde}
		</div>
	</div>
	
	<div class="box_normal">
		<h3>4. {format_waehlen}</h3>
		<div class="boxbox">
			<input type="radio" name="format" value="html_this" id="ll39" checked /> <label for="ll39">{html_this_win}</label> <br />
			<input type="radio" name="format" value="html_new" id="opennewwin" /> <label for="opennewwin">{html_new_win}</label> <br />

			<input type="radio" name="format" value="csv" id="ll36"  /> <label for="ll36">{csv}</label>: {optimieren_fuer}
			<input type="radio" name="csv_type" value="oo" id="ll37" checked /> <label for="ll37">{openoffice}</label>
			<input type="radio" name="csv_type" value="ms" id="ll38"  /> <label for="ll38">{excel}</label><br />
						
		</div>
	</div>
	

	<br />
	<input type="submit" value="{button_auswerten}" />
	<input type="hidden" name="function" value="index" />
	<input type="hidden" name="section" value="auswerten" />
	<br />&nbsp;
	</div>
</form>

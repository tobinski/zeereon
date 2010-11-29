<h1>{config_ansatz}</h1>
{msg}
<div class="box_normal">
		<h3>{config_ansatz_neu}</h3>
		<div class="boxbox">
			<form method="POST" action="">
			<table>
				<tr>
					<td width="100px">{name}:</td>
					<td><input type="text" name="ansatz_name" /></td>
				</tr>
				<tr>
					<td>{betrag}: </td>
					<td><input type="text" size="4" name="ansatz_wert" /> / {stunde}</td>
				</tr>
			</table>
			<br />
			<input type="submit" value="{button_erstellen}" />
			<input type="hidden" name="modul" value="ansatz">
			</form>
		</div>
</div>

<br /><br />

{ansatz_liste}

<form method="POST" action="">
		<input type="hidden" name="modul" value="ansatz">
		<input type="hidden" name="delete_item" value="true">
		<input type="hidden" name="id" value="" id="delform_id">
</form>

<div id="ans_box" class="nodisp" title="{config_ansatz_edit}"></div>

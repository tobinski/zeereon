<h1>{einstellungen}</h1>
{msg}

<div class="box_normal">
	<h3>{sprache}</h3>
	<div class="boxbox">
		<form method="POST">
			{dd_lang}  
			<input type="submit" value="{button_speichern}" />
		</form>
	</div>
</div>

<div class="box_normal">
	<h3>{config_user_pw_change}</h3>
	<div class="boxbox">
		<form method="POST">
			<table>
				<tr>
					<td>{altes_passwort}: </td>
					<td><input type="password" name="oldpw" value="" /></td>
				</tr>
				<tr>
					<td>{neues_passwort}: </td>
					<td><input type="password" name="newpw" value="" /></td>
				</tr>
				<tr>
					<td>{nocheinmal}: </td>
					<td><input type="password" name="newpw2" value="" /></td>
				</tr>
			</table>
			<br />
			<input type="submit" value="{button_speichern}" />
		</form>
	</div>
</div>

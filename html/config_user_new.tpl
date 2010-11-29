<h1>{new_user_h1}</h1>
{fail}
<div class="box_normal">
	<h3>{daten}</h3>
	<div class="boxbox">
		<form method="POST" action ="">
		<table>
			<tr>
				<td>{login}</td>
				<td><input type="text" name="login" /></td>
			</tr>
			<tr>
				<td>{passwort}</td>
				<td><input type="password" name="pw1" /></td>
			</tr>
			<tr>
				<td>{nocheinmal}</td>
				<td><input type="password" name="pw2"></td>
			</tr>
			<tr>
				<td>{voller_name}:</td>
				<td><input type="text" name="name"></td>
			</tr>
		</table>
	</div>
</div>
<br />
<input type="hidden" name="modul" value="user_new" />
<input type="hidden" name="step" value="2" />
<input type="submit" value="{button_erstellen}" />
</form>	

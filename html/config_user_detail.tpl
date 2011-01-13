<h1>{details} {benutzer} {db_user_login}</h1>
{msg}
<div class="box_normal">
	<form method="POST" action="">
	<h3>{config_user_pw_change}</h3>
	<div class="boxbox">
		<table>
			<tr>
				<td>{passwort}: </td>
				<td><input type="password" name="pw1" /></td>
			</tr>
			<tr>
				<td>{nocheinmal}: </td>
				<td><input type="password" name="pw2"></td>
			</tr>
		</table>
		<br />
		<input type="submit" value="{button_edit}" />
		<input type="hidden" name="modul" value="user_detail" />
		</form>
	</div>
</div>

<div class="box_normal">
	<form method="POST" action="">
	<h3>{config_user_name_change}</h3>
	<div class="boxbox">
		<table>
			<tr>
				<td>{login}: </td>
				<td><input type="text" name="userlogin" value="{db_user_login}" /></td>
			</tr>
			<tr>
				<td>{voller_name}: </td>
				<td><input type="text" name="username" value="{db_user_fullname}" /></td>
			</tr>
		</table>
		<br />
		<input type="submit" value="{button_edit}" />
		<input type="hidden" name="modul" value="user_detail" />
		</form>
	</div>
</div>

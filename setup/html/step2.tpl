<table class="top"><tr><td align="center"><div class="overall">

<h2>{etempus_setup} - {schritt} 2/3</h2>
<br />{msg}
<form method="post">

<hr/>
<h3>{admin_login}</h3>
{admin_txt}
<br /><br />

	<table>
		<tr>
			<td style="padding-left:15px;">{login}:</td>
			<td><input type="text" name="admin_login" value="admin" /> {help_admin}</td>
		</tr>
		<tr>
			<td style="padding-left:15px;">{voller_name}:</td>
			<td><input type="text" name="fullname" /></td>
		</tr>
				
		<tr>
			<td style="padding-left:15px;">{passwort}:</td>
			<td><input type="password" autocomplete="off" name="pass"  /></td>
		</tr>
		<tr>
			<td style="padding-left:15px;">{nocheinmal}:</td>
			<td><input type="password" name="pass2" autocomplete="off" /></td>
		</tr>
	</table>	

<hr />
<h3>{einstellungen}</h3>
<input type ="text" value="*" size="3" name="max_user"/> {max_user} <br />
<input type="checkbox" checked name="remote" /> {remonte_acc}
<hr />
<br /><br /><br />

<input type="submit" value="{button_weiter}" /> {help_weiter2}
<input type="hidden" name="step" value="3" />

</form>

</div></td></tr></table>

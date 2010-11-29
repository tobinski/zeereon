<table class="top"><tr><td align="center"><div class="overall">

<h2>{etempus_setup} - {schritt} 1/3</h2>
{s_einfuehrung}
<br /><br />{msg}
<form method="post">
<hr/>
<h3>{db_typ}</h3>
{db_warn}<br /><br />
<input type="radio" {chk_sqlite} name="dbtype" value="sqlite" onclick="$('#mysql_acc').css('display','none');">{sqlite}<br />
<input type="radio" {chk_mysql} name="dbtype" value="mysql" onclick="$('#mysql_acc').css('display','block');">{mysql}:
<div id="mysql_acc" style="display:none;background-color:#E3E3E3;">
	<table>
		<tr>
			<td style="padding-left:15px;">{server}:</td>
			<td><input type="text" name="mysql_host" value="localhost" /> {help_localhost}</td>
		</tr>
		<tr>
			<td style="padding-left:15px;">{user}:</td>
			<td><input type="text" name="mysql_login" value="{mysql_login}" /></td>
		</tr>
				
		<tr>
			<td style="padding-left:15px;">{pass}:</td>
			<td><input type="password" name="mysql_pass" autocomplete="off" /></td>
		</tr>
		<tr>
			<td style="padding-left:15px;">{db_name}:</td>
			<td><input type="text" name="mysql_db" value="{mysql_db}" /></td>
		</tr>
	</table>	
</div>

<hr/>

<h3>{dateirechte}</h3>
{rights_w}
<br /><br />
<input type="radio" name="right" {chk_manual} value="manual" onclick="$('#ftp_acc').css('display','none');">{manual_r}<br />
<input type="radio" name="right" {chk_ftp} value="ftp" onclick="$('#ftp_acc').css('display','block');">{ftp_r} :
<div id="ftp_acc" style="display:none;background-color:#E3E3E3;">
	<table>
		<tr>
			<td style="padding-left:15px;">{l_ftp_host}:</td>
			<td><input type="text" name="ftp_host" value="localhost"/> {help_localhost}</td>
		</tr>
		<tr>
			<td style="padding-left:15px;">{l_ftp_login}:</td>
			<td><input type="text" name="ftp_login" value="{ftp_login}" /></td>
		</tr>
				
		<tr>
			<td style="padding-left:15px;">{l_ftp_pass}:</td>
			<td><input type="password" name="ftp_pass" autocomplete="off" /></td>
		</tr>
		<tr>
			<td style="padding-left:15px;">{l_ftp_path}:</td>
			<td><input type="text" name="ftp_path" value="/httpdocs" /></td>
		</tr>
	</table>	
</div>

<hr />
<br /><br /><br />
<input type="submit" value="{button_weiter}" />
<input type="hidden" name="step" value="2" />

</form>

{script}



</div></td></tr></table>

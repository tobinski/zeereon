<h1>{oo_export_conf}</h1>
{msg}
<form enctype="multipart/form-data" method="post">
		<div class="box_normal">
			<h3>{config_misc}</h3>
			<div class="boxbox">
				<div style="width:130px;float:left">{oo_export_titel}:</div><input type="text" name="top_titel" value="{oo_export_db_titel}" size="30" /><br />
				<div style="width:130px;float:left">{oo_export_text} :</div><input type="text" name="top_text" value="{oo_export_db_text}" size="70" /><br />
				<div style="width:130px;float:left">{oo_export_foot} :</div> <input type="text" name="footer" value="{oo_export_db_foot}" size="70" />
			</div>
		</div>

		<div class="box_normal">
			<h3>{oo_export_logo}</h3>
			<div class="boxbox">
				<table>
					<tr>
						<td>{oo_export_logo_f}: </td>
						<td><input type="file" name="logo_file"  /> (JEPG, 150x50px)</td>
					</tr>
					<tr>
						<td>{oo_export_logo_pos} {oben}: </td>
						<td><input type="text" name="logo_top" size="4" value="{oo_export_db_logo_top}" /></td>
					</tr>
					<tr>
						<td>{oo_export_logo_pos} {links}:</td>
						<td><input type="text" name="logo_left" size="4" value={oo_export_db_logo_left} /></td>
					</tr>
					<tr>
						<td>{oo_export_logo_prev}: </td>
						<td><img src="{plugdir}img/logo.jpg?{unix_time}" alt="logo" style="border:1px solid black;"/></td>
					</tr>
				</table>
			</div>
		</div>

		<div class="box_normal">
			<h3>{oo_export_pos_addr}</h3>
			<div class="boxbox">
				<table>	
					<tr>
						<td>{oben}:</td>
						<td><input type="text" name="addr_top" size="4" value={oo_export_db_addr_top} /></td>
					</tr>
					<tr>
						<td>{links}: </td>
						<td><input type="text" name="addr_left" size="4" value={oo_export_db_addr_left} /></td>
					</tr>
				</table>
			</div>
		</div>

		<input type="submit" value="{button_speichern}" />
		<input type="hidden" name="save" value="true" /> 
</form>

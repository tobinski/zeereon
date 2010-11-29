
			<form method="POST" action="">
			<table>
				<tr>
					<td width="100px">{name}:</td>
					<td><input type="text" name="edit_name" value="{db_name}" /></td>
				</tr>
				<tr>
					<td>{betrag}: </td>
					<td><input type="text" size="4" name="edit_wert" value="{db_wert}"/> / {stunde}</td>
				</tr>
			</table>
			<br />
			<input type="submit" value="{button_edit}" />
			<input type="hidden" name="modul" value="ansatz" />
			<input type="hidden" name="id" value="{id}" />
			<input type="hidden" name="edit" value="true" />
			<input type="hidden" name="section" value="einstellungen" />
			<input type="hidden" name="function" value="build" />
			</form>


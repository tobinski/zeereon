
<h2>{name_db}</h2>


{msg}

<div class="box_normal">
<h3>{titel}</h3>
<div class="boxbox">
<form id="kunde_edit">
<p><span style="color:red">{fail}</span></p>
		<table>
			<tbody>
				<tr>
					<td>{name0}:</td>
					<td><input type="text" name="name" value="{name_db}" /></td>
				</tr>
				<tr>
					<td>{adresse0}:</td>
					<td><input type="text" name="adresse" value="{adresse}" /></td>
				</tr>
				<tr>
					<td>{plz0}:</td>
					<td><input type="text" name="plz" value="{plz}" /></td>
				</tr>
				<tr>
					<td>{ort0}:</td>
					<td><input type="text" name="ort" value="{ort}" /></td>
				</tr>
				<tr>
					<td>{ansprech}:</td>
					<td><input type="text" name="ansprech" value="{ansprechpartner}" /></td>
				</tr>
				<tr>
					<td>{tel}:</td>
					<td><input type="text" name="tel" value="{telefon}" /></td>
				</tr>
				<tr>
					<td>{mail}:</td>
					<td><input type="text" name="mail" value="{email}" /></td>
				</tr>
			</tbody>
		</table>
		
		<input type="button" value="{edit}" onclick="etempus.kunde_edit.speichern();" />
		<input type="hidden" name="function" value="kunde_edit" />
		<input type="hidden" name="section" value="conf" />
		<input type="hidden" name="id" value="{id}" />
		<input type="hidden" name="step" value="2" />
</form></div></div>


<div class="box_normal">
	<h3>{delete_kunde}</h3>
	<div class="boxbox">
		<input type="button" value="{button_del}" onclick="etempus.delete_kunde({id});" />
	</div>
</div>

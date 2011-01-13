<?php

$tpl['ansatz_liste'] = '
<tr class="{trc}">
	<td><img src="images/icon/16/ansatz.png" /> <b>{db_name}</td>
	<td>{db_wert}.- / {stunde}</td>
	<td><a href="javascript:void(0);" onclick="edit_ansatz({id});">{button_edit}</a></td>
	<td>{db_aktiv}</td>
</tr>';


$tpl['ansatz_liste_table'] = '
	
	<table class="struct">
		<tr>
			<th>{ansatz}</th>
			<th>{wert}</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
		{items}
	</table>';

?>

<?php

$tpl['user_liste'] = '

<tr class="{trc}">
	<td><img src="images/icon/16/kunde.png" /> <b>{login}</b></td>
	<td>{name}</td>
	<td><a href="?section=einstellungen&function=build&modul=user_detail&id={id}">{detail}</a></td>
	<td>{del_button}</td>
</tr>';

$tpl['user_del_button']='<a href="?section=einstellungen&function=build&modul=user_del&id={id}">{button_del}</a>';

$tpl['user_liste_table'] = '
	<table class="struct">
		<tr>
			<th>{login}</th>
			<th>{name}</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
		{items}
	</table>';

?>

<?php

$tpl['liste_kunde']=<<<EOF
	
	<tr class="{trc}" onclick="toggle_elm(this);" id="e_{db_val}">
		<td><input onclick="toggle_cb(this)"
		 type="checkbox" name="cb_go[]" value="{db_val}" id="cb_{db_val}"></td>
		<td>{db_nam}</td>
	</tr>
	
EOF;
$tpl['liste_kunde_h']=<<<EOF
	<tr>
		<th width="20px;">&nbsp;</th>
		<th>{kunde_s}</th>
	</tr>
EOF;


$tpl['liste_projekt']=<<<EOF
	
	<tr class="{trc}" onclick="toggle_elm(this);" id="e_{db_val}">
		<td><input onclick="toggle_cb(this)"
		 type="checkbox" name="cb_go[]" value="{db_val}" id="cb_{db_val}"></td>
		<td>{db_knam}</td>
		<td>{db_pnam}</td>
	</tr>

EOF;


$tpl['liste_projekt_h']=<<<EOF
	<tr>
		<th width="20px;">&nbsp;</th>
		<th>{kunde_s}</th>
		<th>{projekt_s}</th>
	</tr>
EOF;

?>

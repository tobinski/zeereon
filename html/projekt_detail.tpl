<h2>{proj_name}</h2>

{msg}

{kunde_s}: <b>{kname}</b>

<div class="box_normal">
<div class="boxbox">
<form id="proj_edit" method="POST" name="new_proj">
<div class="box_normal">
	<h3>{name}</h3>
	<div class="boxbox">
		<input type="text" name="name"  value="{proj_name}" />
	</div>
</div>

<div class="box_normal">
	<h3>{ansatz}</h3>
	<div class="boxbox">
		<input name="ansatz" value="all"{ansatz_all} type="radio" id="chk_aa" onclick="$('#pn_ans_l').hide('fast');" />
		<label for="chk_aa">{ansatz_alle}</label>
		<br />
		<input name="ansatz" value="spec"{ansatz_single} type="radio" id="chk_as" onclick="$('#pn_ans_l').show('fast');" /> 
		<label for="chk_as">{ansatz_limit}:
		<label for="ansatz_liste[]" class="error" style="display:none;">{ans_at_least_one}</label></label>
		
		
		<br />
		<div id="pn_ans_l" style="{disp_box}">{ansatz_liste}</div>
	</div>
</div>

<div class="box_normal">
	<h3>{kostendach}</h3>
	<div class="boxbox">
		<input type="text" name="kostendach"  value="{db_kostendach}" />
	</div>
</div>


<input type="button" id="subm" value="{button_speichern}" />
<input type="hidden" name="section" value="conf" />
<input type="hidden" name="id" value="{id}" />
<input type="hidden" name="step" value="2" />
<input type="hidden" name="function" value="projekt_edit" />
</form>
</div>
</div>

<div class="box_normal">
	<h3>{delete_project}</h3>
	<div class="boxbox">
		<input type="button" value="{button_del}" onclick="etempus.delete_proj({id});" />
	</div>
</div>





<h2>{list_newitem}</h2>


<form id="new_k_proj" method="POST" name="new_k_proj">

<div class="box_normal">
	<h3>{kundenname}</h3>
	<div class="boxbox">
		<input type="text" name="kname"  value="" />
		<br /><br />
	</div>
</div>


<div class="box_normal">
	<h3>{projektname}</h3>
	<div class="boxbox">
		<input type="text" name="name"  value="" />
		<br /><br />
	</div>
</div>

<div class="box_normal">
	<h3>{ansatz}</h3>
	<div class="boxbox">
		<input name="ansatz" checked value="all" type="radio" id="chk_aa" onclick="$('#pn_ans_l').hide('fast');" />
		<label for="chk_aa">{ansatz_alle}</label>
		<br />
		<input name="ansatz" value="spec" type="radio" id="chk_as" onclick="$('#pn_ans_l').show('fast');" /> 
		<label for="chk_as">{ansatz_limit}:
		<label for="ansatz_liste[]" class="error" style="display:none;">{ans_at_least_one}</label></label>
		
		
		<br />
		<div id="pn_ans_l" style="display:none;">{ansatz_liste}</div>
	</div>
</div>

<div class="box_normal">
	<h3>{kostendach}</h3>
	<div class="boxbox">
		<input type="text" name="kostendach"  value="0" />
		<br /><br />
	</div>
</div>



	<input type="button" id="subm" value="{button_erstellen}" />
	<input type="hidden" name="section" value="conf" />
	<input type="hidden" name="function" value="kunde_projekt_neu_create" />
</form>




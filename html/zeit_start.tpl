<script type="text/javascript">
		$(document).ready(function () { 
				etempus.init_zeit();
		});
</script>

	<div style="display:none;">	
		<input type="radio" name="zb" id="zb_z" /> time
		<input type="radio" name="zb" id="zb_b" /> check
	</div>
		
			
			
      <!-- begin: #col1 - first float column -->
      <div id="col1" role="complementary">
        <div id="col1_content" class="clearfix"> 
		  <div class="subcl">
		      <img src="images/li-1.png" class="li"> <span> {choose_proj}:</span>
		  </div>
		<div id="tree">
			<h3><span id="pm_last" onclick="$.plusminus(this,'#last_proj_list')">-</span>{lastused}</h3>
			<div id="last_proj_list"></div>
			<h3><span id="pm_most" onclick="$.plusminus(this,'#most_proj_list')">-</span>{mostused}</h3>
			<div id="most_proj_list">
			</div>
			<br />
			
			<div id="treeNewProject">
			<a href="javascript:void(0);" onclick="etempus.new_proj_kunde();"><img src="images/tree_new_project.png" alt="Neues Projekt anlegen"><span>{list_newitem}</span></a>
			</div>
			<div id="alphabet_cont">
			</div>
			<div id="k_list"></div>
		 </div>     
		      
		      
        </div>
      </div>
      <!-- end: #col1 -->


	<div id="right">
		<div id="cont_time" class='nodisp'>
		</div>
		<div id="cont_pref" class='nodisp'>
		</div>
		<noscript>
			{activate_js}
		</noscript>
	</div>


<br /><div style="clear:both;"></div>


<!-- tabelle letzte einträge !-->
<div id="last_ones">
	<!-- letzte zeiteinträge !-->
	<div id="table_place_zeit">
		<p>{zeige} <input type="text" size="3" id="show_last_num" value="3" onchange="etempus.list.zeit_liste()" /> {tage}</p>
		<form onsubmit="return false;">
			<table id="table_zeit">
				<tr>
					<th width="5%"></th>
					<th>{datum}</th>
					<th>{projekt_s}</th>
					<th>{ansatz}</th>
					<th>{stunden}</th>
					<th>{kommentar}</th>
				</tr>
			</table>
			<img src="images/arrow_ltr.png" /> {markierte}:
			<a href="javascript:etempus.list.zeit_del_selected();"><i>{button_del}</i></a> - 
			<a href="javascript:void(0)" onclick="$('#table_zeit input[type=checkbox]').attr('checked',false);$('#table_zeit tr.light, #table_zeit tr.dark').removeClass('selected');"><i>{uncheck_all}</i></a>
			<input type="hidden" name="section" value="zeit_ajax" />
			<input type="hidden" name="function" value="table_time_edit_save" />
		</form>
	</div>
	<!-- letzte belege !-->
	<div id="table_place_beleg" class="nodisp">	
		<p>{zeige} <input type="text" size="3" id="show_last_num_beleg" onchange="etempus.list.beleg_liste()" value="15" /> {eintraege}</p>
		<form onsubmit="return false;">
			<table id="table_beleg">
				<tr>
					<th width="5%"></th>
					<th>{datum}</th>
					<th>{kunde_s}</th>
					<th>{projekt_s}</th>
					<th>{betrag}</th>
					<th>{kommentar}</th>
				</tr>
			</table>
			<img src="images/arrow_ltr.png" /> {markierte}:
			<a href="javascript:etempus.list.beleg_del_selected();"><i>{button_del}</i></a> - 
			<a href="javascript:void(0)" onclick="$('#table_beleg input[type=checkbox]').attr('checked',false);$('#table_beleg tr.light, #table_beleg tr.dark').removeClass('selected');"><i>{uncheck_all}</i></a>
			<input type="hidden" name="section" value="zeit_ajax" />
			<input type="hidden" name="function" value="table_beleg_edit_save" />
		</form>
	</div>
	<!-- box für frage unten an tabelle !-->
	<div id="question"></div>
</div>


<div style="clear:both;"></div>

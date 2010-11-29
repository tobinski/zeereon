/***********************************
 *
 * 
 * 
 * 
 * 
 **********************************/
function uid() {
		var result='';
		for(var i=0; i<32; i++)
		result += Math.floor(Math.random()*16).toString(16).toUpperCase
		();
		return result
}

etempus.list = new Object();

/***********************************
 * kundnenliste erstellen
 **********************************/

etempus.list.lade_kunden = function (){
	$.ajax({
	  type: "POST",
	  url: "index.php",
	  data: {'section':'zeit_ajax','function':'klist'},
	  success: function(xml) {
			var out="";
			var alphabet="";
			var last_z="";
			$(xml).find('kunde').each( function() {
					
					var kid=$(this).find('id').text();
					var name=$(this).find('name').text();
					
					// alphabet
					var z = name.substring(0,1).toUpperCase();
					if (z!=last_z){
						 alphabet=alphabet+"<span><a href='javascript:void(0);' onclick='$(\"#tree\").scrollTo(\"#scroll_to_"+z+"\",300);'>"+z+"</span> ";
						 last_z=z;
						 out=out+"<div id='scroll_to_"+z+"' class='totop'>"+z+"<img class='link' title='"+etempus.lang.nachoben+
						 		 "' src='images/up16.png' onclick='$(\"#tree\").scrollTo(\"#alphabet_cont\",300);' /></div>";
					}
					
					out=out+"<div class='kunde' id='treeK_"+kid+"'><img src='images/icon_mini_edit.png' class='link' onclick='etempus.kunde_edit("+kid+");' title='"+etempus.lang.edit+"' />"+
							"<img src='images/icon_mini_new.png' class='link' onclick='etempus.projekt_neu("+kid+");' title='"+etempus.lang.projekt_neu+"' /><span class='name'>"+
							name+"</span></div>";
					
					$($(this).find('projekte')).find('projekt').each( function() {
						var pname=$(this).find('proj_name').text();
						var pid=$(this).find('proj_id').text();
						out=out+"<div class='projekt' id='treeP_"+pid+"'>"+
								"<img src='images/icon_mini_edit.png' class='link' onclick='etempus.projekt_detail("+pid+");' title='"+etempus.lang.edit+"' /> "+
								"<a href='javascript:etempus.erfassen("+pid+")'>"+pname+'</a></div>';
					});
					
					out=out+'<div class="klist_spacer"></div>';
					
			});	
			
			$('#k_list').get(0).innerHTML=out;
			$('#alphabet_cont').get(0).innerHTML=alphabet;
			
	  }
  	});
}

/***********************************
 * meist/zuletzt verwendete
 **********************************/
etempus.list.last_most = function(){
		$.ajax({
		  type: "POST",
		  url: "index.php",
		  data: {'section':'zeit_ajax','function':'lastmost'},
		  success: function(xml) {
					  	
						//zuletzt verwendete
						var last_out="";
						$(xml).find('last_proj').each( function() {
								var pid=$(this).find('id').text();
								var kname=$(this).find('kunde').text();
								var name=$(this).find('name').text();
								last_out=last_out+'<div onclick="etempus.erfassen('+ pid +')" class="most_last_a" id="treeP0_'+pid+'"><nobr>'+
												  '<div class="most_last">'+name+'</div><div class="most_last_k">('+kname+')</div></nobr></div>';
						});
						$('#last_proj_list').html(last_out);
						
						//meist verwendete
						var most_out="";
						$(xml).find('most_proj').each( function() {
								var pid=$(this).find('id').text();
								var kname=$(this).find('kunde').text();
								var name=$(this).find('name').text();
								most_out=most_out+'<div onclick="etempus.erfassen('+ pid +')" class="most_last_a" id="treeP1_'+pid+'"><nobr>'+
												  '<div class="most_last">'+name+'</div><div class="most_last_k">('+kname+')</div></nobr></div>';
						});
						$('#most_proj_list').html(most_out);
				  }
		});
}


/***********************************
 * liste zeiteinträge
 **********************************/

etempus.list.zeit_liste = function() {
		if (etempus.list.zeit_liste.locked) { return; }
		var days=$('#show_last_num').val();
		var $table=$('#table_zeit');
		//fix für seitenhöhe
		$('#table_place_zeit').height(etempus.bodyheight);
		
		$.ajax({
				type: "POST",
				url: "index.php",
				data: {'section':'zeit_ajax','function':'zeit_last' , 'days':days },
				success: function(xml) {
						//tabelle leeren
						$('#table_zeit tr.light, #table_zeit tr.dark, #table_zeit tr.total').remove();
						var tr_class="dark";
						
						// walk über xml <day>	
						$(xml).find('day').each( function() {
								var total=$(this).find('total').text();
								var dayout="";
								
								//walk über xml <item>
								$(this).find('item').each( function() {
										var zeit=$(this).find('zeit').text();
										var id_data="zitem_"+$(this).find('id').text() + "_" +
													$(this).find('projekt_id').text() + "_" + $(this).find('ansatz_id').text()+
													"_"+ $(this).find('kunden_id').text();
										dayout=dayout+"<tr class="+etempus.list.tr_class()+" id="+id_data+
													  "><td><input type='checkbox' name='item[]' value='"+$(this).find('id').text()+"' /></td>"+
													  "<td>"+$(this).find('datum').text()+"</td>"+
													  "<td>"+$(this).find('projekt').text()+"</td>"+
													  "<td>"+$(this).find('ansatz').text()+"</td>"+
													  "<td>"+$(this).find('zeit').text()+"</td>";
										// Edit tobinski
										// zu lange Kommentare abschneiden
										if($(this).find('kommentar').text().length > 60)
										{
											var kommentar = $(this).find('kommentar').text().substring(0,60)+'...';
										}
										else
										{
											var kommentar = $(this).find('kommentar').text();
										}
										dayout = dayout + "<td>"+kommentar+"<span class='hidden'>"+$(this).find('kommentar').text()+"</td></tr>";
								});
								
								dayout=dayout+"<tr class='total'><td colspan='4'>"+etempus.lang.tagestotal+"</td>"+
											  "<td>"+total+"</td><td></td></tr>";
								
								$table.append(dayout);
						});
				  		//höhe-fix
						etempus.bodyheight=$table.height()+60;
						$('#table_place_zeit').height(etempus.bodyheight);
						
						// tabelle ist hier fertig aufgebaut, handler setzen
				  		//dopppelcklick für edit
						$('#table_zeit tr.light, #table_zeit tr.dark').dblclick(function () { 
								etempus.list.zeit_liste.edit(this);
						});
						
						//klick für aktivieren auf row
						$('#table_zeit tr.light, #table_zeit tr.dark').click(function () { 
								if (etempus.list.zeit_liste.locked) { return; }
								//deaktivieren
								if ($('input[type=checkbox]',this).get(0).checked){
									$(this).removeClass('selected');
									$('input[type=checkbox]',this).get(0).checked=false;
								} 
								//aktivieren
								else {
									$(this).addClass('selected');
									$('input[type=checkbox]',this).get(0).checked=true;
								}
						
						});
						//klick für aktivieren auf checkbox
						$('#table_zeit input[type=checkbox]').click( function () {
								if (etempus.list.zeit_liste.locked) { return; }
								if (this.checked){
									$(this).parent().parent().removeClass('selected').show("slow");
									this.checked=false;
								} else {
									this.checked=true;
									$(this).parent().parent().addClass('selected');
								}
						});
						
						
						
				}		  
		});
}


/***********************************
 * zeiteintrag bearbeiten (table inline-edit)
 ***********************************/
etempus.list.zeit_liste.locked=false;
etempus.list.zeit_liste.edit=function(real_obj){
		
		if (etempus.list.zeit_liste.locked) { return; }
		etempus.list.zeit_liste.locked=true;
		var obj=$('*',real_obj);
		
		//alle markierungen entfernen
		$('#table_zeit tr.light input[type=checkbox], #table_zeit tr.dark input[type=checkbox]').attr('checked',false);
		$('#table_zeit tr.light, #table_zeit tr.dark').removeClass('selected');
		
		
		//id-array: 0 nix,1 eintrag_id,2 proj_id, 3 ansatz_id,4 kunden_id
		id_arr=real_obj.id.split("_");
		real_obj.id="tr_saving";
		
		// einzelne elemente neu schreiben 
		var i=1;
		obj.each(function(){
				switch (i){
						//checkbox weg
						case 1:
							$(this).html('');
							break;
							
						// 2 ist leer (tr_object nach checkbox)
						case 2: break;
						
						//datum 
						case 3:
							$(this).html("<input type='text' name='datum' id='table_edit_date' value='"+this.innerHTML+"' />");
							$('#table_edit_date').datepicker({ altFormat: 'dd.mm.yy' });
							break;
						
						//projekt 
						case 4:
							$(this).html("<div id='table_proj_area'></div>");
							lade_projekt_liste(id_arr[4], $('#table_proj_area'), id_arr[2], "", 'lade_ansatz_liste(this.value);');
							break;
							
						//ansatz 
						case 5:
							$(this).html("<div id='ansatz_liste'></div>");
							lade_ansatz_liste(id_arr[2], $('#ansatz_liste'), id_arr[3]);
							break;	
							
						//zeit
						case 6:
							$(this).html("<input type='text' id='table_edit_zeit' name='zeit' value='"+this.innerHTML+"' size='5' />");
							break;
						
						//kommentar
						case 7:
							$(this).html("<input type='text' id='table_edit_comment' name='kommentar' value='"+this.find('.hidden').text()+"' />");
							break;	
							
				}
				i++;				
		});
		

		
		//speichern handler
		$("body").click(function (e) { 
				//nur wenn element nicht aktive reihe ist weiter			
				if (!$(e.target).closest("tr#tr_saving, #ui-datepicker-div, div.ui-datepicker-header").length) { 
						
						//wenn kommentar obligatorisch
						if (etempus.comment_required===true){
								var comment=$('#table_edit_comment').val();
								if (comment==''){ 
									$('#table_edit_comment').addClass('error'); 
									$('#table_edit_comment').keyup(function () {
										if (this.value.length>0){ $('#table_edit_comment').removeClass('error'); }
									});
									return; 
								}
						}
						
						//id für php
						$("#table_place_zeit #tbl_edit_iddata").remove();
						$("#table_place_zeit > form").append("<input type='hidden' id='tbl_edit_iddata' name='id_data' value='"+id_arr.toString()+"' />");
						//ajax request
						$.ajax({
							type: "POST",
							url: "index.php",
							data: $("#table_place_zeit > form").serialize() + { 'id_data':id_arr[0] } ,
							success: function(data) {
								
								//ubnind &% liste neu aufbauen
								$("body").unbind('click');
								etempus.list.zeit_liste.locked=false;
								
								//dirrty fix für sqlite-support 5ms reihen aus
								setTimeout('etempus.list.zeit_liste()',50);
								
							}
						});
				}
								
		});
				
}

/***************************************
 * ausgewählte einträge entfernen (zeit)
 **************************************/
etempus.list.zeit_del_selected=function () {
		if (etempus.list.zeit_liste.locked) { return; }
		//checken ob was ausgewählt
		var chk=false;
		$('#table_place_zeit input[type=checkbox]').each( function() { if (this.checked){ chk=true; } } );
		if (!chk) { return; };
		$("#table_place_zeit input[name=function]").val('zeit_eintrag_del');
		$.ajax({
				type: "POST",
				url: "index.php",
				data: $("#table_place_zeit > form").serialize(),
				success: function(data) {
						$('#question').html(data).show();
						$.scrollTo('+=100px','200');
						//handler  abbrechen
						$('#frage_n').click(function() {
								$('#frage_n').unbind('click');
								$.scrollTo('-=100px','300');
								$('#question').hide().html('');
						});
						//handler löschen
						$('#frage_y').click(function() {
								$('#frage_y').unbind('click');
										$.ajax({
												type: "POST",
												url: "index.php",
												data: {'section':'zeit_ajax','function':'zeit_eintrag_del','step':'2' },
												success: function(data) {
														//alert(data);
														$.scrollTo('-=100px','300');
														$('#question').hide().html('');
														etempus.list.zeit_liste();
												}
										});
						}); 
				}
		});
	
}







/***********************************
 * liste belegeinträge
 **********************************/

etempus.list.beleg_liste = function() {
		
		if (etempus.list.beleg_liste.locked) { return; }
		var num_item=$('#show_last_num_beleg').val();
		var $table=$('#table_beleg');
		//fix für seitenhöhe
		$('#table_place_beleg').height(etempus.bodyheight);
		
		$.ajax({
				type: "POST",
				url: "index.php",
				data: {'section':'zeit_ajax','function':'beleg_last' , 'num':num_item },
				success: function(xml) {
						//tabelle leeren
						$('#table_beleg tr.light, #table_beleg tr.dark').remove();
						var tr_class="dark";
						var out="";
						// walk über xml <item>	
						$(xml).find('item').each( function() {
								var total=$(this).find('total').text();
								var dayout="";
								var id_data="bitem_"+$(this).find('id').text() + "_" +
													$(this).find('projekt_id').text() + "_" + 
													$(this).find('kunden_id').text();
								
								out=out+"<tr class="+etempus.list.tr_class()+" id="+id_data+">"+
										"<td><input type='checkbox' name='item[]' value='"+$(this).find('id').text()+"' /></td>"+
										"<td>"+$(this).find('datum').text()+"</td>"+
										"<td>"+$(this).find('kunde').text()+"</td>"+
										"<td>"+$(this).find('projekt').text()+"</td>"+
										"<td>"+$(this).find('betrag').text()+"</td>"+
										"<td>"+$(this).find('kommentar').text()+"</td></tr>";
						});
						$table.append(out);
						

						//klick für aktivieren auf row
						$('#table_beleg tr.light, #table_beleg tr.dark').click(function () { 
								if (etempus.list.zeit_liste.locked) { return; }
								//deaktivieren
								if ($('input[type=checkbox]',this).get(0).checked){
									$(this).removeClass('selected');
									$('input[type=checkbox]',this).get(0).checked=false;
								} 
								//aktivieren
								else {
									$(this).addClass('selected');
									$('input[type=checkbox]',this).get(0).checked=true;
								}
						
						});
						//klick für aktivieren auf checkbox
						$('#table_beleg input[type=checkbox]').click( function () {
								if (etempus.list.zeit_liste.locked) { return; }
								if (this.checked){
									$(this).parent().parent().removeClass('selected').show("slow");
									this.checked=false;
								} else {
									this.checked=true;
									$(this).parent().parent().addClass('selected');
								}
						});
						
				  		//dopppelcklick für edit
						$('#table_beleg tr.light, #table_beleg tr.dark').dblclick(function () { 
								etempus.list.beleg_liste.edit(this);
						});
				}
		});
		
}

/***********************************
 * belegeintrag bearbeiten (table inline-edit)
 ***********************************/
etempus.list.beleg_liste.locked=false;
etempus.list.beleg_liste.edit=function(real_obj){
		
		
		if (etempus.list.beleg_liste.locked) { return; }
		etempus.list.beleg_liste.locked=true;
		var obj=$('*',real_obj);
		
		//alle markierungen entfernen
		$('#table_beleg tr.light input[type=checkbox], #table_beleg tr.dark input[type=checkbox]').attr('checked',false);
		$('#table_beleg tr.light, #table_beleg tr.dark').removeClass('selected');
		
		
		//id-array: 0 nix,1 eintrag_id,2 proj_id, 3 kunden_id
		id_arr=real_obj.id.split("_");
		real_obj.id="tr_saving";
		
		
		// einzelne elemente neu schreiben 
		var i=1;
		obj.each(function(){
				switch (i){
						//checkbox weg
						case 1:
							$(this).html('');
							break;
							
						// 2 ist leer (tr_object nach checkbox)
						case 2: break;
						
						//datum 
						case 3:
							$(this).html("<input type='text' name='datum' id='table_edit_date' value='"+this.innerHTML+"' />");
							$('#table_edit_date').datepicker({ altFormat: 'dd.mm.yy' });
							break;
						
						//kunde 
						case 4:
							$(this).html("<div id='table_kunde_area'></div>");
							lade_kunden_liste($('#table_kunde_area'), id_arr[3], "lade_projekt_liste(this.value);");
							break;
							
						//projekt 
						case 5:
							$(this).html("<div id='projekt_liste'></div>");
							lade_projekt_liste(id_arr[3], $('#projekt_liste'), id_arr[2]);
							break;	
							
						//betrag
						case 6:
							$(this).html("<input type='text' id='table_edit_betrag' name='betrag' value='"+this.innerHTML+"' size='4' />");
							break;
						
						//kommentar
						case 7:
							$(this).html("<input type='text' id='table_edit_comment' name='kommentar' value='"+this.innerHTML+"' />");
							break;	
							
				}
				i++;				
		});
		
		
		//speichern handler
		$("body").click(function (e) { 
				//nur wenn element nicht aktive reihe ist weiter			
				if (!$(e.target).closest("tr#tr_saving, #ui-datepicker-div, div.ui-datepicker-header").length) { 
						//wenn kommentar obligatorisch
						if (etempus.comment_required===true){
								var comment=$('#table_edit_comment').val();
								if (comment==''){ 
									$('#table_edit_comment').addClass('error'); 
									$('#table_edit_comment').keyup(function () {
										if (this.value.length>0){ $('#table_edit_comment').removeClass('error'); }
									});
									return; 
								}
						}
						//id für php
						$("#table_place_beleg #tbl_edit_iddata").remove();
						$("#table_place_beleg > form").append("<input type='hidden' id='tbl_edit_iddata' name='id_data' value='"+id_arr.toString()+"' />");
						//ajax request
						$.ajax({
							type: "POST",
							url: "index.php",
							data: $("#table_place_beleg > form").serialize() + { 'id_data':id_arr[0] } ,
							success: function(data) {
								//ubnind &% liste neu aufbauen
								$("body").unbind('click');
								etempus.list.beleg_liste.locked=false;
								etempus.list.beleg_liste();
								
							}
						});

				}
								
		});
				
}
			
/***************************************
 * ausgewählte einträge entfernen (belege)
 **************************************/
etempus.list.beleg_del_selected=function () {
		if (etempus.list.beleg_liste.locked) { return; }
		//checken ob was ausgewählt
		var chk=false;
		$('#table_place_beleg input[type=checkbox]').each( function() { if (this.checked){ chk=true; } } );
		if (!chk) { return; };
		$("#table_place_beleg input[name=function]").val('beleg_eintrag_del');
		$.ajax({
				type: "POST",
				url: "index.php",
				data: $("#table_place_beleg > form").serialize(),
				success: function(data) {
						$('#question').html(data).show();
						$.scrollTo('+=100px','200');
						//handler  abbrechen
						$('#frage_n').click(function() {
								$('#frage_n').unbind('click');
								$.scrollTo('-=100px','300');
								$('#question').hide().html('');
						});
						//handler löschen
						$('#frage_y').click(function() {
								$('#frage_y').unbind('click');
										$.ajax({
												type: "POST",
												url: "index.php",
												data: {'section':'zeit_ajax','function':'beleg_eintrag_del','step':'2' },
												success: function(data) {
														//alert(data);
														$.scrollTo('-=100px','300');
														$('#question').hide().html('');
														etempus.list.beleg_liste();
												}
										});
						}); 
				}
		});
	
}




						
						
						
						

//
// hilfsfunktion dark/light-toggler
//
etempus.list.tr_class = function () {
		if (this.dark==false){ this.dark=true; return "dark"; }
		else { this.dark=false; return "light"; }
} 

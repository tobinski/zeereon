jQuery.plusminus = function (pm_obj, toggle_id) { 
		var toggle_obj = $(toggle_id);
		if (pm_obj.innerHTML=="+"){
				pm_obj.innerHTML="-";
				toggle_obj.show('slow');
		} else {
				pm_obj.innerHTML="+";
				toggle_obj.hide('slow');
		}	
}


/*************************
* Layout
* Runde Ecken für Layout
************************/
$("#main").corner(15);
$(".hlist ul li").corner("5px top");
$(".hlist ul li a").corner("5px top");
$(".timeCounter").corner("5px top right");
$("#ttop a").corner("5px");
/*************************
* Layout
* Buttons definieren
************************/




etempus = new Object();

/*************************
 * init zeit
 * beim öffnen der zeit/belege seite
 ************************/
etempus.init_zeit = function(){
		etempus.list.lade_kunden();
		etempus.list.last_most();
		etempus.erfassen.autostart();		
		//wenn beleg aktiv
		if ($.url.param("beleg")=='true'){
				$('#submenu > li:eq(0)').attr('class', 'inactive');
				$('#submenu > li:eq(1)').attr('class', 'active');
				$('#zb_b').get(0).checked=true;
				$('#zb_z').get(0).checked=false;
				$('#table_place_beleg').show();
				$('#table_place_zeit').hide();
				etempus.list.beleg_liste();
		} 
		// sonst zeit
		else {
				$('#submenu > li:eq(1)').attr('class', 'inactive');
				$('#submenu > li:eq(0)').attr('class', 'active');
				$('#zb_b').get(0).checked=false;
				$('#zb_z').get(0).checked=true;
				etempus.list.zeit_liste();
		}
}




/****************
 * kunde editieren
 ****************/
etempus.kunde_edit = function(kid){
		this.hidecontent();
		$('#cont_pref').show('fast');
		$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'conf','function':'kunde_edit', 'id':kid },
	  		success: function(data) {
					$('#cont_pref').get(0).innerHTML=data;
			}
		});
}

/****************
 * kunde editieren speichern
 ****************/
etempus.kunde_edit.speichern = function() {
			$('#cont_pref').hide('fast');
			$.ajax({
				url: "index.php",
				data: $('#kunde_edit').serialize(),
				success: function(data) {
						$('#cont_pref').show('fast');
						$('#cont_pref').get(0).innerHTML=data;
						etempus.list.lade_kunden();
				}
			});
	
}


/****************
 * neues projekt
 ****************/

etempus.projekt_neu = function(kid){
		this.hidecontent();
		$('#cont_pref').show('fast');
		$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'conf','function':'projekt_neu', 'id':kid },
	  		success: function(data) {
					$('#cont_pref').get(0).innerHTML=data;
					$('#new_proj').validate({
						rules: {
							name: { 
								required: true,
								minlength: 3
							},
							ansatz: "required",
							"ansatz_liste[]": {
								required: "#chk_as:checked"
							}
						},
						messages: {
							name: etempus.lang.error_noname,
							ansatz: etempus.lang.required,
						}
					});
					$("#subm").click(function() {
						  if ($("#new_proj").valid()){
							  $.ajax({
									url: "index.php",
									data: $('#new_proj').serialize(),
									success: function(data) {
											etempus.list.lade_kunden();
											etempus.projekt_detail(data);
									}
							});
						  }
						  return false;
  					});
			}
		});
}

/******************************
 * projekt-details 
 ******************************/
etempus.projekt_detail = function(pid){
		this.hidecontent();
		$('#cont_pref').show('fast');
		$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'conf','function':'projekt_edit', 'id':pid },
	  		success: function(data) {
				$('#cont_pref').html(data);
					etempus.proj_validate();
					etempus.proj_subm();
			}
		});
}
/******************************
 * projekt-details form validieren
 ******************************/
etempus.proj_validate=function(){
	$('#proj_edit').validate({
						rules: {
							name: { 
								required: true,
								minlength: 3
							},
							ansatz: "required",
							"ansatz_liste[]": {
								required: "#chk_as:checked"
							}
						},
						messages: {
							name: etempus.lang.error_noname,
							ansatz: etempus.lang.required,
						}
	});
}
/******************************
 * projekt-details speichern
 ******************************/
etempus.proj_subm=function(){
		$("#subm").click(function() {
			  if ($("#proj_edit").valid()){
				  $.ajax({ url: "index.php",
						   data: $('#proj_edit').serialize(),
						   success: function(data) {
										etempus.list.lade_kunden();
										$('#cont_pref').html(data);
										etempus.proj_validate();
										etempus.proj_subm();
							}
						});
			  }
			  return;
		});
}


/*******************************
 * neuer kunde & projekt 
 *******************************/

etempus.new_proj_kunde = function() {
		$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'conf','function':'kunde_proj_new' },
	  		success: function(data) {
					etempus.hidecontent();
					$('#cont_pref').show('fast');
					$('#cont_pref').html(data);
					//form validieren
					$('#new_k_proj').validate({
							rules: {
								name: { 
									required: true,
									minlength: 2
								},
								kname: { 
									required: true,
									minlength: 2
								},
								ansatz: "required",
								"ansatz_liste[]": {
									required: "#chk_as:checked"
								}
							},
							messages: {
								name: etempus.lang.error_noname,
								kname: etempus.lang.error_noname,
								ansatz: etempus.lang.required,
							}
					});
					//speichern
					$("#subm").click(function() {
			  			if ($("#new_k_proj").valid()){
							 $.ajax({ url: "index.php",
								   data: $('#new_k_proj').serialize(),
								   success: function(data) {
										etempus.list.lade_kunden();
										etempus.kunde_edit(data);
								   }
							});
						}
					});
			}
		
		});
	
}


/*******************************
 * kunde löschen 
 *******************************/
etempus.delete_kunde=function(kid){
	$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'conf','function':'kunde_del','id':kid },
	  		success: function(data) {
					etempus.hidecontent();
					$('#cont_pref').show('fast');
					$('#cont_pref').html(data);
					$('#frage_n').click(function(){
						etempus.kunde_edit(kid);
						return;
					});
					$('#frage_y').click(function(){
						$.ajax({
							type: "POST",
							url: "index.php",
							data: {'section':'conf','function':'kunde_del','id':kid,'step':'2' },
							success: function(data) {
									etempus.hidecontent();
									$('#cont_pref').show('fast');
									$('#cont_pref').html(data);
									etempus.list.lade_kunden();
									etempus.list.last_most();
									if ($.url.param("beleg")=='true'){ etempus.list.beleg_liste(); } else { etempus.list.zeit_liste(); }
							}
						});
							
					});
			}
	});
}

/*******************************
 * projekt löschen 
 *******************************/
etempus.delete_proj=function(pid){
	$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'conf','function':'projekt_del','id':pid },
	  		success: function(data) {
					etempus.hidecontent();
					$('#cont_pref').show('fast');
					$('#cont_pref').html(data);
					$('#frage_n').click(function(){
						etempus.projekt_detail(pid);
						return;
					});
					$('#frage_y').click(function(){
						$.ajax({
							type: "POST",
							url: "index.php",
							data: {'section':'conf','function':'projekt_del','id':pid,'step':'2' },
							success: function(data) {
									etempus.hidecontent();
									$('#cont_pref').show('fast');
									$('#cont_pref').html(data);
									etempus.list.lade_kunden();
									etempus.list.last_most();
									if ($.url.param("beleg")=='true'){ etempus.list.beleg_liste(); } else { etempus.list.zeit_liste(); }
									
							}
						});
							
					});
			}
	});		
}



/******************************
 * act=zeit erfassen 
 ******************************/
etempus.zeit_erfassen = function(){
	this.zeit_checkhtml();
	$('#zb_z').get(0).checked=true;
	$('#zb_b').get(0).checked=false;
}

/******************************
 * act=beleg erstellen 
 ******************************/
etempus.beleg_erstellen = function(){
	this.zeit_checkhtml();
	$('#zb_b').get(0).checked=true;
	$('#zb_z').get(0).checked=false;
}

etempus.zeit_checkhtml=function(){
	if (typeof($('#zb_b').get(0))=="undefined"){
			location.href="?section=zeit&function=index";	
	}
}



/****************
 * erfassen (zeit/beleg)
 ****************/
etempus.erfassen=function(id){
		if (etempus.erfassen.active==true){
				this.hidecontent();
				$('#cont_time').show();
				//$('body').scrollTo($('#msg_cancel_first'),'300');
				$('#msg_cancel_first').show('slow').animate({opacity: '+=0'}, 2000).hide('slow'); 
				return;
		}
		
		this.hidecontent();	
		//$('#cont_time').show();
		var beleg=$('#zb_b').get(0).checked;
	
		//beleg buchen
		if (beleg){ this.erfassen.beleg(id); }
		else { this.erfassen.zeit(id); }
		
}

// erf-var
etempus.erfassen.active=false;


/******************************
 * zeit erfassen 
 ******************************/
etempus.erfassen.zeit =function(id){
	$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'zeit','function':'buchen', 'id':id },
	  		success: function(data) {
					$('#cont_time').show();
					$('#cont_time').get(0).innerHTML=data;
					$("#date_start").datepicker( { showOn: 'button', buttonImageOnly: true, buttonImage: 'images/form_datepicker.png' });
					$("#date_end").datepicker( { showOn: 'button', buttonImageOnly: true, buttonImage: 'images/form_datepicker.png' });
					etempus.erfassen.active=true;
					etempus.comment_required_err=false;
					$.cookie('etempus.erfassen_pid', id);
					etempus.timer.autostart();
					
					if (etempus.timer.running==false){
						$('#button_start').hide();
						$('#button_pause').show();
						$('#button_cancel').show();
						$('#button_fini').show();
						etempus.timer.start();
						$('div#treeP_'+id+', div#treeP0_'+id+', div#treeP1_'+id).addClass('active');
					} else {
						setTimeout("var id="+id+";$('div#treeP_'+id+', div#treeP0_'+id+', div#treeP1_'+id).addClass('active');","150");
					}
					
			}
	});
}

/****************
 * zeit erfassen abbrechen
 ****************/
efr_ask=false;
etempus.erfassen.cancel = function(){
		if (efr_ask) { return; }
		efr_ask=true;
		$('#button_cancel').append('<span id="ask_cancel_time"><input type="button" value="'+etempus.lang.ja+'" id="frage_ja" /> <input type="button" id="frage_nein" value="'+etempus.lang.nein+'" /></span>');
		
		$('#frage_ja').click(function(){
			efr_ask=false;
			etempus.timer.stop();
			etempus.erfassen.active=false;
			$('#cont_time').get(0).innerHTML="";
			$.cookie('etempus.erfassen_pid', null);
			$('#tree div.active').removeClass('active');	
		});
		
		$('#frage_nein').click(function(){
			efr_ask=false;
			$('#ask_cancel_time').remove();
			
		});
}

/****************
 * zeit erfassen abschliessen
 ****************/
etempus.comment_required_err=false;
etempus.erfassen.fini=function(){
		//wenn kommentar obligatorisch
		if (etempus.comment_required===true){
			var comment=$('#comment').val();
			if (comment==''){ 
					if (etempus.comment_required_err==false){
						$('#comment').addClass('error'); 
						$('#comment').parent('.subcr').append('<label for="comment" class="error">'+etempus.lang.zeit_comment_empty+'</label>');
						etempus.comment_required_err=true;
						$('#comment').keyup(function () {
							if (this.value.length>=1){ 
								$('#comment').removeClass('error'); 
								$('#comment').parent('.subcr').children('label.error').remove();
								etempus.comment_required_err=false;
							} else {
								if (etempus.comment_required_err==false){
									$('#comment').addClass('error'); 
									$('#comment').parent('.subcr').append('<label for="comment" class="error">'+etempus.lang.zeit_comment_empty+'</label>');
									etempus.comment_required_err=true;
								}
							}
						});
					}
					return; 
			}
		}
						
		var secounds ="0";
		if (etempus.timer.manual==true){
				var mode = "manual";
		} else {
			etempus.timer.stop();
			var mode = "timed";
			secounds = etempus.timer.secounds;
		}
		if ($.cookie('etempus.erfassen_pid')==null){
			id=etempus.erfassen.pid;
		} else {
			id=$.cookie('etempus.erfassen_pid');
		}		
		$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'zeit', 'function':'buchen_go', 'id':id, 'mode':mode,
				   'secounds':secounds, 'dstart':$('#date_start').val(), 'tstart':$('#time_start').val(),
				   'dend':$('#date_end').val(), 'tend':$('#time_end').val(), "ansatz":$('#ansatz_dd').val(), "txt":$('#comment').val() },
	  		success: function(data) {
					$.cookie('etempus.erfassen_pid',null);
					etempus.erfassen.active=false;
					$('#cont_time').html(data);
					etempus.list.last_most();
					etempus.list.zeit_liste();
					$('#k_list div.active').removeClass('active');
			}
		});
		
}

/****************
 * zeit erfassen autostart
 ****************/
etempus.erfassen.autostart=function(){
		var pid=$.cookie('etempus.erfassen_pid');
		if (pid){
			etempus.erfassen.zeit(pid);
		}
}


/****************
 * beleg erfassen
 ****************/
etempus.erfassen.beleg =function(id){
	etempus.erfassen.pid=id;
	etempus.comment_required_err=false;
	etempus.num_err=false;
	$('#tree div.active').removeClass('active');
	$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'zeit','function':'beleg_layout', 'id':id },
	  		success: function(data) {
					$('div#treeP_'+id+', div#treeP0_'+id+', div#treeP1_'+id).addClass('active');
					$('#cont_time').show('slow');
					$('#cont_time').html(data);
					$("#datum_start").datepicker({ showOn: 'button', buttonImageOnly: true, buttonImage: 'images/form_datepicker.png' , altFormat: 'dd.mm.yy' });
					etempus.comment_required_err=false;
					etempus.comment_num_err=false;
			}
	});
}


/****************
 * beleg eintragen
 ****************/
etempus.num_err=false;
etempus.erfassen.beleg_eintragen=function(){
		
		var err=false;
		//zahl checken
		if (!is_int($('#betrag').val())){
				if (etempus.num_err==false){
						$('#betrag').addClass('error');
						$('#betrag').parent('.subcr').append('<label for="betrag" class="error">'+etempus.lang.beleg_nur_ganzzahl+'</label>');
						etempus.num_err=true;
						$('#betrag').keyup(function () {
											
							if (is_int(this.value)){ 
								$('#betrag').removeClass('error'); 
								$('#betrag').parent('.subcr').children('label.error').remove();
								etempus.num_err=false;
							} else {
								if (etempus.num_err==false){
									$('#betrag').addClass('error'); 
									$('#betrag').parent('.subcr').append('<label for="betrag" class="error">'+etempus.lang.beleg_nur_ganzzahl+'</label>');
									etempus.num_err=true;
								}
							}
						});
				}
				err=true;
		}
		
		//wenn kommentar obligatorisch
		if (etempus.comment_required===true){
			var comment=$('#comment').val();
			if (comment==''){ 
					if (etempus.comment_required_err==false){
						$('#comment').addClass('error'); 
						$('#comment').parent('.subcr').append('<label for="comment" class="error">'+etempus.lang.zeit_comment_empty+'</label>');
						etempus.comment_required_err=true;
						$('#comment').keyup(function () {
							if (this.value.length>=1){ 
								$('#comment').removeClass('error'); 
								$('#comment').parent('.subcr').children('label.error').remove();
								etempus.comment_required_err=false;
							} else {
								if (etempus.comment_required_err==false){
									$('#comment').addClass('error'); 
									$('#comment').parent('.subcr').append('<label for="comment" class="error">'+etempus.lang.zeit_comment_empty+'</label>');
									etempus.comment_required_err=true;
								}
							}
						});
					}
					err=true; 
			}
		}
		
		if (err) { return; }
		
		//ab hier alles validiert, buchen
		$.ajax({
				type: "POST",
				url: "index.php",
				data: { "section":"zeit","function":"beleg_buchen","id":etempus.erfassen.pid,
						"datum":$('#datum_start').val(), "betrag":$('#betrag').val(),"kommentar":$('#comment').val() },
				success: function(data) {
							$('#tree div.active').removeClass('active');
							$('#cont_time').html(data);
							etempus.list.last_most();
							etempus.list.beleg_liste();	
							etempus.comment_required_err=false;
							etempus.num_err=false;
				}
		});
		
}


/****************
 * 
 ****************/
etempus.hidecontent=function(){
		$('#cont_time').hide();
		$('#cont_pref').hide();

}


etempus.get_last_book_time=function(){
	$.ajax({ 
		type: "POST",
		url: "index.php",
		data: { "section":"zeit_ajax", "function":"get_last_book_time" },
		success: function(data) {
			var arr=data.split("||");
			$('#date_start').val(arr[0]);
			$('#time_start').val(arr[1]);
			etempus.timer.update_ts();
		}
	});	
}


/******************************
 * 
 * alte funktionen
 * 
 ******************************/

function lade_projekt_liste(kunden_id, area, chk_id, show_all, onchange){
			
			area_obj = (typeof area == 'undefined') ? $('#projekt_liste') : area;
			chk_id = (typeof chk_id == 'undefined') ? 0 : chk_id;
			show_all = (typeof show_all == 'undefined') ? "" : show_all;
			onchange = (typeof onchange == 'undefined') ? "" : onchange;
			var req_url = '?section=zeit_ajax&function=projektliste&kunden_id='+kunden_id+'&chk='+chk_id+'&showall='+show_all+'&onchange='+onchange;
			area_obj.load(req_url);
}

function lade_ansatz_liste(proj_id, area, chk_id, onchange) {
			area_obj = (typeof area != 'object') ? $('#ansatz_liste') : area;
			chk_id = (typeof chk_id == 'undefined') ? 0 : chk_id;
			onchange = (typeof onchange == 'undefined') ? "" : onchange;
			var req_url = '?section=zeit_ajax&function=ansatzliste&projekt_id='+proj_id+'&chk='+chk_id+"&onchange="+onchange;
			area_obj.load(req_url);
}

function lade_kunden_liste(area, chk_id, onchange) {
			area_obj = (typeof area == 'undefined') ? $('#kunden_liste') : area;
			chk_id = (typeof chk_id == 'undefined') ? 0 : chk_id;
			onchange = (typeof onchange == 'undefined') ? "" : onchange;
			var req_url = '?section=zeit_ajax&function=kundenliste&chk='+chk_id+"&onchange="+onchange;
			area_obj.load(req_url);
}

function edit_ansatz(ansatz_id){
		$('#ans_box').load('?section=einstellungen&function=ansatz_tt&id='+ansatz_id);
		$('#ans_box').dialog( { draggable: true } ); 
}

function getdate(){
			var d = new Date()
			tag = d.getDate();
			monat= d.getMonth()+1;
			var tagesdatum =  ((tag < 10) ? "0" : "") + tag;
			tagesdatum += ((monat < 10) ? ".0" : ".") + monat;
			tagesdatum += "."+d.getFullYear();
			return tagesdatum;	
}
	
//aktuelle zeit ausgeben
function gettime(){
			var Jetzt = new Date()
			Stunden = Jetzt.getHours();
			Minuten = Jetzt.getMinutes();
			Sekunden = Jetzt.getSeconds();
			ZeitString = Stunden;
			ZeitString += ((Minuten < 10) ? ":0" : ":") + Minuten;
			ZeitString += ((Sekunden < 10) ? ":0" : ":") + Sekunden;
			return ZeitString;
}


function is_int(input) { return !isNaN(input)&&parseInt(input)==input; }



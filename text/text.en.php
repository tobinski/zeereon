<?php

$et_lang = "English";

/**********************************************


 Deutsche Sprachdatei f&uuml;r etempus
 (c) 2009 by cyrill v.wattenwyl, protagonist.ch



 anmerkung f&uuml;r die &uuml;bersetzer:
 
 - format: $txt['var_name'] = "text"; es darf NUR der text ge&auml;ndert
	 werden, die Variable wie $txt['var_name'] muss gleich bleiben.
 - tags solltem mit &uuml;bernommen werden , also z.B. <b></b> usw..
 - %s sind dynamische textbausteine, die automatisch von etempus
   erstellt werden. die reihenfolge dieser platzhalter muss immer
   gleich bleiben. beispiel:
   "<b>%s</b> bei Projekt <b>%s</b> eingetragen" muss mit :
   "<b>%s</b> in project <b>%s</b> inserted" &uuml;brersetzt werden, 
   da %s[0] die eingetragene zeit anzeigt und %s[1] der projektname.
   es ist nicht m&ouml;glich zuerst das projekt und dann die zeit auszugeben.
   

***********************************************/





/**********************************************
 Allgemeine Strings
***********************************************/

//   Variable				         "Text" (nur der Text soll &uuml;bersetzt werden) 
$txt['ansatz']				=	"Rate";
$txt['beschreibung']		=	"Description";
$txt['details']				=	"Details";
$txt['einstellungen']		=	"Settings";
$txt['sortierung']			= 	"Sort";
$txt['ja']					=	"Yes";
$txt['nein']				=	"No";
$txt['confirm_del']			= 	"Confirm Delete";
$txt['zeit_eintragen']		=	"Enter Time";
$txt['name']				= 	"Name";
$txt['zeit']				=	"Time";
$txt['datum']				=	"Date";
$txt['jetzt']				=	"now";
$txt['kommentar']			= 	"Comment";
$txt['minute']				=	"Minute";
$txt['minuten']				= 	"Minutes";
$txt['stunde']				= 	"Hour";
$txt['stunden']				= 	"Hours";
$txt['nachricht']			= 	"Message";
$txt['warnung']				=	"Warning";
$txt['zeige']				= 	"show";
$txt['eintragege']			= 	"Entries";
$txt['prefs_saved']			= 	"Entry saved successfully";
$txt['eintrag_del']			=	"1 entry deleted";
$txt['eintrag_del_multi']	=	"%s entries deleted";
$txt['markierte']			= 	"highlight";
$txt['start']				=	"Start";
$txt['monat']				=	"Month";
$txt['jahr']				=	"Year";
$txt['benutzerdefiniert']	=	"User defined";
$txt['von']					=	"from";
$txt['bis']					=	"to";
$txt['datum_waehlen']		= 	"Select Date";
$txt['benutzer']			=	"User";
$txt['kosten']				=	"Cost";
$txt['spalten']				=	"Columns";
$txt['mwst']				=	"Sales Tax";
$txt['mwst_mitrechnen']		=	"Include Sales Tax";
$txt['mwst_satz']			=	"Sales Tax Amount";
$txt['total']				=	"Total";
$txt['totalzeit']			= 	"Total Time";
$txt['sonstiges']			=	"Miscellnaneous";
$txt['heute']				=	"today";
$txt['query_run']			=	"Query executed";
$txt['daten']				=	"Dates";
$txt['wert']				=	"Amount";
$txt['home']				= 	"Home";
$txt['overhead']			= 	"Overhead";
$txt['willkommen']			=	"Welcome";

/**********************************************
 Buttons
***********************************************/

$txt['button_neu']			=	"new";
$txt['button_senden']		=	"send";
$txt['button_erstellen']	=	"create";
$txt['button_speichern']	=	"save";
$txt['button_del']			=	"delete";
$txt['button_detail']		=	"Details";
$txt['button_edit']			=	"edit";
$txt['button_eintragen']	=	"enter";
$txt['button_zeigen']		= 	"show";
$txt['button_auswerten']	=	"calculate";
$txt['button_aktivieren']	=	"activate";
$txt['button_deaktivieren']	=	"deactivate";


/**********************************************
 Fehler
***********************************************/

$txt['error']				= 	"Error";
$txt['error_connect_db']	=	"Error connectiong to database : ";
$txt['error_choose_db']		=	"Can not select database %s : ";
$txt['error_empty']			=	"Enter all fields";
$txt['error_noname']		=	"Enter Name";
$txt['not_implemented']		= 	"This function is not yet available";
$txt['not_implemented_s']	= 	"not implemented";
$txt['error_nouser']		=	"The user is not in the database, can  not access eTempus";

$txt['error_date']			= 	"Date format incorrect. Try: Tag.Monat.Jahr";
$txt['error_time']			=	"Time format incorrect. Try: Stunde:Minute:Sekunde ";
$txt['error_timestamp']		=	"Can not calculate Unix-Timestamp from the given date format";
$txt['error_time_weird']	=	"Start time can not be greater then end time";
$txt['error_zeit_existiert']=	"You worked already in this time slot, you can only work once!";
$txt['error_id_fails']		=	"Invalid ID";
$txt['error_session_chk']	=	"ID does not correspond with Session. Error might be caused by refreshing of the site.";
$txt['error_user_max']		= 	"Can not add more User. Maximum Uer for this Installation is: %s .";
$txt['warning_kostendach']	=	"Maximum cost of <b>%s.-</b> in Project <b>%s</b> has been exeeded by %s%%";



/*********************************************
 Home
**********************************************/

$txt['monat_statistik']		= 	"Montly stats";
$txt['nachrichten']			=	"Messages";
$txt['post_nachricht']		=	"Write Message";


/**********************************************
 Kunden
***********************************************/

$txt['kunde']				=	"Clients";
$txt['kunde_s']				=	"Client";
$txt['kunde_neu']			=	"Add Client";
$txt['kunde_name']			=	"Name";
$txt['kunde_adresse']		=	"Address";
$txt['kunde_plz']			=	"Postal Code";
$txt['kunde_ort']			=	"City";
$txt['kunde_ansprech']		=	"Contact Person";
$txt['kunde_telefon']		=	"Phone";
$txt['kunde_email']			=	"E-Mail";
$txt['kunde_activ']			=	"Last activity";
$txt['kunde_auswertung']	= 	"Evaluation";
$txt['kunde_detail']		=	"Details Client";
$txt['kunde_edit']			=	"Update Client";



/**********************************************
 Projekte
***********************************************/

$txt['projekt']				=	"Projects";
$txt['projekt_s']			= 	"Project";
$txt['projekt_neu']			=	"add Project";
$txt['projekt_del_confirm'] =	"Do you really want to delete Project <b>%s</b> ? All attached time entries will be deleted as well";
$txt['projekt_details']		= 	"Project details";
$txt['projekt_edit']		=	"Project settings";
$txt['ansatz_alle']			= 	"Use all rates";
$txt['ansatz_limit']		=	"Set rates";
$txt['kostendach']			=	"Max. Cost";

/**********************************************
 Zeit
***********************************************/

$txt['zeit_start_titel']	=	"Time";
$txt['zeit_start']			=	"Start time";
$txt['zeit_ende']			=	"End time";
$txt['zeit_eintragen']		=	"Enter time";
$txt['zeit_comment_empty']	= 	"Add comment";
$txt['zeit_gesamt']			= 	"Total time";
$txt['zeit_eingetragen']	=	"<b>%s</b> entered with Project <b>%s</b>";
$txt['zeit_beleg_eintragen']=	"<b>%s</b>.- entered with Project<b>%s</b>";
$txt['meine_zeit']			= 	"My time";
$txt['letzte_eintraege']	=	"Last entries";
$txt['zeit_eintrag_aendern']=	"Update entry";
$txt['overhead_aktivieren'] =	"Activate Overhead (Set rate in Project details)";


$txt['zeit_eintrag_del']	=	"Delete Entry?";
$txt['zeit_eintrag_multidel']=	"Delete Entries?";
$txt['einstellungen']		=	"Settings";
$txt['auswertung']			=	"Evaluate";
$txt['beleg']				= 	"Proof";
$txt['belege']				=	"Proofs";
$txt['zeiteintraege']		=	"Time entries";
$txt['betrag']				= 	"Amount";
$txt['beleg_eintragen']		=	"Add Proofs";
$txt['eintrag_edit_ok']		= 	"Entry updated successfully";
$txt['beleg_nur_ganzzahl']	=	"No decimals in Amounts";
$txt['overhead_eintragen']	=	"Add Overhead";
$txt['overhead_error_empty']=	"There are no Overhead projects with the selected Client, there are 0 Seconds entered.";
$txt['overhead_ok']			=	"Overhead on <b>%s</b> Projects. <br /><br />Details:<br />%s<br />Total time: <b>%s</b> or <b>%s%%</b> of Time.";
$txt['overhead_ok_list']	=	"<b>%s</b> bei Projekt <b>%s</b><br />";

/**********************************************
 Auswertung
**********************************************/
$txt['auswertung_']					=	"";
$txt['auswertung_kunde']			=	"Evaluation Client";
$txt['auswertung_projekt']			=	"Evaluation Project";
$txt['auswertung_user']				=	"Evaluation User";
$txt['kunde_waehlen']				=	"Select Client";
$txt['zeitraum_waehlen']			=	"Select Time Span";
$txt['details_waehlen']				=	"Select Details";
$txt['projekt_waehlen']				=	"Select Project";
$txt['format_waehlen']				=	"Select Format";
$txt['beschraenkung_waehlen']		=	"Select Limitations (optimal)";
$txt['nur_benutzer']				=	"Only User";
$txt['user_waehlen']				=	"Select User";
$txt['html_this_win']				=	"HTML in this window";
$txt['html_new_win']				=	"HTML in new window";
$txt['csv']							=	"CSV-Format";
$txt['csv_dateiname']				=	"evaluation_%s";
$txt['optimieren_fuer']				=	"optimize for";
$txt['openoffice']					= 	"OpenOffice.org";
$txt['excel']						=	"Microsoft Excel";
$txt['nur_kunde']					=	"Only Client";
$txt['nur_projekt']					=	"Only Project";
$txt['zeit_in_stunden']				=	"Time in Hours";

/***********************************************
 Monate
***********************************************/
$txt['monate']						=	array(1		=>	"January",
											  2		=>	"February",
											  3		=>	"March",
											  4		=>	"April",
											  5		=>	"May",
											  6		=>	"June",
											  7		=>	"July",
											  8		=>	"August",
											  9		=>	"September",
											  10	=>	"October",
											  11	=>	"November",
											  12	=>	"December");


/*************************************************
 einstellungen
***************************************************/
$txt['config_pw_lang']			=	"Password, Language & Style";
$txt['config_ansatz']			=	"Rates";
$txt['config_misc']				= 	"Miscellaneous";
$txt['config_user']				=	"User Management";
$txt['config_user_del']			= 	"Do you really want to delete user <b>%s</b>? Important: All their time entries / proofs will also be deleted!";
$txt['conf_user_del_ok']		= 	"User <b>%s</b> deleted";
$txt['new_user']				=	"New User";
$txt['login']					= 	"Login";
$txt['passwort']				=	"Password";
$txt['nocheinmal']				=	"again";
$txt['voller_name']				=	"Full Name";
$txt['benutzer_angelegt']		=	"User <b>%s</b> added";
$txt['pw_not_match']			= 	"The Passwords do not correspond";
$txt['new_user_h1']				=	"Add new User";
$txt['config_user_name_change']	=	"Update Name & Login";
$txt['config_user_pw_change']	=	"Update Password";
$txt['pw_changed']				=	"Passwort Updated Successfully";
$txt['data_changed']			= 	"Data updated";
$txt['config_ansatz_neu']		=	"Add new Rate";
$txt['config_ansatz_ok']		=	"New Rate added successfully";
$txt['config_ansatz_editok']	= 	"Rate Updated";
$txt['config_ansatz_edit']		=	"Update Rate";
$txt['config_ansatz_del']		=	"1 Rate deleted";
$txt['ansatz_eintrag_del']		= 	"Do you really want to delete this rate? <b>Important:</b> All Time entries with this Rate will also be deleted!";

$txt['pw_not_ok']				= 	"Password wrong!";
$txt['altes_passwort']			= 	"Old Password";
$txt['neues_passwort']			= 	"New Password";
$txt['sprache']					= 	"Language";
$txt['not_allowed']				= 	"Access to this page is only available as admin";
$txt['plugin_error']			= 	"Cannot load Plugin <b>%s</b>, dying badly";
$txt['style']					=	"Style";
$txt['projekt_tagcloud']		= 	"Tagcloud of the last projekt-activity";
$txt['auswertung_fix']			= 	"Auswertung Fixpreis";
$txt['logout']					=	"<img src='css/screen/images/logout.png' alt='Logout'>";

$txt['alle_kunden']				= 	"All Customers";
$txt['alle_projekte']			= 	"All Projects";
$txt['zeit_csv']				=	"Time (CSV)";


/*********************/
//1.3
/********************/
$txt['required']					= 	"this Field is Required";
$txt['ans_at_least_one']			= 	"select at least one";


$txt["topmenu_zeit"]				=	"Book Time";
$txt["topmenu_auswerten"]			=	"Evaluate / Bills";


$txt["zerf_start"]				=	"start";
$txt["zerf_cancel"]				=	"cancel";
$txt["zerf_pause"]				=	"pause";
$txt['zerf_fini']				=	"finish";


$txt['cancel_first']			= "please cancel first the actual timer";
$txt['really_cancel']			= "really cancel?";
$txt['tagestotal']				= "day-total";
$txt['edit_cancel']				= "cancel edit";
$txt['tage']					= "Days";
$txt['activate_js']				= "please turn on Javascript";
$txt['cal_is_rtl']				= 'false';
?>

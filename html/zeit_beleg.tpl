
<div id="col3_content" class="clearfix">
	<form id="beleg_neu">		 
		<!-- Subtemplate: 2 Spalten mit 33/66 Teilung -->
		<h1>{beleg}</h1>
		{msg}<br /><br />
		
		
		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		     	<img src="images/li-1.png" class="li"> {projekt_s}		     
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
				<b>{proj_name}</b> ({kname})
		    </div>
		  </div>
		</div>
		
		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		     	<img src="images/li-2.png" class="li"> {datum}		     
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr" id="dat">
				<input type="text" size="10" id="datum_start" name="datum_start" value="{today}" />
		    </div>
		  </div>
		</div>
		
		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		     	<img src="images/li-3.png" class="li"> {betrag}		     
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
			 	<input type="text" size=5 id="betrag" name="betrag" />
		    </div>
		  </div>
		</div>
		 
		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		     	<img src="images/li-4.png" class="li"> {kommentar}		     
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
			 	<textarea cols=40 rows=2 name="kommentar" id="comment"></textarea>
		    </div>
		  </div>
		</div>
		
		<input type="button" value="{button_eintragen}" onclick="etempus.erfassen.beleg_eintragen();" />
	</form>
	
</div>
				


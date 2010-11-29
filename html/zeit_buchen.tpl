<div id="col3_content" class="clearfix">
		 <!-- Subtemplate: 2 Spalten mit 33/66 Teilung -->
		<div id="msg_cancel_first" class='nodisp'>
		{msg_cancel_first}
		</div>
		{msg}
		
		
		
		
		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		     <img src="images/li-2.png" class="li"><span> {ansatz}    </span>
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
			{ansatz_liste}
		    </div>
		  </div>
		</div> 

		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		     <span><img src="images/li-indent.png" class="li"> {von}</span>
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
		      {datum}: <input type="text" name="startDatum" class="date" id="date_start" onchange="etempus.timer.update_ts();" value="{date_now}" />
		      {zeit}: <input type="text" name="startZeit" class="time" id="time_start" onchange="etempus.timer.update_ts();" value="{time_now}"> 
		      
		      <input type="button" value="Jetzt" onclick="$('#time_start').val(gettime());etempus.timer.update_ts();">
		      <input type="button" value="Letzte" onclick="etempus.get_last_book_time();">

		    
		    </div>
		  </div>
		</div> 

		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		     <span><img src="images/li-indent.png" class="li"> {bis}</span>
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
		      {datum}: <input type="text" name="endDatum" class="date" id="date_end" onchange="etempus.timer.update_manual()">
			  {zeit}: <input type="text" name="endZeit" class="time" id="time_end" onchange="etempus.timer.update_manual()"> 
			  <input type="button" value="Jetzt" onclick="$('#time_end').val(gettime());">
		    </div>
		  </div>
		</div> 

		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		    <span class="li-top"><img src="images/li-indent.png" class="li"> {kommentar}</span>
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
		      <textarea id="comment"></textarea>
		    </div>
		  </div>
		</div> 
		<div class="subcolumns">
		  <div class="c25l">
		    <div class="subcl">
		    <img src="images/li-3.png" class="li"> <span>Zeit Buchen</span>
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
		    </div>
		  </div>
		</div> 
		
		<div class="subcolumns" id="timeCounter">
		  <div class="c25l">
		    <div class="subcl">
			
			<div id="time">00:00:00</div>
		     </div>
		  </div>
		  <div class="c75r">
		    <div class="subcr">
			
		      <div id="button_start" class="">
			  	<p class="link" onclick="etempus.timer.start();"><img src="images/timer_play.jpg" alt="start">{zerf_start}</p>
			  </div>
			  <div id="button_pause" class="nodisp">
			  	<p class="link" onclick="etempus.timer.pause();"><img src="images/timer_pause.png" alt="pause"> {zerf_pause}</p>
			  </div>
			  <div id="button_cancel" class="nodisp">
			  	<p class="link" onclick="etempus.erfassen.cancel();"><img src="images/timer_abbrechen.png" alt="stop">{zerf_cancel}</p>
		      </div>
			  <div style="clear:both;"></div>
			  <div id="button_fini" class="nodisp"> 
			  	<p class="link" onclick="etempus.erfassen.fini();"><img src="images/timer_start.png" alt="fini">{zerf_fini}</p>
		      </div>
			  
		    </div>
		  </div>
		</div> 

        <!-- IE column clearing -->
        <div id="ie_clearing">&nbsp;</div>

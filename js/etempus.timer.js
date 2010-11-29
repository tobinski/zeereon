

// str_pad function
str_pad = function(n, totalDigits) { 
		n = n.toString(); 
		var pd = ''; 
		if (totalDigits > n.length) { 
				for (i=0; i < (totalDigits-n.length); i++){ 
						pd += '0'; 
				} 
		} 
		return pd + n.toString(); 
} 


// sorry but javascript and strtotime ar not cool so i get the answer from php
strtotime = function (str) {

} 







//replace all helper function
String.prototype.ReplaceAll = function(stringToFind,stringToReplace){
    var temp = this;
    var index = temp.indexOf(stringToFind);
        while(index != -1){
            temp = temp.replace(stringToFind,stringToReplace);
            index = temp.indexOf(stringToFind);
        }
        return temp;
}



/********************************************
 * 
 * 
 * 
 * 
 ********************************************/


etempus.timer = new Object();
etempus.timer.running = false;
etempus.timer.secounds = 0;
etempus.timer.ts_start = 0;
etempus.timer.paused = false;
etempus.timer.pause_secounds=0;
etempus.timer.from_cookie=false;
etempus.timer.manual=false;



etempus.timer.start = function() {
		
		$('#button_pause').show();
		$('#button_start').hide();
		etempus.timer.manual=false;
		
		if (this.paused==true){ this.pause(); }
		
		if (this.running==false){ 
			if (this.ts_start===0){
				etempus.timer.ts_start=this.get_timestamp();
			}
			this.set_cookie();
			this.running = true;
			this.refresh_html();
			this.timer=setInterval("etempus.timer.refresh_html()", 1000);
			this.from_cookie=false;
		}
}



etempus.timer.stop =  function() {
		if (this.paused==true){ this.pause(); }
		this.del_cookie();
		this.running = false;
		this.ts_start=0;
		clearInterval(etempus.timer.timer);
		
}


etempus.timer.pause =  function() {
		
		if (this.running && this.paused==false){
				
				//dbg('pause');
				clearInterval(this.timer);
				this.del_cookie();
				this.paused = true;
				this.pause_secounds=this.secounds;
				this.pause_timer=setInterval("etempus.timer.refresh_html_pause()", 500);
				this.set_pause_cookie();
				$('#button_start').show();
				$('#button_pause').hide();
				
		} else if (this.paused==true){
				
				//dbg('stop-pause - old:'+this.pause_secounds);
				$('div[id=time]').show('fast');
				clearInterval(this.pause_timer);
				this.paused = false;
				this.ts_start=this.get_timestamp()-this.pause_secounds;
				this.pause_secounds=0;
				this.running=false;
				this.start();
				this.del_pause_cookie();
				
		}
}


//
// update start timestamp on changin manual
//
etempus.timer.update_ts=function(){
		if (etempus.timer.manual==true){
			etempus.timer.update_manual();
			return;
		}
		var date=$("#date_start").val() + " " + $("#time_start").val();
		$.ajax({
	  		type: "POST",
	  		url: "index.php",
	  		data: {'section':'zeit_ajax','function':'str_to_time', 'ts':date },
	  		success: function(data) { 
				etempus.timer.ts_start=data; 
				etempus.timer.set_cookie();
			}
		});
		

		
}





etempus.timer.get_cookie =  function() {
		ts_start = $.cookie('etempus.timer_start');
		pause = $.cookie('etempus.timer_pause');
		if (pause) {
				//dbg("pause:cookie");
				$('#button_start').show();
				$('#button_pause').hide();
				$('#button_cancel').show();
				$('#button_fini').show();
				
				this.running=true;
				this.paused = true;
				this.pause_secounds = pause;
				this.pause_timer=setInterval("etempus.timer.refresh_html_pause()", 500);
				$('div[id=time]').get(0).innerHTML=this.format_time(pause);				
				
		} else if (ts_start){
				//dbg("start:cookie");
				$('#button_start').hide();
				$('#button_pause').show();
				$('#button_cancel').show();
				$('#button_fini').show();
				
				this.ts_start=ts_start;
				this.running = true;
				this.refresh_html();
				this.timer=setInterval("etempus.timer.refresh_html()", 1000);
		}
}


etempus.timer.set_cookie =  function() {
		$.cookie('etempus.timer_start', this.ts_start);
}
etempus.timer.set_pause_cookie =  function() {
		$.cookie('etempus.timer_pause', this.pause_secounds);
}
etempus.timer.del_cookie = function () {
		$.cookie('etempus.timer_start', null);
}
etempus.timer.del_pause_cookie =  function() {
		$.cookie('etempus.timer_pause', null);
}


etempus.timer.autostart =  function() {
		this.get_cookie();
		if (this.ts_start){
			this.from_cookie=true;
			this.start();
		}
}


etempus.timer.refresh_html = function (){
		this.secounds=this.get_timestamp()-this.ts_start;
		$('div[id=time]').get(0).innerHTML=this.format_time(this.secounds);
}

etempus.timer.refresh_html_pause = function(){
		$('div[id=time]').toggle('fast');
}

etempus.timer.get_timestamp = function(){
		return Math.round(new Date().getTime()/1000);
}

etempus.timer.format_time = function(orig_secounds){
		var hours=Math.floor(orig_secounds/3600);
		var minutes=Math.floor(orig_secounds/60)-(hours*60);
		var seconds=orig_secounds-(hours*3600)-(minutes*60);
		return str_pad(hours,2)+':'+str_pad(minutes,2)+':'+str_pad(seconds,2);
}









/**************/
etempus.timer.update_manual = function(){
		
		if (etempus.timer.manual==false){
			$('#button_start').hide();
			$('#button_pause').hide();
			$('#button_cancel').hide();
			$('#button_fini').show();
			etempus.timer.stop();
			etempus.erfassen.active=false;
			etempus.timer.manual=true;
			etempus.erfassen.pid=$.cookie('etempus.erfassen_pid');
			$.cookie('etempus.erfassen_pid',null);
		}
		
		var date_s=$("#date_start").val() + " " + $("#time_start").val();
		var date_e=$("#date_end").val() + " " + $("#time_end").val();
		
		$.ajax({
				type: "POST",
				url: "index.php",
				data: {'section':'zeit_ajax','function':'time_manual', 'date_s':date_s, 'date_e':date_e },
				success: function(data) { 
						$('div[id=time]').html(etempus.timer.format_time(data));
				}
				
		});

}





/**
 * Copyright (c) 2016 Nicholas Hurd trading as XCalibre8 Unlimited
 *
 * This file is licenced under XCalibre8 Licence X as it stood on 13 Nov 2016
 * The licence may be subject to change and current and past versions can be checked at www.xcalibre8.me/licence.php
 * This file does not add or remove any clauses from the licence.
 * 
 * XCalibre8 Provider Name: XCalibre8 Unlimited
 * XCalibre8 Author Name: Nicholas Hurd
 * XCalibre8 Product Name: XCalibre8.Me
 * 
 * CHANGELOG: None
 **/
function JumpDay(TargetID) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var params = 'For=SCC&ID=1&T='+TargetID;
		params += '&M='+document.getElementById('SCH_MODE').value;
        params += '&DAY='+encodeURIComponent(document.getElementById('SCH_JUMP').value);
        var list = '';
        var nl = document.getElementsByName('SCCType');        
		for (var i = 0; i < nl.length; i++) {
			var obj = nl[i];
			if (obj.checked) {
				list += obj.value + ',';
			}		
		}
		if (list.length > 0) {
			list = list.slice(0,-1);
			params += "&ST=" + list;
		}
		else params += "&ST=0";
        
		var oc = tgt.className;
		LoadTarget(tgt);
        var xh = new XMLHttpRequest();        
        xh.open("POST", "resources/sched/sched_calendar.php", true);
        
        xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        xh.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
            	tgt.className = oc;
                tgt.innerHTML = this.responseText;
            }
        }        
        xh.send(params);		
	}
};
function SaveSchedule(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=SCM&T=" + TargetID;
		params += '&M=' + Mode;
		params += "&ID=" + document.getElementById("SCI_ID").value;		
		params += "&SCT=" + document.getElementById("SCI_SCT_ID").value;
		var md = document.getElementById("SCI_START_D");
		var mt = document.getElementById("SCI_START_T");
		params += "&BEG=" + encodeURIComponent(md.value+' '+mt.value);
		md = document.getElementById("SCI_END_D");
		mt = document.getElementById("SCI_END_T");
		params += "&END=" + encodeURIComponent(md.value+' '+mt.value);
		var dt = new Date(0);
		dt.setFullYear(1970);
		dt.setMonth(1);
		dt.setDate(document.getElementById("BRK_DAYS").value+1);
		dt.setHours(document.getElementById("BRK_HOURS").value);
		dt.setMinutes(document.getElementById("BRK_MINS").value);
		var ds = dt.getFullYear()+'-'+dt.getMonth()+'-'+dt.getDate()+' '+dt.getHours()+':'+dt.getMinutes()+':00';
		params += "&BRK=" + encodeURIComponent(ds);
		var oc = tgt.className;
		LoadTarget(tgt);		
		
		xh.open('POST', "resources/sched/sched_mod.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
		xh.onreadystatechange = function() {
		    if(this.readyState == 4 && this.status == 200) {
		    	tgt.className = oc;
		        tgt.innerHTML = this.responseText;
		    }
		}
		xh.send(params);
	}
};
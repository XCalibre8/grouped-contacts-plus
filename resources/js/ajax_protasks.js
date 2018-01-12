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
var EdTaskDoc = '';
function AddEditProTask(TargetID,PType,Task) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPT&T="+TargetID;
		params += "&TID=" + PType;
		params += "&ID=" + Task;
		if (Task > 0)
			params += "&M=M";
		else params += "&M=N";
		var oc = tgt.className;
		LoadTarget(tgt);
	
		xh.open('POST', "resources/protasks/typed_protasks.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
		xh.onreadystatechange = function() {
		    if(this.readyState == 4 && this.status == 200) {
		    	tgt.className = oc;
		        tgt.innerHTML = this.responseText;
		        EdTaskDoc = CKEDITOR.replace('TSK_DOCUMENT', {
					height: 260
				});
		    }
		}
		xh.send(params);
	}
};
function AddEditSubTask(TargetID,PTask,Task) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=STN&T="+TargetID;
		params += "&PID=" + PTask;
		params += "&ID=" + Task;
		if (Task > 0)
			params += "&M=M";
		else params += "&M=N";
		var oc = tgt.className;
		LoadTarget(tgt);
	
		xh.open('POST', "resources/protasks/subtask_nest.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
		xh.onreadystatechange = function() {
		    if(this.readyState == 4 && this.status == 200) {
		    	tgt.className = oc;
		        tgt.innerHTML = this.responseText;
		        EdTaskDoc = CKEDITOR.replace('TSK_DOCUMENT', {
					height: 260
				});
		    }
		}
		xh.send(params);
	}
};
function SaveProTask(TargetID,Mode,ListTST) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=TPT&T='+TargetID+'&M='+Mode+'&TID='+ListTST;		
		params += "&ID=" + document.getElementById("TSK_ID").value;
		params += "&ETID=" + document.getElementById("TSK_TST_ID").value;
		params += "&Tit=" + encodeURIComponent(document.getElementById("TSK_TITLE").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("TSK_DESC").value);
		if (document.getElementById('TSK_REPEAT').checked)
			params += '&Rpt=1';
		else params += '&Rpt=0';
		params += "&Doc=" + encodeURIComponent(EdTaskDoc.getData());
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/protasks/typed_protasks.php", true);
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
function SaveSubTask(TargetID,Mode,ListPID) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=STN&T='+TargetID+'&M='+Mode+'&PID='+ListPID;	
		params += "&ID=" + document.getElementById("TSK_ID").value;
		params += "&ETID=" + document.getElementById("TSK_TST_ID").value;
		params += "&Tit=" + encodeURIComponent(document.getElementById("TSK_TITLE").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("TSK_DESC").value);
		if (document.getElementById('TSK_REPEAT').checked)
			params += '&Rpt=1';
		else params += '&Rpt=0';
		params += "&Doc=" + encodeURIComponent(EdTaskDoc.getData());
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/protasks/subtask_nest.php", true);
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
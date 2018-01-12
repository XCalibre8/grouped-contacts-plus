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
function SaveContactType(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=CTT&T=" + TargetID + "&M=" + Mode;
		params += "&ID=" + encodeURIComponent(document.getElementById("CTT_ID").value);
		params += "&Name=" + encodeURIComponent(document.getElementById("CTT_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("CTT_DESC").value);
		var oc = tgt.className;
		LoadTarget(tgt);
			
		xh.open('POST', "resources/contacts/contact_types.php", true);	
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
function SaveContactMethod(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=CPT&T=" + TargetID + "&M=" + Mode;
		params += "&ID=" + encodeURIComponent(document.getElementById("CPT_ID").value);
		params += "&Name=" + encodeURIComponent(document.getElementById("CPT_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("CPT_DESC").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/contacts/contact_methods.php", true);
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
function SaveTitle(TargetID,Mode) {	
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TIT&T=" + TargetID + "&M=" + Mode;
		params += "&ID=" + encodeURIComponent(document.getElementById("TIT_ID").value);
		params += "&Title=" + encodeURIComponent(document.getElementById("TIT_TITLE").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("TIT_DESC").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/contacts/contact_titles.php", true);
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
function SaveGroupType(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=GTP&T=" + TargetID + "&M=" + Mode;
		params += "&ID=" + document.getElementById("GTP_ID").value;
		params += "&Name=" + encodeURIComponent(document.getElementById("GTP_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("GTP_DESC").value);
		var oc = tgt.className;
		LoadTarget(tgt);
			
		xh.open('POST', "resources/groups/group_types.php", true);
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
function SaveProTaskState(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=PST&T=" + TargetID + "&M=" + Mode;
		params += "&ID=" + document.getElementById("TSS_ID").value;
		params += "&Name=" + encodeURIComponent(document.getElementById("TSS_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("TSS_DESC").value);
		if (document.getElementById("TSS_COMP").checked)
			params += "&Comp=1";
		else params += "&Comp=0";
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/protasks/protask_states.php", true);
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
function SaveProTaskType(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=PTP&T=" + TargetID + "&M=" + Mode;
		params += "&ID=" + encodeURIComponent(document.getElementById("TST_ID").value);
		params += "&Name=" + encodeURIComponent(document.getElementById("TST_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("TST_DESC").value);
		if (document.getElementById('TST_SUB').checked)
		  params += "&Sub=1";
		else params += "&Sub=0";
		var oc = tgt.className;
		LoadTarget(tgt);
			
		xh.open('POST', "resources/protasks/protask_types.php", true);	
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
function SaveFulfillmentType(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=RFT&T=" + TargetID + "&M=" + Mode;
		params += "&ID=" + document.getElementById("FLT_ID").value;
		params += "&Name=" + encodeURIComponent(document.getElementById("FLT_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("FLT_DESC").value);
		params += "&LTP=" + document.getElementById("FLT_LTP_ID").value;
		var oc = tgt.className;
		LoadTarget(tgt);
			
		xh.open('POST', "resources/reqful/reqful_fulfill_types.php", true);
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
function AddEditFulfillment(TargetID,FType,Fulfillment) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=FLM&T="+TargetID;
		params += "&TID=" + FType;
		params += "&ID=" + Fulfillment;
		if (Fulfillment > 0)
			params += "&M=M";
		else params += "&M=N";
		var oc = tgt.className;
		LoadTarget(tgt);			
	
		xh.open('POST', "resources/reqful/reqful_fulfillments.php", true);
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
function SaveFulfillment(TargetID,Mode,ListFLT) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=FLM&T='+TargetID+'&M='+Mode+'&TID='+ListFLT; 
		params += "&ID=" + document.getElementById("FLM_ID").value;
		params += "&FLT=" + document.getElementById("FLM_FLT_ID").value;
		params += "&Name=" + encodeURIComponent(document.getElementById("FLM_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("FLM_DESC").value);
		params += "&Mode=" + document.getElementById("FLM_MODE").value;
		lid = document.getElementById("FLM_LINK_ID");
		if (lid)
			params += "&LID=" + lid.value;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/reqful/reqful_fulfillments.php", true);
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
function AddEditFulfillmentLevel(TargetID,Fulfillment,Level) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=FLL&T="+TargetID;
		params += "&FLM=" + Fulfillment;
		params += "&ID=" + Level;
		if (Level > 0)
			params += "&M=M";
		else params += "&M=N";
		var oc = tgt.className;
		LoadTarget(tgt);
	
		xh.open('POST', "resources/reqful/reqful_fulfill_levels.php", true);
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
function SaveFulfillmentLevel(TargetID,Mode,ListFLM) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=FLL&T='+TargetID+'&M='+Mode+'&FLM='+ListFLM; 
		params += "&ID=" + document.getElementById("FLL_ID").value;
		params += "&Name=" + encodeURIComponent(document.getElementById("FLL_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("FLL_DESC").value);
		params += "&LVL=" + document.getElementById("FLL_LEVEL").value;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/reqful/reqful_fulfill_levels.php", true);
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
function SaveScheduleType(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=SCT&T='+TargetID+'&M='+Mode; 
		params += "&ID=" + document.getElementById("SCT_ID").value;
		params += "&Name=" + encodeURIComponent(document.getElementById("SCT_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("SCT_DESC").value);
		params += "&Col=" + document.getElementById("SCT_COLOUR").value;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/sched/sched_types.php", true);
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
function SaveUserRole(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=ROL&T='+TargetID+'&M='+Mode; 
		params += "&ID=" + document.getElementById("ROL_ID").value;
		params += "&Name=" + encodeURIComponent(document.getElementById("ROL_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("ROL_DESC").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/users/user_roles.php", true);
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
function SaveRoleAccess(TargetID) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=RAC&T='+TargetID; 
		params += "&ID=" + document.getElementById("RAC_ROL_ID").value;
		params += "&M=" + document.getElementById("RAC_ACA_ID").value;
		params += "&SVL=" + document.getElementById("RAC_SEARCHVIEW").value;
		params += "&NML=" + document.getElementById("RAC_NEWMOD").value;
		params += "&RML=" + document.getElementById("RAC_REMOVE").value;
		params += "&CFG=" + document.getElementById("RAC_CONFIG").value;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/users/role_access.php", true);
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
function EditPassword(TargetID) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		tgt.innerHTML = '<input id="USR_PASSWORD" type="password" /><br /><input id="USR_PASSWORD2" type="password" />(again)';	
	}
};
function SaveUser(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=USR&T='+TargetID+'&M='+Mode; 
		params += "&ID=" + document.getElementById("USR_ID").value;
		params += "&USN=" + encodeURIComponent(document.getElementById("USR_USERNAME").value);
		var pwd = document.getElementById("USR_PASSWORD");
		var pw2 = document.getElementById("USR_PASSWORD2");
		if (pwd && pw2) {
			params += "&PWD=" + encodeURIComponent(pwd.value);
			params += "&PW2=" + encodeURIComponent(pw2.value);
		}
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/users/users.php", true);
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
function AssignAccountUser(TargetID,WACID,Offset,Records) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=WUS&T='+TargetID; 
		params += "&U=" + document.getElementById("WUS_USR_ID").value;
		params += "&W=" + WACID;
		params += "&RO=" + Offset;
		params += "&RC=" + Records;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/users/web_users.php", true);
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
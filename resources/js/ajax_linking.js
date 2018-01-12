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
function SelAll(TypeName) {
	var src = document.getElementById(TypeName);
	if (src) {
		var bxs = document.getElementsByName("chkSel"+TypeName);
		for (var i = 0; i < bxs.length; i++) 
			bxs[i].checked = src.checked;
	}
	else alert('Incorrect JavaScript call. Function SelAll: No checkboxes match naming format.');
};
function SearchLinks(TargetID,TypeName,Params) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
		params += "&M=S";
		if (Params) {
			params += "&" + Params;
		}
		
		var cname = "edtSearchLink"+TypeName;
	
		var ci = document.getElementById(cname);
		if(ci) {
			var ss = ci.value;
			if (ss.length > 0)
				params += "&S=" + encodeURIComponent(ss);
		}
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/linking/type_links.php", true);
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
function SaveLinkPerson(TargetID,LinkToType,LinkTo,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
		params += "&OLT=" + LinkToType;
		params += "&ID=" + LinkTo;
		params += "&CLT=1";
		params += "&LID=" + document.getElementById("LNK_PER_ID").value;
		params += "&M=" + Mode;
		params += "&ExID=" + document.getElementById("EX_PER_ID").value;
		params += "&TIT=" + document.getElementById("LNK_PER_TIT_ID").value;
		params += "&FNames=" + encodeURIComponent(document.getElementById("LNK_PER_FORENAMES").value);
		params += "&SName=" + encodeURIComponent(document.getElementById("LNK_PER_SURNAME").value);
		var oc = tgt.className;
		LoadTarget(tgt);

		xh.open('POST', "resources/linking/type_links.php", true);
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
function SaveLinkGroup(TargetID,LinkToType,LinkTo,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
	
		params += "&OLT=" + LinkToType;
		params += "&ID=" + LinkTo;
		params += '&CLT=4';
		params += "&LID=" + document.getElementById("LNK_GRP_ID").value;
		params += '&M=' + Mode;
		params += "&ExID=" + document.getElementById("EX_GRP_ID").value;
		params += "&GTP=" + document.getElementById("LNK_GRP_GTP_ID").value;
		params += "&Name=" + encodeURIComponent(document.getElementById("LNK_GRP_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("LNK_GRP_DESC").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/linking/type_links.php", true);
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
function SaveLinkAddress(TargetID,LinkToType,LinkTo,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
	
		params += "&OLT=" + LinkToType;
		params += "&ID=" + LinkTo;
		params += '&CLT=2';
		params += '&M=' + Mode;
		params += "&ExID=" + document.getElementById("EX_ADR_ID").value;
		params += "&LID=" + document.getElementById("LNK_ADR_ID").value;
		params += "&CT=" + document.getElementById("LNK_ADR_CTT_ID").value;
		params += "&ACO=" + encodeURIComponent(document.getElementById("LNK_ADR_CARE_OF").value);
		params += "&AL1=" + encodeURIComponent(document.getElementById("LNK_ADR_LINE1").value);
		params += "&AL2=" + encodeURIComponent(document.getElementById("LNK_ADR_LINE2").value);
		params += "&ATW=" + encodeURIComponent(document.getElementById("LNK_ADR_TOWN").value);
		params += "&ACT=" + encodeURIComponent(document.getElementById("LNK_ADR_COUNTY").value);
		params += "&ACR=" + document.getElementById("LNK_ADR_CNTRY_ID").value;
		params += "&APC=" + encodeURIComponent(document.getElementById("LNK_ADR_POSTCODE").value);
		var oc = tgt.className;
		LoadTarget(tgt);
	
		xh.open('POST', "resources/linking/type_links.php", true);
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
function SaveLinkContactPoint(TargetID,LinkToType,LinkTo,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
	
		params += "&OLT=" + LinkToType;
		params += "&ID=" + LinkTo;
		params += '&CLT=3';
		params += '&M=' + Mode;
		params += "&ExID=" + document.getElementById("EX_CNP_ID").value;
		params += "&LID=" + document.getElementById("LNK_CNP_ID").value;
		params += "&CT=" + document.getElementById("LNK_CNP_CTT_ID").value;
		params += "&ST=" + encodeURIComponent(document.getElementById("LNK_CNP_SPEAK_TO").value);
		params += "&CM=" + document.getElementById("LNK_CNP_CPT_ID").value;
		params += "&CNP=" + encodeURIComponent(document.getElementById("LNK_CNP_CONTACT").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/linking/type_links.php", true);
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
function SaveLinkTask(TargetID,LinkToType,LinkTo,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
	
		params += "&OLT=" + LinkToType;
		params += "&ID=" + LinkTo;
		params += '&CLT=5';
		params += "&LID=" + document.getElementById("LNK_TSK_ID").value;
		params += '&M=' + Mode;
		params += "&ExID=" + document.getElementById("EX_TSK_ID").value;
		params += "&TST=" + document.getElementById("LNK_TSK_TST_ID").value;
		params += "&Tit=" + encodeURIComponent(document.getElementById("LNK_TSK_TITLE").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("LNK_TSK_DESC").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/linking/type_links.php", true);
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
function SaveLinkingTaskState(TargetID,LinkedToType,LinkedTo,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPD&T=" + TargetID;
	
		params += "&CLT=" + LinkedToType;
		params += "&ID=" + LinkedTo;
		params += '&OLT=5';
		params += "&LID=" + document.getElementById("LKD_TSK_ID").value;
		params += '&M=' + Mode;
		params += "&TSS=" + document.getElementById("LKD_STATE").value;
		params += "&NT=" + encodeURIComponent(document.getElementById("LKD_NOTE").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/linking/type_linked.php", true);
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
function SaveLinkTaskState(TargetID,LinkedToType,LinkedTo,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
	
		params += "&CLT=" + LinkedToType;
		params += "&ID=" + document.getElementById("LKS_TSK_ID").value;
		params += '&OLT=5';
		params += "&LID=" + LinkedTo;
		params += '&M=' + Mode;
		params += "&TSS=" + document.getElementById("LKS_STATE").value;
		params += "&NT=" + encodeURIComponent(document.getElementById("LKS_NOTE").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/linking/type_links.php", true);
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
function LinkSelected(TargetID,TypeName,Params) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
		params += "&M=S";
		if (Params) {
			params += "&" + Params;
		}
	
		var c = document.getElementById("edtSearch" + TypeName);
		if(c) {
			params += "&S=" + c.value;
		}
		
		var list = '';
		var sel = document.getElementsByName("chkSel"+TypeName);
		for (var i = 0; i < sel.length; i++) {
			var obj = sel[i];
			if (obj.checked) {
				list += obj.value + ',';
			}		
		}
		if (list.length > 0) {
			list = list.slice(0,-1);
			params += "&LL=" + list;
		}
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/linking/type_links.php", true);
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
function SaveLinkSched(TargetID,LinkToType,LinkTo,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TPL&T=" + TargetID;
	
		params += "&OLT=" + LinkToType;
		params += "&ID=" + LinkTo;
		params += '&CLT=8';
		params += "&LID=" + document.getElementById("LNK_SCI_ID").value;
		params += '&M=' + Mode;
		params += "&SCT=" + document.getElementById("LNK_SCI_SCT_ID").value;
		var md = document.getElementById("LNK_SCI_START_D");
		var mt = document.getElementById("LNK_SCI_START_T");
		params += "&BEG=" + encodeURIComponent(md.value+' '+mt.value);
		md = document.getElementById("LNK_SCI_END_D");
		mt = document.getElementById("LNK_SCI_END_T");
		params += "&END=" + encodeURIComponent(md.value+' '+mt.value);
		var dt = new Date(0);
		dt.setFullYear(1970);
		dt.setMonth(1);
		dt.setDate(document.getElementById("LNK_BRK_DAYS").value+1);
		dt.setHours(document.getElementById("LNK_BRK_HOURS").value);
		dt.setMinutes(document.getElementById("LNK_BRK_MINS").value);
		var ds = dt.getFullYear()+'-'+dt.getMonth()+'-'+dt.getDate()+' '+dt.getHours()+':'+dt.getMinutes()+':00';
		params += "&BRK=" + encodeURIComponent(ds);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/linking/type_links.php", true);
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
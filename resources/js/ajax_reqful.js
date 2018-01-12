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
function SaveFulfillProvider(TargetID,LinkType,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=RQF&T='+TargetID+'&LTP='+LinkType+'&M='+Mode;
		params += "&ID=" + document.getElementById("FLP_LINK_ID").value;
		params += "&FLP=" + document.getElementById("FLP_ID").value;
		params += "&FLT=" + document.getElementById('FLP_FLT_ID').value;
		params += "&FLM=" + document.getElementById('FLP_FLM_ID').value;
		var fll = document.getElementById('FLP_FLL_ID');
		if (fll)
		  params += "&FLL=" + fll.value;
		params += "&REF=" + encodeURIComponent(document.getElementById('FLP_REF').value);
		params += "&ACQ=" + encodeURIComponent(document.getElementById('FLP_ACQUIRED').value);
		params += "&EXP=" + encodeURIComponent(document.getElementById('FLP_EXPIRES').value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/reqful/reqful_fulfill.php", true);
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
function SaveRequirement(TargetID,LinkType,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=RQR&T='+TargetID+'&LTP='+LinkType+'&M='+Mode;
		params += "&ID=" + document.getElementById("REQ_ONR_ID").value;
		params += "&REQ=" + document.getElementById("REQ_ID").value;
		params += "&ALT=" + document.getElementById("REQ_FOR_LTP_ID").value;
		params += "&FLT=" + document.getElementById('REQ_FLT_ID').value;
		var flm = document.getElementById('REQ_FLM_ID');
		if (flm)
			params += "&FLM=" + flm.value;
		else params += "&FLM=0";
		var fll = document.getElementById('REQ_FLL_ID');
		if (fll)
			params += "&FLL=" + fll.value;
		else params += "&FLL=0";
		params += "&NUM=" + document.getElementById('REQ_COUNT').value;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/reqful/reqful_require.php", true);
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
function SelectFulfillmentType(LinkType,LinkID,Prefix,DivPrefix) {
	var TargetID = DivPrefix+'type'+LinkType+'_'+LinkID;	
	var tgt = document.getElementById(TargetID);
	var lt = document.getElementById(Prefix+'_FOR_LTP_ID');	
	if (lt && tgt) {
		var params = 'LTP=' + LinkType;
		params += '&ID=' + LinkID;
		params += '&ALT=' + lt.value;
		params += '&PX=' + Prefix;
		params += '&DPX=' + DivPrefix;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		var xh = new XMLHttpRequest();
		xh.open('POST', 'resources/reqful/reqful_select_type.php', true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xh.onreadystatechange = function() {
		    if(this.readyState == 4 && this.status == 200) {
		    	tgt.className = oc;
		        tgt.innerHTML = this.responseText;
		        SelectFulfillments(LinkType,LinkID,Prefix,DivPrefix);		        
		    }
		}
		xh.send(params);
	}
};
function SelectFulfillments(LinkType,LinkID,Prefix,DivPrefix) {
	var TargetID = DivPrefix+LinkType+'_'+LinkID;
	var tgt = document.getElementById(TargetID);
	var lt = document.getElementById(Prefix+'_FOR_LTP_ID');
	var flt = document.getElementById(Prefix+'_FLT_ID');
	if (flt && tgt) {
		var params = 'LTP=' + LinkType;
		params += '&ID=' + LinkID;
		params += '&PX=' + Prefix;
		params += '&DPX=' + DivPrefix;
		if (lt)
			params += '&ALT=' + lt.value;
		params += '&FLT=' + flt.value;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		var xh = new XMLHttpRequest();
		xh.open('POST', 'resources/reqful/reqful_select_fulfill.php', true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xh.onreadystatechange = function() {
		    if(this.readyState == 4 && this.status == 200) {
		    	tgt.className = oc;
		        tgt.innerHTML = this.responseText;
		        SelectLevels(LinkType,LinkID,Prefix,DivPrefix);
		    }
		}
		xh.send(params);
	}
};
function SelectLevels(LinkType,LinkID,Prefix,DivPrefix) {
	var TargetID = DivPrefix+'level'+LinkType+'_'+LinkID;
	var tgt = document.getElementById(TargetID);
	var lt = document.getElementById(Prefix+'_FOR_LTP_ID');
	var flt = document.getElementById(Prefix+'_FLT_ID');
	var flm = document.getElementById(Prefix+'_FLM_ID');
	var valok = (flm || (flt.value == '0') || (flt.value == 'NULL'));
	if (valok && tgt) {
		var params = 'LTP=' + LinkType;
		params += '&ID=' + LinkID;
		params += '&PX=' + Prefix;
		if (lt)
			params += '&ALT=' + lt.value;
		if (flt)
			params += '&FLT=' + flt.value;
		if (flm)
			params += '&FLM=' + flm.value;
		else params += '&FLM=' + 'NULL';
		var oc = tgt.className;
		LoadTarget(tgt);
		
		var xh = new XMLHttpRequest();
		xh.open('POST', 'resources/reqful/reqful_select_level.php', true);
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
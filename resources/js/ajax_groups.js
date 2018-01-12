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
var EdGroupDoc = '';
function AddEditGroup(TargetID,GType,Group) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=TGP&T="+TargetID;
		params += "&TID=" + GType;
		params += "&ID=" + Group;
		if (Group > 0)
			params += "&M=M";
		else params += "&M=N";
		var oc = tgt.className;
		LoadTarget(tgt);			
	
		xh.open('POST', "resources/groups/typed_groups.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
		xh.onreadystatechange = function() {
		    if(this.readyState == 4 && this.status == 200) {
		        tgt.className = oc;
		    	tgt.innerHTML = this.responseText;
		        EdGroupDoc = CKEDITOR.replace('GRP_DOCUMENT', {
					height: 260
				});
		    }
		}
		xh.send(params);
	}
};
function SaveGroup(TargetID,Mode,ListGTP) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 'For=TGP&T='+TargetID+'&M='+Mode+'&TID='+ListGTP; 
		params += "&ID=" + encodeURIComponent(document.getElementById("GRP_ID").value);
		params += "&ETID=" + encodeURIComponent(document.getElementById("GRP_GTP_ID").value);
		params += "&Name=" + encodeURIComponent(document.getElementById("GRP_NAME").value);
		params += "&Desc=" + encodeURIComponent(document.getElementById("GRP_DESC").value);
		params += "&Doc=" + encodeURIComponent(EdGroupDoc.getData());
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/groups/typed_groups.php", true);
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
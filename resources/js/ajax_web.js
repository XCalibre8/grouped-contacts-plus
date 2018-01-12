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
function SaveAccountEmail(TargetID,WACID) {
	//XCal - We won't create the XMLHttpRequest unless we get past params
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var params = 'T='+TargetID+'&M=M';
		params += '&ID='+WACID;
		params += '&Email='+encodeURIComponent(document.getElementById('ACE_EMAIL').value);
		var oc = tgt.className;
		LoadTarget(tgt);

		var xh = new XMLHttpRequest();
		xh.open('POST', 'resources/web/account_email.php', true);
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
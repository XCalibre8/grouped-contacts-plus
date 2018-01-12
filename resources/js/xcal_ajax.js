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
function AJAX(TargetID,pagestr,paramstr) {
	/*XCal - Below is just a debug line to uncomment if you're having issues with a call*/
	//alert('Target: '+TargetID+'; Page: '+pagestr+'; Params: '+paramstr);
    if ((TargetID.length == 0) || (pagestr.length == 0)) {
    	if (! TargetID == "") {
    		document.getElementById(TargetID).innerHTML = "";
    		return;
    	}
    	else return;
    } else {
    	var tgt = document.getElementById(TargetID);
    	if (tgt) {
    		var oc = tgt.className;
    		LoadTarget(tgt);
	        var xh = new XMLHttpRequest();        
	        xh.open("POST", pagestr, true);
	        
	        xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	        
	        xh.onreadystatechange = function() {
	            if (this.readyState == 4 && this.status == 200) {
	            	tgt.className = oc;
	                tgt.innerHTML = this.responseText;
	            }
	        }        
	        xh.send(paramstr);	        
    	}
    }
};
function BlankTarget(TargetID) {
	document.getElementById(TargetID).innerHTML = '';
};
function LoadTarget(tgt) {
	tgt.innerHTML = '';
	tgt.className = 'loader';
};
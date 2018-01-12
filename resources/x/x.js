/**
 * Copyright (c) 2016 Nicholas Hurd trading as XCalibre8 Unlimited
 *
 * This file is licenced under XCalibre8 Licence X as it stood on 13 Nov 2016
 * The licence may be subject to change and current and past versions can be checked at www.xcalibre8.me/licence.php
 * This file does not add or remove any clauses from the licence.
 * 
 * XCalibre8 Provider Name: XCalibre8 Unlimited
 * XCalibre8 Author Name: Nicholas Hurd
 * XCalibre8 Product Name: XCalibre8 PHP
 * 
 * CHANGELOG: Available at www.xcalibre8.me
 **/
function x(T,XC,Request) {	
	var xh = new XMLHttpRequest();	
	var params = 'X=' + encodeURIComponent(XC);
	if (! T == 'PAGE') {
		var XT = document.getElementById(T);
		if (! XT)
			var XD = document.getElementById('XD');
	}
	if (strlen(Request) > 0)
		params += '&' + Request; 
	xh.open('POST', "xc.php", true);
	xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xh.onreadystatechange = function() {
	    if(this.readyState == 4 && this.status == 200) {
	    	if (XT) {
	    		XT.innerHTML = this.responseText;
	    	}
	    	else if (XD) {
	    		XD.innerHTML = this.responseText;
	    	}
	    	else {
	    		document.getElementsByTagName('body')[0].innerHTML = this.responseText;
	    	}
	    }
	}
	xh.send(params);
};
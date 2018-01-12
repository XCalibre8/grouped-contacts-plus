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
function SavePersonEmbedded(TargetID,Mode,ExtraP) {
	//XCal - We won't create the XMLHttpRequest unless we get past params
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var params = 'For=PED&T='+TargetID+'&M='+Mode;
		params += '&ID='+document.getElementById('PED_PER_ID').value;
		params += '&TIT='+document.getElementById('PED_TIT_ID').value;
		params += '&FNames='+encodeURIComponent(document.getElementById('PED_FORENAMES').value);
		params += '&SName='+encodeURIComponent(document.getElementById('PED_SURNAME').value);
		params += '&DOB='+encodeURIComponent(document.getElementById('PED_DOB').value);
		if (ExtraP.length > 0) {
			params += '&'+ExtraP;
		}
		var oc = tgt.className;
		LoadTarget(tgt);

		var xh = new XMLHttpRequest();
		xh.open('POST', 'resources/contacts/person_embed.php', true);
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
function SearchPeople(TargetID) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = 
			"Type=" + document.getElementById("SearchType").value;
		params += "&Forenames=" + document.getElementById("SearchForenames").value;
		params += "&Surname=" + document.getElementById("SearchSurname").value;
		params += "&Records=" + document.getElementById("SearchRecords").value;
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open("POST", "resources/contacts/people_search.php", true);
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
function SavePerson(TargetID,Mode) {
	var tgt = document.getElementById(TargetID);
	if (tgt) {
		var xh = new XMLHttpRequest();
		var params = "For=PRM&T="+TargetID+"&M="+Mode;
			
		params = params + 
			"&ID=" + document.getElementById("PER_ID").value +
			"&AdrID=" + document.getElementById("PER_ADR_ID").value +
			"&CnpID=" + document.getElementById("PER_CNP_ID").value +
			"&TIT=" + document.getElementById("PER_TIT_ID").value +
			"&FNames=" + encodeURIComponent(document.getElementById("PER_FORENAMES").value) +
			"&SName=" + encodeURIComponent(document.getElementById("PER_SURNAME").value) +
			"&DOB=" + encodeURIComponent(document.getElementById('PER_DOB').value) + 
			"&AdrCTT=" + document.getElementById("AED_CTT_ID").value +
			"&CO=" + encodeURIComponent(document.getElementById("AED_CARE_OF").value) +
			"&Line1=" + encodeURIComponent(document.getElementById("AED_LINE1").value) +
			"&Line2=" + encodeURIComponent(document.getElementById("AED_LINE2").value) +
			"&Town=" + encodeURIComponent(document.getElementById("AED_TOWN").value) +
			"&County=" + encodeURIComponent(document.getElementById("AED_COUNTY").value) +
			"&CntryID=" + document.getElementById("AED_CNTRY_ID").value +
			"&PCode=" + encodeURIComponent(document.getElementById("AED_POSTCODE").value) +
			"&CnpCTT=" + document.getElementById("CED_CTT_ID").value +
			"&ST=" + encodeURIComponent(document.getElementById("CED_SPEAK_TO").value) +
			"&CnpCPT=" + document.getElementById("CED_CPT_ID").value +
			"&Con=" + encodeURIComponent(document.getElementById("CED_CONTACT").value);
		var oc = tgt.className;
		LoadTarget(tgt);
		
		xh.open('POST', "resources/contacts/person_mod.php", true);
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
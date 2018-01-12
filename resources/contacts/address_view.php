<?php
require(dirname(__FILE__).'/../gen_session.php');
//XCal - We only want to pay attention to request variables if they're for the person view page	
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'ADV')) {
	$For = 'ADV';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$ADVTarget = $_REQUEST['T'];

	if (isset($_REQUEST['ID']))
		$ADVID = $_REQUEST['ID'];
	else $ADVID = -1;
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to PRV then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'ADV')) {
		if (isset($Target))
			$ADVTarget = $Target;
		if (isset($ID))
			$ADVID = $ID;
	}
}
$ADVPath = 'resources/contacts/person_view.php';
$ADVDieError = '';
$ADVFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($ADVID))
	$ADVDieError .= 'Address view has not been passed an address to display.<br />';
if (! isset($ADVTarget))
	$ADVDieError .= 'Address view has not been told where to target its displays and thus would not be safe to run.<br />';

if ($ADVDieError == '') {
	if (! function_exists('ValidateToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql,1);
	
		if ($CONSec['SearchView'] > 0) {
			if ($ConSec['SearchView'] == 2)
				$CanView = true;
			else $CanView = ItemLinked($sql, 2, $ADVID);
			
			if ($CanView) {
				$stm = $sql->prepare(
						'SELECT ADR_LINE1,ADR_LINE2,ADR_POST_TOWN,ADR_COUNTY,CNTRY_NAME,ADR_POSTCODE,ADR_UPDATED '.
						'FROM con_addresses '.
						'LEFT OUTER JOIN con_countries ON CNTRY_ID = ADR_CNTRY_ID '.
						'WHERE (ADR_ID = ?)');
				if ($stm) {
					$stm->bind_param('i',$ADVID);
					//$_SESSION['DebugStr'] .= "Address View: Querying statement with ID $ADVID<br />";
					if ($stm->execute()) {
						$stm->bind_result($ADRLine1,$ADRLine2,$ADRTown,$ADRCounty,$ADRCountry,$ADRPostcode,$ADRUpdated);
						$stm->fetch();
						$stm->free_result();
					}
					else $ADVFailError .= 'Address view failed attempting to retrieve address details for viewing. <br />';
				}
				else $ADVFailError .= 'Address view encountered an error preparing to get address details to view ('.$sql->error.')<br />';
					
				//XCal - Build the address view screen		
				echo (
					'<h5>View Address Information</h5><br />');
				echo('<table>'.
					'<tr><td class="fieldname">Line 1</td>'."<td>$ADRLine1</td></tr>".
					'<tr><td class="fieldname">Line 2</td>'."<td>$ADRLine2</td></tr>".
					'<tr><td class="fieldname">Town</td>'."<td>$ADRTown</td></tr>".
					'<tr><td class="fieldname">County</td>'."<td>$ADRCounty</td></tr>".
					'<tr><td class="fieldname">Country</td>'."<td>$ADRCountry</td></tr>".
					'<tr><td class="fieldname">Postcode</td>'."<td>$ADRPostcode</td></tr>".
					'</table><br />');
				if ($ADRUpdated > 0)
					echo("Person Updated: $ADRUpdated<br /><br />");
			
				$LTP = 2;
				$ID = $ADVID;
				require(dirname(__FILE__).'/../reqful/reqful_both.php');
				
				echo('<h4>Address Linked by...</h4>'.
						'<div id="addresslinked">');
				$For = 'ALD';
				$CLT = 2;
				$Target = 'addresslinked';
				require(dirname(__FILE__).'/../linking/all_linked.php');
				echo('</div>');
				
				if (strlen($ADVFailError) > 0)
					echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$ADVFailError");
			}
			else echo('You only have the right to view contacts your account is linked by, which does not include this address.');
		}
		else echo('You do not have the required access rights to view address details.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Address View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$ADVDieError.
		'</article>');
}
?>
<?php
//XCal - We only want to pay attention to request variables if they're for the person view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'CNV')) {
	$For = 'CNV';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$CNVTarget = $_REQUEST['T'];

	if (isset($_REQUEST['ID']))
		$CNVID = $_REQUEST['ID'];
	else $CNVID = -1;
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to PRV then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'CNV')) {
		if (isset($Target))
			$CNVTarget = $Target;
		if (isset($ID))
			$CNVID = $ID;
	}
}
$CNVPath = 'resources/contacts/contactpoint_view.php';
$CNVDieError = '';
$CNVFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($CNVID))
	$CNVDieError .= 'Contactpoint view has not been passed a contact point to display.<br />';
if (! isset($CNVTarget))
	$CNVDieError .= 'Contactpoint view has not been told where to target its displays and thus would not be safe to run.<br />';

if ($CNVDieError == '') {
	require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql, 1);
		if ($CONSec['SearchView'] == 2)
			$CanView = true;
		else $CanView = ItemLinked($sql,3,$CNVID);
		if ($CanView) {
			$stm = $sql->prepare(
					'SELECT CPT_NAME,CNP_CONTACT,CNP_UPDATED '.
					'FROM con_contact_points '.
					'LEFT OUTER JOIN con_contact_point_types ON CPT_ID = CNP_CPT_ID '.
					'WHERE (CNP_ID = ?)');
			if ($stm) {
				$stm->bind_param('i',$CNVID);
				//$_SESSION['DebugStr'] .= "Contactpoint View: Querying statement with ID $CNVID<br />";
				if ($stm->execute()) {
					$stm->bind_result($CNPMethod,$CNPContact,$CNPUpdated);
					$stm->fetch();
					$stm->free_result();
				}
				else $CNVFailError .= 'Contactpoint view failed attempting to retrieve contact point details for viewing. <br />';
			}
			else $CNVFailError .= 'Contactpoint view encountered an error preparing to get contact point details to view ('.$sql->error.')<br />';
		
			//XCal - Build the contact point view screen
			echo (
					'<h5>View Contact Point Information</h5><br />');
			echo('<table>'.
					'<tr><td class="fieldname">Method</td>'."<td>$CNPMethod</td></tr>".
					'<tr><td class="fieldname">Contact Point</td>'."<td>$CNPContact</td></tr>".
					'</table><br />');
			if ($CNPUpdated > 0)
				echo("Contact Point Updated: $CNPUpdated<br /><br />");
		
			$LTP = 3;
			$ID = $CNVID;
			require(dirname(__FILE__).'/../reqful/reqful_both.php');
			
			echo('<h4>Contact Point Linked by...</h4>'.
					'<div id="contactpointlinked">');
			$For = 'ALD';
			$CLT = 3;
			$Target = 'contactpointlinked';
			require(dirname(__FILE__).'/../linking/all_linked.php');
			echo('</div>');
		
			if (strlen($CNVFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$CNVFailError");
		}
		elseif ($CONSec['SearchView'] == 1)
			echo('You only have the right to view contacts your account is linked by, which does not include this contact point.');
		else echo('You do not have the required access rights to view contacts.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Contactpoint View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$CNVDieError.
			'</article>');
}
?>
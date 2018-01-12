<?php
if (isset($PRVFrom))
	unset($PRVFrom);
//XCal - We only want to pay attention to request variables if they're for the person view page	
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PRV')) {
	$For = 'PRV';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$PRVTarget = $_REQUEST['T'];

	if (isset($_REQUEST['ID']))
		$PRVID = $_REQUEST['ID'];
	else $PRVID = -1;
	if (isset($_REQUEST['From']))
		$PRVFrom = $From;
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to PRV then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'PRV')) {
		if (isset($Target))
			$PRVTarget = $Target;
		if (isset($ID))
			$PRVID = $ID;
		if (isset($From))
			$PRVFrom = $From;
	}
}
$PRVPath = 'resources/contacts/person_view.php';
$PRVDieError = '';
$PRVFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($PRVID))
	$PRVDieError .= 'Person view has not been passed a person to display.<br />';
if (! isset($PRVTarget))
	$PRVDieError .= 'Person view has not been told where to target its displays and thus would not be safe to run.<br />';

if ($PRVDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql,1);
		if ($CONSec['SearchView'] > 0) {
			if ($CONSec['SearchView'] == 2)
				$CanView = true;
			else $CanView = ItemLinked($sql,1,$PRVID);
			if ($CanView) {
				$stm = $sql->prepare(
						'SELECT fn_con_fullname(PER_ID),PER_DOB,PER_UPDATED,'.
						'PER_ADR_ID,fn_con_sep_link_address(1,PER_ID,PER_ADR_ID,\'<br />\'),ADR_UPDATED,'.
						'PER_CNP_ID,fn_con_full_link_contact_point(1,PER_ID,PER_CNP_ID),CNP_UPDATED '.
						'FROM con_people '.
						'LEFT OUTER JOIN con_addresses ON ADR_ID = PER_ADR_ID '.
						'LEFT OUTER JOIN con_contact_points ON CNP_ID = PER_CNP_ID '.
						'WHERE (PER_SR = 0) AND (PER_ID = ?)');
				if ($stm) {
					$stm->bind_param('i',$PRVID);
					//$_SESSION['DebugStr'] .= "Person View: Querying statement with ID $PRVID<br />";
					if ($stm->execute()) {
						$stm->bind_result($PRVPerFullName,$PRVBirthday,$PRVPerUpdated,$PRVAdrID,$PRVSepAdr,$PRVAdrUpdated,$PRVCnpID,
								$PRVFullConPoint,$PRVCnpUpdated);
						$stm->fetch();
						$stm->free_result();
					}
					else $PRVFailError .= 'Person view failed attempting to retrieve person details for viewing. <br />';
				}
				else $PRVFailError .= 'Person view encountered an error preparing to get person details to view ('.$sql->error.')<br />';
							
				//XCal - Build the person view screen		
				echo (
					'<h5>View Person Information</h5><br />');
				if ($CONSec['NewMod'] == 2)
					$CanEdit = true;
				elseif ($CONSec['NewMod'] == 1)
					$CanEdit = ItemLinked($sql,1,$PRVID);
				else $CanEdit = false;
				if ($CanEdit or (isset($_SESSION['WACPERID']) and ($PRVID == $_SESSION['WACPERID'])))
					echo('<input type="button" class="button" value="Edit Person" onclick="'.
						"AJAX('$PRVTarget','resources/contacts/person_mod.php','For=PRM&T=$PRVTarget&M=M&ID=$PRVID');".
						'" />');
				echo('<table><tr>'.
					'<th>Full Name</th><th>Date of Birth</th></tr><tr>'.
					"<td>$PRVPerFullName</td><td>".date('l jS F Y',strtotime($PRVBirthday))."</td></tr></table><br />");
				if ($PRVPerUpdated > 0)
					echo("Person Updated: $PRVPerUpdated<br /><br />");
				
				echo ('<table><tr><th>Primary Address</th><th>Primary Contact</th></tr>');
				echo('<tr><td><div id="perprimaryadr">');
			
				if ($PRVAdrID > 0) 
					echo("$PRVSepAdr<br /><br />Address Updated: $PRVAdrUpdated");
				else echo("<em>No Primary Address Present</em>");
				echo('</div></td><td><div id="perprimarycnp">');
			
				if ($PRVCnpID > 0) 
					echo("$PRVFullConPoint<br /><br />Contact Point Updated: $PRVCnpUpdated");
				else echo("<em>No Primary Contact Point Present</em>");
				echo('</div></td></tr></table>');
			
				$LTP = 1;
				$ID = $PRVID;
				require(dirname(__FILE__).'/../reqful/reqful_both.php');
				
				echo('<h4>Person Links...</h4>'.
						'<div id="personlinks">');
				$For = 'ALN';
				$OLT = 1;
				$ID = $PRVID;	
				$ExADR = $PRVAdrID;
				$ExCNP = $PRVCnpID;
				$Target = 'personlinks';
				require(dirname(__FILE__).'/../linking/all_links.php');
				echo('</div>');
				
				echo('<h4>Person Linked by...</h4>'.
						'<div id="personlinked">');
				$For = 'ALD';
				$CLT = 1;
				$ID = $PRVID;
				$Target = 'personlinked';
				require(dirname(__FILE__).'/../linking/all_linked.php');
				echo('</div>');
				
				if (strlen($PRVFailError) > 0)
					echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$PRVFailError");
			}
			else echo('You only have the right to view contacts your account is linked by, which does not include this person.');
		}
		else echo('You do not have the required access rights to view contacts.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Person View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$PRVDieError.
		'</article>');
}
?>
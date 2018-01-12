<?php
//XCal - We only want to pay attention to request variables if they're for the group types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'LTP')) {
	$For = 'LTP';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$LTPTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},C|List(Default),Configure(Changes Select Target);;]
	if (isset($_REQUEST['M']))
		$LTPMode = $_REQUEST['M'];
	else $LTPMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$LTPSelTarget = $_REQUEST['ST'];
	else $LTPSelTarget = 'LTPSubDiv';
	
	if (isset($_REQUEST['ID']))
		$LTPID = $_REQUEST['ID'];
	else $LTPID = -1;
	if (isset($_REQUEST['Desc']))
		$LTPDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
	}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to LTP then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'LTP')) {
		if (isset($Target))
			$LTPTarget = $Target;
		if (isset($Mode))
			$LTPMode = $Mode;
		elseif (! isset($LTPMode))
			$LTPMode = 'L';
		if (isset($SelTarget))
			$LTPSelTarget = $SelTarget;
		else $LTPSelTarget = 'LTPSubDiv';
		
		if (isset($ID))
			$LTPID = $ID;
		elseif (! isset($LTPID))
		$LTPID = -1;
		if (! isset($Offset))
			$Offset = 0;
		if (! isset($Records))
			$Records = 5;
	}
	else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
		if (! isset($LTPMode))
			$LTPMode = 'L';
		$LTPID = -1;
		if (! isset($LTPSelTarget))
			$LTPSelTarget = 'LTPSubDiv';
		unset($LTPDesc);
		$Offset = 0;
		$Records = 5;
	}
}
$LTPPath = 'resources/linking/link_types.php';
$LTPDieError = '';
$LTPFailError = '';
$LTPModes = array('L','C');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($LTPTarget))
	$LTPDieError .= 'Link types has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($LTPMode,$LTPModes))
	$LTPDieError .= "Link types has been passed a mode ($LTPMode) which is does not support.<br />";

if ($LTPDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$LNKSec = GetAreaSecurity($sql, 7);
		if ($LNKSec['Config']) {
			$res = $sql->query('SELECT COUNT(LTP_ID) FROM lnk_link_types');
			if (! $res) {
				$LTPFailError .= 'Link types failed to get a count of types ('.$sql->error.').<br />';
				$Count = 0;
			}
			else {
				$row = $res->fetch_array();
				$Count = $row[0];
			}
			$LowRec = $Offset+1;
			if ($Count < ($Offset+$Records))
				$HighRec = $Count;
			else $HighRec = $Offset+$Records;
			echo("Showing link types $LowRec to $HighRec of $Count.<br />");
					
			echo('<table><tr><th>Select</th><th>Type</th><th>Updated</th></tr>');
			$stm = $sql->prepare(
				'SELECT LTP_ID,LTP_NAME,LTP_UPDATED '.
				'FROM lnk_link_types '.
				'LIMIT ?,?');
			if ($stm) {
				$stm->bind_param('ii',$Offset,$Records);
				$stm->execute();
				$stm->bind_result($LTPID,$LTPName,$LTPUpdated);
				while ($stm->fetch()) {
					echo('<tr>'.
							'<td><input type="button" class="button" value="Select" onclick="');
					//XCal - The select button needs to bring up a different page if we're configuring to not
					if ($LTPMode == 'C')
						echo("AJAX('$LTPSelTarget','resources/linking/link_type_avail.php','For=LTA&T=$LTPSelTarget&ID=$LTPID');");
					else echo("AJAX('$LTPSelTarget','resources/linking/typed_links.php','For=LNK&T=$LTPSelTarget&TID=$LTPID');");
					echo(	'" /></td>'.
							"<td>$LTPName</td><td>$LTPUpdated</td>".
						'</tr>');
				}	
			}
			else $LTPFailError .= 'Link types failed preparing to get a list of types to display ('.$sql->error.').<br />';
			
			echo('</table>');
			
			if ($Offset > 0) {
				$PrevOffset = $Offset-$Records;
				echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
					"AJAX('$LTPTarget','$LTPPath','For=LTP&T=$LTPTarget&M=$LTPMode&RO=$PrevOffset&RC=$Records');".
					'" />');
			}
			
			if (($Offset + $Records) < $Count) {
				$NextOffset = $Offset+$Records;
				echo('<input class="nextbutton" type="button" value="Next" onclick="'.
					"AJAX('$LTPTarget','$LTPPath','For=LTP&T=$LTPTarget&M=$LTPMode&RO=$NextOffset&RC=$Records');".
					'" />');
			}			
			echo('<br /><div id="LTPSubDiv"></div>');
			if (strlen($LTPFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$LTPFailError");
		}
		else echo('You do not have the required access rights to configure the linking system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Link Types Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$LTPDieError.
		'</article>');
}
?>
<?php
//XCal - We only want to pay attention to request variables if they're for the group types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'LTA')) {
	$For = 'LTA';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$LTATarget = $_REQUEST['T'];
	
	if (isset($_REQUEST['ID']))
		$LTPID = $_REQUEST['ID'];
	else $LTPID = -1;
	if (isset($_REQUEST['A']))
		$AddLTP = $_REQUEST['A'];
	if (isset($_REQUEST['D']))
		$DelLTP = $_REQUEST['D'];

	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to CTS then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'LTA')) {
		if (isset($Target))
			$LTATarget = $Target;
			
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
		$LTPID = -1;
		$Offset = 0;
		$Records = 5;
	}
}
$LTAPath = 'resources/linking/link_type_avail.php';
$LTADieError = '';
$LTAFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($LTATarget))
	$LTADieError .= 'Link type availability has not been told where to target its displays and thus would not be safe to run.<br />';
if ($LTPID == -1)
	$LTADieError .= "Link type availability has not been told what link type to work with.<br />";

if ($LTADieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$LNKSec = GetAreaSecurity($sql, 7);
		if ($LNKSec['Config']) {
			//XCal - If a link type removal has been requested then remove it
			if (isset($DelLTP)) {
				$stm = $sql->prepare(
					'DELETE FROM lnk_link_type_avail '.
					'WHERE LTA_ONR_LTP_ID = ? AND LTA_CHD_LTP_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$LTPID,$DelLTP);
					if ($stm->execute())					
						$stm->free_result();
					else echo('Remove failed, message: '.$stm->error);
				}
				else $LTAFailError .= 'Link type availability encountered an error preparing to remove the link type.<br />';
			}
			//XCal - If a link type is being added then add it
			if (isset($AddLTP)) {
				$stm = $sql->prepare(
					'INSERT INTO lnk_link_type_avail (LTA_ONR_LTP_ID,LTA_CHD_LTP_ID,LTA_DESCRIPTION,LTA_UPDATED) '.
					'VALUES (?,?,'.
						"(SELECT CONCAT('Allows ',l1.LTP_NAME,' to link to ',l2.LTP_NAME) ".
						'FROM lnk_link_types l1, lnk_link_types l2 '.
						'WHERE l1.LTP_ID = ? AND l2.LTP_ID = ?)'.
						',current_timestamp())');
				if ($stm) {
					$stm->bind_param('iiii',$LTPID,$AddLTP,$LTPID,$AddLTP);
					if ($stm->execute())
						$stm->free_result();
					else echo('Add failed, message: '.$stm->error);
				}
				else $LTAFailError .= 'Link type availability encountered an error preparing to add the link type.<br />'; 
			}
			
			//XCal - Get the name and last updated date of the group type
			$s_gtype = $sql->prepare('SELECT LTP_NAME, LTP_UPDATED FROM lnk_link_types WHERE LTP_ID = ?');
			if ($s_gtype) {
				$s_gtype->bind_param('i',$LTPID);
				if ($s_gtype->execute()) {
					$s_gtype->bind_result($LTPName,$LTPUpdated);
					$s_gtype->fetch();
					$s_gtype->free_result();
				}
				else $LTAFailError .= 'Link link availability encountered an error getting link type details.<br />';
			}
			else $LTAFailError .= 'Link type availability encountered an error preparing to get link type details.<br />';
			
			echo('<article>'.
					"<h3>$LTPName Configuration</h3> Link type last updated $LTPUpdated".
					'<p>You must specify what system cores can link to in order to make links available from that system core.</p>'.
					'<table>'.
						'<tr><th>Allowed Link Types</th><th>Possible Link Types</th></tr>'.
						'<tr><td>'.
							'<table>');
		
			//XCal - Echo the assigned types, or "All links available" here
			$Count = 0;
			$s_linked = $sql->prepare(
					'SELECT LTA_CHD_LTP_ID,LTP_NAME,LTA_DESCRIPTION '.
					'FROM lnk_link_type_avail '.
					'JOIN lnk_link_types ON LTP_ID = LTA_CHD_LTP_ID '.
					'WHERE LTA_ONR_LTP_ID = ?');
			if ($s_linked) {
				$s_linked->bind_param('i',$LTPID);
				$s_linked->execute();
				$s_linked->bind_result($CHDLTPID,$LTPName,$LTPDesc);
				while ($s_linked->fetch()) {
					$Count++;
					echo(			"<tr><td>$LTPName</td><td>$LTPDesc</td>".
									'<td><input type="button" class="button" value="Remove >>" onclick="'.
									"AJAX('$LTATarget','$LTAPath','For=LTA&T=$LTATarget&ID=$LTPID&D=$CHDLTPID');".
									'" /></td></tr>');
				}
				$s_linked->free_result();
			}		
			if ($Count == 0)
				echo(			'<tr><td><i>No links allowed.</ti></td></tr>');
			
			echo(			'</table></td>'.
							'<td><table>');
		
			//XCal - Echo the unassigned types here
			$Count = 0;
			$s_avail = $sql->prepare(
					'SELECT LTP_ID,LTP_NAME '.
					'FROM lnk_link_types '.
					'WHERE  NOT EXISTS (SELECT LTA_CHD_LTP_ID '.
						'FROM lnk_link_type_avail '.
						'WHERE LTA_CHD_LTP_ID = LTP_ID AND LTA_ONR_LTP_ID = ?)');
			if ($s_avail) {
				//$_SESSION['DebugStr'] .= 'Link Type Link Availability: Available Types Prepared OK.<br />';
				$s_avail->bind_param('i',$LTPID);
				if (! $s_avail->execute())
					echo('Error fetching available link types: '.$s_avail->error);
				$s_avail->bind_result($CHDLTPID,$LTPName);
				while ($s_avail->fetch()) {
					$Count++;
					echo(			'<tr>'.
									'<td><input type="button" class="button" value="<< Add" onclick="'.
									"AJAX('$LTATarget','$LTAPath','For=LTA&T=$LTATarget&ID=$LTPID&A=$CHDLTPID');".
									'" /></td>'.
									"<td>$LTPName</td></tr>");
				}
				$s_avail->free_result();		
			}
			else {
				//$_SESSION['DebugStr'] .= 'Link Type Link Availability: Available Types failed to Prepare ('.$sql->error.').<br />';
				$LTAFailError .= 'Link type availability failed preparing to get available link types ('.$sql->error.').<br />';
			}		
			if ($Count == 0)
				echo(			'<tr><td><i>All links specifically allowed.</ti></td></tr>');
			
			echo(			'</table>'.
						'</td></tr>'.
					'</table>'.
				'</article>');
			
			if (strlen($LTAFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$LTAFailError");
		}
		else echo('You do not have the required access right to configure the linking system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Link Type Availability Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$LTADieError.
		'</article>');
}			
?>
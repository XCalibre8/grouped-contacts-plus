<?php
//XCal - We only want to pay attention to the right request variables
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'GTA')) {
	$For = 'GTA';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$GTATarget = $_REQUEST['T'];
	
	if (isset($_REQUEST['ID']))
		$GTPID = $_REQUEST['ID'];
	else $GTPID = -1;
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
elseif (isset($For) and ($For == 'GTA')) {
	if (isset($Target))
		$GTATarget = $Target;
		
	if (isset($ID))
		$GTPID = $ID;
	elseif (! isset($GTPID))
	$GTPID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	$GTPID = -1;
	$Offset = 0;
	$Records = 5;
}

$GTAPath = 'resources/groups/group_type_link_avail.php';
$GTADieError = '';
$GTAFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($GTATarget))
	$GTADieError .= 'Group type link availability has not been told where to target its displays and thus would not be safe to run.<br />';
if ($GTPID == -1)
	$GTADieError .= "Group type link availability has not been told what group type to work with.<br />";

if ($GTADieError == '') {	
	require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$GRPSec = GetAreaSecurity($sql, 2);
		if ($GRPSec['Config']) {
			//XCal - If a link type removal has been requested then remove it
			if (isset($DelLTP)) {
				$stm = $sql->prepare(
					'DELETE FROM grp_group_type_avail_links '.
					'WHERE GTA_GTP_ID = ? AND GTA_LTP_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$GTPID,$DelLTP);
					if ($stm->execute())					
						$stm->free_result();
					else echo('Remove failed, message: '.$stm->error);
				}
				else $GTAFailError .= 'Group type link availability encountered an error preparing to remove the link type.<br />';
			}
			//XCal - If a link type is being added then add it
			if (isset($AddLTP)) {
				$stm = $sql->prepare(
					'INSERT INTO grp_group_type_avail_links (GTA_GTP_ID,GTA_LTP_ID,GTA_UPDATED) '.
					'VALUES (?,?,current_timestamp())');
				if ($stm) {
					$stm->bind_param('ii',$GTPID,$AddLTP);
					if ($stm->execute())
						$stm->free_result();
					else echo('Add failed, message: '.$stm->error);
				}
				else $GTAFailError .= 'Group type link availability encountered an error preparing to add the link type.<br />'; 
			}
			
			//XCal - Get the name and last updated date of the group type
			$s_gtype = $sql->prepare('SELECT GTP_NAME, GTP_UPDATED FROM grp_group_types WHERE GTP_ID = ?');
			if ($s_gtype) {
				$s_gtype->bind_param('i',$GTPID);
				if ($s_gtype->execute()) {
					$s_gtype->bind_result($GTPName,$GTPUpdated);
					$s_gtype->fetch();
					$s_gtype->free_result();
				}
				else $GTAFailError .= 'Group type link availability encountered an error getting group type details.<br />';
			}
			else $GTAFailError .= 'Group type link availability encountered an error preparing to get group type details.<br />';
			
			echo('<article>'.
					"<h3>\"$GTPName\" Configuration</h3> Group type last updated $GTPUpdated".
					'<p>You can restrict what groups can link to by their type. If no types are specified all types will be available.</p>'.
					'<table>'.
						'<tr><th>Specified Link Types</th><th>Possible Link Types</th></tr>'.
						'<tr><td>'.
							'<table>');
		
			//XCal - Echo the assigned types, or "All links available" here
			$Count = 0;
			$s_linked = $sql->prepare(
					'SELECT GTA_LTP_ID,LTP_NAME '.
					'FROM grp_group_type_avail_links '.
					'JOIN lnk_link_types ON LTP_ID = GTA_LTP_ID '.
					'WHERE GTA_GTP_ID = ?');
			if ($s_linked) {
				$s_linked->bind_param('i',$GTPID);
				$s_linked->execute();
				$s_linked->bind_result($LTPID,$LTPName);
				while ($s_linked->fetch()) {
					$Count++;
					echo(			"<tr><td>$LTPName</td>".
									'<td><input type="button" class="button" value="Remove >>" onclick="'.
									"AJAX('$GTATarget','$GTAPath','For=GTA&T=$GTATarget&ID=$GTPID&D=$LTPID');".
									'" /></td></tr>');
				}
				$s_linked->free_result();
			}		
			if ($Count == 0)
				echo(			'<tr><td><i>All links available.</i></td></tr>');
			
			echo(			'</table></td>'.
							'<td><table>');
		
			//XCal - Echo the unassigned types here
			$Count = 0;
			$s_avail = $sql->prepare(
					'SELECT LTP_ID,LTP_NAME '.
					'FROM lnk_link_type_avail '.
					'JOIN lnk_link_types ON LTP_ID = LTA_CHD_LTP_ID '.
					'WHERE LTA_ONR_LTP_ID = 4 '.
					'AND NOT EXISTS (SELECT GTA_LTP_ID '.
						'FROM grp_group_type_avail_links '.
						'WHERE GTA_LTP_ID = LTP_ID AND GTA_GTP_ID = ?)');
			if ($s_avail) {
				//$_SESSION['DebugStr'] .= 'Group Type Link Availability: Available Types Prepared OK.<br />';
				$s_avail->bind_param('i',$GTPID);
				if (! $s_avail->execute())
					echo('Error fetching available link types: '.$s_avail->error);
				$s_avail->bind_result($LTPID,$LTPName);
				while ($s_avail->fetch()) {
					$Count++;
					echo(			'<tr>'.
									'<td><input type="button" class="button" value="<< Add" onclick="'.
									"AJAX('$GTATarget','$GTAPath','For=GTA&T=$GTATarget&ID=$GTPID&A=$LTPID');".
									'" /></td>'.
									"<td>$LTPName</td></tr>");
				}
				$s_avail->free_result();		
			}
			else {
				//$_SESSION['DebugStr'] .= 'Group Type Link Availability: Available Types failed to Prepare ('.$sql->error.').<br />';
				$GTAFailError .= 'Group type link availability failed preparing to get available link types ('.$sql->error.').<br />';
			}		
			if ($Count == 0)
				echo(			'<tr><td><i>All links specifically allowed.</i></td></tr>');
			
			echo(			'</table>'.
						'</td></tr>'.
					'</table>'.
				'</article>');
			//$_SESSION['DebugStr'] .= "Group Type Link Availability: Failure messages below.<br />$GTAFailError";
			if (strlen($GTAFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$GTAFailError");
		}
		else echo('You do not have the required access rights to configure the group system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Group Type Link Availability Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$GTADieError.
		'</article>');
}			
?>
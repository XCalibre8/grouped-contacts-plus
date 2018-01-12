<?php
//XCal - We only want to pay attention to the right request variables
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'STA')) {
	$For = 'STA';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$STATarget = $_REQUEST['T'];
	
	if (isset($_REQUEST['ID']))
		$STPID = $_REQUEST['ID'];
	else $STPID = -1;
	if (isset($_REQUEST['A']))
		$AddSTP = $_REQUEST['A'];
	if (isset($_REQUEST['D']))
		$DelSTP = $_REQUEST['D'];

	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
}
elseif (isset($For) and ($For == 'STA')) {
	if (isset($Target))
		$STATarget = $Target;
		
	if (isset($ID))
		$STPID = $ID;
	elseif (! isset($STPID))
	$STPID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	$STPID = -1;
	$Offset = 0;
	$Records = 5;
}

$STAPath = 'resources/sched/sched_type_link_avail.php';
$STADieError = '';
$STAFailError = '';

//XCal - Let's make sure we have the values we need to run the page
if (! isset($STATarget))
	$STADieError .= 'Schedule type link availability has not been told where to target its displays and thus would not be safe to run.<br />';
if ($STPID == -1)
	$STADieError .= "Schedule type link availability has not been told what schedule type to work with.<br />";

if ($STADieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$SCHSec = GetAreaSecurity($sql, 5);
		if ($SCHSec['Config']) {
			//XCal - If a link type removal has been requested then remove it
			if (isset($DelSTP)) {
				$stm = $sql->prepare(
					'DELETE FROM sch_schedule_type_avail_links '.
					'WHERE SCA_SCT_ID = ? AND SCA_LTP_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$STPID,$DelSTP);
					if ($stm->execute())					
						$stm->free_result();
					else echo('Remove failed, message: '.$stm->error);
				}
				else $STAFailError .= 'Schedule type link availability encountered an error preparing to remove the link type.<br />';
			}
			//XCal - If a link type is being added then add it
			if (isset($AddSTP)) {
				$stm = $sql->prepare(
					'INSERT INTO sch_schedule_type_avail_links (SCA_SCT_ID,SCA_LTP_ID,SCA_UPDATED) '.
					'VALUES (?,?,current_timestamp())');
				if ($stm) {
					$stm->bind_param('ii',$STPID,$AddSTP);
					if ($stm->execute())
						$stm->free_result();
					else echo('Add failed, message: '.$stm->error);
				}
				else $STAFailError .= 'Schedule type link availability encountered an error preparing to add the link type.<br />'; 
			}
			
			//XCal - Get the name and last updated date of the schedule type
			$s_type = $sql->prepare('SELECT SCT_NAME, SCT_UPDATED FROM sch_schedule_types WHERE SCT_ID = ?');
			if ($s_type) {
				$s_type->bind_param('i',$STPID);
				if ($s_type->execute()) {
					$s_type->bind_result($SCTName,$SCTUpdated);
					$s_type->fetch();
					$s_type->free_result();
				}
				else $STAFailError .= 'Schedule type link availability encountered an error getting schedule type details.<br />';
			}
			else $STAFailError .= 'Schedule type link availability encountered an error preparing to get schedule type details.<br />';
			
			echo('<article>'.
					"<h3>\"$SCTName\" Configuration</h3> Schedule type last updated $SCTUpdated".
					'<p>You can restrict what schedules can link to by their type. If no types are specified all types will be available.</p>'.
					'<table>'.
						'<tr><th>Specified Link Types</th><th>Possible Link Types</th></tr>'.
						'<tr><td>'.
							'<table>');
		
			//XCal - Echo the assigned types, or "All links available" here
			$Count = 0;
			$s_linked = $sql->prepare(
					'SELECT SCA_LTP_ID,LTP_NAME '.
					'FROM sch_schedule_type_avail_links '.
					'JOIN lnk_link_types ON LTP_ID = SCA_LTP_ID '.
					'WHERE SCA_SCT_ID = ?');
			if ($s_linked) {
				$s_linked->bind_param('i',$STPID);
				$s_linked->execute();
				$s_linked->bind_result($LTPID,$LTPName);
				while ($s_linked->fetch()) {
					$Count++;
					echo(			"<tr><td>$LTPName</td>".
									'<td><input type="button" class="button" value="Remove >>" onclick="'.
									"AJAX('$STATarget','$STAPath','For=STA&T=$STATarget&ID=$STPID&D=$LTPID');".
									'" /></td></tr>');
				}
				$s_linked->free_result();
			}		
			if ($Count == 0)
				echo(			'<tr><td><i>All links available.</ti></td></tr>');
			
			echo(			'</table></td>'.
							'<td><table>');
		
			//XCal - Echo the unassigned types here
			$Count = 0;
			$s_avail = $sql->prepare(
					'SELECT LTP_ID,LTP_NAME '.
					'FROM lnk_link_type_avail '.
					'JOIN lnk_link_types ON LTP_ID = LTA_CHD_LTP_ID '.
					'WHERE LTA_ONR_LTP_ID = 8 '.
					'AND NOT EXISTS (SELECT SCA_LTP_ID '.
						'FROM sch_schedule_type_avail_links '.
						'WHERE SCA_LTP_ID = LTP_ID AND SCA_SCT_ID = ?)');
			if ($s_avail) {
				//$_SESSION['DebugStr'] .= 'Schedule Type Link Availability: Available Types Prepared OK.<br />';
				$s_avail->bind_param('i',$STPID);
				if (! $s_avail->execute())
					echo('Error fetching available link types: '.$s_avail->error);
				$s_avail->bind_result($LTPID,$LTPName);
				while ($s_avail->fetch()) {
					$Count++;
					echo(			'<tr>'.
									'<td><input type="button" class="button" value="<< Add" onclick="'.
									"AJAX('$STATarget','$STAPath','For=STA&T=$STATarget&ID=$STPID&A=$LTPID');".
									'" /></td>'.
									"<td>$LTPName</td></tr>");
				}
				$s_avail->free_result();		
			}
			else {
				//$_SESSION['DebugStr'] .= 'Schedule Type Link Availability: Available Types failed to Prepare ('.$sql->error.').<br />';
				$STAFailError .= 'Schedule type link availability failed preparing to get available link types ('.$sql->error.').<br />';
			}		
			if ($Count == 0)
				echo(			'<tr><td><i>All links specifically allowed.</ti></td></tr>');
			
			echo(			'</table>'.
						'</td></tr>'.
					'</table>'.
				'</article>');
			
			if (strlen($STAFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$STAFailError");
		}
		else echo('You do not have the required access right to configure the scheduling system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Schedule Type Link Availability Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$STADieError.
		'</article>');
}			
?>
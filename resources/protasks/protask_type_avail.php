<?php
//XCal - We only want to pay attention to request variables if they're for the ProTask types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PTA')) {
	$For = 'PTA';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$PTATarget = $_REQUEST['T'];

	if (isset($_REQUEST['ID']))
		$TSTID = $_REQUEST['ID'];
	else $TSTID = -1;
	if (isset($_REQUEST['A']))
		$AddPTA = $_REQUEST['A'];
	if (isset($_REQUEST['D']))
		$DelPTA = $_REQUEST['D'];

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
	if (isset($For) and ($For == 'PTA')) {
		if (isset($Target))
			$PTATarget = $Target;

		if (isset($ID))
			$TSTID = $ID;
		elseif (! isset($TSTID))
		$TSTID = -1;
		if (! isset($Offset))
			$Offset = 0;
		if (! isset($Records))
			$Records = 5;
	}
	else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
		$TSTID = -1;
		$Offset = 0;
		$Records = 5;
	}
}
$PTAPath = 'resources/protasks/protask_type_avail.php';
$PTADieError = '';
$PTAFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($PTATarget))
	$PTADieError .= 'ProTask type availability has not been told where to target its displays and thus would not be safe to run.<br />';
if ($TSTID == -1)
	$PTADieError .= "ProTask type availability has not been told what ProTask type to work with.<br />";

if ($PTADieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$TSKSec = GetAreaSecurity($sql, 3);
		if ($TSKSec['Config']) {
			//XCal - If a ProTask type removal has been requested then remove it
			if (isset($DelPTA)) {
				$stm = $sql->prepare(
						'DELETE FROM pro_task_type_avail_types '.
						'WHERE TTT_TST_ID = ? AND TTT_SUB_TST_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$TSTID,$DelPTA);
					if ($stm->execute())
						$stm->free_result();
					else $PTAFailError .= 'ProTask type availability failed attempting to remove the type with error ('.$stm->error.')<br />';
				}
				else $PTAFailError .= 'ProTask type availability encountered an error preparing to remove the type.<br />';
			}
			//XCal - If a ProTask type is being added then add it
			if (isset($AddPTA)) {
				$stm = $sql->prepare(
						'INSERT INTO pro_task_type_avail_types (TTT_TST_ID,TTT_SUB_TST_ID,TTT_UPDATED) '.
						'VALUES (?,?,current_timestamp())');
				if ($stm) {
					$stm->bind_param('ii',$TSTID,$AddPTA);
					if ($stm->execute())
						$stm->free_result();
					else $PTAFailError .= 'ProTask type availability failed adding the type with error('.$stm->error.')<br />';
				}
				else $PTAFailError .= 'ProTask type availability encountered an error preparing to add the type.<br />';
			}
		
			//XCal - Get the name and last updated date of the ProTask type
			$s_gtype = $sql->prepare('SELECT TST_NAME, TST_UPDATED FROM pro_task_types WHERE TST_ID = ?');
			if ($s_gtype) {
				$s_gtype->bind_param('i',$TSTID);
				if ($s_gtype->execute()) {
					$s_gtype->bind_result($PTAName,$PTAUpdated);
					$s_gtype->fetch();
					$s_gtype->free_result();
				}
				else $PTAFailError .= 'ProTask type availability encountered an error getting task type details.<br />';
			}
			else $PTAFailError .= 'ProTask type availability encountered an error preparing to get task type details.<br />';
		
			echo('<article>'.
					"<h3>$PTAName Configuration</h3> ProTask type last updated $PTAUpdated".
					'<p>You can restrict what tasks can have as sub-tasks to by their type. If no types are specified all types will be available.</p>'.
					'<table>'.
					'<tr><th>Allowed Sub-Task Types</th><th>Possible Sub-Task Types</th></tr>'.
					'<tr><td>'.
					'<table>');
		
			//XCal - Echo the assigned types, or "All types available" here
			$Count = 0;
			$s_linked = $sql->prepare(
					'SELECT TTT_SUB_TST_ID,TST_NAME '.
					'FROM pro_task_type_avail_types '.
					'JOIN pro_task_types ON TST_ID = TTT_SUB_TST_ID '.
					'WHERE TTT_TST_ID = ?');
			if ($s_linked) {
				$s_linked->bind_param('i',$TSTID);
				$s_linked->execute();
				$s_linked->bind_result($PTAID,$PTAName);
				while ($s_linked->fetch()) {
					$Count++;
					echo(	"<tr><td>$PTAName</td>".
							'<td><input type="button" class="button" value="Remove >>" onclick="'.
							"AJAX('$PTATarget','$PTAPath','For=PTA&T=$PTATarget&ID=$TSTID&D=$PTAID');".
							'" /></td></tr>');
				}
				$s_linked->free_result();
			}
			if ($Count == 0)
				echo(			'<tr><td><i>All types available.</ti></td></tr>');
		
			echo(			'</table></td>'.
					'<td><table>');
		
			//XCal - Echo the unassigned types here
			$Count = 0;
			$s_avail = $sql->prepare(
					'SELECT TST_ID,TST_NAME '.
					'FROM pro_task_types '.
					'WHERE NOT EXISTS (SELECT TTT_TST_ID '.
					'FROM pro_task_type_avail_types '.
					'WHERE TTT_SUB_TST_ID = TST_ID AND TTT_TST_ID = ?)');
			if ($s_avail) {
				//$_SESSION['DebugStr'] .= 'ProTask Type Availability: Available Types Prepared OK.<br />';
				$s_avail->bind_param('i',$TSTID);
				if (! $s_avail->execute())
					echo('Error fetching available ProTask types: '.$s_avail->error);
				$s_avail->bind_result($PTAID,$PTAName);
				while ($s_avail->fetch()) {
					$Count++;
					echo(			'<tr>'.
							'<td><input type="button" class="button" value="<< Add" onclick="'.
							"AJAX('$PTATarget','$PTAPath','For=PTA&T=$PTATarget&ID=$TSTID&A=$PTAID');".
							'" /></td>'.
							"<td>$PTAName</td></tr>");
				}
				$s_avail->free_result();
			}
			else {
				//$_SESSION['DebugStr'] .= 'ProTask Type Availability: Available types failed to Prepare ('.$sql->error.').<br />';
				$PTAFailError .= 'ProTask type availability failed preparing to get available types ('.$sql->error.').<br />';
			}
			if ($Count == 0)
				echo(			'<tr><td><i>All types specifically allowed.</ti></td></tr>');
		
			echo(			'</table>'.
					'</td></tr>'.
					'</table>'.
					'</article>');
			//$_SESSION['DebugStr'] .= "ProTask Type Availability: Failure messages below.<br />$PTAFailError";
			if (strlen($PTAFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$PTAFailError");
		}
		else echo('You do not have the required access rights to configure the ProTask system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>ProTask Type Availability Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$PTADieError.
			'</article>');
}
?>

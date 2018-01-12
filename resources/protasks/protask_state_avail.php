<?php
//XCal - We only want to pay attention to request variables if they're for the group types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PSA')) {
	$For = 'PSA';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$PSATarget = $_REQUEST['T'];

	if (isset($_REQUEST['ID']))
		$TSTID = $_REQUEST['ID'];
	else $TSTID = -1;
	if (isset($_REQUEST['A']))
		$AddPSA = $_REQUEST['A'];
	if (isset($_REQUEST['D']))
		$DelPSA = $_REQUEST['D'];

	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to PSA then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'PSA')) {
		if (isset($Target))
			$PSATarget = $Target;

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
$PSAPath = 'resources/protasks/protask_state_avail.php';
$PSADieError = '';
$PSAFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($PSATarget))
	$PSADieError .= 'ProTask state availability has not been told where to target its displays and thus would not be safe to run.<br />';
if ($TSTID == -1)
	$PSADieError .= "ProTask state availability has not been told what ProTask type to work with.<br />";

if ($PSADieError == '') {
	if (! function_exists('CheckToken'))		
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$TSKSec = GetAreaSecurity($sql, 3);
		if ($TSKSec['Config']) {
			//XCal - If a ProTask state removal has been requested then remove it
			if (isset($DelPSA)) {			
				$stm = $sql->prepare(
						'DELETE FROM pro_task_type_avail_states '.
						'WHERE TTA_TST_ID = ? AND TTA_TSS_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$TSTID,$DelPSA);
					if ($stm->execute())
						$stm->free_result();
					else $PSAFailError .= 'ProTask state availability failed attempting to remove the type with error ('.$stm->error.')<br />';
				}
				else $PSAFailError .= 'ProTask state availability encountered an error preparing to remove the type.<br />';
			}
			//XCal - If a ProTask state is being added then add it
			if (isset($AddPSA)) {
				$stm = $sql->prepare(
						'INSERT INTO pro_task_type_avail_states (TTA_TST_ID,TTA_TSS_ID,TTA_UPDATED) '.
						'VALUES (?,?,current_timestamp())');
				if ($stm) {
					$stm->bind_param('ii',$TSTID,$AddPSA);
					if ($stm->execute())
						$stm->free_result();
					else $PSAFailError .= 'ProTask state availability failed adding the type with error('.$stm->error.')<br />';
				}
				else $PSAFailError .= 'ProTask state availability encountered an error preparing to add the type.<br />';
			}
		
			//XCal - Get the name and last updated date of the ProTask type
			$s_gtype = $sql->prepare('SELECT TST_NAME, TST_UPDATED FROM pro_task_types WHERE TST_ID = ?');
			if ($s_gtype) {
				$s_gtype->bind_param('i',$TSTID);
				if ($s_gtype->execute()) {
					$s_gtype->bind_result($PSAName,$PTAUpdated);
					$s_gtype->fetch();
					$s_gtype->free_result();
				}
				else $PSAFailError .= 'Protask type state availability encountered an error getting task type details.<br />';
			}
			else $PSAFailError .= 'Protask type state availability encountered an error preparing to get task type details.<br />';
		
			echo('<article>'.
					"<h3>\"$PSAName\" Task Configuration</h3>Task type last updated $PTAUpdated".
					'<p>You can restrict what states tasks can have by their type. If no states are specified all states will be available.</p>'.
					'<table>'.
					'<tr><th>Allowed States</th><th>Possible States</th></tr>'.
					'<tr><td>'.
					'<table>');
		
			//XCal - Echo the assigned states, or "All states available" here
			$Count = 0;
			$s_linked = $sql->prepare(
					'SELECT TSS_ID,TSS_NAME '.
					'FROM pro_task_type_avail_states '.
					'JOIN pro_task_states ON TSS_ID = TTA_TSS_ID '.
					'WHERE TTA_TST_ID = ?');
			if ($s_linked) {
				$s_linked->bind_param('i',$TSTID);
				$s_linked->execute();
				$s_linked->bind_result($PSAID,$PSAName);
				while ($s_linked->fetch()) {
					$Count++;
					echo(	"<tr><td>$PSAName</td>".
							'<td><input type="button" class="button" value="Remove >>" onclick="'.
							"AJAX('$PSATarget','$PSAPath','For=PSA&T=$PSATarget&ID=$TSTID&D=$PSAID');".
							'" /></td></tr>');
				}
				$s_linked->free_result();
			}
			if ($Count == 0)
				echo(			'<tr><td><i>All states available.</ti></td></tr>');
		
			echo(			'</table></td>'.
					'<td><table>');
		
			//XCal - Echo the unassigned types here
			$Count = 0;
			$s_avail = $sql->prepare(
					'SELECT TSS_ID,TSS_NAME '.
					'FROM pro_task_states '.
					'WHERE NOT EXISTS (SELECT TTA_TST_ID '.
					'FROM pro_task_type_avail_states '.
					'WHERE TTA_TSS_ID = TSS_ID AND TTA_TST_ID = ?)');
			if ($s_avail) {
				//$_SESSION['DebugStr'] .= 'ProTask State Availability: Available states prepared OK.<br />';
				$s_avail->bind_param('i',$TSTID);
				if (! $s_avail->execute())
					echo('Error fetching available ProTask states: '.$s_avail->error);
				$s_avail->bind_result($PSAID,$PSAName);
				while ($s_avail->fetch()) {
					$Count++;
					echo(			'<tr>'.
							'<td><input type="button" class="button" value="<< Add" onclick="'.
							"AJAX('$PSATarget','$PSAPath','For=PSA&T=$PSATarget&ID=$TSTID&A=$PSAID');".
							'" /></td>'.
							"<td>$PSAName</td></tr>");
				}
				$s_avail->free_result();
			}
			else {
				//$_SESSION['DebugStr'] .= 'ProTask State availability: Available types failed to prepare ('.$sql->error.').<br />';
				$PSAFailError .= 'ProTask state availability failed preparing to get available states ('.$sql->error.').<br />';
			}
			if ($Count == 0)
				echo(			'<tr><td><i>All states specifically allowed.</ti></td></tr>');
		
			echo(			'</table>'.
					'</td></tr>'.
					'</table>'.
					'</article>');
			//$_SESSION['DebugStr'] .= "ProTask State availability: Failure messages below.<br />$PSAFailError";
			if (strlen($PSAFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$PSAFailError");
		}
		else echo('You do not have the required access rights to configure the ProTask system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>ProTask State Availability Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$PSADieError.
			'</article>');
}
?>

<?php
//XCal - We only want to pay attention to request variables if they're for the contact methods page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PST')) {
	$For = 'PST';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$PSTTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$PSTMode = $_REQUEST['M'];
	else $PSTMode = 'L';
	
	if (isset($_REQUEST['ID']))
		$PSTID = $_REQUEST['ID'];
	else $PSTID = -1;
	if (isset($_REQUEST['Name']))
		$PSTName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$PSTDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['Comp']))
		$PSTComplete = $_REQUEST['Comp'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
}
elseif (isset($For) and ($For == 'PST')) {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to PST then the generic variables have been qualified by the calling page (theoretically)
		if (isset($Target))
			$PSTTarget = $Target;
		if (isset($Mode))
			$PSTMode = $Mode;
		elseif (! isset($PSTMode))
			$PSTMode = 'L';
		if (isset($ID))
			$PSTID = $ID;
		elseif (! isset($PSTID))
			$PSTID = -1;
		if (! isset($Offset))
			$Offset = 0;
		if (! isset($Records))
			$Records = 5;
}		
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	$PSTMode = 'L';
	$PSTID = -1;
	unset($PSTName);
	unset($PSTDesc);
	unset($PSTComplete);
	$Offset = 0;
	$Records = 5;
}
$PSTPath = 'resources/protasks/protask_states.php';
$PSTDieError = '';
$PSTFailError = '';
$PSTModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($PSTTarget))
	$PSTDieError .= 'Project/Task states has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($PSTMode,$PSTModes))
	$PSTDieError .= "Project/Task states has been passed a mode ($PSTMode) which is does not support.<br />";	

if ($PSTDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$TSKSec = GetAreaSecurity($sql, 3);
		if ($TSKSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($PSTMode,array('N','M','D'))) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($PSTMode,array('M','D')) and (!($PSTID > 0))) {
					$PSTFailError .= 'Project/Task states was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
					$PSTMode = 'L';
				}
				elseif ($PSTMode == 'D') { //XCal -Perform the 'delete' and return to list mode
					//$_SESSION['DebugStr'] .= 'Contact Methods: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
							
						if ($stm->prepare('CALL sp_pro_rem_state(?,@Code,@Msg)')) {
							$stm->bind_param('i', $PSTID);
							if ($stm->execute()) {
								$stm->free_result();
						
								if ($stm->prepare('SELECT @Code,@Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();
								}
								else $PSTFailError .= 'Project/Task states encountered an error retrieving state removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $PSTFailError .= 'Project/Task states encountered an error preparing to request state removal.<br />';
					}
					else $PSTFailError .= 'Project/Task states encountered an error initialising state removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the list view back
					$PSTMode = 'L';				
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($PSTName)) {					
						$_SESSION['DebugStr'] .= "Project/Task States: Save Mode $PSTMode<br />";
						if ($PSTMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO pro_task_states (TSS_NAME,TSS_DESCRIPTION,TSS_COMPLETE,TSS_UPDATED) '.
								'VALUES (?,?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('ssi',$PSTName,$PSTDesc,$PSTComplete);
								$stm->execute();
								$stm->free_result();
								$PSTMode = 'L';
							}
							else $PSTFailError .= 'Project/Task states encountered an error preparing to add the state.<br />';
						}
						else {
							$stm = $sql->prepare(
								'UPDATE pro_task_states SET '.
								'TSS_NAME = ?, TSS_DESCRIPTION = ?, TSS_COMPLETE = ?, TSS_UPDATED = current_timestamp() '.
								'WHERE TSS_ID = ?');
							if ($stm) {
								$stm->bind_param('ssii',$PSTName,$PSTDesc,$PSTComplete,$PSTID);
								$stm->execute();
								$stm->free_result();
								$PSTMode = 'L';
							}
							else $PSTFailError .= 'Project/Task states encountered an error preparing to modify the state.<br />';
						}
					}
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Contact Methods: New/Modify Mode<br />';
						echo('<h4>');
						if ($PSTMode == 'M') {
							echo('Modify');
							$stm = $sql->prepare('SELECT TSS_NAME,TSS_DESCRIPTION,TSS_COMPLETE FROM pro_task_states WHERE TSS_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$PSTID);
								$stm->execute();
								$stm->bind_result($PSTName,$PSTDesc,$PSTComplete);
								$stm->fetch();
								$stm->free_result();
							}
							else $PSTFailError .= 'Project/Task states encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$PSTName = '';
							$PSTDesc = '';
							$PSTComplete = 0;
						}
						echo(' Project/Task State</h4><br />'.
							'<input type="hidden" id="TSS_ID" value="'.$PSTID.'" />'.
							'<table>'.
							'<tr><td class="fieldname">Name</td>'.
							"<td><input id=\"TSS_NAME\" type=\"text\" value=\"$PSTName\" /></td></tr>".
							'<tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"TSS_DESC\" rows=\"5\" cols=\"50\">$PSTDesc</textarea></td></tr>".
							'<tr><td class="fieldname">Complete?</td>'.
							'<td><input id="TSS_COMP" type="checkbox"');
						if ($PSTComplete == 1)
							echo(' checked');
						echo(' /></td></tr>'.
							'</table>'.
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveProTaskState('$PSTTarget','$PSTMode');".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$PSTTarget','$PSTPath','For=PST&T=$PSTTarget&M=L');".
							'" /><br />');
					}
				}
			}
			
			//XCal - Either list mode was requested or the (N)ew,(M)odify or (D)delete mode has finished its work
			if ($PSTMode == 'L') {
				//$_SESSION['DebugStr'] .= 'Contact Methods: List Mode<br />';
				$res = $sql->query('SELECT COUNT(TSS_ID) FROM pro_task_states WHERE TSS_SR = 0');
				if (! $res) {
					$PSTFailError .= 'Project/Task states failed to get a count of states ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				echo('<input type="button" class="button" value="New State" onclick="'.
					"AJAX('$PSTTarget','$PSTPath','For=PST&T=$PSTTarget&M=N');".
					'" /><br />');
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing states $LowRec to $HighRec of $Count.<br />");
						
				echo('<table><tr><th>State</th><th>Description</th><th>Complete?</th><th>Updated</th><th>Edit</th><th>Remove</th></tr>');
				$stm = $sql->prepare(
					'SELECT TSS_ID,TSS_NAME,TSS_DESCRIPTION,TSS_COMPLETE,TSS_UPDATED '.
					'FROM pro_task_states '.
					'WHERE TSS_SR = 0 LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					$stm->execute();
					$stm->bind_result($PSTID,$PSTName,$PSTDesc,$PSTComplete,$PSTUpdated);
					while ($stm->fetch()) {
						echo('<tr>'.
								"<td>$PSTName</td><td>$PSTDesc</td>");
						if ($PSTComplete == 1)
							echo('<td>Yes</td>');
						else echo('<td>No</td>');
						echo("<td>$PSTUpdated</td>".
								'<td><input class="button" type="button" value="Edit" onclick="'.
								"AJAX('$PSTTarget','$PSTPath','For=PST&T=$PSTTarget&M=M&ID=$PSTID');".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$PSTTarget','$PSTPath','For=PST&T=$PSTTarget&M=D&ID=$PSTID');".
								'" /></td>'.
							'</tr>');
					}				
				}
				else $PSTFailError .= 'Project/Task states failed to get a list of states to display ('.$sql->error.').<br />';
				echo('</table>');
				
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
							"AJAX('$PSTTarget','$PSTPath','For=PST&T=$PSTTarget&M=L&RO=$PrevOffset&RC$Records');".
							'" />');
				}
				
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
							"AJAX('$PSTTarget','$PSTPath','For=PST&T=$PSTTarget&M=L&RO=$NextOffset&RC$Records');".
							'" />');
				}
				echo('<br />');
			}	
			if (strlen($PSTFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$PSTFailError");
		}
		else echo('You do not have the required access rights to configure the ProTask system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Projects/Tasks States Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$PSTDieError.
		'</article>');
}
?>
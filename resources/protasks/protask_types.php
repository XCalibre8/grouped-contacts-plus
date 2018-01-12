<?php
//XCal - We only want to pay attention to request variables if they're for the group types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PTP')) {
	$For = 'PTP';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$PTPTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D,C|List(Default),New,Modify,Delete,Configure(Changes Select Target and enables N,M,D);;]
	if (isset($_REQUEST['M']))
		$PTPMode = $_REQUEST['M'];
	else $PTPMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$PTPSelTarget = $_REQUEST['ST'];
	else $PTPSelTarget = 'PTPSubDiv';
	
	if (isset($_REQUEST['ID']))
		$PTPID = $_REQUEST['ID'];
	else $PTPID = -1;
	if (isset($_REQUEST['Name']))
		$PTPName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$PTPDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['Sub']))
		$PTPSub = $_REQUEST['Sub'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
	}
elseif (isset($For) and ($For == 'PTP')) {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to PTP then the generic variables have been qualified by the calling page (theoretically)
	if (isset($Target))
		$PTPTarget = $Target;
	if (isset($Mode))
		$PTPMode = $Mode;
	elseif (! isset($PTPMode))
		$PTPMode = 'L';
	if (isset($SelTarget))
		$PTPSelTarget = $SelTarget;
	else $PTPSelTarget = 'PTPSubDiv';
	
	if (isset($ID))
		$PTPID = $ID;
	elseif (! isset($PTPID))
	$PTPID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($PTPMode))
		$PTPMode = 'L';
	$PTPID = -1;
	if (! isset($PTPSelTarget))
		$PTPSelTarget = 'PTPSubDiv';
	unset($PTPName);
	unset($PTPDesc);
	$Offset = 0;
	$Records = 5;
}
$PTPPath = 'resources/protasks/protask_types.php';
$PTPDieError = '';
$PTPFailError = '';
$PTPModes = array('L','N','M','D','C');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($PTPTarget))
	$PTPDieError .= 'Project/task types has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($PTPMode,$PTPModes))
	$PTPDieError .= "Project/task types has been passed a mode ($PTPMode) which is does not support.<br />";

if ($PTPDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$TSKSec = GetAreaSecurity($sql, 3);
		//XCal - Check if we're in New/Modify/Delete mode
		if (in_array($PTPMode,array('N','M','D'))) {
			if ($TSKSec['Config']) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($PTPMode,array('M','D')) and (!($PTPID > 0))) {
					$PTPFailError .= 'Project/task types was passed a modify or delete request without an ID specified. Changed to select mode.<br />';
					$PTPMode = 'C'; //XCal - We can only do these actions on protask types from configuration list mode, so we go to that not L
				}
				elseif ($PTPMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
					//$_SESSION['DebugStr'] .= 'Project/Task Types: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
							
						if ($stm->prepare('CALL sp_pro_rem_task_type(?,@Code,@Msg)')) {
							$stm->bind_param('i', $PTPID);						
							if ($stm->execute()) {
								$stm->free_result();
					
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();							
								}
								else $PTPFailError .= 'Project/task types encountered an error retrieving type removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $PTPFailError .= 'Project/task types encountered an error preparing to request type removal.<br />';
					}
					else $PTPFailError .= 'Project/task types encountered an error initialising type removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the config list view back
					$PTPMode = 'C';
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($PTPName)) {
						//$_SESSION['DebugStr'] .= 'Project/Task Types: Save Mode<br />';
						if ($PTPMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO pro_task_types (TST_NAME,TST_DESCRIPTION,TST_SUPPORT_SUB,TST_UPDATED) '.
								'VALUES (?,?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('ssi',$PTPName,$PTPDesc,$PTPSub);
								$stm->execute();
								$stm->free_result();
								$PTPMode = 'C';
							}
							else $PTPFailError .= 'Project/task types encountered an error preparing to add the type.<br />';
						}
						else {
							$stm = $sql->prepare(
									'UPDATE pro_task_types SET '.
									'TST_NAME = ?, TST_DESCRIPTION = ?, TST_SUPPORT_SUB = ?, TST_UPDATED = current_timestamp() '.
									'WHERE TST_ID = ?');							
							if ($stm) {
								$stm->bind_param('ssii',$PTPName,$PTPDesc,$PTPSub,$PTPID);
								$stm->execute();
								$stm->free_result();
								$PTPMode = 'C';
							}
							else $PTPFailError .= 'Project/task types encountered an error preparing to modify the type.<br />';
						}
					}
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Projet/Task Types: New/Modify Mode<br />';						
						echo('<h4>');
						if ($PTPMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare('SELECT TST_NAME,TST_DESCRIPTION,TST_SUPPORT_SUB FROM pro_task_types WHERE TST_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$PTPID);
								$stm->execute();
								$stm->bind_result($PTPName,$PTPDesc,$PTPSub);
								$stm->fetch();
								$stm->free_result();
							}
							else $PTPFailError .= 'Project/task types encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$PTPName = '';
							$PTPDesc = '';
							$PTPSub = 1;
						}
						echo(' Project/Task Type</h4><br />'.
							'<input type="hidden" id="TST_ID" value="'.$PTPID.'" />'.
							'<table>'.
							'<tr><td class="fieldname">Name</td>'.
							"<td><input id=\"TST_NAME\" type=\"text\" value=\"$PTPName\" /></td></tr>".
							'<tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"TST_DESC\" rows=\"5\" cols=\"50\">$PTPDesc</textarea></td></tr>".
							'<tr><td class="fieldname">Sub-tasks allowed?</td>'.
							"<td><input id=\"TST_SUB\" type=\"checkbox\" ");
						if ($PTPSub == 1)
							echo('checked');
						echo(' /></td></tr></table>'.
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveProTaskType('$PTPTarget','$PTPMode');".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$PTPTarget','$PTPPath','For=PTP&T=$PTPTarget&M=C');".
							'" /><br />');
					}
				}
			}
			else echo('You do not have the required access rights to configure the ProTask system.');
		}
		
		//XCal - We're not editing or we have saved the details so show the list view
		if (in_array($PTPMode,array('L','C'))) {
			if ($PTPMode == 'C')
				$CanView = $TSKSec['Config'];
			else $CanView = $TSKSec['SearchView'] == 2;
			
			if ($CanView) {
				//$_SESSION['DebugStr'] .= 'ProTask Types: List Mode<br />';
				$res = $sql->query('SELECT COUNT(TST_ID) FROM pro_task_types WHERE TST_SR = 0');
				if (! $res) {
					$PTPFailError .= 'Project/task types failed to get a count of types ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				if ($PTPMode == 'C') 
					echo('<input type="button" class="button" value="New Project/Task Type" onclick="'.
						"AJAX('$PTPTarget','$PTPPath','For=PTP&T=$PTPTarget&M=N');".
						'" /><br />');
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing project/task types $LowRec to $HighRec of $Count.<br />");
						
				echo('<table><tr><th>Select</th><th>Type</th><th>Description</th><th>Subtasks?</th><th>Updated</th>');
				if ($PTPMode == 'C')
					echo('<th>Edit</th><th>Remove</th>');
				echo('</tr>');
				$stm = $sql->prepare(
					'SELECT TST_ID,TST_NAME,TST_DESCRIPTION,TST_SUPPORT_SUB,TST_UPDATED '.
					'FROM pro_task_types '.
					'WHERE TST_SR = 0 LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					$stm->execute();
					$stm->bind_result($PTPID,$PTPName,$PTPDesc,$PTPSub,$PTPUpdated);
					while ($stm->fetch()) {
						echo('<tr><td><input type="button" class=');
						//XCal - The select button needs to bring up a different page if we're configuring to not
						if ($PTPMode == 'C') {
							echo('"vertscroll" value="Types" onclick="'.
								"AJAX('$PTPSelTarget','resources/protasks/protask_type_avail.php','For=PTA&T=$PTPSelTarget&ID=$PTPID');".
								'" /><input type="button" class="vertscroll" value="States" onclick="'.
								"AJAX('$PTPSelTarget','resources/protasks/protask_state_avail.php','For=PSA&T=$PTPSelTarget&ID=$PTPID');");
						}
						else {						
							echo('"button" value="Select" onclick="'.
								"AJAX('$PTPSelTarget','resources/protasks/typed_protasks.php','For=TPT&T=$PTPSelTarget&TID=$PTPID');");
						}
						echo('" /></td>'.
								"<td>$PTPName</td><td>$PTPDesc</td>");
						if ($PTPSub == 1)
							echo('<td>Yes</td>');
						else echo('<td>No</td>');
						echo("<td>$PTPUpdated</td>");
						if ($PTPMode == 'C')
							echo('<td><input class="button" type="button" value="Edit" onclick="'.
								"AJAX('$PTPTarget','$PTPPath','For=PTP&T=$PTPTarget&M=M&ID=$PTPID');".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$PTPTarget','$PTPPath','For=PTP&T=$PTPTarget&M=D&ID=$PTPID');".
								'" /></td>');
						echo('</tr>');
					}	
				}
				else $PTPFailError .= 'Project/task types failed preparing to get a list of types to display ('.$sql->error.').<br />';
				
				echo('</table>');
				
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
						"AJAX('$PTPTarget','$PTPPath','For=PTP&T=$PTPTarget&M=$PTPMode&RO=$PrevOffset&RC=$Records');".
						'" />');
				}
				
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
						"AJAX('$PTPTarget','$PTPPath','For=PTP&T=$PTPTarget&M=$PTPMode&RO=$NextOffset&RC=$Records');".
						'" />');
				}
				echo('<br /><div id="PTPSubDiv"></div>');
			}
			elseif ($PTPMode == 'C')
				echo('You do not have the required access rights to configure the ProTask system.');
			elseif ($TSKSec['SearchView'] == 1)
				echo('You only have the right to view ProTasks which your account is linked by, which does not include ProTask browsing.');
			else echo('You do not have the required rights to view ProTasks');
		}
		if (strlen($PTPFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$PTPFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Projects/Tasks Types Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$PTPDieError.
		'</article>');
}
?>
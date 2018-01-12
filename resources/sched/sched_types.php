<?php
//XCal - We only want to pay attention to request variables if they're for the group types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'SCT')) {
	$For = 'SCT';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$SCTTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D,C|List(Default),New,Modify,Delete,Configure(Changes Select Target and enables N,M,D);;]
	if (isset($_REQUEST['M']))
		$SCTMode = $_REQUEST['M'];
	else $SCTMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$SCTSelTarget = $_REQUEST['ST'];
	else $SCTSelTarget = 'SCTSubDiv';
	
	if (isset($_REQUEST['ID']))
		$SCTID = $_REQUEST['ID'];
	else $SCTID = -1;
	if (isset($_REQUEST['Name']))
		$SCTName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$SCTDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['Col']))
		$SCTColour = $_REQUEST['Col'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
	}
elseif (isset($For) and ($For == 'SCT')) {
	if (isset($Target))
		$SCTTarget = $Target;
	if (isset($Mode))
		$SCTMode = $Mode;
	elseif (! isset($SCTMode))
		$SCTMode = 'L';
	if (isset($SelTarget))
		$SCTSelTarget = $SelTarget;
	else $SCTSelTarget = 'SCTSubDiv';
	
	if (isset($ID))
		$SCTID = $ID;
	elseif (! isset($SCTID))
	$SCTID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($SCTMode))
		$SCTMode = 'L';
	$SCTID = -1;
	if (! isset($SCTSelTarget))
		$SCTSelTarget = 'SCTSubDiv';
	unset($SCTName);
	unset($SCTDesc);
	$Offset = 0;
	$Records = 5;
}

$SCTPath = 'resources/sched/sched_types.php';
$SCTDieError = '';
$SCTFailError = '';
$SCTModes = array('L','N','M','D','C');

//XCal - Let's make sure we have the values we need to run the page
if (! isset($SCTTarget))
	$SCTDieError .= 'Schedule types has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($SCTMode,$SCTModes))
	$SCTDieError .= "Schedule types has been passed a mode ($SCTMode) which is does not support.<br />";

if ($SCTDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$SCHSec = GetAreaSecurity($sql, 5);
		if ($SCHSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($SCTMode,array('N','M','D'))) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($SCTMode,array('M','D')) and (!($SCTID > 0))) {
					$SCTFailError .= 'Schedule types was passed a modify or delete request without an ID specified. Changed to select mode.<br />';
					$SCTMode = 'C'; //XCal - We can only do these actions on schedule types from configuration list mode, so we go to that not L
				}
				elseif ($SCTMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
					//$_SESSION['DebugStr'] .= 'Schedule Types: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
							
						if ($stm->prepare('CALL sp_sch_rem_schedule_type(?,@Code,@Msg)')) {
							$stm->bind_param('i', $SCTID);						
							if ($stm->execute()) {
								$stm->free_result();
					
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();							
								}
								else $SCTFailError .= 'Schedule types encountered an error retrieving type removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $SCTFailError .= 'Schedule types encountered an error preparing to request type removal.<br />';
					}
					else $SCTFailError .= 'Schedule types encountered an error initialising type removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the config list view back
					$SCTMode = 'C';
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($SCTName)) {
						//$_SESSION['DebugStr'] .= 'Schedule Types: Save Mode<br />';
						if ($SCTMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO sch_schedule_types (SCT_NAME,SCT_DESCRIPTION,SCT_COLOUR,SCT_UPDATED) '.
								'VALUES (?,?,?,current_timestamp())');
							if ($stm) {
								//XCal - TODO - Get the colour value passed and saved
								$stm->bind_param('sss',$SCTName,$SCTDesc,$SCTColour);
								$stm->execute();
								$stm->free_result();
								$SCTMode = 'C';
							}
							else $SCTFailError .= 'Schedule types encountered an error preparing to add the type.<br />';
						}
						else {
							$stm = $sql->prepare(
									'UPDATE sch_schedule_types SET '.
									'SCT_NAME = ?, SCT_DESCRIPTION = ?, SCT_COLOUR = ?, SCT_UPDATED = current_timestamp() '.
									'WHERE SCT_ID = ?');							
							if ($stm) {
								$stm->bind_param('sssi',$SCTName,$SCTDesc,$SCTColour,$SCTID);
								$stm->execute();
								$stm->free_result();
								$SCTMode = 'C';
							}
							else $SCTFailError .= 'Schedule types encountered an error preparing to modify the type.<br />';
						}
					}
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Schedule Types: New/Modify Mode<br />';						
						echo('<h4>');
						if ($SCTMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare('SELECT SCT_NAME,SCT_DESCRIPTION,SCT_COLOUR FROM sch_schedule_types WHERE SCT_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$SCTID);
								$stm->execute();
								$stm->bind_result($SCTName,$SCTDesc,$SCTColour);
								$stm->fetch();
								$stm->free_result();
							}
							else $SCTFailError .= 'Schedule types encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$SCTName = '';
							$SCTDesc = '';
							$SCTColour = '#CCCCFF';
						}
						echo(' Schedule Type</h4><br />'.
							'<input type="hidden" id="SCT_ID" value="'.$SCTID.'" />'.
							'<table>'.
							'<tr><td class="fieldname">Name</td>'.
							"<td><input id=\"SCT_NAME\" type=\"text\" value=\"$SCTName\" /></td></tr>".
							'<tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"SCT_DESC\" rows=\"5\" cols=\"50\">$SCTDesc</textarea></td></tr>".
							'<tr><td class="fieldname">Colour</td>'.
							"<td><input type=\"color\" id=\"SCT_COLOUR\" value=\"$SCTColour\" /></td></tr></table>".
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveScheduleType('$SCTTarget','$SCTMode');".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$SCTTarget','$SCTPath','For=SCT&T=$SCTTarget&M=C');".
							'" /><br />');
					}
				}
			}
			
			//XCal - We're not editing or we have saved the details so show the list view
			if (in_array($SCTMode,array('L','C'))) {		
				//$_SESSION['DebugStr'] .= 'Schedule Types: List Mode<br />';
				$res = $sql->query('SELECT COUNT(SCT_ID) FROM sch_schedule_types WHERE SCT_SR = 0');
				if (! $res) {
					$SCTFailError .= 'Schedule types failed to get a count of types ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				if ($SCTMode == 'C') 
					echo('<input type="button" class="button" value="New Schedule Type" onclick="'.
						"AJAX('$SCTTarget','$SCTPath','For=SCT&T=$SCTTarget&M=N');".
						'" /><br />');
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing schedule types $LowRec to $HighRec of $Count.<br />");
						
				echo('<table><tr><th>Select</th><th>Type</th><th>Description</th><th>Colour</th><th>Updated</th>');
				if ($SCTMode == 'C')
					echo('<th>Edit</th><th>Remove</th>');
				echo('</tr>');
				$stm = $sql->prepare(
					'SELECT SCT_ID,SCT_NAME,SCT_DESCRIPTION,SCT_COLOUR,SCT_UPDATED '.
					'FROM sch_schedule_types '.
					'WHERE SCT_SR = 0 LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					$stm->execute();
					$stm->bind_result($SCTID,$SCTName,$SCTDesc,$SCTColour,$SCTUpdated);
					while ($stm->fetch()) {
						echo('<tr>'.
								'<td><input type="button" class="button" value="Select" onclick="');
						echo("AJAX('$SCTSelTarget','resources/sched/sched_type_link_avail.php','For=STA&T=$SCTSelTarget&ID=$SCTID');");
						echo('" /></td>'.
								"<td>$SCTName</td><td>$SCTDesc</td><td style=\"background-color: $SCTColour;\"></td><td>$SCTUpdated</td>");
						if ($SCTMode == 'C')
							echo('<td><input class="button" type="button" value="Edit" onclick="'.
								"AJAX('$SCTTarget','$SCTPath','For=SCT&T=$SCTTarget&M=M&ID=$SCTID');".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$SCTTarget','$SCTPath','For=SCT&T=$SCTTarget&M=D&ID=$SCTID');".
								'" /></td>');
						echo('</tr>');
					}	
				}
				else $SCTFailError .= 'Schedule types failed preparing to get a list of types to display ('.$sql->error.').<br />';
				
				echo('</table>');
				
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
						"AJAX('$SCTTarget','$SCTPath','For=SCT&T=$SCTTarget&M=$SCTMode&RO=$PrevOffset&RC=$Records');".
						'" />');
				}
				
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
						"AJAX('$SCTTarget','$SCTPath','For=SCT&T=$SCTTarget&M=$SCTMode&RO=$NextOffset&RC=$Records');".
						'" />');
				}			
				echo('<br /><div id="SCTSubDiv"></div>');
			}
			if (strlen($SCTFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$SCTFailError");
		}
		else echo('You do not have the required access right to configure the scheduling system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Schedule Types Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$SCTDieError.
		'</article>');
}
?>
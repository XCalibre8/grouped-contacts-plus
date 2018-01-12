<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'RFT')) {
	$For = 'RFT';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$RFTTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D,C|List(Default),New,Modify,Delete,Configure(Changes Select Target and enables N,M,D);;]
	if (isset($_REQUEST['M']))
		$RFTMode = $_REQUEST['M'];
	else $RFTMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$RFTSelTarget = $_REQUEST['ST'];
	else $RFTSelTarget = 'RFTSubDiv';
	
	if (isset($_REQUEST['ID']))
		$RFTID = $_REQUEST['ID'];
	else $RFTID = -1;
	if (isset($_REQUEST['Name']))
		$RFTName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$RFTDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['LTP']))
		$RFTLTPID = $_REQUEST['LTP'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
	}
elseif (isset($For) and ($For == 'RFT')) { //XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	if (isset($Target))
		$RFTTarget = $Target;
	if (isset($Mode))
		$RFTMode = $Mode;
	elseif (! isset($RFTMode))
		$RFTMode = 'L';
	if (isset($SelTarget))
		$RFTSelTarget = $SelTarget;
	else $RFTSelTarget = 'RFTSubDiv';
	
	if (isset($ID))
		$RFTID = $ID;
	elseif (! isset($RFTID))
	$RFTID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($RFTMode))
		$RFTMode = 'L';
	$RFTID = -1;
	if (! isset($RFTSelTarget))
		$RFTSelTarget = 'RFTSubDiv';
	unset($RFTName);
	unset($RFTDesc);
	unset($RFTLTPID);
	$Offset = 0;
	$Records = 5;
}
$RFTPath = 'resources/reqful/reqful_fulfill_types.php';
$RFTDieError = '';
$RFTFailError = '';
$RFTModes = array('L','N','M','D','C');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($RFTTarget))
	$RFTDieError .= 'Fulfillment types has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($RFTMode,$RFTModes))
	$RFTDieError .= "Fulfillment types has been passed a mode ($RFTMode) which is does not support.<br />";

if ($RFTDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$REQSec = GetAreaSecurity($sql, 4);
		if ($REQSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($RFTMode,array('N','M','D'))) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($RFTMode,array('M','D')) and (!($RFTID > 0))) {
					$RFTFailError .= 'Fulfillment types was passed a modify or delete request without an ID specified. Changed to select mode.<br />';
					$RFTMode = 'C'; //XCal - We can only do these actions on group types from configuration list mode, so we go to that not L
				}
				elseif ($RFTMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
					//$_SESSION['DebugStr'] .= 'Fulfillment Types: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
							
						if ($stm->prepare('CALL sp_rqf_rem_fulfill_type(?,@Code,@Msg)')) {
							$stm->bind_param('i', $RFTID);						
							if ($stm->execute()) {
								$stm->free_result();
					
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();							
								}
								else $RFTFailError .= 'Fulfillment types encountered an error retrieving type removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $RFTFailError .= 'Fulfillment types encountered an error preparing to request type removal.<br />';
					}
					else $RFTFailError .= 'Fulfillment types encountered an error initialising type removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the config list view back
					$RFTMode = 'C';
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($RFTName)) {
						//$_SESSION['DebugStr'] .= 'Fulfillment Types: Save Mode<br />';
						if ($RFTMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO rqf_fulfillment_types (FLT_NAME,FLT_DESCRIPTION,FLT_FOR_LTP_ID,FLT_UPDATED) '.
								'VALUES (?,?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('ssi',$RFTName,$RFTDesc,$RFTLTPID);
								$stm->execute();
								$stm->free_result();
								$RFTMode = 'C';
							}
							else $RFTFailError .= 'Fulfillment types encountered an error preparing to add the type.<br />';
						}
						else {
							$stm = $sql->prepare(
									'UPDATE rqf_fulfillment_types SET '.
									'FLT_NAME = ?, FLT_DESCRIPTION = ?, FLT_FOR_LTP_ID = ?, FLT_UPDATED = current_timestamp() '.
									'WHERE FLT_ID = ?');							
							if ($stm) {
								$stm->bind_param('ssii',$RFTName,$RFTDesc,$RFTLTPID,$RFTID);
								$stm->execute();
								$stm->free_result();
								$RFTMode = 'C';
							}
							else $RFTFailError .= 'Fulfillment types encountered an error preparing to modify the type.<br />';
						}
					}
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Fulfillment Types: New/Modify Mode<br />';						
						echo('<h4>');
						if ($RFTMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare('SELECT FLT_NAME,FLT_DESCRIPTION,FLT_FOR_LTP_ID FROM rqf_fulfillment_types WHERE FLT_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$RFTID);
								$stm->execute();
								$stm->bind_result($RFTName,$RFTDesc,$RFTLTPID);
								$stm->fetch();
								$stm->free_result();
							}
							else $RFTFailError .= 'Fulfillment types encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$RFTName = '';
							$RFTDesc = '';
							unset($RFTLTPID);
						}
						if (!isset($RFTLTPID))
							$RFTLTPID = 0;
							
						echo(' Fulfillment Type</h4><br />'.
							'<input type="hidden" id="FLT_ID" value="'.$RFTID.'" />'.
							'<table>'.
							'<tr><td class="fieldname">Name</td>'.
							"<td><input id=\"FLT_NAME\" type=\"text\" value=\"$RFTName\" /></td></tr>".
							'<tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"FLT_DESC\" rows=\"5\" cols=\"50\">$RFTDesc</textarea></td></tr>".
							'<tr><td class="fieldname">For Type</td>'.
							'<td><select id="FLT_LTP_ID">');
						
						$stm = $sql->prepare('SELECT LTP_ID,LTP_NAME FROM lnk_link_types');
						if ($stm) {
							if ($stm->execute()) {
								$stm->bind_result($LTPID,$LTPName);
								while ($stm->fetch()) {
									if ($RFTLTPID == 0)
										$RFTLTPID = $LTPID;
									echo('<option value="'.$LTPID.'"');
									if ($RFTLTPID == $LTPID)
										echo(' selected');
									echo(">$LTPName</option>");
								}
								$stm->free_result();
							}
							else $RFTFailError .= 'Fulfillment types failed to get link types ('.$stm->error.').<br />';
						}
						else $RFTFailError .= 'Fulfillment types encountered an error preparing to get link types ('.$sql->error.').<br />';
						
						echo('</select></td></tr>'.
							'</table>'.
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveFulfillmentType('$RFTTarget','$RFTMode');".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$RFTTarget','$RFTPath','For=RFT&T=$RFTTarget&M=C');".
							'" /><br />');
					}
				}
			}
			
			//XCal - We're not editing or we have saved the details so show the list view
			if (in_array($RFTMode,array('L','C'))) {		
				//$_SESSION['DebugStr'] .= 'Fulfillment Types: List Mode<br />';
				$res = $sql->query('SELECT COUNT(FLT_ID) FROM rqf_fulfillment_types WHERE FLT_SR = 0');
				if (! $res) {
					$RFTFailError .= 'Fulfillment types failed to get a count of types ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				if ($RFTMode == 'C') 
					echo('<input type="button" class="button" value="New Fulfillment Type" onclick="'.
						"AJAX('$RFTTarget','$RFTPath','For=RFT&T=$RFTTarget&M=N');".
						'" /><br />');
				
				if ($Count == 0)
					echo('No fulfillment types found.');
				else {
					$LowRec = $Offset+1;
					if ($Count < ($Offset+$Records))
						$HighRec = $Count;
					else $HighRec = $Offset+$Records;
					echo("Showing fulfillment types $LowRec to $HighRec of $Count.<br />");
							
					echo('<table><tr><th>Select</th><th>Type</th><th>Description</th><th>For...</th><th>Updated</th>');
					//if ($RFTMode == 'C')
					echo('<th>Edit</th><th>Remove</th>');
					echo('</tr>');
					$stm = $sql->prepare(
						'SELECT FLT_ID,FLT_NAME,FLT_DESCRIPTION,LTP_NAME,FLT_UPDATED '.
						'FROM rqf_fulfillment_types '.
						'JOIN lnk_link_types ON LTP_ID = FLT_FOR_LTP_ID '.
						'WHERE FLT_SR = 0 LIMIT ?,?');
					if ($stm) {
						$stm->bind_param('ii',$Offset,$Records);
						$stm->execute();
						$stm->bind_result($RFTID,$RFTName,$RFTDesc,$RFTLTPName,$RFTUpdated);
						while ($stm->fetch()) {
							echo('<tr>'.
									'<td><input type="button" class="button" value="Select" onclick="');
							//XCal - The fulfillment types screen only has config mode
							//if ($RFTMode == 'C')
							echo("AJAX('$RFTSelTarget','resources/reqful/reqful_fulfillments.php','For=FLM&T=$RFTSelTarget&TID=$RFTID');");
							//else echo("AJAX('$RFTSelTarget','resources/groups/typed_groups.php','For=TGP&T=$RFTSelTarget&TID=$RFTID');");
							echo('" /></td>'.
									"<td>$RFTName</td><td>$RFTDesc</td><td>$RFTLTPName</td><td>$RFTUpdated</td>");
							//if ($RFTMode == 'C')
							echo('<td><input class="button" type="button" value="Edit" onclick="'.
								"AJAX('$RFTTarget','$RFTPath','For=RFT&T=$RFTTarget&M=M&ID=$RFTID');".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$RFTTarget','$RFTPath','For=RFT&T=$RFTTarget&M=D&ID=$RFTID');".
								'" /></td>');
							echo('</tr>');
						}
					}
					else $RFTFailError .= 'Fulfillment types failed preparing to get a list of types to display ('.$sql->error.').<br />';
				
					echo('</table>');
					
					if ($Offset > 0) {
						$PrevOffset = $Offset-$Records;
						echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
							"AJAX('$RFTTarget','$RFTPath','For=RFT&T=$RFTTarget&M=$RFTMode&RO=$PrevOffset&RC=$Records');".
							'" />');
					}
					
					if (($Offset + $Records) < $Count) {
						$NextOffset = $Offset+$Records;
						echo('<input class="nextbutton" type="button" value="Next" onclick="'.
							"AJAX('$RFTTarget','$RFTPath','For=RFT&T=$RFTTarget&M=$RFTMode&RO=$NextOffset&RC=$Records');".
							'" />');
					}			
					echo('<br /><div id="RFTSubDiv"></div>');
				}
			}
			if (strlen($RFTFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$RFTFailError");
		}
		else echo('You do not have the required access rights to configure the requirement fulfillment system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Fulfillment Types Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$RFTDieError.
		'</article>');
}
?>
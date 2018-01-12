<?php
require(dirname(__FILE__).'/../gen_session.php');	
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'FLM')) {
	$For = 'FLM';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$FLMTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$FLMMode = $_REQUEST['M'];
	else $FLMMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$FLMSelTarget = $_REQUEST['ST'];
	else $FLMSelTarget = 'FLMSubDiv';
	
	if (isset($_REQUEST['ID']))
		$FLMID = $_REQUEST['ID'];
	else $FLMID = -1;
	if (isset($_REQUEST['TID']))
		$FLTID = $_REQUEST['TID'];
	if (isset($_REQUEST['FLT']))
		$FLMFLTID = $_REQUEST['FLT'];
	if (isset($_REQUEST['Name']))
		$FLMName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$FLMDesc = $_REQUEST['Desc'];			
	if (isset($_REQUEST['Mode']))
		$FLMEMode = $_REQUEST['Mode'];
	if (isset($_REQUEST['LID']))
		$FLMLinkID = $_REQUEST['LID'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
	
	}
elseif (isset($For) and ($For == 'FLM')) {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to FLM then the generic variables have been qualified by the calling page (theoretically)
	
	if (isset($Target))
		$FLMTarget = $Target;
	if (isset($Mode))
		$FLMMode = $Mode;
	elseif (! isset($FLMMode))
		$FLMMode = 'L';
	if (isset($SelTarget))
		$FLMSelTarget = $SelTarget;
	else $FLMSelTarget = 'FLMSubDiv';
	
	if (isset($ID))
		$FLMID = $ID;
	elseif (! isset($FLMID))
		$FLMID = -1;
	if (isset($TID))
		$FLTID = $TID;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($FLMMode))
		$FLMMode = 'L';
	$FLMID = -1;
	if (! isset($FLMSelTarget))
		$FLMSelTarget = 'FLMSubDiv';
	unset($FLMFLTID);
	unset($FLMName);
	unset($FLMDesc);
	unset($FLMEMode);
	$Offset = 0;
	$Records = 5;
}
$FLMPath = 'resources/reqful/reqful_fulfillments.php';
$FLMDieError = '';
$FLMFailError = '';
$FLMModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($FLTID))
	$FLMDieError .= 'Fulfillments configuration has not been passed a type for which to display fulfillments.<br />';
if (! isset($FLMTarget))
	$FLMDieError .= 'Fulfillments configuration has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($FLMMode,$FLMModes))
	$FLMDieError .= "Fulfillments configuration has been passed a mode ($FLMMode) which is does not support.<br />";

if ($FLMDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$REQSec = GetAreaSecurity($sql, 4);
		if ($REQSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($FLMMode,array('N','M','D'))) {
				if (in_array($FLMMode,array('M','D')) and (!($FLMID > 0))) {
					$FLMFailError .= 'Fulfillments configuration was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
					$FLMMode = 'L';
				}
				elseif ($FLMMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
					//$_SESSION['DebugStr'] .= 'Fulfillments configuration: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
				
						if ($stm->prepare('CALL sp_rqf_rem_fulfillment(?,@Code,@Msg)')) {
							$stm->bind_param('i', $FLMID);
							if ($stm->execute()) {
								$stm->free_result();
				
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();
								}
								else $FLMFailError .= 'Fulfillments configuration encountered an error retrieving fulfillment removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $FLMFailError .= 'Fulfillments configuration encountered an error preparing to request fulfillment removal.<br />';
					}
					else $FLMFailError .= 'Fulfillments configuration encountered an error initialising fulfillment removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the list view back
					$FLMMode = 'L';
				}
				else { //XCal - Either (N)ew or (M)odify	
					//XCal - If the Name has been passed in we're saving
					if (isset($FLMName)) {
						//$_SESSION['DebugStr'] .= 'Fulfillments configuration: Save Mode<br />';
						if ($FLMMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO rqf_fulfillments (FLM_FLT_ID,FLM_NAME,FLM_DESCRIPTION,FLM_MODE,FLM_LINK_ID,FLM_UPDATED) '.
								'VALUES (?,?,?,?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('issii',$FLMFLTID,$FLMName,$FLMDesc,$FLMMode,$FLMLinkID);
								$stm->execute();
								$stm->free_result();
								$FLMMode = 'L';
							}
							else $FLMFailError .= 'Fulfillments configuration encountered an error preparing to add the fulfillment.<br />';
						}
						else {
							$stm = $sql->prepare(
								'UPDATE rqf_fulfillments SET '.
								'FLM_FLT_ID = ?, FLM_NAME = ?, FLM_DESCRIPTION = ?, '.
								'FLM_MODE = ?, FLM_LINK_ID = ?, FLM_UPDATED = current_timestamp() '.
								'WHERE FLM_ID = ?');
							if ($stm) {
								$stm->bind_param('issiii',$FLMFLTID,$FLMName,$FLMDesc,$FLMEMode,$FLMLinkID,$FLMID);
								$stm->execute();
								$stm->free_result();
								$FLMMode = 'L';
							}
							else $FLMFailError .= 'Fulfillments configuration encountered an error preparing to modify the fulfillment.<br />';
						}
					}				
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Fulfillments configuration: New/Modify Mode<br />';			
						echo('<h4>');
						//XCal - We'll always want the fulfillment type selection list
						$r_ftypes = $sql->query('SELECT FLT_ID,FLT_NAME FROM rqf_fulfillment_types WHERE FLT_SR = 0');
						if ($FLMMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare(
								'SELECT FLM_FLT_ID,FLM_NAME,FLM_DESCRIPTION,FLM_MODE,FLM_LINK_ID,FLM_UPDATED '.
								'FROM rqf_fulfillments WHERE FLM_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$FLMID);
								$stm->execute();
								$stm->bind_result($FLMFLTID,$FLMName,$FLMDesc,$FLMEMode,$FLMLinkID,$FLMUpdated);
								$stm->fetch();
								$stm->free_result();
							}
							else $FLMFailError .= 'Fulfillments configuration encountered an error preparing to get values to modify ('.$sql->error.').<br />';						
						}
						else {
							echo('New');						
							$FLMFLTID = $FLTID;
							$FLMName = '';
							$FLMDesc = '';
							$FLMEMode = 0;
							$FLMLinkID = 0;
							$FLMUpdated = 'Never';
						}
						echo(' Fulfillment</h4><br />'.
							'<input type="hidden" id="FLM_ID" value="'.$FLMID.'" />'.
							'<table>'.
							'<tr><td>'.
							'<table><tr><td class="fieldname">Type</td>'.
							'<td><select id="FLM_FLT_ID">');
						while ($row = $r_ftypes->fetch_assoc()) {
							echo('<option value="'.$row['FLT_ID'].'" ');
							if ($row['FLT_ID'] == $FLMFLTID)
								echo('selected');
							echo('>'.$row['FLT_NAME'].'</option>');
						}
						echo('</select></td></tr>'.
							'<tr><td class="fieldname">Name</td>'.				
							"<td><input id=\"FLM_NAME\" type=\"text\" value=\"$FLMName\" /></td>".
							'</tr>'.
							'<tr><td class="fieldname">Mode</td>'.
							'<td><select id="FLM_MODE">'.
							'<option value="0"');
							if ($FLMEMode == 0)
								echo(' selected');
							echo('>Direct Fulfillment</option>'.
									'<option value="1"');
							if ($FLMEMode == 1)
								echo(' selected');
							echo('>ProTask Status</option>'.
									'<option value="2"');
							if ($FLMEMode == 2)
								echo(' selected');
							echo('>Fulfillment Chain</option>'.
								'</select></td></tr>'.
								'</table></td>'.
								'<td><table><tr><td class="fieldname">Description</td>'.
								"<td><textarea id=\"FLM_DESC\" rows=\"5\" cols=\"45\">$FLMDesc</textarea></td></tr>".
								'</table></td></tr></table>'.
								'<div id="fulfillmodeselector">');
						if ($FLMEMode > 0) {
							echo('Task/Fulfillment ID:<input id="FLM_LINK_ID" type="text" value="'.$FLMLinkID.'" />');
							//XCal - TODO - We need a "micropage" to include here which supports selecting a task or fulfillment by FLMMode
						}
						echo('</div>');
						echo('<input type="button" class="save" value="Save" onclick="'.
							"SaveFulfillment('$FLMTarget','$FLMMode',$FLTID);".
							'" />'.							
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$FLMTarget','$FLMPath','For=FLM&T=$FLMTarget&TID=$FLTID');".
							'" /><br />');
					}
				}
			}
			
			//XCal - If we're not editing or we have saved the details show the list view
			if ($FLMMode == 'L') {	
				//$_SESSION['DebugStr'] .= 'Fulfillments configuration: List Mode<br />';
				$stm = $sql->prepare('SELECT FLT_NAME FROM rqf_fulfillment_types WHERE FLT_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$FLTID);
					$stm->execute();
					$stm->bind_result($TypeName);
					$stm->fetch();
					$stm->free_result();
				}
				else $TypeName = '[Error!]';
				$stm = $sql->prepare('SELECT COUNT(FLM_ID) FROM rqf_fulfillments WHERE FLM_SR = 0 AND FLM_FLT_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$FLTID);
					$stm->execute();
					$stm->bind_result($Count);
					$stm->fetch();
					$stm->free_result();
				}
				else $Count = 0;
				
				echo("<h3>\"$TypeName\" Fulfillments</h3><br />".
					'<input type="button" class="button" value="New Fulfillment" onclick="'.
					"AddEditFulfillment('$FLMTarget','$FLTID',0);".
					'" />'.
					'<br />');
				
				if ($Count == 0)
					echo('No fulfillments of this type found to display.');
				else {
					$LowRec = $Offset+1;
					if ($Count < ($Offset+$Records))
						$HighRec = $Count;
					else $HighRec = $Offset+$Records;
					echo("Showing fulfillments $LowRec to $HighRec of $Count.<br />");
					
					echo('<table><tr><th>Levels</th><th>Fulfillment</th><th>Description</th><th>Mode</th><th>Updated</th>');		
					echo('<th>Edit</th><th>Remove</th>');
					echo('</tr>');		
					$stm = $sql->prepare(
						'SELECT FLM_ID,FLM_NAME,FLM_DESCRIPTION,FLM_MODE,FLM_UPDATED '.
						'FROM rqf_fulfillments '.
						'WHERE FLM_SR = 0 AND FLM_FLT_ID = ? LIMIT ?,?');
					if ($stm) {
						$stm->bind_param('iii',$FLTID,$Offset,$Records);
						$stm->execute();
						$stm->bind_result($FLMID,$FLMName,$FLMDesc,$FLMEMode,$FLMUpdated);
						while ($stm->fetch()) {
							echo('<tr>'.
								'<td><input type="button" class="button" value="Levels" onclick="'.
								"AJAX('$FLMSelTarget','resources/reqful/reqful_fulfill_levels.php','For=FLL&T=$FLMSelTarget&FLM=$FLMID');".
								'" /></td>'.
								"<td>$FLMName</td><td>$FLMDesc</td>");
							switch ($FLMEMode) {
								case 0:
									echo('<td>Direct</td>');
								break;
								
								case 1:
									echo('<td>ProTask</td>');
								break;
								
								case 2:
									echo('<td>Chain</td>');
								break;
								
								default:
									echo('<td>Unknown!</td>');
								break;
							}
							echo("<td>$FLMUpdated</td>");
							echo('<td><input class="button" type="button" value="Edit" onclick="'.
								"AddEditFulfillment('$FLMTarget',$FLTID,$FLMID);".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$FLMTarget','$FLMPath','For=FLM&T=$FLMTarget&TID=$FLTID&ID=$FLMID&M=D');".
								'" /></td>');
							echo('</tr>');
						}				
					}
					else $FLMFailError .= 'Fulfillments configuration failed preparing to get a list of fulfillments to display ('.$sql->error.').<br />';
		
					echo('</table>');
					
					if ($Offset > 0) {
						$PrevOffset = $Offset-$Records;
						echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
							"AJAX('$FLMTarget','$FLMPath','For=FLM&T=$FLMTarget&TID=$FLTID&RO=$PrevOffset&RC=$Records');".
							'" />');
					}
					
					if (($Offset + $Records) < $Count) {
						$NextOffset = $Offset+$Records;
						echo('<input class="nextbutton" type="button" value="Next" onclick="'.
							"AJAX('$FLMTarget','$FLMPath','For=FLM&T=$FLMTarget&TID=$FLTID&RO=$NextOffset&RC=$Records');".
							'" />');
					}
					echo('<br /><div id="FLMSubDiv"></div>');
				}			
			}
			if (strlen($FLMFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$FLMFailError");
		}
		else echo('You do not have the required access rights to configure the requirement fulfillment system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Fulfillments Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$FLMDieError.
		'</article>');
}
?>
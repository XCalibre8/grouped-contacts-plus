<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'FLL')) {
	$For = 'FLL';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$FLLTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$FLLMode = $_REQUEST['M'];
	else $FLLMode = 'L';
	
	if (isset($_REQUEST['ID']))
		$FLLID = $_REQUEST['ID'];
	else $FLLID = 0;
	if (isset($_REQUEST['FLM']))
		$FLLFLMID = $_REQUEST['FLM'];
	if (isset($_REQUEST['LVL']))
		$FLLLevel = $_REQUEST['LVL'];
	if (isset($_REQUEST['Name']))
		$FLLName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$FLLDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['POS'])) //XCal - [XC:POS|Position:U,D|Up,Down;;]
		$FLLMove = $_REQUEST['POS'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
	
	}
elseif (isset($For) and ($For == 'FLL')) {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to FLL then the generic variables have been qualified by the calling page (theoretically)
	
	if (isset($Target))
		$FLLTarget = $Target;
	if (isset($Mode))
		$FLLMode = $Mode;
	elseif (! isset($FLLMode))
		$FLLMode = 'L';
	
	if (isset($ID))
		$FLLID = $ID;
	elseif (! isset($FLLID))
		$FLLID = 0;
	if (isset($FLM))
		$FLLFLMID = $FLM;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($FLLMode))
		$FLLMode = 'L';
	$FLLID = 0;
	unset($FLLFLMID);
	unset($FLLName);
	unset($FLLDesc);
	$Offset = 0;
	$Records = 5;
}
$FLLPath = 'resources/reqful/reqful_fulfill_levels.php';
$FLLDieError = '';
$FLLFailError = '';
$FLLModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($FLLFLMID))
	$FLLDieError .= 'Fulfillment levels has not been passed a fulfillment for which to display levels.<br />';
if (! isset($FLLTarget))
	$FLLDieError .= 'Fulfillment levels has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($FLLMode,$FLLModes))
	$FLLDieError .= "Fulfillment levels has been passed a mode ($FLLMode) which is does not support.<br />";

if ($FLLDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$REQSec = GetAreaSecurity($sql, 4);
		if ($REQSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($FLLMode,array('N','M','D'))) {
				if (in_array($FLLMode,array('M','D')) and (!($FLLID > 0))) {
					$FLLFailError .= 'Fulfillment levels was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
					$FLLMode = 'L';
				}
				elseif ($FLLMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
					//$_SESSION['DebugStr'] .= 'Fulfillments configuration: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
				
						if ($stm->prepare('CALL sp_rqf_rem_fulfill_level(?,@Code,@Msg)')) {
							$stm->bind_param('i', $FLLID);
							if ($stm->execute()) {
								$stm->free_result();
				
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();
								}
								else $FLLFailError .= 'Fulfillment levels encountered an error retrieving level removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $FLLFailError .= 'Fulfillment levels encountered an error preparing to request level removal.<br />';
					}
					else $FLLFailError .= 'Fulfillments configuration encountered an error initialising level removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the list view back
					$FLLMode = 'L';
				}
				else { //XCal - Either (N)ew or (M)odify	
					//XCal - If the Name has been passed in we're saving
					if (isset($FLLName)) {
						//$_SESSION['DebugStr'] .= 'Fulfillment levels: Save Mode<br />';
						$stm = $sql->prepare('SET @FLLID = ?,@FLLLevel = ?,@RCode = 0,@RMsg = \'\'');
						if ($stm) {
							$stm->bind_param('ii',$FLLID,$FLLLevel);
							if ($stm->execute()) {
								$stm->free_result();
								if ($stm->prepare('CALL sp_rqf_addedit_fulfill_level(@FLLID,?,@FLLLevel,?,?,@RCode,@RMsg)')) {
									$stm->bind_param('iss',$FLLFLMID,$FLLName,$FLLDesc);
									if ($stm->execute()) {								
										$stm->free_result();
										if ($stm->prepare('SELECT @RCode,@RMsg')) { //XCal - We can also get the inserted ID and adjusted level here if needed
											if ($stm->execute()) {
												$stm->bind_result($RCode,$RMsg);
												$stm->fetch();
												$stm->free_result();
												echo("New/Modify result: $RMsg<br />");
												$FLLMode = 'L';
											}
											else $FLLFailError  .= 'Fulfillment levels failed to retrieve the new/modify results ('.$stm->error.').<br />';
										}
										else $FLLFailError .= 'Fulfillment levels encountered an error preparing to retrieve the new/modify results ('.$stm->error.').<br />'; 
									}
									else $FLLFailError .= 'Fulfillment levels failed performing the new/modify action ('.$stm->error.').<br />';
								}
								else $FLLFailError .= 'Fulfillment levels encountered an error preparing the new/modify action ('.$stm->error.').<br />';
							}
							else $FLLFailError .= 'Fulfillment levels failed to initialise the new/modify action ('.$stm->error.').<br />';
						}
						else $FLLFailError .= 'Fulfillment levels encountered an error initialising the new/modify action ('.$sql->error.').<br />';
					}				
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Fulfillment levels: New/Modify Mode<br />';			
						echo('<h4>');
						$stm = $sql->prepare('SELECT COUNT(FLL_ID) FROM rqf_fulfillment_levels WHERE FLL_FLM_ID = ?');
						if ($stm) {
							$stm->bind_param('i',$FLLFLMID);
							if ($stm->execute()) {
								$stm->bind_result($FLLMaxLevel);
								$stm->fetch();
								$stm->free_result();
								if ($FLLMode == 'N')
									$FLLMaxLevel++;
							}
							else $FLLMaxLevel = 100;
						}
						else $FLLMaxLevel = 100;
						if ($FLLMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare(
								'SELECT FLL_LEVEL,FLL_NAME,FLL_DESCRIPTION,FLL_UPDATED '.
								'FROM rqf_fulfillment_levels WHERE FLL_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$FLLID);
								$stm->execute();
								$stm->bind_result($FLLLevel,$FLLName,$FLLDesc,$FLLUpdated);
								$stm->fetch();
								$stm->free_result();
							}
							else $FLLFailError .= 'Fulfillment levels encountered an error preparing to get values to modify ('.$sql->error.').<br />';						
						}
						else {
							echo('New');
							$FLLLevel = $FLLMaxLevel;
							$FLLName = '';
							$FLLDesc = '';
							$FLLUpdated = 'Never';
						}
						echo(' Fulfillment</h4><br />'.
							'<input type="hidden" id="FLL_ID" value="'.$FLLID.'" />'.
							'<table>'.
							'<tr><td>'.
							'<table><tr><td class="fieldname">Level</td>'.
							'<td><input id="FLL_LEVEL" type="number" min="1" max="'.$FLLMaxLevel.'" step="1" value="'.$FLLLevel.'" /></td></tr>'.
							'<tr><td class="fieldname">Name</td>'.				
							"<td><input id=\"FLL_NAME\" type=\"text\" value=\"$FLLName\" /></td>".
							'</tr>'.
							'</table></td>'.
							'<td><table><tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"FLL_DESC\" rows=\"5\" cols=\"45\">$FLLDesc</textarea></td></tr>".
							'</table></td></tr></table>');
						echo('<input type="button" class="save" value="Save" onclick="'.
							"SaveFulfillmentLevel('$FLLTarget','$FLLMode',$FLLFLMID);".
							'" />'.							
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$FLLTarget','$FLLPath','For=FLL&T=$FLLTarget&FLM=$FLLFLMID&M=L');".
							'" /><br />');
					}
				}
			}
			
			//XCal - If we're not editing or we have saved the details show the list view
			if ($FLLMode == 'L') {	
				//$_SESSION['DebugStr'] .= 'Fulfillment Levels: List Mode<br />';
				$stm = $sql->prepare('SELECT FLM_NAME FROM rqf_fulfillments WHERE FLM_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$FLLFLMID);
					$stm->execute();
					$stm->bind_result($FulfillName);
					$stm->fetch();
					$stm->free_result();
				}
				else $FulfillName = '[Error!]';
				$stm = $sql->prepare('SELECT COUNT(FLL_ID) FROM rqf_fulfillment_levels WHERE FLL_SR = 0 AND FLL_FLM_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$FLLFLMID);
					$stm->execute();
					$stm->bind_result($Count);
					$stm->fetch();
					$stm->free_result();
				}
				else $Count = 0;
				
				echo("<h3>\"$FulfillName\" Levels</h3><br />".
					'<input type="button" class="button" value="New Level" onclick="'.
					"AddEditFulfillmentLevel('$FLLTarget','$FLLFLMID',0);".
					'" />'.
					'<br />');
				
				if ($Count == 0)
					echo('No fulfillment levels configured for this fulfillment.');
				else {
					$LowRec = $Offset+1;
					if ($Count < ($Offset+$Records))
						$HighRec = $Count;
					else $HighRec = $Offset+$Records;
					echo("Showing fulfillment levels $LowRec to $HighRec of $Count.<br />");
					
					echo('<table><tr><th>Levels</th><th>Fulfillment</th><th>Description</th><th>Updated</th>');		
					echo('<th>Edit</th><th>Remove</th>');
					echo('</tr>');		
					$stm = $sql->prepare(
						'SELECT FLL_ID,FLL_LEVEL,FLL_NAME,FLL_DESCRIPTION,FLL_UPDATED '.
						'FROM rqf_fulfillment_levels '.
						'WHERE FLL_SR = 0 AND FLL_FLM_ID = ? LIMIT ?,?');
					if ($stm) {
						$stm->bind_param('iii',$FLLFLMID,$Offset,$Records);
						$stm->execute();
						$stm->bind_result($FLLID,$FLLLevel,$FLLName,$FLLDesc,$FLLUpdated);
						while ($stm->fetch()) {
							echo('<tr>'.					
								"<td>$FLLLevel</td><td>$FLLName</td><td>$FLLDesc</td><td>$FLLUpdated</td>");
							echo('<td><input class="button" type="button" value="Edit" onclick="'.
								"AddEditFulfillmentLevel('$FLLTarget',$FLLFLMID,$FLLID);".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$FLLTarget','$FLLPath','For=FLL&T=$FLLTarget&FLM=$FLLFLMID&ID=$FLLID&M=D');".
								'" /></td>');
							echo('</tr>');
						}				
					}
					else $FLLFailError .= 'Fulfillment levels configuration failed preparing to get a list of levels to display ('.$sql->error.').<br />';
		
					echo('</table>');
					
					if ($Offset > 0) {
						$PrevOffset = $Offset-$Records;
						echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
							"AJAX('$FLLTarget','$FLLPath','For=FLL&T=$FLLTarget&FLM=$FLLFLMID&RO=$PrevOffset&RC=$Records');".
							'" />');
					}
					
					if (($Offset + $Records) < $Count) {
						$NextOffset = $Offset+$Records;
						echo('<input class="nextbutton" type="button" value="Next" onclick="'.
							"AJAX('$FLLTarget','$FLLPath','For=FLL&T=$FLLTarget&FLM=$FLLFLMID&RO=$NextOffset&RC=$Records');".
							'" />');
					}
					echo('<br />');
				}			
			}
			if (strlen($FLLFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$FLLFailError");
		}
		else echo('You do not have the required access rights to configure the requirement fulfillment system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Fulfillment Levels Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$FLLDieError.
		'</article>');
}
?>
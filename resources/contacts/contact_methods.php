<?php
//XCal - We only want to pay attention to request variables if they're for the contact methods page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'CPT')) {
	$For = 'CPT';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$CPTTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$CPTMode = $_REQUEST['M'];
	else $CPTMode = 'L';
	
	if (isset($_REQUEST['ID']))
		$CPTID = $_REQUEST['ID'];
	else $CPTID = -1;
	if (isset($_REQUEST['Name']))
		$CPTName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$CPTDesc = $_REQUEST['Desc'];
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
	if (isset($For) and ($For == 'CPT')) {
		if (isset($Target))
			$CPTTarget = $Target;
		if (isset($Mode))
			$CPTMode = $Mode;
		elseif (! isset($CPTMode))
			$CPTMode = 'L';
		if (isset($ID))
			$CPTID = $ID;
		elseif (! isset($CPTID))
			$CPTID = -1;
		if (! isset($Offset))
			$Offset = 0;
		if (! isset($Records))
			$Records = 5;
	}
	else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
		$CPTMode = 'L';
		$CPTID = -1;
		unset($CPTName);
		unset($CPTDesc);
		$Offset = 0;
		$Records = 5;
	}
}
$CPTPath = 'resources/contacts/contact_methods.php';
$CPTDieError = '';
$CPTFailError = '';
$CPTModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($CPTTarget))
	$CPTDieError .= 'Contact methods has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($CPTMode,$CPTModes))
	$CPTDieError .= "Contact methods has been passed a mode ($CPTMode) which is does not support.<br />";	

if ($CPTDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql, 1);
		if ($CONSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($CPTMode,array('N','M','D'))) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($CPTMode,array('M','D')) and (!($CPTID > 0))) {
					$CPTFailError .= 'Contact methods was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
					$CPTMode = 'L';
				}
				elseif ($CPTMode == 'D') { //XCal -Perform the 'delete' and return to list mode
					//$_SESSION['DebugStr'] .= 'Contact Methods: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
							
						if ($stm->prepare('CALL sp_con_rem_contact_point_type(?,@Code,@Msg)')) {
							$stm->bind_param('i', $CPTID);
							if ($stm->execute()) {
								$stm->free_result();
						
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();
								}
								else $CPTFailError .= 'Contact methods encountered an error retrieving method removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $CPTFailError .= 'Contact methods encountered an error preparing to request method removal.<br />';
					}
					else $CPTFailError .= 'Contact methods encountered an error initialising method removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the list view back
					$CPTMode = 'L';				
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($CPTName)) {
						//$_SESSION['DebugStr'] .= 'Contact Types: Save Mode<br />';
						if ($CTTMode = 'N') {
							$stm = $sql->prepare(
								'INSERT INTO con_contact_point_types (CPT_NAME,CPT_DESCRIPTION,CPT_UPDATED) '.
								'VALUES (?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('ss',$CPTName,$CPTDesc);
								$stm->execute();
								$stm->free_result();
								$CPTMode = 'L';
							}
							else $CPTFailError .= 'Contact methods encountered an error preparing to add the method.<br />';
						}
						else {
							$stm = $sql->prepare(
								'UPDATE con_contact_point_types SET '.
								'CPT_NAME = ?, CPT_DESCRIPTION = ?, CPT_UPDATED = current_timestamp() '.
								'WHERE CPT_ID = ?');
							if ($stm) {
								$stm->bind_param('ssi',$CPTName,$CPTDesc,$CPTID);
								$stm->execute();
								$stm->free_result();
								$CPTMode = 'L';
							}
							else $CPTFailError .= 'Contact methods encountered an error preparing to modify the method.<br />';
						}
					}
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Contact Methods: New/Modify Mode<br />';
						echo('<h4>');
						if ($CPTMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare('SELECT CPT_NAME,CPT_DESCRIPTION FROM con_contact_point_types WHERE CPT_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$CPTID);
								$stm->execute();
								$stm->bind_result($CPTName,$CPTDesc);
								$stm->fetch();
								$stm->free_result();
							}
							else $CPTFailError .= 'Contact methods encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$CPTName = '';
							$CPTDesc = '';
						}
						echo(' Contact Method</h4><br />'.
							'<input type="hidden" id="CPT_ID" value="'.$CPTID.'" />'.
							'<table>'.
							'<tr><td class="fieldname">Name</td>'.
							"<td><input id=\"CPT_NAME\" type=\"text\" value=\"$CPTName\" /></td></tr>".
							'<tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"CPT_DESC\">$CPTDesc</textarea></td></tr></table>".
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveContactMethod('$CPTTarget','$CPTMode');".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$CPTTarget','$CPTPath','For=CPT&T=$CPTTarget&M=L');".
							'" /><br />');
					}
				}
			}
			
			//XCal - Either list mode was requested or the (N)ew,(M)odify or (D)delete mode has finished its work
			if ($CPTMode == 'L') {
				//$_SESSION['DebugStr'] .= 'Contact Methods: List Mode<br />';
				$res = $sql->query('SELECT COUNT(CPT_ID) FROM con_contact_point_types WHERE CPT_SR = 0');
				if (! $res) {
					$CPTFailError .= 'Contact methods failed to get a count of methods ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				echo('<input type="button" class="button" value="New Contact Method" onclick="'.
					"AJAX('$CPTTarget','$CPTPath','For=CPT&T=$CPTTarget&M=N');".
					'" /><br />');
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing contact methods $LowRec to $HighRec of $Count.<br />");
						
				echo('<table><tr><th>Method</th><th>Description</th><th>Updated</th><th>Edit</th><th>Remove</th></tr>');
				$stm = $sql->prepare(
					'SELECT CPT_ID,CPT_NAME,CPT_DESCRIPTION,CPT_UPDATED '.
					'FROM con_contact_point_types '.
					'WHERE CPT_SR = 0 LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					$stm->execute();
					$stm->bind_result($CPTID,$CPTName,$CPTDesc,$CPTUpdated);
					while ($stm->fetch()) {
						echo('<tr>'.
								"<td>$CPTName</td><td>$CPTDesc</td><td>$CPTUpdated</td>".
								'<td><input class="button" type="button" value="Edit" onclick="'.
								"AJAX('$CPTTarget','$CPTPath','For=CPT&T=$CPTTarget&M=M&ID=$CPTID');".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$CPTTarget','$CPTPath','For=CPT&T=$CPTTarget&M=D&ID=$CPTID');".
								'" /></td>'.
							'</tr>');
					}				
				}
				else $CPTFailError .= 'Contact methods failed to get a list of methods to display ('.$sql->error.').<br />';
				echo('</table>');
				
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
							"AJAX('$CPTTarget','$CPTPath','For=CPT&T=$CPTTarget&M=L&RO=$PrevOffset&RC$Records');".
							'" />');
				}
				
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
							"AJAX('$CPTTarget','$CPTPath','For=CPT&T=$CPTTarget&M=L&RO=$NextOffset&RC$Records');".
							'" />');
				}
				echo('<br />');
			}	
			if (strlen($CPTFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$CPTFailError");
		}
		else echo('You do not have the required access rights to configure the contacts system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Contact Methods Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$CPTDieError.
		'</article>');
}
?>
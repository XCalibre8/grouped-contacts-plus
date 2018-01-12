<?php
//XCal - We only want to pay attention to request variables if they're for the contact types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'CTT')) {
	$For = 'CTT';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$CTTTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$CTTMode = $_REQUEST['M'];
	else $CTTMode = 'L';
	
	if (isset($_REQUEST['ID']))
		$CTTID = $_REQUEST['ID'];
	else $CTTID = -1;		
	if (isset($_REQUEST['Name']))
		$CTTName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$CTTDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to CTT then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'CTT')) {
		if (isset($Target))
			$CTTTarget = $Target;
		if (isset($Mode))
			$CTTMode = $Mode;
		elseif (! isset($CTTMode))
			$CTTMode = 'L';
		if (isset($ID))
			$CTTID = $ID;
		elseif (! isset($CTTID))
			$CTTID = -1;
		if (! isset($Offset))
			$Offset = 0;
		if (! isset($Records))
			$Records = 5;
	}
	else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
		$CTTMode = 'L';
		$CTTID = -1;
		unset($CTTName);
		unset($CTTDesc);
		$Offset = 0;
		$Records = 5;
	}
}
$CTTPath = 'resources/contacts/contact_types.php';
$CTTDieError = '';
$CTTFailError = '';
$CTTModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($CTTTarget))
	$CTTDieError .= 'Contact types has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($CTTMode,$CTTModes))
	$CTTDieError .= "Contact types has been passed a mode ($CTTMode) which is does not support.<br />";

if ($CTTDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql, 1);
		if ($CONSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($CTTMode,array('N','M','D'))) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($CTTMode,array('M','D')) and (!($CTTID > 0))) {
					$CTTFailError .= 'Contact types was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
					$CTTMode = 'L';
				}
				elseif ($CTTMode == 'D') { //XCal - Perform the 'delete' and return to list mode
					//$_SESSION['DebugStr'] .= 'Contact Types: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
						
						if ($stm->prepare('CALL sp_con_rem_contact_type(?,@Code,@Msg)')) {
							$stm->bind_param('i', $CTTID);
							if ($stm->execute()) {
								$stm->free_result();
							
								if ($stm->prepare('SELECT @Code,@Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();
								}
								else $CTTFailError .= 'Contact types encountered an error retrieving type removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $CTTFailError .= 'Contact types encountered an error preparing to request type removal.<br />';					
					}
					else $CTTFailError .= 'Contact types encountered an error initialising type removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the list view back
					$CTTMode = 'L';				
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($CTTName)) {
						//$_SESSION['DebugStr'] .= 'Contact Types: Save Mode<br />';					
						if ($CTTMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO con_contact_types (CTT_NAME,CTT_DESCRIPTION,CTT_UPDATED) '.
								'VALUES (?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('ss',$CTTName,$CTTDesc);
								$stm->execute();
								$stm->free_result();
								$CTTMode = 'L';							
							}
							else $CTTFailError .= 'Contact types encountered an error preparing to add the type.<br />';
						}
						else {
							$stm = $sql->prepare(
								'UPDATE con_contact_types SET '.
								'CTT_NAME = ?, CTT_DESCRIPTION = ?, CTT_UPDATED = current_timestamp() '.
								'WHERE CTT_ID = ?');
							if ($stm) {
								$stm->bind_param('ssi',$CTTName,$CTTDesc,$CTTID);
								$stm->execute();
								$stm->free_result();
								$CTTMode = 'L';
							}
							else $CTTFailError .= 'Contact types encountered an error preparing to modify the type.<br />';
						}
					}
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Contact Types: New/Modify Mode<br />';
						echo('<h4>');
						if ($CTTMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare('SELECT CTT_NAME,CTT_DESCRIPTION FROM con_contact_types WHERE CTT_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$CTTID);
								$stm->execute();
								$stm->bind_result($CTTName,$CTTDesc);
								$stm->fetch();
								$stm->free_result();
							}
							else $CTTFailError .= 'Contact types encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$CTTName = '';
							$CTTDesc = '';
						}
						echo(' Contact Type</h4><br />'.
								'<input type="hidden" id="CTT_ID" value="'.$CTTID.'" />'.
								'<table>'.
								'<tr><td class="fieldname">Name</td>'.
								"<td><input id=\"CTT_NAME\" type=\"text\" value=\"$CTTName\" /></td></tr>".
								'<tr><td class="fieldname">Description</td>'.
								"<td><textarea id=\"CTT_DESC\">$CTTDesc</textarea></td></tr></table>".
								'<input type="button" class="save" value="Save" onclick="'.
								"SaveContactType('$CTTTarget','$CTTMode');".
								'" />'.
								'<input type="button" class="cancel" value="Cancel" onclick="'.
								"AJAX('$CTTTarget','$CTTPath','For=CTT&T=$CTTTarget&M=L');".
								'" />');					
					}
				}			
			}
			
			//XCal - Either list mode was requested or the (N)ew,(M)odify or (D)delete mode has finished its work
			if ($CTTMode == 'L') {		
				//$_SESSION['DebugStr'] .= 'Contact Types: List Mode<br />';
				$res = $sql->query('SELECT COUNT(CTT_ID) FROM con_contact_types WHERE CTT_SR = 0');
				if (! $res) {
					$CTTFailError .= 'Contact types failed to get a count of types ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				echo('<input type="button" class="button" value="New Contact Type" onclick="'.
					"AJAX('$CTTTarget','$CTTPath','For=CTT&T=$CTTTarget&M=N');".
					'" /><br />');
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing contact types $LowRec to $HighRec of $Count.<br />");
						
				echo('<table><tr><th>Type</th><th>Description</th><th>Updated</th><th>Edit</th><th>Remove</th></tr>');
				$stm = $sql->prepare(			
					'SELECT CTT_ID,CTT_NAME,CTT_DESCRIPTION,CTT_UPDATED '.
					'FROM con_contact_types '.
					'WHERE CTT_SR = 0 LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					$stm->execute();
					$stm->bind_result($CTTID,$CTTName,$CTTDesc,$CTTUpdated);
					while ($stm->fetch()) {
						echo('<tr>'.
								"<td>$CTTName</td><td>$CTTDesc</td><td>$CTTUpdated</td>".
								'<td><input class="button" type="button" value="Edit" onclick="'.
								"AJAX('$CTTTarget','$CTTPath','For=CTT&T=$CTTTarget&M=M&ID=$CTTID');".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$CTTTarget','$CTTPath','For=CTT&T=$CTTTarget&M=D&ID=$CTTID');".
								'" /></td>'.							
							'</tr>');					
					}
				}
				else $CTTFailError .= 'Contact types failed to get a list of types to display ('.$sql->error.').<br />';
				echo('</table>');
				
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
						"AJAX('$CTTTarget','$CTTPath','For=CTT&T=$CTTTarget&M=L&RO=$PrevOffset&RC$Records');".
						'" />');
				}
					
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
						"AJAX('$CTTTarget','$CTTPath','For=CTT&T=$CTTTarget&M=L&RO=$NextOffset&RC$Records');".
						'" />');
				}
				echo('<br />');
			}
			if (strlen($CTTFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$CTTFailError");
		}
		else echo('You do not have the required access rights to configure the contacts system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Contact Types Code Problem</h2>'.
			'<div class="article-info">'.
				'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$CTTDieError.
		'</article>');
}
?>
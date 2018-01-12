<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'ROL')) {
	$For = 'ROL';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$ROLTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D,C|List(Default),New,Modify,Delete,Configure(Changes Select Target and enables N,M,D);;]
	if (isset($_REQUEST['M']))
		$ROLMode = $_REQUEST['M'];
	else $ROLMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$ROLSelTarget = $_REQUEST['ST'];
	else $ROLSelTarget = 'ROLSubDiv';
	
	if (isset($_REQUEST['ID']))
		$ROLID = $_REQUEST['ID'];
	else $ROLID = -1;
	if (isset($_REQUEST['Name']))
		$ROLName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$ROLDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
	}
elseif (isset($For) and ($For == 'ROL')) {
	if (isset($Target))
		$ROLTarget = $Target;
	if (isset($Mode))
		$ROLMode = $Mode;
	elseif (! isset($ROLMode))
		$ROLMode = 'L';
	if (isset($SelTarget))
		$ROLSelTarget = $SelTarget;
	else $ROLSelTarget = 'ROLSubDiv';
	
	if (isset($ID))
		$ROLID = $ID;
	elseif (! isset($ROLID))
	$ROLID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($ROLMode))
		$ROLMode = 'L';
	$ROLID = -1;
	if (! isset($ROLSelTarget))
		$ROLSelTarget = 'ROLSubDiv';
	unset($ROLName);
	unset($ROLDesc);
	$Offset = 0;
	$Records = 5;
}

$ROLPath = 'resources/users/user_roles.php';
$ROLDieError = '';
$ROLFailError = '';
$ROLModes = array('L','N','M','D','C');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($ROLTarget))
	$ROLDieError .= 'User roles has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($ROLMode,$ROLModes))
	$ROLDieError .= "User roles has been passed a mode ($ROLMode) which is does not support.<br />";

if ($ROLDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$USRSec = GetAreaSecurity($sql, 6);
		if ($USRSec['Config']) {
			echo('<h4>User Roles</h4>'.
				'<p>Create and maintain roles to control which system cores users have access to and what they can do within each.</p>');
			
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($ROLMode,array('N','M','D'))) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($ROLMode,array('M','D')) and (!($ROLID > 0))) {
					$ROLFailError .= 'User roles was passed a modify or delete request without an ID specified. Changed to select mode.<br />';
					$ROLMode = 'C'; //XCal - We can only do these actions from configuration list mode, so we go to that not L
				}
				elseif ($ROLMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
					//$_SESSION['DebugStr'] .= 'User Roles: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
							
						if ($stm->prepare('CALL sp_usr_rem_role(?,@Code,@Msg)')) {
							$stm->bind_param('i', $ROLID);						
							if ($stm->execute()) {
								$stm->free_result();
					
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();							
								}
								else $ROLFailError .= 'User roles encountered an error retrieving role removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $ROLFailError .= 'User roles encountered an error preparing to request role removal.<br />';
					}
					else $ROLFailError .= 'User roles encountered an error initialising role removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the config list view back
					$ROLMode = 'C';
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($ROLName)) {
						//$_SESSION['DebugStr'] .= 'User Roles: Save Mode<br />';
						if ($ROLMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO usr_roles (ROL_NAME,ROL_DESCRIPTION,ROL_UPDATED) '.
								'VALUES (?,?,current_timestamp())');
							if ($stm) {						
								$stm->bind_param('ss',$ROLName,$ROLDesc);
								$stm->execute();
								$stm->free_result();
								$ROLMode = 'C';
							}
							else $ROLFailError .= 'User roles encountered an error preparing to add the role.<br />';
						}
						else {
							$stm = $sql->prepare(
									'UPDATE usr_roles SET '.
									'ROL_NAME = ?, ROL_DESCRIPTION = ?, ROL_UPDATED = current_timestamp() '.
									'WHERE ROL_ID = ?');							
							if ($stm) {
								$stm->bind_param('ssi',$ROLName,$ROLDesc,$ROLID);
								$stm->execute();
								$stm->free_result();
								$ROLMode = 'C';
							}
							else $ROLFailError .= 'User roles encountered an error preparing to modify the role.<br />';
						}
					}
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'User Roles: New/Modify Mode<br />';						
						echo('<h4>');
						if ($ROLMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare('SELECT ROL_NAME,ROL_DESCRIPTION FROM usr_roles WHERE ROL_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$ROLID);
								$stm->execute();
								$stm->bind_result($ROLName,$ROLDesc);
								$stm->fetch();
								$stm->free_result();
							}
							else $ROLFailError .= 'User roles encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$ROLName = '';
							$ROLDesc = '';
						}
						echo(' Role</h4><br />'.
							'<input type="hidden" id="ROL_ID" value="'.$ROLID.'" />'.
							'<table>'.
							'<tr><td class="fieldname">Name</td>'.
							"<td><input id=\"ROL_NAME\" type=\"text\" value=\"$ROLName\" /></td></tr>".
							'<tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"ROL_DESC\" rows=\"5\" cols=\"50\">$ROLDesc</textarea></td></tr>".
							'</table>'.
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveUserRole('$ROLTarget','$ROLMode');".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$ROLTarget','$ROLPath','For=ROL&T=$ROLTarget&M=C');".
							'" /><br />');
					}
				}
			}
			
			//XCal - We're not editing or we have saved the details so show the list view
			if (in_array($ROLMode,array('L','C'))) {		
				//$_SESSION['DebugStr'] .= 'User Roles: List Mode<br />';
				$res = $sql->query('SELECT COUNT(ROL_ID) FROM usr_roles WHERE ROL_SR = 0');
				if (! $res) {
					$ROLFailError .= 'User roles failed to get a count of roles ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				if ($ROLMode == 'C') 
					echo('<input type="button" class="button" value="New Role" onclick="'.
						"AJAX('$ROLTarget','$ROLPath','For=ROL&T=$ROLTarget&M=N');".
						'" /><br />');
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing user roles $LowRec to $HighRec of $Count.<br />");
						
				echo('<table><tr><th>Access</th><th>Role</th><th>Description</th><th>Updated</th>');
				if ($ROLMode == 'C')
					echo('<th>Edit</th><th>Remove</th>');
				echo('</tr>');
				$stm = $sql->prepare(
					'SELECT ROL_ID,ROL_NAME,ROL_DESCRIPTION,ROL_UPDATED '.
					'FROM usr_roles '.
					'WHERE ROL_SR = 0 LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					$stm->execute();
					$stm->bind_result($ROLID,$ROLName,$ROLDesc,$ROLUpdated);
					while ($stm->fetch()) {
						echo('<tr>'.
								'<td><input type="button" class="button" value="Access" onclick="');
						echo("AJAX('$ROLSelTarget','resources/users/role_access.php','For=RAC&T=$ROLSelTarget&ID=$ROLID');");
						echo('" /></td>'.
								"<td>$ROLName</td><td>$ROLDesc</td><td>$ROLUpdated</td>");
						if ($ROLMode == 'C')
							echo('<td><input class="button" type="button" value="Edit" onclick="'.
								"AJAX('$ROLTarget','$ROLPath','For=ROL&T=$ROLTarget&M=M&ID=$ROLID');".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$ROLTarget','$ROLPath','For=ROL&T=$ROLTarget&M=D&ID=$ROLID');".
								'" /></td>');
						echo('</tr>');
					}	
				}
				else $ROLFailError .= 'User roles failed preparing to get a list of roles to display ('.$sql->error.').<br />';
				
				echo('</table>');
				
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
						"AJAX('$ROLTarget','$ROLPath','For=ROL&T=$ROLTarget&M=$ROLMode&RO=$PrevOffset&RC=$Records');".
						'" />');
				}
				
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
						"AJAX('$ROLTarget','$ROLPath','For=ROL&T=$ROLTarget&M=$ROLMode&RO=$NextOffset&RC=$Records');".
						'" />');
				}
				echo('<br /><div id="ROLSubDiv"></div>');
			}
			if (strlen($ROLFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$ROLFailError");
		}
		else echo('You do not have the required access right to configure the users system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>User Roles Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$ROLDieError.
		'</article>');
}
?>
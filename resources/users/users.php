<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'USR')) {
	$For = 'USR';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$USRTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D,C|List(Default),New,Modify,Delete,Configure(Changes Select Target and enables N,M,D);;]
	if (isset($_REQUEST['M']))
		$USRMode = $_REQUEST['M'];
	else $USRMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$USRSelTarget = $_REQUEST['ST'];
	else $USRSelTarget = 'USRSubDiv';
	
	if (isset($_REQUEST['ID']))
		$USRID = $_REQUEST['ID'];
	else $USRID = -1;
	if (isset($_REQUEST['USN']))
		$USRUserName = $_REQUEST['USN'];
	if (isset($_REQUEST['PWD']))
		$USRPassword = $_REQUEST['PWD'];
	if (isset($_REQUEST['PW2']))
		$USRPassword2 = $_REQUEST['PW2'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
	}
elseif (isset($For) and ($For == 'USR')) {
	if (isset($Target))
		$USRTarget = $Target;
	if (isset($Mode))
		$USRMode = $Mode;
	elseif (! isset($USRMode))
		$USRMode = 'L';
	if (isset($SelTarget))
		$USRSelTarget = $SelTarget;
	else $USRSelTarget = 'USRSubDiv';
	
	if (isset($ID))
		$USRID = $ID;
	elseif (! isset($USRID))
	$USRID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($USRMode))
		$USRMode = 'L';
	$USRID = -1;
	if (! isset($USRSelTarget))
		$USRSelTarget = 'USRSubDiv';
	unset($USRUserName);
	unset($USRPassword);
	$Offset = 0;
	$Records = 5;
}

$USRPath = 'resources/users/users.php';
$USRDieError = '';
$USRFailError = '';
$USRModes = array('L','N','M','D','C');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($USRTarget))
	$USRDieError .= 'Users has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($USRMode,$USRModes))
	$USRDieError .= "Users has been passed a mode ($USRMode) which is does not support.<br />";

if ($USRDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	echo('<h4>Users</h4>'.
		'<p>Create and maintain users and assign their roles to control system access.</p>');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$USRSec = GetAreaSecurity($sql, 6);
		if ($USRSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($USRMode,array('N','M','D'))) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($USRMode,array('M','D')) and (!($USRID > 0))) {
					$USRFailError .= 'Users was passed a modify or delete request without an ID specified. Changed to select mode.<br />';
					$USRMode = 'C'; //XCal - We can only do these actions from configuration list mode, so we go to that not L
				}
				elseif ($USRMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
					//$_SESSION['DebugStr'] .= 'Users : Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
							
						if ($stm->prepare('CALL sp_usr_rem_user(?,@Code,@Msg)')) {
							$stm->bind_param('i', $USRID);						
							if ($stm->execute()) {
								$stm->free_result();
					
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();							
								}
								else $USRFailError .= 'Users encountered an error retrieving user removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $USRFailError .= 'Users encountered an error preparing to request user removal.<br />';
					}
					else $USRFailError .= 'Users encountered an error initialising user removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the config list view back
					$USRMode = 'C';
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($USRUserName)) {				
						//$_SESSION['DebugStr'] .= 'Users: Save Mode<br />';
						//XCal - Validate that the username is unique and the passwords match
						$ValError = '';
						$stm = $sql->prepare('SELECT COUNT(USR_ID) FROM usr_users '.
							'WHERE USR_ID <> ? AND USR_USERNAME = ?');
						if ($stm) {
							$stm->bind_param('is',$USRID,$USRUserName);
							if ($stm->execute()) {
								$stm->bind_result($CCount);
								$stm->fetch();
								$stm->free_result();
								if ($CCount > 0)
									$ValError .= 'Username already in use.<br />';
							}
						}
						
						if (isset($USRPassword)) {
							if ((! isset($USRPassword2)) or ($USRPassword != $USRPassword2))
								$ValError .= 'Passwords do not match.<br />';						 
							$HashPass = hash('sha256', $USRPassword);
						}
						//XCal - Only insert or save if there are no validation errors
						if (strlen($ValError) == 0) {
							if ($USRMode == 'N') {
								//XCal - TODO - We should check for the existence of the username and whether password is being changed
								$stm = $sql->prepare(
									'INSERT INTO usr_users (USR_USERNAME,USR_PASSHASH,USR_UPDATED) '.
									'VALUES (?,?,current_timestamp())');
								if ($stm) {			
									
									$stm->bind_param('ss',$USRUserName,$HashPass);
									$stm->execute();
									$stm->free_result();
									$USRMode = 'C';
								}
								else $USRFailError .= 'Users encountered an error preparing to add the user.<br />';
							}
							else {
								$stm = $sql->prepare(
										'UPDATE usr_users SET '.
										'USR_USERNAME = ?, USR_PASSHASH = ?, USR_UPDATED = current_timestamp() '.
										'WHERE USR_ID = ?');							
								if ($stm) {
									$stm->bind_param('ssi',$USRUserName,$HashPass,$USRID);
									$stm->execute();
									$stm->free_result();
									$USRMode = 'C';
								}
								else $USRFailError .= 'Users encountered an error preparing to modify the user.<br />';
							}
						}
					}
					
					if ($USRMode != 'C') { //XCal - If the mode has not been changed to C we still need the input screen
						//$_SESSION['DebugStr'] .= 'Users: New/Modify Mode<br />';						
						echo('<h4>');
						if ($USRMode == 'M') {
							echo('Edit');
							if (! isset($USRUserName)) {
								$stm = $sql->prepare('SELECT USR_USERNAME FROM usr_users WHERE USR_ID = ?');
								if ($stm) {
									$stm->bind_param('i',$USRID);
									$stm->execute();
									$stm->bind_result($USRUserName);
									$stm->fetch();
									$stm->free_result();
								}
								else $USRFailError .= 'Users encountered an error preparing to get values to modify ('.$sql->error.').<br />';
							}
						}
						else {
							echo('New');
							if (! isset($USRUserName))
								$USRUserName = '';
							if (! isset($USRPassword))
								$USRPassword = '';
							if (! isset($USRPassword2))
								$USRPassword2 = '';
						}
						echo(' User</h4><br />'.
							'<input type="hidden" id="USR_ID" value="'.$USRID.'" />');
						if (isset($ValError) and (strlen($ValError) > 0))
							echo($ValError);
						echo('<table>'.
							'<tr><td class="fieldname">Name</td>'.
							"<td><input id=\"USR_USERNAME\" type=\"text\" value=\"$USRUserName\" /></td></tr>".
							'<tr><td class="fieldname">Password</td>'.
							'<td><div id="Passwords">');
						if (! isset($USRPassword))
							echo('<input type="button" class="button" value="Set" onclick="'.
								'EditPassword(\'Passwords\');'.
								'" />');
						else echo('<input id="USR_PASSWORD" type="password" value="'.$USRPassword.'" /><br /><input id="USR_PASSWORD2" type="password" value="'.$USRPassword2.'" />(again)');
						echo('</div></td></tr>'.
							'</table>'.
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveUser('$USRTarget','$USRMode');".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$USRTarget','$USRPath','For=USR&T=$USRTarget&M=C');".
							'" /><br />');
					}
				}
			}
			
			//XCal - We're not editing or we have saved the details so show the list view
			if (in_array($USRMode,array('L','C'))) {		
				//$_SESSION['DebugStr'] .= 'User Roles: List Mode<br />';
				$res = $sql->query('SELECT COUNT(USR_ID) FROM usr_users WHERE USR_SR = 0');
				if (! $res) {
					$USRFailError .= 'Users failed to get a count of users ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				if ($USRMode == 'C') 
					echo('<input type="button" class="button" value="New User" onclick="'.
						"AJAX('$USRTarget','$USRPath','For=USR&T=$USRTarget&M=N');".
						'" /><br />');
				
				if ($Count > 0) {
					$LowRec = $Offset+1;
					if ($Count < ($Offset+$Records))
						$HighRec = $Count;
					else $HighRec = $Offset+$Records;
					echo("Showing users $LowRec to $HighRec of $Count.<br />");
							
					echo('<table><tr><th>Roles/Accounts</th><th>Username</th><th>Web Accounts</th><th>Updated</th>');
					if ($USRMode == 'C')
						echo('<th>Edit</th><th>Remove</th>');
					echo('</tr>');
					$stm = $sql->prepare(
						'SELECT USR_ID,USR_USERNAME,(SELECT COUNT(WAC_ID) FROM web_accounts WHERE WAC_USR_ID = USR_ID),USR_UPDATED '.
						'FROM usr_users '.
						'WHERE USR_SR = 0 LIMIT ?,?');
					if ($stm) {
						$stm->bind_param('ii',$Offset,$Records);
						$stm->execute();
						$stm->bind_result($USRID,$USRUserName,$WACCount,$USRUpdated);
						while ($stm->fetch()) {
							/* if ($WACExists != 1)
								$HasWAC = 'No';
							else $HasWAC = 'Yes'; */
							echo('<tr>'.
								'<td><input type="button" class="button" value="Roles" onclick="'.
								"AJAX('$USRSelTarget','resources/users/user_has_roles.php','For=UHR&T=$USRSelTarget&ID=$USRID');".
								'" />');
							if ($WACCount > 0)
								echo('<input type="button" class="button" value="Accounts" onclick="'.
									"AJAX('$USRSelTarget','resources/users/user_web_accounts.php','For=UWA&T=$USRSelTarget&ID=$USRID');".	
									'" />');
							echo('</td>'.
								"<td>$USRUserName</td><td>$WACCount</td><td>$USRUpdated</td>");
							if ($USRMode == 'C')
								echo('<td><input class="button" type="button" value="Edit" onclick="'.
									"AJAX('$USRTarget','$USRPath','For=USR&T=$USRTarget&M=M&ID=$USRID');".
									'" /></td>'.
									'<td><input class="button" type="button" value="Remove" onclick="'.
									"AJAX('$USRTarget','$USRPath','For=USR&T=$USRTarget&M=D&ID=$USRID');".
									'" /></td>');
							echo('</tr>');
						}	
					}
					else $USRFailError .= 'Users failed preparing to get a list of users to display ('.$sql->error.').<br />';
					
					echo('</table>');
					
					if ($Offset > 0) {
						$PrevOffset = $Offset-$Records;
						echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
							"AJAX('$USRTarget','$USRPath','For=USR&T=$USRTarget&M=$USRMode&RO=$PrevOffset&RC=$Records');".
							'" />');
					}
					
					if (($Offset + $Records) < $Count) {
						$NextOffset = $Offset+$Records;
						echo('<input class="nextbutton" type="button" value="Next" onclick="'.
							"AJAX('$USRTarget','$USRPath','For=USR&T=$USRTarget&M=$USRMode&RO=$NextOffset&RC=$Records');".
							'" />');
					}			
					echo('<br /><div id="USRSubDiv"></div>');
				}
				else echo('There are no users present in the system.');
			}
			if (strlen($USRFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$USRFailError");
		}
		else echo('You do not have the required access right to configure the users system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Users Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$USRDieError.
		'</article>');
}
?>
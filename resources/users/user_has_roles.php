<?php
//XCal - We only want to pay attention to the right request variables
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'UHR')) {
	$For = 'UHR';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$UHRTarget = $_REQUEST['T'];
	
	if (isset($_REQUEST['ID']))
		$UHRID = $_REQUEST['ID'];
	if (isset($_REQUEST['A']))
		$AddROL = $_REQUEST['A'];
	if (isset($_REQUEST['D']))
		$DelROL = $_REQUEST['D'];

	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
}
elseif (isset($For) and ($For == 'UHR')) {
	if (isset($Target))
		$UHRTarget = $Target;
		
	if (isset($ID))
		$UHRID = $ID;

	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	$Offset = 0;
	$Records = 5;
}

$UHRPath = 'resources/users/user_has_roles.php';
$UHRDieError = '';
$UHRFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($UHRTarget))
	$UHRDieError .= 'User has roles has not been told where to target its displays and thus would not be safe to run.<br />';
if (! isset($UHRID))
	$UHRDieError .= "User has roles has not been told what user to work with.<br />";

if ($UHRDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql,$TokenValid);	
	if ($TokenValid) {
		$USRSec = GetAreaSecurity($sql, 6);
		
		//XCal - If a link type removal has been requested then remove it
		if (isset($DelROL)) {
			if ($USRSec['Config']) {
				$stm = $sql->prepare(
					'DELETE FROM usr_user_roles '.
					'WHERE URL_USR_ID = ? AND URL_ROL_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$UHRID,$DelROL);
					if ($stm->execute())					
						$stm->free_result();
					else echo('Remove failed, message: '.$stm->error);
				}
				else $UHRFailError .= 'User has roles encountered an error preparing to remove the role assignment.<br />';
			}
			else echo('You do not have the right to detach user roles!');
		}
		//XCal - If a link type is being added then add it
		if (isset($AddROL)) {
			if ($USRSec['Config']) {
				$stm = $sql->prepare(
					'INSERT INTO usr_user_roles (URL_USR_ID,URL_ROL_ID,URL_UPDATED) '.
					'VALUES (?,?,current_timestamp())');
				if ($stm) {
					$stm->bind_param('ii',$UHRID,$AddROL);
					if ($stm->execute())
						$stm->free_result();
					else echo('Add failed, message: '.$stm->error);
				}
				else $UHRFailError .= 'User has roles encountered an error preparing to add the role assignment.<br />'; 
			}
			else echo('You do not have the right to assign user roles!');
		}
		
		//XCal - Get the username and last updated date of the user
		$s_gtype = $sql->prepare('SELECT USR_USERNAME,USR_UPDATED FROM usr_users WHERE USR_ID = ?');
		if ($s_gtype) {
			$s_gtype->bind_param('i',$UHRID);
			if ($s_gtype->execute()) {
				$s_gtype->bind_result($USRName,$USRUpdated);
				$s_gtype->fetch();
				$s_gtype->free_result();
			}
			else $UHRFailError .= 'User has roles encountered an error getting user details.<br />';
		}
		else $UHRFailError .= 'User has roles encountered an error preparing to get user details.<br />';
		
		echo('<article>'.
				"<h3>Roles for User \"$USRName\"</h3>".
				'<p>User system access is controlled by their assigned roles. User access is determined by the highest access level for each area from all assigned roles.</p>'.
				'<table>'.
					'<tr><th>Assigned Roles</th>');
		if ($USRSec['Config'])
			echo('<th>Available Roles</th></tr>');
		else echo('</tr>');
		echo('<tr><td>'.
				'<table>');
	
		//XCal - Echo the assigned roles
		$Count = 0;
		$s_linked = $sql->prepare(
				'SELECT URL_ROL_ID,ROL_NAME '.
				'FROM usr_user_roles '.
				'JOIN usr_roles ON ROL_ID = URL_ROL_ID '.
				'WHERE URL_USR_ID = ?');
		if ($s_linked) {
			$s_linked->bind_param('i',$UHRID);
			$s_linked->execute();
			$s_linked->bind_result($ROLID,$ROLName);
			while ($s_linked->fetch()) {
				$Count++;
				echo("<tr><td>$ROLName</td>");
				if ($USRSec['Config'])
					echo('<td><input type="button" class="button" value="Remove >>" onclick="'.
							"AJAX('$UHRTarget','$UHRPath','For=UHR&T=$UHRTarget&ID=$UHRID&D=$ROLID');".
							'" /></td>');
				echo('</tr>');
			}
			$s_linked->free_result();
		}		
		if ($Count == 0)
			echo(			'<tr><td><i>No roles assigned.</i></td></tr>');
		
		echo('</table></td>');
		
		if ($USRSec['Config']) {
			echo('<td><table>');
	
			//XCal - Echo the unassigned types here
			$Count = 0;
			$s_avail = $sql->prepare(
					'SELECT ROL_ID,ROL_NAME '.
					'FROM usr_roles '.			
					'WHERE NOT EXISTS (SELECT URL_ROL_ID '.
						'FROM usr_user_roles '.
						'WHERE URL_ROL_ID = ROL_ID AND URL_USR_ID = ?)');
			if ($s_avail) {
				$s_avail->bind_param('i',$UHRID);
				if (! $s_avail->execute())
					echo('Error fetching available roles: '.$s_avail->error);
				$s_avail->bind_result($ROLID,$ROLName);
				while ($s_avail->fetch()) {
					$Count++;
					echo(			'<tr>'.
									'<td><input type="button" class="button" value="<< Add" onclick="'.
									"AJAX('$UHRTarget','$UHRPath','For=UHR&T=$UHRTarget&ID=$UHRID&A=$ROLID');".
									'" /></td>'.
									"<td>$ROLName</td></tr>");
				}
				$s_avail->free_result();		
			}
			else {
				//$_SESSION['DebugStr'] .= 'Group Type Link Availability: Available Types failed to Prepare ('.$sql->error.').<br />';
				$UHRFailError .= 'User has roles failed preparing to get available roles ('.$sql->error.').<br />';
			}		
			if ($Count == 0)
				echo('<tr><td><i>All roles assigned!</i></td></tr>');
			
			echo('</table></td></tr>');
		}
					
		echo('</table>'.
			'</article>');
		
		if (strlen($UHRFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$UHRFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>User Has Roles Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$UHRDieError.
		'</article>');
}			
?>
<?php
//XCal - We only want to pay attention to the right request variables
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'WUS')) {
	$For = 'WUS';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$WUSTarget = $_REQUEST['T'];
	
	if (isset($_REQUEST['U']))
		$WUSUserID = $_REQUEST['U'];
	if (isset($_REQUEST['W']))
		$WUSWebID = $_REQUEST['W'];

	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
}
elseif (isset($For) and ($For == 'WUS')) {
	if (isset($Target))
		$WUSTarget = $Target;
		
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	$WUSUserID = -1;
	$Offset = 0;
	$Records = 5;
}

$WUSPath = 'resources/users/web_users.php';
$WUSDieError = '';
$WUSFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($WUSTarget))
	$WUSDieError .= 'Web Users has not been told where to target its displays and thus would not be safe to run.<br />';

if ($WUSDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$USRSec = GetAreaSecurity($sql, 6);
		if ($USRSec['Config']) {
			//XCal - If an account and user have been set then assign the user to the account
			if (isset($WUSUserID) and isset($WUSWebID)) {
				$stm = $sql->prepare('UPDATE web_accounts SET WAC_USR_ID = ? WHERE WAC_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$WUSUserID,$WUSWebID);
					if ($stm->execute()) {				
						$stm->free_result();
						echo('User assigned to account.<br />');
					}
					else echo('Assignment failed, message: '.$stm->error.'<br />');
				}
				else $WUSFailError .= 'Web users encountered an error preparing to assign the user to the web account.<br />';
			}
				
			echo('<article>'.
					"<h3>Web Accounts</h3>".
					'<p>Assign system users to registered web accounts to provide them with data access rights.</p>'.
					'System User for Assignment: <select id="WUS_USR_ID">');
			$stm = $sql->prepare('SELECT USR_ID,USR_USERNAME FROM usr_users');
			if ($stm and $stm->execute()) {
				$stm->bind_result($USRID,$USRUserName);
				while ($stm->fetch()) {
					if (! isset($WUSUserID))
						$WUSUserID = $USRID;
					echo('<option value="'.$USRID.'"');
					if ($USRID = $WUSUserID)
						echo(' selected');
					echo(">$USRUserName</option>");
				}
				$stm->free_result();
			}
			else $WUSFailError .= 'Web users failed to get a list of users to assign.<br/>';
			echo('</select><br/><table>'.
					'<tr><th>Username</th><th>Email</th><th>Person</th><th>Assign</th></tr>');
		
			//XCal - Get a list of web accounts with no users
			$Count = 0;
			$res = $sql->query('SELECT COUNT(WAC_ID) FROM web_accounts WHERE WAC_USR_ID IS NULL');
			if (! $res) {
				$WUSFailError .= 'Web users failed to get a count of accounts ('.$sql->error.').<br />';
				$Count = 0;
			}
			else {
				$row = $res->fetch_array();
				$Count = $row[0];
			}
			
			if ($Count == 0)
				echo('</table><i>No web accounts without users.</i>');
			else {
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing web accounts $LowRec to $HighRec of $Count.<br />");
				
				$stm = $sql->prepare(
						'SELECT WAC_ID,WAC_USERNAME,IFNULL(WAC_EMAIL,\'None\'),fn_con_fullname(WAC_PER_ID) '.
						'FROM web_accounts '.			
						'WHERE WAC_USR_ID IS NULL LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					if ($stm->execute()) {
						$stm->bind_result($WACID,$WACUserName,$WACEmail,$WACPerson);
						while ($stm->fetch()) {
							echo(			"<tr><td>$WACUserName</td><td>$WACEmail</td><td>$WACPerson</td>".
											'<td><input type="button" class="button" value="Assign" onclick="'.
											"AssignAccountUser('$WUSTarget',$WACID,$Offset,$Records);".
											'" /></td></tr>');
						}
						$stm->free_result();
					}
					else $WUSFailError .= 'Web users failed to get a list of web accounts with no user rights.<br />';
				}
				else $WUSFailError .= 'Web users failed preparing to get a list of web accounts with no user rights.<br />';	
				echo('</table>');
				
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
							"AJAX('$WUSTarget','$WUSPath','For=WUS&T=$WUSTarget&U=$WUSUserID&RO=$PrevOffset&RC=$Records');".
							'" />');
				}
				
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
							"AJAX('$WUSTarget','$WUSPath','For=WUS&T=$WUSTarget&U=$WUSUserID&RO=$NextOffset&RC=$Records');".
							'" />');
				}
			}
			echo('</article>');
			
			if (strlen($WUSFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$WUSFailError");
		}
		else echo('You do not have the required access right to configure the user system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Web Users Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$WUSDieError.
		'</article>');
}			
?>
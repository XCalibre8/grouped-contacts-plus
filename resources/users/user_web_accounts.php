<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'UWA')) {
	$For = 'UWA';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$UWATarget = $_REQUEST['T'];
	
	if (isset($_REQUEST['ID']))
		$UWAID = $_REQUEST['ID'];
	if (isset($_REQUEST['W']))
		$UWAWebID = $_REQUEST['W'];
	
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
	}
elseif (isset($For) and ($For == 'UWA')) {
	if (isset($Target))
		$UWATarget = $Target;
	
	if (isset($ID))
		$UWAID = $ID;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	$Offset = 0;
	$Records = 5;
}

$UWAPath = 'resources/users/user_web_accounts.php';
$UWADieError = '';
$UWAFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($UWATarget))
	$UWADieError .= 'User web accounts has not been told where to target its displays and thus would not be safe to run.<br />';
if (! isset($UWAID))
	$UWADieError .= "User web accounts has not been given a user to display web accounts for.<br />";

if ($UWADieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	echo('<article><h3>User Web Accounts</h3>'.
		'<p>Web accounts attached to a user account, optionally remove their user access if you have configuration rights.</p>');
	
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$USRSec = GetAreaSecurity($sql, 6);
		
		//XCal - If a web account has been passed then detach it
		if (isset($UWAWebID)) {
			if ($USRSec['Config']) {
				$stm = $sql->prepare('UPDATE web_accounts SET WAC_USR_ID = NULL WHERE WAC_ID = ? AND WAC_USR_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$UWAWebID,$UWAID);
					if ($stm->execute()) {
						$stm->free_result();
						echo('Account user access removed.<br />');
					}
					else $UWAFailError .= 'User web accounts failed to detach the user rights from the web account.<br />';
				}
				else $UWAFailError .= 'User web accounts failed preparing to detach the user rights from the web account.<br />';
			}
			else echo('You do not have the right to detach web accounts!');
		}
		
		$stm = $sql->prepare('SELECT COUNT(WAC_ID) FROM web_accounts WHERE WAC_USR_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$UWAID);
			if ($stm->execute()) {
				$stm->bind_result($Count);
				$stm->fetch();
				$stm->free_result();
			}
			else {
				$Count = 0;
				$UWAFailError .= 'User web accounts failed to get a count of web accounts ('.$sql->error.').<br />';
			}
		}
		else {
			$Count = 0;
			$UWAFailError .= 'User web accounts failed to get a count of web accounts ('.$sql->error.').<br />';
		}
	
		if ($Count > 0) {
			$LowRec = $Offset+1;
			if ($Count < ($Offset+$Records))
				$HighRec = $Count;
			else $HighRec = $Offset+$Records;
			echo("Showing web accounts $LowRec to $HighRec of $Count.<br />");
					
			echo('<table><tr><th>Web Username</th><th>Email</th><th>Person</th>');
			if ($USRSec['Config'])
				echo('<th>Remove</th>');
			echo('</tr>');
			$stm = $sql->prepare(
				'SELECT WAC_ID,WAC_USERNAME,IFNULL(WAC_EMAIL,\'None\'),fn_con_fullname(WAC_PER_ID) '.
				'FROM web_accounts '.
				'WHERE WAC_USR_ID = ? LIMIT ?,?');
			if ($stm) {
				$stm->bind_param('iii',$UWAID,$Offset,$Records);
				$stm->execute();
				$stm->bind_result($WACID,$WACUserName,$WACEmail,$WACPerson);
				while ($stm->fetch()) {
					echo("<tr><td>$WACUserName</td><td>$WACEmail</td><td>$WACPerson</td>");
					if ($USRSec['Config'])
						echo('<td><input type="button" class="button" value="Remove" onclick="'.
						"AJAX('$UWATarget','$UWAPath','For=UWA&T=$UWATarget&ID=$UWAID&W=$WACID');".
						'" /></td>');
					echo('</tr>');
				}	
			}
			else $UWAFailError .= 'User roles failed preparing to get a list of roles to display ('.$sql->error.').<br />';
			
			echo('</table>');
			
			if ($Offset > 0) {
				$PrevOffset = $Offset-$Records;
				echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
					"AJAX('$UWATarget','$UWAPath','For=UWA&T=$UWATarget&RO=$PrevOffset&RC=$Records');".
					'" />');
			}
			
			if (($Offset + $Records) < $Count) {
				$NextOffset = $Offset+$Records;
				echo('<input class="nextbutton" type="button" value="Next" onclick="'.
					"AJAX('$UWATarget','$UWAPath','For=UWA&T=$UWATarget&RO=$NextOffset&RC=$Records');".
					'" />');
			}			
			echo('<br />');
		}
		else echo('<i>No web accounts found attached to user.</i><br />');
		echo('</article>');
		
		if (strlen($UWAFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$UWAFailError");
	}
}
else {
	echo(
		'<article>'.
			'<h2>User Web Accounts Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$UWADieError.
		'</article>');
}
?>
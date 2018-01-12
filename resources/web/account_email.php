<?php
if (isset($_REQUEST['ID']))
	$ACEWACID = $_REQUEST['ID'];
	
if (isset($_REQUEST['T']))
	$ACETarget = $_REQUEST['T'];
elseif ((! isset($ACETarget)) and isset($Target))
	$ACETarget = $Target;
if (isset($_REQUEST['M'])) //[XC:M|Mode:M,V|Modify,View;]
	$ACEMode = $_REQUEST['M'];
elseif (! isset($ACEMode))
	$ACEMode = 'V';
	
if (isset($_REQUEST['Email']))
	$ACEEmail = $_REQUEST['Email'];

$ACEPath = 'resources/web/account_email.php';
$ACEDieError = '';
$ACEFailError = '';

if (! isset($ACEWACID))
	$ACEDieError .= 'Account Email has not been told which account to relate to.<br />';

if ($ACEDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {	
		if ($ACEMode == 'M') {
			//XCal - Now we know it's a modify request
			if (isset($ACEEmail)) {
				//XCal - Now we know they've already filled in the details
				$_SESSION['DebugStr'] .= 'Account Email: Save Modifications<br />';
				//XCal - Now we know they want to modify a record
				$stm = $sql->prepare(
					'UPDATE web_accounts SET '.
					'WAC_EMAIL = ?,WAC_UPDATED = current_timestamp() '.
					'WHERE WAC_ID = ?');
				if ($stm) {
					$stm->bind_param('si',$ACEEmail,$ACEWACID);
					if ($stm->execute())
						$stm->free_result();
					$ACEMode = 'V';
				}
			}
			else {
				//XCal - Now we know they haven't done the new/modify form yet
				//$_SESSION['DebugStr'] .= 'Account Email: Modify Existing<br />';
				$stm = $sql->prepare(
					'SELECT WAC_EMAIL '.
					'FROM web_accounts '.
					'WHERE WAC_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$ACEWACID);
					if ($stm->execute()) {
						$stm->bind_result($ACEEmail);
						$stm->fetch();
						$stm->free_result();
					}
				}
					
				echo('<form action="javascript:;" onsubmit="'.
					"SaveAccountEmail('$ACETarget','$ACEWACID');".
					'">'.
					'Email:<input type="email" id="ACE_EMAIL" value="'.$ACEEmail.'" />'.
					'<input type="submit" class="button" value="Save" />');
				echo('</form>');
			}
		}
		if ($ACEMode == 'V') {
			//$_SESSION['DebugStr'] .= 'Account Email: View.<br />';
			//XCal - Now we know we're at least looking for a person record to exist
			$stm = $sql->prepare(
					'SELECT WAC_EMAIL '.
					'FROM web_accounts '.
					'WHERE WAC_ID = ?');
			if ($stm) {
				$stm->bind_param('i',$ACEWACID);
				if ($stm->execute()) {
					$stm->bind_result($ACEEmail);
					if ($stm->fetch()) {
						if ($ACEEmail == '')
							$ACEEmail = 'There is no email address set on the account.';
						echo("Email: $ACEEmail ");
						if ($ACEWACID == $_SESSION['WebAccID'])
							echo(' <input type="button" class="button" value="Modify" onclick="'.
									"AJAX('$ACETarget','$ACEPath','T=$ACETarget&M=M&ID=$ACEWACID');".
									'" />');
					}
					$stm->free_result();							
				}
				else echo('Account email failed getting the email address.');
			}
			else echo('Account email failed preparing to get the email address.');
		}
		if (strlen($ACEFailError) > 0)
			echo("Sorry to trouble you, but you may want to know about the following issues.<br />$ACEFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
		'<h2>Account Email Code Problem</h2>'.
		'<div class="article-info">'.
		'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
		'</div>'.
		'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
		'Please contact someone and let them know about the following problems...</p>'.
		$ACEDieError.
		'</article>');
}
?>
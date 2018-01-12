<?php
//XCal - We only want to pay attention to request variables if they're for the contact titles page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'TIT')) {
	$For = 'TIT';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$TITTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$TITMode = $_REQUEST['M'];
	else $TITMode = 'L';
	
	if (isset($_REQUEST['ID']))
		$TITID = $_REQUEST['ID'];
	else $TITID = -1;
	if (isset($_REQUEST['Title']))
		$TITTitle = $_REQUEST['Title'];
	if (isset($_REQUEST['Desc']))
		$TITDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 10;
}
else {		
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to CTS then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'TIT')) {
		if (isset($Target))
			$TITTarget = $Target;
		if (isset($Mode))
			$TITMode = $Mode;
		elseif (! isset($TITMode))
			$TITMode = 'L';
		if (isset($ID))
			$TITID = $ID;
		elseif (! isset($TITID))
			$TITID = -1;
		if (! isset($Offset))
			$Offset = 0;
		if (! isset($Records))
			$Records = 10;
	}  
	else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults 
		$TITMode = 'L';
		$TITID = -1;
		unset($TITTitle);
		unset($TITDesc);
		$Offset = 0;
		$Records = 10;
	}
}
$TITPath = 'resources/contacts/contact_titles.php';
$TITDieError = '';
$TITFailError = '';
$TITModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($TITTarget))
	$TITDieError .= 'Contact titles has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($TITMode,$TITModes))
	$TITDieError .= "Contact titles has been passed a mode ($TITMode) which is does not support.<br />";

if ($TITDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql, 1);
		if ($CONSec['Config']) {
			//XCal - Check if we're in New/Modify/Delete mode
			if (in_array($TITMode,array('N','M','D'))) {			
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($TITMode,array('M','D')) and (!($TITID > 0))) {				
					$TITFailError .= 'Contact titles was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
					$TITMode = 'L';
				}
				elseif ($TITMode == 'D') { //XCal - Perform the 'delete' and return to list mode
					//$_SESSION['DebugStr'] .= 'Contact Titles: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
						
						if ($stm->prepare('CALL sp_con_rem_title(?,@Code,@Msg)')) {
							$stm->bind_param('i', $TITID);
							if ($stm->execute()) {
								$stm->free_result();
								
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();
								}
								else $TITFailError .= 'Contact titles encountered an error retrieving title removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $TITFailError .= 'Contact titles encountered an error preparing to request title removal.<br />';					
					}
					else $TITFailError .= 'Contact titles encountered an error initialising title removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the list view back
					$TITMode = 'L';
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Title has been passed in we're saving
					if (isset($TITTitle)) {
						//$_SESSION['DebugStr'] .= 'Contact Titles: Save Mode<br />';
						if ($TITMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO con_titles (TIT_TITLE,TIT_DESCRIPTION,TIT_UPDATED) '.
								'VALUE (?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('ss',$TITTitle,$TITDesc);
								$stm->execute();
								$stm->free_result();
								$TITMode = 'L';
							}
							else $TITFailError .= 'Contact titles encountered an error preparing to add the title.<br />';
						}
						else {
							$stm = $sql->prepare(
								'UPDATE con_titles SET '.
								'TIT_TITLE = ?, TIT_DESCRIPTION = ?, TIT_UPDATED = current_timestamp() '.
								'WHERE TIT_ID = ?');
							if ($stm) {
								$stm->bind_param('ssi',$TITTitle,$TITDesc,$TITID);
								$stm->execute();
								$stm->free_result();
								$TITMode = 'L';
							}
							else $TITFailError .= 'Contact titles encountered an error preparing to modify the title.<br />';
						}					
					}
					else { //XCal - Title not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Contact Titles: New/Modify Mode<br />';
						echo('<h4>');
						if ($TITMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare('SELECT TIT_TITLE,TIT_DESCRIPTION FROM con_titles WHERE TIT_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$TITID);
								$stm->execute();
								$stm->bind_result($TITTitle,$TITDesc);
								$stm->fetch();
								$stm->free_result();
							}
							else $TITFailError .= 'Contact titles encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$TITTitle = '';
							$TITDesc = '';
						}
						echo(' Title</h4><br />'.
								'<input type="hidden" id="TIT_ID" value="'.$TITID.'" />'.
								'<table>'.
								'<tr><td class="fieldname">Title</td>'.
								"<td><input id=\"TIT_TITLE\" type=\"text\" value=\"$TITTitle\" /></td></tr>".
								'<tr><td class="fieldname">Description</td>'.
								"<td><textarea id=\"TIT_DESC\" rows=\"5\" cols=\"50\">$TITDesc</textarea></td></tr></table>".
								'<input type="button" class="save" value="Save" onclick="'.
								"SaveTitle('$TITTarget','$TITMode');"
								.'" />'.
								'<input type="button" class="cancel" value="Cancel" onclick="'.
								"AJAX('$TITTarget','$TITPath','For=TIT&T=$TITTarget&M=L');".
								'" />');					
					}
				}
			}
			
			//XCal - Either list mode was requested or the (N)ew,(M)odify or (D)delete mode has finished its work
			if ($TITMode == 'L') {
				//$_SESSION['DebugStr'] .= 'Contact Titles: List Mode<br />';
				$res = $sql->query('SELECT COUNT(TIT_ID) FROM con_titles WHERE TIT_SR = 0');
				if (! $res) {
					$TITFailError .= 'Contact titles failed to get a count of titles ('.$sql->error.').<br />';
					$Count = 0;
				} 
				else {
				  $row = $res->fetch_array();
				  $Count = $row[0];
				}
				echo('<input type="button" class="button" value="New Title" onclick="'.
					"AJAX('$TITTarget','$TITPath','For=TIT&T=$TITTarget&M=N');".
					'" /><br />');
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing titles $LowRec to $HighRec of $Count.<br />");
				
				echo('<table><tr><th>Title</th><th>Description</th><th>Updated</th><th>Edit</th><th>Remove</th></tr>');
				$stm = $sql->prepare(
					'SELECT TIT_ID,TIT_TITLE,TIT_DESCRIPTION,TIT_UPDATED '.
					'FROM con_titles '.
					'WHERE TIT_SR = 0 LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					$stm->execute();
					$stm->bind_result($TITID,$TITTitle,$TITDesc,$TITUpdated);
					while ($stm->fetch()) {
						echo('<tr>'.
							"<td>$TITTitle</td><td>$TITDesc</td><td>$TITUpdated</td>".
							'<td><input class="button" type="button" value="Edit" onclick="'.
							"AJAX('$TITTarget','$TITPath','For=TIT&T=$TITTarget&M=M&ID=$TITID');".
							'" /></td>'.
							'<td><input class="button" type="button" value="Remove" onclick="'.
							"AJAX('$TITTarget','$TITPath','For=TIT&T=$TITTarget&M=D&ID=$TITID');".
							'" /></td>'.
							'</tr>');					
					}
					$stm->free_result();
				}
				else $TITFailError .= 'Contact titles failed to get a list of titles to display ('.$sql->error.').<br />';
				echo('</table>');
					
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
						"AJAX('$TITTarget','$TITPath','For=TIT&T=$TITTarget&M=L&RO=$PrevOffset&RC$Records');".
						'" />');
				}
					
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
						"AJAX('$TITTarget','$TITPath','For=TIT&T=$TITTarget&M=L&RO=$NextOffset&RC$Records');".
						'" />');
				}
				echo('<br />');
			}
			if (strlen($TITFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$TITFailError");
		}
		else echo('You do not have the required access rights to configure the contacts system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Contact Titles Code Problem</h2>'.
			'<div class="article-info">'.
				'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$TITDieError.
			'</article>');
}
?>
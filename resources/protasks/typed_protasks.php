<?php
//XCal - We only want to pay attention to request variables if they're for the typed protasks page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'TPT')) {
	$For = 'TPT';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$TPTTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$TPTMode = $_REQUEST['M'];
	else $TPTMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$TPTSelTarget = $_REQUEST['ST'];
	else $TPTSelTarget = 'TPTSubDiv';
	
	if (isset($_REQUEST['ID']))
		$TSKID = $_REQUEST['ID'];
	else $TSKID = -1;
	if (isset($_REQUEST['TID']))
		$TSTID = $_REQUEST['TID'];
	if (isset($_REQUEST['ETID']))
		$TPTTSTID = $_REQUEST['ETID'];
	if (isset($_REQUEST['PID']))
		$TPTPID = $_REQUEST['PID'];
	else $TPTPID = 0;
	if (isset($_REQUEST['Tit']))
		$TPTTitle = $_REQUEST['Tit'];
	if (isset($_REQUEST['Desc']))
		$TPTDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['Doc']))
		$TPTDoc = $_REQUEST['Doc'];
	if (isset($_REQUEST['Rpt']))
		$TPTRepeat = $_REQUEST['Rpt'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
	
	}
elseif (isset($For) and ($For == 'TPT')) {
	if (isset($Target))
		$TPTTarget = $Target;
	if (isset($Mode))
		$TPTMode = $Mode;
	elseif (! isset($TPTMode))
		$TPTMode = 'L';
	if (isset($SelTarget))
		$TPTSelTarget = $SelTarget;
	else $TPTSelTarget = 'TPTSubDiv';
	
	if (isset($ID))
		$TSKID = $ID;
	elseif (! isset($TSKID))
		$TSKID = -1;
	if (isset($TID))
		$TSTID = $TID;
	if (isset($PID))
		$TPTPID = $PID;
	else $TPTPID = 0;			
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($TPTMode))
		$TPTMode = 'L';
	$TSKID = -1;
	if (! isset($TPTSelTarget))
		$TPTSelTarget = 'TPTSubDiv';
	$TPTPID = 0;
	unset($TPTTSTID);
	unset($TPTTitle);
	unset($TPTDesc);
	unset($TPTDoc);
	$Offset = 0;
	$Records = 5;
}

$TPTPath = 'resources/protasks/typed_protasks.php';
$TPTDieError = '';
$TPTFailError = '';
$TPTModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($TSTID))
	$TPTDieError .= 'Typed ProTasks has not been passed a task type for which to display groups.<br />';
if (! isset($TPTTarget))
	$TPTDieError .= 'Typed ProTasks has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($TPTMode,$TPTModes))
	$TPTDieError .= "Typed ProTasks has been passed a mode ($TPTMode) which is does not support.<br />";

if ($TPTDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$TSKSec = GetAreaSecurity($sql, 3);
		if (isset($TSKID))
			$IsLinked = ItemLinked($sql,5,$TSKID);		
		
		//XCal - Check if we're in New/Modify/Delete mode
		if (in_array($TPTMode,array('N','M','D'))) {
			if (in_array($TPTMode,array('M','D')) and (!($TSKID > 0))) {
				$TPTFailError .= 'Typed ProTasks was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
				$TPTMode = 'L';
			}
			elseif ($TPTMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
				if ($TSKSec['Remove'] == 2)
					$CanRem = true;
				else $CanRem = ($TSKSec['Remove'] == 1) and $IsLinked;
				
				if ($CanRem) {
					//$_SESSION['DebugStr'] .= 'Typed ProTasks: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
				
						if ($stm->prepare('CALL sp_pro_rem_task(?,@Code,@Msg)')) {
							$stm->bind_param('i', $TSKID);
							if ($stm->execute()) {
								$stm->free_result();
				
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();
								}
								else $TPTFailError .= 'Typed ProTasks encountered an error retrieving task removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $TPTFailError .= 'Typed ProTasks encountered an error preparing to request task removal.<br />';
					}
					else $TPTFailError .= 'Typed ProTasks encountered an error initialising task removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the list view back
				}
				elseif ($TSKSec['Remove'] == 0)
					echo('You do not have the required access rights to remove ProTasks.');
				else echo('You only have the right to remove ProTasks which your account is linked by, which does not include the requested ProTask.');
				$TPTMode = 'L';
			}
			else { //XCal - Either (N)ew or (M)odify
				if ($TSKSec['NewMod'] == 2)
					$CanMod = true;
				else $CanMod = ($TSKSec['NewMod'] == 1) and $IsLinked;
				
				if ($CanMod) {
					//XCal - If the Name has been passed in we're saving
					if (isset($TPTTitle)) {
						//$_SESSION['DebugStr'] .= 'Typed ProTasks: Save Mode<br />';
						if ($TPTMode == 'N') {
							$query = 'INSERT INTO pro_tasks(TSK_TST_ID,TSK_TSK_ID,TSK_TITLE,'.
									'TSK_DESCRIPTION,TSK_DOCUMENT,TSK_REPEATABLE,TSK_UPDATED) ';
							if ($TPTPID == 0)
								$query .= 'VALUES (?,NULL,?,?,?,?,current_timestamp())';
							else $query .= 'VALUES (?,?,?,?,?,?,current_timestamp())';
							$stm = $sql->prepare($query);
							if ($stm) {
								if ($TPTPID == 0)
									$stm->bind_param('isssi',$TPTTSTID,$TPTTitle,$TPTDesc,$TPTDoc,$TPTRepeat);
								else $stm->bind_param('iisssi',$TPTTSTID,$TPTPID,$TPTTitle,$TPTDesc,$TPTDoc,$TPTRepeat);
								$stm->execute();
								$stm->free_result();
								$TPTMode = 'L';
							}
							else $TPTFailError .= 'Typed ProTasks encountered an error preparing to add the task.<br />';
						}
						else {
							if ($TPTPID == 0)
								$query = 'TSK_TSK_ID = NULL';
							else $query = 'TSK_TSK_ID = ?';
							$stm = $sql->prepare(
								'UPDATE pro_tasks SET '.
								"TSK_TST_ID = ?, $query, TSK_TITLE = ?, TSK_DESCRIPTION = ?, ".
								'TSK_DOCUMENT = ?, TSK_REPEATABLE = ?, TSK_UPDATED = current_timestamp() '.
								'WHERE TSK_ID = ?');
							if ($stm) {
								if ($TPTPID == 0)
									$stm->bind_param('isssii',$TPTTSTID,$TPTTitle,$TPTDesc,$TPTDoc,$TPTRepeat,$TSKID);
								else $stm->bind_param('iisssii',$TPTTSTID,$TPTPID,$TPTTitle,$TPTDesc,$TPTDoc,$TPTRepeat,$TSKID);
								$stm->execute();
								$stm->free_result();
								$TPTMode = 'L';
							}
							else $TPTFailError .= 'Typed ProTasks encountered an error preparing to modify the task.<br />';
						}
					}				
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Typed ProTasks: New/Modify Mode<br />';			
						echo('<h4>');
						//XCal - We'll always want the protask type selection list
						$r_pttypes = $sql->query('SELECT TST_ID,TST_NAME FROM pro_task_types WHERE TST_SR = 0');
						if ($TPTMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare(
								'SELECT TSK_TST_ID,TSK_TSK_ID,TSK_TITLE,TSK_DESCRIPTION,TSK_DOCUMENT,TSK_REPEATABLE '.
								'FROM pro_tasks WHERE TSK_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$TSKID);
								$stm->execute();
								$stm->bind_result($TPTTSTID,$TPTPID,$TPTTitle,$TPTDesc,$TPTDoc,$TPTRepeat);
								$stm->fetch();
								$stm->free_result();
							}
							else $TPTFailError .= 'Typed groups encountered an error preparing to get values to modify ('.$sql->error.').<br />';						
						}
						else {
							echo('New');						
							$TPTTSTID = $TSTID;						
							$TPTTitle = '';
							$TPTDesc = '';
							$TPTDoc = '';
							$TPTRepeat = 0;
						}
						echo(' ProTask</h4><br />'.
							'<input type="hidden" id="TSK_ID" value="'.$TSKID.'" />'.
							'<table>'.
							'<tr><td>'.
							'<table><tr><td class="fieldname">Type</td>'.
							'<td><select id="TSK_TST_ID">');
						while ($row = $r_pttypes->fetch_assoc()) {
							echo('<option value="'.$row['TST_ID'].'" ');
							if ($row['TST_ID'] == $TPTTSTID)
								echo('selected');
							echo('>'.$row['TST_NAME'].'</option>');
						}
						echo('</select></td></tr>'.
							'<tr><td class="fieldname">Title</td>'.				
							"<td><input id=\"TSK_TITLE\" type=\"text\" value=\"$TPTTitle\" /></td>".
							'</tr></table></td>'.
							'<td><table><tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"TSK_DESC\" rows=\"5\" cols=\"45\">$TPTDesc</textarea></td></tr>".
							'<tr><td class="fieldname">Repeatable</td>'.
							'<td><input type="checkbox" id="TSK_REPEAT" ');
						if ($TPTRepeat == 1)
							echo('selected ');
						echo('/></td></tr>'.
							'</table></td></tr></table>'.
							'<h4>Group Document</h4><br />'.
							"<textarea id=\"TSK_DOCUMENT\" rows=\"10\" cols=\"80\">$TPTDoc</textarea><br />".
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveProTask('$TPTTarget','$TPTMode',$TSTID);".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$TPTTarget','$TPTPath','For=TPT&T=$TPTTarget&TID=$TSTID');".
							'" /><br />');
					}
				}
				elseif ($TSKSec['NewMod'] == 0)
					echo('You do not have the required access rights to edit ProTasks.');
				else echo('You only have the right to modify ProTasks which your account is linked by, which does not include the requested ProTask.');
			}
		}
		
		//XCal - If we're not editing or we have saved the details show the list view
		if ($TPTMode == 'L') {
			if ($TSKSec['SearchView'] == 2) {
				//$_SESSION['DebugStr'] .= 'Typed Protasks: List Mode<br />';
				$stm = $sql->prepare('SELECT TST_NAME FROM pro_task_types WHERE TST_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$TSTID);
					$stm->execute();
					$stm->bind_result($TypeName);
					$stm->fetch();
					$stm->free_result();
				}
				else $TypeName = '[Error!]';
				$stm = $sql->prepare('SELECT COUNT(TSK_ID) FROM pro_tasks WHERE TSK_TSK_ID IS NULL AND TSK_SR = 0 AND TSK_TST_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$TSTID);
					$stm->execute();
					$stm->bind_result($Count);
					$stm->fetch();
					$stm->free_result();
				}
				else $Count = 0;
				
				echo("<h3>\"$TypeName\" Tasks</h3><br />");
				if ($TSKSec['NewMod'] == 2)
					echo('<input type="button" class="button" value="New ProTask" onclick="'.
							"AddEditProTask('$TPTTarget','$TSTID',0);".
							'" />'.
							'<br />');
				
				if ($Count == 0)
					echo('No tasks of this type found to display.');
				else {
					$MightEdit = $TSKSec['NewMod'] > 0;
					$MightRem = $TSKSec['Remove'] > 0;
					$LowRec = $Offset+1;
					if ($Count < ($Offset+$Records))
						$HighRec = $Count;
					else $HighRec = $Offset+$Records;
					echo("Showing ProTasks $LowRec to $HighRec of $Count.<br />");
					
					echo('<table><tr><th>Select</th><th>Task</th><th>Description</th><th>Repeatable?</th><th>Updated</th>');
					if ($MightEdit)
						echo('<th>Edit</th>');
					if ($MightRem)
						echo('<th>Remove</th>');
					echo('</tr>');		
					$stm = $sql->prepare(
						'SELECT TSK_ID,TSK_TST_ID,TSK_TITLE,TSK_DESCRIPTION,TSK_REPEATABLE,TSK_UPDATED '.
						'FROM pro_tasks '.
						'WHERE TSK_TSK_ID IS NULL AND TSK_SR = 0 AND TSK_TST_ID = ? LIMIT ?,?');
					if ($stm) {
						$stm->bind_param('iii',$TSTID,$Offset,$Records);
						$stm->execute();
						$stm->bind_result($TSKID,$TPTTSTID,$TPTTitle,$TPTDesc,$TPTRepeat,$TPTUpdated);
						while ($stm->fetch()) {
							$IsLinked = ItemLinked($sql,5,$TSKID);
							if ($MightEdit)								
								$CanEdit = ($TSKSec['NewMod'] == 2) or $IsLinked;
							else $CanEdit = false;
							if ($MightRem)
								$CanRem = ($TSKSec['Remove'] == 2) or $IsLinked;
							else $CanRem = false;
							echo('<tr>'.
									'<td><input type="button" class="button" value="Select" onclick="'.
									"AJAX('$TPTSelTarget','resources/protasks/protask_view.php','For=PTV&T=$TPTSelTarget&ID=$TSKID');".
									'" /></td>'.
									"<td>$TPTTitle</td><td>$TPTDesc</td><td>");
							if ($TPTRepeat == 1)
								echo('Yes');
							else echo('No');
							echo("</td><td>$TPTUpdated</td>");
							if ($CanEdit)
								echo('<td><input class="button" type="button" value="Edit" onclick="'.
									"AddEditProTask('$TPTTarget',$TPTTSTID,$TSKID);".
									'" /></td>');
							if ($CanRem)
								echo('<td><input class="button" type="button" value="Remove" onclick="'.
									"AJAX('$TPTTarget','$TPTPath','For=TPT&T=$TPTTarget&TID=$TPTTSTID&ID=$TSKID&M=D');".
									'" /></td>');
							echo('</tr>');
						}				
					}
					else $TPTFailError .= 'Typed ProTasks failed preparing to get a list of tasks to display ('.$sql->error.').<br />';
		
					echo('</table>');
					
					if ($Offset > 0) {
						$PrevOffset = $Offset-$Records;
						echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
							"AJAX('$TPTTarget','$TPTPath','For=TPT&T=$TPTTarget&TID=$TSTID&RO=$PrevOffset&RC=$Records');".
							'" />');
					}
					
					if (($Offset + $Records) < $Count) {
						$NextOffset = $Offset+$Records;
						echo('<input class="nextbutton" type="button" value="Next" onclick="'.
							"AJAX('$TPTTarget','$TPTPath','For=TPT&T=$TPTTarget&TID=$TSTID&RO=$NextOffset&RC=$Records');".
							'" />');
					}
					echo('<br /><div id="TPTSubDiv"></div>');
				}
			}
			elseif ($TSKSec['SearchView'] == 0)
				echo('You do not have the required access right to view ProTasks.');
			else echo('You only have the right to view ProTasks your account is linked by, which does not include browsing ProTasks.');
		}
		if (strlen($TPTFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$TPTFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Typed ProTasks Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$TPTDieError.
		'</article>');
}
?>
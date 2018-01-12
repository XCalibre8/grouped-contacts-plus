<?php
//XCal - We only want to pay attention to request variables if they're for the typed protasks page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'STN')) {
	$For = 'STN';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$STNTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$STNMode = $_REQUEST['M'];
	else $STNMode = 'L';
	if (isset($_REQUEST['EBD'])) //XCal - 0,1 Indicates embedded mode (display hide subtasks button)
		$STNEBD = $_REQUEST['EBD'] == 1;
	else $STNEBD = false;
	
	if (isset($_REQUEST['ID']))
		$STNTSKID = $_REQUEST['ID'];
	else $STNTSKID = -1;
	if (isset($_REQUEST['TID']))
		$TSTID = $_REQUEST['TID'];
	if (isset($_REQUEST['ETID']))
		$STNTSTID = $_REQUEST['ETID'];
	if (isset($_REQUEST['PID']))
		$STNPID = $_REQUEST['PID'];
	else $STNPID = 0;
	if (isset($_REQUEST['Tit']))
		$STNTitle = $_REQUEST['Tit'];
	if (isset($_REQUEST['Desc']))
		$STNDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['Doc']))
		$STNDoc = $_REQUEST['Doc'];
	if (isset($_REQUEST['Rpt']))
		$STNRepeat = $_REQUEST['Rpt'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
	
	}
elseif (isset($For) and ($For == 'STN')) {
	if (isset($Target))
		$STNTarget = $Target;
	if (isset($Mode))
		$STNMode = $Mode;
	elseif (! isset($STNMode))
		$STNMode = 'L';
	if (isset($EBD))
		$STNEBD = $EBD;
	else $STNEBD = false;
	
	if (isset($ID))
		$STNTSKID = $ID;
	elseif (! isset($STNTSKID))
		$STNTSKID = -1;
	if (isset($PID))
		$STNPID = $PID;
	elseif (! isset($STNPID))
		$STNPID = 0;			
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($STNMode))
		$STNMode = 'L';
	$STNTSKID = -1;
	if (! isset($STNPID))
		$STNPID = 0;
	$STNEBD = false;
	unset($STNTSTID);
	unset($STNTitle);
	unset($STNDesc);
	unset($STNDoc);
	$Offset = 0;
	$Records = 5;
}
$STNPath = 'resources/protasks/subtask_nest.php';
$STNDieError = '';
$STNFailError = '';
$STNModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($STNPID))
	$STNDieError .= 'Nested SubTasks has not been told which task to display subtasks for.<br />';
if (! isset($STNTarget))
	$STNDieError .= 'Nested SubTasks has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($STNMode,$STNModes))
	$STNDieError .= "Nested SubTasks has been passed a mode ($STNMode) which is does not support.<br />";

if ($STNDieError == '') {
	$STNSelTarget = 'STN'.$STNPID.'SubDiv';
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$TSKSec = GetAreaSecurity($sql, 3);
		$IsLinked = ItemLinked($sql,5,$STNPID);
		if ($TSKSec['SearchView'] == 2)
			$CanView = true;
		else $CanView = ($TSKSec['SearchView'] == 1) and $IsLinked;
		if ($TSKSec['NewMod'] == 2)
			$CanMod = true;
		else $CanMod = ($TSKSec['NewMod'] == 1) and $IsLinked;
		if ($TSKSec['Remove'] == 2)
			$CanRem = true;
		else $CanRem = ($TSKSec['Remove'] == 1) and $IsLinked;
		
		//XCal - Check if we're in New/Modify/Delete mode
		if (in_array($STNMode,array('N','M','D'))) {
			if ($TSKSec['NewMod'] > 0) {
				if ($CanMod) {
					if (in_array($STNMode,array('M','D')) and (!($STNTSKID > 0))) {
						$STNFailError .= 'Nested SubTasks was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
						$STNMode = 'L';
					}
					elseif ($STNMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
						//$_SESSION['DebugStr'] .= 'Nested SubTasks: Delete Mode<br />';
						$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
						if ($stm) {
							$stm->execute();
							$stm->free_result();
					
							if ($stm->prepare('CALL sp_pro_rem_task(?,@Code,@Msg)')) {
								$stm->bind_param('i', $STNTSKID);
								if ($stm->execute()) {
									$stm->free_result();
					
									if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
										$stm->execute();
										$stm->bind_result($RCode,$RMsg);
										$stm->fetch();
										echo("Remove result: $RMsg<br />");
										$stm->free_result();
									}
									else $STNFailError .= 'Nested SubTasks encountered an error retrieving task removal results.<br />';
								}
								else echo('Remove attempt failed with error: '.$stm->error.'<br />');
							}
							else $STNFailError .= 'Nested SubTasks encountered an error preparing to request task removal.<br />';
						}
						else $STNFailError .= 'Nested SubTasks encountered an error initialising task removal.<br />';
						//XCal - Whether removal failed or succeeded we want to give the list view back
						$STNMode = 'L';
					}
					else { //XCal - Either (N)ew or (M)odify	
						//XCal - If the Name has been passed in we're saving
						if (isset($STNTitle)) {
							//$_SESSION['DebugStr'] .= 'Nested SubTasks: Save Mode<br />';
							if ($STNMode == 'N') {
								$query = 'INSERT INTO pro_tasks(TSK_TST_ID,TSK_TSK_ID,TSK_TITLE,'.
										'TSK_DESCRIPTION,TSK_DOCUMENT,TSK_REPEATABLE,TSK_UPDATED) ';
								if ($STNPID == 0)
									$query .= 'VALUES (?,NULL,?,?,?,?,current_timestamp())';
								else $query .= 'VALUES (?,?,?,?,?,?,current_timestamp())';
								$stm = $sql->prepare($query);
								if ($stm) {
									if ($STNPID == 0)
										$stm->bind_param('isssi',$STNTSTID,$STNTitle,$STNDesc,$STNDoc,$STNRepeat);
									else $stm->bind_param('iisssi',$STNTSTID,$STNPID,$STNTitle,$STNDesc,$STNDoc,$STNRepeat);
									$stm->execute();
									$stm->free_result();
									$STNMode = 'L';
								}
								else $STNFailError .= 'Nested SubTasks encountered an error preparing to add the task.<br />';
							}
							else {
								if ($STNPID == 0)
									$query = 'TSK_TSK_ID = NULL';
								else $query = 'TSK_TSK_ID = ?';
								$stm = $sql->prepare(
									'UPDATE pro_tasks SET '.
									"TSK_TST_ID = ?, $query, TSK_TITLE = ?, TSK_DESCRIPTION = ?, ".
									'TSK_DOCUMENT = ?, TSK_REPEATABLE = ?, TSK_UPDATED = current_timestamp() '.
									'WHERE TSK_ID = ?');
								if ($stm) {
									if ($STNPID == 0)
										$stm->bind_param('isssii',$STNTSTID,$STNTitle,$STNDesc,$STNDoc,$STNRepeat,$STNTSKID);
									else $stm->bind_param('iisssii',$STNTSTID,$STNPID,$STNTitle,$STNDesc,$STNDoc,$STNRepeat,$STNTSKID);
									$stm->execute();
									$stm->free_result();
									$STNMode = 'L';
								}
								else $STNFailError .= 'Nested SubTasks encountered an error preparing to modify the task.<br />';
							}
						}				
						else { //XCal - Name not set, so we want the new/modify input screen
							//$_SESSION['DebugStr'] .= 'Typed ProTasks: New/Modify Mode<br />';			
							echo('<h4>');
							//XCal - We'll always want the protask type selection list
							$r_pttypes = $sql->query('SELECT TST_ID,TST_NAME FROM pro_task_types WHERE TST_SR = 0');
							if ($STNMode == 'M') {
								echo('Edit');
								$stm = $sql->prepare(
									'SELECT TSK_TST_ID,TSK_TSK_ID,TSK_TITLE,TSK_DESCRIPTION,TSK_DOCUMENT,TSK_REPEATABLE '.
									'FROM pro_tasks WHERE TSK_ID = ?');
								if ($stm) {
									$stm->bind_param('i',$STNTSKID);
									$stm->execute();
									$stm->bind_result($STNTSTID,$STNRPID,$STNTitle,$STNDesc,$STNDoc,$STNRepeat);
									$stm->fetch();
									$stm->free_result();
								}
								else $STNFailError .= 'Nested SubTasks encountered an error preparing to get values to modify ('.$sql->error.').<br />';						
							}
							else {
								echo('New');						
								$STNRPID = $STNPID;						
								$STNTitle = '';
								$STNDesc = '';
								$STNDoc = '';
								$STNRepeat = 0;
							}
							echo(' SubTask</h4><br />'.
								'<input type="hidden" id="TSK_ID" value="'.$STNTSKID.'" />'.
								'<table>'.
								'<tr><td>'.
								'<table><tr><td class="fieldname">Type</td>'.
								'<td><select id="TSK_TST_ID">');
							while ($row = $r_pttypes->fetch_assoc()) {
								if (! isset($STNTSTID))
									$STNTSTID = $row['TST_ID'];
								echo('<option value="'.$row['TST_ID'].'" ');
								if ($row['TST_ID'] == $STNTSTID)
									echo('selected');
								echo('>'.$row['TST_NAME'].'</option>');
							}
							echo('</select></td></tr>'.
								'<tr><td class="fieldname">Title</td>'.				
								"<td><input id=\"TSK_TITLE\" type=\"text\" value=\"$STNTitle\" /></td>".
								'</tr></table></td>'.
								'<td><table><tr><td class="fieldname">Description</td>'.
								"<td><textarea id=\"TSK_DESC\" rows=\"5\" cols=\"45\">$STNDesc</textarea></td></tr>".
								'<tr><td class="fieldname">Repeatable</td>'.
								'<td><input type="checkbox" id="TSK_REPEAT" ');
							if ($STNRepeat == 1)
								echo('selected ');
							echo('/></td></tr>'.
								'</table></td></tr></table>'.
								'<h4>Group Document</h4><br />'.
								"<textarea id=\"TSK_DOCUMENT\" rows=\"10\" cols=\"80\">$STNDoc</textarea><br />".
								'<input type="button" class="save" value="Save" onclick="'.
								"SaveSubTask('$STNTarget','$STNMode',$STNRPID);".
								'" />'.
								'<input type="button" class="cancel" value="Cancel" onclick="'.
								"AJAX('$STNTarget','$STNPath','For=STN&T=$STNTarget&PID=$STNPID');".
								'" /><br />');
						}
					}
				}
				else echo('You only have the right to modify ProTasks your account is linked by, which does not include the requested ProTask.');
			}
			else echo('You do not have the required access rights to modify ProTasks.');
		}
		
		//XCal - If we're not editing or we have saved the details show the list view
		if ($STNMode == 'L') {
			if ($TSKSec['SearchView'] > 0) {				
				if ($CanView) {
					//$_SESSION['DebugStr'] .= 'Nested Subtasks: List Mode<br />';
					$stm = $sql->prepare('SELECT TSK_TITLE FROM pro_tasks WHERE TSK_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$STNPID);
						$stm->execute();
						$stm->bind_result($PTaskName);
						$stm->fetch();
						$stm->free_result();
					}
					else $PTaskName = '[Error!]';
					$stm = $sql->prepare('SELECT COUNT(TSK_ID) FROM pro_tasks WHERE TSK_SR = 0 AND TSK_TSK_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$STNPID);
						$stm->execute();
						$stm->bind_result($Count);
						$stm->fetch();
						$stm->free_result();
					}
					else $Count = 0;
					
					echo("<h3>\"$PTaskName\" Subtasks</h3><br />");
					if ($CanMod)
						echo('<input type="button" class="button" value="New SubTask" onclick="'.
							"AddEditSubTask('$STNTarget','$STNPID',0);".
							'" />');
					if ($STNEBD)
						echo('<input type="button" class="button" value="Hide Subtasks" onclick="'.
							"BlankTarget('$STNTarget');".'" />');
					echo('<br />');
					
					if ($Count == 0)
						echo('No sub-tasks found to display.');
					else {
						$LowRec = $Offset+1;
						if ($Count < ($Offset+$Records))
							$HighRec = $Count;
						else $HighRec = $Offset+$Records;
						echo("Showing SubTasks $LowRec to $HighRec of $Count.<br />");
						
						//XCal - We can't just embed a table between two rows of another table, so each row needs to target the parent ID's subdiv
						echo('<table><tr><th>Select</th><th>Task</th><th>Description</th><th>Repeatable?</th><th>SubTasks?</th><th>Updated</th>');
						if ($CanMod)
							echo('<th>Edit</th>');
						if ($CanRem)
							echo('<th>Remove</th>');
						echo('</tr>');		
						$stm = $sql->prepare(
							'SELECT t1.TSK_ID,t1.TSK_TST_ID,t1.TSK_TITLE,t1.TSK_DESCRIPTION,t1.TSK_REPEATABLE,'.
							'(SELECT COUNT(t2.TSK_ID) FROM pro_tasks t2 WHERE t2.TSK_TSK_ID = t1.TSK_ID),'.
							't1.TSK_UPDATED '.
							'FROM pro_tasks t1 '.
							'WHERE t1.TSK_SR = 0 AND t1.TSK_TSK_ID = ? LIMIT ?,?');
						if ($stm) {
							$stm->bind_param('iii',$STNPID,$Offset,$Records);
							$stm->execute();
							$stm->bind_result($STNTSKID,$STNTSTID,$STNTitle,$STNDesc,$STNRepeat,$STNSubCount,$STNUpdated);
							while ($stm->fetch()) {
								echo('<tr>'.
										'<td><input type="button" class="button" value="Select" onclick="'.
										"AJAX('$STNSelTarget','resources/protasks/protask_view.php','For=PTV&T=$STNSelTarget&ID=$STNTSKID');".
										'" /></td>'.
										"<td>$STNTitle</td><td>$STNDesc</td><td>");
								if ($STNRepeat == 1)
									echo('Yes</td>');
								else echo('No</td>');
								if ($STNSubCount == 0)
									echo('<td>None</td>');
								else echo('<td><input type="button" class="button" value="'."($STNSubCount) View".'" onclick="'.
										"AJAX('pt$STNPID"."subtasks','$STNPath','For=STN&PID=$STNTSKID&EBD=1&T=pt$STNPID"."subtasks');".
										'" /></td>');
								echo(	"<td>$STNUpdated</td>");
								if ($CanMod)
									echo('<td><input class="button" type="button" value="Edit" onclick="'.
										"AddEditSubTask('$STNTarget',$STNPID,$STNTSKID);".
										'" /></td>');
								if ($CanRem)
									echo('<td><input class="button" type="button" value="Remove" onclick="'.
										"AJAX('$STNTarget','$STNPath','For=STN&T=$STNTarget&ID=$STNTSKID&M=D');".
										'" /></td>');
								echo('</tr>');
							}
							echo('</table>'.
								"<div id=\"pt$STNPID"."subtasks\"></div>");
							$stm->free_result();
						}
						else $STNFailError .= 'Nested SubTasks failed preparing to get a list of tasks to display ('.$sql->error.').<br />';
			
						
						if ($Offset > 0) {
							$PrevOffset = $Offset-$Records;
							echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
								"AJAX('$STNTarget','$STNPath','For=STN&T=$STNTarget&TID=$TSTID&RO=$PrevOffset&RC=$Records');".
								'" />');
						}
						
						if (($Offset + $Records) < $Count) {
							$NextOffset = $Offset+$Records;
							echo('<input class="nextbutton" type="button" value="Next" onclick="'.
								"AJAX('$STNTarget','$STNPath','For=STN&T=$STNTarget&TID=$TSTID&RO=$NextOffset&RC=$Records');".
								'" />');
						}
						echo('<br /><div id="STN'.$STNPID.'SubDiv"></div>');
					}
				}
				else echo('You only have the right to view ProTasks your account is linked by, which does not include the requested ProTask.');
			}
			else echo('You do not have the required access rights to view ProTasks.');
		}
		if (strlen($STNFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$STNFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Nested SubTasks Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$STNDieError.
		'</article>');
}
?>
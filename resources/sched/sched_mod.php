<?php
//XCal - We only want to pay attention to request variables if they're for the person modify page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'SCM')) {
	$For = 'SCM';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$SCMTarget = $_REQUEST['T'];
	if (isset($_REQUEST['M'])) //XCal - [XC:M|Mode:N,M|New,Modify;]
		$SCMMode = $_REQUEST['M'];
	else $SCMMode = 'M';

	if (isset($_REQUEST['ID']))
		$SCMID = $_REQUEST['ID'];
	else $SCMID = -1;
	
	if (isset($_REQUEST['SCT']))
		$SCMSCTID = $_REQUEST['SCT'];
	if (isset($_REQUEST['BEG']))
		$SCMStart = $_REQUEST['BEG'];
	if (isset($_REQUEST['END']))
		$SCMEnd = $_REQUEST['END'];
	if (isset($_REQUEST['BRK']))
		$SCMBreaks = $_REQUEST['BRK'];	
}
elseif (isset($For) and ($For == 'SCM')) {
	if (isset($Target))
		$SCMTarget = $Target;
	if (isset($ID))
		$SCMID = $ID;
	if (isset($Mode))
		$SCMMode = $Mode;
	elseif (!isset($SCMMode))
		$SCMMode = 'M';
}
else $SCMMode = '<None>';
$SCMPath = 'resources/sched/sched_mod.php';
$SCMDieError = '';
$SCMFailError = '';
$SCMModes = array('N','M');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if ((! isset($SCMID)) and ($SCMMode == 'M'))
	$SCMDieError .= 'Schedule Modify has not been passed a schedule item to modify.<br />';
if (! isset($SCMTarget))
	$SCMDieError .= 'Schedule Modify has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($SCMMode,$SCMModes))
	$SCMDieError .= "Schedule Modify has been passed a mode ($SCMMode) which it does not support.<br />";

if ($SCMDieError == '') {
	if (! function_exists('CheckToken'))		
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$SCHSec = GetAreaSecurity($sql, 5);
		if ($SCHSec['NewMod'] == 2)
			$CanEdit = true;
		else $CanEdit = ItemLinked($sql,8,$SCMID);
		if ($CanEdit) {
			if (isset($SCMBreaks)) {
				if ($SCMMode == 'N') {
					$stm = $sql->prepare(
							'INSERT INTO sch_schedule_items ('.
							'SCI_SCT_ID,SCI_START,SCI_END,SCI_BREAKS,SCI_UPDATED) VALUES (?,?,?,?,current_timestamp())');
					if ($stm) {
						$stm->bind_param('isss',$SCMSCTID,$SCMStart,$SCMEnd,$SCMBreaks);
						if ($stm->execute()) {
							$stm->free_result();
							if ($stm->prepare('SELECT LAST_INSERT_ID()')) {
								if ($stm->execute()) {
									$stm->bind_result($SCMID);
									$stm->fetch();
									echo('<article>'.
											'<h2>Schedule Saved</h2>'.
											'<div class="article-info">'.
											'Message generated on <time datetime ="'. date('Y-m-d H:i:s') .'">'. date('Y-m-d H:i:s') .'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a></div>'.
											"<p>The schedule item was added successfully.<br />".
											'<input class="button" type="button" value="View Item" onclick="'.
											"AJAX('scheditem','resources/sched/sched_view.php','For=SCV&T=scheditem&ID=$SCMID');\" />".
											'</p></article>');
									$stm->free_result();
								}
								else $SCMFailError .= 'Schedule Modify encountered an error retrieving the added items ID ('.$stm->error.').<br />';
							}
							else $SCMFailError .= 'Schedule Modify encountered an error preparing to retrieve the added items ID ('.$stm->error.').<br />';
						}
						else $SCMFailError .= 'Schedule Modify encountered an error adding the item ('.$stm->error.').<br />';
					}
					else $SCMFailError .= 'Schedule Modify encountered an error preparing to add the item ('.$sql->error.').<br />';
				}
				else { //XCal - It's not flagged as a new record so must be modify
					$stm = $sql->prepare(
							'UPDATE sch_schedule_items SET '.
							'SCI_SCT_ID = ?,SCI_START = ?,SCI_END = ?, SCI_BREAKS = ?, SCI_UPDATED = current_timestamp() '.
							'WHERE SCI_ID = ?');
					if ($stm) {
						$stm->bind_param('isssi',$SCMSCTID,$SCMStart,$SCMEnd,$SCMBreaks,$SCMID);
						if ($stm->execute()) {
							$stm->free_result();
							echo('<article>'.
									'<h2>Schedule Saved</h2>'.
									'<div class="article-info">'.
									'Message generated on <time datetime ="'. date('Y-m-d H:i:s') .'">'. date('Y-m-d H:i:s') .'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a></div>'.
									"<p>The schedule item was modified successfully.<br />".
									'<input class="button" type="button" value="View Item" onclick="'.
									"AJAX('$SCMTarget','resources/sched/sched_view.php','For=SCV&T=$SCMTarget&ID=$SCMID');\" />".
									'</p></article>');
						}
						else $SCMFailError .= 'Schedule Modify encountered an error modifying the item ('.$stm->error.').<br />';
					}
					else $SCMFailError .= 'Schedule Modify encountered an error preparing to modify the item ('.$sql->error.').<br />';
				}
				
			}
			else { //XCal - No break time passed so we want the input screen
				if ($SCMID > 0) {
					$stm = $sql->prepare(
							'SELECT SCI_SCT_ID,SCI_START,SCI_END,SCI_BREAKS,SCI_UPDATED '.
							'FROM sch_schedule_items '.
							'WHERE SCI_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$SCMID);
						$stm->execute();
						$stm->bind_result($SCMSCTID,$SCMStart,$SCMEnd,$SCMBreaks,$SCMUpdated);
						$stm->fetch();
						$stm->free_result();
					}
					else $TPLFailError .= 'Schedule Modify encountered an error preparing to get schedule details for modification.<br />';
				}
				else {
					$SCMSCTID = 0;
					if (!isset($SCMStart))
						$SCMStart = date('Y-m-d H:i:s');
					if (!isset($SCMEnd))
						$SCMEnd = date('Y-m-d H:i:s');					
					$SCMBreaks = '1970-01-01 00:00:00';
					$SCMUpdated = 'Never';
				}
				//XCal - Now we've got everything we need to build the New/Modify screen, so do that
				echo("<input id=\"SCI_ID\" type=\"hidden\" value=\"$SCMID\" />");
				echo(
						'<table>'.
						'<tr><td class="fieldname">Type</td>'.
						'<td><select id="SCI_SCT_ID">');
				$stm = $sql->prepare(
						'SELECT SCT_ID,SCT_NAME '.
						'FROM sch_schedule_types '.
						'WHERE SCT_SR = 0 OR SCT_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$SCMSCTID);
					if ($stm->execute()) {
						$stm->bind_result($SCTID,$SCTName);
						while ($stm->fetch()) {
							if ($SCMSCTID == 0)
								$SCMSCTID = $SCTID;
							echo(
									'<option');
							
							if ($SCTID == $SCMSCTID)
								echo(' selected');
						
							echo(	" value=\"$SCTID\">$SCTName</option>");
						}
					}
					else $TPLFailError .= 'Schedule Modify failed getting available types with error: '.$stm->error.'<br />';
				}
				else $TPLFailError .= 'Schedule Modify encountered an error preparing to get available types.<br />';
			
				echo(
						'</select></td></tr>'.
						'<tr><td class="fieldname">Start</td>'.
						'<td><input id="SCI_START_D" type="date" value="'.date('Y-m-d',strtotime($SCMStart)).'" /><input id="SCI_START_T" type="time" value="'.date('H:i',strtotime($SCMStart)).'" /></td></tr>'.
						'<tr><td class="fieldname">End</td>'.
						'<td><input id="SCI_END_D" type="date" value="'.date('Y-m-d',strtotime($SCMEnd)).'" /><input id="SCI_END_T" type="time" value="'.date('H:i',strtotime($SCMEnd)).'" /></td></tr>'.
						'<tr><td class="fieldname">Breaks</td>'.
						'<td>Days:<input id="BRK_DAYS" type="number" value="'.date('z',strtotime($SCMBreaks)).'" min="0" max="365" />'.
						'Hours:<input id="BRK_HOURS" type="number" value="'.date('H',strtotime($SCMBreaks)).'" min="0" max="23" />'.
						'Minutes:<input id="BRK_MINS" type="number" value="'.date('i',strtotime($SCMBreaks)).'" min="0" max="59" /></td></tr>'.
						'<tr><td class="fieldname">Updated</td>'.
						"<td>$SCMUpdated</td></tr>".
						'</table>'.
						'<input class="save" type="button" value="Save" onclick="SaveSchedule('."'$SCMTarget','$SCMMode'".');" />'.
						'<input class="cancel" type="button" value="Cancel" onclick="'.
						"AJAX('$SCMTarget','resources/sched/sched_view.php','For=SCV&T=$SCMTarget');\" /><br />");		
			}
			if (strlen($SCMFailError) > 0)
				echo("Sorry to trouble you, but you may want to know about the following issues.<br />$SCMFailError");
		}
		elseif ($SCHSec['NewMod'] == 1)
			echo('You only have the right to modify schedules which your account is linked by, which does not include the requested item.');
		else echo('You do not have the required access rights to modify schedules.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Schedule Modify Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$SCMDieError.
			'</article>');
}
?>
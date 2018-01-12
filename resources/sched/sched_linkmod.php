<?php
//XCal - This unit is only to be included from type_links or somewhere that does the [initialisation:UK;initialization:US] for it
// This unit REQUIRES the variables $TPLMode,$ONRLTPID,$ONRID,$CHDLTPID to be set already
if (isset($_REQUEST['SCT']))
	$LSCSCTID = $_REQUEST['SCT'];
if (isset($_REQUEST['BEG']))
	$LSCStart = $_REQUEST['BEG'];
if (isset($_REQUEST['END']))
	$LSCEnd = $_REQUEST['END'];
if (isset($_REQUEST['BRK']))
	$LSCBreaks = $_REQUEST['BRK'];
if (! isset($TPLLinkID))
	$TPLLinkID = 0;
	
//XCal - If the start has been passed in the request then it's a save action
if (isset($LSCStart)) {
	//$_SESSION['DebugStr'] .= 'Link Schedule Modify: Save Mode<br />';
	$stm = $sql->prepare("SELECT 0,'',? INTO @Code,@Msg,@SciID");
	if ($stm) {
		$stm->bind_param('i',$TPLLinkID);
		$stm->execute();
		$stm->free_result();
		if ($stm->prepare('CALL sp_sch_addedit_link_sched(?,?,@SciID,?,?,?,?,@Code,@Msg)')) {
			$stm->bind_param('iiisss',$ONRLTPID,$ONRID,$LSCSCTID,$LSCStart,$LSCEnd,$LSCBreaks);
			if ($stm->execute()) {
				$stm->free_result();
				if ($stm->prepare('SELECT @Code,@Msg,@SciID')) {
					$stm->execute();
					$stm->bind_result($RCode,$RMsg,$TPLLinkID);
					$stm->fetch();
					echo("New/Modify result: $RMsg<br />");
					$stm->free_result();
				}
				else $TPLFailError .= 'New/Modify link schedule encountered an error preparing to get link results.<br />';
			}
			else echo('New/Modify link schedule failed with error : '.$stm->error.'<br />');
		}
		else $TPLFailError .= 'New/Modify link schedule encountered an error preparing to add/edit the item.<br />';
	}
	else $TPLFailError .= 'New/Modify link schedule encountered an error initialising the add/edit operation.<br />';
	//XCal - Fail or succeed we want the link types screen to go back to list mode
	$TPLMode = 'L';
}
else { //XCal - No name passed so we want the input screen
	if ($TPLLinkID > 0) {
		$stm = $sql->prepare(
				'SELECT SCI_SCT_ID,SCI_START,SCI_END,SCI_BREAKS,SCI_UPDATED '.
				'FROM sch_schedule_items '.
				'WHERE SCI_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$TPLLinkID);
			$stm->execute();
			$stm->bind_result($LSCSCTID,$LSCStart,$LSCEnd,$LSCBreaks,$LSCUpdated);
			$stm->fetch();
			$stm->free_result();
		}
		else $TPLFailError .= 'New/Modify link schedule encountered an error preparing to get schedule details for modification.<br />';
	}
	else {
		$LSCSCTID = 0;
		$LSCStart = date('Y-m-d H:i:s'); //XCal - TODO - Find out how to add days and hours in PHP
		$LSCEnd = date('Y-m-d H:i:s');		
		$LSCBreaks = date(0);
		$LSCUpdated = 'Never';
	}
	//XCal - Now we've got everything we need to build the New/Modify screen, so do that
	echo("<input id=\"LNK_SCI_ID\" type=\"hidden\" value=\"$TPLLinkID\" />");
	echo(
			'<table>'.
			'<tr><td class="fieldname">Type</td>'.
			'<td><select id="LNK_SCI_SCT_ID">');
	$stm = $sql->prepare(
			'SELECT SCT_ID,SCT_NAME '.
			'FROM sch_schedule_types '.
			'WHERE SCT_SR = 0 OR SCT_ID = ?');
	if ($stm) {
		$stm->bind_param('i',$LSCSCTID);
		if ($stm->execute()) {
			$stm->bind_result($SCTID,$SCTName);
			while ($stm->fetch()) {
				if ($LSCSCTID == 0)
					$LSCSCTID = $SCTID;
				echo(
						'<option');
				
				if ($SCTID == $LSCSCTID)
					echo(' selected');
			
				echo(	" value=\"$SCTID\">$SCTName</option>");
			}
		}
		else $TPLFailError .= 'New/Modify link schedule failed getting available types with error: '.$stm->error.'<br />';
	}
	else $TPLFailError .= 'New/Modify link schedule encountered an error preparing to get available types.<br />';

	echo(
			'</select></td></tr>'.
			'<tr><td class="fieldname">Start</td>'.
			'<td><input id="LNK_SCI_START_D" type="date" value="'.date('Y-m-d',strtotime($LSCStart)).'" /><input id="LNK_SCI_START_T" type="time" value="'.date('H:i',strtotime($LSCStart)).'" /></td></tr>'.
			'<tr><td class="fieldname">End</td>'.
			'<td><input id="LNK_SCI_END_D" type="date" value="'.date('Y-m-d',strtotime($LSCEnd)).'" /><input id="LNK_SCI_END_T" type="time" value="'.date('H:i',strtotime($LSCEnd)).'" /></td></tr>'.
			'<tr><td class="fieldname">Breaks</td>'.
			'<td>Days:<input id="LNK_BRK_DAYS" type="number" value="'.date('z',strtotime($LSCBreaks)).'" min="0" max="365" />'.
			'Hours:<input id="LNK_BRK_HOURS" type="number" value="'.date('H',strtotime($LSCBreaks)).'" min="0" max="23" />'.
			'Minutes:<input id="LNK_BRK_MINS" type="number" value="'.date('i',strtotime($LSCBreaks)).'" min="0" max="59" /></td></tr>'.
			'<tr><td class="fieldname">Updated</td>'.
			"<td>$LSCUpdated</td></tr>".
			'</table>'.
			'<input class="save" type="button" value="Save" onclick="SaveLinkSched('."'$TPLTarget','$ONRLTPID','$ONRID','$TPLMode'".');" />'.
			'<input class="cancel" type="button" value="Cancel" onclick="'.
			"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&LID=$TPLLinkID');\" /><br />");		
}
?>
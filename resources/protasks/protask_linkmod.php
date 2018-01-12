<?php
//XCal - This unit is only to be included from type_links or somewhere that does the [initialisation:UK;initialization:US] for it
// This unit REQUIRES the variables $TPLMode,$ONRLTPID,$ONRID,$CHDLTPID to be set already
if (isset($_REQUEST['TST']))
	$LPTTSTID = $_REQUEST['TST'];
if (isset($_REQUEST['Tit']))
	$LPTTitle = $_REQUEST['Tit'];
if (isset($_REQUEST['Desc']))
	$LPTDesc = $_REQUEST['Desc'];
if (! isset($TPLLinkID))
	$TPLLinkID = 0;
	
//XCal - Ifthe name has been passed in the request then it's a save action
if (isset($LPTTitle)) {
	//$_SESSION['DebugStr'] .= 'Link Protask Modify: Save Mode<br />';
	$stm = $sql->prepare("SELECT 0,'',? INTO @Code,@Msg,@TskID");
	if ($stm) {
		$stm->bind_param('i',$TPLLinkID);
		$stm->execute();
		$stm->free_result();
		if ($stm->prepare('CALL sp_pro_addedit_link_task_nodoc(?,?,@TskID,?,?,?,@Code,@Msg)')) {
			$stm->bind_param('iiiss',$ONRLTPID,$ONRID,$LPTTSTID,$LPTTitle,$LPTDesc);
			if ($stm->execute()) {
				$stm->free_result();
				if ($stm->prepare('SELECT @Code,@Msg,@TskID')) {
					$stm->execute();
					$stm->bind_result($RCode,$RMsg,$TPLLinkID);
					$stm->fetch();
					echo("New/Modify result: $RMsg<br />");
					$stm->free_result();
				}
				else $TPLFailError .= 'New/Modify link protask encountered an error preparing to get link results.<br />';
			}
			else echo('New/Modify link protask failed with error : '.$stm->error.'<br />');
		}
		else $TPLFailError .= 'New/Modify link protask encountered an error preparing to add/edit the task.<br />';
	}
	else $TPLFailError .= 'New/Modify link protask encountered an error initialising the add/edit operation.<br />';
	//XCal - Fail or succesd we want the link types screen to go back to list mode
	$TPLMode = 'L';
}
else { //XCal - No name passed so we want the input screen
	if ($TPLLinkID > 0) {
		$stm = $sql->prepare(
				'SELECT TSK_TST_ID,TSK_TITLE,TSK_DESCRIPTION,TSK_UPDATED '.
				'FROM pro_tasks '.
				'WHERE TSK_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$TPLLinkID);
			$stm->execute();
			$stm->bind_result($LPTTSTID,$LPTTitle,$LPTDesc,$LPTUpdated);
			$stm->fetch();
			$stm->free_result();
		}
		else $TPLFailError .= 'New/Modify link protask encountered an error preparing to get task details for modification.<br />';
	}
	else {
		$LPTTSTID = 0;
		$LPTTitle = '';
		$LPTDesc = '';
		$LPTUpdated = 'Never';
	}
	//XCal - Now we've got everything we need to build the New/Modify screen, so do that
	echo("<input id=\"LNK_TSK_ID\" type=\"hidden\" value=\"$TPLLinkID\" />");
	if (isset($ExID))
		echo("<input id=\"EX_TSK_ID\" type=\"hidden\" value=\"$ExID\" />");
	echo(
			'<table>'.
			'<tr><td class="fieldname">Type</td>'.
			'<td><select id="LNK_TSK_TST_ID">');
	$stm = $sql->prepare(
			'SELECT TST_ID,TST_NAME '.
			'FROM pro_task_types '.
			'WHERE TST_SR = 0 OR TST_ID = ?');
	if ($stm) {
		$stm->bind_param('i',$LPTTSTID);
		if ($stm->execute()) {
			$stm->bind_result($TSTID,$TTName);
			while ($stm->fetch()) {
				if ($LPTTSTID == 0)
					$LPTTSTID = $TSTID;
				echo(
						'<option');
				
				if ($TSTID == $LPTTSTID)
					echo(' selected');
			
				echo(	" value=\"$TSTID\">$TTName</option>");
			}
		}
		else $TPLFailError .= 'New/Modify link protask failed getting available types with error: '.$stm->error.'<br />';
	}
	else $TPLFailError .= 'New/Modify link protask encountered an error preparing to get available types.<br />';

		
	echo(
			'</select></td></tr>'.
			'<tr><td class="fieldname">Title</td>'.
			"<td><input id=\"LNK_TSK_TITLE\" type=\"text\" value=\"$LPTTitle\" /></td></tr>".
			'<tr><td class="fieldname">Description</td>'.
			"<td><textarea id=\"LNK_TSK_DESC\" cols=\"50\" rows=\"5\">$LPTDesc</textarea></td></tr>".
			'<tr><td class="fieldname">Updated</td>'.
			"<td>$LPTUpdated</td></tr>".
			'</table>'.
			'<input class="save" type="button" value="Save" onclick="SaveLinkTask('."'$TPLTarget','$ONRLTPID','$ONRID','$TPLMode'".');" />'.
			'<input class="cancel" type="button" value="Cancel" onclick="'.
			"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&LID=$TPLLinkID');\" /><br />");		
}
?>
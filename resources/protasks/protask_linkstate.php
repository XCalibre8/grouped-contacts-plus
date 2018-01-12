<?php
	//XCal - This unit is only to be included from type_links or somewhere that does the [initialisation:UK;initialization:US] for it
	// This unit REQUIRES the variables $TPLMode,$CHDID,$CHDLTPID,$ONRID to be set already
	if (isset($_REQUEST['TST']))
		$PTSType = $_REQUEST['TST'];
	if (isset($_REQUEST['TSS']))
		$PTSState = $_REQUEST['TSS'];
	if (isset($_REQUEST['NT']))
		$PTSNote = $_REQUEST['NT'];
	
	if (! isset($TPLLinkID)) 
		die('The link task state modify screen has been requested without a linked identifier and has died.');
	
	
	//XCal - If the state has been passed then the link needs saving
	if (isset($PTSState)) {
		$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
		if ($stm) {
			$stm->execute();
			$stm->free_result();
			if ($stm->prepare('CALL sp_pro_mod_linked(?,?,?,?,?,@Code,@Msg)')) {
				$_SESSION['DebugStr'] .= "Link ProTask State: Modifying link with params TaskID: $ONRID, Link Type: $CHDLTPID, Link: $TPLLinkID, State:$PTSState, Note:$PTSNote<br />";
				$stm->bind_param('iiiss',$ONRID,$CHDLTPID,$TPLLinkID,$PTSState,$PTSNote);
				if ($stm->execute()) {
					$stm->free_result();
					if ($stm->prepare('SELECT @Code,@Msg')) {
						$stm->execute();
						$stm->bind_result($RCode,$RMsg);
						$stm->fetch();
						echo("Link Update result: $RMsg<br />");
						$stm->free_result();
					}
					else $TPLFailError .= 'Update link task state encountered an error preparing to get link results.<br />';
				}
				else $TPLFailError .= 'Update link task state failed with error : '.$stm->error.'<br />';
			}
			else $TPLFailError .= 'Update link task state encountered an error preparing to add/edit the task.<br />';
		}
		else $TPLFailError .= 'Update link task state encountered an error initialising the add/edit operation.<br />';
		//XCal - Fail or succesd we want the link types screen to go back to list mode
		$TPLMode = 'L';		
	}
	else { //XCal - No state passed so we want the input screen
		if ($TPLLinkID > 0) {
			$stm = $sql->prepare(
					'SELECT TST_NAME,TSK_TITLE,TSK_DESCRIPTION,LNK_X_ID,LNK_X_STR,TSK_UPDATED '.
					'FROM pro_tasks '.
					'JOIN pro_task_types ON TST_ID = TSK_TST_ID '.
					'JOIN lnk_links ON LNK_ONR_LTP_ID = 5 AND LNK_ONR_ID = TSK_ID '.
						'AND LNK_CHD_LTP_ID = ? AND LNK_CHD_ID = ? '.
					'WHERE TSK_ID = ?');
			if ($stm) {
				$stm->bind_param('iii',$CHDLTPID,$TPLLinkID,$ONRID);
				$stm->execute();
				$stm->bind_result($PTSType,$PTSTitle,$PTSDesc,$PTSState,$PTSNote,$PTSUpdated);
				$stm->fetch();
				$stm->free_result();
			}
			else $TPLFailError .= 'Update link task state encountered an error preparing to get task details for modification ('.$sql->error.').<br />';
		}
		else {
			$PTSType = 0;
			$PTSTitle = '';
			$PTSDesc = '';
			$PTSState = 0;
			$PTSNote = '';
			$PTSUpdated = 'Never';
		}
		//XCal - Now we've got everything we need to build the link update screen, so do that
		echo("<input id=\"LKS_TSK_ID\" type=\"hidden\" value=\"$ONRID\" />");
		echo("<h5>Set Status for $PTSType</h5><h4>$PTSTitle</h4><br />".
			 $PTSDesc.'<br />'.
			'<table>'.
			'<tr><td class="fieldname">State</td>'.
				'<td><select id="LKS_STATE">');
		//XCal - TODO - This will need to be refined with state availability and progression
		$stm = $sql->prepare(
				'SELECT TSS_ID,TSS_NAME '.
				'FROM pro_task_states '.
				'WHERE TSS_SR = 0 OR TSS_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$PTSState);
			if ($stm->execute()) {
				$stm->bind_result($TSSID,$TState);
				while ($stm->fetch()) {
					if ($PTSState == 0)
						$PTSState = $TSSID;
					echo(
							'<option');
					
					if ($TSSID == $PTSState)
						echo(' selected');
				
					echo(	" value=\"$TSSID\">$TState</option>");
				}
			}
			else $TPLFailError .= 'New/Modify linking protask failed getting available types with error: '.$stm->error.'<br />';
		}
		else $TPLFailError .= 'New/Modify linking protask encountered an error preparing to get available types.<br />';

			
		echo(
				'</select></td></tr>'.
				'<tr><td class="fieldname">Note</td>'.
				"<td><input id=\"LKS_NOTE\" type=\"text\" value=\"$PTSNote\" /></td></tr>".
				'<tr><td class="fieldname">Updated</td>'.
				"<td>$PTSUpdated</td></tr>".
				'</table>'.
				'<input class="save" type="button" value="Save" onclick="'.
				"SaveLinkTaskState('$TPLTarget','$CHDLTPID','$TPLLinkID','$TPLMode');".
				'" />'.
				'<input class="cancel" type="button" value="Cancel" onclick="'.
				"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&LID=$TPLLinkID');\" /><br />");		
	}
?>
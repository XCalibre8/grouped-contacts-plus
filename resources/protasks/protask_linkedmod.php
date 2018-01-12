<?php
	//XCal - This unit is only to be included from type_links or somewhere that does the [initialisation:UK;initialization:US] for it
	// This unit REQUIRES the variables $TPDMode,$CHDID,$CHDLTPID,$ONRID to be set already
	if (isset($_REQUEST['TST']))
		$PTLType = $_REQUEST['TST'];
	if (isset($_REQUEST['TSS']))
		$PTLState = $_REQUEST['TSS'];
	if (isset($_REQUEST['NT']))
		$PTLNote = $_REQUEST['NT'];
	
	if (! isset($TPDLinkID)) 
		die('The linking protask modify screen has been requested without a linked identifier and has died.');
	
	//XCal - If the state has been passed then the link needs saving
	if (isset($PTLState)) {
		$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
		if ($stm) {
			$stm->execute();
			$stm->free_result();
			if ($stm->prepare('CALL sp_pro_mod_linked(?,?,?,?,?,@Code,@Msg)')) {
				//$_SESSION['DebugStr'] .= "Linking ProTasks: Modifying link with params TaskID: $TPDLinkID, Link Type: $CHDLTPID, Link: $CHDID, State:$PTLState, Note:$PTLNote<br />";
				$stm->bind_param('iiiss',$TPDLinkID,$CHDLTPID,$CHDID,$PTLState,$PTLNote);
				if ($stm->execute()) {
					$stm->free_result();
					if ($stm->prepare('SELECT @Code,@Msg')) {
						$stm->execute();
						$stm->bind_result($RCode,$RMsg);
						$stm->fetch();
						echo("Link Update result: $RMsg<br />");
						$stm->free_result();
					}
					else $TPDFailError .= 'Update linking protask encountered an error preparing to get link results.<br />';
				}
				else $TPDFailError .= 'Update linking protask failed with error : '.$stm->error.'<br />';
			}
			else $TPDFailError .= 'Update linking protask encountered an error preparing to add/edit the task.<br />';
		}
		else $TPDFailError .= 'Update linking protask encountered an error initialising the add/edit operation.<br />';
		//XCal - Fail or succesd we want the linked types screen to go back to list mode
		$TPDMode = 'L';		
	}
	else { //XCal - No name or state passed so we want the input screen
		if ($TPDLinkID > 0) {
			$stm = $sql->prepare(
					'SELECT TST_NAME,TSK_TITLE,TSK_DESCRIPTION,LNK_X_ID,LNK_X_STR,TSK_UPDATED '.
					'FROM pro_tasks '.
					'JOIN pro_task_types ON TST_ID = TSK_TST_ID '.
					'JOIN lnk_links ON LNK_ONR_LTP_ID = 5 AND LNK_ONR_ID = TSK_ID '.
						'AND LNK_CHD_LTP_ID = ? AND LNK_CHD_ID = ? '.
					'WHERE TSK_ID = ?');
			if ($stm) {
				$stm->bind_param('iii',$CHDLTPID,$CHDID,$TPDLinkID);
				$stm->execute();
				$stm->bind_result($PTLType,$PTLTitle,$PTLDesc,$PTLState,$PTLNote,$PTLUpdated);
				$stm->fetch();
				$stm->free_result();
			}
			else $TPDFailError .= 'Update linking protask encountered an error preparing to get task details for modification ('.$sql->error.').<br />';
		}
		else {
			$PTLType = 0;
			$PTLTitle = '';
			$PTLDesc = '';
			$PTLState = 0;
			$PTLNote = '';
			$PTLUpdated = 'Never';
		}
		//XCal - Now we've got everything we need to build the link update screen, so do that
		echo("<input id=\"LKD_TSK_ID\" type=\"hidden\" value=\"$TPDLinkID\" />");
		echo("<h5>Set Status for $PTLType</h5><h4>$PTLTitle</h4><br />".
			 $PTLDesc.'<br />'.
			'<table>'.
			'<tr><td class="fieldname">State</td>'.
				'<td><select id="LKD_STATE">');
		//XCal - TODO - This will need to be refined with state availability and progression
		$stm = $sql->prepare(
				'SELECT TSS_ID,TSS_NAME '.
				'FROM pro_task_states '.
				'WHERE TSS_SR = 0 OR TSS_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$PTLState);
			if ($stm->execute()) {
				$stm->bind_result($TSSID,$TState);
				while ($stm->fetch()) {
					if ($PTLState == 0)
						$PTLState = $TSSID;
					echo(
							'<option');
					
					if ($TSSID == $PTLState)
						echo(' selected');
				
					echo(	" value=\"$TSSID\">$TState</option>");
				}
			}
			else $TPDFailError .= 'New/Modify linking protask failed getting available types with error: '.$stm->error.'<br />';
		}
		else $TPDFailError .= 'New/Modify linking protask encountered an error preparing to get available types.<br />';

			
		echo(
				'</select></td></tr>'.
				'<tr><td class="fieldname">Note</td>'.
				"<td><input id=\"LKD_NOTE\" type=\"text\" value=\"$PTLNote\" /></td></tr>".
				'<tr><td class="fieldname">Updated</td>'.
				"<td>$PTLUpdated</td></tr>".
				'</table>'.
				'<input class="save" type="button" value="Save" onclick="'.
				"SaveLinkingTaskState('$TPDTarget','$CHDLTPID','$CHDID','$TPDMode');".
				'" />'.
				'<input class="cancel" type="button" value="Cancel" onclick="'.
				"AJAX('$TPDTarget','$TPDPath','$TPDKeys&M=L&LID=$TPDLinkID');\" /><br />");		
	}
?>
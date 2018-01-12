<?php
	//XCal - This unit is only to be included from type_links or somewhere that does the [initialisation:UK;initialization:US] for it
	// This unit REQUIRES the variables $TPLMode,$ONRLTPID,$ONRID,$CHDLTPID to be set already
	if (isset($_REQUEST['TIT']))
		$LPRTitID = $_REQUEST['TIT'];
	if (isset($_REQUEST['FNames']))
		$LPRForenames = $_REQUEST['FNames'];
	if (isset($_REQUEST['SName']))
		$LPRSurname = $_REQUEST['SName'];
	if (! isset($TPLLinkID))
		$TPLLinkID = 0;
	
	//XCal - If either of the names have been passed in the request then it's a save action
	if (isset($LPRFornames) or isset($LPRSurname)) {
		//$_SESSION['DebugStr'] .= 'Link Person Modify: Save Mode<br />';
// 		if (! is_int($TPLLinkID))
// 			die('The request to New/Modify link person passed a non integer as the link ID. I\'ve stopped execution to prevent a possible SQL injection attack.');
		$stm = $sql->prepare("SELECT 0,'',$TPLLinkID INTO @Code,@Msg,@PerID");
		if ($stm) {
			$stm->execute();
			$stm->free_result();
			if ($stm->prepare('CALL sp_con_addedit_link_person_only(?,?,@PerID,?,?,?,@Code,@Msg)')) {
				$_SESSION['DebugStr'] .= 'Saving person with title ID '.$LPRTitID.'<br />';
				$stm->bind_param('iiiss',$ONRLTPID,$ONRID,$LPRTitID,$LPRForenames,$LPRSurname);
				if ($stm->execute()) {
					$stm->free_result();
					if ($stm->prepare('SELECT @Code,@Msg,@PerID')) {
						$stm->execute();
						$stm->bind_result($RCode,$RMsg,$TPLLinkID);
						$stm->fetch();
						echo("New/Modify result: $RMsg<br />");
						$stm->free_result();
					}
					else $TPLFailError .= 'New/Modify link person encountered an error preparing to get link results.<br />';
				}
				else echo('New/Modify link person failed with error : '.$stm->error.'<br />');
			}
			else $TPLFailError .= 'New/Modify link person encountered an error preparing to add/edit the person.<br />';
		}
		else $TPLFailError .= 'New/Modify link person ecountered an error initialising the add/edit operation.<br />';
		//XCal - Fail or succeed we want the link types screen to go back to list mode
		$TPLMode = 'L';
	}
	else { //XCal - No name passed so we want the input screen
		if ($TPLLinkID > 0) {
			$stm = $sql->prepare(
					'SELECT PER_TIT_ID,PER_FORENAMES,PER_SURNAME,PER_UPDATED '.
					'FROM con_people '.
					'WHERE PER_ID = ?');
			if ($stm) {
				$stm->bind_param('i',$TPLLinkID);
				$stm->execute();
				$stm->bind_result($LPRTitID,$LPRForenames,$LPRSurname,$LPRUpdated);
				$stm->fetch();
				$stm->free_result();
			}
			else $TPLFailError .= 'New/Modify link person encountered an error preparing to get person details for modification.<br />';
		}
		else {
			$LPRTitID = 0;
			$LPRForenames = '';
			$LPRSurname = '';
			$LPRUpdated = 'Never';
		}
		//XCal - Now we've got everything we need to build the New/Modify screen, so do that
		echo("<input id=\"LNK_PER_ID\" type=\"hidden\" value=\"$TPLLinkID\" />");
		if (isset($ExID))
			echo("<input id=\"EX_PER_ID\" type=\"hidden\" value=\"$ExID\" />");
		echo(
				'<table>'.
				'<tr><td class="fieldname">Title</td>'.
				'<td><select id="LNK_PER_TIT_ID">');
		$stm = $sql->prepare(
				'SELECT TIT_ID,TIT_TITLE '.
				'FROM con_titles '.
				'WHERE TIT_SR = 0 OR TIT_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$LPRTitID);
			if ($stm->execute()) {
				$stm->bind_result($TitID,$Title);
				while ($stm->fetch()) {
					if ($LPRTitID == 0)
						$LPRTitID = $TitID;
					echo(
							'<option');
					
					if ($TitID == $LPRTitID)
						echo(' selected');
				
					echo(	" value=\"$TitID\">$Title</option>");
				}
			}
			else $TPLFailError .= 'New/Modify link person failed getting available titles with error: '.$stm->error.'<br />';
		}
		else $TPLFailError .= 'New/Modify link person encountered an error preparing to get available titles.<br />';

			
		echo(
				'</select></td></tr>'.
				'<tr><td class="fieldname">Forenames</td>'.
				"<td><input id=\"LNK_PER_FORENAMES\" type=\"text\" value=\"$LPRForenames\" /></td></tr>".
				'<tr><td class="fieldname">Surname</td>'.
				"<td><input id=\"LNK_PER_SURNAME\" type=\"text\" value=\"$LPRSurname\" /></td></tr>".
				'<tr><td class="fieldname">Updated</td>'.
				"<td>$LPRUpdated</td></tr>".
				'</table>'.
				'<input class="save" type="button" value="Save" onclick="SaveLinkPerson('."'$TPLTarget','$ONRLTPID','$ONRID','$TPLMode'".');" />'.
				'<input class="cancel" type="button" value="Cancel" onclick="'.
				"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&LID=$TPLLinkID');\" /><br />");		
	}
?>
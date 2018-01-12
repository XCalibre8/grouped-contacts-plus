<?php
	//XCal - This unit is only to be included from type_links or somewhere that does the [initialisation:UK;initialization:US] for it
	// This unit REQUIRES the variables $TPLMode,$ONRLTPID,$ONRID,$CHDLTPID to be set already
	if (isset($_REQUEST['CT']))
		$LCNCTTID = $_REQUEST['CT'];
	if (isset($_REQUEST['ST']))
		$LCNSpeakTo = $_REQUEST['ST'];
	if (isset($_REQUEST['CM']))
		$LCNCPTID = $_REQUEST['CM'];
	if (isset($_REQUEST['CNP']))
		$LCNConPoint = $_REQUEST['CNP'];
	if (! isset($TPLLinkID))
		$TPLLinkID = 0;
	
	//XCal - If values have been passed in the request then it's a save action
	if (isset($LCNCTTID) or isset($LCNSpeakTo) or isset($LCNConPoint)
			or isset($LCNCPTID)) {
		//$_SESSION['DebugStr'] .= 'Link Contact Point Modify: Save Mode<br />';
// 		if (! is_int($TPLLinkID))
// 			die('The request to New/Modify link contact point passed a non integer as the link ID. I\'ve stopped execution to prevent a possible SQL injection attack.');
		$stm = $sql->prepare("SELECT 0,'',? INTO @Code,@Msg,@CnpID");
		if ($stm) {
			$stm->bind_param('i',$TPLLinkID);
			$stm->execute();
			$stm->free_result();
			if ($stm->prepare('CALL sp_con_addedit_link_contact_point(?,?,@CnpID,?,?,?,?,@Code,@Msg)')) {
				$stm->bind_param('iiisss',$ONRLTPID,$ONRID,$LCNCTTID,$LCNSpeakTo,$LCNCPTID,$LCNConPoint);
				if ($stm->execute()) {
					$stm->free_result();
					if ($stm->prepare('SELECT @Code,@Msg,@CnpID')) {
						$stm->execute();
						$stm->bind_result($RCode,$RMsg,$TPLLinkID);
						$stm->fetch();
						echo("New/Modify result: $RMsg<br />");
						$stm->free_result();
					}
					else $TPLFailError .= 'New/Modify link contact point encountered an error preparing to get link results.<br />';
				}
				else echo('New/Modify link contact point failed with error : '.$stm->error.'<br />');
			}
			else $TPLFailError .= 'New/Modify link contact point encountered an error preparing to add/edit the address.<br />';
		}
		else $TPLFailError .= 'New/Modify link contact point ecountered an error initialising the add/edit operation.<br />';
		//XCal - Fail or succesd we want the link types screen to go back to list mode
		$TPLMode = 'L';
	}
	else { //XCal - No name passed so we want the input screen
		if ($TPLLinkID > 0) {
			$stm = $sql->prepare(
					'SELECT LNK_X_ID CTypeID,LNK_X_STR SpeakTo,CNP_CPT_ID,CNP_CONTACT,CNP_UPDATED '.
					'FROM con_contact_points '.
					'JOIN lnk_links ON LNK_ONR_LTP_ID = ? AND LNK_ONR_ID = ? AND LNK_CHD_LTP_ID = 3 AND LNK_CHD_ID = CNP_ID '.
					'WHERE CNP_ID = ?');
			if ($stm) {
				$stm->bind_param('iii',$ONRLTPID,$ONRID,$TPLLinkID);
				$stm->execute();
				$stm->bind_result($LCNCTTID,$LCNSpeakTo,$LCNCPTID,$LCNConPoint,$LCNUpdated);
				$stm->fetch();
				$stm->free_result();
			}
			else $TPLFailError .= 'New/Modify link address encountered an error preparing to get address details for modification.<br />';
		}
		else {
			$LCNCTTID = 0;
			$LCNSpeakTo = '';
			$LCNCPTID = '';
			$LCNConPoint = '';
			$LCNUpdated = 'Never';
		}
		//XCal - Now we've got everything we need to build the New/Modify screen, so do that
		echo("<input id=\"LNK_CNP_ID\" type=\"hidden\" value=\"$TPLLinkID\" />");
		if (isset($ExID))
			echo("<input id=\"EX_CNP_ID\" type=\"hidden\" value=\"$ExID\" />");
		echo(
				'<table>'.
				'<tr><td class="fieldname">Contact Type</td>'.
				'<td><select id="LNK_CNP_CTT_ID">');
		$stm = $sql->prepare(
				'SELECT CTT_ID,CTT_NAME '.
				'FROM con_contact_types '.
				'WHERE CTT_SR = 0 OR CTT_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$LCNCTTID);
			if ($stm->execute()) {
				$stm->bind_result($CTTID,$CTName);
				while ($stm->fetch()) {
					if ($LCNCTTID == 0)
						$LCNCTTID = $CTTID;
					echo(
							'<option');
					
					if ($CTTID == $LCNCTTID)
						echo(' selected');
				
					echo(	" value=\"$CTTID\">$CTName</option>");
				}
			}
			else $TPLFailError .= 'New/Modify link contact point failed getting available contact types with error ('.$stm->error.')<br />';
		}
		else $TPLFailError .= 'New/Modify link contact point encountered an error preparing to get available contact types.<br />';
		
		echo(
				'</select></td></tr>'.
				'<tr><td class="fieldname">Speak To</td>'.
				"<td><input id=\"LNK_CNP_SPEAK_TO\" type=\"text\" value=\"$LCNSpeakTo\" /></td></tr>".
				'<tr><td class="fieldname">Contact Method</td>'.
				'<td><select id="LNK_CNP_CPT_ID">');
		$stm = $sql->prepare(
				'SELECT CPT_ID,CPT_NAME '.
				'FROM con_contact_point_types '.
				'WHERE CPT_SR = 0 OR CPT_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$LCNCPTID);
			if ($stm->execute()) {
				$stm->bind_result($CPTID,$CMName);
				while ($stm->fetch()) {
					if ($LCNCPTID == 0)
						$LCNCPTID = $CPTID;
					echo(
							'<option');
						
					if ($CPTID == $LCNCPTID)
						echo(' selected');
		
					echo(	" value=\"$CPTID\">$CMName</option>");
				}
			}
			else $TPLFailError .= 'New/Modify link contact point failed getting available contact types with error ('.$stm->error.')<br />';
		}
		else $TPLFailError .= 'New/Modify link contact point encountered an error preparing to get available contact types.<br />';
		
		echo(
				'</select></td></tr>'.		
				'<tr><td class="fieldname">Contact</td>'.
				"<td><input id=\"LNK_CNP_CONTACT\" type=\"text\" value=\"$LCNConPoint\" /></td></tr>".
				'<tr><td class="fieldname">Updated</td>'.
				"<td>$LCNUpdated</td></tr>".
				'</table>'.
				'<input class="save" type="button" value="Save" onclick="SaveLinkContactPoint('."'$TPLTarget','$ONRLTPID','$ONRID','$TPLMode'".');" />'.
				'<input class="cancel" type="button" value="Cancel" onclick="'.
				"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&LID=$TPLLinkID');\" /><br />");		
	}
?>
<?php
	//XCal - This unit is only to be included from type_links or somewhere that does the [initialisation:UK;initialization:US] for it
	// This unit REQUIRES the variables $TPLMode,$ONRLTPID,$ONRID,$CHDLTPID to be set already
	if (isset($_REQUEST['GTP']))
		$LGRGTPID = $_REQUEST['GTP'];
	if (isset($_REQUEST['Name']))
		$LGRName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$LGRDesc = $_REQUEST['Desc'];
	if (! isset($TPLLinkID))
		$TPLLinkID = 0;
	
	//XCal - Ifthe name has been passed in the request then it's a save action
	if (isset($LGRName)) {
		//$_SESSION['DebugStr'] .= 'Link Group Modify: Save Mode<br />';
// 		if (! is_int($TPLLinkID))
// 			die('The request to New/Modify link group passed a non integer as the link ID. I\'ve stopped execution to prevent a possible SQL injection attack.');
		$stm = $sql->prepare("SELECT 0,'',$TPLLinkID INTO @Code,@Msg,@GrpID");
		if ($stm) {
			$stm->execute();
			$stm->free_result();
			if ($stm->prepare('CALL sp_grp_addedit_link_group_nodoc(?,?,@GrpID,?,?,?,@Code,@Msg)')) {
				$stm->bind_param('iiiss',$ONRLTPID,$ONRID,$LGRGTPID,$LGRName,$LGRDesc);
				if ($stm->execute()) {
					$stm->free_result();
					if ($stm->prepare('SELECT @Code,@Msg,@GrpID')) {
						$stm->execute();
						$stm->bind_result($RCode,$RMsg,$TPLLinkID);
						$stm->fetch();
						echo("New/Modify result: $RMsg<br />");
						$stm->free_result();
					}
					else $TPLFailError .= 'New/Modify link group encountered an error preparing to get link results.<br />';
				}
				else echo('New/Modify link group failed with error : '.$stm->error.'<br />');
			}
			else $TPLFailError .= 'New/Modify link group encountered an error preparing to add/edit the group.<br />';
		}
		else $TPLFailError .= 'New/Modify link group encountered an error initialising the add/edit operation.<br />';
		//XCal - Fail or succesd we want the link types screen to go back to list mode
		$TPLMode = 'L';
	}
	else { //XCal - No name passed so we want the input screen
		if ($TPLLinkID > 0) {
			$stm = $sql->prepare(
					'SELECT GRP_GTP_ID,GRP_NAME,GRP_DESCRIPTION,GRP_UPDATED '.
					'FROM grp_groups '.
					'WHERE GRP_ID = ?');
			if ($stm) {
				$stm->bind_param('i',$TPLLinkID);
				$stm->execute();
				$stm->bind_result($LGRGTPID,$LGRName,$LGRDesc,$LGRUpdated);
				$stm->fetch();
				$stm->free_result();
			}
			else $TPLFailError .= 'New/Modify link group encountered an error preparing to get group details for modification.<br />';
		}
		else {
			$LGRGTPID = 0;
			$LGRName = '';
			$LGRDesc = '';
			$LGRUpdated = 'Never';
		}
		//XCal - Now we've got everything we need to build the New/Modify screen, so do that
		echo("<input id=\"LNK_GRP_ID\" type=\"hidden\" value=\"$TPLLinkID\" />");
		if (isset($ExID))
			echo("<input id=\"EX_GRP_ID\" type=\"hidden\" value=\"$ExID\" />");
		echo(
				'<table>'.
				'<tr><td class="fieldname">Type</td>'.
				'<td><select id="LNK_GRP_GTP_ID">');
		$stm = $sql->prepare(
				'SELECT GTP_ID,GTP_NAME '.
				'FROM grp_group_types '.
				'WHERE GTP_SR = 0 OR GTP_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$LGRGTPID);
			if ($stm->execute()) {
				$stm->bind_result($GTPID,$GTName);
				while ($stm->fetch()) {
					if ($LGRGTPID == 0)
						$LGRGTPID = $GTPID;
					echo(
							'<option');
					
					if ($GTPID == $LGRGTPID)
						echo(' selected');
				
					echo(	" value=\"$GTPID\">$GTName</option>");
				}
			}
			else $TPLFailError .= 'New/Modify link group failed getting available types with error: '.$stm->error.'<br />';
		}
		else $TPLFailError .= 'New/Modify link groups encountered an error preparing to get available types.<br />';

			
		echo(
				'</select></td></tr>'.
				'<tr><td class="fieldname">Name</td>'.
				"<td><input id=\"LNK_GRP_NAME\" type=\"text\" value=\"$LGRName\" /></td></tr>".
				'<tr><td class="fieldname">Description</td>'.
				"<td><textarea id=\"LNK_GRP_DESC\" cols=\"50\" rows=\"5\">$LGRDesc</textarea></td></tr>".
				'<tr><td class="fieldname">Updated</td>'.
				"<td>$LGRUpdated</td></tr>".
				'</table>'.
				'<input class="save" type="button" value="Save" onclick="SaveLinkGroup('."'$TPLTarget','$ONRLTPID','$ONRID','$TPLMode'".');" />'.
				'<input class="cancel" type="button" value="Cancel" onclick="'.
				"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&LID=$TPLLinkID');\" /><br />");		
	}
?>
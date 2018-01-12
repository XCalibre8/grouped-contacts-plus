<?php
	//XCal - This unit is only to be included from type_links or somewhere that does the [initialisation:UK;initialization:US] for it
	// This unit REQUIRES the variables $TPLMode,$ONRLTPID,$ONRID,$CHDLTPID to be set already
	if (isset($_REQUEST['CT']))
		$LADCTTID = $_REQUEST['CT'];
	if (isset($_REQUEST['ACO']))
		$LADCareOf = $_REQUEST['ACO'];
	if (isset($_REQUEST['AL1']))
		$LADLine1 = $_REQUEST['AL1'];
	if (isset($_REQUEST['AL2']))
		$LADLine2 = $_REQUEST['AL2'];
	if (isset($_REQUEST['ATW']))
		$LADTown = $_REQUEST['ATW'];
	if (isset($_REQUEST['ACT']))
		$LADCounty = $_REQUEST['ACT'];
	if (isset($_REQUEST['ACR']))
		$LADCountryID = $_REQUEST['ACR'];
	if (isset($_REQUEST['APC']))
		$LADPostCode = $_REQUEST['APC'];
	if (! isset($TPLLinkID))
		$TPLLinkID = 0;
	
	//XCal - If values have been passed in the request then it's a save action
	if (isset($LADCTTID) or isset($LADCareOf) or isset($LADLine1) or isset($LADLine2)
			or isset($LADTown) or isset($LADCounty) or isset($LADPostCode)) {
		//$_SESSION['DebugStr'] .= 'Link Address Modify: Save Mode<br />';

		$stm = $sql->prepare("SELECT 0,'',$TPLLinkID INTO @Code,@Msg,@AdrID");
		if ($stm) {
			$stm->execute();
			$stm->free_result();
			if ($stm->prepare('CALL sp_con_addedit_link_address(?,?,@AdrID,?,?,?,?,?,?,?,?,@Code,@Msg)')) {
				$stm->bind_param('iiisssssis',$ONRLTPID,$ONRID,$LADCTTID,$LADCareOf,$LADLine1,$LADLine2,$LADTown,$LADCounty,$LADCountryID,$LADPostCode);
				if ($stm->execute()) {
					$stm->free_result();
					if ($stm->prepare('SELECT @Code,@Msg,@AdrID')) {
						$stm->execute();
						$stm->bind_result($RCode,$RMsg,$TPLLinkID);
						$stm->fetch();
						echo("New/Modify result: $RMsg<br />");
						$stm->free_result();
					}
					else $TPLFailError .= 'New/Modify link address encountered an error preparing to get link results.<br />';
				}
				else echo('New/Modify link address failed with error : '.$stm->error.'<br />');
			}
			else $TPLFailError .= 'New/Modify link address encountered an error preparing to add/edit the address.<br />';
		}
		else $TPLFailError .= 'New/Modify link address ecountered an error initialising the add/edit operation.<br />';
		//XCal - Fail or succesd we want the link types screen to go back to list mode
		$TPLMode = 'L';
	}
	else { //XCal - No name passed so we want the input screen
		if ($TPLLinkID > 0) {
			$stm = $sql->prepare(
					'SELECT LNK_X_ID CTypeID,LNK_X_STR CareOf,ADR_LINE1,ADR_LINE2,ADR_POST_TOWN,ADR_COUNTY,ADR_CNTRY_ID,ADR_POSTCODE,ADR_UPDATED '.
					'FROM con_addresses '.
					'JOIN lnk_links ON LNK_ONR_LTP_ID = ? AND LNK_ONR_ID = ? AND LNK_CHD_LTP_ID = 2 AND LNK_CHD_ID = ADR_ID '.
					'WHERE ADR_ID = ?');
			if ($stm) {
				$stm->bind_param('iii',$ONRLTPID,$ONRID,$TPLLinkID);
				$stm->execute();
				$stm->bind_result($LADCTTID,$LADCareOf,$LADLine1,$LADLine2,$LADTown,$LADCounty,$LADCountryID,$LADPostCode,$LADUpdated);
				$stm->fetch();
				$stm->free_result();
			}
			else $TPLFailError .= 'New/Modify link address encountered an error preparing to get address details for modification.<br />';
		}
		else {
			$LADCTTID = 0;
			$LADCareOf = '';
			$LADLine1 = '';
			$LADLine2 = '';
			$LADTown = '';
			$LADCounty = '';
			$LADCountryID = 826;
			$LADPostCode = '';			
			$LADUpdated = 'Never';
		}
		//XCal - Now we've got everything we need to build the New/Modify screen, so do that
		echo("<input id=\"LNK_ADR_ID\" type=\"hidden\" value=\"$TPLLinkID\" />");
		if (isset($ExID))
			echo("<input id=\"EX_ADR_ID\" type=\"hidden\" value=\"$ExID\" />");
		echo(
				'<table>'.
				'<tr><td class="fieldname">Contact Type</td>'.
				'<td><select id="LNK_ADR_CTT_ID">');
		$stm = $sql->prepare(
				'SELECT CTT_ID,CTT_NAME '.
				'FROM con_contact_types '.
				'WHERE CTT_SR = 0 OR CTT_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$LADCTTID);
			if ($stm->execute()) {
				$stm->bind_result($CTTID,$CTName);
				while ($stm->fetch()) {
					if ($LADCTTID == 0)
						$LADCTTID = $CTTID;
					echo(
							'<option');
					
					if ($CTTID == $LADCTTID)
						echo(' selected');
				
					echo(	" value=\"$CTTID\">$CTName</option>");
				}
			}
			else $TPLFailError .= 'New/Modify link address failed getting available contact types with error ('.$stm->error.')<br />';
		}
		else $TPLFailError .= 'New/Modify link address encountered an error preparing to get available contact types.<br />';
		
		echo(
				'</select></td></tr>'.
				'<tr><td class="fieldname">Care Of</td>'.
				"<td><input id=\"LNK_ADR_CARE_OF\" type=\"text\" value=\"$LADCareOf\" /></td></tr>".
				'<tr><td class="fieldname">Line 1</td>'.
				"<td><input id=\"LNK_ADR_LINE1\" type=\"text\" value=\"$LADLine1\" /></td></tr>".
				'<tr><td class="fieldname">Line 2</td>'.
				"<td><input id=\"LNK_ADR_LINE2\" type=\"text\" value=\"$LADLine2\" /></td></tr>".
				'<tr><td class="fieldname">Post Town</td>'.
				"<td><input id=\"LNK_ADR_TOWN\" type=\"text\" value=\"$LADTown\" /></td></tr>".
				'<tr><td class="fieldname">County</td>'.
				"<td><input id=\"LNK_ADR_COUNTY\" type=\"text\" value=\"$LADCounty\" /></td></tr>".
				'<tr><td class="fieldname">Country</td>'.
				'<td><select id="LNK_ADR_CNTRY_ID">');
		
		$stm = $sql->prepare('SELECT CNTRY_ID,CNTRY_NAME FROM con_countries');
		if ($stm) {
			if ($stm->execute()) {
				$stm->bind_result($CtryID,$CtryName);
				while ($stm->fetch()) {
					echo(
							'<option');
					
					if ($CtryID == $LADCountryID)
						echo(' selected');
				
					echo(	" value=\"$CtryID\">$CtryName</option>");
				}
			}
			else $TPLFailError .= 'New/Modify link address failed getting available countries with error ('.$stm->error.')<br />';
		}
		else $TPLFailError .= 'New/Modify link address encountered an error preparing to get available countries.<br />';
				
		echo(	'</select></td></tr>'.
				'<tr><td class="fieldname">Postcode</td>'.
				"<td><input id=\"LNK_ADR_POSTCODE\" type=\"text\" value=\"$LADPostCode\" /></td></tr>".
				'<tr><td class="fieldname">Updated</td>'.
				"<td>$LADUpdated</td></tr>".
				'</table>'.
				'<input class="save" type="button" value="Save" onclick="SaveLinkAddress('."'$TPLTarget','$ONRLTPID','$ONRID','$TPLMode'".');" />'.
				'<input class="cancel" type="button" value="Cancel" onclick="'.
				"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&LID=$TPLLinkID');\" /><br />");		
	}
?>
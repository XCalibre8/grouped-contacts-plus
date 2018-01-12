<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'AED')) {
	$For = 'AED';
	if (isset($_REQUEST['ID']))
		$AEDADR_ID = $_REQUEST['ID'];
	else $AEDADR_ID = 0;
	if (isset($_REQUEST['OLT']))
		$ONRLTPID = $_REQUEST['OLT'];
	if (isset($_REQUEST['OID']))
		$ONRID = $REQUEST['OID'];		
		
	if (isset($_REQUEST['T']))
		$AEDTarget = $_REQUEST['T'];
	elseif ((! isset($AEDTarget)) and isset($Target))
		$AEDTarget = $Target;
	if (isset($_REQUEST['M'])) //[XC:M|Mode:N,M,V,C,EM|New,Modify,View,Card(NE|NeverEdit),Embedded Modify(No Save, caller responsible);]
		$AEDMode = $_REQUEST['M'];
	elseif (! isset($AEDMode))
		$AEDMode = 'V';
		
	if (isset($_REQUEST['CTT']))
		$AEDCTTID = $_REQUEST['CTT'];
	if (isset($_REQUEST['CareOf']))
		$AEDCareOf = $_REQUEST['CareOf'];
	if (isset($_REQUEST['Line1']))
		$AEDLine1 = $_REQUEST['Line1'];
	if (isset($_REQUEST['Line2']))
		$AEDLine2 = $_REQUEST['Line2'];
	if (isset($_REQUEST['Town']))
		$AEDTown = $_REQUEST['Town'];
	if (isset($_REQUEST['County']))
		$AEDCounty = $_REQUEST['County'];
	if (isset($_REQUEST['CntryID']))
		$AEDCntryID = $_REQUEST['CntryID'];
	if (isset($_REQUEST['PCode']))
		$AEDPostcode = $_REQUEST['PCode'];
}
elseif (isset($For) and ($For == 'AED')) {
	if (isset($ID))
		$AEDADR_ID = $ID;
	elseif (! isset($AEDADR_ID))
		$AEDADR_ID = 0;
	if (isset($Target))
		$AEDTarget = $Target;
	if (isset($Mode))
		$AEDMode = $Mode;
	elseif (! isset($AEDMode))
		$AEDMode = 'V';
}
	$Linked = (isset($ONRLTPID) and (isset($ONRID)));
	$AEDPath = 'resources/contacts/address_embed.php';
	$AEDDieError = '';
	$AEDFailError = '';
	$AEDModes = array('N','M','V','C','EM');
	
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql, 1);
	
		if ($AEDMode == 'C') {
			//$_SESSION['DebugStr'] .= 'Embedded Address: Card View.<br />';
			//XCal - TODO build a 'card' view of an address, preferably with functions and params for re-use
		}
		else {
			//$_SESSION['DebugStr'] .= 'Embedded Address: Mode '.$AEDMode.'.<br />';
			if (in_array($AEDMode,array('N','M','EM'))) {
				if ($CONSec['NewMod'] == 2)
					$CanEdit = true;
				else $CanEdit = ItemLinked($sql, 2, $AEDADR_ID);
				if ($CanEdit) {
					//XCal - Now we know it's a new or modify request
					if (isset($AEDCTTID)) {
						//XCal - Now we know they've already filled in the details
						if ($Linked) {
							//XCal - If it's linked we can use the stored proc for new/mod
							$stm = $sql->prepare('SET @AdrID = ?, @RCode = 0, @RMsg = \'\'');
							if ($stm) {
								$stm->bind_param('i',$AEDADR_ID);
								if ($stm->execute()) {
									$stm->free_result();
									if ($stm->prepare('CALL sp_con_addedit_link_address('.
											'?,?,@AdrID,?,?,?,?,?,?,?,?,@RCode,@RMsg)')) {
											$stm->bind_param('iiisssssis',$ONRLTPID,$ONRID,$AEDCTTID,
													$AEDCareOf,$AEDLine1,$AEDLine2,$AEDTown,$AEDCounty,
													$AEDCntryID,$AEDPostcode);
											if ($stm->execute()) {
												$stm->free_result();
												if ($stm->prepare('SELECT @AdrID,@RCode,@RMsg')) {
													if ($stm->execute()) {
														$stm->bind_result($AEDADR_ID,$RCode,$RMsg);
														$stm->fetch();
														$stm->free_result();
														echo('New/Modify result: '.$RMsg);
														$AEDMode = 'V';
													}
													else $AEDFailError .= 'Embedded Address failed fetching the new/modify result.<br />';
												}
												else $AEDFailError .= 'Embedded Address failed preparing to fetch the new/modify result.<br />';
											}
											else $AEDFailError .= 'Embedded Address failed performing the new/modify request.<br />';
									}
									else $AEDFailError .= 'Embedded Address failed preparing the new/modify request.<br />';
								}
								else $AEDFailError .= 'Embedded Address failed initialising the linked address new/modify request.<br />';
							}
							else $AEDFailError .= 'Embedded Address failed preparing to initialise the linked address new/modify request.<br />';
						}
						else {
							if ($AEDMode == 'N') {
								//$_SESSION['DebugStr'] .= 'Embedded Address: Save New<br />';
								//XCal - Now we know they want to insert a record without a link
								//XCal - Now we know they don't want/need a link, so we can just insert the address
								$stm = $sql->prepare(
									'INSERT INTO con_addresses ('.
									'ADR_LINE1,ADR_LINE2,ADR_POST_TOWN,ADR_COUNTY,ADR_CNTRY_ID,'.
									'ADR_POSTCODE,ADR_UPDATED) '.
									'VALUES (?,?,?,?,?,?,current_timestamp())');
								if ($stm) {
									$stm->bind_param('ssssis',$AEDLine1,$AEDLine2,$AEDTown,$AEDCounty,
										$AEDCntryID,$AEDPostcode);
									if ($stm->execute()) {
										$stm->free_result();
										$stm = $sql->prepare('SELECT LAST_INSERT_ID()');
										if ($stm) {
											if ($stm->execute()) {
												$stm->bind_result($AEDADR_ID);
												$stm->fetch();
												$stm->free_result();
											}
											else $AEDFailError .= 'Embedded Address failed to get the inserted address identifier.<br />';
										}
										else $AEDFailError .= 'Embedded Address failed preparing to get the inserted address identifier.<br />';
									}
									else $AEDFailError .= 'Embedded Address failed to execute the insert.<br />';
									$AEDMode = 'V';
								}
								else $AEDFailError .= 'Embedded Address failed to prepare the insert.<br />';
							}
							else {
								//$_SESSION['DebugStr'] .= 'Embedded Address: Save Modifications<br />';
								//XCal - Now we know they want to modify a record without a link
								$stm = $sql->prepare(
										'UPDATE con_addresses SET '.
										'ADR_LINE1 = ?,ADR_LINE2 = ?,ADR_POST_TOWN = ?,'.
										'ADR_COUNTY = ?,ADR_CNTRY_ID = ?,ADR_POSTCODE = ?,ADR_UPDATED = current_timestamp() '.
										'WHERE ADR_ID = ?');
								if ($stm) {
									$stm->bind_param('ssssisi',$AEDLine1,$AEDLine2,$AEDTown,$AEDCounty,$AEDCntryID,$AEDPostcode,$AEDADR_ID);
									if ($stm->execute())
										$stm->free_result();
									else $AEDFailError .= 'Embedded Address failed to modify the address.<br />';
									$AEDMode = 'V';
								}
								else $AEDFailError .= 'Embedded Address failed preparing to modify the address.<br />';
							}
						}
						
					}
					else {
						//XCal - Now we know they haven't done the new/modify form yet
						if (in_array($AEDMode,array('M','EM')) and ($AEDADR_ID > 0)) {
							//$_SESSION['DebugStr'] .= 'Embedded Address: Modify Existing<br />';
							$Query = 'SELECT ADR_LINE1,ADR_LINE2,ADR_POST_TOWN,ADR_COUNTY,ADR_CNTRY_ID,ADR_POSTCODE,ADR_UPDATED';
							if ($Linked) 
								$Query .= ',LNK_X_ID,LNK_X_STR';
							$Query .= ' FROM con_addresses ';
							if ($Linked)
								$Query .= 'JOIN lnk_links ON LNK_ONR_LTP_ID = ? AND LNK_ONR_ID = ? '.
									'AND LNK_CHD_LTP_ID = 2 AND LNK_CHD_ID = ADR_ID ';
							$Query .= 'WHERE ADR_ID = ?';
							$stm = $sql->prepare($Query);
							if ($stm) {
								if ($Linked)
									$stm->bind_param('iii',$ONRLTPID,$ONRID,$AEDADR_ID);
								else $stm->bind_param('i',$AEDADR_ID);
								if ($stm->execute()) {
									if ($Linked)
										$stm->bind_result($AEDLine1,$AEDLine2,$AEDTown,$AEDCounty,
											$AEDCntryID,$AEDPostcode,$AEDUpdated,$AEDCTTID,$AEDCareOf);
									else 
										$stm->bind_result($AEDLine1,$AEDLine2,$AEDTown,$AEDCounty,
											$AEDCntryID,$AEDPostcode,$AEDUpdated);
									$stm->fetch();
									$stm->free_result();
								}
								else $AEDFailError .= 'Embedded address failed to fetch address details to modify.<br />';
							}
							else $AEDFailError .= 'Embedded address failed preparing to fetch address details to modify.<br />';
							
						}
						else {
							//$_SESSION['DebugStr'] .= 'Embedded Address: New Record<br />';
							//XCal - Now we know it's a new record or the address ID has failed so we need to initialise blanks
							$AEDLine1 = '';
							$AEDLine2 = '';
							$AEDTown = '';
							$AEDCounty = '';
							$AEDCntryID = 826; 
							$AEDPostcode = '';
							$AEDUpdated = 'Never';
							unset($AEDCTTID);
							$AEDCareOf = '';
						}
		
						//XCal - If we implement attachment like "AddWAC" on person_embed we need to set it into the extra params string here
						if (! $AEDMode == 'EM')
							echo('<form action="javascript:;" onsubmit="'.
								"SaveAddressEmbedded('$AEDTarget','$AEDMode','OLT=$ONRLTPID&OID=$ONRID');".
								'">');
						echo('<input type="hidden" id="AED_ADR_ID" value="'.$AEDADR_ID.'" />'.
							'<table><tr><td class="fieldname">Contact Type</td>'.
							'<td><select id="AED_CTT_ID">');
						if (isset($AEDCTTID))
							$ExQ = ' OR CTT_ID = ?';
						else $ExQ = '';
						$r_contypes = $sql->prepare(
								'SELECT CTT_ID,CTT_NAME '.
								'FROM con_contact_types '.
								'WHERE CTT_SR = 0'.$ExQ);
						if ($r_contypes) {
							if (isset($AEDCTTID))
								$r_contypes->bind_param('i',$AEDCTTID);
							if ($r_contypes->execute()) {
								$r_contypes->bind_result($CTTID,$ConType);
								while ($r_contypes->fetch()) {
									if (!isset($AEDCTTID))
										$AEDCTTID = $CTTID;
									echo('<option value="'.$CTTID.'"');
									if ($CTTID == $AEDCTTID)
										echo(' selected');
									echo('>'.$ConType.'</option>');							
								}
								$r_contypes->free_result();
							}
							else $AEDFailError .= 'Embedded Address failed to get a list of contact types.<br />';
						}
						else $AEDFailError .= 'Embedded Address failed preparing to get a list of contact types.<br />';
						echo('</select></td></tr>'.
							'<tr><td class="fieldname">Care Of</td>'.
							'<td><input type="text" id="AED_CARE_OF" value="'.$AEDCareOf.'" /></td></tr>'.
							'<tr><td class="fieldname">Line 1</td>'.
							'<td><input type="text" id="AED_LINE1" value="'.$AEDLine1.'" /></td></tr>'.
							'<tr><td class="fieldname">Line 2</td>'.
							'<td><input type="text" id="AED_LINE2" value="'.$AEDLine2.'" /></td></tr>'.
							'<tr><td class="fieldname">Town</td>'.
							'<td><input type="text" id="AED_TOWN" value="'.$AEDTown.'" /></td></tr>'.
							'<tr><td class="fieldname">County</td>'.
							'<td><input type="text" id="AED_COUNTY" value="'.$AEDCounty.'" /></td></tr>'.
							'<tr><td class="fieldname">Country</td>'.
							'<td><select id="AED_CNTRY_ID">');
						$r_countries = $sql->prepare('SELECT CNTRY_ID,CNTRY_NAME FROM con_countries');
						if ($r_countries) {
							if ($r_countries->execute()) {
								$r_countries->bind_result($CntryID,$Country);
								while ($r_countries->fetch()) {
									echo('<option value="'.$CntryID.'"');
									if ($CntryID == $AEDCntryID)
										echo(' selected');
									echo(">$Country</option>");
								}
								$r_countries->free_result();
							}
							else $AEDFailError .= 'Embedded Address failed to get a list of countries.<br />';
						}
						else $AEDFailError .= 'Embedded Address failed preparing to get a list of countries.<br />';
						echo('</select></td></tr>'.
							'<tr><td class="fieldname">Postcode</td>'.
							'<td><input type="text" id="AED_POSTCODE" value="'.$AEDPostcode.'" /></td></tr>'.
							'</table><br />'.
							"Address Updated: $AEDUpdated<br />");
						if (! $AEDMode == 'EM')
							echo('<input type="submit" class="button" value="Save" />'.
								'</form>');
					}
				}
				elseif ($CONSec['NewMod'] == 1)
					echo('You only have the right to modify contacts your account is linked by, which does not include this address.');
				else echo('You do not have the required access rights to modify contacts.');
			}
			if ($AEDMode == 'V') {
				if ($CONSec['SearchView'] == 2)
					$CanView = true;
				else $CanView = ItemLinked($sql,2,$AEDADR_ID);			
				if ($CanView) {
					if ($AEDADR_ID == 0) {
						//$_SESSION['DebugStr'] .= 'Embedded Address: View Nothing.<br />';
						//XCal - Now we know we're being called to operate on a potential address record.
						echo('No Address Defined.');
						
						//XCal - If we end up needing to be able to create and attach we'll need the below code
		// 				echo(' <input type="button" class="button" value="Create" onclick="'.
		// 					"AJAX('$AEDTarget','$AEDPath','For=AED&T=$AEDTarget&M=N&OLT=$ONRLTPID$OID=$ONRID');".
		// 					'" />');
					}
					else {
						//$_SESSION['DebugStr'] .= 'Embedded Address: View an Address.<br />';
						//XCal - Now we know we're at least looking for an address record to exist
						if ($Linked)
							$stm = $sql->prepare('SELECT fn_con_sep_link_address(?,?,?,\'<br />\')');
						else $stm = $sql->prepare('SELECT fn_con_sep_address(?,\'<br />\')');
						if ($stm) {
							if ($Linked)
								$stm->bind_param('iii',$ONRLTPID,$ONRID,$AEDADR_ID);
							else $stm->bind_param('i',$AEDADR_ID);
							if ($stm->execute()) {
								$stm->bind_result($AEDBlockAddress);
								if ($stm->fetch()) {
									echo($AEDBlockAddress.
										'<br /><input type="button" class="button" value="Modify" onclick="'.
										"AJAX('$AEDTarget','$AEDPath','For=AED&T=$AEDTarget&M=M&ID=$AEDADR_ID&OLT=$ONRLTPID&OID=$ONRID');".
										'" />');
								}
								$stm->free_result();							
							}
							else $AEDFailError .= 'Embedded Address failed getting the address details.<br />';
						}
						else $AEDFailError .= 'Embedded Address failed preparing to get the address details.<br />';
					}
				}
				elseif ($CONSec['SearchView'] == 1)
					echo('You only have the right to view contacts your account is linked by, which does not include this address.');
				else echo('You do not have the required access rights to view contacts.');
			}
		}
	}
	else echo('You must be logged in to view this data.');
	if (strlen($AEDFailError) > 0)
		echo("Sorry to trouble you, but you may want to know about the following issues.<br />$AEDFailError");
?>
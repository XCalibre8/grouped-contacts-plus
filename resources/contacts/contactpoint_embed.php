<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'CED')) {
	$For = 'CED';
	if (isset($_REQUEST['ID']))
		$CEDCNP_ID = $_REQUEST['ID'];
	else $CEDCNP_ID = 0;
	if (isset($_REQUEST['OLT']))
		$ONRLTPID = $_REQUEST['OLT'];
	if (isset($_REQUEST['OID']))
		$ONRID = $REQUEST['OID'];		
		
	if (isset($_REQUEST['T']))
		$CEDTarget = $_REQUEST['T'];
	elseif ((! isset($CEDTarget)) and isset($Target))
		$CEDTarget = $Target;
	if (isset($_REQUEST['M'])) //[XC:M|Mode:N,M,V,C,EM|New,Modify,View,Card(NE|NeverEdit),Embedded Modify(No Save, caller responsible);]
		$CEDMode = $_REQUEST['M'];
	elseif (! isset($CEDMode))
		$CEDMode = 'V';
		
	if (isset($_REQUEST['CTT']))
		$CEDCTTID = $_REQUEST['CTT'];
	if (isset($_REQUEST['CPT']))
		$CEDCPTID = $_REQUEST['CPT'];
	if (isset($_REQUEST['ST']))
		$CEDSpeakTo = $_REQUEST['ST'];
	if (isset($_REQUEST['Con']))
		$CEDContact = $_REQUEST['Con'];
}
elseif (isset($For) and ($For == 'CED')) {
	if (isset($ID))
		$CEDCNP_ID = $ID;
	elseif (! isset($CEDCNP_ID))
		$CEDCNP_ID = 0;
	if (isset($Target))
		$CEDTarget = $Target;
	if (isset($Mode))
		$CEDMode = $Mode;
	elseif (! isset($CEDMode))
		$CEDMode = 'V';
}
$Linked = (isset($ONRLTPID) and (isset($ONRID)));
$CEDPath = 'resources/contacts/contactpoint_embed.php';
$CEDDieError = '';
$CEDFailError = '';
$CEDModes = array('N','M','V','C','EM');

if (! function_exists('CheckToken'))
	require(dirname(__FILE__).'/../web/web_security.php');
CheckToken($sql, $TokenValid);
if ($TokenValid) {
	$CONSec = GetAreaSecurity($sql, 1);
	if ($CEDMode == 'C') {
		//$_SESSION['DebugStr'] .= 'Embedded Contactpoint: Card View.<br />';
		//XCal - TODO build a 'card' view of a contact point, preferably with functions and params for re-use
	}
	else {
		//$_SESSION['DebugStr'] .= 'Embedded Contactpoint: Mode '.$CEDMode.'.<br />';
		if (in_array($CEDMode,array('N','M','EM'))) {
			if ($CONSec['NewMod'] == 2)
				$CanEdit = true;
			else $CanEdit = ItemLinked($sql, 3, $CEDCNP_ID);
			if ($CanEdit) {
				//XCal - Now we know it's a new or modify request
				if (isset($CEDCTTID)) {
					//XCal - Now we know they've already filled in the details
					if ($Linked) {
						//XCal - If it's linked we can use the stored proc for new/mod
						$stm = $sql->prepare('SET @CnpID = ?, @RCode = 0, @RMsg = \'\'');
						if ($stm) {
							$stm->bind_param('i',$CEDCNP_ID);
							if ($stm->execute()) {
								$stm->free_result();
								if ($stm->prepare('CALL sp_con_addedit_link_contact_point('.
										'?,?,@CnpID,?,?,?,?,@RCode,@RMsg)')) {
										$stm->bind_param('iiisis',$ONRLTPID,$ONRID,$CEDCTTID,
												$CEDSpeakTo,$CEDCPTID,$CEDContact);
										if ($stm->execute()) {
											$stm->free_result();
											if ($stm->prepare('SELECT @CnpID,@RCode,@RMsg')) {
												if ($stm->execute()) {
													$stm->bind_result($CEDCNP_ID,$RCode,$RMsg);
													$stm->fetch();
													$stm->free_result();
													echo('New/Modify result: '.$RMsg);
													$CEDMode = 'V';
												}
												else $CEDFailError .= 'Embedded Contactpoint failed fetching the new/modify result.<br />';
											}
											else $CEDFailError .= 'Embedded Contactpoint failed preparing to fetch the new/modify result.<br />';
										}
										else $CEDFailError .= 'Embedded Contactpoint failed performing the new/modify request.<br />';
								}
								else $CEDFailError .= 'Embedded Contactpoint failed preparing the new/modify request.<br />';
							}
							else $CEDFailError .= 'Embedded Contactpoint failed initialising the linked contactpoint new/modify request.<br />';
						}
						else $CEDFailError .= 'Embedded Contactpoint failed preparing to initialise the linked contactpoint new/modify request.<br />';
					}
					else {
						if ($CEDMode == 'N') {
							//$_SESSION['DebugStr'] .= 'Embedded Contactpoint: Save New<br />';
							//XCal - Now we know they want to insert a record without a link
							//XCal - Now we know they don't want/need a link, so we can just insert the address
							$stm = $sql->prepare(
								'INSERT INTO con_contact_points ('.
								'CNP_CPT_ID,CNP_CONTACT,CNP_UPDATED) '.
								'VALUES (?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('is',$CEDCPTID,$CEDContact);
								if ($stm->execute()) {
									$stm->free_result();
									$stm = $sql->prepare('SELECT LAST_INSERT_ID()');
									if ($stm) {
										if ($stm->execute()) {
											$stm->bind_result($CEDCNP_ID);
											$stm->fetch();
											$stm->free_result();
										}
										else $CEDFailError .= 'Embedded Contactpoint failed to get the inserted contactpoint identifier.<br />';
									}
									else $CEDFailError .= 'Embedded Contactpoint failed preparing to get the inserted contactpoint identifier.<br />';
								}
								else $CEDFailError .= 'Embedded Contactpoint failed to execute the insert.<br />';
								$CEDMode = 'V';
							}
							else $CEDFailError .= 'Embedded Contactpoint failed to prepare the insert.<br />';
						}
						else {
							//$_SESSION['DebugStr'] .= 'Embedded Address: Save Modifications<br />';
							//XCal - Now we know they want to modify a record without a link
							$stm = $sql->prepare(
									'UPDATE con_contact_points SET '.
									'CNP_CPT_ID = ?,CNP_CONTACT = ?,CNP_UPDATED = current_timestamp() '.
									'WHERE CNP_ID = ?');
							if ($stm) {
								$stm->bind_param('isi',$CEDCPTID,$CEDContact,$CEDCNP_ID);
								if ($stm->execute())
									$stm->free_result();
								else $CEDFailError .= 'Embedded Contactpoint failed to modify the contactpoint.<br />';
								$CEDMode = 'V';
							}
							else $CEDFailError .= 'Embedded Contactpoint failed preparing to modify the contactpoint.<br />';
						}
					}
					
				}
				else {
					//XCal - Now we know they haven't done the new/modify form yet
					if (in_array($CEDMode,array('M','EM')) and ($CEDCNP_ID > 0)) {
						//$_SESSION['DebugStr'] .= 'Embedded Contactpoint: Modify Existing<br />';
						$Query = 'SELECT CNP_CPT_ID,CNP_CONTACT,CNP_UPDATED';
						if ($Linked) 
							$Query .= ',LNK_X_ID,LNK_X_STR';
						$Query .= ' FROM con_contact_points ';
						if ($Linked)
							$Query .= 'JOIN lnk_links ON LNK_ONR_LTP_ID = ? AND LNK_ONR_ID = ? '.
								'AND LNK_CHD_LTP_ID = 3 AND LNK_CHD_ID = CNP_ID ';
						$Query .= 'WHERE CNP_ID = ?';
						$stm = $sql->prepare($Query);
						if ($stm) {
							if ($Linked)
								$stm->bind_param('iii',$ONRLTPID,$ONRID,$CEDCNP_ID);
							else $stm->bind_param('i',$CEDCNP_ID);
							if ($stm->execute()) {
								if ($Linked)
									$stm->bind_result($CEDCPTID,$CEDContact,$CEDUpdated,
										$CEDCTTID,$CEDSpeakTo);
								else 
									$stm->bind_result($CEDCPTID,$CEDContact,$CEDUpdated);
								$stm->fetch();
								$stm->free_result();
							}
							else $CEDFailError .= 'Embedded Contactpoint failed to fetch contactpoint details to modify.<br />';
						}
						else $CEDFailError .= 'Embedded Contactpoint failed preparing to fetch contactpoint details to modify.<br />';
						
					}
					else {
						$_SESSION['DebugStr'] .= 'Embedded Contactpoint: New Record<br />';
						//XCal - Now we know it's a new record or the address ID has failed so we need to initialise blanks
						unset($CEDCPTID);
						$CEDContact = '';
						$CEDUpdated = 'Never';
						unset($CEDCTTID);
						$CEDSpeakTo = '';
					}
	
					//XCal - If we implement attachment like "AddWAC" on person_embed we need to set it into the extra params string here
					if (! $CEDMode == 'EM')
						echo('<form action="javascript:;" onsubmit="'.
							"SaveContactPointEmbedded('$CEDTarget','$CEDMode','OLT=$ONRLTPID&OID=$ONRID');".
							'">');
					echo('<input type="hidden" id="CED_CNP_ID" value="'.$CEDCNP_ID.'" />'.
						'<table><tr><td class="fieldname">Contact Type</td>'.
						'<td><select id="CED_CTT_ID">');
					if (isset($CEDCTTID))
						$ExQ = ' OR CTT_ID = ?';
					else $ExQ = '';
					$r_contypes = $sql->prepare(
							'SELECT CTT_ID,CTT_NAME '.
							'FROM con_contact_types '.
							'WHERE CTT_SR = 0'.$ExQ);
					if ($r_contypes) {
						if (isset($CEDCTTID))
							$r_contypes->bind_param('i',$CEDCTTID);
						if ($r_contypes->execute()) {
							$r_contypes->bind_result($CTTID,$ConType);
							while ($r_contypes->fetch()) {
								if (!isset($CEDCTTID))
									$CEDCTTID = $CTTID;
								echo('<option value="'.$CTTID.'"');
								if ($CTTID == $CEDCTTID)
									echo(' selected');
								echo(">$ConType</option>");
							}
							$r_contypes->free_result();
						}
						else $CEDFailError .= 'Embedded Contactpoint failed to get a list of contact types.<br />';
					}
					else $CEDFailError .= 'Embedded Contactpoint failed preparing to get a list of contact types.<br />';
					unset($r_contypes);
					echo('</select></td></tr>'.
						'<tr><td class="fieldname">Speak To</td>'.
						'<td><input type="text" id="CED_SPEAK_TO" value="'.$CEDSpeakTo.'" /></td></tr>'.
						'<tr><td class="fieldname">Contact Method</td>'.
						'<td><select id="CED_CPT_ID">');
					if (isset($CEDCPTID))
						$ExQ = ' OR CPT_ID = ?';
					else $ExQ = '';
					$r_conmethods = $sql->prepare(
							'SELECT CPT_ID,CPT_NAME '.
							'FROM con_contact_point_types '.
							'WHERE CPT_SR = 0'.$ExQ);
					if ($r_conmethods) {
						if (isset($CEDCPTID))
							$r_conmethods->bind_param('i',$CEDCPTTID);
						if ($r_conmethods->execute()) {
							$r_conmethods->bind_result($CPTID,$ConMethod);
							while ($r_conmethods->fetch()) {
								if (!isset($CEDCPTID))
									$CEDCPTID = $CPTID;
								echo('<option value="'.$CPTID.'"');
								if ($CPTID == $CEDCPTID)
									echo(' selected');
								echo(">$ConMethod</option>");
							}
							$r_conmethods->free_result();
						}
						else $CEDFailError .= 'Embedded Contactpoints failed to get a list of contact methods.<br />';
					}
					else $CEDFailError .= 'Embedded Contactpoint failed preparing to get a list of contact methods.<br />';
					unset($r_conmethods);
					echo('</select></td></tr>'.
						'<tr><td class="fieldname">Contact Point</td>'.
						'<td><input type="text" id="CED_CONTACT" value="'.$CEDContact.'" /></td></tr>'.
						'</table><br />'.
						"Contactpoint Updated: $AEDUpdated<br />");
					if (! $CEDMode == 'EM')
						echo('<input type="submit" class="button" value="Save" />'.
							'</form>');
				}
			}
			elseif ($CONSec['NewMod'] == 1)
				echo('You only have the right to modify contacts your account is linked by, which does not include this contact point.');
			else echo('You do not have the required access rights to modify contacts.');
		}
		if ($CEDMode == 'V') {
			if ($CONSec['SearchView'] == 2)
				$CanView = true;
			else $CanView = ItemLinked($sql,3,$CEDCNP_ID);
			if ($CanView) {
				if ($CEDCNP_ID == 0) {
					$_SESSION['DebugStr'] .= 'Embedded Contactpoint: View Nothing.<br />';
					//XCal - Now we know we're being called to operate on a potential address record.
					echo('No Contactpoint Defined.');
					
					//XCal - If we end up needing to be able to create and attach we'll need the below code
	// 				echo(' <input type="button" class="button" value="Create" onclick="'.
	// 					"AJAX('$CEDTarget','$CEDPath','For=AED&T=$CEDTarget&M=N&OLT=$ONRLTPID$OID=$ONRID');".
	// 					'" />');
				}
				else {
					$_SESSION['DebugStr'] .= 'Embedded Contactpoint: View a contactpoint.<br />';
					//XCal - Now we know we're at least looking for a contact point record to exist
					if ($Linked)
						$stm = $sql->prepare('SELECT fn_con_full_link_contact_point(?,?,?)');
					else $stm = $sql->prepare('SELECT fn_con_full_contact_point(?)');
					if ($stm) {
						if ($Linked)
							$stm->bind_param('iii',$ONRLTPID,$ONRID,$CEDCNP_ID);
						else $stm->bind_param('i',$CEDCNP_ID);
						if ($stm->execute()) {
							$stm->bind_result($CEDConText);
							if ($stm->fetch()) {
								echo($CEDConText.
									'<br /><input type="button" class="button" value="Modify" onclick="'.
									"AJAX('$CEDTarget','$CEDPath','For=CED&T=$CEDTarget&M=M&ID=$CEDCNP_ID&OLT=$ONRLTPID&OID=$ONRID');".
									'" />');
							}
							$stm->free_result();							
						}
						else $CEDFailError .= 'Embedded Contactpoint failed getting the contactpoint details.<br />';
					}
					else $CEDFailError .= 'Embedded Contactpoint failed preparing to get the contactpoint details.<br />';
				}
			}
			elseif ($CONSec['SearchView'] == 1)
				echo('You only have the right to view contacts your account is linked by, which does not include this contact point.');
			else echo('You do not have the required access rights to view contacts.');
		}
	}
	if (strlen($CEDFailError) > 0)
		echo("Sorry to trouble you, but you may want to know about the following issues.<br />$CEDFailError");
}
else echo('You must be logged in to view this data.');
?>
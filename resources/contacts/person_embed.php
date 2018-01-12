<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PED')) {
	$For = 'PED';
	if (isset($_REQUEST['ID']))
		$PEDPER_ID = $_REQUEST['ID'];
	else $PEDPER_ID = 0;
		
	if (isset($_REQUEST['T']))
		$PEDTarget = $_REQUEST['T'];
	elseif ((! isset($PEDTarget)) and isset($Target))
		$PEDTarget = $Target;
	if (isset($_REQUEST['M'])) //[XC:M|Mode:N,M,V,C|New,Modify,View,Card(NE|NeverEdit);]
		$PEDMode = $_REQUEST['M'];
	elseif (! isset($PEDMode))
		$PEDMode = 'V';
		
	if (isset($_REQUEST['TIT']))
		$PEDTitID = $_REQUEST['TIT'];
	if (isset($_REQUEST['FNames']))
		$PEDForenames = $_REQUEST['FNames'];
	if (isset($_REQUEST['SName']))
		$PEDSurname = $_REQUEST['SName'];
	if (isset($_REQUEST['DOB']))
		$PEDBirthday = $_REQUEST['DOB'];
	
	//XCal - These below operate together, first set or pass $PEDExtraP
	//This will enable the New Person button which will pass the param string in ExtraP
	//This should be "AddWAC=X" where X=WAC_ID, which means when the person is
	//inserted the ID will be retrieved and attached to PER_ID on web_accounts
	//AddXXX additional request handlers for other areas with an embedded PER_ID
	if (isset($_REQUEST['AddWAC'])) {
		$PEDAddWAC = $_REQUEST['AddWAC'];
		$PEDExtraP = "AddWAC=$PEDAddWAC";
	}
	elseif (isset($_REQUEST['ExtraP']))
		$PEDExtraP = $_REQUEST['ExtraP'];
}
elseif (isset($For) and ($For == 'PED')) {
	if (isset($ID))
		$PEDPER_ID = $ID;
	elseif (! isset($PEDPER_ID))
		$PEDPER_ID = 0;
	if (isset($Target))
		$PEDTarget = $Target;
	if (isset($Mode))
		$PEDMode = $Mode;
	elseif (! isset($PEDMode))
		$PEDMode = 'V';
	if (isset($ExtraP))
		$PEDExtraP = $ExtraP;
}
	
$PEDPath = 'resources/contacts/person_embed.php';
$PEDDieError = '';
$PEDFailError = '';	

if (! function_exists('CheckToken'))
	require(dirname(__FILE__).'/../web/web_security.php');
CheckToken($sql, $TokenValid);
if ($TokenValid) {
	$CONSec = GetAreaSecurity($sql, 1);
	if ($PEDMode == 'C') {		
		//$_SESSION['DebugStr'] .= 'Embedded Person: Card View.<br />';
		//XCal - TODO build a 'card' view of a person, preferably with functions and params for re-use
	}
	else {
		//$_SESSION['DebugStr'] .= 'Embedded Person: Mode '.$PEDMode.'.<br />';		
		if (in_array($PEDMode,array('N','M'))) {
			if ($CONSec['NewMod'] == 2)
				$CanEdit = true;
			else $CanEdit = ItemLinked($sql, 1, $PERPER_ID);
			if ($CanEdit) {
				//XCal - Now we know it's a new or modify request
				if (isset($PEDSurname) or isset($PEDForenames)) {
					//XCal - Now we know they've already filled in the details
					if ($PEDMode == 'N') {
						//$_SESSION['DebugStr'] .= 'Embedded Person: Save New<br />';
						//XCal - Now we know they want to insert a record
						$stm = $sql->prepare(
							'INSERT INTO con_people (PER_TIT_ID,PER_FORENAMES,PER_SURNAME,PER_DOB,PER_UPDATED) '.
							'VALUES (?,?,?,?,current_timestamp())');
						if ($stm) {
							$stm->bind_param('isss',$PEDTitID,$PEDForenames,$PEDSurname,$PEDBirthday);
							if ($stm->execute()) {
								$stm->free_result();
								$stm = $sql->prepare('SELECT LAST_INSERT_ID()');
								if ($stm) {
									if ($stm->execute()) {
										$stm->bind_result($PEDPER_ID);
										$stm->fetch();
										$stm->free_result();
									}
									else $PEDFailError .= 'Embedded Person failed to get the inserted person identifier.<br />';
								}
								else $PEDFailError .= 'Embedded Person failed preparing to get the inserted person identifier.<br />';
								
								if (isset($PEDAddWAC) and isset($_SESSION['WebAccID']) and ($PEDAddWAC == $_SESSION['WebAccID'])) {
									//$_SESSION['DebugStr'] .= 'Embedded Person: Attempting to attach person to web account.<br />';
									//XCal - Now we know they want to add the person to their own web account
									if ($PEDPER_ID > 0) {
										$stm = $sql->prepare(
											'UPDATE web_accounts SET '.
											'WAC_PER_ID = ?,'.
											'WAC_UPDATED = current_timestamp() '.
											'WHERE WAC_ID = ?');
										if ($stm) {
											$stm->bind_param('ii',$PEDPER_ID,$PEDAddWAC);
											if ($stm->execute()) {
												$stm->free_result();
												$_SESSION['WACPERID'] = $PEDPER_ID;
											}
											else $PEDFailError .= 'Embedded Person failed attaching the inserted person to the web account ('.$stm->error.').<br />';
										}
										else $PEDFailError .= 'Embedded Person failed preparing to attach the inserted person to the web account ('.$sql->error.').<br />';
									}
								}
								//else $_SESSION['DebugStr'] .= 'Embedded Person: No compliant attach ID found.<br />';
							}
							else $PEDFailError .= 'Embedded Person failed to execute the insert.<br />';
							$PEDMode = 'V';
						}
						else $PEDFailError .= 'Embedded Person failed to prepare the insert.<br />';
					}
					else {
						//$_SESSION['DebugStr'] .= 'Embedded Person: Save Modifications<br />';
						//XCal - Now we know they want to modify a record
						$stm = $sql->prepare(
							'UPDATE con_people SET '.
							'PER_TIT_ID = ?,PER_FORENAMES = ?,PER_SURNAME = ?,'.
							'PER_DOB = ?,PER_UPDATED = current_timestamp() '.
							'WHERE PER_ID = ?');
						if ($stm) {
							$stm->bind_param('isssi',$PEDTitID,$PEDForenames,$PEDSurname,$PEDBirthday,$PEDPER_ID);
							if ($stm->execute())
								$stm->free_result();
							$PEDMode = 'V';
						}
					}
				}
				else {
					//XCal - Now we know they haven't done the new/modify form yet
					if (($PEDMode == 'M') and ($PEDPER_ID > 0)) {
						//$_SESSION['DebugStr'] .= 'Embedded Person: Modify Existing<br />';
						$stm = $sql->prepare(
							'SELECT PER_TIT_ID,PER_FORENAMES,PER_SURNAME,PER_DOB '.
							'FROM con_people '.
							'WHERE PER_ID = ?');
						if ($stm) {
							$stm->bind_param('i',$PEDPER_ID);
							if ($stm->execute()) {
								$stm->bind_result($PEDTitID,$PEDForenames,$PEDSurname,$PEDBirthday);
								$stm->fetch();
								$stm->free_result();
							}
						}
						
					}
					else {
						//$_SESSION['DebugStr'] .= 'Embedded Person: New Record<br />';
						//XCal - Now we know it's a new record or the person ID has failed so we need to initialise blanks
						unset($PEDTitID);
						$PEDForenames = '';
						$PEDSurname = '';
					}
					if (isset($PEDTitID))
						$ExQ = ' OR TIT_ID = ?';
					else $ExQ = '';
					$r_titles = $sql->prepare(
						'SELECT TIT_ID,TIT_TITLE '.
						'FROM con_titles '.
						'WHERE TIT_SR = 0'.$ExQ);
					if (isset($PEDAddWAC))
						$PEDExtraP = "AddWAC=$PEDAddWAC";
					elseif (!isset($PEDExtraP))
						$PEDExtraP = '';
					echo('<form action="javascript:;" onsubmit="'.
						"SavePersonEmbedded('$PEDTarget','$PEDMode','$PEDExtraP')".
						'">'.
						'<input type="hidden" id="PED_PER_ID" value="'.$PEDPER_ID.'" />'.
						'<select id="PED_TIT_ID">');
					if ($r_titles) {
						if (isset($PEDTitID))
							$r_titles->bind_param('i',$PEDTitID);
						if ($r_titles->execute()) {
							$r_titles->bind_result($TitID,$Title);
							while ($r_titles->fetch()) {
								if (!isset($PEDTitID))
									$PEDTitID = $TitID;
								echo('<option value="'.$TitID.'"');
								if ($TitID == $PEDTitID)
									echo(' selected');
								echo('>'.$Title.'</option>');							
							}
							$r_titles->free_result();
						}
					}
					echo('</select>'.
						'Forenames:&nbsp;<input type="text" id="PED_FORENAMES" value="'.$PEDForenames.'" /> '.
						'Surname:&nbsp;<input type="text" id="PED_SURNAME" value="'.$PEDSurname.'" /> '.
						'Date&nbsp;of&nbsp;Birth:&nbsp;<input type="date" id="PED_DOB" value="'.date('Y-m-d',strtotime($PEDBirthday)).'" /> '.
						'<input type="submit" class="button" value="Save" />');
					echo('</form>');
				}
			}
			elseif ($CONSec['NewMod'] == 1)
				echo('You only have the right to modify contacts your account is linked by, which does not include this person.');
			else echo('You do not have the required access rights to modify contacts.');
		}
		if ($PEDMode == 'V') {
			if ($CONSec['SearchView'] == 2)
				$CanView = true;
			else $CanView = ItemLinked($sql,3,$CEDCNP_ID);
			if ($CanView) {
				if ($PEDPER_ID == 0) {
					//$_SESSION['DebugStr'] .= 'Embedded Person: View Nobody.<br />';
					//XCal - Now we know we're being called to operate on a potential person record.
					echo('No Person Defined.');
					if (isset($PEDExtraP)) {						
						parse_str($PEDExtraP,$PEDExtraAR);
						if (isset($PEDExtraAR['AddWAC']) and isset($_SESSION['WebAccID']) and 
								($PEDExtraAR['AddWAC'] == $_SESSION['WebAccID'])) {
							//XCal - Now we know it's a user account and they're sending the account ID to add the person to
							echo(' <input type="button" class="button" value="Create" onclick="'.
								"AJAX('$PEDTarget','$PEDPath','For=PED&T=$PEDTarget&M=N&$PEDExtraP');".
								'" />');
						}
					}
					echo('<br />No Date of Birth.'.
						'<br />No Address. No ContactPoint.');
				}
				else {
					//$_SESSION['DebugStr'] .= 'Embedded Person: View Someone.<br />';
					//XCal - Now we know we're at least looking for a person record to exist
					$stm = $sql->prepare(
						'SELECT fn_con_fullname(PER_ID),PER_ADR_ID,PER_CNP_ID,PER_DOB '.
						'FROM con_people '.
						'WHERE PER_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$PEDPER_ID);
						if ($stm->execute()) {
							$stm->bind_result($PEDFullName,$PEDAdrID,$PEDCnpID,$PEDBirthday);
							if ($stm->fetch()) {
								echo("<a href=\"contacts.php?V=P&ID=$PEDPER_ID\">$PEDFullName</a>");
								if (isset($PEDExtraP)) {
									parse_str($PEDExtraP,$PEDExtraAR);
									if (isset($PEDExtraAR['AddWAC']) and isset($_SESSION['WebAccID']) and
											($PEDExtraAR['AddWAC'] == $_SESSION['WebAccID'])) {
										//XCal - Now we know it's a user account and they're sending the account ID to add the person to
										echo(' <input type="button" class="button" value="Modify" onclick="'.
											"AJAX('$PEDTarget','$PEDPath','For=PED&T=$PEDTarget&M=M&ID=$PEDPER_ID&$PEDExtraP');".
											'" />');
									}
								}
								if ($PEDBirthday > 0)
									echo('<br />Date of Birth: '.date('l jS F Y',strtotime($PEDBirthday)).'<br />');
								else echo('<br />No Date of Birth.<br />');
								if ($PEDAdrID > 0)
									echo('Has Address. ');
								else echo('No Address. ');
								if ($PEDCnpID > 0)
									echo('Has ContactPoint. ');
								else echo('No ContactPoint. ');
							}
							$stm->free_result();							
						}
						else echo('Embed Person failed getting the person details.');
					}
					else echo('Embed Person failed preparing to get person details.');
				}
			}
			elseif ($CONSec['SearchView'] == 1)
				echo('You only have the right to view contacts your account is linked by, which does not include this person.');
			else echo('You do not have the required access rights to view contacts.');
		}
	}
	if (strlen($PEDFailError) > 0)
		echo("Sorry to trouble you, but you may want to know about the following issues.<br />$PEDFailError");
}
else echo('You must be logged in to view this data.')
?>
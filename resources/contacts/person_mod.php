<?php
//XCal - We only want to pay attention to request variables if they're for the person modify page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PRM')) {
	$For = 'PRM';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$PRMTarget = $_REQUEST['T'];
	if (isset($_REQUEST['M'])) //XCal - [XC:M|Mode:N,M|New,Modify;]
		$PRMMode = $_REQUEST['M'];
	else $PRMMode = 'M';

	if (isset($_REQUEST['ID']))
		$PRMID = $_REQUEST['ID'];
	else $PRMID = -1;
	
	if (isset($_REQUEST['TIT']))
		$PRMTitID = $_REQUEST['TIT'];
	if (isset($_REQUEST['FNames']))
		$PRMForenames = $_REQUEST['FNames'];
	if (isset($_REQUEST['SName']))
		$PRMSurname = $_REQUEST['SName'];
	if (isset($_REQUEST['DOB']))
		$PRMBirthday = $_REQUEST['DOB'];
	if (isset($_REQUEST['AdrID']))
		$PRMAdrID = $_REQUEST['AdrID'];
	if (isset($_REQUEST['CnpID']))
		$PRMCnpID = $_REQUEST['CnpID'];
	if (isset($_REQUEST['AdrCTT']))
		$PRMAdrCTT = $_REQUEST['AdrCTT'];
	if (isset($_REQUEST['CO']))
		$PRMCareOf = $_REQUEST['CO'];
	if (isset($_REQUEST['Line1']))
		$PRMLine1 = $_REQUEST['Line1'];
	if (isset($_REQUEST['Line2']))
		$PRMLine2 = $_REQUEST['Line2'];
	if (isset($_REQUEST['Town']))
		$PRMTown = $_REQUEST['Town'];
	if (isset($_REQUEST['County']))
		$PRMCounty = $_REQUEST['County'];
	if (isset($_REQUEST['CntryID']))
		$PRMCntryID = $_REQUEST['CntryID'];
	if (isset($_REQUEST['PCode']))
		$PRMPostcode = $_REQUEST['PCode'];
	if (isset($_REQUEST['CnpCTT']))
		$PRMCnpCTT = $_REQUEST['CnpCTT'];
	if (isset($_REQUEST['ST']))
		$PRMSpeakTo = $_REQUEST['ST'];
	if (isset($_REQUEST['CnpCPT']))
		$PRMCnpCPT = $_REQUEST['CnpCPT'];
	if (isset($_REQUEST['Con']))
		$PRMContact = $_REQUEST['Con'];
	
}
elseif (isset($For) and ($For == 'PRM')) {
	if (isset($Target))
		$PRMTarget = $Target;
	if (isset($ID))
		$PRMID = $ID;
	if (isset($Mode))
		$PRMMode = $Mode;
	elseif (!isset($PRMMode))
		$PRMMode = 'M';
}
else $PRMMode = '<None>';
$PRMPath = 'resources/contacts/person_mod.php';
$PRMDieError = '';
$PRMFailError = '';
$PRMModes = array('N','M');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if ((! isset($PRMID)) and ($PRMMode == 'M'))
	$PRMDieError .= 'Person Modify has not been passed a person to modify.<br />';
if (! isset($PRMTarget))
	$PRMDieError .= 'Person Modify has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($PRMMode,$PRMModes))
	$PRMDieError .= "Person Modify has been passed a mode ($PRMMode) which it does not support.<br />";

if ($PRMDieError == '') {
	if (! function_exists('CheckToken'))		
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql, 1);
		if ($CONSec['NewMod'] == 2)
			$CanEdit = true;
		else $CanEdit = ItemLinked($sql,1,$PRMID);
		if ($CanEdit) {
			if (isset($PRMTitID)) {
				//XCal - Now we know they've attempted the save since the title ID was passed
				$stm = $sql->prepare('SET @PerID = ?, @AdrID = ?, @CnpID = ?, @RCode = 0, @RMsg = \'\'');
				if ($stm) {
					$stm->bind_param('iii',$PRMID,$PRMAdrID,$PRMCnpID);
					if ($stm->execute()) {
						$stm->free_result();
						if ($stm->prepare(
								'CALL sp_con_addedit_person_full(@PerID,'.
								'?,?,?,?,@AdrID,@CnpID,'.
								'?,?,?,?,?,?,'.
								'?,?,?,?,?,?,@RCode,@RMsg)')) {
							$stm->bind_param('isssisssssisisis',
								$PRMTitID,$PRMForenames,$PRMSurname,$PRMBirthday,
								$PRMAdrCTT,$PRMCareOf,$PRMLine1,$PRMLine2,$PRMTown,$PRMCounty,
								$PRMCntryID,$PRMPostcode,$PRMCnpCTT,$PRMSpeakTo,$PRMCnpCPT,$PRMContact);
							if ($stm->execute()) {
								$stm->free_result();
								if ($stm->prepare('SELECT @RCode,@RMsg,@PerID,@AdrID,@CnpID')) {
									if ($stm->execute()) {
										$stm->bind_result($RCode,$RMsg,$PRMID,$PRMAdrID,$PRMCnpID);
										$stm->fetch();
										$stm->free_result();
										echo('<article>'.
											'<h2>Person Saved</h2>'.
											'<div class="article-info">'.
											'Message generated on <time datetime ="'. date('Y-m-d H:i:s') .'">'. date('Y-m-d H:i:s') .'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a></div>'.
											"<p>$RMsg.<br />".
											'<input class="button" type="button" value="View Person" onclick="'.
											"AJAX('$PRMTarget','resources/contacts/person_view.php','For=PRV&T=$PRMTarget&ID=$PRMID');\" />".
											'</p></article>');
									}
									else $PRMFailError .= 'Person modify failed to retrieve the save result.<br />';
								}
								else $PRMFailError .= 'Person modify failed preparing to retrieve the save result.<br />';
							}
							else $PRMFailError .= 'Person modify failed to save the person('.$stm->error.').<br />';
						}
						else $PRMFailError .= 'Person modify failed preparing to save the person ('.$stm->error.').<br />';
					}
					else $PRMFailError .= 'Person modify failed initialising the save operation.<br />';
				}
				else $PRMFailError .= 'Person modify failed preparing to initialise the save operation.<br />';
			}
			else {
				//XCal - Now we know they haven't done the edit and attempted to save yet. So provide the edit screen
				$GotPerson = false;
				if ($PRMID > 0) {
					//XCal - Now we know they're editing not inserting
					$stm = $sql->prepare(
						'SELECT PER_TIT_ID,PER_FORENAMES,PER_SURNAME,PER_DOB,'.
						'PER_ADR_ID,PER_CNP_ID,PER_UPDATED '.
						'FROM con_people '.
						'WHERE PER_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$PRMID);
						if ($stm->execute()) {
							$stm->bind_result($PRMTitID,$PRMForenames,$PRMSurname,$PRMBirthday,
								$PRMAdrID,$PRMCnpID,$PRMPerUpdated);
							$GotPerson = $stm->fetch();
							$stm->free_result();
						}
						else $PRMFailError .= 'Person modify failed to get the person details.<br />';
					}
					else $PRMFailError .= 'Person modify failed preparing to get the person details.<br />';
				}
				else {
					//XCal - Since they're adding a new person initialise the variables
					$GotPerson = true;
					if (isset($PRMTitID))
						unset($PRMTitID);
					$PRMForenames = '';
					$PRMSurname = '';
					$PRMBirthday = '1981-05-07';
					$PRMAdrID = 0;
					$PRMCnpID = 0;
					$PRMPerUpdated = 'Never';
				}
				
				if ($GotPerson) {
					//XCal - Build the person edit screen
					if ($PRMMode == 'M')
						$Action = 'Modify';
					else $Action = 'New';
					echo ("<h4>$Action Person Information</h4>".
						//'<form action="resources/contacts/person_mod.php" method="POST">'.
						//'<input type="hidden" name="Action" value="SAVE" />'.
						"<input type=\"hidden\" id=\"PER_ID\" value=\"$PRMID\" />".
						"<input type=\"hidden\" id=\"PER_ADR_ID\" value=\"$PRMAdrID\" />".
						"<input type=\"hidden\" id=\"PER_CNP_ID\" value=\"$PRMCnpID\" />".
						'<table><tr>'.
						'<td class="fieldname">Title</td>'.
						'<td><select id="PER_TIT_ID">');
					$stm = $sql->prepare(
						'SELECT TIT_ID,TIT_TITLE FROM con_titles WHERE TIT_SR = 0 OR TIT_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$PRMTitID);
						if ($stm->execute()) {
							$stm->bind_result($TitID,$Title);
							while ($stm->fetch()) {
								if (! isset($PRMTitID))
									$PRMTitID = $TitID;
								echo("<option value=\"$TitID\"");
								if ($TitID == $PRMTitID)
									echo(' selected');
								echo(">$Title</option>");
							}
							$stm->free_result();
						}
						else $PRMFailError .= 'Person modify failed to get available titles.<br />';
					}
					else $PRMFailError .= 'Person modify failed preparing to get available titles.<br />';
					echo ('</select></td><tr>'.
						'<td class="fieldname">Forenames</td>'.
						"<td><input type=\"text\" id=\"PER_FORENAMES\" value=\"$PRMForenames\" /></td></tr>".
						'<tr><td class="fieldname">Surname</td>'.
						"<td><input type=\"text\" id=\"PER_SURNAME\" value=\"$PRMSurname\" />".
						'</td></tr>'.
						'<tr><td class="fieldname">Date of Birth</td>'.
						'<td><input type="date" id="PER_DOB" value="'.date('Y-m-d',strtotime($PRMBirthday)).'" />'.
						'</td></tr>'.
						'</table><br />');
					if ($PRMPerUpdated > 0)
						echo("Person Updated: $PRMPerUpdated<br /><br />");
					
					echo ('<table><tr><th>Primary Address</th><th>Primary Contact</th></tr>'.
						'<tr>'.
						'<td><div id="embedaddress">');
					$For = 'AED';
					$Target = 'embedaddress';
					$ONRLTPID = 1;
					$ONRID = $PRMID;
					$ID = $PRMAdrID;
					$Mode = 'EM';
					require(dirname(__FILE__).'/address_embed.php');
					echo('</div></td>'.
						'<td><div id="embedcontactpoint">');
					$For = 'CED';
					$Target = 'embedcontactpoint';
					$ONRLTPID = 1;
					$ONRID = $PRMID;
					$ID = $PRMCnpID;
					$Mode = 'EM';
					require(dirname(__FILE__).'/contactpoint_embed.php');
					echo('</div></td></tr></table>'.
						'<input class="save" type="button" value="Save" onclick="'.
						"SavePerson('$PRMTarget','$PRMMode');".
						'" />'.
						'<input class="cancel" type="button" value="Cancel" onclick="'.
						"AJAX('$PRMTarget','resources/contacts/person_view.php','For=PRV&T=$PRMTarget&ID=$PRMID');".
						'" /><br />');
				}
				else $PRMFailError .= 'Person modify did not fail but did not retrieve the person details. This is an unexpected error.<br />';
			}
			if (strlen($PRMFailError) > 0)
				echo("Sorry to trouble you, but you may want to know about the following issues.<br />$PRMFailError");
		}
		elseif ($CONSec['NewMod'] == 1)
			echo('You only have the right to modify contacts which your account is linked by, which does not include this person.');
		else echo('You do not have the required access rights to modify contacts.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Person Modify Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$PRMDieError.
			'</article>');
}
?>
<?php
//XCal - We only want to pay attention to request variables if they're for the all links view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'RQF')) {
	$For = 'RQF';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$RQFTarget = $_REQUEST['T'];

	//XCal - [XC:OLT|Owner Link Type: The owner link type ID;;]
	if (isset($_REQUEST['LTP']))
		$RQFLinkType = $_REQUEST['LTP'];
	if (isset($_REQUEST['ID']))
		$RQFID = $_REQUEST['ID'];
	
	if (isset($_REQUEST['FLP']))
		$RQFFLPID = $_REQUEST['FLP'];
	if (isset($_REQUEST['FLT']))
		$RQFFLTID = $_REQUEST['FLT'];
	if (isset($_REQUEST['FLM']))
		$RQFFLMID = $_REQUEST['FLM'];
	if (isset($_REQUEST['FLL']))
		$RQFFLLID = $_REQUEST['FLL'];
	if (isset($_REQUEST['REF']))
		$RQFReference = $_REQUEST['REF'];
	if (isset($_REQUEST['ACQ']))
		$RQFAcquired = $_REQUEST['ACQ'];
	if (isset($_REQUEST['EXP']))
		$RQFExpires = $_REQUEST['EXP'];	

	//XCal - Supported values [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;]
	if (isset($_REQUEST['M']))
		$RQFMode = $_REQUEST['M'];
	elseif (! isset($RQFMode))
		$RQFMode = 'L';
	//XCal - Variables that are reset if they aren't passed in
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 10;		
}
elseif (isset($For) and ($For == 'RQF')) {
	if (isset($Target))
		$RQFTarget = $Target;
	
	if (isset($LTP))
		$RQFLinkType = $LTP;
	if (isset($ID))
		$RQFID = $ID;	
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 10;
}
else {			
	$Offset = 0;
	$Records = 10;
}
if (! isset($RQFMode))
	$RQFMode = 'L';
if (! isset($RQFFLPID))
	$RQFFLPID = 0;

$RQFPath = 'resources/reqful/reqful_fulfill.php';
$RQFDieError = '';
$RQFFailError = '';
$RQFModes = array('L','N','M','D');

//XCal - Let's make sure we have the values we need to run the page
if (! isset($RQFLinkType))
	$RQFDieError .= 'Fulfill requirements has not been passed the link type identifier.<br />';
if (! isset($RQFID))
	$RQFDieError .= 'Fulfill requirements has not been passed the identifier to display fulfillments for.<br />';
if (! isset($RQFTarget))
	$RQFDieError .= 'Fulfill requirements has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($RQFMode,$RQFModes))
	$RQFDieError .= "Fulfill requirements has been passed a mode ($RQFMode) which it does not support.<br />";
	

if ($RQFDieError == '') {
	$RQFKeys = "For=RQF&T=$RQFTarget&LTP=$RQFLinkType&ID=$RQFID";
	//$_SESSION['DebugStr'] .= "Fulfill Requirements: Running with keys $RQFKeys on child $TPLLinkID<br />";
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$REQSec = GetAreaSecurity($sql, 4);
		$IsLinked = ItemLinked($sql,$RQFLinkType,$RQFID);
		if ($REQSec['SearchView'] == 2)
			$CanView = true;
		else $CanView = ($REQSec['SearchView'] > 0) and $IsLinked;
		if ($REQSec['NewMod'] == 2)
			$CanMod = true;
		else $CanMod = ($REQSec['NewMod'] > 0) and $IsLinked;
		if ($REQSec['Remove'] == 2)
			$CanRem = true;
		else $CanRem = ($REQSec['Remove'] > 0) and $IsLinked;
		
		//XCal - Let's check if we're in New/Modify/Delete mode
		if (in_array($RQFMode,array('N','M','D'))) {
			if (in_array($RQFMode,array('M','D')) and (!($RQFFLPID > 0))) {
				$RQFFailError .= 'Fulfill requirements was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
				$RQFMode = 'L';
			}
			elseif ($RQFMode == 'D') { //XCal - Delete item and return to list mode
				if ($CanRem) {
					//$_SESSION['DebugStr'] .= 'Type Links: Delete Mode<br />';
					$stm->prepare("DELETE FROM rqf_fulfillment_providers WHERE FLP_ID = ?");
					if ($stm) {
						$stm->bind_param('i',$RQFFLPID);
						if ($stm->execute()) {
							$stm->free_result();
						}
						else $RQFFailError .= 'Fulfill requirements failed removing the provision ('.$stm->error.').<br />';
					}
					else $RQFFailError .= 'Fulfill requirements encountered an error initialising provision removal ('.$sql->error.').<br />';
				}
				elseif ($REQSec['Remove'] == 1)
					echo('You only have the right to remove fulfillments from items your account is linked by, which does not include the requested item.');
				else echo('You do not have the required access right to remove fulfillments.');
				//XCal - Remove or fail return to list view, beats the hell out of a blank screen!
				$RQFMode = 'L';
			}
			else { //XCal - (N)ew or (M)odify
				if ($CanMod) {
					if (isset($RQFFLTID)) { //XCal - If the type's been passed we know it's a save operation
						//XCal - We're using an add/edit stored proc so we don't actually care if we're inserting or modifying
						$stm = $sql->prepare('SET @FLPID = ?,@Code = 0,@Msg = \'\'');
						if ($stm) {
							$stm->bind_param('i',$RQFFLPID);
							if ($stm->execute()) {
								$stm->free_result();
								if ($stm->prepare('CALL sp_rqf_addedit_provider(@FLPID,?,?,?,?,?,?,?,@Code,@Msg)')) {
									if ($RQFAcquired == '')
										$RQFAcquired = NULL;
									if ($RQFExpires == '')
										$RQFExpires = NULL;
									$stm->bind_param('iiiisss',$RQFFLTID,$RQFID,$RQFFLMID,$RQFFLLID,$RQFReference,$RQFAcquired,$RQFExpires);
									if ($stm->execute()) {
										$stm->free_result();
										if ($stm->prepare('SELECT @Code,@Msg')) {
											if ($stm->execute()) {
												$stm->bind_result($RCode,$RMsg);
												$stm->fetch();
												$stm->free_result();
												echo("New/Modify result: $RMsg");
												$RQFMode = 'L';
											}
											else $RQFFailError .= 'Fulfill requirements encountered an error retrieving the new/modify results ('.$stm->error.').<br />';
										}
										else $RQFFailError .= 'Fulfill requirements encountered an error initialising new/modify result retrieval ('.$stm->error.').<br />';
									}
									else $RQFFailError .= 'Fulfill requirements encountered an error performing the new/modify operation ('.$stm->error.').<br />';
								}
								else $RQFFailError .= 'Fulfill requirements encountered an error preparing the new/modify operation ('.$stm->error.').<br />';
							}
							else $RQFFailError .= 'Fulfill requirements encountered an error initialising the new/modify operation ('.$stm->error.').<br />';
						}
						else $RQFFailError .= 'Fulfill requirements encountered an error preparing to initialise the new/modify operation ('.$sql->error.').<br />';
					}
					else { //XCal - Values not passed so we know they want the new/modify input screen
						//XCal - If it's a modify then get the DB values
						if ($RQFFLPID > 0) {
							$stm = $sql->prepare('SELECT FLP_FLT_ID,FLP_FLM_ID,FLP_FLL_ID,FLP_REFERENCE,FLP_ACQUIRED,FLP_EXPIRES,FLP_UPDATED '.
									'FROM rqf_fulfillment_providers '.
									'WHERE FLP_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$RQFFLPID);
								if ($stm->execute()) {
									$stm->bind_result($RQFFLTID,$RQFFLMID,$RQFFLLID,$RQFReference,$RQFAcquired,$RQFExpires,$RQFUpdated);
									$stm->fetch();
									$stm->free_result();
									if (!empty($RQFAcquired))
										$RQFAcquired = date('Y-m-d',strtotime($RQFAcquired));
									if (!empty($RQFExpires))
										$RQFExpires = date('Y-m-d',strtotime($RQFExpires));
								}
								else $RQFFailError .= 'Fulfill requirements encountered an error retrieving values for modification ('.$stm->error.').<br />';
							}
							else $RQFFailError .= 'Fulfill requirements encountered an error initialising value retrieval for modification ('.$sql->error.').<br />';
						}
						else {
							unset($RQFFLTID);
							unset($RQFFLMID);
							unset($RQFFLLID);
							$RQFReference = '';
							$RQFAcquired = date('Y-m-d'); 
							$RQFExpires = '';
							$RQFUpdated = 'Never';
						}
						
						echo('<h5>');
						if ($RQFFLPID > 0)
							echo('Modify ');
						else echo('New ');
						echo('Fulfillment Provision</h5>'.
							'<input id="FLP_ID" type="hidden" value="'.$RQFFLPID.'" />'.
							'<input id="FLP_LINK_ID" type="hidden" value="'.$RQFID.'" />'.
							'<table><tr><td><table><tr>'.
							'<td class="fieldname">Type</td>'.
							'<td><select id="FLP_FLT_ID" onchange="'.
								"SelectFulfillments('$RQFLinkType','$RQFID','FLP','fulfill')".
								'">'); 
						
						$stm = $sql->prepare('SELECT FLT_ID,FLT_NAME FROM rqf_fulfillment_types WHERE FLT_FOR_LTP_ID = ? '.
								'AND EXISTS(SELECT FLM_ID FROM rqf_fulfillments WHERE FLM_FLT_ID = FLT_ID AND FLM_MODE = 0)');
						if ($stm) {
							$stm->bind_param('i',$RQFLinkType);
							if ($stm->execute()) {
								$stm->bind_result($FLTID,$FLTName);
								while ($stm->fetch()) {
									if (!isset($RQFFLTID))
										$RQFFLTID = $FLTID;
									echo('<option value="'.$FLTID.'"');
									if ($FLTID == $RQFFLTID)
										echo(' selected');
									echo(">$FLTName</option>");
								}
								$stm->free_result();
							}
							else $RQFFailError .= 'Fulfill requirements encountered an error fetching types for selection ('.$sql->error.').<br />';
						}
						else $RQFFailError .= 'Fulfill requirements encountered an error initialising type selection ('.$sql->error.').<br />';
						
						echo('</select></td>'.
							'<tr><td class="fieldname">Provide</td>'.
							"<td><div id=\"fulfill$RQFLinkType"."_$RQFID\">");
						require(dirname(__FILE__).'/reqful_select_fulfill.php');				
						echo('</div></td></tr>'.
							'<tr><td class="fieldname">Level</td>'.
							"<td><div id=\"fulfilllevel$RQFLinkType"."_$RQFID\">");
						require(dirname(__FILE__).'/reqful_select_level.php');
						echo('</div></td></tr>'.
							'</table></td><td><table>'.
							'<tr><td class="fieldname">Reference</td>'.
							'<td><input id="FLP_REF" type="text" value="'.$RQFReference.'" /></td></tr>'.
							'<tr><td class="fieldname">Acquired</td>'.
							'<td><input id="FLP_ACQUIRED" type="date" value="'.$RQFAcquired.'" /></td></tr>'.
							'<tr><td class="fieldname">Expires</td>'.
							'<td><input id="FLP_EXPIRES" type="date" value="'.$RQFExpires.'" /></td></tr>'.
							'</table></td></tr></table>'.
							'<input class="save" type="button" value="Save" onclick="SaveFulfillProvider('."'$RQFTarget','$RQFLinkType','$RQFMode'".');" />'.
							'<input class="cancel" type="button" value="Cancel" onclick="'.
							"AJAX('$RQFTarget','$RQFPath','$RQFKeys&M=L');\" /><br /><br />");
					}
				}
				elseif ($REQSec['NewMod'] == 1)
					echo('You only have the right to remove fulfillments from items your account is linked by, which does not include the requested item.');
				else echo('You do not have the required access rights to remove fulfillments.');
			}
		}
		
		//XCal - If we're not modifying or the action's done we need to run the search/list
		if ($RQFMode == 'L') {
			if ($CanView) {
				//XCal - First lets check if fulfillment provision is possible for this type!		
				$stm = $sql->prepare('SELECT COUNT(FLT_ID) FROM rqf_fulfillment_types WHERE '.
						'FLT_FOR_LTP_ID = ? AND '.
						'EXISTS(SELECT FLM_ID FROM rqf_fulfillments '.
							'WHERE FLM_FLT_ID = FLT_ID AND FLM_MODE = 0)');
				if ($stm) {
					$stm->bind_param('i',$RQFLinkType);
					if ($stm->execute()) {
						$stm->bind_result($TCount);
						$stm->fetch();
						$stm->free_result();
					}
					else $RQFFailError .= 'Fulfill requirements encountered an error counting applicable types ('.$stm->error.').<br />';
				}
				else $RQFFailError .= 'Fulfill requirements encountered an error preparing to count applicable types ('.$sql->error.').<br />';
				
				//XCal - Only show fulfillment provider information if they're possible for the type
				if (isset($TCount) and ($TCount > 0)) {
					echo('<h4>Fulfill Requirements</h4>');
					if ($CanMod)
						echo('<input type="button" class="button" value="Provide..." onclick="'.
								"AJAX('$RQFTarget','$RQFPath','$RQFKeys&M=N');".	
								'" /><br />');
					$stm = $sql->prepare('SELECT COUNT(FLP_ID) '.
								'FROM rqf_fulfillment_providers '.
								'JOIN rqf_fulfillment_types ON FLT_ID = FLP_FLT_ID AND FLT_FOR_LTP_ID = ? '.
								'WHERE FLP_LINK_ID = ? ');
					if ($stm) {
						$stm->bind_param('ii',$RQFLinkType,$RQFID);
						if ($stm->execute()) {
					  		$stm->bind_result($Count);
					  		$stm->fetch();
					  		$stm->free_result();
						}
						else $RQFFailError .= 'Fulfill requirements encountered an error getting fulfillment count ('.$stm->error.').<br />';
					}
					else $RQFFailError .= 'Fulfill requirements encountered an error preparing to get fulfillment count ('.$sql->error.').<br />';
					
					if ($Count == 0)
						echo("<center><em>Record provides no fulfillments</em></center><br />");
					else {
						$stm = $sql->prepare(
								'SELECT FLP_ID,FLT_NAME,FLM_NAME,FLL_NAME,FLP_ACQUIRED,FLP_EXPIRES '.
								'FROM rqf_fulfillment_providers '.
								'JOIN rqf_fulfillment_types ON FLT_ID = FLP_FLT_ID AND FLT_FOR_LTP_ID = ? '.
								'JOIN rqf_fulfillments ON FLM_ID = FLP_FLM_ID '.
								'JOIN rqf_fulfillment_levels ON FLL_ID = FLP_FLL_ID '.
								'WHERE FLP_LINK_ID = ? LIMIT ?,?');
						if ($stm) {					
							$stm->bind_param('iiii',$RQFLinkType,$RQFID,$Offset,$Records);
							if ($stm->execute()) {
								$stm->bind_result($FLPID,$TypeName,$FulfillName,$LevelName,$Acquired,$Expires);
			
								$LowRec = $Offset+1;
								if ($Offset+$Records > $Count)
									$HighRec = $Count;
								else $HighRec = $Offset+$Records;						
								echo("<center>Provisions $LowRec-$HighRec of $Count</center>".
									'<table><tr><th>Type</th><th>Fulfillment</th><th>Level</th><th>Acquired</th><th>Expires</th>');
								if ($CanMod)
									echo('<th>Edit</th>');
								if ($CanRem)
									echo('<th>Remove</th>');
								echo('</tr>');
								while ($stm->fetch()) {
									echo("<tr><td>$TypeName</td><td>$FulfillName</td><td>$LevelName</td><td>$Acquired</td><td>$Expires</td>");
									if ($CanMod)
										echo('<td><input type="button" class="button" value="Edit" onclick="'.
												"AJAX('$RQFTarget','$RQFPath','$RQFKeys&M=M&FLP=$FLPID');".
												'" /></td>');
									if ($CanRem)
										echo('<td><input type="button" class="button" value="Remove" onclick="'.
												"AJAX('$RQFTarget','$RQFPath','$RQFKeys&M=D&FLP=$FLPID');".
												'" /></td>');
									echo('</tr>');
								}
								$stm->free_result();
								echo('</table>');
							}
							else $RQFFailError .= 'Fulfill requirements failed to get provisions ('.$stm->error.').<br />';
						}
						else $RQFFailError .= 'Fulfill requirements failed while preparing to get provisions ('.$sql->error.').<br />';
						
						//XCal - Control that apply only when there are records
						if ($Offset > 0) {
							$PrevOffset = $Offset-$Records;
							echo('<input class="prevbutton" type="button" value="Prev" onclick="'.
									"AJAX('$RQFTarget','$RQFPath','$RQFKeys&M=L&RO=$PrevOffset&RC=$Records');".
								'" />');
						}
						if ($Count > ($Offset+$Records)) {
							$NextOffset = $Offset+$Records;
							echo('<input class="nextbutton" type="button" value="Next" onclick="'.
								"AJAX('$RQFTarget','$RQFPath','$RQFKeys&M=L&RO=$NextOffset&RC=$Records');".
								'" />');
						}
						echo('<br />'); //XCal - Stops next and prior floating over next article
					}
				}
			}
			elseif ($REQSec['SearchView'] == 1)
				echo('You only have the right to view fulfillments on items your account is linked by, which does not include this item.');
			else echo('You do not have the required access right to view fulfillments.');
		}
		if (strlen($RQFFailError) > 0)
			echo('Sorry to trouble you, but there were some issues you may want to know about, '.$RQFFailError);
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Fulfill Requirements Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$RQFDieError.
			'</article>');
}
?>
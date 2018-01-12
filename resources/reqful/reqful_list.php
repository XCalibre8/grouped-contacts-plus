<?php
//XCal - We only want to pay attention to request variables if they're for the all links view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'RQL')) {
	$For = 'RQL';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$RQLTarget = $_REQUEST['T'];

	//XCal - [XC:OLT|Owner Link Type: The owner link type ID;;]
	if (isset($_REQUEST['LTP']))
		$RQLLinkType = $_REQUEST['LTP'];
	if (isset($_REQUEST['ID']))
		$RQLID = $_REQUEST['ID'];
	
	if (isset($_REQUEST['REQ']))
		$RQLREQID = $_REQUEST['REQ'];
	
	if (isset($_REQUEST['ALT']))
		$RQLApplyLinkType = $_REQUEST['ALT'];
	if (isset($_REQUEST['FLT']))
		$RQRFLTID = $_REQUEST['FLT'];
	if (isset($_REQUEST['FLM']))
		$RQRFLMID = $_REQUEST['FLM'];
	if (isset($_REQUEST['FLL']))
		$RQRFLLID = $_REQUEST['FLL'];
	if (isset($_REQUEST['NUM']))
		$RQRCount = $_REQUEST['NUM'];

	//XCal - Supported values [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;]
	if (isset($_REQUEST['M']))
		$RQRMode = $_REQUEST['M'];
	elseif (! isset($RQRMode))
		$RQRMode = 'L';
}
elseif (isset($For) and ($For == 'RQR')) {
	if (isset($Target))
		$RQLTarget = $Target;
	
	if (isset($LTP))
		$RQLLinkType = $LTP;	
	if (isset($ID))
		$RQLID = $ID;
}
if (! isset($RQRMode))
	$RQRMode = 'L';
if (! isset($RQLREQID))
	$RQLREQID = 0;
if (isset($RQRFLTID) and ($RQRFLTID == 'NULL'))
	$RQRFLTID = null;
if (isset($RQLApplyLinkType) and ($RQLApplyLinkType == 'NULL'))
	$RQLApplyLinkType = null;

$RQRPath = 'resources/reqful/reqful_require.php';
$RQRDieError = '';
$RQRFailError = '';
$RQRModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($RQLLinkType))
	$RQRDieError .= 'Require fulfillments has not been passed the link type identifier.<br />';
if (! isset($RQLID))
	$RQRDieError .= 'Require fulfillments has not been passed the identifier to display fulfillments for.<br />';
if (! isset($RQLTarget))
	$RQRDieError .= 'Require fulfillments has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($RQRMode,$RQRModes))
	$RQRDieError .= "Require fulfillments has been passed a mode ($RQRMode) which it does not support.<br />";
	

if ($RQRDieError == '') {
	$RQRKeys = "For=RQR&T=$RQLTarget&LTP=$RQLLinkType&ID=$RQLID";
	//$_SESSION['DebugStr'] .= "Require Fulfillments: Running with keys $RQRKeys on child $TPLLinkID<br />";
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$REQSec = GetAreaSecurity($sql, 4);
		$IsLinked = ItemLinked($sql,$RQLLinkType,$RQLID);
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
		if (in_array($RQRMode,array('N','M','D'))) {
			if (in_array($RQRMode,array('M','D')) and (!($RQLREQID > 0))) {
				$RQRFailError .= 'Require fulfillments was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
				$RQRMode = 'L';
			}
			elseif ($RQRMode == 'D') { //XCal - Delete item and return to list mode
				if ($CanRem) {
					//$_SESSION['DebugStr'] .= 'Type Links: Delete Mode<br />';
					$stm = $sql->prepare("DELETE FROM rqf_requirements WHERE REQ_ID = ?");
					if ($stm) {
						$stm->bind_param('i',$RQLREQID);
						if ($stm->execute()) {
							$stm->free_result();
							echo('Record removed.');
						}
						else $RQRFailError .= 'Require fulfillments failed removing the requirement ('.$stm->error.').<br />';
					}
					else $RQRFailError .= 'Require fulfillments encountered an error initialising requirement removal ('.$sql->error.').<br />';
				}
				elseif ($REQSec['Remove'] > 0)
					echo('You only have the right to remove requirements from items your account is linked by, which does not include the requested item.');
				else echo('You do not have the required access right to remove requirements.');
				//XCal - Remove or fail return to list view, beats the hell out of a blank screen!
				$RQRMode = 'L';
			}
			else { //XCal - (N)ew or (M)odify
				if ($CanMod) {
					if (isset($RQRCount)) { //XCal - If the type's been passed we know it's a save operation
						//XCal - We're using an add/edit stored proc so we don't actually care if we're inserting or modifying
						$stm = $sql->prepare('SET @REQID = ?,@Code = 0,@Msg = \'\'');
						if ($stm) {
							$stm->bind_param('i',$RQLREQID);
							if ($stm->execute()) {
								$stm->free_result();
								if ($stm->prepare('CALL sp_rqf_addedit_requirement(@REQID,?,?,?,?,?,?,?,@Code,@Msg)')) {							
									$stm->bind_param('iiiiiii',$RQLLinkType,$RQLID,$RQLApplyLinkType,$RQRFLTID,$RQRFLMID,$RQRFLLID,$RQRCount);
									if ($stm->execute()) {
										$stm->free_result();
										if ($stm->prepare('SELECT @Code,@Msg')) {
											if ($stm->execute()) {
												$stm->bind_result($RCode,$RMsg);
												$stm->fetch();
												$stm->free_result();
												echo("New/Modify result: $RMsg");										
											}
											else $RQRFailError .= 'Require fulfillments encountered an error retrieving the new/modify results ('.$stm->error.').<br />';
										}
										else $RQRFailError .= 'Require fulfillments encountered an error initialising new/modify result retrieval ('.$stm->error.').<br />';
									}
									else $RQRFailError .= 'Require fulfillments encountered an error performing the new/modify operation ('.$stm->error.').<br />';
								}
								else $RQRFailError .= 'Require fulfillments encountered an error preparing the new/modify operation ('.$stm->error.').<br />';
							}
							else $RQRFailError .= 'Require fulfillments encountered an error initialising the new/modify operation ('.$stm->error.').<br />';
						}
						else $RQRFailError .= 'Require fulfillments encountered an error preparing to initialise the new/modify operation ('.$sql->error.').<br />';
						$RQRMode = 'L';
					}
					else { //XCal - Values not passed so we know they want the new/modify input screen
						//XCal - If it's a modify then get the DB values
						if ($RQLREQID > 0) {
							$stm = $sql->prepare('SELECT REQ_CHD_LTP_ID,REQ_FLT_ID,REQ_FLM_ID,REQ_MIN_FLL_ID,REQ_COUNT,REQ_UPDATED '.
									'FROM rqf_requirements '.
									'WHERE REQ_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$RQLREQID);
								if ($stm->execute()) {
									$stm->bind_result($RQLApplyLinkType,$RQRFLTID,$RQRFLMID,$RQRFLLID,$RQRCount,$RQRUpdated);
									$stm->fetch();
									$stm->free_result();
								}
								else $RQRFailError .= 'Require fulfillments encountered an error retrieving values for modification ('.$stm->error.').<br />';
							}
							else $RQRFailError .= 'Require fulfillments encountered an error initialising value retrieval for modification ('.$sql->error.').<br />';
						}
						else {
							unset($RQLApplyLinkType);
							unset($RQRFLTID);
							unset($RQRFLMID);
							unset($RQRFLLID);
							$RQRCount = 1;
							$RQRUpdated = 'Never';
						}
						
						echo('<h5>');
						if ($RQLREQID > 0)
							echo('Modify ');
						else echo('New ');
						echo('Requirement</h5>'.
							'<input id="REQ_ID" type="hidden" value="'.$RQLREQID.'" />'.
							'<input id="REQ_ONR_ID" type="hidden" value="'.$RQLID.'" />'.
							'<table><tr>'.
							'<td class="fieldname">For...</td>'.
							'<td><select id="REQ_FOR_LTP_ID" onchange="'.
								"SelectFulfillmentType('$RQLLinkType','$RQLID','REQ','require')".
								'">');
						if ($RQLLinkType == 4)
							$TCount = GroupLinkCount($RQRFailError,$sql);
						else $TCount = 0;
						if ($TCount > 0)
							$stm = $sql->prepare('SELECT LTP_ID,LTP_NAME FROM grp_groups '.
									'JOIN grp_group_type_avail_links ON GTA_GTP_ID = GRP_GTP_ID '.
									'JOIN lnk_link_types ON LTP_ID = GTA_LTP_ID '.
									'WHERE GRP_ID = ?');
						else $stm = $sql->prepare('SELECT LTP_ID,LTP_NAME FROM lnk_link_type_avail JOIN lnk_link_types ON LTP_ID = LTA_CHD_LTP_ID '.
								'WHERE LTA_ONR_LTP_ID = ? '.
								'AND EXISTS(SELECT FLT_ID FROM rqf_fulfillment_types WHERE FLT_FOR_LTP_ID = LTA_CHD_LTP_ID)');
						if ($stm) {
							if ($TCount > 0)
								$stm->bind_param('i',$RQLID);
							else $stm->bind_param('i',$RQLLinkType);
							if ($stm->execute()) {
								$stm->bind_result($LTPID,$LTPName);
								while ($stm->fetch()) {
									if (!isset($RQLApplyLinkType))
										$RQLApplyLinkType = $LTPID;
									echo('<option value="'.$LTPID.'"');
									if ($LTPID == $RQLApplyLinkType)
										echo(' selected');
									echo(">$LTPName</option>");
								}
								$stm->free_result();
							}
							else $RQRFailError .= 'Require fulfillments encountered an error getting link types for selection ('.$sql->error.').<br />';
						}
						else $RQRFailError .= 'Require fulfillments encountered an error initialising link type selection ('.$sql->error.').<br />';
						echo('</select></td></tr>'.
							'<tr><td class="fieldname">Type</td>'.
							"<td><div id=\"requiretype$RQLLinkType"."_$RQLID\">");
						//XCal - Minor fudge below regarding variable name conventions to re-use the select pages
						$RQFPrefix = 'REQ';
						$RQFDivPrefix = 'require';
						$RQFLinkType = $RQLLinkType;
						$RQFApplyLinkType = $RQLApplyLinkType;
						if (isset($RQRFLTID))
							$RQFFLTID = $RQRFLTID;
						$RQFID = $RQLID;
						if (isset($RQRFLMID))
							$RQFFLMID = $RQRFLMID;				
						require(dirname(__FILE__).'/reqful_select_type.php');
						echo('</div></td></tr>'.
							'<tr><td class="fieldname">Provide</td>'.
							"<td><div id=\"require$RQLLinkType"."_$RQLID\">");
						require(dirname(__FILE__).'/reqful_select_fulfill.php');				
						echo('</div></td></tr>'.
							'<tr><td class="fieldname">Level</td>'.
							"<td><div id=\"requirelevel$RQLLinkType"."_$RQLID\">");
						require(dirname(__FILE__).'/reqful_select_level.php');
						echo('</div></td></tr>'.
							'<tr><td class="fieldname">Count</td>'.
							"<td><input id=\"REQ_COUNT\" type=\"number\" min=\"0\" value=\"$RQRCount\" /> Set to 0 for all</td></tr>".
							'</table>'.
							'<input class="save" type="button" value="Save" onclick="'.
								"SaveRequirement('$RQLTarget','$RQLLinkType','$RQRMode'".');" />'.
							'<input class="cancel" type="button" value="Cancel" onclick="'.
							"AJAX('$RQLTarget','$RQRPath','$RQRKeys&M=L');\" /><br /><br />");
					}
				}
				elseif ($REQSec['NewMod'] > 0)
					echo('You only have the right to modify requirements on items your account is linked by, which does not include the requested item.');
				else echo('You do not have the required access right to remove requirements.');
			}
		}
		
		//XCal - If we're not modifying or the action's done we need to run the search/list
		if ($RQRMode == 'L') {
			if ($CanView) {
				//XCal - First lets check if requirements are possible for this types link types!		
				if ($RQLLinkType == 4) {
					$TCount = GroupLinkCount($RQRFailError,$sql);
		
					if ($TCount > 0) {
						$stm = $sql->prepare('SELECT COUNT(FLT_ID) FROM grp_groups '.
								'JOIN grp_group_type_avail_links ON GTA_GTP_ID = GRP_GTP_ID '.
								'JOIN rqf_fulfillment_types ON FLT_FOR_LTP_ID = GTA_LTP_ID '.
								'AND EXISTS(SELECT FLM_ID FROM rqf_fulfillments '.
									'WHERE FLM_FLT_ID = FLT_ID)'.
								'WHERE GRP_ID = ?');
						if ($stm) {
							$stm->bind_param('i',$RQLID);
							if ($stm->execute()) {
								$stm->bind_result($TCount);
								$stm->fetch();
								$stm->free_result();
							}
							else $RQRFailError .= 'Require fulfillments encountered an error counting applicable types ('.$stm->error.').<br />';
						}
						else $RQRFailError .= 'Require fulfillments encountered an error preparing to count applicable types ('.$sql->error.').<br />';				
					}
					else unset($TCount);
				}
				
				if (!isset($TCount)) {
					$stm = $sql->prepare('SELECT COUNT(FLT_ID) FROM lnk_link_type_avail '.
							'JOIN rqf_fulfillment_types ON FLT_FOR_LTP_ID = LTA_CHD_LTP_ID '.
							'AND EXISTS(SELECT FLM_ID FROM rqf_fulfillments '.
								'WHERE FLM_FLT_ID = FLT_ID)'.
							'WHERE LTA_ONR_LTP_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$RQLLinkType);
						if ($stm->execute()) {
							$stm->bind_result($TCount);
							$stm->fetch();
							$stm->free_result();
						}
						else $RQRFailError .= 'Require fulfillments encountered an error counting applicable types ('.$stm->error.').<br />';
					}
					else $RQRFailError .= 'Require fulfillments encountered an error preparing to count applicable types ('.$sql->error.').<br />';
				}
				//XCal - Only show requirements information if they're possible for the type
				if (isset($TCount) and ($TCount > 0)) {
					echo('<h4>Require Fulfillments</h4>'.				
						'<input type="button" class="button" value="Require..." onclick="'.
						"AJAX('$RQLTarget','$RQRPath','$RQRKeys&M=N');".	
						'" /><br />');
					$stm = $sql->prepare('SELECT COUNT(REQ_ID) '.
								'FROM rqf_requirements '.
								'LEFT OUTER JOIN rqf_fulfillment_types ON FLT_ID = REQ_FLT_ID AND FLT_FOR_LTP_ID = REQ_CHD_LTP_ID '.
								'WHERE REQ_ONR_LTP_ID = ? AND REQ_ONR_ID = ?');
					if ($stm) {
						$stm->bind_param('ii',$RQLLinkType,$RQLID);
						if ($stm->execute()) {
					  		$stm->bind_result($Count);
					  		$stm->fetch();
					  		$stm->free_result();
						}
						else $RQRFailError .= 'Require fulfillments encountered an error getting requirement count ('.$stm->error.').<br />';
					}
					else $RQRFailError .= 'Require fulfillments encountered an error preparing to get requirement count ('.$sql->error.').<br />';
					
					if ($Count == 0)
						echo("<center><em>Record has no requirements</em></center><br />");
					else {
						$stm = $sql->prepare(
								'SELECT REQ_ID,LTP_NAME,REQ_FLT_ID,REQ_CHD_LTP_ID,FLT_NAME,REQ_FLM_ID,FLM_NAME,FLL_LEVEL,FLL_NAME,REQ_COUNT '.
								'FROM rqf_requirements '.
								'JOIN lnk_link_types ON LTP_ID = REQ_CHD_LTP_ID '.
								'LEFT OUTER JOIN rqf_fulfillment_types ON FLT_ID = REQ_FLT_ID '.
								'LEFT OUTER JOIN rqf_fulfillments ON FLM_ID = REQ_FLM_ID '.
								'LEFT OUTER JOIN rqf_fulfillment_levels ON FLL_ID = REQ_MIN_FLL_ID '.
								'WHERE REQ_ONR_LTP_ID = ? AND REQ_ONR_ID = ?');
						if ($stm) {					
							$stm->bind_param('ii',$RQLLinkType,$RQLID);
							if ($stm->execute()) {
								$stm->store_result();
								$stm->bind_result($REQID,$ForTypeName,$FLTID,$FLTForLTP,$FulfillTypeName,$FLMID,$FulfillName,$Level,$LevelName,$RCount);
			
								$ReqText = 'Requirement';
								if ($Count != 1)
									$ReqText .= 's';
								echo("<center>$Count $ReqText</center>".
									'<table><tr><th>For...</th><th>Type</th><th>Fulfillment</th><th>Min Level</th><th>Needs</th><th>Has</th><th>Status</th>');
								if ($CanMod)
									echo('<th>Edit</th>');
								if ($CanRem)
									echo('<th>Remove</th>');
								echo('</tr>');
								while ($stm->fetch()) {
									if (is_null($FLTID))
										$FulfillTypeName = 'Min Count';
									elseif ($FLTID == 0)
										$FulfillTypeName = 'Max Count';
									if (is_null($FulfillName))
										$FulfillName = 'N/A';
									if (is_null($LevelName))
										$LevelName = 'N/A';
									echo("<tr><td>$ForTypeName</td><td>$FulfillTypeName</td>".
										"<td>$FulfillName</td><td>$LevelName</td>");
									if ($RCount == 0)
										echo('<td>All</td>');
									else echo("<td>$RCount</td>");
									//XCal - Check the count of linked items or linked providers
									if (is_null($FLTID) or ($FLTID == 0)) { //XCal - Just a min or max link count needed
										$chk = $sql->prepare(
												'SELECT COUNT(LNK_CHD_ID) '.
												'FROM lnk_links '.
												'WHERE LNK_ONR_LTP_ID = ? AND LNK_ONR_ID = ? AND LNK_CHD_LTP_ID = ?');
										if ($chk) {
											$chk->bind_param('iii',$RQLLinkType,$RQLID,$FLTForLTP);
											if ($chk->execute()) {
												$chk->bind_result($FCount);
												$chk->fetch();
												$chk->free_result();
											}
											else $RQRFailError .= 'Require fulfillments failed checking link count ('.$chk->error.').<br />';
										}
										else $RQRFailError .= 'Require fulfillments failed preparing to check link count ('.$sql->error.').<br />';
									}
									else {
										//XCal - TODO - This still needs more to add support for ProTask status and Fulfillment Chains
										$chk = $sql->prepare(
												'SELECT COUNT(LNK_CHD_ID) '.
												'FROM lnk_links '.
												'JOIN rqf_fulfillment_providers ON FLP_FLT_ID = ? AND FLP_LINK_ID = LNK_CHD_ID AND FLP_FLM_ID = ? '.
												'LEFT OUTER JOIN rqf_fulfillment_levels ON FLL_ID = FLP_FLL_ID '.
												'WHERE LNK_ONR_LTP_ID = ? AND LNK_ONR_ID = ? AND LNK_CHD_LTP_ID = ? AND (? IS NULL OR FLL_LEVEL >= ?)');
										if ($chk) {
											$chk->bind_param('iiiiiii',$FLTID,$FLMID,$RQLLinkType,$RQLID,$FLTForLTP,$Level,$Level);
											if ($chk->execute()) {
												$chk->bind_result($FCount);
												$chk->fetch();
												$chk->free_result();
											}
											else $RQRFailError .= 'Require fulfillments failed checking fulfillment count ('.$sql->error.').<br />';
										}
										else $RQRFailError .= 'Require fulfillments failed preparing to check fulfillment count ('.$sql->error.').<br />';
									}
									
									echo("<td>$FCount</td>");
									if (is_null($FLTID) and ($FCount >= $RCount))								 
										$Status = 'Passed';
									elseif ((!is_null($FLTID)) and ($FLTID == 0) and ($FCount <= $RCount))
										$Status = 'Passed';
									elseif (($FLTID > 0) and ($FCount >= $RCount))
										$Status = 'Passed';
									else $Status = 'Failed';
									echo("<td>$Status</td>");
									
									if ($CanMod)
										echo('<td><input type="button" class="button" value="Edit" onclick="'.
												"AJAX('$RQLTarget','$RQRPath','$RQRKeys&M=M&REQ=$REQID');".
												'" /></td>');
									if ($CanRem)
										echo('<td><input type="button" class="button" value="Remove" onclick="'.
												"AJAX('$RQLTarget','$RQRPath','$RQRKeys&M=D&REQ=$REQID');".
												'" /></td>');
									echo('</tr>');
									//XCal - Buttons to show those who fulfill a requirement or could if not linked 
									echo('<tr><input type="button" class="button" value="Show" onclick="'.
											"AJAX('reqful_listdiv',''resources/reqful/reqful_list.php','');".
											'" /></tr>'.
											'<tr><input type="button" class="button" value="Find" onclick="'.
											"AJAX('reqful_listdiv',''resources/reqful/reqful_avail.php','');".
											'" /></tr>');
								}
								$stm->free_result();
								echo('</table>');
								echo('<div id="reqful_listdiv"></div>');
							}
							else $RQRFailError .= 'Require fulfillments failed to get provisions ('.$stm->error.').<br />';
						}
						else $RQRFailError .= 'Require fulfillments failed while preparing to get provisions ('.$sql->error.').<br />';
						
						//echo('<br />'); //XCal - Stops next and prior floating over next article
					}
				}
			}
			elseif ($REQSec['SearchView'] > 0)
				echo('You only have the right to view requirements on items your account is linked by, which does not include the requested item.');
			else echo('You do not have the required access right to view requirements.');
		}
		if (strlen($RQRFailError) > 0)
			echo('Sorry to trouble you, but there were some issues you may want to know about, '.$RQRFailError);
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Require Fulfillments Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$RQRDieError.
			'</article>');
}
?>
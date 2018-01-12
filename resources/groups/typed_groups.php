<?php
//XCal - We only want to pay attention to request variables if they're for the group types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'TGP')) {
	$For = 'TGP';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$TGPTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D|List(Default),New,Modify,Delete;;]
	if (isset($_REQUEST['M']))
		$TGPMode = $_REQUEST['M'];
	else $TGPMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$TGPSelTarget = $_REQUEST['ST'];
	else $TGPSelTarget = 'TGPSubDiv';
	
	if (isset($_REQUEST['ID']))
		$GRPID = $_REQUEST['ID'];
	else $GRPID = -1;
	if (isset($_REQUEST['TID']))
		$GTPID = $_REQUEST['TID'];
	if (isset($_REQUEST['ETID']))
		$TGPGTPID = $_REQUEST['ETID'];
	if (isset($_REQUEST['Name']))
		$TGPName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$TGPDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['Doc']))
		$TGPDoc = $_REQUEST['Doc'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
	
	}
elseif (isset($For) and ($For == 'TGP')) {
	if (isset($Target))
		$TGPTarget = $Target;
	if (isset($Mode))
		$TGPMode = $Mode;
	elseif (! isset($TGPMode))
		$TGPMode = 'L';
	if (isset($SelTarget))
		$TGPSelTarget = $SelTarget;
	else $TGPSelTarget = 'TGPSubDiv';
	
	if (isset($ID))
		$GRPID = $ID;
	elseif (! isset($GRPID))
		$GRPID = -1;
	if (isset($TID))
		$GTPID = $TID;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($TGPMode))
		$TGPMode = 'L';
	$GRPID = -1;
	if (! isset($TGPSelTarget))
		$TGPSelTarget = 'TGPSubDiv';
	unset($TGPGTPID);
	unset($TGPName);
	unset($TGPDesc);
	unset($TGPDoc);
	$Offset = 0;
	$Records = 5;
}
$TGPPath = 'resources/groups/typed_groups.php';
$TGPDieError = '';
$TGPFailError = '';
$TGPModes = array('L','N','M','D');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($GTPID))
	$TGPDieError .= 'Typed groups has not been passed a group type for which to display groups.<br />';
if (! isset($TGPTarget))
	$TGPDieError .= 'Typed groups has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($TGPMode,$TGPModes))
	$TGPDieError .= "Typed groups has been passed a mode ($TGPMode) which it does not support.<br />";

if ($TGPDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$GRPSec = GetAreaSecurity($sql, 2);
		//XCal - Check if we're in New/Modify/Delete mode
		if (in_array($TGPMode,array('N','M','D'))) {
			if ($GRPSec['NewMod'] > 0) {
				if ($GRPSec['NewMod'] == 2)
					$CanEdit = true;
				else $CanEdit = ItemLinked($sql,4,$GRPID);
				if ($CanEdit) {
					if (in_array($TGPMode,array('M','D')) and (!($GRPID > 0))) {
						$TGPFailError .= 'Typed groups was passed a modify or delete request without an ID specified. Changed to list mode.<br />';
						$TGPMode = 'L';
					}
					elseif ($TGPMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
						//$_SESSION['DebugStr'] .= 'Typed Groups: Delete Mode<br />';
						$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
						if ($stm) {
							$stm->execute();
							$stm->free_result();
					
							if ($stm->prepare('CALL sp_grp_rem_group(?,@Code,@Msg)')) {
								$stm->bind_param('i', $GRPID);
								if ($stm->execute()) {
									$stm->free_result();
					
									if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
										$stm->execute();
										$stm->bind_result($RCode,$RMsg);
										$stm->fetch();
										echo("Remove result: $RMsg<br />");
										$stm->free_result();
									}
									else $TGPFailError .= 'Typed groups encountered an error retrieving group removal results.<br />';
								}
								else echo('Remove attempt failed with error: '.$stm->error.'<br />');
							}
							else $TGPFailError .= 'Typed groups encountered an error preparing to request group removal.<br />';
						}
						else $TGPFailError .= 'Typed groups encountered an error initialising group removal.<br />';
						//XCal - Whether removal failed or succeeded we want to give the list view back
						$TGPMode = 'L';
					}
					else { //XCal - Either (N)ew or (M)odify	
						//XCal - If the Name has been passed in we're saving
						if (isset($TGPName)) {
							//$_SESSION['DebugStr'] .= 'Typed Groups: Save Mode<br />';
							if ($TGPMode == 'N') {
								$stm = $sql->prepare(
									'INSERT INTO grp_groups (GRP_GTP_ID,GRP_NAME,GRP_DESCRIPTION,GRP_DOCUMENT,GRP_UPDATED) '.
									'VALUES (?,?,?,?,current_timestamp())');
								if ($stm) {
									$stm->bind_param('isss',$TGPGTPID,$TGPName,$TGPDesc,$TGPDoc);
									$stm->execute();
									$stm->free_result();
									$TGPMode = 'L';
								}
								else $TGPFailError .= 'Typed groups encountered an error preparing to add the group.<br />';
							}
							else {
								$stm = $sql->prepare(
									'UPDATE grp_groups SET '.
									'GRP_GTP_ID = ?, GRP_NAME = ?, GRP_DESCRIPTION = ?, '.
									'GRP_DOCUMENT = ?, GRP_UPDATED = current_timestamp() '.
									'WHERE GRP_ID = ?');
								if ($stm) {
									$stm->bind_param('isssi',$TGPGTPID,$TGPName,$TGPDesc,$TGPDoc,$GRPID);
									$stm->execute();
									$stm->free_result();
									$TGPMode = 'L';
								}
								else $TGPFailError .= 'Typed groups encountered an error preparing to modify the group.<br />';
							}
						}				
						else { //XCal - Name not set, so we want the new/modify input screen
							//$_SESSION['DebugStr'] .= 'Typed Groups: New/Modify Mode<br />';			
							echo('<h4>');
							//XCal - We'll always want the group type selection list
							$r_gtypes = $sql->query('SELECT GTP_ID,GTP_NAME FROM grp_group_types WHERE GTP_SR = 0');
							if ($TGPMode == 'M') {
								echo('Edit');
								$stm = $sql->prepare(
									'SELECT GRP_GTP_ID,GRP_NAME,GRP_DESCRIPTION,GRP_DOCUMENT '.
									'FROM grp_groups WHERE GRP_ID = ?');
								if ($stm) {
									$stm->bind_param('i',$GRPID);
									$stm->execute();
									$stm->bind_result($TGPGTPID,$TGPName,$TGPDesc,$TGPDoc);
									$stm->fetch();
									$stm->free_result();
								}
								else $TGPFailError .= 'Typed groups encountered an error preparing to get values to modify ('.$sql->error.').<br />';						
							}
							else {
								echo('New');						
								$TGPGTPID = $GTPID;
								$TGPName = '';
								$TGPDesc = '';
								$TGPDoc = '';
							}
							echo(' Group</h4><br />'.
								'<input type="hidden" id="GRP_ID" value="'.$GRPID.'" />'.
								'<table>'.
								'<tr><td>'.
								'<table><tr><td class="fieldname">Type</td>'.
								'<td><select id="GRP_GTP_ID">');
							while ($row = $r_gtypes->fetch_assoc()) {
								echo('<option value="'.$row['GTP_ID'].'" ');
								if ($row['GTP_ID'] == $TGPGTPID)
									echo('selected');
								echo('>'.$row['GTP_NAME'].'</option>');
							}
							echo('</select></td></tr>'.
								'<tr><td class="fieldname">Name</td>'.				
								"<td><input id=\"GRP_NAME\" type=\"text\" value=\"$TGPName\" /></td>".
								'</tr></table></td>'.
								'<td><table><tr><td class="fieldname">Description</td>'.
								"<td><textarea id=\"GRP_DESC\" rows=\"5\" cols=\"45\">$TGPDesc</textarea></td></tr>".
								'</table></td></tr></table>'.
								'<h4>Group Document</h4><br />'.
								"<textarea id=\"GRP_DOCUMENT\" rows=\"10\" cols=\"80\">$TGPDoc</textarea><br />".
								'<input type="button" class="save" value="Save" onclick="'.
								"SaveGroup('$TGPTarget','$TGPMode',$GTPID);".
								'" />'.
								'<input type="button" class="cancel" value="Cancel" onclick="'.
								"AJAX('$TGPTarget','$TGPPath','For=TGP&T=$TGPTarget&TID=$GTPID');".
								'" /><br />');
						}
					}
				}
				else echo('You only have the right to modify groups you are linked by, which does not include this group.');
			}
			else echo('You do not have the required rights to add or modify groups.');
		}
		
		//XCal - If we're not editing or we have saved the details show the list view
		if ($TGPMode == 'L') {
			if ($GRPSec['SearchView'] > 0) {
				if ($GRPSec['SearchView'] == 2)
					$CanView = true;
				else $CanView = false;//ItemLinked($sql,4,$GRPID);
				if ($CanView) {
					//$_SESSION['DebugStr'] .= 'Typed Groups: List Mode<br />';
					$stm = $sql->prepare('SELECT GTP_NAME FROM grp_group_types WHERE GTP_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$GTPID);
						$stm->execute();
						$stm->bind_result($TypeName);
						$stm->fetch();
						$stm->free_result();
					}
					else $TypeName = '[Error!]';
					$stm = $sql->prepare('SELECT COUNT(GRP_ID) FROM grp_groups WHERE GRP_SR = 0 AND GRP_GTP_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$GTPID);
						$stm->execute();
						$stm->bind_result($Count);
						$stm->fetch();
						$stm->free_result();
					}
					else $Count = 0;
					
					echo("<h3>\"$TypeName\" Groups</h3><br />".
						'<input type="button" class="button" value="New Group" onclick="'.
						"AddEditGroup('$TGPTarget','$GTPID',0);".
						'" />'.
						'<br />');
					
					if ($Count == 0)
						echo('No groups of this type found to display.');
					else {
						$LowRec = $Offset+1;
						if ($Count < ($Offset+$Records))
							$HighRec = $Count;
						else $HighRec = $Offset+$Records;
						echo("Showing groups $LowRec to $HighRec of $Count.<br />");
						
						echo('<table><tr><th>Select</th><th>Group</th><th>Description</th><th>Updated</th>');		
						echo('<th>Edit</th><th>Remove</th>');
						echo('</tr>');		
						$stm = $sql->prepare(
							'SELECT GRP_ID,GRP_GTP_ID,GRP_NAME,GRP_DESCRIPTION,GRP_UPDATED '.
							'FROM grp_groups '.
							'WHERE GRP_SR = 0 AND GRP_GTP_ID = ? LIMIT ?,?');
						if ($stm) {
							$stm->bind_param('iii',$GTPID,$Offset,$Records);
							$stm->execute();
							$stm->bind_result($GRPID,$TGPGTPID,$TGPName,$TGPDesc,$TGPUpdated);
							while ($stm->fetch()) {
								echo('<tr>'.
										'<td><input type="button" class="button" value="Select" onclick="'.
										"AJAX('$TGPSelTarget','resources/groups/group_view.php','For=GPV&T=$TGPSelTarget&ID=$GRPID');".
										'" /></td>'.
										"<td>$TGPName</td><td>$TGPDesc</td><td>$TGPUpdated</td>");
								echo(	'<td><input class="button" type="button" value="Edit" onclick="'.
										"AddEditGroup('$TGPTarget',$TGPGTPID,$GRPID);".
										'" /></td>'.
										'<td><input class="button" type="button" value="Remove" onclick="'.
										"AJAX('$TGPTarget','$TGPPath','For=TGP&T=$TGPTarget&TID=$GTPID&ID=$GRPID&M=D');".
										'" /></td>');
								echo('</tr>');
							}				
						}
						else $TGPFailError .= 'Typed groups failed preparing to get a list of groups to display ('.$sql->error.').<br />';
			
						echo('</table>');
						
						if ($Offset > 0) {
							$PrevOffset = $Offset-$Records;
							echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
								"AJAX('$TGPTarget','$TGPPath','For=TGP&T=$TGPTarget&TID=$GTPID&RO=$PrevOffset&RC=$Records');".
								'" />');
						}
						
						if (($Offset + $Records) < $Count) {
							$NextOffset = $Offset+$Records;
							echo('<input class="nextbutton" type="button" value="Next" onclick="'.
								"AJAX('$TGPTarget','$TGPPath','For=TGP&T=$TGPTarget&TID=$GTPID&RO=$NextOffset&RC=$Records');".
								'" />');
						}
						echo('<br /><div id="TGPSubDiv"></div>');
					}
				}
				else echo('You only have the right to view groups which your account is linked by, which does not include group browsing.'); 
			}
			else echo('You do not have the required access rights to view groups.');
		}
		if (strlen($TGPFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$TGPFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Typed Groups Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$TGPDieError.
		'</article>');
}
?>
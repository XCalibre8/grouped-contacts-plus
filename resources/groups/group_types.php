<?php
//XCal - We only want to pay attention to request variables if they're for the group types page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'GTP')) {
	$For = 'GTP';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$GTPTarget = $_REQUEST['T'];
	//XCal - [XC:M|Mode:{L},N,M,D,C|List(Default),New,Modify,Delete,Configure(Changes Select Target and enables N,M,D);;]
	if (isset($_REQUEST['M']))
		$GTPMode = $_REQUEST['M'];
	else $GTPMode = 'L';
	//XCal - TODO For this to truly work it needs to be passed in all AJAX calls
	if (isset($_REQUEST['ST']))
		$GTPSelTarget = $_REQUEST['ST'];
	else $GTPSelTarget = 'GTPSubDiv';
	
	if (isset($_REQUEST['ID']))
		$GTPID = $_REQUEST['ID'];
	else $GTPID = -1;
	if (isset($_REQUEST['Name']))
		$GTPName = $_REQUEST['Name'];
	if (isset($_REQUEST['Desc']))
		$GTPDesc = $_REQUEST['Desc'];
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;		
	}
elseif (isset($For) and ($For == 'GTP')) {
	if (isset($Target))
		$GTPTarget = $Target;
	if (isset($Mode))
		$GTPMode = $Mode;
	elseif (! isset($GTPMode))
		$GTPMode = 'L';
	if (isset($SelTarget))
		$GTPSelTarget = $SelTarget;
	else $GTPSelTarget = 'GTPSubDiv';
	
	if (isset($ID))
		$GTPID = $ID;
	elseif (! isset($GTPID))
	$GTPID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	if (! isset($GTPMode))
		$GTPMode = 'L';
	$GTPID = -1;
	if (! isset($GTPSelTarget))
		$GTPSelTarget = 'GTPSubDiv';
	unset($GTPName);
	unset($GTPDesc);
	$Offset = 0;
	$Records = 5;
}

$GTPPath = 'resources/groups/group_types.php';
$GTPDieError = '';
$GTPFailError = '';
$GTPModes = array('L','N','M','D','C');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($GTPTarget))
	$GTPDieError .= 'Group types has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($GTPMode,$GTPModes))
	$GTPDieError .= "Group types has been passed a mode ($GTPMode) which is does not support.<br />";

if ($GTPDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$GRPSec = GetAreaSecurity($sql, 2);		
		//XCal - Check if we're in New/Modify/Delete mode
		if (in_array($GTPMode,array('N','M','D'))) {
			if ($GRPSec['Config']) {
				//XCal - Make sure we have an ID if we're modifying or deleting
				if (in_array($GTPMode,array('M','D')) and (!($GTPID > 0))) {
					$GTPFailError .= 'Group types was passed a modify or delete request without an ID specified. Changed to select mode.<br />';
					$GTPMode = 'C'; //XCal - We can only do these actions on group types from configuration list mode, so we go to that not L
				}
				elseif ($GTPMode == 'D') { //XCal - Perform the 'delete' and return to config list mode
					//$_SESSION['DebugStr'] .= 'Group Types: Delete Mode<br />';
					$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
					if ($stm) {
						$stm->execute();
						$stm->free_result();
							
						if ($stm->prepare('CALL sp_grp_rem_group_type(?,@Code,@Msg)')) {
							$stm->bind_param('i', $GTPID);						
							if ($stm->execute()) {
								$stm->free_result();
					
								if ($stm->prepare('SELECT @Code Code,@Msg Msg')) {
									$stm->execute();
									$stm->bind_result($RCode,$RMsg);
									$stm->fetch();
									echo("Remove result: $RMsg<br />");
									$stm->free_result();							
								}
								else $GTPFailError .= 'Group types encountered an error retrieving type removal results.<br />';
							}
							else echo('Remove attempt failed with error: '.$stm->error.'<br />');
						}
						else $GTPFailError .= 'Group types encountered an error preparing to request type removal.<br />';
					}
					else $GTPFailError .= 'Group types encountered an error initialising type removal.<br />';
					//XCal - Whether removal failed or succeeded we want to give the config list view back
					$GTPMode = 'C';
				}
				else { //XCal - Either (N)ew or (M)odify
					//XCal - If the Name has been passed in we're saving
					if (isset($GTPName)) {
						//$_SESSION['DebugStr'] .= 'Group Types: Save Mode<br />';
						if ($GTPMode == 'N') {
							$stm = $sql->prepare(
								'INSERT INTO grp_group_types (GTP_NAME,GTP_DESCRIPTION,GTP_UPDATED) '.
								'VALUES (?,?,current_timestamp())');
							if ($stm) {
								$stm->bind_param('ss',$GTPName,$GTPDesc);
								$stm->execute();
								$stm->free_result();
								$GTPMode = 'C';
							}
							else $GTPFailError .= 'Group types encountered an error preparing to add the type.<br />';
						}
						else {
							$stm = $sql->prepare(
									'UPDATE grp_group_types SET '.
									'GTP_NAME = ?, GTP_DESCRIPTION = ?, GTP_UPDATED = current_timestamp() '.
									'WHERE GTP_ID = ?');							
							if ($stm) {
								$stm->bind_param('ssi',$GTPName,$GTPDesc,$GTPID);
								$stm->execute();
								$stm->free_result();
								$GTPMode = 'C';
							}
							else $GTPFailError .= 'Group types encountered an error preparing to modify the type.<br />';
						}
					}
					else { //XCal - Name not set, so we want the new/modify input screen
						//$_SESSION['DebugStr'] .= 'Group Types: New/Modify Mode<br />';						
						echo('<h4>');
						if ($GTPMode == 'M') {
							echo('Edit');
							$stm = $sql->prepare('SELECT GTP_NAME,GTP_DESCRIPTION FROM grp_group_types WHERE GTP_ID = ?');
							if ($stm) {
								$stm->bind_param('i',$GTPID);
								$stm->execute();
								$stm->bind_result($GTPName,$GTPDesc);
								$stm->fetch();
								$stm->free_result();
							}
							else $GTPFailError .= 'Group types encountered an error preparing to get values to modify ('.$sql->error.').<br />';
						}
						else {
							echo('New');
							$GTPName = '';
							$GTPDesc = '';
						}
						echo(' Group Type</h4><br />'.
							'<input type="hidden" id="GTP_ID" value="'.$GTPID.'" />'.
							'<table>'.
							'<tr><td class="fieldname">Name</td>'.
							"<td><input id=\"GTP_NAME\" type=\"text\" value=\"$GTPName\" /></td></tr>".
							'<tr><td class="fieldname">Description</td>'.
							"<td><textarea id=\"GTP_DESC\" rows=\"5\" cols=\"50\">$GTPDesc</textarea></td></tr></table>".
							'<input type="button" class="save" value="Save" onclick="'.
							"SaveGroupType('$GTPTarget','$GTPMode');".
							'" />'.
							'<input type="button" class="cancel" value="Cancel" onclick="'.
							"AJAX('$GTPTarget','$GTPPath','For=GTP&T=$GTPTarget&M=C');".
							'" /><br />');
					}
				}
			}
			else echo('You do not have the required access rights to configure the group system.');
		}
		
		//XCal - We're not editing or we have saved the details so show the list view
		if (in_array($GTPMode,array('L','C'))) {
			if ($GTPMode == 'C')
				$CanView = $GRPSec['Config'];
			else $CanView = $GRPSec['SearchView'] == 2;
			
			if ($CanView) {
				//$_SESSION['DebugStr'] .= 'Group Types: List Mode<br />';
				$res = $sql->query('SELECT COUNT(GTP_ID) FROM grp_group_types WHERE GTP_SR = 0');
				if (! $res) {
					$GTPFailError .= 'Group types failed to get a count of types ('.$sql->error.').<br />';
					$Count = 0;
				}
				else {
					$row = $res->fetch_array();
					$Count = $row[0];
				}
				if ($GTPMode == 'C') 
					echo('<input type="button" class="button" value="New Group Type" onclick="'.
						"AJAX('$GTPTarget','$GTPPath','For=GTP&T=$GTPTarget&M=N');".
						'" /><br />');
				$LowRec = $Offset+1;
				if ($Count < ($Offset+$Records))
					$HighRec = $Count;
				else $HighRec = $Offset+$Records;
				echo("Showing group types $LowRec to $HighRec of $Count.<br />");
						
				echo('<table><tr><th>Select</th><th>Type</th><th>Description</th><th>Updated</th>');
				if ($GTPMode == 'C')
					echo('<th>Edit</th><th>Remove</th>');
				echo('</tr>');
				$stm = $sql->prepare(
					'SELECT GTP_ID,GTP_NAME,GTP_DESCRIPTION,GTP_UPDATED '.
					'FROM grp_group_types '.
					'WHERE GTP_SR = 0 LIMIT ?,?');
				if ($stm) {
					$stm->bind_param('ii',$Offset,$Records);
					$stm->execute();
					$stm->bind_result($GTPID,$GTPName,$GTPDesc,$GTPUpdated);
					while ($stm->fetch()) {
						echo('<tr>'.
								'<td><input type="button" class="button" value="Select" onclick="');
						//XCal - The select button needs to bring up a different page if we're configuring to not
						if ($GTPMode == 'C')
							echo("AJAX('$GTPSelTarget','resources/groups/group_type_link_avail.php','For=GTA&T=$GTPSelTarget&ID=$GTPID');");
						else echo("AJAX('$GTPSelTarget','resources/groups/typed_groups.php','For=TGP&T=$GTPSelTarget&TID=$GTPID');");
						echo('" /></td>'.
								"<td>$GTPName</td><td>$GTPDesc</td><td>$GTPUpdated</td>");
						if ($GTPMode == 'C')
							echo('<td><input class="button" type="button" value="Edit" onclick="'.
								"AJAX('$GTPTarget','$GTPPath','For=GTP&T=$GTPTarget&M=M&ID=$GTPID');".
								'" /></td>'.
								'<td><input class="button" type="button" value="Remove" onclick="'.
								"AJAX('$GTPTarget','$GTPPath','For=GTP&T=$GTPTarget&M=D&ID=$GTPID');".
								'" /></td>');
						echo('</tr>');
					}	
				}
				else $GTPFailError .= 'Group types failed preparing to get a list of types to display ('.$sql->error.').<br />';
				
				echo('</table>');
				
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
						"AJAX('$GTPTarget','$GTPPath','For=GTP&T=$GTPTarget&M=$GTPMode&RO=$PrevOffset&RC=$Records');".
						'" />');
				}
				
				if (($Offset + $Records) < $Count) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="'.
						"AJAX('$GTPTarget','$GTPPath','For=GTP&T=$GTPTarget&M=$GTPMode&RO=$NextOffset&RC=$Records');".
						'" />');
				}			
				echo('<br /><div id="GTPSubDiv"></div>');
			}
			elseif ($GTPMode == 'C')
				echo('You do not have the required access rights to configure the group system');
			elseif ($GRPSec['SearchView'] == 1)
				echo('You only have the right to view groups which your account is linked by, which does not include browsing groups.');
			else echo('You do not have the required access rights to view groups.');
		}
		if (strlen($GTPFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$GTPFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Group Types Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$GTPDieError.
		'</article>');
}
?>
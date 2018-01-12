<?php
function DescribeAccess($Code) {
	switch ($Code) {
		case 0:
			return 'Deny';
		break;
		
		case 1:
			return 'Linked';
		break;
		
		case 2:
			return 'Full';
		break;
		
		default:
			return 'Unknown';
		break;
	}
}
function AccessSelector($ID,$AccessLevel) {
	echo('<select id="'.$ID.'">'.
		'<option value="0"');
	if ($AccessLevel == 0)
		echo(' selected');
	echo('>Deny</option>'.
		'<option value="1"');
	if ($AccessLevel == 1)
		echo(' selected');
	echo('>Linked</option>'.
		'<option value="2"');
	if ($AccessLevel == 2)
		echo(' selected');
	echo('>Full</option></select>');
	
}
//XCal - We only want to pay attention to the right request variables
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'RAC')) {
	$For = 'RAC';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$RACTarget = $_REQUEST['T'];
	
	if (isset($_REQUEST['ID']))
		$RACID = $_REQUEST['ID'];
	else $RACID = -1;
	if (isset($_REQUEST['ACA']))
		$RACACAID = $_REQUEST['ACA'];
	if (isset($_REQUEST['A'])) {
		$AddRAC = $_REQUEST['A'];
		$ModRAC = $AddRAC;
	}
	if (isset($_REQUEST['M']))
		$ModRAC = $_REQUEST['M'];
	if (isset($_REQUEST['D']))
		$DelRAC = $_REQUEST['D'];
	if (isset($_REQUEST['SVL']))
		$RACSearchViewLevel = $_REQUEST['SVL'];
	if (isset($_REQUEST['NML']))
		$RACNewModLevel = $_REQUEST['NML'];
	if (isset($_REQUEST['RML']))
		$RACRemoveLevel = $_REQUEST['RML'];
	if (isset($_REQUEST['CFG']))
		$RACConfig = $_REQUEST['CFG'] == 1;

	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 5;
}
elseif (isset($For) and ($For == 'RAC')) {
	if (isset($Target))
		$RACTarget = $Target;
		
	if (isset($ID))
		$RACID = $ID;
	elseif (! isset($RACID))
	$RACID = -1;
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 5;
}
else { //XCal - If we've had no specific setup in request or variables [initialise:UK;initialize:US] defaults
	$RACID = -1;
	$Offset = 0;
	$Records = 5;
}

$RACPath = 'resources/users/role_access.php';
$RACDieError = '';
$RACFailError = '';

//XCal - Let's make sure we have the values we need to run the page
if (! isset($RACTarget))
	$RACDieError .= 'User role access has not been told where to target its displays and thus would not be safe to run.<br />';
if ($RACID == -1)
	$RACDieError .= "User role access has not been told what role to work with.<br />";

if ($RACDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$USRSec = GetAreaSecurity($sql, 6);
		if ($USRSec['Config']) {
			//XCal - If an access area removal has been requested then remove it
			if (isset($DelRAC)) {
				$stm = $sql->prepare(
					'DELETE FROM usr_role_access '.
					'WHERE RAC_ROL_ID = ? AND RAC_ACA_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$RACID,$DelRAC);
					if ($stm->execute())					
						$stm->free_result();
					else echo('Remove failed, message: '.$stm->error);
				}
				else $RACFailError .= 'User role access encountered an error preparing to remove the access area.<br />';
			}
			//XCal - If an access area is being added then add it
			if (isset($AddRAC)) {
				$stm = $sql->prepare(
					'INSERT INTO usr_role_access (RAC_ROL_ID,RAC_ACA_ID,RAC_UPDATED) '.
					'VALUES (?,?,current_timestamp())');
				if ($stm) {
					$stm->bind_param('ii',$RACID,$AddRAC);
					if ($stm->execute())
						$stm->free_result();
					else echo('Add failed, message: '.$stm->error);
				}
				else $RACFailError .= 'User role access encountered an error preparing to add the access area.<br />'; 
			}
			//XCal - If an access area is being modified and has passed values update and return to list mode
			if (isset($ModRAC) and isset($RACSearchViewLevel)) {
				$stm = $sql->prepare('UPDATE usr_role_access SET '.
						'RAC_SEARCHVIEW = ?,'.
						'RAC_NEWMOD = ?,'.
						'RAC_REMOVE = ?,'.
						'RAC_CONFIG = ? '.
						'WHERE RAC_ROL_ID = ? AND RAC_ACA_ID = ?');
				if ($stm) {
					$stm->bind_param('iiiiii',$RACSearchViewLevel,$RACNewModLevel,$RACRemoveLevel,$RACConfig,$RACID,$ModRAC);
					if ($stm->execute()) {
						$stm->free_result();
						unset($ModRAC);
					}
					else $RACFailError .= 'User role access encountered an error modifying the access area.<br />';
				}
				else $RACFailError .= 'User role access encountered an error preparing to modify the access area.<br />';		
			}
			
			//XCal - Get the name and last updated date of the role
			$s_type = $sql->prepare('SELECT ROL_NAME, ROL_UPDATED FROM usr_roles WHERE ROL_ID = ?');
			if ($s_type) {
				$s_type->bind_param('i',$RACID);
				if ($s_type->execute()) {
					$s_type->bind_result($ROLName,$ROLUpdated);
					$s_type->fetch();
					$s_type->free_result();
				}
				else $RACFailError .= 'User role access encountered an error getting role details.<br />';
			}
			else $RACFailError .= 'User role access encountered an error preparing to get role details.<br />';
			
			echo('<article>'.
					"<h3>\"$ROLName\" Configuration</h3> Role last updated $ROLUpdated".
					'<p>You can set the access levels of users with this role by area as well as their default access level.</p>');
			
			if (isset($ModRAC)) {
				$stm = $sql->prepare(
						'SELECT ACA_NAME,RAC_SEARCHVIEW,RAC_NEWMOD,RAC_REMOVE,RAC_CONFIG '.
						'FROM usr_role_access '.
						'JOIN usr_access_areas ON ACA_ID = RAC_ACA_ID '.
						'WHERE RAC_ROL_ID = ? AND RAC_ACA_ID = ?');
				if ($stm) {
					$stm->bind_param('ii',$RACID,$ModRAC);
					if ($stm->execute()) {
						$stm->bind_result($ACAName,$RACSearchView,$RACNewMod,$RACRemove,$RACConfig);
						$stm->fetch();
						$stm->free_result();
					}
					else $RACFailError .= 'User role access encountered an error getting access levels.<br />';
				}
				else $RACFailError .= 'User role access encountered an error preparing to get access levels.<br />';
				echo("<p>Specify $ACAName access for the $ROLName role.</p>".
					'<input type="hidden" id="RAC_ROL_ID" value="'.$RACID.'" />'.
					'<input type="hidden" id="RAC_ACA_ID" value="'.$ModRAC.'" />'.
					'<table><tr><td class="fieldname">Search/View</td><td>');
				AccessSelector('RAC_SEARCHVIEW', $RACSearchView);
				echo('</td></tr><tr><td class="fieldname">New/Modify</td><td>');
				AccessSelector('RAC_NEWMOD', $RACNewMod);
				echo('</td></tr><tr><td class="fieldname">Remove</td><td>');
				AccessSelector('RAC_REMOVE', $RACRemove);
				echo('</td></tr><tr><td class="fieldname">Configure</td>'.
					'<td><select id="RAC_CONFIG">'.
					'<option value="0"');
				if ($RACConfig != 1)
					echo(' selected');
				echo('>No</option><option value="1"');
				if ($RACConfig == 1)
					echo(' selected');
				echo('>Yes</option></select></td></tr></table>'.
					'<input type="button" class="save" value="Save" onclick="'.
					"SaveRoleAccess('$RACTarget');".
					'" />'.
					'<input type="button" class="cancel" value="Cancel" onclick="'.
					"AJAX('$RACTarget','$RACPath','For=RAC&T=$RACTarget&ID=$RACID');".
					'" /><br />');
			}
			else {
				echo('<table>'.
						'<tr><th>Area</th><th>Description</th><th>Access</th><th>Rights</th><th>Edit</th></tr>');
			
				//XCal - Echo the areas and their settings
				$Count = 0;
				$s_access = $sql->prepare(
						'SELECT ACA_ID,ACA_NAME,ACA_DESCRIPTION,RAC_SEARCHVIEW,RAC_NEWMOD,RAC_REMOVE,RAC_CONFIG '.
						'FROM usr_access_areas '.
						'LEFT OUTER JOIN usr_role_access ON RAC_ROL_ID = ? AND RAC_ACA_ID = ACA_ID '.			
						'ORDER BY ACA_ID');
				if ($s_access) {
					$s_access->bind_param('i',$RACID);
					$s_access->execute();
					$s_access->bind_result($ACAID,$ACAName,$ACADesc,$RACSearchView,$RACNewMod,$RACRemove,$RACConfig);
					while ($s_access->fetch()) {
						$Count++;
						echo("<tr><td>$ACAName</td><td>$ACADesc</td>");
						if (is_null($RACSearchView)) 
							echo('<td><input type="button" class="button" value="Specify" onclick="'.
								"AJAX('$RACTarget','$RACPath','For=RAC&T=$RACTarget&ID=$RACID&A=$ACAID');".
								'" /></td><td>Search/View:&nbsp;Default<br />New/Modify:&nbsp;Default<br />Remove:&nbsp;Default<br />Config:&nbsp;Default</td><td>N/A</td></tr>');
						else {
							echo('<td><input type="button" class="button" value="Remove" onclick="'.			
								"AJAX('$RACTarget','$RACPath','For=RAC&T=$RACTarget&ID=$RACID&D=$ACAID');".
								'" /></td><td>Search/View:&nbsp;'.DescribeAccess($RACSearchView).
								'<br />New/Modify:&nbsp;'.DescribeAccess($RACNewMod).
								'<br />Remove:&nbsp;'.DescribeAccess($RACRemove).'<br />');
							if ($RACConfig == 1)
								echo('Configure:&nbsp;Yes</td>');
							else echo('Configure:&nbsp;No</td>');
							echo('<td><input type="button" class="button" value="Edit" onclick="'.
								"AJAX('$RACTarget','$RACPath','For=RAC&T=$RACTarget&ID=$RACID&M=ACAID');".
								'" /></td></tr>');
						}
					}
					$s_access->free_result();
				}		
				echo('</table>');
			}
			echo('</article>');
			//$_SESSION['DebugStr'] .= "Schedule Type Link Availability: Failure messages below.<br />$RACFailError";
			if (strlen($RACFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$RACFailError");
		}
		else echo('You do not have the required access right to configure the user system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>User Role Access Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$RACDieError.
		'</article>');
}			
?>
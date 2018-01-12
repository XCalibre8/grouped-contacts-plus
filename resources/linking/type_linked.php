<?php
require(dirname(__FILE__).'/../gen_session.php');
//XCal - We only want to pay attention to request variables if they're for the all links view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'TPD')) {
	$For = 'TPD';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$TPDTarget = $_REQUEST['T'];

	//XCal - [XC:OLT|Owner Link Type: The owner link type ID;;]
	if (isset($_REQUEST['OLT']))
		$ONRLTPID = $_REQUEST['OLT'];
	if (isset($_REQUEST['ID']))
		$CHDID = $_REQUEST['ID'];
	//XCal - [XC:CLT|Child Link Type: The child link type ID;;]
	if (isset($_REQUEST['CLT']))
		$CHDLTPID = $_REQUEST['CLT'];
	//XCal - These are the "eXtra" fields on the link table
	if (isset($_REQUEST['XID']))
		$TPDXID = $_REQUEST['XID'];
	if (isset($_REQUEST['XSTR']))
		$TPDXSTR = $_REQUEST['XSTR'];		
	//XCal - Supported values [XC:M|Mode:{L},M,|List(Default),Modify;]
	if (isset($_REQUEST['M']))
		$TPDMode = $_REQUEST['M'];
	elseif (! isset($TPDMode))
		$TPDMode = 'L';
	if (isset($_REQUEST['S']))
		$TPDSearch = $_REQUEST['S'];
	elseif (! isset($TPDSearch))
		$TPDSearch = '';
	//XCal - Supported values [XC:SM|SearchMode:{C},B,L|Contains,Begins,Exact;]
	if (isset($_REQUEST['SM']))
		$TPDSMode = $_REQUEST['SM'];
	elseif (! isset($TPDSMode))
		$TPDSMode = 'C';
	//XCal - Variables that are reset if they aren't passed in
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 10;		
	//XCal - [XC:LID|Link ID:The subject link ID for Modify;]
	if (isset($_REQUEST['LID']))
		$TPDLinkID = $_REQUEST['LID'];
}
elseif (isset($For) and ($For == 'TPD')) {
	if (isset($Target))
		$TPDTarget = $Target;
	
	if (isset($OLT))
		$ONRLTPID = $OLT;
	if (isset($CLT))
		$CHDLTPID = $CLT;
	if (isset($ID))
		$CHDID = $ID;	
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 10;
}
else {			
	$Offset = 0;
	$Records = 10;
	$TPDMultiLink = true;
}
if (! isset($TPDMode))
	$TPDMode = 'L';
if (! isset($TPDSMode))
	$TPDSMode = 'C';

//XCal - If these aren't set then we'll need to pass NULL to the stored proc
if (! isset($TPDXID))
	$TPDXID = null;
if (! isset($TPDXSTR))
	$TPDXSTR = null;
if (! isset($ExID))
	$ExID = 0;
$TPDPath = 'resources/linking/type_linked.php';
$TPDDieError = '';
$TPDFailError = '';
$TPDModes = array('L','M');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($ONRLTPID))
	$TPDDieError .= 'Type linked view has not been passed the owner link type identifier.<br />';
if (! isset($CHDID))
	$TPDDieError .= 'Type linked view has not been passed the child identifier to display who it\'2 linked by.<br />';
if (! isset($CHDLTPID))
	$TPDDieError .= 'Type linked view has not been passed the child link type identifier.<br />';
if (! isset($TPDTarget))
	$TPDDieError .= 'Type linked view has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($TPDMode,$TPDModes))
	$TPDDieError .= "Type linked view has been passed a mode ($TPDMode) which it does not support.<br />";
if ((! $TPDDieError == '') and (isset($For) and ($For <> 'TPD')))
	$TPDDieError .= "These problems might be because params are marked as for '$For' and need to be for 'TPD'.<br />";
	

if ($TPDDieError == '') {
	$TPDKeys = "For=TPD&T=$TPDTarget&OLT=$ONRLTPID&ID=$CHDID&CLT=$CHDLTPID";
	//$_SESSION['DebugStr'] .= "Type Linked: Running with keys $TPDKeys<br />";
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		CheckWebAccount($sql);
		//XCal - This gets us the dynamic data we need for the link type
		$stm = $sql->prepare('SELECT LTP_NAME,LTP_TABLE,LTP_ID_FIELD '.
				'FROM lnk_link_types '.
				'WHERE LTP_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$ONRLTPID);
			if ($stm->execute()) {
				$stm->bind_result($LTPName,$LTPTable,$LTPIDField);
				$stm->fetch();
				$stm->free_result();
			}
			else $TPDFailError .= 'Type linked view failed getting link type details ('.$stm->error.').<br />';
		}
		//XCal - We don't really want to keep storing our queries in the tables, so lets determine selection here
		switch ($ONRLTPID) {
			case 1: //Linking People
				$LTPTitles = 'Full Name';
				$LTPSelect = 'fn_con_fullname(PER_ID)';
			break;
			
			case 2: //Linking Addresses
				$LTPTitles = 'Full Address';
				$LTPSelect = 'fn_con_sep_lnk_address(LNK_ONR_LTP_ID,LNK_ONR_ID,ADR_ID,\', \')';
			break;
			
			case 3: //Linking ContactPoints
				$LTPTitles = 'Contact';
				$LTPSelect = 'fn_con_full_link_contact_point(LNK_ONR_LTP_ID,LNK_ONR_ID,CNP_ID)';
			break;
			
			case 4: //Linking Groups
				$LTPTitles = 'Group,Description';
				$LTPSelect = 'GRP_NAME,GRP_DESCRIPTION';
			break;
			
			case 5: //Linking Projects/Tasks
				$LTPTitles = 'Title,Description';
				$LTPSelect = 'TSK_TITLE,TSK_DESCRIPTION';
				if (! in_array($CHDLTPID,array(2,3))) { //XCal - Addresses and ContactPoints always need contact type and care of/speak to, otherwise show protask state
					$LTPTitles .= ',Status';
					$LTPSelect .= ',fn_pro_state_name(LNK_X_ID)';
				}
			break;
			
			case 6: //Linking Web Accounts
				$LTPTitles = 'User Name,Active?';
				$LTPSelect = 'WAC_USERNAME,WAC_ACTIVE';
			break;
			
			case 7: //Linking Self Aware Systems
				$LTPTitles = 'Name,Description';
				$LTPSelect = 'SYS_NAME,SYS_DESCRIPTION';
			break;
			
			case 8: //Linking Schedule Items
				$LTPTitles = 'Type,Start,End';
				$LTPSelect = 'fn_sch_schedule_type_name(SCI_SCT_ID),SCI_START,SCI_END';
			break;
			
			case 9: //Linking Users
				$LTPTitles = 'Username';
				$LTPSelect = 'USR_USERNAME';
			break;
			
			default:
				$TPDFailError .= 'Type linked view does not support the type it\'s been requested to link!<br />';
			break;
		}
		
		if (isset($LTPTitles) and in_array($CHDLTPID,array(2,3))) {
			$LTPTitles .= ',Type';
			if ($CHDLTPID == 2)
				$LTPTitles .= ',Care Of';
			else $LTPTitles .= ',Speak To';
			$LTPSelect .= ',fn_con_contact_type_name(LNK_X_ID),LNK_X_STR';
		}
		
		//XCal - Let's check if we're in New/Modify mode
		if ($TPDMode == 'M') {
			//XCal - If we're modifying we require an ID
			if (!(isset($TPDLinkID) and ($TPDLinkID > 0))) {
				$TPDFailError .= 'Type linked was passed a modify request without an ID specified. Changing to list mode.<br />';
				$TPDMode = 'L';
			}
			else { //XCal - (M)odify
				//XCal - OK then, let's just check the type and call the relevant linkmod unit
				switch ($ONRLTPID) {
	// 				case 1: //XCal - Person
	// 					require(dirname(__FILE__).'/../contacts/person_linkedmod.php');
	// 				break;
	// 				case 2: //XCal - Address
	// 					require(dirname(__FILE__).'/../contacts/address_linkedmod.php');
	// 				break;
	// 				case 3: //XCal - Contact Point
	// 					require(dirname(__FILE__).'/../contacts/contactpoint_linkedmod.php');
	// 				break;
	// 				case 4: //XCal - Group
	// 					require(dirname(__FILE__).'/../groups/group_linkedmod.php');
	// 				break;
					case 5: //XCal - Project/Task
						require(dirname(__FILE__).'/../protasks/protask_linkedmod.php');
					break;							
					default:
						echo('<p>The type linked module has attempted to add or modify an unsupported type!</p>');
						$TPDMode = 'L';
					break;
				}
			}
		}
		
		//XCal - If we're not modifying or the action's done we need to run the search/list
		if ($TPDMode == 'L') {
			$CQuery = 'SELECT COUNT(LNK_ONR_LTP_ID) '.
					'FROM lnk_links '.				
					'WHERE LNK_ONR_LTP_ID = ? '.
					'AND LNK_CHD_ID = ? '.
					'AND LNK_CHD_LTP_ID = ?';		
			//XCal - TODO - We should really have a way of checking the SR indicator
			$SQuery = "SELECT $LTPIDField,$LTPSelect ".
				'FROM lnk_links '.
				"JOIN $LTPTable ON $LTPIDField = LNK_ONR_ID ".
				'WHERE LNK_ONR_LTP_ID = ? '.
				'AND LNK_CHD_ID = ? '.
				'AND LNK_CHD_LTP_ID = ?';
			if ($ONRLTPID == 5) {//XCal - Only show incomplete tasks
				$CQuery .= ' AND fn_pro_state_complete(LNK_X_ID) = 0';
				$SQuery .= ' AND fn_pro_state_complete(LNK_X_ID) = 0';
			}
			$SQuery .= ' LIMIT ?,?';
			
			$stm = $sql->prepare($CQuery);
			if ($stm) {
				$stm->bind_param('iii',$ONRLTPID,$CHDID,$CHDLTPID);
				//$_SESSION['DebugStr'] .= "Type Linked: Querying link count with ONR_LTP_ID = $ONRLTPID, CHD_ID = $CHDID and CHD_LTP_ID = $CHDLTPID.<br />";
				if ($stm->execute()) {
					$stm->bind_result($Count);
					$stm->fetch();
					$stm->free_result();
				}
				else $TPDFailError .= 'Type linked view failed querying link count ('.$stm->error.').<br />';
			}
			else $TPDFailError .= 'Type linked view failed preparing to count typed links ('.$sql->error.').<br />';
			
			if ($Count == 0)
				echo("<center><em>No linking $LTPName found.</em></center><br />");
			else {
				//XCal - This supporting fixed numbers of result fields is a total fiddle while I look up how to bind a dynamic field list to an array
				$LTPTitArr = str_getcsv($LTPTitles,',');
				$FCount = sizeof($LTPTitArr);
				//$_SESSION['DebugStr'] .= "Type Linked: Preparing to run query: $SQuery<br />";
			
				$stm->prepare($SQuery);
				if ($stm) {					
					$stm->bind_param('iiiii',$ONRLTPID,$CHDID,$CHDLTPID,$Offset,$Records);
						
					if ($stm->execute()) {
						switch ($FCount) {
							case 1:
								$stm->bind_result($TPDLinkID,$LRes[0]);
								break;
							case 2:
								$stm->bind_result($TPDLinkID,$LRes[0],$LRes[1]);
								break;
							case 3:
								$stm->bind_result($TPDLinkID,$LRes[0],$LRes[1],$LRes[2]);
								break;
							case 4:
								$stm->bind_result($TPDLinkID,$LRes[0],$LRes[1],$LRes[2],$LRes[3]);
								break;
							case 5:
								$stm->bind_result($TPDLinkID,$LRes[0],$LRes[1],$LRes[2],$LRes[3],$LRes[4]);
								break;
							default:
								$TPDFailError .= 'Type linked supports displaying between 1 and 5 fields, the request is outside that range!<br />';
								break;
						}
	
						$LowRec = $Offset+1;
						if ($Offset+$Records > $Count)
							$HighRec = $Count;
						else $HighRec = $Offset+$Records;						
						echo("<center>Linking $LTPName: $LowRec-$HighRec of $Count</center>");
						echo('<table><tr>');
						
						foreach ($LTPTitArr as $LTPTitle)
							echo("<th>$LTPTitle</th>");
						//XCal - Only show edit on linked for types that support it
						if (in_array($ONRLTPID,array(5))) {
							if ($ONRLTPID == 5) {
								$TSKSec = GetAreaSecurity($sql,3);								
								$MightEdit = ($TSKSec['NewMod'] > 0)
										or (($CHDLTPID == 6) and ($CHDID == $_SESSION['WebAccID']))
										or (($CHDLTPID == 1) and ($CHDID == $_SESSION['WACPERID']));
							}
							else $MightEdit = false;
							if ($MightEdit)
								echo('<th>Edit</th>');
						}
						echo('</tr>');
						while ($stm->fetch()) {
							echo('<tr>');
							for ($i = 0; $i < $FCount; $i++) {
								if ($i == 0)
									switch ($ONRLTPID) {
										case 1:
											echo("<td><a href=\"contacts.php?V=P&ID=$TPDLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 2:
											echo("<td><a href=\"contacts.php?V=A&ID=$TPDLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 3:
											echo("<td><a href=\"contacts.php?V=C&ID=$TPDLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 4:
											echo("<td><a href=\"groups.php?ID=$TPDLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 5:
											echo("<td><a href=\"protasks.php?ID=$TPDLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 6:
											echo("<td><a href=\"account.php?ID=$TPDLinkID\">$LRes[$i]</a></td>");
										break;
												
										default:
											echo("<td>$LRes[$i]</td>");;
										break;
									}
								else echo("<td>$LRes[$i]</td>");
							}
							//XCal - Only show edit on linked for types that support it
							if ($ONRLTPID == 5) {
								if ($TSKSec['NewMod'] == 2)
									$CanEdit = true;
								elseif ($TSKSec['NewMod'] == 1)
									$CanEdit = ItemLinked($sql,5,$ONRID);
								else $CanEdit = false;
							}
							else $CanEdit = false;
							if ($CanEdit)
								echo('<td><input type="button" class="button" value="Edit" onclick="'.
										"AJAX('$TPDTarget','$TPDPath','$TPDKeys&M=M&LID=$TPDLinkID');".
										'" /></td>');
							echo('</tr>');
						}
						$stm->free_result();
						echo('</table>');
					}
					else $TPDFailError .= 'Type linked failed to fetch linking items ('.$stm->error.').<br />';
				}
				else $TPDFailError .= 'Type linked failed while preparing to fetch linking items.<br />';
				
				//XCal - Control that apply only when there are records
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Prev" onclick="');
					echo("AJAX('$TPDTarget','$TPDPath','$TPDKeys&M=L&RO=$PrevOffset&RC=$Records');");
					echo('" />');
				}
				if ($Count > ($Offset+$Records)) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="');
					echo("AJAX('$TPDTarget','$TPDPath','$TPDKeys&M=L&RO=$NextOffset&RC=$Records');");
					echo('" />');
				}
				echo('<br />'); //XCal - Stops next and prior floating over next article
			}
		}
		if (strlen($TPDFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$TPDFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Type Linked View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$TPDDieError.
			'</article>');
}
?>
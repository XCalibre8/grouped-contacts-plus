<?php
//XCal - We only want to pay attention to request variables if they're for the all links view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'TPL')) {
	$For = 'TPL';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$TPLTarget = $_REQUEST['T'];

	//XCal - [XC:OLT|Owner Link Type: The owner link type ID;;]
	if (isset($_REQUEST['OLT']))
		$ONRLTPID = $_REQUEST['OLT'];
	if (isset($_REQUEST['ID']))
		$ONRID = $_REQUEST['ID'];
	//XCal - [XC:CLT|Child Link Type: The child link type ID;;]
	if (isset($_REQUEST['CLT']))
		$CHDLTPID = $_REQUEST['CLT'];
	//XCal - [XC:ExID|Exclude ID:If you want to exclude a record, e.g. A persons primary address, pass the ID here;;]
	if (isset($_REQUEST['ExID']))
		$ExID = $_REQUEST['ExID'];
	//XCal - These are the "eXtra" fields on the link table
	if (isset($_REQUEST['XID']))
		$TPLXID = $_REQUEST['XID'];
	if (isset($_REQUEST['XSTR']))
		$TPLXSTR = $_REQUEST['XSTR'];		
	//XCal - Supported values [XC:M|Mode:{L},N,M,D,S|List(Default),New,Modify,Delete,Search;]
	if (isset($_REQUEST['M']))
		$TPLMode = $_REQUEST['M'];
	elseif (! isset($TPLMode))
		$TPLMode = 'L';
	if (isset($_REQUEST['S']))
		$TPLSearch = $_REQUEST['S'];
	elseif (! isset($TPLSearch))
		$TPLSearch = '';
	//XCal - Supported values [XC:SM|SearchMode:{C},B,L|Contains,Begins,Exact;]
	if (isset($_REQUEST['SM']))
		$TPLSMode = $_REQUEST['SM'];
	elseif (! isset($TPLSMode))
		$TPLSMode = 'C';
	//XCal - Variables that are reset if they aren't passed in
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 10;
	if (isset($_REQUEST['ORD']))
		$Order = $_REQUEST['ORD'];	
	if (isset($_REQUEST['SO']))
		$Sort = $_REQUEST['SO'];
	else $Sort = 'ASC';
	//XCal - [XC:LID|Link ID:The subject link ID for New/Modify/Delete/XRef(Attach Existing) operations
	if (isset($_REQUEST['LID']))
		$TPLLinkID = $_REQUEST['LID'];
	//XCal - [XC:LL|Link ID List:A comma separated list of ID's to link;]
	if (isset($_REQUEST['LL']))
		$TPLLinkList = $_REQUEST['LL'];
	//XCal - [XC:ML|Multi-Link:Boolean(0,1)|(0|Single Select Only,1|Allow Multi-Link);]
	if (isset($_REQUEST['ML']))
		$TPLMultiLink = ($_REQUEST['ML'] = 1);
	else $TPLMultiLink = true;
}
elseif (isset($For) and ($For == 'TPL')) {
	if (isset($Target))
		$TPLTarget = $Target;
	
	if (isset($OLT))
		$ONRLTPID = $OLT;
	if (isset($CLT))
		$CHDLTPID = $CLT;
	if (isset($ID))
		$ONRID = $ID;	
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 10;
	if (isset($MultiLink))
		$TPLMultiLink = $MultiLink;
	else $TPLMultiLink = true;
}
else {			
	$Offset = 0;
	$Records = 10;
	$TPLMultiLink = true;
}
if (! isset($TPLMode))
	$TPLMode = 'L';
if (! isset($TPLSMode))
	$TPLSMode = 'C';

//XCal - If these aren't set then we'll need to pass NULL to the stored proc
if (! isset($TPLXID))
	$TPLXID = null;
if (! isset($TPLXSTR))
	$TPLXSTR = null;
if (! isset($ExID))
	$ExID = 0;
$TPLPath = 'resources/linking/type_links.php';
$TPLDieError = '';
$TPLFailError = '';
$TPLModes = array('L','N','M','D','S');

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($ONRLTPID))
	$TPLDieError .= 'Type links view has not been passed the owner link type identifier.<br />';
if (! isset($ONRID))
	$TPLDieError .= 'Type links view has not been passed the owner identifier to display links for.<br />';
if (! isset($CHDLTPID))
	$TPLDieError .= 'Type links view has not been passed the child link type identifier.<br />';
if (! isset($TPLTarget))
	$TPLDieError .= 'Type links view has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($TPLMode,$TPLModes)) 
	$TPLDieError .= "Type links view has been passed a mode ($TPLMode) which it does not support.<br />";
if ((! $TPLDieError == '') and (isset($For) and ($For <> 'TPL')))
	$TPLDieError .= "These problems might be because params are marked as for '$For' and need to be for 'TPL'.<br />";
	

if ($TPLDieError == '') {
	$TPLKeys = "For=TPL&T=$TPLTarget&OLT=$ONRLTPID&ID=$ONRID&CLT=$CHDLTPID";
	//$_SESSION['DebugStr'] .= "Type Links: Running with keys $TPLKeys on child $TPLLinkID<br />";
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		CheckWebAccount($sql);
		//XCal - This gets us the dynamic data we need for the link type
		$stm = $sql->prepare('SELECT LTP_NAME,LTP_TABLE,LTP_ID_FIELD '.
				'FROM lnk_link_types '.
				'WHERE LTP_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$CHDLTPID);
			if ($stm->execute()) {
				$stm->bind_result($LTPName,$LTPTable,$LTPIDField);
				$stm->fetch();
				$stm->free_result();
			}
			else $TPLFailError .= 'Type links view failed getting link type details ('.$stm->error.').<br />';
		}
		//XCal - We don't really want to keep storing our queries in the tables, so lets determine selection here
		$LTPNLSelect = '';
		switch ($CHDLTPID) {
			case 1: //Linked People
				$LTPTitles = 'Title,Forenames,Surname';
				$LTPSelect = 'fn_con_title(PER_TIT_ID),PER_FORENAMES,PER_SURNAME';			
			break;
			
			case 2: //Linked Addresses
				$LTPTitles = 'Type,Line 1,Town,Country,Postcode';
				$LTPSelect = 'fn_con_contact_type_name(LNK_X_ID),ADR_LINE1,ADR_POST_TOWN,fn_con_country_name(ADR_CNTRY_ID),ADR_POSTCODE';
				$LTPNLTitles = 'Line 1,Town,Country,Postcode';
				$LTPNLSelect = 'ADR_LINE1,ADR_POST_TOWN,fn_con_country_name(ADR_CNTRY_ID),ADR_POSTCODE';
			break;
			
			case 3: //Linked ContactPoints
				$LTPTitles = 'Type,Method,Contact';
				$LTPSelect = 'fn_con_contact_type_name(LNK_X_ID),fn_con_contact_point_type_name(CNP_CPT_ID),CNP_CONTACT';
				$LTPNLTitles = 'Method,Contact';
				$LTPNLSelect = 'fn_con_contact_point_type_name(CNP_CPT_ID),CNP_CONTACT';
			break;
			
			case 4: //Linked Groups
				$LTPTitles = 'Group,Description';
				$LTPSelect = 'GRP_NAME,GRP_DESCRIPTION';
			break;
			
			case 5: //Linked Projects/Tasks
				$LTPTitles = 'Title,Description';
				$LTPSelect = 'TSK_TITLE,TSK_DESCRIPTION';
			break;
			
			case 6: //Linked Web Accounts
				$LTPTitles = 'User Name,Active?';
				$LTPSelect = 'WAC_USERNAME,WAC_ACTIVE';
			break;
			
			case 7: //Linked Self Aware Systems
				$LTPTitles = 'Name,Description';
				$LTPSelect = 'SYS_NAME,SYS_DESCRIPTION';
			break;
			
			case 8: //Linked Schedule Items
				$LTPTitles = 'Type,Start,End';
				$LTPSelect = 'fn_sch_schedule_type_name(SCI_SCT_ID),SCI_START,SCI_END';
			break;
			
			case 9: //Linking Users
				$LTPTitles = 'Username';
				$LTPSelect = 'USR_USERNAME';
			break;
			
			default:
				$TPLFailError .= 'Type links view does not support the type it\'s been requested to link!<br />';
			break;
		}
		
		if (($ONRLTPID == 5) and ($TPLMode != 'S') and (! in_array($CHDLTPID,array(2,3)))) { //XCal - Addresses and ContactPoints always need contact type and care of/speak to, otherwise show protask state
			$LTPTitles .= ',Status';
			$LTPSelect .= ',fn_pro_state_name(LNK_X_ID)';		
		}
		
		$ONRAreaID = GetAreaIDByLinkType($ONRLTPID);
		$ONRGENSec = GetAreaSecurity($sql, $ONRAreaID);
		$CHDAreaID = GetAreaIDByLinkType($CHDLTPID);
		$CHDGENSec = GetAreaSecurity($sql, $CHDAreaID);
		$ONRLinked = ItemLinked($sql,$ONRLTPID,$ONRID);
		if ($ONRGENSec['NewMod'] == 2)
			$ONREdit = true;
		else $ONREdit = ($ONRGENSec['NewMod'] > 0) and $ONRLinked;
		if ($ONRGENSec['Remove'] == 2)
			$ONRRem = true;
		else $ONRRem = ($ONRGENSec['Remove'] > 0) and $ONRLinked;
		//XCal - Let's check if we're in New/Modify/Delete mode
		if (in_array($TPLMode,array('N','M','D'))) {					
			//XCal - If we're modifying or deleting we require an ID
			if (in_array($TPLMode,array('M','D')) and (!(isset($TPLLinkID) and ($TPLLinkID > 0)))) {
				$TPLFailError .= 'Type links was passed a modify or delete request without an ID specified. Changing to list mode.<br />';
				$TPLMode = 'L';
			}
			elseif ($TPLMode == 'D') { //XCal - Delete item and return to list mode
				//$_SESSION['DebugStr'] .= 'Type Links: Delete Mode<br />';
				if ($ONRGENSec['Remove'] > 0) {
					if ($ONRGENSec['Remove'] == 2)
						$ONRRem = true;
					else $ONRRem = $ONRLinked;
				
					$CHDRem = $CHDGENSec['Remove'] > 0;
					
					if ($ONRRem and $CHDRem) {
						$stm->prepare("SELECT 0,'' INTO @Code,@Msg");
						if ($stm) {
							$stm->execute();
							$stm->free_result();
							
							if ($stm->prepare('CALL sp_lnk_rem_link_item(?,?,?,?,@Code,@Msg)')) {
								$stm->bind_param('iiii',$ONRLTPID,$ONRID,$CHDLTPID,$TPLLinkID);
								if ($stm->execute()) {
									$stm->free_result();
									
									if ($stm->prepare('SELECT @Code,@Msg')) {
										$stm->execute();
										$stm->bind_result($RCode,$RMsg);
										$stm->fetch();
										echo("Remove result: $RMsg<br />");
										$stm->free_result();
									}
									else $TPLFailError .= 'Type links encountered an error retrieving link removal result<br />.';
								}
								else echo('Remove attempt failed with error: '.$stm->error.'<br />');
							}
							else $TPLFailError .= 'Type links encountered an error preparing to remove the link ('.$stm->error.').<br />';
						}
						else $TPLFailError .= 'Type links encountered an error initialising link removal.<br />';
					}
					else echo('Your access rights do not allow you to remove this linked item.');
					//XCal - Remove or fail return to list view, beats the hell out of a blank screen!
					$TPLMode = 'L';
				}
				else echo('You do not have the required access rights to remove the requested record type.');
			}
			else { //XCal - (N)ew or (M)odify
				if ($ONRGENSec['NewMod'] > 0) {
					$CHDEdit = $CHDGENSec['NewMod'] > 0;
					if ($ONREdit and $CHDEdit) {
						//XCal - OK then, let's just check the type and call the relevant linkmod unit
						switch ($CHDLTPID) {
							case 1: //XCal - Person
								if ($ONRLTPID == 5)
									require(dirname(__FILE__).'/../protasks/protask_linkstate.php');
								else require(dirname(__FILE__).'/../contacts/person_linkmod.php');
							break;
							case 2: //XCal - Address
								require(dirname(__FILE__).'/../contacts/address_linkmod.php');
							break;
							case 3: //XCal - Contact Point
								require(dirname(__FILE__).'/../contacts/contactpoint_linkmod.php');
							break;
							case 4: //XCal - Group
								if ($ONRLTPID == 5)
									require(dirname(__FILE__).'/../protasks/protask_linkstate.php');
								else require(dirname(__FILE__).'/../groups/group_linkmod.php');
							break;
							case 5: //XCal - Project/Task
								if ($ONRLTPID == 5)
									require(dirname(__FILE__).'/../protasks/protask_linkstate.php');
								else require(dirname(__FILE__).'/../protasks/protask_linkmod.php');
							break;	
							case 6: //XCal - Web Account
								if ($ONRLTPID == 5)
									require(dirname(__FILE__).'/../protasks/protask_linkstate.php');
								else echo('<p>The type links module has attempted to add or modify an unsupported type!</p>');
							break;
							case 7: //XCal - Self Aware System
								if ($ONRLTPID == 5)
									require(dirname(__FILE__).'/../protasks/protask_linkstate.php');
								else echo('<p>The type links module has attempted to add or modify an unsupported type!</p>');
							break;
							case 8: //XCal - Schedule Items
								require(dirname(__FILE__).'/../sched/sched_linkmod.php');
							break;
							default:
								echo('<p>The type links module has attempted to add or modify an unsupported type!</p>');
							break;
						}
					}
					else echo('Your access rights to not allow you to edit this link type on this item.');
				}
				else echo('You do not have the required access rights to modify the requested record type.');
			}
		}
		
		//XCal - If we're not modifying or the action's done we need to run the search/list
		if (in_array($TPLMode,array('S','L'))) {
			//XCal - Here we set the queries up by whether it's a search or a "pure" list
			if ($TPLMode == 'S') {
				if ($ONRGENSec['NewMod'] > 0) {					
					if ($ONREdit) {
						//XCal - If we've been passed an existing record ID to link, do that and return to list mode
						if (isset($TPLLinkID) and ($TPLLinkID > 0)) {
							$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
							if ($stm) {
								if ($stm->execute()) {
									$stm->free_result();
									if ($stm->prepare('CALL sp_link_existing(?,?,?,?,?,?,@Code,@Msg)')) {
										$stm->bind_param('iiiiis',$ONRLTPID,$ONRID,$CHDLTPID,$TPLLinkID,$LTPXID,$LTPXSTR);
										if ($stm->execute()) {
											$stm->free_result();
											if ($stm->prepare('SELECT @Code,@Msg')) {
												if ($stm->execute()) {
													$stm->bind_result($RCode,$RMsg);
													$stm->fetch();
													$stm->free_result();
													echo("Linking result: $RMsg<br />");
													$TPLMode = 'L';
												}
												else $TPLFailError .= 'Type links failed getting linking results with error: '.$stm->error.'<br />';
											}
											else $TPLFailError .= 'Type links encountered an error preparing to get linking results.<br />';
										}
										else $TPLFailError .= 'Type links failed linking the item with error: '.$stm->error.'<br />';
									}
									else $TPLFailError .= 'Type links encountered an error preparing to link the item.<br />';
								}
								else $TPLFailError .= 'Type links failed initialising linking with error: '.$stm->error.'<br />';
							}
							else $TPLFailError .= 'Type links encountered an error preparing to initialise linking.<br />';
						}
						//XCal - If we've been passed a list of IDs to link, do it and return to list mode
						if (isset($TPLLinkList)) {
							$LinkArray = str_getcsv($TPLLinkList);
							$TPLAddCount = 0;
							$TPLUpdCount = 0;
							$TPLPreventCount = 0;
							$TPLUnexpected = 0;
							foreach ($LinkArray as $ID) {
								$stm = $sql->prepare("SELECT 0,'' INTO @Code,@Msg");
								if ($stm) {
									$stm->execute();
									$stm->free_result();
									if ($stm->prepare('CALL sp_link_existing(?,?,?,?,?,?,@Code,@Msg)')) {
										$stm->bind_param('iiiiis',$ONRLTPID,$ONRID,$CHDLTPID,$ID,$LTPXID,$LTPXSTR);
										//$_SESSION['DebugStr'] .= "Type Links: Calling sp_lnk_existing with params $ONRLTPID:$ONRID:$CHDLTPID:$ID:$LTPXID:$LTPXSTR<br />";
										$stm->execute();
										$stm->free_result();
										if ($stm->prepare('SELECT @Code,@Msg')) {
											$stm->execute();
											$stm->bind_result($RCode,$RMsg);
											$stm->fetch();
											$stm->free_result();
											switch ($RCode) {
												case -1:
													$TPLPreventCount++;
												break;
												case 1:
													$TPLAddCount++;
												break;
												case 2:
													$TPLUpdCount++;
												break;							  	
												default:
													$TPLUnexpected++;
			// 											if (! isset($UMsg))
			// 												$UMsg = '';
			// 											$UMsg .= "Unexpected result code $RCode with message $RMsg<br />";
												break;
											}
										}
										else $TPLFailError .= 'Type links encountered an error preparing to get linking results.<br />';
									}
									else $TPLFailError .= 'Type links encountered an error preparing to link items.<br />';
								}
								else $TPLFailError .= 'Type links encountered an error initialising linking.<br />';
							}					
							echo("Linking results: $TPLAddCount added, $TPLUpdCount updated, $TPLPreventCount prevented, $TPLUnexpected unexpected.<br />");
			// 					if ($TPLUnexpected > 0)
			// 						echo($UMsg);
							$TPLMode = 'L';
						}
						//XCal - If we're still in search mode then we haven't linked yet and need to search
						if ($TPLMode == 'S') {
							if ($TPLSearch > '') {
								switch($CHDLTPID) {
									case 1: //XCal - Person
										if ($TPLSMode == 'E') 
											$SFields = ' AND ((PER_FORENAMES = @SText) OR (PER_SURNAME = @SText))';
										else $SFields = ' AND ((PER_FORENAMES LIKE @SText) OR (PER_SURNAME LIKE @SText))';								
									break;
									case 2: //XCal - Address
										if ($TPLSMode == 'E')
											$SFields = ' AND ((ADR_LINE1 = @SText) '.
													'OR (ADR_LINE2 = @SText) '.
													'OR (ADR_POST_TOWN = @SText) '.
													'OR (ADR_COUNTY = @SText) '.
													'OR (ADR_POSTCODE = @SText))';
										else
											$SFields = ' AND ((ADR_LINE1 LIKE @SText) '.
													'OR (ADR_LINE2 LIKE @SText) '.
													'OR (ADR_POST_TOWN LIKE @SText) '.
													'OR (ADR_COUNTY LIKE @SText) '.
													'OR (ADR_POSTCODE LIKE @SText))';
									break;
									case 3: //XCal - Contact Point
										if ($TPLSMode == 'E')
											$SFields = ' AND (CNP_CONTACT = @SText)';
										else $SFields = ' AND (CNP_CONTACT LIKE @SText)';
									break;
									case 4: //XCal - Group
										if ($TPLSMode == 'E')
											$SFields = ' AND (GRP_NAME = @SText)';
										else $SFields = ' AND (GRP_NAME LIKE @SText)';
									break;
									case 5: //XCal - Project/Task
										if ($TPLSMode == 'E')
											$SField = ' AND (TSK_TITLE = @SText)';
										else $SFields = ' AND (TSK_TITLE LIKE @SText)';
									break;
									default:
										echo('<p>The type links module has attempted to search an unsupported type!</p>');
										break;
								}
							}
							else $SFields = '';
							if ($ONRLTPID == $CHDLTPID)
								$SFields .= " AND $LTPIDField <> ?";
							$CQuery = "SELECT COUNT($LTPIDField) ".
									"FROM $LTPTable ".
									'WHERE NOT EXISTS('.
										'SELECT LNK_X_ID '.
										'FROM lnk_links '.
										'WHERE LNK_ONR_LTP_ID = ? '.
										'AND LNK_ONR_ID = ? '.
										'AND LNK_CHD_LTP_ID = ? '.
										"AND LNK_CHD_ID = $LTPIDField)".
										$SFields;
							$SQuery = "SELECT $LTPIDField,";
							if (strlen(trim($LTPNLSelect)) > 0)
								$SQuery .= $LTPNLSelect;
							else $SQuery .= $LTPSelect;
							$SQuery .= " FROM $LTPTable ".
									'WHERE NOT EXISTS('.
									'SELECT LNK_X_ID '.
									'FROM lnk_links '.
									'WHERE LNK_ONR_LTP_ID = ? '.
									'AND LNK_ONR_ID = ? '.
									'AND LNK_CHD_LTP_ID = ? '.
									"AND LNK_CHD_ID = $LTPIDField)".
									$SFields.
									' LIMIT ?,?';
							//$_SESSION['DebugStr'] .= "Type links: Search Query:<br />$SQuery<br />";
							$RText = 'matching';
						}
					}
					else echo('You do not have the right to add linked items to the requested item.');
				}
				else echo('You do not have the required access rights to add linked items to the requested item type.');
			}
			//XCal - Don't else this, the search may have linked and need the list view 
			if ($TPLMode == 'L') {				
				if (isset($ExID)) //XCal - We now know they have an exclusion
					$ExQuery = ' AND LNK_CHD_ID <> ?';
				else $ExQuery = '';
				
				$CQuery = 'SELECT COUNT(LNK_ONR_LTP_ID) '.
						'FROM lnk_links '.				
						'WHERE LNK_ONR_LTP_ID = ? '.
						'AND LNK_ONR_ID = ? '.
						'AND LNK_CHD_LTP_ID = ?'.$ExQuery;
				//XCal - TODO - We should really have a way of checking the SR indicator
				$SQuery = "SELECT $LTPIDField,$LTPSelect";
				$SQuery .= ' FROM lnk_links '.
						"JOIN $LTPTable ON $LTPIDField = LNK_CHD_ID ".
						'WHERE LNK_ONR_LTP_ID = ? '.
						'AND LNK_ONR_ID = ? '.
						'AND LNK_CHD_LTP_ID = ?'.$ExQuery;					
				//XCal - ORDER BY		
				if (isset($Order))
					$SQuery .= ' ORDER BY '.$Order.' '.$Sort;
				$SQuery .= ' LIMIT ?,?';
				$RText = 'linked';
			}
			$IsSearch = ($TPLMode == 'S');
			
			//XCal - NOW at long last we can build the list or search view
			if (isset($SFields) and ($TPLSearch > '')) {
				//$_SESSION['DebugStr'] .= "Type Links: Selecting $TPLSearch into @SText<br />";
				$stm = $sql->prepare('SELECT ? INTO @SText');
				if ($stm) {
					if ($TPLSMode == 'C')
						$TPLEncSearch = "%$TPLSearch%";
					elseif ($TPLSMode == 'B')
						$TPLEncSearch = "$TPLSearch%";
					else $TPLEncSearch = $TPLSearch;
					$stm->bind_param('s',$TPLEncSearch);
					$stm->execute();
					$stm->free_result();
	// 					if ($stm->execute())
	// 						$stm->free_result();
	// 					else $TPLFailError .= 'Type links failed initialising the search value.<br />';
				}
				else $TPLFailError .= 'Type links encountered an error initialising the search value.<br />';
			}
			$stm = $sql->prepare($CQuery);
			if ($stm) {
				if (isset($ExID)and (! $IsSearch))
					$stm->bind_param('iiii',$ONRLTPID,$ONRID,$CHDLTPID,$ExID);
				elseif ($IsSearch and ($ONRLTPID == $CHDLTPID))
					$stm->bind_param('iiii',$ONRLTPID,$ONRID,$CHDLTPID,$ONRID);
				else $stm->bind_param('iii',$ONRLTPID,$ONRID,$CHDLTPID);
				//$_SESSION['DebugStr'] .= "Type Links: Querying link count with ONR_LTP_ID = $ONRLTPID, ONR_ID = $ONRID and CHD_LTP_ID = $CHDLTPID.<br />";
				if ($stm->execute()) {
					$stm->bind_result($Count);
					$stm->fetch();
					$stm->free_result();
				}
				else $TPLFailError .= 'Type links view failed querying link count ('.$stm->error.').<br />';
			}
			else $TPLFailError .= 'Type links view failed preparing to count typed links ('.$sql->error.').<br />';
			
			//XCal - Controls that should be present regardless of whether there are records
			if ($ONRGENSec['NewMod'] > 0) {
				if ($IsSearch) {				
					echo('<form name='."LinkSearch$LTPName".'" action="javascript:;" onsubmit="'.
							"SearchLinks('$TPLTarget','$LTPName','OLT=$ONRLTPID&ID=$ONRID&CLT=$CHDLTPID')\">".
							"<input type=\"text\" id=\"edtSearchLink$LTPName\"");
					if (strlen($TPLSearch) > 0)
						echo(" value=\"$TPLSearch\"");
					echo(' /><input type="Submit" class="button" value="Search"/>'.
							'</form>'.
							'<input type="button" class="button" value="Show Linked" onclick="'.
							"AJAX('$TPLTarget','$TPLPath','$TPLKeys');".
							'" />');
				}
				else echo('<input type="button" class="button" value="Find Existing" onclick="'.
						"SearchLinks('$TPLTarget','$LTPName','OLT=$ONRLTPID&ID=$ONRID&CLT=$CHDLTPID');".
						'" />');				 
				echo('<input class="button" type="button" value="New" onclick="'.
						"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=N');".
						'" />');
			}
			if ($Count == 0)
				echo("<center><em>No $RText $LTPName found.</em></center><br />");
			else {
				if ($IsSearch and $TPLMultiLink)
					echo('<input class="button" type="button" value="Link Selected" onclick="'.
							"LinkSelected('$TPLTarget','$LTPName','OLT=$ONRLTPID&ID=$ONRID&CLT=$CHDLTPID');".
							'" />');
				//XCal - This supporting fixed numbers of result fields is a total fiddle while I look up how to bind a dynamic field list to an array
				if ($IsSearch and isset($LTPNLTitles)) {
					$LTPTitArr = str_getcsv($LTPNLTitles,',');
					$LTPUseSel = $LTPNLSelect;
				}
				else {
					$LTPTitArr = str_getcsv($LTPTitles,',');
					$LTPUseSel = $LTPSelect;
				}
				$FCount = sizeof($LTPTitArr);
				if ($FCount > 1)
					$LTPFldArr = str_getcsv($LTPUseSel,',');
				else $LTPFldArr[0] = $LTPUseSel;
				//$_SESSION['DebugStr'] .= "Type Links: Querying $SQuery<br />";
			
				$stm = $sql->prepare($SQuery);				
				if ($stm) {					
					if (isset($ExID) and (! $IsSearch))
						$stm->bind_param('iiiiii',$ONRLTPID,$ONRID,$CHDLTPID,$ExID,$Offset,$Records);
					elseif ($IsSearch and ($ONRLTPID == $CHDLTPID))
						$stm->bind_param('iiiiii',$ONRLTPID,$ONRID,$CHDLTPID,$ONRID,$Offset,$Records);
					else $stm->bind_param('iiiii',$ONRLTPID,$ONRID,$CHDLTPID,$Offset,$Records);
						
					if ($stm->execute()) {
						switch ($FCount) {
							case 1:
								$stm->bind_result($TPLLinkID,$LRes[0]);
								break;
							case 2:
								$stm->bind_result($TPLLinkID,$LRes[0],$LRes[1]);
								break;
							case 3:
								$stm->bind_result($TPLLinkID,$LRes[0],$LRes[1],$LRes[2]);
								break;
							case 4:
								$stm->bind_result($TPLLinkID,$LRes[0],$LRes[1],$LRes[2],$LRes[3]);
								break;
							case 5:
								$stm->bind_result($TPLLinkID,$LRes[0],$LRes[1],$LRes[2],$LRes[3],$LRes[4]);
								break;
							default:
								$TPLFailError .= 'Type links supports displaying between 1 and 5 fields, the request is outside that range!<br />';
								break;
						}
	
						$LowRec = $Offset+1;
						if ($Offset+$Records > $Count)
							$HighRec = $Count;
						else $HighRec = $Offset+$Records;						
						echo("<center>Linked $LTPName: $LowRec-$HighRec of $Count</center>");
						echo('<table><tr>');
						
						if ($IsSearch and $TPLMultiLink)
							echo('<th><input type="checkbox" id="'.$LTPName.'" onchange="SelAll('."'$LTPName'".');" /></th>');
						$FCount = count($LTPTitArr);
						for ($i = 0; $i < $FCount; $i++) {
							echo('<th>'.$LTPTitArr[$i]);
							if (! $IsSearch) {
								if (isset($Order) and ($LTPFldArr[$i] == $Order)) {
									if ($Sort == 'ASC') 
										echo('&dArr;');
									else echo('<input type="button" class="button" value="&dArr;" onclick="'.
											"AJAX('$TPLTarget','$TPLPath','$TPLKeys&ORD=$LTPFldArr[$i]');".
											'" />');
									if ($Sort == 'DESC')
										echo('&uArr;');
									else echo('<input type="button" class="button" value="&uArr;" onclick="'.
											"AJAX('$TPLTarget','$TPLPath','$TPLKeys&ORD=$LTPFldArr[$i]&SO=DESC');".
											'" />');								
								}
								else {
									echo('<input type="button" class="button" value="&dArr;" onclick="'.
											"AJAX('$TPLTarget','$TPLPath','$TPLKeys&ORD=$LTPFldArr[$i]');".
											'" />');
									echo('<input type="button" class="button" value="&uArr;" onclick="'.
											"AJAX('$TPLTarget','$TPLPath','$TPLKeys&ORD=$LTPFldArr[$i]&SO=DESC');".
											'" />');
								}
							}
							echo('</th>');
						}
						//foreach ($LTPTitArr as $LTPTitle) {
						//	echo("<th>$LTPTitle");
						//}
						if ($IsSearch)
							echo('<th>Link</th>');
						else {
							echo('<th>Edit</th><th>Remove</th>');
						}
						echo('</tr>');
						while ($stm->fetch()) {
							$CHDLinked = ItemLinked($sql,$CHDLTPID,$TPLLinkID);
							echo(//"<input type=\"hidden\" name=\"LNK_CHD_ID\" value=\"$TPLLinkID\" />".
								'<tr>');
							if ($IsSearch and $TPLMultiLink) //XCal - Output checkbox here if in search mode
								echo('<td><input type="checkbox" name="chkSel'.$LTPName.'" value="'.$TPLLinkID.'" /></td>');
							for ($i = 0; $i < $FCount; $i++) {
								if ($i == 0)
									switch ($CHDLTPID) {
										case 1:
											echo("<td><a href=\"contacts.php?V=P&ID=$TPLLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 2:
											echo("<td><a href=\"contacts.php?V=A&ID=$TPLLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 3:
											echo("<td><a href=\"contacts.php?V=C&ID=$TPLLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 4:
											echo("<td><a href=\"groups.php?ID=$TPLLinkID\">$LRes[$i]</a></td>");
										break;
										
										case 5:
											echo("<td><a href=\"protasks.php?ID=$TPLLinkID\">$LRes[$i]</a></td>");
										break;
										
										default:
											echo("<td>$LRes[$i]</td>");;
										break;
									}
								else echo("<td>$LRes[$i]</td>");
							}
							//XCal - Output select or edit/remove buttons here depending on mode
							if ($IsSearch)
							echo('<td><input type="button" class="button" value="LinkMe" onclick="'.
									"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=S&LID=$TPLLinkID');".
									'" /></td>');
							else {
								if ($CHDGENSec['NewMod'] == 2)
									$CHDEdit = true;
								else $CHDEdit = ($CHDGENSec['NewMod'] > 0) and $CHDLinked;
								
								if ($CHDGENSec['Remove'] == 2)
									$CHDRem = true;
								else $CHDRem = ($CHDGENSec['Remove'] > 0) and $CHDLinked;
								
								if ($ONREdit and $CHDEdit)
									echo('<td><input type="button" class="button" value="Edit" onclick="'.
											"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=M&LID=$TPLLinkID');".
											'" /></td>');
								if ($ONRRem and $CHDRem)
									echo('<td><input type="button" class="button" value="Remove" onclick="'.
											"AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=D&LID=$TPLLinkID');".
											'" /></td>');
							}
							echo('</tr>');
						}
						$stm->free_result();
						echo('</table>');
					}
					else $TPLFailError .= 'Type links failed to fetch linked items ('.$stm->error.').<br />';
				}
				else $TPLFailError .= 'Type links failed while preparing to fetch linked items('.$sql->error.').<br />';
				
				//XCal - Control that apply only when there are records
				if ($Offset > 0) {
					$PrevOffset = $Offset-$Records;
					echo('<input class="prevbutton" type="button" value="Prev" onclick="');
					if ($IsSearch)
						echo("SearchLinks('$TPLTarget','$LTPName','OLT=$ONRLTPID&ID=$ONRID&CLT=$CHDLTPID&RO=$PrevOffset&RC=$Records");
					else echo("AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&RO=$PrevOffset&RC=$Records");
					if (isset($Order))
						echo("&ORD=$Order&SO=$Sort");
					echo('\');" />');
				}
				if ($Count > ($Offset+$Records)) {
					$NextOffset = $Offset+$Records;
					echo('<input class="nextbutton" type="button" value="Next" onclick="');
					if ($IsSearch)
						echo("SearchLinks('$TPLTarget','$LTPName','OLT=$ONRLTPID&ID=$ONRID&CLT=$CHDLTPID&RO=$NextOffset&RC=$Records");
					else echo("AJAX('$TPLTarget','$TPLPath','$TPLKeys&M=L&RO=$NextOffset&RC=$Records");
					if (isset($Order))
						echo("&ORD=$Order&SO=$Sort");
					echo('\');" />');
				}
				echo('<br />'); //XCal - Stops next and prior floating over next article
			}
		}
		if (strlen($TPLFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$TPLFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Type Links View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$TPLDieError.
			'</article>');
}
?>
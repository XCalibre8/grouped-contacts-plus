<?php
require(dirname(__FILE__).'/../f_utilities.php');
require(dirname(__FILE__).'/../gen_session.php');
//XCal - Get the web account ID for the login token if present
function ValidateToken(&$Connection) {
	if (isset($_SESSION['LoginToken'])) {
		CheckConnection($Connection);
		$stm = $Connection->prepare('SET @WAC = NULL');
		if ($stm) {
			$stm->execute();
			$stm->free_result();
			if ($stm->prepare('CALL sp_web_validate_token(?,@WAC)')) {
				$stm->bind_param('s',$_SESSION['LoginToken']);
				if ($stm->execute()) {
					$stm->free_result();
					if ($stm->prepare('SELECT @WAC')) {
						if ($stm->execute()) {
							$stm->bind_result($WACID);
							$stm->fetch();						
							$stm->free_result();
						}
					}
				}
			}
		}
		if (isset($WACID)) {
			if ((! isset($_SESSION['WebAccID'])) or ($_SESSION['WebAccID'] != $WACID))
				$_SESSION['WebAccID'] = $WACID;
			return $WACID;		
		}
		else {			
			$_SESSION['LoginToken'] = ' ';
			return NULL;
		}
	}
	else return NULL;
};
//XCal - Validate the token if the var isn't already true
function CheckToken(&$Connection,&$TokenValid) {
	if (! isset($TokenValid) or (! $TokenValid))
		$TokenValid = ValidateToken($Connection) > 0;
}
//XCal - Initialise or re-load web account details into the session
function LoadWebAccount(&$Connection) {
	if (isset($_SESSION['WebAccID'])) {
		CheckConnection($Connection);
		$stm = $Connection->prepare(
				'SELECT WAC_USERNAME,WAC_EMAIL,WAC_PER_ID,WAC_USR_ID,WAC_UPDATED '.
				'FROM web_accounts '.
				'WHERE WAC_ID = ?');
		if ($stm) {		
			$stm->bind_param('i',$_SESSION['WebAccID']);
			if ($stm->execute()) {
				$stm->bind_result($WACUserName,$WACEmail,$WACPERID,$WACUSRID,$WACUPDATED);
				$Found = $stm->fetch();
				if ($Found) {				
					$_SESSION['WACUserName'] = $WACUserName;
					$_SESSION['WACEmail'] = $WACEmail;
					$_SESSION['WACPERID'] = $WACPERID;
					$_SESSION['WACUSRID'] = $WACUSRID;
					$_SESSION['WACUpdated'] = strtotime($WACUPDATED);
					$_SESSION['WACChecked'] = time();
				}
				$stm->free_result();
				return $Found;
			}
		}
	}
	else return false;
};
//XCal - Set up security defaults of no access for when no security is specified.
function SetDefaultSecurity() {
	if (isset($_SESSION['Security']))
		unset($_SESSION['Security']);
	$_SESSION['Security']['Loaded'] = time();
	$_SESSION['Security'][0]['SearchView'] = 0;
	$_SESSION['Security'][0]['NewMod'] = 0;
	$_SESSION['Security'][0]['Remove'] = 0;
	$_SESSION['Security'][0]['Config'] = false;
};
//XCal - Initialise or re-load session security for the session web account
function LoadSecurity(&$Connection) {	
	if (isset($_SESSION['WebAccID'])) {		
		if (CheckWebAccount($Connection)) {
			if (isset($_SESSION['WACUSRID']) and ($_SESSION['WACUSRID'] > 0)) {
				$stm = $Connection->prepare(
						'SELECT ACA_ID,RAC_SEARCHVIEW,RAC_NEWMOD,RAC_REMOVE,RAC_CONFIG '.
						'FROM usr_user_roles '.
						'JOIN usr_role_access ON RAC_ROL_ID = URL_ROL_ID '.
						'JOIN usr_access_areas ON ACA_ID = RAC_ACA_ID '.
						'WHERE URL_USR_ID = ? '.
						'ORDER BY RAC_ROL_ID,RAC_ACA_ID');
				if ($stm) {
					$stm->bind_param('i',$_SESSION['WACUSRID']);
					if ($stm->execute()) {
						if (isset($_SESSION['Security']))
							unset($_SESSION['Security']);
						$_SESSION['Security']['Loaded'] = time();
						$stm->bind_result($ID,$SearchView,$NewMod,$Remove,$Config);						
						$Recs = 0;
						while ($stm->fetch()) {
							$Recs++;
							if (!isset($_SESSION['Security'][$ID]['SearchView']))
								$_SESSION['Security'][$ID]['SearchView'] = $SearchView;
							elseif ($SearchView > $_SESSION['Security'][$ID]['SearchView'])
								$_SESSION['Security'][$ID]['SearchView'] = $SearchView;
							if (!isset($_SESSION['Security'][$ID]['NewMod']))
								$_SESSION['Security'][$ID]['NewMod'] = $NewMod;
							elseif ($NewMod > $_SESSION['Security'][$ID]['NewMod'])
								$_SESSION['Security'][$ID]['NewMod'] = $NewMod;
							if (!isset($_SESSION['Security'][$ID]['Remove']))
								$_SESSION['Security'][$ID]['Remove'] = $Remove;
							elseif ($Remove > $_SESSION['Security'][$ID]['Remove'])
								$_SESSION['Security'][$ID]['Remove'] = $Remove;
							if (!isset($_SESSION['Security'][$ID]['Config']))
								$_SESSION['Security'][$ID]['Config'] = $Config == 1;
							elseif (! $_SESSION['Security'][$ID]['Config'])
							$_SESSION['Security'][$ID]['Config'] = $Config == 1;
						}
						$stm->free_result();
						if ($Recs == 0)
							SetDefaultSecurity();
						return true;
					}
					else return false;
				}
				else return false;
			}
			else {
				SetDefaultSecurity();
				return true;
			}
		}
		else return false;
	}
	else return false;
};
//XCal - Check that security settings are loaded and up to date to within 5 minutes
function CheckSecurity(&$Connection) {
	CheckWebAccount($Connection);
	if (isset($_SESSION['Security'])) {
		if (isset($_SESSION['WACUSRID']) and ($_SESSION['WACUSRID'] > 0)) {
			$TNow = time();
			$TUpd = $_SESSION['Security']['Loaded'];
			$DNow = date_create();
			$DUpd = date_create();
			date_date_set($DNow,date('Y',$TNow),date('m',$TNow),date('d',$TNow));
			date_time_set($DNow,date('H',$TNow),date('i',$TNow),date('s',$TNow));
			date_date_set($DUpd,date('Y',$TUpd),date('m',$TUpd),date('d',$TUpd));
			date_time_set($DUpd,date('H',$TUpd),date('i',$TUpd),date('s',$TUpd));
			$Diff = date_diff($DNow, $DUpd);
			if ($Diff > date_interval_create_from_date_string('5 minutes')) {
				CheckConnection($Connection);
				$stm = $Connection->prepare(
						'SELECT max(RAC_UPDATED) '.
						'FROM usr_user_roles '.
						'JOIN usr_role_access ON RAC_ROL_ID = URL_ROL_ID '.
						'WHERE URL_USR_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$_SESSION['WACUSRID']);
					if ($stm->execute()) {
						$stm->bind_result($LatestUpdate);
						$stm->fetch();
						if (isset($LatestUpdate)) {
							$SecUpdated = strtotime($LatestUpdate);
							if ($SecUpdated > $_SESSION['Security']['Loaded'])
								return LoadSecurity($Connection);
							else return true;
						}
						else return true;
						$stm->free_result();
					}
				}
				else return false;
			}
			else return true;
		}
		else return true;
	}
	else return LoadSecurity($Connection);
};
//XCal - Check that web account details are loaded and up to date to within 5 minutes
function CheckWebAccount(&$Connection) {
	if (isset($_SESSION['WebAccID'])) {
		if (! isset($_SESSION['WACUserName'])) {
			return LoadWebAccount($Connection);
		}
		else {			
			$TNow = time();
			$DNow = date_create();
			date_date_set($DNow,date('Y',$TNow),date('m',$TNow),date('d',$TNow));
			date_time_set($DNow, date('H',$TNow), date('i',$TNow), date('s',$TNow));
			$DUpd = date_create();
			$TUpd = $_SESSION['WACUpdated'];
			date_date_set($DUpd,date('Y',$TUpd),date('m',$TUpd),date('d',$TUpd));
			date_time_set($DUpd, date('H',$TUpd), date('i',$TUpd), date('s',$TUpd));
			$Diff = date_diff($DNow, $DUpd);

			if ($Diff > date_interval_create_from_date_string('5 minutes')) {
				CheckConnection($Connection);
				$stm = $Connection->prepare('SELECT WAC_UPDATED FROM web_accounts WHERE WAC_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$_SESSION['WebAccID']);
					if ($stm->execute()) {
						$stm->bind_result($Updated);
						$stm->fetch();
						$stm->free_result();
					}
				}
				if (isset($Updated)) {
					$AccUpdated = strtotime($Updated);
					if ($AccUpdated > $_SESSION['WACUpdated'])
						return LoadWebAccount($Connection);
					else return true;					
				}
				else return false;
			}
			else return true;
		}
	}
	else return false;
};
//XCal - Return the relevant security array for an area
function GetAreaSecurity(&$Connection,$AreaID) {
	if (CheckSecurity($Connection)) {
		if (isset($_SESSION['Security'][$AreaID])) 
			return $_SESSION['Security'][$AreaID];		
		else 
			return $_SESSION['Security'][0];		
	}
	else {
		$res['SearchView'] = 0;
		$res['NewMod'] = 0;
		$res['Remove'] = 0;
		$res['Config'] = false;
		return $res;
	}
};
//XCal - Return the security area for a link type
function GetAreaIDByLinkType($LinkType) {
	$CONTypes = array(1,2,3);
	$GRPTypes = array(4);
	$TSKTypes = array(5);
	$SCHTypes = array(8);
	$USRTypes = array(9);
	if (in_array($LinkType,$CONTypes))
		return 1;
	elseif (in_array($LinkType,$GRPTypes))
		return 2;
	elseif (in_array($LinkType,$TSKTypes))
		return 3;
	elseif (in_array($LinkType,$SCHTypes))
		return 5;
	elseif (in_array($LinkType,$USRTypes))
		return 6;
	else return 0;
};
//XCal - Check whether the user or web account is linked by an item
function ItemLinked(&$Connection,$LinkType,$LinkID) {
	CheckWebAccount($Connection);
	$stm = $Connection->prepare(
			'SELECT EXISTS(SELECT LNK_UPDATED FROM lnk_links '.
			'WHERE LNK_ONR_LTP_ID = ? AND LNK_ONR_ID = ? '.
			'AND ((LNK_CHD_LTP_ID = 9 AND LNK_CHD_ID = ?) '.
			'OR (LNK_CHD_LTP_ID = 6 AND LNK_CHD_ID = ?)'.
			'OR (LNK_CHD_LTP_ID = 1 AND LNK_CHD_ID = ?))');
	if ($stm) {
		$stm->bind_param('iiiii',$LinkType,$LinkID,$_SESSION['WACUSRID'],$_SESSION['WebAccID'],$_SESSION['WACPERID']);
		if ($stm->execute()) {
			$stm->bind_result($IsLinked);
			$stm->fetch();
			return ($IsLinked == 1);
			$stm->free_result();
		}
		else return false;
	}
	else return false;
};
?>
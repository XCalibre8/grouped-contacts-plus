<?php
//XCal - We only want to pay attention to request variables if they're for the all links view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'ALN')) {
	$For = 'ALN';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$ALNTarget = $_REQUEST['T'];

	if (isset($_REQUEST['OLT']))
		$ONRLTPID = $_REQUEST['OLT'];
	if (isset($_REQUEST['ID']))
		$ONRID = $_REQUEST['ID'];
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to CTS then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'ALN')) {
		if (isset($Target))
			$ALNTarget = $Target;
		
		if (isset($OLT))
			$ONRLTPID = $OLT;
		if (isset($ID))
			$ONRID = $ID;
	}
}
$ALNPath = 'resources/linking/all_links.php';
$ALNDieError = '';
$ALNFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($ONRLTPID))
	$ALNDieError .= 'All links view has not been passed the owner link type identifier.<br />';
if (! isset($ONRID))
	$ALNDieError .= 'All links view has not been passed the owner identifier to display links for.<br />';
if (! isset($ALNTarget))
	$ALNDieError .= 'All links view has not been told where to target its displays and thus would not be safe to run.<br />';

if ($ALNDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		//XCal - TODO - This should behave differently for types which have a type availability table (groups,schedules)
		$stm = $sql->prepare(
				'SELECT LTP_ID,LTP_NAME '.
				'FROM lnk_link_type_avail '.
				'JOIN lnk_link_types ON LTA_CHD_LTP_ID = LTP_ID '.
				'WHERE LTA_ONR_LTP_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$ONRLTPID);
			if ($stm->execute()) {
				$stm->bind_result($LTPID,$LTPChildName);
				//XCal - We'll stick the link types in an array so we don't hold an open dataset
				$Rec = 0;
				while ($stm->fetch()) {
					$LTypes[$Rec]['ID'] = $LTPID;
					$LTypes[$Rec]['Name'] = $LTPChildName;
					$Rec++;
				}
				$stm->free_result();
			}
			else $ALNFailError .= 'All links view failed to retrieve available link types with error ('.$stm->error.')<br />';
		}
		else $ALNFailError .= 'All links view encountered an error preparing to get available link type details ('.$sql->error.')<br />';
		//XCal - Since we're recursively calling type_links
		$For = 'TPL';
		$OLT = $ONRLTPID;
		$ID = $ONRID;
		if (isset($LTypes))
			foreach ($LTypes as $LType) {
				$CLT = $LType['ID'];
				$LTPChildName = $LType['Name'];
				$Target = "$ONRLTPID"."_$ONRID"."_link$LTPChildName";
				if (isset($ExADR) and ($CLT == 2))
					$ExID = $ExADR;
				elseif (isset($ExCNP) and ($CLT == 3))
					$ExID = $ExCNP;			
				echo("<article><h5>Linked $LTPChildName</h5>".
						"<div id=\"$Target\">");
				
				require(dirname(__FILE__).'/type_links.php');
				
				echo(	'</div>'.
					'</article>');
			}
		if (strlen($ALNFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$ALNFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>All Links View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$ALNDieError.
			'</article>');
}
?>
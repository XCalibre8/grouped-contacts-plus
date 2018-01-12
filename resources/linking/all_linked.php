<?php
//XCal - We only want to pay attention to request variables if they're for the all links view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'ALD')) {
	$For = 'ALD';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$ALDTarget = $_REQUEST['T'];

	if (isset($_REQUEST['CLT']))
		$CHDLTPID = $_REQUEST['CLT'];
	if (isset($_REQUEST['ID']))
		$CHDID = $_REQUEST['ID'];
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	if (isset($For) and ($For == 'ALD')) {
		if (isset($Target))
			$ALDTarget = $Target;
		
		if (isset($CLT))
			$CHDLTPID = $CLT;
		if (isset($ID))
			$CHDID = $ID;
	}
}
$ALDPath = 'resources/linking/all_linked.php';
$ALDDieError = '';
$ALDFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($CHDLTPID))
	$ALDDieError .= 'All linked view has not been passed the child link type identifier.<br />';
if (! isset($CHDID))
	$ALDDieError .= 'All linked view has not been passed the child identifier to display what it\'s linked by.<br />';
if (! isset($ALDTarget))
	$ALDDieError .= 'All linked view has not been told where to target its displays and thus would not be safe to run.<br />';

if ($ALDDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		//XCal - TODO - This should behave differently for types which have a type availability table (groups,schedules)
		$stm = $sql->prepare(
				'SELECT LTP_ID,LTP_NAME '.
				'FROM lnk_link_type_avail '.
				'JOIN lnk_link_types ON LTA_ONR_LTP_ID = LTP_ID '.
				'WHERE LTA_CHD_LTP_ID = ?');
		if ($stm) {
			$stm->bind_param('i',$CHDLTPID);
			if ($stm->execute()) {
				$stm->bind_result($LTPID,$LTPOwnerName);
				//XCal - We'll stick the link types in an array so we don't hold an open dataset
				$Rec = 0;
				while ($stm->fetch()) {
					$LTypes[$Rec]['ID'] = $LTPID;
					$LTypes[$Rec]['Name'] = $LTPOwnerName;
					$Rec++;
				}
				$stm->free_result();
			}
			else $ALDFailError .= 'All linked view failed to retrieve available link types with error ('.$stm->error.')<br />';
		}
		else $ALDFailError .= 'All linked view encountered an error preparing to get available link type details ('.$sql->error.')<br />';
		//XCal - Since we're recursively calling type_linked
		$For = 'TPD';
		$OLT = $CHDLTPID;
		$ID = $CHDID;
		if (isset($LTypes))
			foreach ($LTypes as $LType) {
				$OLT = $LType['ID'];
				$LTPOwnerName = $LType['Name'];
				$Target = $LTPOwnerName."link$CHDLTPID"."_$CHDID";
				echo("<article><h5>Linked by $LTPOwnerName</h5>".
						"<div id=\"$Target\">");
				
				require(dirname(__FILE__).'/type_linked.php');
				
				echo(	'</div>'.
					'</article>');
			}
		if (strlen($ALDFailError) > 0)
			echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$ALDFailError");
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>All Linked View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$ALDDieError.
			'</article>');
}
?>
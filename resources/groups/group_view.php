<?php
require(dirname(__FILE__).'/../gen_session.php');
//$_SESSION['DebugStr'] .= 'Entered Group View<br />';
//XCal - We only want to pay attention to request variables if they're for the group view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'GPV')) {
	$For = 'GPV';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$GPVTarget = $_REQUEST['T'];

	if (isset($_REQUEST['ID']))
		$GRPID = $_REQUEST['ID'];
	else $GRPID = -1;	
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	//XCal - If the $For variable is set to CTS then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'GPV')) {
		if (isset($Target))
			$GPVTarget = $Target;
		if (isset($ID))
			$GRPID = $ID;
	}
}
$GPVPath = 'resources/groups/group_view.php';
$GPVDieError = '';
$GPVFailError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($GRPID))
	$GPVDieError .= 'Group view has not been passed a group to display.<br />';
if (! isset($GPVTarget))
	$GPVDieError .= 'Group view has not been told where to target its displays and thus would not be safe to run.<br />';

if ($GPVDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');	
	
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$GRPSec = GetAreaSecurity($sql, 2);
		if ($GRPSec['SearchView'] > 0) {
			if ($GRPSec['SearchView'] == 2)
				$CanView = true;
			else $CanView = ItemLinked($sql,4,$GRPID);
			if ($CanView) {
				$stm = $sql->prepare(
					'SELECT GRP_GTP_ID,GRP_NAME,GRP_DESCRIPTION,GRP_DOCUMENT '.
					'FROM grp_groups '.
					'WHERE GRP_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$GRPID);
					$stm->execute();
					$stm->bind_result($GRPGTPID,$GRPName,$GRPDesc,$GRPDoc);
					$stm->fetch();
					$stm->free_result();
				}
				else $GPVDieError .= 'Group view failed preparing to get group details.<br />';
				echo("<article><h3>$GRPName</h3>");
				if ($GRPDesc > '')
					echo("<p>$GRPDesc</p>");
				if ($GRPDoc > '')
					echo($GRPDoc.'<br />');
				
				//XCal - Now we need to determine what link types to show for the group
				//XC8 - Actually we don't, we're going to put all this in the link unit
				//XCal - Yes we do numbnuts, group types control the available links!
				//XC8 - Oh yeah, sorry. I'll shut up.
				
				$stm = $sql->prepare('SELECT COUNT(GTA_LTP_ID) '.
						'FROM grp_group_type_avail_links '.
						'WHERE GTA_GTP_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$GRPGTPID);
					$stm->execute();
					$stm->bind_result($Count);
					$stm->fetch();
					$stm->free_result();
				}
			
				//XC8 - Look, see, you're going to NEED to make this dynamic to work with the new link system!
				//XCal - Yes yes, but I need this unit formatted safely so I can get on to that Task!!!
				if ($Count == 0) {
					//XCal - No restrictions, set all supported link types to true
					$LinkPeople = true;
					$LinkAddresses = true;
					$LinkContactPoints = true;
					$LinkGroups = true;
					$LinkTasks = true;
				}
				else {
					//XCal - We have restrictions, so default everything to false
					$LinkPeople = false;
					$LinkAddresses = false;
					$LinkContactPoints = false;
					$LinkGroups = false;
					$LinkTasks = false;
					//XCal - Now check the data and set link type support true for results
					$stm = $sql->prepare('SELECT GTA_LTP_ID '.
							'FROM grp_group_type_avail_links '.
							'WHERE GTA_GTP_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$GRPGTPID);
						$stm->execute();
						$stm->bind_result($GTALTPID);
						while ($stm->fetch()) {
							switch ($GTALTPID) {
								case 1:
									$LinkPeople = true;
									break;
								case 2:
									$LinkAddresses = true;
									break;
								case 3:
									$LinkContactPoints = true;
									break;
								case 4:
									$LinkGroups = true;
									break;
								case 5:
									$LinkTasks = true;
									break;
							}
						}
						$stm->free_result();
					}
				}
				
				$LTP = 4;
				$ID = $GRPID;
				require(dirname(__FILE__).'/../reqful/reqful_both.php');
				
				//XCal - Now we can go make the right link types available for the group by group type
				$For = 'TPL';
				$OLT = 4;
				echo('<h4>Group Links</h4>');
				if ($LinkGroups) {
					$Target = 'linkgroups';
					$CLT = 4;	
					echo ("<article><h5>Linked Groups</h5><div id=\"$Target\">");
					require(dirname(__FILE__).'/../linking/type_links.php');
					echo ("</div></article>");
				}
				if ($LinkPeople) {
					$Target = 'linkpeople';
					$CLT = 1;
					echo ("<article><h5>Linked People</h5><div id=\"$Target\">");
					require(dirname(__FILE__).'/../linking/type_links.php');
					echo ("</div></article>");
				}
				if ($LinkAddresses) {
					$Target = 'linkaddresses';
					$CLT = 2;
					echo ("<article><h5>Linked Addresses</h5><div id=\"$Target\">");
					require(dirname(__FILE__).'/../linking/type_links.php');
					echo ("</div></article>");
				}
				if ($LinkContactPoints) {
					$Target = 'linkcontactpoints';
					$CLT = 3;
					echo ("<article><h5>Linked Contact Points</h5><div id=\"$Target\">");
					require(dirname(__FILE__).'/../linking/type_links.php');
					echo ("</div></article>");
				}
				if ($LinkTasks) {
					$Target = 'linktasks';
					$CLT = 5;
					echo ("<article><h5>Linked Tasks</h5><div id=\"$Target\">");
					require(dirname(__FILE__).'/../linking/type_links.php');
					echo ("</div></article>");
				}
				
				echo('<h4>Group Linked by...</h4>'.
						'<div id="grouplinked">');
				$For = 'ALD';
				$CLT = 4;
				$ID = $GRPID;
				$Target = 'grouplinked';
				require(dirname(__FILE__).'/../linking/all_linked.php');
				echo('</div>');
				
				if (strlen($GPVFailError) > 0)
					echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$GPVFailError");
			}
			else echo('You only have the right to view groups your account is linked by, which does not include the requested group.');
		}
		else echo('You do not have the required access rights to view groups.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Group View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$GPVDieError.
		'</article>');
}
?>	
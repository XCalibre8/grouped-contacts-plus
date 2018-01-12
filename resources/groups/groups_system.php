<?php
//XCal - We only want to pay attention to request variables if they're for the groups system page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'GRP')) {
	$For = 'GRP';
	if (isset($_REQUEST['Cfg']) and ($_REQUEST['Cfg'] == 1)) {
		$Cfg = true;
	}
	else $Cfg = false;
	if (isset($_REQUEST['T']))
		$GRPTarget = $_REQUEST['T'];
}
else {		
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	$Cfg = false;
	//XCal - If the $For variable is set to GRP then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'GRP')) {
		if (isset($Target))
			$GRPTarget = $Target;
	}
}
$GRPPath = 'resources/groups/groups_system.php';
$GRPDieError = '';


//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($GRPTarget))
	$GRPDieError .= 'The groups system has not been told where to target its displays and thus would not be safe to run.<br />';

if ($GRPDieError == '') {
	require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$GRPSec = GetAreaSecurity($sql, 2);
		//XCal - If we're not in config mode just display the stats
		if (! $Cfg) {
			$sql->multi_query(
				'SELECT COUNT(GTP_ID) FROM grp_group_types WHERE GTP_SR = 0;'.
				'SELECT COUNT(GRP_ID) FROM grp_groups WHERE GRP_SR = 0;'.
				'SELECT COUNT(GTA_GTP_ID) FROM grp_group_type_avail_links;');
	
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$GTPCount = $row[0];
			$sql->next_result();
			
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$GRPCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$GTACount = $row[0];
			
			if ($GTACount > 0) {
				$Restrictions = 'not all groups can link to all system areas';
			}
			else {
				$Restrictions = 'there are no restrictions on what any groups can link to';
			}
						
			echo(
				'<article>'.
					'<h2>Groups System Information</h2>'.
					'<div class="article-info">'.
					'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
					'</div>'.
					"<p>The groups system currently stores <em>$GTPCount group types</em> and <em>$GRPCount groups</em>. ".
					"There are <em>$GTACount available link types</em> specified on group types, which means <em>$Restrictions</em>.</p>".
				'</article>');
			
			if ($GRPSec['Config'])
				echo('<input type="button" class="button" value="Configure Groups System" onclick="'.
					"AJAX('$GRPTarget','$GRPPath','For=GRP&T=$GRPTarget&Cfg=1');".
					'" /><br /><br />');
				
		}
		elseif ($GRPSec['Config']) { //XCal - Configuration Mode
			echo(
				'<input type="button" class="button" value="Finish Groups Configuration" onclick="'.
				"AJAX('$GRPTarget','$GRPPath','For=GRP&T=$GRPTarget');".
				'" />'.
				'<article>'.
					'<h2>Groups Configuration</h2>'.
					'<p>Manage and maintain group types and their restrictions here.</p>'.
				'</article>');
	
			echo(
				'<article>'.
					'<h4>Group Types</h4>'.
					'<p>These allow you to classify different types of groups and control what groups of each type can do.</p>'.	
					'<div id="grouptypes">');
			$GTPTarget = 'grouptypes';
			$GTPMode = 'C';
			require(dirname(__FILE__).'/group_types.php');
			echo(
					'</div>'.
				'</article>');
				
			echo('<input type="button" class="button" value="Finish Groups Configuration" onclick="'.
				"AJAX('$GRPTarget','$GRPPath','For=GRP&T=$GRPTarget');".
				'" /><br /><br />');
		}
		else echo('You do not have the required access rights to configure the groups system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Groups System Code Problem</h2>'.
			'<div class="article-info">'.
				'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$GRPDieError.
		'</article>');
}
?>
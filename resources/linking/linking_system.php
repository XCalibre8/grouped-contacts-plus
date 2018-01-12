<?php
//XCal - We only want to pay attention to request variables if they're for the groups system page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'LSY')) {
	$For = 'LSY';
	if (isset($_REQUEST['Cfg']) and ($_REQUEST['Cfg'] == 1)) {
		$Cfg = true;
	}
	else $Cfg = false;
	if (isset($_REQUEST['T']))
		$LSYTarget = $_REQUEST['T'];
}
else {		
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	$Cfg = false;
	//XCal - If the $For variable is set to LSY then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'LSY')) {
		if (isset($Target))
			$LSYTarget = $Target;
	}
}
$LSYPath = 'resources/linking/linking_system.php';
$LSYDieError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($LSYTarget))
	$LSYDieError .= 'The linking system has not been told where to target its displays and thus would not be safe to run.<br />';

if ($LSYDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$LNKSec = GetAreaSecurity($sql, 7);
		//XCal - If we're not in config mode just display the stats
		if (! $Cfg) {
			$sql->multi_query(
				'SELECT COUNT(LTP_ID) FROM lnk_link_types;'.
				'SELECT COUNT(LNK_ONR_LTP_ID) FROM lnk_links;'.
				'SELECT COUNT(LTA_SR) FROM lnk_link_type_avail WHERE LTA_SR = 0;');
	
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$LTPCount = $row[0];
			$sql->next_result();
			
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$LNKCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$LTACount = $row[0];
			
			echo(
				'<article>'.
					'<h2>Linking System Information</h2>'.
					'<div class="article-info">'.
					'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
					'</div>'.
					"<p>The linking system currently stores <em>$LTPCount linkable system cores</em> and ".
					"<em>$LNKCount links</em> between (or within) systems. ".
					"There are <em>$LTACount available ways</em> for system cores to link to each other allowed.</p>".
				'</article>');
			
			if ($LNKSec['Config'])
				echo('<input type="button" class="button" value="Configure Linking System" onclick="'.
					"AJAX('$LSYTarget','$LSYPath','For=LSY&T=$LSYTarget&Cfg=1');".
					'" /><br /><br />');				
		}
		elseif ($LNKSec['Config']) { //XCal - Configuration Mode
			echo(
				'<input type="button" class="button" value="Finish Linking Configuration" onclick="'.
				"AJAX('$LSYTarget','$LSYPath','For=LSY&T=$LSYTarget');".
				'" />'.
				'<article>'.
					'<h2>Linking Configuration</h2>'.
					'<p>Manage and maintain which system cores can link to which other system cores.</p>'.
				'</article>');
	
			echo(
				'<article>'.
					'<h4>Link Types</h4>'.
					'<p>These are the linkable areas of your XCalibre8 system, called system cores. '.
					'These are determined by your installation, select a system core to set what it can link to.</p>'.	
					'<div id="linktypes">');
			
			
			$LTPTarget = 'linktypes';
			$LTPMode = 'C';
			require(dirname(__FILE__).'/link_types.php');
			
			echo(
					'</div>'.
				'</article>');
				
			echo('<input type="button" class="button" value="Finish Linking Configuration" onclick="'.
				"AJAX('$LSYTarget','$LSYPath','For=LSY&T=$LSYTarget');".
				'" /><br /><br />');
		}
		else echo('You do not have the required access rights to configure the linking system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Linking System Code Problem</h2>'.
			'<div class="article-info">'.
				'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$LSYDieError.
		'</article>');
}
?>
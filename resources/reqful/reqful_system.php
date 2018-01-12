<?php
//XCal - We only want to pay attention to request variables if they're for the groups system page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'RFS')) {
	$For = 'RFS';
	if (isset($_REQUEST['Cfg']) and ($_REQUEST['Cfg'] == 1)) {
		$Cfg = true;
	}
	else $Cfg = false;		
	if (isset($_REQUEST['T']))
		$RFSTarget = $_REQUEST['T'];
}
else {		
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	$Cfg = false;
	if (isset($For) and ($For == 'RFS')) {
		if (isset($Target))
			$RFSTarget = $Target;
	}
}
$RFSPath = 'resources/reqful/reqful_system.php';
$RFSDieError = '';


//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($RFSTarget))
	$RFSDieError .= 'The requirement fulfillment system has not been told where to target its displays and thus would not be safe to run.<br />';

if ($RFSDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$RQFSec = GetAreaSecurity($sql, 4);
		//XCal - If we're not in config mode just display the stats
		if (! $Cfg) {
			$sql->multi_query(
				'SELECT COUNT(FLT_ID) FROM rqf_fulfillment_types WHERE FLT_SR = 0;'.
				'SELECT COUNT(FLM_ID) FROM rqf_fulfillments WHERE FLM_SR = 0;'.
				'SELECT COUNT(FLL_FLM_ID) FROM rqf_fulfillment_levels WHERE FLL_SR = 0;'.
				'SELECT COUNT(FLP_FLT_ID) FROM rqf_fulfillment_providers WHERE FLP_SR = 0;'.
				'SELECT COUNT(REQ_ONR_LTP_ID) FROM rqf_requirements WHERE REQ_SR = 0;');
	
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$FLTCount = $row[0];
			$sql->next_result();
			
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$FLMCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$FLLCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$FLPCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$REQCount = $row[0];
									
			echo(
				'<article>'.
					'<h2>Requirement Fulfillment System Information</h2>'.
					'<div class="article-info">'.
					'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
					'</div>'.
					"<p>The requirement fulfillment system currently stores <em>$FLTCount fulfillment types</em> and <em>$FLMCount fulfillments</em>. ".
					"There are <em>$FLLCount specified levels</em> of fulfillment, <em>$FLPCount fulfillment providers</em> and <em>$REQCount requirements</em> in the system.</p>".
				'</article>');
			
			if ($RQFSec['Config'])
				echo('<input type="button" class="button" value="Configure Requirement Fulfillment System" onclick="'.
						"AJAX('$RFSTarget','$RFSPath','For=RFS&T=$RFSTarget&Cfg=1');".
						'" /><br /><br />');
				
		}
		elseif ($RQFSec['Config']) { //XCal - Configuration Mode
			echo(
				'<input type="button" class="button" value="Finish Requirement Fulfillment Configuration" onclick="'.
				"AJAX('$RFSTarget','$RFSPath','For=RFS&T=$RFSTarget');".
				'" />'.
				'<article>'.
					'<h2>Requirement Fulfillment Configuration</h2>'.
					'<p>Manage and maintain fulfillment types and available fulfillments here.</p>'.
				'</article>');
	
			echo(
				'<article>'.
					'<h4>Fulfillment Types</h4>'.
					'<p>These allow you to classify different types of fulfillment which linkable system areas can provide.</p>'.	
					'<div id="fulfillmenttypes">');
			$RFTTarget = 'fulfillmenttypes';
			$RFTMode = 'C';
			require(dirname(__FILE__).'/reqful_fulfill_types.php');
			echo(
					'</div>'.
				'</article>');
				
			echo('<input type="button" class="button" value="Finish Requirement Fulfillment Configuration" onclick="'.
				"AJAX('$RFSTarget','$RFSPath','For=RFS&T=$RFSTarget');".
				'" /><br /><br />');
		}
		else echo('You do not have the required access rights to configure the requirement fulfillment system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Requirement Fulfillment System Code Problem</h2>'.
			'<div class="article-info">'.
				'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$RFSDieError.
		'</article>');
}
?>
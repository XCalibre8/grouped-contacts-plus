<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'SCS')) {
	$For = 'SCS';
	if (isset($_REQUEST['Cfg']) and ($_REQUEST['Cfg'] == 1)) {
		$Cfg = true;
	}
	else $Cfg = false;
	if (isset($_REQUEST['T']))
		$SCSTarget = $_REQUEST['T'];
}
else {		
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	$Cfg = false;
	if (isset($For) and ($For == 'SCS')) {
		if (isset($Target))
			$SCSTarget = $Target;
	}
}
$SCSPath = 'resources/sched/sched_system.php';
$SCSDieError = '';

//XCal - Let's make sure we have the values we need to run the page
if (! isset($SCSTarget))
	$SCSDieError .= 'The scheduling system has not been told where to target its displays and thus would not be safe to run.<br />';

if ($SCSDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$SCHSec = GetAreaSecurity($sql, 5);
		//XCal - If we're not in config mode just display the stats
		if (! $Cfg) {
			$sql->multi_query(
				'SELECT COUNT(SCT_ID) FROM sch_schedule_types WHERE SCT_SR = 0;'.
				'SELECT COUNT(SCA_SCT_ID) FROM sch_schedule_type_avail_links;'.
				'SELECT COUNT(SCI_ID) FROM sch_schedule_items WHERE SCI_SR = 0;'.
				'SELECT COUNT(SCP_ID) FROM sch_schedule_patterns WHERE SCP_SR = 0;'.
				'SELECT COUNT(SPI_ID) FROM sch_schedule_pattern_items WHERE SPI_SR = 0;');
	
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$SCTCount = $row[0];
			$sql->next_result();
			
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$SCACount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$SCICount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$SCPCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$SPICount = $row[0];
			
			if ($SCICount > 0) {
				$Restrictions = 'not all schedule types can link to all system areas';
			}
			else {
				$Restrictions = 'there are no restrictions on what any schedule type can link to';
			}
						
			echo(
				'<article>'.
					'<h2>Scheduling System Information</h2>'.
					'<div class="article-info">'.
					'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
					'</div>'.
					"<p>The scheduling system currently stores <em>$SCTCount schedule types</em> and <em>$SCICount scheduled items</em>. ".
					"There are <em>$SCACount available link types</em> specified on schedule types, which means <em>$Restrictions</em>. ".
					"There are also <em>$SCPCount schedule patterns</em> configured with <em>$SPICount defined segments</em>.</p>".
				'</article>');
			
			if ($SCHSec['Config'])
				echo('<input type="button" class="button" value="Configure Scheduling System" onclick="'.
						"AJAX('$SCSTarget','$SCSPath','For=SCS&T=$SCSTarget&Cfg=1');".
						'" /><br /><br />');
				
		}
		elseif ($SCHSec['Config']) { //XCal - Configuration Mode
			echo(
				'<input type="button" class="button" value="Finish Scheduling Configuration" onclick="'.
				"AJAX('$SCSTarget','$SCSPath','For=SCS&T=$SCSTarget');".
				'" />'.
				'<article>'.
					'<h2>Scheduling Configuration</h2>'.
					'<p>Manage and maintain schedule types and patterns here.</p>'.
				'</article>');
	
			echo(
				'<article>'.
					'<h4>Scheduling Types</h4>'.
					'<p>These allow you to classify different schedule types and control their default colour and what each type can link to.</p>'.	
					'<div id="schedtypes">');
			$SCTTarget = 'schedtypes';
			$SCTMode = 'C';
			require(dirname(__FILE__).'/sched_types.php');
			echo(
					'</div>'.
				'</article>');
				
			echo('<input type="button" class="button" value="Finish Scheduling Configuration" onclick="'.
				"AJAX('$SCSTarget','$SCSPath','For=SCS&T=$SCSTarget');".
				'" /><br /><br />');
		}
		else echo('You do not have the required access rights to configure the scheduling system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Scheduling System Code Problem</h2>'.
			'<div class="article-info">'.
				'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$SCSDieError.
		'</article>');
}
?>
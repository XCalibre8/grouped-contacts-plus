<?php
//XCal - We only want to pay attention to request variables if they're for the groups system page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PTS')) {
	$For = 'PTS';
	if (isset($_REQUEST['Cfg']) and ($_REQUEST['Cfg'] == 1)) {
		$Cfg = true;
	}
	else $Cfg = false;
	if (isset($_REQUEST['T']))
		$PTSTarget = $_REQUEST['T'];
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	$Cfg = false;
	//XCal - If the $For variable is set to PTS then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'GRP')) {
		if (isset($Target))
			$PTSTarget = $Target;
	}
}
$PTSPath = 'resources/protasks/protask_system.php';
$PTSDieError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($PTSTarget))
	$PTSDieError .= 'The projects/tasks system has not been told where to target its displays and thus would not be safe to run.<br />';

if ($PTSDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$TSKSec = GetAreaSecurity($sql, 3);
		//XCal - If we're not in config mode just display the stats
		if (! $Cfg) {
			$sql->multi_query(
					'SELECT COUNT(TST_ID) FROM pro_task_types WHERE TST_SR = 0;'.
					'SELECT COUNT(TSK_ID) FROM pro_tasks WHERE TSK_SR = 0;'.
					'SELECT COUNT(TSS_ID) FROM pro_task_states WHERE TSS_SR = 0;'.
					'SELECT COUNT(TTA_TST_ID) FROM pro_task_type_avail_states;'.
					'SELECT COUNT(TTT_TST_ID) FROM pro_task_type_avail_types;');			
					
	
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$TSTCount = $row[0];
			
			$sql->next_result();			
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$TSKCount = $row[0];
				
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$TSSCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$TTACount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$TTTCount = $row[0];
				
			if ($TTACount > 0)
				$TTARestrict = 'some task states are restricted from some task types';
			else $TTARestrict = 'all task states are available for all types';
			
			if ($TTTCount > 0)
				$TTTRestrict = 'some task types which support sub-tasks restrict the types available to their sub-tasks';
			else $TTTRestrict = 'any type of task which supports sub-tasks can have any type of task as a sub-task';
	
			echo(
					'<article>'.
					'<h2>Projects/Tasks System Information</h2>'.
					'<div class="article-info">'.
					'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
					'</div>'.
					"<p>The projects/tasks system currently stores <em>$TSTCount types</em>, <em>$TSKCount tasks</em> and ".
					"<em>$TSSCount possible states</em> for projects/tasks. There are <em>$TTACount specifications</em> on what states are ".
					"allowed for types, which means <em>$TTARestrict</em>. There are <em>$TTTCount specifications</em> on what types are ".
					"allowed for types, which means <em>$TTTRestrict</em>.</p>".
					'</article>');
				
			if ($TSKSec['Config'])
				echo('<input type="button" class="button" value="Configure Projects/Tasks System" onclick="'.
						"AJAX('$PTSTarget','$PTSPath','For=PTS&T=$PTSTarget&Cfg=1');".
						'" /><br /><br />');
	
		}
		elseif ($TSKSec['Config']) { //XCal - Configuration Mode
			echo(
					'<input type="button" class="button" value="Finish Projects/Tasks Configuration" onclick="'.
					"AJAX('$PTSTarget','$PTSPath','For=PTS&T=$PTSTarget');".
					'" />'.
					'<article>'.
					'<h2>Projects/Tasks Configuration</h2>'.
					'<p>Manage and maintain project/task types, states and their restrictions here.</p>'.
					'</article>');
	
			echo(
					'<article>'.
					'<h4>Project/Task States</h4>'.
					'<p>These allow you define possible states for your projects/tasks and whether they class as complete.</p>'.
					'<div id="protaskstates">');
			$PSTTarget = 'protaskstates';
			require(dirname(__FILE__).'/protask_states.php');
			echo(
					'</div>'.
					'</article>'.
					'<article>'.
					'<h4>Project/Task Types</h4>'.
					'<p>These allow you to classify different types of projects/tasks and control what each type can do.</p>'.
					'<div id="protasktypes">');
			$PTPTarget = 'protasktypes';
			$PTPMode = 'C';
			require(dirname(__FILE__).'/protask_types.php');
			echo(
					'</div>'.
					'</article>');
	
			echo('<input type="button" class="button" value="Finish Projects/Tasks Configuration" onclick="'.
					"AJAX('$PTSTarget','$PTSPath','For=PTS&T=$PTSTarget');".
					'" /><br /><br />');
		}
		else echo('You do not have the required access rights to configure the ProTask system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Projects/Tasks System Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$PTSDieError.
			'</article>');
}
?>
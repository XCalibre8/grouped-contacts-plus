<?php
//XCal - We only want to pay attention to request variables if they're for the group view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'PTV')) {
	$For = 'PTV';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$PTVTarget = $_REQUEST['T'];

	if (isset($_REQUEST['ID']))
		$TSKID = $_REQUEST['ID'];
	else $TSKID = -1;
}
else {
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	if (isset($For) and ($For == 'PTV')) {
		if (isset($Target))
			$PTVTarget = $Target;
		if (isset($ID))
			$TSKID = $ID;
	}
}
$PTVPath = 'resources/protasks/protask_view.php';
$PTVDieError = '';
$PTVFailError = '';

//XCal - Let's make sure we have the values we need to run the page
if (! isset($TSKID))
	$PTVDieError .= 'ProTask view has not been passed a task to display.<br />';
if (! isset($PTVTarget))
	$PTVDieError .= 'ProTask view has not been told where to target its displays and thus would not be safe to run.<br />';

if ($PTVDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');

	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$TSKSec = GetAreaSecurity($sql, 3);
		if ($TSKSec['SearchView'] > 0) {
			if ($TSKSec['SearchView'] == 2) 
				$CanView = true;
			else $CanView = ItemLinked($sql,5,$TSKID);
			
			if ($CanView) {
				$stm = $sql->prepare(
						'SELECT TSK_TST_ID,TST_SUPPORT_SUB,TSK_TITLE,TSK_DESCRIPTION,TSK_REPEATABLE,TSK_DOCUMENT,TSK_UPDATED '.
						'FROM pro_tasks '.
						'JOIN pro_task_types ON TST_ID = TSK_TST_ID '.
						'WHERE TSK_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$TSKID);
					$stm->execute();
					$stm->bind_result($TSKTSTID,$TSTSupportSub,$TSKTitle,$TSKDesc,$TSKRepeat,$TSKDoc,$TSKUpdated);
					$stm->fetch();
					$stm->free_result();
				}
				else $PTVFailError .= 'ProTask view failed preparing to get task details.<br />';
				echo("<article><h3>$TSKTitle</h3>");
				echo('<p>This task <em>is ');
				if ($TSKRepeat <> 1)
					echo('not ');
				echo('repeatable</em>.</p>');
				if ($TSKDesc > '')
					echo("<p>$TSKDesc</p>");
				if ($TSKDoc > '')
					echo($TSKDoc.'<br />');
				
				if ($TSTSupportSub) {
					echo('<div id="protask'.$TSKID.'subtasks">');
					$For = 'STN';	
					$PID = $TSKID;
					$Target = 'protask'.$TSKID.'subtasks';
					require(dirname(__FILE__).'/subtask_nest.php');
					echo('</div><br />');
				}
				
				$LTP = 5;
				$ID = $TSKID;
				require(dirname(__FILE__).'/../reqful/reqful_both.php');
				
				echo('<h4>ProTask "'.$TSKTitle.'" Links</h4>'.
						'<div id="protask'.$TSKID.'links">');
				
				$For = 'ALN';
				$OLT = 5;
				$Target = 'protask'.$TSKID.'links';
				require(dirname(__FILE__).'/../linking/all_links.php');
				echo('</div><br />');
			
				if (strlen($PTVFailError) > 0)
					echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$PTVFailError");
			}
			else echo('You only have the right to view ProTasks your account is linked by, which does not include the requested ProTask.');
		}
		else echo('You do not have the required access rights to view ProTasks.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>ProTask View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$PTVDieError.
			'</article>');
}
?>
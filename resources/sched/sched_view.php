<?php
//XCal - We only want to pay attention to request variables if they're for the group view page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'SCV')) {
	$For = 'SCV';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$SCVTarget = $_REQUEST['T'];

	if (isset($_REQUEST['ID']))
		$SCVID = $_REQUEST['ID'];
	else $SCVID = -1;
}
elseif (isset($For) and ($For == 'SCV')) {
	if (isset($Target))
		$SCVTarget = $Target;
	if (isset($ID))
		$SCVID = $ID;
	elseif (! isset($SCVID))
		$SCVID = -1;
}
$SCVPath = 'resources/sched/sched_view.php';
$SCVDieError = '';
$SCVFailError = '';

//XCal - Let's make sure we have the values we need to run the page
if (! isset($SCVTarget))
	$SCVDieError .= 'Schedule view has not been told where to target its displays and thus would not be safe to run.<br />';

if ($SCVDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$SCHSec = GetAreaSecurity($sql, 5);
		if ($SCHSec['SearchView'] > 0) {
			$IsLinked = ItemLinked($sql,8,$SCVID);
			if ($SCHSec['SearchView'] == 2)
				$CanView = true;
			else $CanView = $IsLinked;
			
			if ($CanView) {
				$stm = $sql->prepare(
						'SELECT SCT_NAME,SCI_START,SCI_END,SCI_BREAKS,SCI_UPDATED '.
						'FROM sch_schedule_items '.
						'JOIN sch_schedule_types ON SCT_ID = SCI_SCT_ID '.
						'WHERE SCI_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$SCVID);
					if ($stm->execute()) {
						$stm->bind_result($SCTName,$SCIStart,$SCIEnd,$SCIBreaks,$SCIUpdated);
						$GotRes = $stm->fetch();					
						$stm->free_result();
					}
					else $SCVFailError .= 'Schedule view failed fetching task details.<br />';
				}
				else $SCVFailError .= 'Schedule view failed preparing to fetch task details.<br />';
				if ($GotRes) {
					echo("<article><h3>$SCTName Details</h3>");		
					$TStart = strtotime($SCIStart);
					$TEnd = strtotime($SCIEnd);
					$TBreaks = strtotime($SCIBreaks);
					$TUpdated = strtotime($SCIUpdated);
					echo('<p>Start: '.date('jS M Y H:i:s',$TStart).'</p>'.
							'<p>End: '.date('jS M Y H:i:s',$TEnd).'</p>'.
							'<p>Breaks: '.date('z',$TBreaks).' days, '.date('H',$TBreaks).' hours and '.date('i',$TBreaks).' minutes</p>'.
							'<p>Last Updated: '.date('Y-m-d H:i:s',$TUpdated));
					if (($SCHSec['NewMod'] == 2) or (($SCHSec['NewMod'] == 1) and $IsLinked))
						echo(' <input type="button" class="button" value="Modify" onclick="'.
							"AJAX('$SCVTarget','resources/sched/sched_mod.php','For=SCM&T=$SCVTarget&M=M&ID=$SCVID');".
							'" />');
					echo('</p>');
					
					$LTP = 8;
					$ID = $SCVID;
					require(dirname(__FILE__).'/../reqful/reqful_both.php');
					
					echo('<h4>Schedule Links</h4>'.
							'<div id="sched'.$SCVID.'links">');
					
					$For = 'ALN';
					$OLT = 8;
					$Target = 'sched'.$SCVID.'links';
					require(dirname(__FILE__).'/../linking/all_links.php');
					echo('</div><br />');
					
					echo('<h4>Schedule Linked by...</h4>'.
							'<div id="schedlinked">');
					$For = 'ALD';
					$CLT = 8;		
					$Target = 'schedlinked';
					require(dirname(__FILE__).'/../linking/all_linked.php');
					echo('</div>');
				}
				else echo('No schedule item to display.');
			
				if (strlen($SCVFailError) > 0)
					echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$SCVFailError");
			}
			else echo('You only have the right to view schedule items your account is linked by, which does not include the requested item.');
		}
		else echo('You do not have the required access right to view schedule items.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Schedule View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$SCVDieError.
			'</article>');
}
?>
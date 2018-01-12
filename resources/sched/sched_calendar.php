<?php
//XCal - We only want to pay attention to request variables if they're for the calendar page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'SCC')) {
	$For = 'SCC';
	//XCal - [XC:T|Target:String(The DOM Element ID of the container);;]
	if (isset($_REQUEST['T']))
		$SCCTarget = $_REQUEST['T'];

	//XCal - [XC:OLT|Owner Link Type: The owner link type ID;;]
	if (isset($_REQUEST['ST']))
		$SCCTypes = $_REQUEST['ST'];
	if (isset($_REQUEST['LTP']))
		$SCCLTPID = $_REQUEST['LTP'];
	if (isset($_REQUEST['LID']))
		$SCCLinkID = $_REQUEST['LID'];
	if (isset($_REQUEST['ID']))
		$SCCID = $_REQUEST['ID'];
	//XCal - Supported values [XC:M|Mode:{M},D,W,Y|Month(Default),Day,Week,Year;]
	if (isset($_REQUEST['M']))
		$SCCMode = $_REQUEST['M'];
	elseif (! isset($SCCMode))
		$SCCMode = 'M';
	if (isset($_REQUEST['DAY']))
		$SCCDay = $_REQUEST['DAY'];
	//XCal - Variables that are reset if they aren't passed in
	if (isset($_REQUEST['RO']))
		$Offset = $_REQUEST['RO'];
	else $Offset = 0;
	if (isset($_REQUEST['RC']))
		$Records = $_REQUEST['RC'];
	else $Records = 10;		
}
elseif (isset($For) and ($For == 'SCC')) {
	if (isset($Target))
		$SCCTarget = $Target;
	
	if (isset($LTP))
		$SCCLTPID = $LTP;
	if (isset($ID))
		$SCCID = $ID;	
	if (! isset($Offset))
		$Offset = 0;
	if (! isset($Records))
		$Records = 10;
}
else {			
	$Offset = 0;
	$Records = 10;
}
if (! isset($SCCMode))
	$SCCMode = 'M';

$SCCPath = 'resources/sched/sched_calendar.php';
$SCCDieError = '';
$SCCFailError = '';
$SCCModes = array('M','D','W'); //XCal - Add 'Y' again if year is re-implemented

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($SCCID))
	$SCCID = 0;
	//$SCCDieError .= 'Schedule calendar view has not been passed the owner identifier to display links for.<br />';
if (! isset($SCCTarget))
	$SCCDieError .= 'Schedule calendar view has not been told where to target its displays and thus would not be safe to run.<br />';
if (! in_array($SCCMode,$SCCModes))
	$SCCDieError .= "Schedule calendar view has been passed a mode ($SCCMode) which it does not support.<br />";
if ((! $SCCDieError == '') and (isset($For) and ($For <> 'SCC')))
	$SCCDieError .= "These problems might be because params are marked as for '$For' and need to be for 'SCC'.<br />";
	

if ($SCCDieError == '') {
	$SCCKeys = "For=SCC&T=$SCCTarget&ID=$SCCID";
	//$_SESSION['DebugStr'] .= "Schedule Calendar: Running with keys $SCCKeys on child $SCCSCIID<br />";
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$SCHSec = GetAreaSecurity($sql, 5);
		
		if ($SCHSec['SearchView'] > 0) {
			if (isset($SCCTypes))
				$SCCTypeAR = str_getcsv($SCCTypes,',');
			$WeekStart = 1; //XCal - Start on a Monday
			if ($WeekStart == 0)
				$WeekEnd = 6;
			else $WeekEnd = $WeekStart-1;
			
			if (isset($SCCDay))
				$CalDate = strtotime($SCCDay);
			else $CalDate = strtotime(date('Y-m-d'));
			$Today = strtotime(date('Y-m-d'));
			$Year = date('Y',$CalDate);
			$Month = date('m',$CalDate);
			$Day = date('d',$CalDate);	
			$SDate = date_create();
			$EDate = date_create();
			//XCal - Init. by mode
			switch ($SCCMode) {
				case 'M':
					//XCal - Get the first and last days of the month we're in
					$SDay = strtotime("$Year-$Month-01");
					$EDay = strtotime("$Year-$Month-".date('t',$CalDate));
					date_date_set($SDate,$Year,$Month,1);				
					date_date_set($EDate,$Year,$Month,date('t',$CalDate));
					//XCal - Get the days of the week for the month start and end
					$StartDoW = date('w',$SDay);
					$EndDoW = date('w',$EDay);
					//XCal - Calculate the actual first and last days to be shown
					if ($StartDoW > $WeekStart)
						date_sub($SDate,date_interval_create_from_date_string($StartDoW-$WeekStart.' days'));
					elseif ($StartDoW < $WeekStart)
						date_sub($SDate,date_interval_create_from_date_string((7-($WeekStart-$StartDoW)).' days'));
					if ($WeekEnd > $EndDoW)
						date_add($EDate,date_interval_create_from_date_string($WeekEnd-$EndDoW.' days'));
					elseif ($WeekEnd < $EndDoW)
						date_add($EDate,date_interval_create_from_date_string((7+($WeekEnd-$EndDoW)).' days'));
				break;
				
				case 'W':
					date_date_set($SDate,$Year,$Month,$Day);
					date_date_set($EDate,$Year,$Month,$Day);
					//XCal - Get the day of the week for the selected day
					$DoW = date('w',$CalDate);
					//XCal - Calculate the actual first and last days to be shown
					if ($DoW > $WeekStart)
						date_sub($SDate,date_interval_create_from_date_string($DoW-$WeekStart.' days'));
					elseif ($DoW < $WeekStart)
						date_sub($SDate,date_interval_create_from_date_string((7-($WeekStart-$DoW)).' days'));
					if ($WeekEnd > $DoW)
						date_add($EDate,date_interval_create_from_date_string($WeekEnd-$DoW.' days'));
					elseif ($WeekEnd < $DoW)
						date_add($EDate,date_interval_create_from_date_string((7+($WeekEnd-$DoW)).' days'));
				break;
				
				/*XCal - Decided against year for now, awkward display + could be slow
				case 'Y':
					$SDay = strtotime("$Year-01-01");
					$EDay = strtotime("$Year-12-".date('t',$CalDate));				
					date_date_set($SDate,$Year,1,1);
					date_date_set($EDate,$Year,12,31);
					//XCal - Get the days of the week for the month start and end
					$StartDoW = date('w',$SDay);
					$EndDoW = date('w',$EDay);
					//XCal - Calculate the actual first and last days to be shown
					if ($StartDoW > $WeekStart)
						date_sub($SDate,date_interval_create_from_date_string($StartDoW - $WeekStart.' days'));
					elseif ($StartDoW < $WeekStart)
					date_sub($SDate,date_interval_create_from_date_string((7-($StartDoW-$WeekStart)).' days'));
					if ($WeekEnd > $EndDoW)
						date_add($EDate,date_interval_create_from_date_string($WeekEnd - $EndDoW.' days'));
					elseif ($WeekEnd < $EndDoW)
					date_add($EDate,date_interval_create_from_date_string((7+($WeekEnd - $EndDoW)).' days'));			
				break;*/
				
				default:
					date_date_set($SDate,$Year,$Month,$Day);
					$EDate = $SDate;
				break;
			}
			
			date_time_set($SDate,0,0,0);
			$FirstDay = date_timestamp_get($SDate);
			date_time_set($EDate,23,59,59);
			$LastDay = date_timestamp_get($EDate);
			$Diff = date_diff($SDate,$EDate);
			
			echo('View: ');
			if ($SCCMode == 'D')
				echo('Day');
			else echo('<input type="button" class="button" value="Day" onclick="'."AJAX('$SCCTarget','$SCCPath','$SCCKeys&M=D');".'" />');
			if ($SCCMode == 'W')
				echo('Week');
			else echo('<input type="button" class="button" value="Week" onclick="'."AJAX('$SCCTarget','$SCCPath','$SCCKeys&M=W');".'" />');
			if ($SCCMode == 'M')
				echo('Month');
			else echo('<input type="button" class="button" value="Month" onclick="'."AJAX('$SCCTarget','$SCCPath','$SCCKeys&M=M');".'" />');
			/*XCal - Not currently going to support year
			 * if ($SCCMode == 'Y')
				echo('Year');
			else echo('<input type="button" class="button" value="Year" onclick="'."AJAX('$SCCTarget','$SCCPath','$SCCKeys&M=Y');".'" />');*/
			
			echo('<br />Date <input type="date" id="SCH_JUMP" value="'.date('Y-m-d',$CalDate).'" />'.
					'<input type="hidden" id="SCH_MODE" value="'.$SCCMode.'" /><br />');
					
			$stm = $sql->prepare('SELECT SCT_ID,SCT_NAME FROM sch_schedule_types WHERE SCT_SR = 0');
			if ($stm) {
				if ($stm->execute()) {
					$stm->bind_result($SCTID,$SCTName);
					echo('Types:');
					while ($stm->fetch()) {
						echo('&nbsp;<input type="checkbox" name="SCCType" value="'.$SCTID.'" ');
						if (isset($SCCTypeAR)) {						
							if (in_array($SCTID,$SCCTypeAR))
								echo('checked ');
						}
						else echo('checked ');
						echo('/>'.$SCTName);
					}
					$stm->free_result();
				}
				else $SCCFailError .= 'Schedule calendar failed preparing to get schedule types ('.$sql->error.').<br />';
			}
			else $SCCFailError .= 'Schedule calendar failed preparing to get schedule types ('.$sql->error.').<br />';
			
			echo(' <input type="button" class="button" value="Refresh" onclick="'.
					"JumpDay('$SCCTarget')".
					'" /><br />');
			
			echo('Displaying '.date('Y-m-d',$FirstDay).' to '.date('Y-m-d',$LastDay).'<br />');
			$Days = date_interval_format($Diff,'%a');
			$Days++;
			$CurDate = date_create();
			echo('<table><tr>');
			//XCal - Build the week header if week or month view
			if ($SCCMode != 'D') {
				date_date_set($CurDate, date('Y',$FirstDay), date('m',$FirstDay), date('d',$FirstDay));		
				for ($i = 0; $i < 7; $i++) {
					$CurDay = date_timestamp_get($CurDate);
					echo('<th>'.date('l',$CurDay).'</th>');
					date_add($CurDate,date_interval_create_from_date_string('1 day'));			
				}
			}		
			date_date_set($CurDate, date('Y',$FirstDay), date('m',$FirstDay), date('d',$FirstDay));
			date_time_set($CurDate, 0, 0, 0);
			
			//XCal - Fetch records for the date range to insert while building calendar
			if (isset($CalItems))
				unset($CalItems);
			$stm = $sql->prepare('SELECT ?,?,?,? INTO @SDate, @EDate, @SCT, @LID');
			if ($stm) {
				$FDStr = date('Y-m-d H:i:s',$FirstDay);
				$LDStr = date('Y-m-d H:i:s',$LastDay);
				$stm->bind_param('ssii',$FDStr,$LDStr,$SCCLTPID,$SCCLinkID);
				if ($stm->execute()) {
					$stm->free_result();
					$SQuery = 'SELECT SCI_ID,SCT_NAME,SCI_START,SCI_END,SCT_COLOUR '.
							'FROM sch_schedule_items '.
							//'LEFT OUTER JOIN lnk_links ON ((LNK_ONR_LTP_ID = @LTP AND SCI_ID = LNK_ONR_ID) '.
								//'OR (LNK_CHD_LTP_ID = @LTP AND SCI_ID = LNK_CHD_ID)) '.							
							'JOIN sch_schedule_types ON SCT_ID = SCI_SCT_ID '.
							'WHERE ((SCI_START BETWEEN @SDate AND @EDate) OR (SCI_END BETWEEN @SDate AND @EDate) '.
								'OR (SCI_START <= @SDate AND SCI_END >= @EDate)) ';
					if (isset($SCCTypes))
						$SQuery .= "AND SCI_SCT_ID IN ($SCCTypes) ";
								//'AND ((LNK_ONR_LTP_ID = @LTP AND LNK_ONR_ID = @LID) OR (LNK_CHD_LTP_ID = @LTP AND LNK_CHD_ID = @LID)) '.
					$SQuery .= 'ORDER BY SCI_START,SCI_END';
					if ($stm->prepare($SQuery)) {
						//$stm->bind_param('iiii',$SCCTypes,$SCCID,$SCCTypes,$SCCID);
						if ($stm->execute()) {
							$stm->bind_result($SCIID,$SCIType,$SCIStart,$SCIEnd,$SCIColour);
							$i = 0;
							while ($stm->fetch()) {
								$CalItems[$i]['ID'] = $SCIID;
								$CalItems[$i]['Type'] = $SCIType;
								$CalItems[$i]['Start'] = $SCIStart;
								$CalItems[$i]['End'] = $SCIEnd;
								$CalItems[$i]['Colour'] = $SCIColour;
								$i++;
							}					
							$stm->free_result();
							//$SCCFailError .= "Counted $i results from FirstDay $FirstDay and LastDay $LastDay<br />";
						}
						else $SCCFailError .= 'Schedule failed checking the date range for items.<br />';
					}
					else $SCCFailError .= 'Schedule failed preparing to check the date range for items.<br />';
				}
				else $SCCFailError .= 'Schedule failed to initialise checking the date range for items.<br />';
			}
			else $SCCFailError .= 'Schedule failed preparing to initialise checking the date range for items.<br />';
			
			//XCal - Populate the days for the range selected
			echo('<tr>');
			$WeekEnds = array(0,6);
			$TomDate = date_create();	
			for ($i = 0; $i < $Days; $i++) {
				$CurDay = date_timestamp_get($CurDate);
				date_date_set($TomDate,date('Y',$CurDay),date('m',$CurDay),date('d',$CurDay));
				date_time_set($TomDate,0,0,0);
				date_add($TomDate,date_interval_create_from_date_string('1 day'));
				$TomDay = date_timestamp_get($TomDate);
				if (date('d',$CurDay) == 1)
					$DateFmt = 'M d';
				else $DateFmt = 'd';
				echo('<td id="'.date('Y_m_d',$CurDay).'"');
				if ($CurDay == $Today)
					echo(' style="background-color: #99cc99"');
				elseif (in_array(date('w',$CurDay),$WeekEnds))
					echo(' style="background-color: #ffffcc"');
				$DayStr = date('Y-m-d',$CurDay).'%2009:00:00';			
				echo('><input type="button" class="button" value="'.date($DateFmt,$CurDay).'" onclick="'.
						"AJAX('scheditem','resources/sched/sched_mod.php','For=SCM&T=scheditem&M=N&BEG=$DayStr&END=$DayStr');".
						'" />');
				
				//XCal - Check for calendar items
				if (isset($CalItems)) {
					$j = 0;
					$Max = sizeof($CalItems);			
					$Continue = true;
					while ($j < $Max and $Continue) {				
						$IStart = strtotime($CalItems[$j]['Start']);
						$IEnd = strtotime($CalItems[$j]['End']);
						//echo("$IStart | $IEnd | $CurDay | $TomDay");					
						if (($IStart <= $CurDay and $IEnd >= $CurDay) or ($IStart > $CurDay and $IStart < $TomDay)
								or ($IEnd > $CurDay and $IEnd < $TomDay))
							echo('<div style="background-color: '.$CalItems[$j]['Colour'].'">'.
									'<input type="button" class="button" value="^" onclick="'.
									"AJAX('scheditem','resources/sched/sched_view.php','For=SCV&T=scheditem&ID=".$CalItems[$j]['ID']."');".
									'" />'.
									$CalItems[$j]['Type'].'</div>');
						$Continue = $CalItems[$j]['Start'] < $TomDay;
						$j++;
					}
				}
				echo('</td>');
				if (($SCCMode == 'M') and (($i+1) % 7 == 0))
					echo('</tr><tr>');
				date_add($CurDate,date_interval_create_from_date_string('1 day'));
			}
			echo('</tr></table><div id="scheditem"></div>');
			
			if (strlen($SCCFailError) > 0)
				echo("Sorry to trouble you, but there were some issues you may want to know about.<br />$SCCFailError");
		}
		else echo('You do not have the required access rights to view schedule items.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
			'<article>'.
			'<h2>Calendar View Code Problem</h2>'.
			'<div class="article-info">'.
			'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$SCCDieError.
			'</article>');
}
?>
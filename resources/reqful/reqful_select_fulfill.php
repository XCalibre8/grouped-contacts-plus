<?php
if (isset($_REQUEST['LTP']))
	$RQFLinkType = $_REQUEST['LTP'];
if (isset($_REQUEST['ID']))
	$RQFID = $_REQUEST['ID'];
if (isset($_REQUEST['FLT']))
	$RQFFLTID = $_REQUEST['FLT'];
if (isset($_REQUEST['FLM']))
	$RQFFLMID = $_REQUEST['FLM'];
if (isset($_REQUEST['PX']))
	$RQFPrefix = $_REQUEST['PX'];
if (isset($_REQUEST['DPX']))
	$RQFDivPrefix = $_REQUEST['DPX'];	
if (isset($RQFFLTID)) {
	if (! isset($RQFFLMID))
		$RQFFLMID = 0;
	if (! isset($RQFPrefix))
		$RQFPrefix = 'FLP';
	if (! isset($RQFDivPrefix))
		$RQFDivPrefix = 'fulfill';
	
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');	
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$stm = $sql->prepare('SELECT FLM_ID,FLM_NAME FROM rqf_fulfillments WHERE FLM_FLT_ID = ? AND FLM_MODE = 0');
		if ($stm) {
			$stm->bind_param('i',$RQFFLTID);
			if ($stm->execute()) {
				$stm->bind_result($FLMID,$FLMName);
				unset($Fetched);
				while ($stm->fetch()) {
					if (! isset($Fetched)) {
						$Fetched = true;
						echo('<select id="'.$RQFPrefix.'_FLM_ID" onchange="'."SelectLevels('$RQFLinkType','$RQFID','$RQFPrefix','$RQFDivPrefix')\">");
					}
					if ($RQFFLMID == 0)
						$RQFFLMID = $FLMID;
					echo('<option value="'.$FLMID.'"');
					if ($FLMID == $RQFFLMID)
						echo(' selected');
					echo(">$FLMName</option>");
				}
				$stm->free_result();
				if (isset($Fetched))
					echo('</select>');
				else echo('Basic Count Checks');
			}
			else echo('Select fulfill encountered an error getting fulfillments for selection ('.$stm->error.').<br />');
		}
		else echo('Select fulfill encountered an error preparing to get fulfillments for selection ('.$sql->error.').<br />');
	}
	else echo('You must be logged in to view this data.');
}
else echo('Select fulfill code problem. Fulfillment type not set to list fulfillments for.');
?>
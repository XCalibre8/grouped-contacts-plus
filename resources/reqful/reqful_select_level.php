<?php
if (isset($_REQUEST['LTP']))
	$RQFLinkType = $_REQUEST['LTP'];
if (isset($_REQUEST['ID']))
	$RQFID = $_REQUEST['ID'];
if (isset($_REQUEST['FLM']))
	$RQFFLMID = $_REQUEST['FLM'];
if (isset($_REQUEST['PX']))
	$RQFPrefix = $_REQUEST['PX'];
if (isset($RQFFLMID)) {
	if (! isset($RQFFLLID))
		$RQFFLLID = 0;
	if (! isset($RQFPrefix))
		$RQFPrefix = 'FLP';
	
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql, $TokenValid);
	if ($TokenValid) {
		$stm = $sql->prepare('SELECT FLL_ID,FLL_NAME FROM rqf_fulfillment_levels WHERE FLL_FLM_ID = ?');
		if ($stm) {
			if ($RQFFLMID == 'NULL')
				$RQFFLMID = NULL;
			$stm->bind_param('i',$RQFFLMID);
			if ($stm->execute()) {
				$stm->bind_result($FLLID,$FLLName);
				unset($Fetched);
				while ($stm->fetch()) {
					if (!isset($Fetched)) {
						$Fetched = true;
						echo('<select id="'.$RQFPrefix.'_FLL_ID">');
					}
					if ($RQFFLLID == 0)
						$RQFFLLID = $FLLID;
					echo('<option value="'.$FLLID.'"');
					if ($FLLID == $RQFFLLID)
						echo(' selected');
					echo(">$FLLName</option>");
				}
				$stm->free_result();
				if (isset($Fetched))
					echo('</select>');
				else echo('N/A');
			}
			else echo('Select fulfillment levels encountered an error getting levels for selection ('.$stm->error.').<br />');
		}
		else echo('Select fulfillment levels encountered an error preparing to get levels for selection ('.$sql->error.').<br />');
	}
	else echo('You must be logged in to view this data.');
}
else echo('Select fulfillment level code problem. Fulfillment not set to list levels for.');
?>
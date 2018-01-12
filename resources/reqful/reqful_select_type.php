<?php
if (isset($_REQUEST['LTP']))
	$RQFLinkType = $_REQUEST['LTP'];
if (isset($_REQUEST['ID']))
	$RQFID = $_REQUEST['ID'];
if (isset($_REQUEST['ALT']))
	$RQFApplyLinkType = $_REQUEST['ALT'];
if (isset($_REQUEST['FLT']))
	$RQFFLTID = $_REQUEST['FLT'];
if (isset($_REQUEST['PX']))
	$RQFPrefix = $_REQUEST['PX'];
if (isset($_REQUEST['DPX']))
	$RQFDivPrefix = $_REQUEST['DPX'];
if (isset($RQFApplyLinkType)) {
	if (! isset($RQFFLTID))
		$RQFFLTID = -1;
	if (! isset($RQFPrefix))
		$RQFPrefix = 'FLP';
	if (! isset($RQFDivPrefix))
		$RQFDivPrefix = 'fulfill';
	
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$stm = $sql->prepare('SELECT FLT_ID,FLT_NAME FROM rqf_fulfillment_types WHERE FLT_FOR_LTP_ID = ? '.
							'AND EXISTS(SELECT FLM_ID FROM rqf_fulfillments WHERE FLM_FLT_ID = FLT_ID AND FLM_MODE = 0)');
		if ($stm) {
			$stm->bind_param('i',$RQFApplyLinkType);
			if ($stm->execute()) {
				$stm->bind_result($FLTID,$FLTName);
				echo('<select id="'.$RQFPrefix.'_FLT_ID" onchange="'.
							"SelectFulfillments('$RQFLinkType','$RQFID','$RQFPrefix','$RQFDivPrefix')".
							'">');
				if ($RQFPrefix == 'REQ') {
					echo('<option value="NULL"');
					if ($RQFFLTID == 'NULL')
						echo(' selected');
					echo('>Min Count</option>');
					echo('<option value="0"');
					if ($RQFFLTID == '0')
						echo(' selected');
					echo('>Max Count</option>');
				}
				while ($stm->fetch()) {
					if ($RQFFLTID == -1)
						$RQFFLTID = $FLTID;
					echo('<option value="'.$FLTID.'"');
					if ($FLTID == $RQFFLTID)
						echo(' selected');
					echo(">$FLTName</option>");
				}
				$stm->free_result();
				echo('</select>');
			}
			else echo('Select fulfillment type encountered an error getting types for selection ('.$stm->error.').<br />');
		}
		else echo('Select fulfillment type encountered an error preparing to get types for selection ('.$sql->error.').<br />');
	}
	else echo('You must be logged in to view this data.');
}
else echo('Select fulfillment type code problem. Link type not set to list fulfillment types for.');
?>
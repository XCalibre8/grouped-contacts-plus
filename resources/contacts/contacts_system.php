<?php
//XCal - We only want to pay attention to request variables if they're for the contact system page
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'CON')) {
	$For = 'CON';
	if (isset($_REQUEST['Cfg']) and ($_REQUEST['Cfg'] == 1)) {
		$Cfg = true;
	}
	else $Cfg = false;
	if (isset($_REQUEST['T']))
		$CONTarget = $_REQUEST['T'];
}
else {		
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	$Cfg = false;
	//XCal - If the $For variable is set to CON then the generic variables have been qualified by the calling page (theoretically)
	if (isset($For) and ($For == 'CON')) {
		if (isset($Target))
			$CONTarget = $Target;
	}
}
$CONPath = 'resources/contacts/contacts_system.php';
$CONDieError = '';

//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($CONTarget))
	$CONDieError .= 'The contacts system has not been told where to target its displays and thus would not be safe to run.<br />';

if ($CONDieError == '') {
	require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$CONSec = GetAreaSecurity($sql, 1);
		//XCal - If we're not in config mode just display the stats
		if (! $Cfg) {
			$sql->multi_query(
				'SELECT COUNT(PER_ID) FROM con_people WHERE PER_SR = 0;'.
				'SELECT COUNT(ADR_ID) FROM con_addresses WHERE ADR_SR = 0;'.
				'SELECT COUNT(CNP_ID) FROM con_contact_points WHERE CNP_SR = 0;'.
				'SELECT COUNT(TIT_ID) FROM con_titles WHERE TIT_SR = 0;'.
				'SELECT COUNT(CTT_ID) FROM con_contact_types WHERE CTT_SR = 0;'.
				'SELECT COUNT(CPT_ID) FROM con_contact_point_types WHERE CPT_SR = 0;');
	
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$PERCount = $row[0];
			$sql->next_result();
			
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$ADRCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$CNPCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$TITCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$CTTCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$CPTCount = $row[0];
			
			echo(
				'<article>'.
					'<h2>Contacts System Information</h2>'.
					'<div class="article-info">'.
					'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
					'</div>'.
					"<p>The contacts system currently stores <em>$PERCount people</em>, <em>$ADRCount addresses</em> and <em>$CNPCount contact points</em>. ".
					"It also has <em>$TITCount</em> possible titles for people, <em>$CTTCount</em> ways to classify a contact and <em>$CPTCount contact methods</em>.</p>".
				'</article>');
			
			if ($CONSec['Config'])
				echo('<input type="button" class="button" value="Configure Contacts System" onclick="'.
						"AJAX('$CONTarget','$CONPath','For=CON&T=$CONTarget&Cfg=1');".
						'" /><br /><br />');
				
		}
		elseif ($CONSec['Config']) { //XCal - Configuration Mode
			
			echo(
				'<input type="button" class="button" value="Finish Contacts Configuration" onclick="'.
				"AJAX('$CONTarget','$CONPath','For=CON&T=$CONTarget');".
				'" />'.
				'<article>'.
					'<h2>Contacts Configuration</h2>'.
					'<p>Manage and maintain information relating to the contacts system here.</p>'.
				'</article>');
	
			echo(
				'<article>'.
					'<h4>Contact Types</h4>'.
					'<p>These allow you to customise the types which can be selected on addresses and contact points.</p>'.	
					'<div id="contacttypes">');
			$CTTTarget = 'contacttypes';
			require(dirname(__FILE__).'/contact_types.php');
			echo(
					'</div>'.
				'</article>');
				
			echo(
				'<article>'.
					'<h4>Contact Methods</h4>'.
					'<p>These allow you to customise the contact methods which can be selected on contact points.</p>'.
					'<div id="contactmethods">');
			$CPTTarget = 'contactmethods';
			require(dirname(__FILE__).'/contact_methods.php');
			echo(
					'</div>'.
				'</article>');
			
			echo(
				'<article>'.
					'<h4>Titles</h4>'.
					'<p>These allow you to customise the titles which can be selected against people records.</p>'.
					'<div id="contacttitles">');
			$TITTarget = 'contacttitles';
			require(dirname(__FILE__).'/contact_titles.php');
			echo(
					'</div>'.
				'</article>');
			echo('<input type="button" class="button" value="Finish Contacts Configuration" onclick="'.
				"AJAX('$CONTarget','$CONPath','For=CON&T=$CONTarget');".
				'" /><br /><br />');
		}
		else echo('You do not have the required access rights to configure the contacts system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>Contacts System Code Problem</h2>'.
			'<div class="article-info">'.
				'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$CONDieError.
		'</article>');
}
?>
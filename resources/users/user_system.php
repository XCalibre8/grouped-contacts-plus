<?php
if (isset($_REQUEST['For']) and ($_REQUEST['For'] == 'USS')) {
	$For = 'USS';
	if (isset($_REQUEST['Cfg']) and ($_REQUEST['Cfg'] == 1)) {
		$Cfg = true;
	}
	else $Cfg = false;
	if (isset($_REQUEST['T']))
		$USSTarget = $_REQUEST['T'];
}
else {		
	//XCal - If we haven't been passed a request we should just check and [initialise:UK;initialize:US;] variables we need here
	$Cfg = false;
	if (isset($For) and ($For == 'USS')) {
		if (isset($Target))
			$USSTarget = $Target;
	}
}
$USSPath = 'resources/users/user_system.php';
$USSDieError = '';


//XCal - Security checks should go here, users should have a session array var for each system area loaded at login
// Security Placeholder
//XCal - Let's make sure we have the values we need to run the page
if (! isset($USSTarget))
	$USSDieError .= 'The users system has not been told where to target its displays and thus would not be safe to run.<br />';

if ($USSDieError == '') {
	if (! function_exists('CheckToken'))
		require(dirname(__FILE__).'/../web/web_security.php');
	CheckToken($sql,$TokenValid);
	if ($TokenValid) {
		$USRSec = GetAreaSecurity($sql, 6);
		//XCal - If we're not in config mode just display the stats
		if (! $Cfg) {
			$sql->multi_query(
				'SELECT COUNT(ROL_ID) FROM usr_roles WHERE ROL_SR = 0;'.
				'SELECT COUNT(ACA_ID) FROM usr_access_areas;'.
				'SELECT COUNT(USR_ID) FROM usr_users WHERE USR_SR = 0;'.
				'SELECT COUNT(URL_ROL_ID) FROM usr_user_roles;'.
				'SELECT COUNT(RAC_ROL_ID) FROM usr_role_access;');
	
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$ROLCount = $row[0];
			$sql->next_result();
			
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$ACACount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$USRCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$URLCount = $row[0];
			
			$sql->next_result();
			$res = $sql->store_result();
			if (! $res)
				echo('Error in SQL: '.$sql->error);
			$row = $res->fetch_array();
			$RACCount = $row[0];
								
			echo(
				'<article>'.
					'<h2>User System Information</h2>'.
					'<div class="article-info">'.
					'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
					'</div>'.
					"<p>The user system currently stores <em>$ROLCount roles</em> and <em>$USRCount users</em> with <em>$URLCount assigned roles</em>. ".
					"There are <em>$ACACount securable areas</em> and <em>$RACCount role access definitions</em>. ".
				'</article>');
			
			if ($USRSec['Config'])
				echo('<input type="button" class="button" value="Configure User System" onclick="'.
						"AJAX('$USSTarget','$USSPath','For=USS&T=$USSTarget&Cfg=1');".
						'" /><br /><br />');
				
		}
		elseif ($USRSec['Config']) { //XCal - Configuration Mode
			echo(
				'<input type="button" class="button" value="Finish User Configuration" onclick="'.
				"AJAX('$USSTarget','$USSPath','For=USS&T=$USSTarget');".
				'" />'.
				'<article>'.
					'<h2>User Configuration</h2>'.
					'<p>Manage and maintain user roles, role access and users here.</p>'.
				'</article>');
	
			echo(
				'<article>'.
				'<input type="button" class="button" value="User Roles" onclick="'.
				"AJAX('userconfig','resources/users/user_roles.php','For=ROL&T=userconfig&M=C');".
				'" /><input type="button" class="button" value="Users" onclick="'.
				"AJAX('userconfig','resources/users/users.php','For=USR&T=userconfig&M=C');".
				'" /><input type="button" class="button" value="Web Accounts" onclick="'.
				"AJAX('userconfig','resources/users/web_users.php','For=WUS&T=userconfig');".
				'" />'.
				'<div id="userconfig">');
			$ROLTarget = 'userconfig';
			$ROLMode = 'C';
			require(dirname(__FILE__).'/user_roles.php');
			echo(
					'</div>'.
				'</article>');
				
			echo('<input type="button" class="button" value="Finish User Configuration" onclick="'.
				"AJAX('$USSTarget','$USSPath','For=USS&T=$USSTarget');".
				'" /><br /><br />');
		}
		else echo('You do not have the required access rights to configure the user system.');
	}
	else echo('You must be logged in to view this data.');
}
else {
	echo(
		'<article>'.
			'<h2>User System Code Problem</h2>'.
			'<div class="article-info">'.
				'Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>'.
			'</div>'.
			'<p>I\'m terribly sorry about the inconvenience, but it appears a web developer has asked me for this page incorrectly. '.
			'Please contact someone and let them know about the following problems...</p>'.
			$USSDieError.
		'</article>');
}
?>
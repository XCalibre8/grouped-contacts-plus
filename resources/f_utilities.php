<?php
function CheckSession() {
	if (! isset($_SESSION)) {
		session_start();
		echo('Session Started');
	}
	 if (!isset($_SESSION['LoginToken'])) {
		$_SESSION['LoginToken'] = ' ';
		echo('Token Blanked');
	}
};
function CheckConnection(&$Connection) {
	if ((! isset($Connection)) or (! is_resource($Connection))) {
		require(dirname(__FILE__).'/get_settings.php');
		$Connection = new mysqli($config['dbhost'],$config['dbuser'],$config['dbpass'],$config['dbcat'],$config['dbport']);
	}
};
?>
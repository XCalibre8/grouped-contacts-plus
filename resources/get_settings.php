<?php
/*
 * XCal - To make this dynamic make a new file in the following format

return array(
	'var1'=> 'value1',
	'var2'=> 'value2',
);

* This file can then be called with

	$config = include('filename.php');

* Which will initialise the array variable
* Changes can then saved by calling the following

	file_put_contents('filename.php', '<?php return ' . var_export($config, true) . ';');

 */
	if (! isset($config))
		$config = array (
			'dbhost'=> 'dbhost',
			'dbuser'=> 'dbuser',
			'dbpass'=> 'dbpassword',
			'dbcat'=> 'dbcatalog',
			'dbport'=> 3306
		);

?>

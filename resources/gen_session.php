<?php
if (! isset($_SESSION) or is_resource($_SESSION))
	session_start();
if (!isset($_SESSION['LoginToken']))
	$_SESSION['LoginToken'] = ' ';
/* XCal - We may want to support sessions without cookies in future
if (SID != '') 
{
	$_COOKIE = true;
    if ($_COOKIE['CanCookie'] == true)
        $_SESSION['nocookie'] = false;
    else
        $_SESSION['nocookie'] = true;
}
else
{
	if ($_COOKIE['CanCookie'] == true)
		$_SESSION['nocookie'] = false;
	else
		$_SESSION['nocookie'] = true;
}
*/	
?>
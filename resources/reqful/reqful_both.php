<?php
echo('<div id="reqful_require">');
$For = 'RQR';
$Target = 'reqful_require';
require(dirname(__FILE__).'/../reqful/reqful_require.php');
echo('</div>');

echo('<div id="reqful_fulfill">');
$For = 'RQF';
$Target = 'reqful_fulfill';
require(dirname(__FILE__).'/../reqful/reqful_fulfill.php');
echo('</div>');	
?>
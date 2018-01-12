<?php
function ProcessXC ($XC) {
	switch ($XC) {
		case 'INTRO':
			echo('<article><center><h2>XCalibre8 Unlimited<br />Code X<br />XC</h2>'.
					'<p>XC is the following...'.
					'<ul><li>Two letters, coincidentally \'parallel\' in the alphabet (3rd from start, 3rd from end)</li>'.
					'<li>Extensible Coding (or eXtendable Code, etc)</li>'.
					'<li>The first two letters of on of my favorite user handle, XCalibre8</li>'.
					'<li>The first part of my "Code X" project</li>'.
					'<li>A coding codex system as opposed to a coding library</li>'.
					'<li>Intended to allow users to customise their experience</li>'.
					'<li>Not Ready Yet!</li>'.
					'</ul>'.
					'</p></center></article>');
		break;
		case 'TPL':
			require('resources/linking/type_links.php');
		break;

		default:
			;
		break;
	}
}
//XCal - [XC:RQM|RequestMode(G|Get,P|Post,M|Mixed);]
if (isset($RQM)) unset($RQM);
if (isset($_GET)) $RQM='G';
if (isset($_POST)){
	if (isset($RQM)) $RQM='M';
	else $RQM='P';
}
if ((sizeof($_REQUEST) > 0) or (isset($_SESSION['XCS']))): ?>
<?php
	if (isset($_REQUEST['X']))//XCal:Check for an X request
		$X=$_REQUEST['X'];
	else $X = $_SESSION['XCS'];//XCal:Check the (X)(C)ommand(S)tack
	if (isset($X))
		ProcessXC($X);
	else echo'<a href="xc.php?X=Echo">Except</a>';
?>
<?php else: ?>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript" src="resources/x/x.js"></script>
	<script type="text/javascript" src="resources/js/xcal_ajax.js"></script>
	<title>XC Code X</title>
	</head>
	<body>
		<h3>This AJAX page only responds to XC requests.</h3>
		<div id="XD">
			<input type="button" value="Request Introduction" onclick="x('','INTRO');" />
		</div>
	</body>
<?php endif; ?>

<header>
	<div class="width">
		<h1><a href="xcalibre8.php">XCalibre8.Me</a></h1>

	    <nav>
    		<ul class="sf-menu dropdown">
    		<?php    		
    			$PageName = basename($_SERVER['PHP_SELF']);

    			if ($PageName == 'index.php')
    				$IndexSel = ' class="selected"';
    			else $IndexSel = '';
    			if ($PageName == 'login.php')
    				$LoginSel = ' class="selected"';
    			else $LoginSel = '';
    			if ($PageName == 'register.php')
    				$RegisterSel = ' class="selected"';
    			else $RegisterSel = '';
    			if (in_array($PageName,array('people.php','contacts.php','groups.php','protasks.php','schedule.php')))
    				$SystemSel = ' class="selected"';
    			else $SystemSel = '';
    			if (in_array($PageName,array('account.php','logout.php')))
    				$AccountSel = ' class="selected"';
    			else $AccountSel = '';
    			if ($PageName == 'config.php')
    				$ConfigSel = ' class="selected"';
    			else $ConfigSel = '';
    			
    			echo("<li$IndexSel><a href=\"index.php\">Home</a></li>");
    			    			
				//if ((! isset($_SESSION['LoginToken'])) or ($_SESSION['LoginToken'] == ' ')) {
				CheckToken($sql,$TokenValid);
				if (! $TokenValid) {
					echo("<li$LoginSel><a href=\"login.php\">Log In</a></li>".
						"<li$RegisterSel><a href=\"register.php\">Register</a></li>");
				}
				else {
					echo("<li$SystemSel><a href=\"#\">Systems</a><ul>");
					//XCal - Groups Menu if available
					echo("<li><a href=\"groups.php\">Groups</a>".
						'</li>');
					//XCal - Contact Menu if available 
					echo("<li><a href=\"contacts.php\">Contacts</a>".
						'<ul>');
					echo('<li><a href="people.php">People</a></li>');
					if ($PageName == 'people.php')
						echo('<li><input type="button" value="New Person" onclick="AJAX('."'results','resources/contacts/person_mod.php','For=PRM&T=results&ID=0&M=N');".'" /></li>');
					echo('</ul></li>');
					//XCal - ProTasks
					echo('<li><a href="protasks.php">ProTasks</a></li>');
					echo('<li><a href="schedule.php">Schedule</a></li>');
					echo('</ul></li>');
					echo("<li$ConfigSel>".'<a href="config.php">Config</a></li>');
					echo("<li$AccountSel>".'<a href="account.php">Account</a><ul><li><a href="logout.php">Log Out</a></li></ul></li>');	
				}
				?>        		
        	</ul>
	
    	</nav>
    </div>

	<div class="clear"></div>
</header>
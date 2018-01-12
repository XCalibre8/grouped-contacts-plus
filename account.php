<?php require('resources/gen_session.php'); 
?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');
	?>	
	<script type="text/javascript" src="resources/js/ajax_linking.js"></script>
	<script type="text/javascript" src="resources/js/ajax_contacts.js"></script>
	<script type="text/javascript" src="resources/js/ajax_web.js"></script>
	<body>
	<div id="container">	
		<?php 
		require('resources/web/web_security.php');
		
		$WebAccID = ValidateToken($sql, $_SESSION['LoginToken']);
		require('resources/gen_menu.php');
		
	    echo('<div id="body" class="width">'.	
			'<section id="content" class="two-column with-right-sidebar">');
			
		if ($WebAccID > 0) {
			if (isset($_REQUEST['ID']))
				$ID = $_REQUEST['ID'];
			else $ID = $WebAccID;
			
			if ($ID == $WebAccID) {
				CheckWebAccount($sql);				
				if (isset($_SESSION['WACUserName'])) {
					$WACUserName = $_SESSION['WACUserName'];
					$WACEmail = $_SESSION['WACEmail'];
					$WACPERID = $_SESSION['WACPERID'];
					$WACUSRID = $_SESSION['WACUSRID'];
					$WACUpdated = $_SESSION['WACUpdated'];				
				}
			}
			else {
				$stm = $sql->prepare(
						'SELECT WAC_USERNAME,WAC_EMAIL,WAC_PER_ID,WAC_USR_ID,WAC_UPDATED,WAC_SR '.
						'FROM web_accounts '.
						'WHERE WAC_ID = ?');
				if ($stm) {
					$stm->bind_param('i',$ID);
					if ($stm->execute()) {
						$stm->bind_result($WACUserName,$WACEmail,$WACPERID,$WACUSRID,$WACSR,$WACUpdated);
						$stm->fetch();
						$stm->free_result();
					}
					else echo('Web accounts encountered an error fetching account details.<br />');
				}
				else echo('Web accounts encountered an error preparing to get account details.<br />');
			}
			if (isset($WACUserName)) {
				if ($WACUSRID > 0) {
					$stm = $sql->prepare('SELECT USR_USERNAME FROM usr_users WHERE USR_ID = ?');
					if ($stm) {
						$stm->bind_param('i',$WACUSRID);
						if ($stm->execute()) {
							$stm->bind_result($USRUserName);
							$stm->fetch();
							$stm->free_result();
						}
					}
				}
		    	echo('<article>'.
					"<h2>Web Account \"$WACUserName\"</h2>".
					'<p><div id="accountemail">');
			
				if (strlen($WACEmail) > 0) 
					echo("Email: $WACEmail ");
				else echo('There is no email address set on the account. ');
				
				if ($WebAccID == $ID)
					echo('<input type="button" class="button" value="Modify" onclick="'.
							"AJAX('accountemail','resources/web/account_email.php','T=accountemail&M=M&ID=$WebAccID');".
							'" />');
								
				echo('</div></p>'.
					'<p><div id="accountperson">');
				$For = 'PED';
				if (isset($WACPERID)) 
					$ID = $WACPERID;
				else $ID = 0;
				$Target = 'accountperson';
				$Mode = 'V';
				$ExtraP = 'AddWAC='.$WebAccID;
				require('resources/contacts/person_embed.php');
					
				echo('</div>'.
					'</p>'.
					'<p>');
				 
				if ($WACUSRID > 0)
					echo("User: <a href=\"users.php?ID=$WACUSRID\">$USRUserName</a>");					
				else echo('Your web account does not have systems access.');
				
				echo('</p>'.
					'</article>');
			}
			
			echo('<h4>Account Links...</h4>'.
					'<div id="webaccountlinks">');
			
			$For = 'ALN';
			$OLT = 6;
			$ID = $ID;
			$ExPER = $WACPERID;
			$Target = 'webaccountlinks';
			require('resources/linking/all_links.php');
			echo('</div>'.				
	        	'</section>'.
	        	'<aside class="sidebar big-sidebar right-sidebar">');
				
			require('resources/gen_sidebar.php');
						
	        echo('</aside>'.
	    		'<div class="clear"></div>');
	    }
	    else echo('You must be logged in to view this page.');
	    
		echo('</div>');
	    
    	require('resources/gen_footer.php')
?>
	</div>
	</body>
</html>
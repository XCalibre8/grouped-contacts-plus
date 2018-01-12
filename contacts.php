<?php require('resources/gen_session.php') ?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');
	require('resources/web/web_security.php');
	?>
	<script type="text/javascript" src="resources/js/ajax_contacts.js"></script>
	<script type="text/javascript" src="resources/js/ajax_linking.js"></script>
	<script type="text/javascript" src="resources/js/ajax_reqful.js"></script>
	<body>
	<div id="container">
		<?php
			//CheckToken($sql,$TokenValid);
			require('resources/gen_menu.php');
			if ($TokenValid) :
		?>
		<div id="body" class="width">
			<section id="content" class="two-column with-right-sidebar">
			
			<?php				
				$CONSec = GetAreaSecurity($sql, 1);
				if (isset($_REQUEST['V'])) : 
			?>
			<div id="viewport">
			<?php
				if ($CONSec['SearchView'] > 0) {
					if ($CONSec['SearchView'] == 2)
						$CanView = true;
					elseif (isset($CanView))
						unset($CanView);
										
					$Target = 'viewport';
					$ID = $_REQUEST['ID'];
					
					switch ($_REQUEST['V']) {
						case 'P':
							if (! isset($CanView))
								$CanView = ItemLinked($sql,1,$ID);
							if ($CanView) {
								$For = 'PRV';
								require('resources/contacts/person_view.php');
							}
							else echo('You only have the right to view contacts your account is linked by, which does not include the requested person.');
						break;
						
						case 'A':
							if (! isset($CanView))
								$CanView = ItemLinked($sql,2,$ID);
							if ($CanView) {
								$For = 'ADV';
								require('resources/contacts/address_view.php');
							}
							else echo('You only have the right to view contacts your account is linked by, which does not include the requested address.');
						break;
						
						case 'C':
							if (! isset($CanView))
								$CanView = ItemLinked($sql,3,$ID);
							if ($CanView) {
								$For = 'CNV';
								require('resources/contacts/contactpoint_view.php');
							}
							else echo('You only have the right to view contacts your account is linked by, which does not include the requested contact point.');
						break;
							
						default:
							echo('An unsupported view request has been made to the contacts system.');
						break;
					}
				}
				else echo('You do not have the required access rights to view contact details.');
			?>
			</div>
			<?php else:
				if ($CONSec['SearchView'] > 0) {
					$sql->multi_query(
						'SELECT COUNT(PER_ID) FROM con_people WHERE PER_SR = 0;'.
						'SELECT COUNT(ADR_ID) FROM con_addresses WHERE ADR_SR = 0;'.
						'SELECT COUNT(CNP_ID) FROM con_contact_points WHERE CNP_SR = 0;');
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
				
					echo('<article>'.
							'<h2>People Information</h2>'.
							'<div class="article-info">Generated on <time datetime="'.date('Y-m-d H:i:s').'">'.date('d M Y H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a></div>'.			 
							"<p>There are currently <em>$PERCount people</em>, <em>$ADRCount addresses</em> and <em>$CNPCount contact points</em> in the system.</p>".
							'<a href="people.php" class="button">View/Search People</a>'.
						'</article>');
				}
				else echo('You do not have the required access rights to view contact details.');
		?>
		<?php endif; ?>
	        
	        </section>
	        
	        <aside id="sidebar" class="sidebar big-sidebar right-sidebar">
			
			<?php 
				require('resources/gen_sidebar.php');
			?>	
		
	        </aside>
	    	<div class="clear"></div>
	    </div>
	    
	    <?php 
	    	else:
	    		echo('You must be logged in to view this page.');
	    	endif;
	    	
	    	require('resources/gen_footer.php');
	    	$sql->close();
	    ?>

	</div>
	</body>
</html>
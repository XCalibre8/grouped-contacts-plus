<?php require('resources/gen_session.php') ?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');	
	?>
	<script type="text/javascript" src="resources/js/ajax_contacts.js"></script>
	<script type="text/javascript" src="resources/js/ajax_linking.js"></script>
	<script type="text/javascript" src="resources/js/ajax_reqful.js"></script>
	<body>
	<div id="container">
		<?php
			require('resources/web/web_security.php');
			require('resources/gen_menu.php'); ?>
		<div id="body" class="width">
			<section id="content" class="two-column with-right-sidebar">
			
			<?php				
				if ($TokenValid) {
					$USRSec = GetAreaSecurity($sql, 6);
					
					if ($USRSec['SearchView'] > 0) {
						if ($USRSec['SearchView'] == 2)
							$CanView = true;
						else {
							$USRLinked = ItemLinked($sql, 9, $ID);
							$CanView = $USRLinked;
						}
						
						if ($CanView) {
							if (isset($_REQUEST['ID']))
								$ID = $_REQUEST['ID'];
							if (isset($ID)) {
								$stm = $sql->prepare(
										'SELECT USR_USERNAME,USR_UPDATED,USR_SR '.
										'FROM usr_users '.
										'WHERE USR_ID = ?');
								if ($stm) {
									$stm->bind_param('i',$ID);
									if ($stm->execute()) {
										$stm->bind_result($UserName,$UserUpdated,$UserSuppressed);
										if ($stm->fetch()) {
											echo('<h2>User Details</h2>'.
													'<table><tr><td class="fieldname">Username</td>'.
													"<td>$UserName</td></tr>".
													'<tr><td class="fieldname">Last Updated</td>'.
													"<td>$UserUpdated</td>");
											if ($UserSuppressed == 1)
												echo('<tr><td class="fieldname">Suppressed</td>'.
														'<td>This user has been suppressed but retained for historic purposes.</td></tr>');
											echo('</table>');
										}
										else echo('The requested user was not found in the system.');
										$stm->free_result();
									}
									else echo('The users page failed attempting to get the requested user details.');
								}
								else echo('The users page failed preparing to get the requested user details.');
								
								if (isset($UserName)) {
									$For = 'UHR';
									$UHRTarget = 'userroles';
									echo('<div id="userroles">');
									require(dirname(__FILE__).'/resources/users/user_has_roles.php');
									echo('</div>');
									
									$For = 'UWA';
									$UWATarget = 'userwebaccounts';
									echo('<div id="userwebaccounts">');
									require(dirname(__FILE__).'/resources/users/user_web_accounts.php');
									echo('</div>');
									
									echo('<h4>User Links...</h4>'.
											'<div id="userlinks">');
									
									$For = 'ALN';
									$OLT = 9;
									$Target = 'userlinks';
									require('resources/linking/all_links.php');
									echo('</div>');
								}
							}
							else {
								echo('The users page does not currently support listing or searching for users.');
							}
						}
						else echo('You may only view users your account is linked by, which does not include the requested user.');
					}
					else echo('You do not have the required access rights to view user details.');					
				}
			?>
	        
	        </section>
	        
	        <aside id="sidebar" class="sidebar big-sidebar right-sidebar">
			
			<?php 
				require('resources/gen_sidebar.php');
			?>	
		
	        </aside>
	    	<div class="clear"></div>
	    </div>
	    
	    <?php 
	    	require('resources/gen_footer.php');
	    	$sql->close();
	    ?>

	</div>
	</body>
</html>
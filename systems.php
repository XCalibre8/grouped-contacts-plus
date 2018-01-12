<?php require('resources/gen_session.php'); ?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');
	require('resources/web/web_security.php');
	?>
	<script type="text/javascript" src="resources/js/ajax_linking.js"></script>
	<script src="resources/ckeditor/vendor/ckeditor/ckeditor.js"></script>
	<body>	
	<div id="container">
		<?php require('resources/gen_menu.php'); ?>	    
	    <div id="body" class="width">	
			<section id="content" class="two-column with-right-sidebar">
			
			<?php
				if ($TokenValid)
					$TSKSec = GetAreaSecurity($sql, 8);
				if ($TokenValid and ($TSKSec['SearchView'] > 0)) :
					if (isset($_REQUEST['ID'])) :
			?>
					<div id="viewport">
					<?php
						$ID = $_REQUEST['ID'];
						if ($TSKSec['SearchView'] == 2)
							$CanView = true;
						else $CanView = ItemLinked($sql, 5, $ID);
						if ($CanView) {
							$For = 'SYV';
							$Target = 'viewport';						
							require('resources/systems/system_view.php');
						}
						else echo('You only have the right to view systems your account is linked by, which does not include the requested system.');
					?>
					</div>
					<?php
					else: 
						if ($TSKSec['SearchView'] == 2) :	
					?>	
						<article>
							<h2>Systems</h2>							
							<p>View all systems or select systems by groups which contain them or </p>
							<div id="systems">
							<?php 
 							//XCal - I'd like this to display groups which contain systems and "Ungrouped" for system selection
								$SYLTarget = 'systems';
								require('resources/systems/systems_list.php');						
							?>
							</div>
						</article>
					<?php
						else:
							echo('You only have the right to view systems your account is linked by, which does not allow system browsing.');
						endif;
					endif;
				else:
					if (! $TokenValid)
						echo('You must be logged in to view this page!');
					else echo('You do not have the required access rights to view systems.');
				endif;
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
	    ?>

	</div>
	</body>
</html>
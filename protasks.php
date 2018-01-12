<?php require('resources/gen_session.php'); ?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');
	require('resources/web/web_security.php');
	?>
	<script type="text/javascript" src="resources/js/ajax_linking.js"></script>
	<script type="text/javascript" src="resources/js/ajax_protasks.js"></script>
	<script src="resources/ckeditor/vendor/ckeditor/ckeditor.js"></script>
	<body>	
	<div id="container">
		<?php require('resources/gen_menu.php'); ?>	    
	    <div id="body" class="width">	
			<section id="content" class="two-column with-right-sidebar">
			
			<?php
				if ($TokenValid)
					$TSKSec = GetAreaSecurity($sql, 3);
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
							$For = 'PTV';
							$Target = 'viewport';						
							require('resources/protasks/protask_view.php');
						}
						else echo('You only have the right to view tasks your account is linked by, which does not include the requested task.');
					?>
					</div>
					<?php
					else: 
						if ($TSKSec['SearchView'] == 2) :	
					?>	
						<article>
							<h2>ProTasks</h2>
							<p>Please select a task type to view or add tasks.</p>
							<div id="protasktypes">
							<?php 
								$PTPTarget = 'protaskstypes';
								require('resources/protasks/protask_types.php');						
							?>
							</div>
						</article>
					<?php
						else:
							echo('You only have the right to view tasks your account is linked by, which does not allow task browsing.');
						endif;
					endif;
				else:
					if (! $TokenValid)
						echo('You must be logged in to view this page!');
					else echo('You do not have the required access rights to view tasks.');
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
<?php require('resources/gen_session.php'); ?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');
	require('resources/web/web_security.php');
	?>
	<script type="text/javascript" src="resources/js/ajax_linking.js"></script>
	<script type="text/javascript" src="resources/js/ajax_sched.js"></script>	
	<body>	
	<div id="container">
		<?php require('resources/gen_menu.php'); ?>	    
	    <div id="body" class="width">	
			<section id="content" class="two-column with-right-sidebar">
			
			<?php 
			if ($TokenValid)
				$SCHSec = GetAreaSecurity($sql, 8);
			if ($TokenValid and ($SCHSec['SearchView'] > 0)) :	
				if (isset($_REQUEST['ID'])) :
?>
					<div id="viewport">
<?php
						$For = 'SCV';
						$Target = 'viewport';
						$ID = $_REQUEST['ID'];
						require('resources/sched/sched_view.php');
?>
					</div>
<?php 
				else:
?>	
						<article>
							<h2>Schedule</h2>
							<div id="sched_calendar">
<?php
								$For = 'SCC';
								$Target = 'sched_calendar';
								//$LTP = 1;
								//$ID = 1;
								require('resources/sched/sched_calendar.php');						
?>
							</div>
						</article>
<?php
				endif;
			else :
				if (! $TokenValid)
					echo('You must be logged in to view this page!');
				else echo('You do not have the required access rights to view schedules.');
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
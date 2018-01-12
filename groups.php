<?php require('resources/gen_session.php') ?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');
	require('resources/web/web_security.php');
	?>
	<script type="text/javascript" src="resources/js/ajax_groups.js"></script>
	<script type="text/javascript" src="resources/js/ajax_linking.js"></script>
	<script type="text/javascript" src="resources/js/ajax_reqful.js"></script>
	<script src="resources/ckeditor/vendor/ckeditor/ckeditor.js"></script>
	<body>	
	<div id="container">
		<?php			
			require('resources/gen_menu.php');
			if ($TokenValid) :
		?>	    
	    <div id="body" class="width">	
			<section id="content" class="two-column with-right-sidebar">			

			<?php
				$GRPSec = GetAreaSecurity($sql,2);
				if ($GRPSec['SearchView'] > 0) {
					if ($GRPSec['SearchView'] == 2)
						$CanView = true;
				}
				if (isset($_REQUEST['ID'])) :
			?>
			<div id="viewport">
			<?php
				$ID = $_REQUEST['ID'];
				if (! isset($CanView))
					$CanView = ItemLinked($sql,4,$ID);
				if ($CanView) {
					$For = 'GPV';
					$Target = 'viewport';				
					require('resources/groups/group_view.php');
				}
				elseif ($GRPSec['SearchView'] == 1)
					echo('You only have the right to view groups your account is linked by, which does not include the requested group.');
				else echo('You do not have the required access rights to view group details.');
			?>
			</div>
			<?php else: ?>
			<?php
				if (isset($CanView)) { 
					echo('<article>'.
							'<h2>Group Types</h2>'.
							'<p>Please select a group type to view or add groups.</p>'.
							'<div id="grouptypes">');
					$GTPTarget = 'grouptypes';
					require('resources/groups/group_types.php');						
					
					echo('</div>'.
							'</article>'.
							'<div id="groups"></div>'.
							'<div id="selgroup"></div>');
				}
				elseif ($GRPSec['SearchView'] == 1)
					echo('You only have the right to view groups your account is linked by, which does not allow group browsing.');
				else echo('You do not have the required access rights to view groups.');
			?>
			<?php endif;?>	        
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
	    	require('resources/gen_footer.php')
	    ?>

	</div>
	</body>
</html>
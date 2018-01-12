<?php require('resources/gen_session.php'); ?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');
	?>
	<script type="text/javascript" src="resources/js/xcal_ajax.js"></script>
	<script type="text/javascript" src="resources/js/ajax_config.js"></script>
	<body>	
	<div id="container">
		<?php
		
		require('resources/web/web_security.php');
		
		CheckToken($sql,$TokenValid);
		
		require('resources/gen_menu.php');
		
		if ($TokenValid) :?>
	    
	    <div id="body" class="width">
	
			<section id="content" class="two-column with-right-sidebar">			
				<article>
					<h2>System Configuration</h2>
					<div class="article-info">
						Generated on <time datetime="<?php echo(date('Y-m-d H:i:s')); ?>"> <?php echo(date('d M Y H:i:s')); ?></time> by <a href="xcalibre8.php" rel="author">XCalibre8</a>
					</div>					
					<p>Please select a system core to view it's summary info. Configuration rights depend on your user access level.</p>
				</article>
				<input type="button" class="button" value="Contacts" onclick="AJAX('systemconfig','resources/contacts/contacts_system.php','For=CON&T=systemconfig');" />
				<input type="button" class="button" value="Groups" onclick="AJAX('systemconfig','resources/groups/groups_system.php','For=GRP&T=systemconfig');" />
				<input type="button" class="button" value="ProTasks" onclick="AJAX('systemconfig','resources/protasks/protask_system.php','For=PTS&T=systemconfig');" />
				<input type="button" class="button" value="Linking" onclick="AJAX('systemconfig','resources/linking/linking_system.php','For=LSY&T=systemconfig');" />
				<input type="button" class="button" value="Requirement Fulfillment" onclick="AJAX('systemconfig','resources/reqful/reqful_system.php','For=RFS&T=systemconfig');" />
				<input type="button" class="button" value="Scheduling" onclick="AJAX('systemconfig','resources/sched/sched_system.php','For=SCS&T=systemconfig');" />
				<input type="button" class="button" value="Users" onclick="AJAX('systemconfig','resources/users/user_system.php','For=USS&T=systemconfig');" />
				<div id="systemconfig">
				</div>

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
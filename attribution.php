<?php require('resources/gen_session.php') ?>
<!doctype html>
<html>
	<?php
	require('resources/web/web_security.php');
	require('resources/gen_head.php');
	?>
	<body>
	<div id="container">
		<?php
			require('resources/gen_menu.php');
		?>
	    
	    <div id="body" class="width">
	
			<section id="content" class="two-column with-right-sidebar">
			<?php
			//XCal - Replace the following articles up to /section with code to read and display article feed when ready			
			?>
				<article>
				<h2>Software Attributions</h2>
				<div class="article-info">Posted on <time datetime="2017-01-13">13 Jan 2017</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a></div>				
				<p>This section lists any attribution to component or content providers.</p>
				<ul>
					<li><a href="http://zypopwebtemplates.com/">Free HTML5 Templates</a> by ZyPOP - I've tweaked it a bit, but they had the best free CSS template I could find to start with.</li>
					<li><a href="http://ckeditor.com">CKEditor</a> - User HTML document editing is provided by the creators of CKEditor.</li> 
				</ul>
			</article>
			
	        </section>
	        
	        <aside class="sidebar big-sidebar right-sidebar">
				
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
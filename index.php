<?php require('resources/gen_session.php'); ?>
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
				<h2>Launch Day!</h2>
				<div class="article-info">Posted on <time datetime="2017-01-13">13 Jan 2017</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a></div>
	
	            <p>Well, today is launch day for the system! The starting modules are useable although 
	            certainly not fully finished. I'll be spending the next two and a half weeks putting it 
	            through it's paces and getting it looking and feeling a bit slicker.</p>
	            
	            <p>The system currently has working modules for the following core areas.</p>
	            
	            <ul>
	            	<li>Linking - This system core allows most items to link to each other in a configurable and unified manner.</li>
	            	<li>Contacts - Provides basic data storage for the following:
	            	<ul>
	            		<li>People</li>
	            		<li>Addresses</li>
	            		<li>Contact Points (Configurable: Phone,Email,Skype,etc)</li>
	            	</ul></li>
	            	<li>Groups - Configurable typed groups allow sets of items to be linked together</li>
	            	<li>ProTasks - Configurable typed tasks system allows projects and tasks to be stored with a status and note on items they link.</li>
	            	<li>Scheduling - Configurable typed schedules allow dates and times to link to other items.</li>
	            	<li>Users & Web Accounts - Provides system user and role based security while allowing web accounts 
	            	to be registered without gaining access to system information unless they are linked to a system user.</li>
	            </ul>
	            
	            <p>Many more changes and enhancements to come in all areas as well as further system cores 
	            for other industry areas.</p>
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
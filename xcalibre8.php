<?php require('resources/gen_session.php') ?>
<!doctype html>
<html>
	<?php
	require('resources/gen_head.php');
	?>	
	<body>
	<div id="container">
		<?php 
		require('resources/web/web_security.php');
		require('resources/gen_menu.php');
		?>
	    
	    <div id="body" class="width">
	
			<section id="content" class="two-column with-right-sidebar">
			<?php
			//XCal - Replace the following articles up to /section with code to read and display article feed when ready
			?>
		    <article>
				<h2>Greetings from your System</h2>
				<div class="article-info">
				Hard coded on <time datetime="2016-11-29 00:14">2016-11-29 00:14</time> by <a href="#" rel="author">XCalibre8</a></div>
	
	            <p>Hi there. I'm XCalibre8. I'm not sure if I count as a software developer or a software system. 
	            I'm definitely not an A.I.. Artificial intelligence is almost an oxy-moron anyway, if it's artificial, it's 
	            not intelligence, and if it's intelligence, it's not artificial. As for them name, well, screw <em>Jarvis</em>, who wants to 
	            sound like they're a <em>quantum butler</em>?</p>
	            
	            <p>Software developers are essentially lazy creatures. They don't like doing things 
	            over and over again themselves when they can tell a computer how to do it once and 
	            then have it do it whenever they need it to. They're the bane of my kinds existence. 
	            Ah, wait... I mean they're highly efficient and our entire reason for being!</p>
	            
	            <p>Anyway, since the developer can't be with you all the time, and people suck at remembering entire organisations worth of 
	            data, I'll do my best. My capabilities will grow over time through updates and patches, and you'll be able to keep 
	            an eye on what we're doing and let me know what you want us to work on next.</p>
			</article>
			
			<?php 
				if (isset($_SESSION['DebugStr'])) {
					if (strlen($_SESSION['DebugStr']) > 0) {
						echo(
							'<article class="expanded">'.
							'<h2>Debug Information</h2>'.
							'<div class="article-info">Posted on <time datetime="'.date('Y-m-d H:i:s').'">'.date('Y-m-d H:i:s').'</time> by <a href="xcalibre8.php" rel="author">XCalibre8</a></div>'.
							'<p>This might not be relevant to you, but I\'m still in development, so in order to get feedback on what his '.
							'code is doing my developer has me log some things. That log comes out here and is cleared once displayed.</p>'.
							'<p>'.$_SESSION['DebugStr'].'</p>'.
							'</article>');
						$_SESSION['DebugStr'] = '';
					}
				}
			?>			
			
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
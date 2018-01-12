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
		CheckToken($sql,$TokenValid);
		if ($TokenValid) {				
			$sql->multi_query("CALL sp_web_logout_token('".
					$_SESSION['LoginToken']."',@Code,@Message);".
					"SELECT @Code Code, @Message Message");
			if ($sql->errno > 0)
				echo('Error in SQL: '.$sql->error);
			$sql->next_result();
			if ($sql->errno > 0)
				echo('Error in SQL: '.$sql->error);
			$res = $sql->store_result();
			$row = $res->fetch_assoc();
			$Code = $row['Code'];
			$Message = $row['Message'];
			$res->free();
			$_SESSION['LoginToken'] = ' ';
			unset($TokenValid);
			$sql->close();
			session_destroy();
		}
		else {
			$Message = 'You need to be logged in before you can log out!';
		}		
		require('resources/gen_menu.php');
		?>
	    
	    <div id="body" class="width">
	
			<section id="content" class="two-column with-right-sidebar">
			<?php
				echo($Message);
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
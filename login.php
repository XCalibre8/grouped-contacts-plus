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
			$Code = 0;
			$UserName = '';
			$Email = '';
			$Password = '';
			if (isset($_REQUEST['UserName']) or isset($_REQUEST["Email"])) {
				$Password = $_REQUEST['Password'];
				if (isset($_REQUEST['UserName']))
				    $UserName = $_REQUEST['UserName'];
				if (isset($_REQUEST['Email']))
				    $Email = $_REQUEST['Email'];
				$UserError = '';
				$EmailError = '';
				$UserPassError = '';
				$EmailPassError = '';
				
				if (isset($_REQUEST['UserName'])) {
					if (strlen($UserName) < 3)
						$UserError = 'User name must be at least 3 characters.';
				}
				else {
					if (strlen($Email) < 8)
						$EmailError = 'So far as we know email addresses must be at least 8 character long (eg a@123.bc)';
				}		
				if (($EmailError == '') and ($UserError == '')) {
					CheckConnection($sql);
					$HashPass = hash('sha256', $_REQUEST['Password']);
					if (isset($_REQUEST['UserName'])) {
					  $sql->multi_query("CALL sp_web_login_username('$UserName','$HashPass',@Code,@Message,@Token);".
					  		"SELECT @Code Code, @Message Message, @Token Token");
					}
					else {
						$sql->multi_query("CALL sp_web_login_email('$Email','$HashPass',@Code,@Message,@Token);".
								"SELECT @Code Code, @Message Message, @Token Token");			
					}
					if ($sql->errno > 0)
						echo('Error in SQL: '.$sql->error);
					$sql->next_result();
					if ($sql->errno > 0)
						echo('Error in SQL: '.$sql->error);
					$res = $sql->store_result();
					$row = $res->fetch_assoc();
					$Code = $row['Code'];
					$Message = $row['Message'];
					$Token = $row['Token'];
					$res->free();
				    
					if ($Code > 0) {
						$_SESSION['LoginToken'] = $Token;
						//$_SESSION['DebugStr'] .= "Logged in, token set to $Token<br />";
					}	    
				    
					$sql->close();		
				}
			}
			else {
				$UserError = '';
				$UserPassError = '';
				$EmailError = '';
				$EmailPassError = '';
			}
			require('resources/gen_menu.php');			
		?>
	    
	    <div id="body" class="width">
	
			<section id="content" class="two-column with-right-sidebar">

			<?php
				if ($Code > 0) {
					echo($Message."<br /><br /><a href=\"index.php\">Return to main page</a>");
				}
				else {
					if ($Code < 0) {
						echo($Message."<br />");
					}
					if (! $TokenValid) {
						echo(
							'<article>'.
							'<h3>Login</h3>'.
							'<p>Please ensure your connection is secure by ensuring your address bar refers to an https '.
							'address unless this system is running on a private network.</p>'.
							'</article>'.
							'<div class="Login">'.
							'<table><tr><th>Log In by Username</th></tr>'.
							'<tr><td><form action="login.php" method="POST">'.
							'<table><tr>'.
							'<td class="fieldname">User Name</td>'.
							"<td><input type=\"text\" name=\"UserName\" value=\"$UserName\" /> ".$UserError."</td></tr>".
							'<tr><td class="fieldname">Password</td>'.
							"<td><input type=\"password\" name=\"Password\" value=\"$Password\" /></td></tr>".	
							'</table><tr><td><input type="submit" class="button" value="Log In" /></td></tr>'.
							'</form></table>'.
	// 					"<br />OR<br /><br />".
	// 					"Log In by Email<br />".
	// 					"<form action=\"login.php\">".
	// 					"Email Address: <input type=\"text\" name=\"Email\" value=\"$Email\" /> ".$EmailError."<br />".
	// 					"Password: <input type=\"password\" name=\"Password\" value=\"$Password\" /><br />".
	// 					"<input type=\"submit\" value=\"Register by Email\" />".
	// 					"</form>".
							'</div>');
					}
					else echo('You are already logged in!');
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
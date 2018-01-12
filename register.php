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
		$Code = 0;
		$UserName = '';
		$Email = '';
		$Password = '';
		$Password2 = '';
		if (isset($_REQUEST['UserName']) or isset($_REQUEST["Email"])) {
			$Password = $_REQUEST['Password'];
			$Password2 = $_REQUEST['Password2'];
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
			if (($Password == $Password2) and ($EmailError == '') and ($UserError == '')) {
				CheckConnection($sql);
					
				$HashPass = hash('sha256', $Password);
				if (isset($_REQUEST['UserName'])) {
					$sql->multi_query("CALL sp_web_register_username('$UserName','$HashPass',@Code,@Message,@Token);".
							"SELECT @Code Code, @Message Message, @Token Token");
				}
				else {
					$sql->multi_query("CALL sp_web_register_email('$Email','$HashPass',@Code,@Message,@Token);".
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
		
				$sql->close();
					
				if ($Code > 0) {
					if (isset($_REQUEST['UserName'])) {
						$_SESSION['LoginToken'] = $Token;
					}
					else {
						//XCal - Need to send an email to the user here
					}
				}
			}
			else {
				if (isset($_REQUEST['UserName']))
					$UserPassError = 'Passwords do not match.';
				else
					$EmailPassError = 'Passwords do not match.';
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
				echo($Message.'<br />You can create a person to go with your account from your <a href="account.php">account</a> page.'.
							'You might not want to yet though as those details would be visible to anyone who registers, and you saw how hard that was!');
			}
			else {
				echo("<body>");
				if ($Code < 0) {
					echo($Message."<br />");
				}
				echo(
					'<article>'.
					'<h3>Registration</h3>'.
					'<p>Please ensure your connection is secure by ensuring your address bar refers to an https '.
					'address unless this system is running on a private network.</p>'.
// 					'<p>Please note that choosing to register via email will require you to confirm receipt of an email.</p>'.
					'</article>'.
					'<table><tr><th>Register by Username</th>'.
// 					'<th>Register by Email</th>'.
					'</tr>'.
					"<tr><td><form action=\"register.php\" method=\"POST\">".
					'<table><tr>'.
					'<td class="fieldname">User Name</td>'.
					"<td><input type=\"text\" name=\"UserName\" value=\"$UserName\" /> $UserError</td></tr>".
					'<tr><td class="fieldname">Password</td>'. 
					"<td><input type=\"password\" name=\"Password\" value=\"$Password\" /></td></tr>".
					'<tr><td class="fieldname">Password (again)</td>'.
					"<td><input type=\"password\" name=\"Password2\" value=\"$Password2\" /> ".$UserPassError."</td></tr>".
					'</table><tr><td><input class="button" type="submit" value="Register by Username" /></td></tr>'.
// 					'</form>'.
// 					'<td>'.
// 					"<form action=\"register.php\" method=\"POST\">".
// 					'<table><tr>'.
// 					'<td class="fieldname">Email Address</td>'.
// 					"<td><input type=\"email\" name=\"Email\" value=\"$Email\" /> ".$EmailError."</td></tr>".
// 					'<tr><td class="fieldname">Password</td>'.
// 					"<td><input type=\"password\" name=\"Password\" value=\"$Password\" /></td></tr>".
// 					'<tr><td class="fieldname">Password (again)</td>'.
// 					"<td><input type=\"password\" name=\"Password2\" value=\"$Password2\" /> ".$EmailPassError."</td></tr>".
// 					'</table><br /><input class="button" type="submit" value="Register by Email" />'.
					'</form></table>');
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
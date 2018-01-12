<?php require('resources/gen_session.php'); ?>
<!doctype html>
<html>
	<?php
	require('resources/web/web_security.php');
	require('resources/gen_head.php');
	?>
	<script type="text/javascript" src="resources/js/ajax_contacts.js"></script>
	<script type="text/javascript" src="resources/js/ajax_linking.js"></script>
	<body>
	<div id="container">
		<?php 
		require('resources/gen_menu.php');
		
		if ($TokenValid)
			$CONSec = GetAreaSecurity($sql,1);
		
		if ($TokenValid and ($CONSec['SearchView'] > 0)) :
		?>
	    
	    <div id="body" class="width">
	
			<section id="content" class="two-column with-right-sidebar">
			
				<div id="search">.
					<form>
						<table>
							<tr><th></th><th>Find People</th><th></th></tr>
							<tr><td>Match Type<br />
							<select name="Type" id="SearchType">
								<option value="CONTAINS" selected>Contains</option>
								<option value="BEGINS">Begins</option>
								<option value="EXACT">Exact</option>
							</select>
							</td>
							<td><table>
								<tr><td class="fieldname">Forenames</td>
								<td><input id="SearchForenames" type="text" name="Forenames" /></td></tr>
								<tr><td class="fieldname">Surname</td>
								<td><input id="SearchSurname" type="text" name="Surname" /></td></tr>
							</table></td>
							<td>Records per page<br />
							<select id="SearchRecords" name="Records">
								<option value="10" selected>10</option>
								<option value="20">20</option>
								<option value="50">50</option>
							</select><br />
							<input class="prevbutton" type="button" value="Search" onclick="SearchPeople('results');"/>				
							<input class="nextbutton" type="reset" />
							</td></tr></table>
					</form>
				</div>
				
				<input class="button" type="button" value="New Person" onclick="AJAX('results','resources/contacts/person_mod.php','For=PRM&T=results&ID=0&M=N');" /><br /><br />
				
				<div id="results">
					<?php 
						//require('resources/contacts/people_search.php');
					?>
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
	    	else :
	    		if ($TokenValid)
	    			echo('You do not have the required access rights to view contacts.');
	    		else echo('You must be logged in to view this page!');
	    	endif;
	    	
	    	require('resources/gen_footer.php');
	    ?>

	</div>
	</body>
</html>
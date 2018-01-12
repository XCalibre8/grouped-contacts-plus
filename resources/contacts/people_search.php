<?php
require(dirname(__FILE__).'/../web/web_security.php');
CheckToken($sql,$TokenValid);
if ($TokenValid) {
	CheckWebAccount($sql);
	$CONSec = GetAreaSecurity($sql, 1);
	
	if ($CONSec['SearchView'] > 0) {
		//XCal - Initialise variables
		if (isset($_REQUEST['Type']))
			$Type = $_REQUEST['Type']; 
		else
			$Type = 'CONTAINS';
		
		if (isset($_REQUEST['Records']))
			$Records = $_REQUEST['Records'];
		else
			$Records = 10;
		
		if (isset($_REQUEST['Offset']))
			$Offset = $_REQUEST['Offset'];
		else
			$Offset = 0;
		
		if (isset($_REQUEST['Forenames']))
			$Forenames = $_REQUEST['Forenames'];
		else $Forenames = '';
		
		if (isset($_REQUEST['Surname']))
			$Surname = $_REQUEST['Surname'];
		else $Surname = '';
			
		$where = 'WHERE (PER_SR = 0) ';
		//XCal - Determine where clause by params
		if (!($Forenames == '') or !($Surname == '')) {
			if (! $Forenames == '') {
				$where .= "AND (PER_FORENAMES LIKE ? ";
				if (! $Surname == '')
					$where .= 'OR ';
			}
			if (! $Surname == '') {
				if ($Forenames == '')
					$where .= 'AND (';
				$where .= "PER_SURNAME LIKE ?) ";
			}
			else $where .= ')';
			if ($Type == 'BEGINS') {
				if (!($Forenames == ''))
					$UseForename = "$Forenames%";
				if (!($Surname == ''))
					$UseSurname = "$Surname%";
			}
			elseif ($Type == 'CONTAINS') {
				if (!($Forenames == ''))
					$UseForename = "%$Forenames%";
				if (!($Surname == ''))
					$UseSurname = "%$Surname%";
			}
			else {
				if (!($Forenames == ''))
					$UseForename = $Forenames;
				if (!($Surname == ''))
					$UseSurname = $Surname;
			}
		}
		
		
		$CQuery = 'SELECT COUNT(PER_ID) FROM con_people ';
		if ($CONSec['SearchView'] == 1)
			$CQuery .= 'JOIN lnk_links ON LNK_ONR_LTP_ID = 1 AND LNK_ONR_ID = PER_ID AND ((LNK_CHD_LTP_ID = 6 AND LNK_CHD_ID = ?) OR '.
					'(LNK_CHD_LTP_ID = 9 AND LNK_CHD_ID = ?)) ';		
		$CQuery .= $where;
		$Query =		
		'SELECT PER_ID,fn_con_fullname(PER_ID) FULLNAME,fn_con_sep_link_address(1,PER_ID,PER_ADR_ID,\', \') ADDRESS,'.
		'fn_con_full_link_contact_point(1,PER_ID,PER_CNP_ID) CONTACT,PER_ADR_ID,PER_CNP_ID '.
		'FROM con_people ';
		//XCal - Limit search to people who link the account or user if that's their access level 
		if ($CONSec['SearchView'] == 1)
			$Query .= 'JOIN lnk_links ON LNK_ONR_LTP_ID = 1 AND LNK_ONR_ID = PER_ID AND ((LNK_CHD_LTP_ID = 6 AND LNK_CHD_ID = ?) OR '.
				'(LNK_CHD_LTP_ID = 9 AND LNK_CHD_ID = ?)) ';
		
		$Query .= $where.'LIMIT ?,?';
		
		$stm = $sql->prepare($CQuery);
		if ($stm) {			
			if (isset($UseForename)) {
				if (isset($UseSurname)) {
					if ($CONSec['SearchView'] == 1) 
						$stm->bind_param('ssii',$UseForename,$UseSurname,$_SESSION['WebAccID'],$_SESSION['WACUSRID']);
					else $stm->bind_param('ss',$UseForename,$UseSurname);
				}
				elseif ($CONSec['SearchView'] == 1)
					$stm->bind_param('sii',$UseForename,$_SESSION['WebAccID'],$_SESSION['WACUSRID']);
				else $stm->bind_param('s',$UseForename);
			}
			elseif (isset($UseSurname)) {
				if ($CONSec['SearchView'] == 1)
					$stm->bind_param('sii',$UseSurname,$_SESSION['WebAccID'],$_SESSION['WACUSRID']);
				else $stm->bind_param('s',$UseSurname);
			}
			else {
				if ($CONSec['SearchView'] == 1)
					$stm->bind_param('ii',$_SESSION['WebAccID'],$_SESSION['WACUSRID']);
			}
			if ($stm->execute()) {
				$stm->bind_result($Count);
				$stm->fetch();			
				$stm->free_result();
			}
			else echo('People search encountered an error performing the search count ('.$stm->error.').');
		}
		else echo('People search encountered an error preparing the search count ('.$sql->error.').');
		
		if ($Count > 0) {
			echo("$Count people found.");
			$stm = $sql->prepare($Query);
			if ($stm) {
				if (isset($UseForename)) {
					if (isset($UseSurname)) {
						if ($CONSec['SearchView'] == 1)
							$stm->bind_param('ssiiii',$UseForename,$UseSurname,$_SESSION['WebAccID'],$_SESSION['WACUSRID'],$Offset,$Records);
						else $stm->bind_param('ssii',$UseForename,$UseSurname,$Offset,$Records);
					}
					elseif ($CONSec['SearchView'] == 1)
					$stm->bind_param('siiii',$UseForename,$_SESSION['WebAccID'],$_SESSION['WACUSRID'],$Offset,$Records);
					else $stm->bind_param('sii',$UseForename,$Offset,$Records);
				}
				elseif (isset($UseSurname)) {
					if ($CONSec['SearchView'] == 1)
						$stm->bind_param('siiii',$UseSurname,$_SESSION['WebAccID'],$_SESSION['WACUSRID'],$Offset,$Records);
					else $stm->bind_param('sii',$UseSurname,$Offset,$Records);
				}
				else {
					if ($CONSec['SearchView'] == 1)
						$stm->bind_param('iiii',$_SESSION['WebAccID'],$_SESSION['WACUSRID'],$Offset,$Records);
					else $stm->bind_param('ii',$Offset,$Records);
				}
				if ($stm->execute()) {
					echo('<table>');
					echo('<tr><th>Full Name</th><th>Address</th><th>Contact</th><th>View</th>');
					if ($CONSec['NewMod'] > 0)
						echo('<th>Edit</th>');
					echo('</tr>');
					$stm->bind_result($PERID,$FullName,$Address,$Contact,$PERADRID,$PERCNPID);
					while ($stm->fetch()) {
						echo(
								'<tr>'.
								"<td>$FullName</td>".
								"<td>$Address</td>".
								"<td>$Contact</td>".
								'<td><input class="button" type="button" value="View" onclick="'.
								"AJAX('results','resources/contacts/person_view.php','For=PRV&T=results&ID=$PERID');".
								'" /></td>');
						if ($CONSec['NewMod'] > 0)
							echo('<td><input class="button"type="button" value="Edit" onclick="'.
								"AJAX('results','resources/contacts/person_mod.php','For=PRM&T=results&ID=$PERID');".
								'" />');
						echo(
								'</td>'.
								'</tr>');
					}
					echo("</table>");
					if ($Offset > 0) {
						$PrevOffset = $Offset-$Records;
						echo('<input class="prevbutton" type="button" value="Previous" onclick="'.
								"AJAX('results','resources/contacts/people_search.php','Offset=$PrevOffset&Records=$Records&Type=$Type&Forenames=$Forenames&Surname=$Surname');".
								'" />');
					}
						
					if (($Offset + $Records) < $Count) {
						$NextOffset = $Offset+$Records;
						echo('<input class="nextbutton" type="button" value="Next" onclick="'.
								"AJAX('results','resources/contacts/people_search.php','Offset=$NextOffset&Records=$Records&Type=$Type&Forenames=$Forenames&Surname=$Surname');".
								'" />');
					}
					$stm->free_result();
				}
				else echo('People search encountered an error performing the search('.$stm->error.').');
			}
			else echo('People search encountered an error preparing the search ('.$sql->error.').');
		}
		else {
			echo('No people match your search.');
			if ($CONSec['SearchView'] == 1)
				echo(' This may be because your account can only view records it is linked by.');			
		}
	}
	else echo('You do not have the required access rights to view contact details.');
}
else echo('You must be logged in to view this page!');
?>
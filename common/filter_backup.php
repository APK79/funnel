<?php
//запрос по партнеру
$partner_query =  "SELECT DISTINCT fp.partner_id, fp.partner_no, fp.partner_name FROM funnel_list AS fl LEFT JOIN funnel_partner AS fp ON fl.list_pnm = fp.partner_id";
$partner_data = mysqli_query($dbc, $partner_query);								
$partners =[];
while ($row = mysqli_fetch_array($partner_data)) { 
	array_push($partners, $row);
}

//запрос по заказчику
$customer_query =  "SELECT DISTINCT fc.customer_id, fc.customer_llc, fc.customer_name FROM funnel_list AS fl LEFT JOIN funnel_customer AS fc ON fl.list_cnm = fc.customer_id";
$customer_data = mysqli_query($dbc, $customer_query);								
$customers =[];
while ($row = mysqli_fetch_array($customer_data)) { 
	array_push($customers, $row);
}

//запрос по линейке / названию производителя
$vendor_query =  "SELECT DISTINCT	fm.vendor_id, fm.vendor_line, fm.vendor_name FROM funnel_list AS fl LEFT JOIN funnel_manufacturer AS fm ON fl.list_vendor = fm.vendor_id";
$vendor_data = mysqli_query($dbc, $vendor_query);								
$vendors =[];
while ($row = mysqli_fetch_array($vendor_data)) { 
	array_push($vendors, $row);
}
//запрос по шансу
$chance_query =  "SELECT DISTINCT	fcan.chance_id, fcan.chance_num FROM funnel_list AS fl LEFT JOIN funnel_chance AS fcan ON fl.list_chance = fcan.chance_id ORDER BY fcan.chance_id DESC";
$chance_data = mysqli_query($dbc, $chance_query);								
$chances =[];
while ($row = mysqli_fetch_array($chance_data)) { 
	array_push($chances, $row);
}
//запрос по автору
$author_query =  "SELECT DISTINCT fus.user_id, fus.username, fus.first_name, fus.last_name FROM funnel_list AS fl LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id";
$author_data = mysqli_query($dbc, $author_query);								
$authors =[];
while ($row = mysqli_fetch_array($author_data)) { 
	array_push($authors, $row);
}
//запрос по департаменту
$dep_query =  "SELECT DISTINCT fdp.dep_id, fdp.dep_desc FROM funnel_list AS fl LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id LEFT JOIN funnel_dep AS fdp ON fus.department = fdp.dep_id";
$dep_data = mysqli_query($dbc, $dep_query);								
$departaments =[];
while ($row = mysqli_fetch_array($dep_data)) { 
	array_push($departaments, $row);
}

if (isset($_POST['submit'])){
	
	$filter_partner_name = mysqli_real_escape_string($dbc, trim($_POST['filter_partner_name'])); 	var_dump($filter_partner_name);
	$filter_customer_name = mysqli_real_escape_string($dbc, trim($_POST['filter_customer_name'])); 	
	$filter_line_name = mysqli_real_escape_string($dbc, trim($_POST['filter_line_name']));				
	$filter_chance = mysqli_real_escape_string($dbc, trim($_POST['filter_chance']));				
	$filter_author = mysqli_real_escape_string($dbc, trim($_POST['filter_author']));				
	$filter_dep = mysqli_real_escape_string($dbc, trim($_POST['filter_dep']));								
	
	if (!empty($filter_partner_name)){
		if ((!empty($filter_customer_name)) || (!empty($filter_line_name)) || (!empty($filter_dep)) || (!empty($filter_author)) || (!empty($filter_chance))){
			$filter_partner_query = "fp.partner_id = '$filter_partner_name' and ";
		}
	
		else{
			$filter_partner_query = "fp.partner_id = '$filter_partner_name'";
		}
	}
	else{
		$filter_partner_query = "";
	}
	if (!empty($filter_customer_name)){
		if ((!empty($filter_line_name)) || (!empty($filter_dep)) || (!empty($filter_author)) || (!empty($filter_chance))){
			$filter_customer_query = "fc.customer_id = '$filter_customer_name' and ";
		}
		else{
			$filter_customer_query = "fc.customer_id = '$filter_customer_name'";
		}
	}
	else{
		$filter_customer_query = "";
	}
	
		$query = "SELECT 
							fl.list_id, 
							DATE_FORMAT(list_jdate, '%d-%m-%Y') AS list_jdate, 
							fl.list_pnb, 
							fl.list_pnm, 
							fl.list_cnm, 
							fl.list_cllc,
							fl.list_vendor, 
							DATE_FORMAT(list_rdate, '%d-%m-%Y') AS list_rdate,
							fl.list_amount, 
							fl.list_curr, 
							fl.list_chance,
							fl.list_status,
							fl.list_pnum,
							fl.list_author,
							fl.list_desc,
							fl.list_old,
							fp.partner_id,
							fp.partner_no,
							fp.partner_name,
							fc.customer_id,
							fc.customer_llc,
							fc.customer_name,
							fm.vendor_id,
							fm.vendor_line,
							fm.vendor_name,
							fcur.currency_id,
							fcur.currency_name,
							fcan.chance_id,
							fcan.chance_num,
							fst.status_id,
							fst.status_desc,
							fus.user_id,
							fus.username,
							fus.first_name,
							fus.last_name,
							fus.department,
							fus.user_full,
							fus.approwed,
							fdp.dep_id,
							fdp.dep_desc
							FROM funnel_list AS fl
										LEFT JOIN funnel_partner AS fp ON fl.list_pnm = fp.partner_id
										LEFT JOIN funnel_customer AS fc ON fl.list_cnm = fc.customer_id
										LEFT JOIN funnel_manufacturer AS fm ON fl.list_vendor = fm.vendor_id
										LEFT JOIN funnel_currency AS fcur ON fl.list_curr = fcur.currency_id
										LEFT JOIN funnel_chance AS fcan ON fl.list_chance = fcan.chance_id
										LEFT JOIN funnel_status AS fst ON fl.list_status = fst.status_id
										LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id
										LEFT JOIN funnel_dep AS fdp ON fus.department = fdp.dep_id
										WHERE $filter_partner_query
											  $filter_customer_query
										ORDER BY fl.list_jdate DESC LIMIT " . $pagination->skip() .", ". $pagination->take();
	//var_dump($query);
	$data = mysqli_query($dbc, $query);
}

//выводим окно фильтра
	echo' 
		<div class="col-12 col-md-12 p-box">
			<div class="row">
				<h4 class="h6 text-muted thin text-uppercase mb-3 ml-3">Фильтр</h4>
				<form  class="marg mr-4" enctype="multipart/form-data" method="post" action="' . $_SERVER['PHP_SELF'] . '">
				 <fieldset class="mb-4">
				  <div class="input-group marg-01">
					<select id="filter_partner_name" class="custom-select rounded-0" name="filter_partner_name">
						<option value="">по партнеру...</option>';
						foreach ($partners as $partner) { 
						echo "<option " . ((!empty($filter_partner_name) && ($filter_partner_name == $partner['partner_id'])) ? 'selected ' : '') . "value= '" . $partner['partner_id'] . "'>" . $partner['partner_name'] . " / " . $partner['partner_no'] . "</option>"; 
						var_dump($filter_partner_name);}
	echo'			</select></div>';
	echo'			<div class="input-group marg-01"><select name="filter_customer_name" class="custom-select rounded-0" id="filter_customer_name">
						<option value="">по заказчику...</option>';
						foreach ($customers as $customer) { 
						echo "<option " . ((!empty($filter_customer_name) && ($filter_customer_name == $customer['customer_id'])) ? 'selected ' : '') . "value= '" . $customer['customer_id'] . "'>" . $customer['customer_name'] . " / " . $customer['customer_llc'] . "</option>"; }
	echo'			</select></div>';   
	echo'			<div class="input-group marg-01"><select id="filter_line_name" class="custom-select rounded-0" name="filter_line_name">
						<option value="">по линейке...</option>';
						foreach ($vendors as $vendor) { 
						echo "<option " . ((!empty($filter_line_name) && ($filter_line_name == $vendor['vendor_id'])) ? 'selected ' : '') . "value= '" . $vendor['vendor_id'] . "'>" . $vendor['vendor_line'] . " / " . $vendor['vendor_name'] . "</option>"; }
	echo'			</select></div>';      
	echo'			<div class="input-group marg-01"><select id="filter_author" class="custom-select rounded-0" name="filter_author">
						<option value="">по автору...</option>';
						foreach ($authors as $author) { 
						echo "<option " . ((!empty($filter_author) && ($filter_author == $author['user_id'])) ? 'selected ' : '') . "value= '" . $author['user_id'] . "'>" . $author['username'] . "</option>"; }
	echo'			</select></div>';   
	echo'			<div class="input-group marg-01"><select id="filter_dep" class="custom-select rounded-0" name="filter_dep">
						<option value="">по департаменту...</option>';
						foreach ($departaments as $departament) { 
						echo "<option " . ((!empty($filter_dep) && ($filter_dep == $departament['dep_id'])) ? 'selected ' : '') . "value= '" . $departament['dep_id'] . "'>" . $departament['dep_desc'] . "</option>"; }

	echo'				</select></div>';
	echo'			<div class="input-group marg-01"><select id="filter_chance" class="custom-select rounded-0" name="filter_chance">
						<option value="">по шансу...</option>';
						foreach ($chances as $chance) { 
						echo "<option " . ((!empty($filter_chance) && ($filter_chance == $chance['vendor_id'])) ? 'selected ' : '') . "value= '" . $chance['chance_id'] . "'>" . $chance['chance_num'] . "</option>"; }
	echo'			</select></div></fieldset>';
	echo'			<button class="btn btn-info" type="submit" name="submit">Применить</button></form>';
				
	echo'		</div>';
	echo'	</div>';

	
?>
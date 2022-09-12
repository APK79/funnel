<?php
if (isset($_POST['submit'])){
	// получение данных фильтра
	$filter_partner_name = mysqli_real_escape_string($dbc, trim($_POST['filter_partner_name'])); 
	$filter_customer_name = mysqli_real_escape_string($dbc, trim($_POST['filter_customer_name'])); 	
	$filter_city = mysqli_real_escape_string($dbc, trim($_POST['filter_city'])); 	
	$filter_line_name = mysqli_real_escape_string($dbc, trim($_POST['filter_line_name']));				
	$filter_chance = mysqli_real_escape_string($dbc, trim($_POST['filter_chance']));
	$filter_author = mysqli_real_escape_string($dbc, trim($_POST['filter_author']));				
	$filter_dep = mysqli_real_escape_string($dbc, trim($_POST['filter_dep']));
	$filter_dates = mysqli_real_escape_string($dbc, trim($_POST['filter_date'])); 
	$filter_date = explode(' - ', $filter_dates); //делим полученные даты на стартову и конечную
	$start_date = date('Y-m-d', strtotime($filter_date[0])); //приведение стартовой даты в правильный вид для запроса к БД
	$end_date = date('Y-m-d', strtotime($filter_date[1]));	//приведение стартовой даты в правильный вид для запроса к БД
	$filter_arch = mysqli_real_escape_string($dbc, trim($_POST['filter_arch'])); 
	
	if ($filter_arch == "on"){
		$query_arch = "WHERE fl.list_old = 1";
	}
	else{
		$query_arch = "WHERE fl.list_old = 0";
	}
		
	
	//добавляем WHERE в SQL запрос
	if ((!empty($filter_city)) || (!empty($filter_partner_name)) || (!empty($filter_customer_name)) || (!empty($filter_line_name)) || (!empty($filter_dep)) || (!empty($filter_author)) || (!empty($filter_chance)) || (!empty($filter_dates))){
		$filter_where = "$query_arch AND";
	}
	else if(!empty($filter_arch)){
		$filter_where = "$query_arch";
	}
	else{
		$filter_where = "$query_arch";
	}
	//Фильтр по дате
	if (!empty($filter_dates)){
		if ((!empty($filter_city)) || (!empty($filter_partner_name)) || (!empty($filter_customer_name)) || (!empty($filter_line_name)) || (!empty($filter_dep)) || (!empty($filter_author)) || (!empty($filter_chance))){
			$filter_date_query = "(list_rdate BETWEEN '$start_date' AND '$end_date') AND ";
		}
	
		else{
			$filter_date_query = "(list_rdate BETWEEN '$start_date' AND '$end_date')";
		}
	}
	else{
		$filter_date_query = "";
	}
	//фильтр по городу
	if (!empty($filter_city)){
		if ((!empty($filter_partner_name)) || (!empty($filter_customer_name)) || (!empty($filter_line_name)) || (!empty($filter_dep)) || (!empty($filter_author)) || (!empty($filter_chance))){
			$filter_city_query = "fct.city_id = '$filter_city' AND ";
		}
	
		else{
			$filter_city_query = "fct.city_id = '$filter_city'";
		}
	}
	else{
		$filter_city_query = "";
	}
	//фильтр по партнеру
	if (!empty($filter_partner_name)){
		if ((!empty($filter_customer_name)) || (!empty($filter_line_name)) || (!empty($filter_dep)) || (!empty($filter_author)) || (!empty($filter_chance))){
			$filter_partner_query = "fp.partner_id = '$filter_partner_name' AND ";
		}
	
		else{
			$filter_partner_query = "fp.partner_id = '$filter_partner_name'";
		}
	}
	else{
		$filter_partner_query = "";
	}
	//фильтр по заказчику
	if (!empty($filter_customer_name)){
		if ((!empty($filter_line_name)) || (!empty($filter_dep)) || (!empty($filter_author)) || (!empty($filter_chance))){
			$filter_customer_query = "fc.customer_id = '$filter_customer_name' AND ";
		}
		else{
			$filter_customer_query = "fc.customer_id = '$filter_customer_name'";
		}
	}
	else{
		$filter_customer_query = "";
	}
	//фильтр по линейке
	if (!empty($filter_line_name)){
		if ((!empty($filter_dep)) || (!empty($filter_author)) || (!empty($filter_chance))){
			$filter_line_query = "fm.vendor_id = '$filter_line_name' AND ";
		}
		else{
			$filter_line_query = "fm.vendor_id = '$filter_line_name'";
		}
	}
	else{
		$filter_line_query = "";
	}
	//фильтр по автору
	if (!empty($filter_author)){
		if ((!empty($filter_dep)) || (!empty($filter_chance))){
			$filter_author_query = "fl.list_author = '$filter_author' AND ";
		}
		else{
			$filter_author_query = "fl.list_author = '$filter_author'";
		}
	}
	else{
		$filter_author_query = "";
	}
	//фильт по департаменту
	if (!empty($filter_dep)){
		if ((!empty($filter_chance))){
			$filter_dep_query = "fus.department = '$filter_dep' AND ";
		}
		else{
			$filter_dep_query = "fus.department = '$filter_dep'";
		}
	}
	else{
		$filter_dep_query = "";
	}
	//фильтр по шансу
	if ((!empty($filter_chance)) && ($filter_chance =='1')){
		if ((!empty($filter_summ))){
			$filter_chance_query = "fl.list_chance < '4' AND ";
		}
		else{
			$filter_chance_query = "fl.list_chance < '4'";
		}
	}
	else if ((!empty($filter_chance)) && ($filter_chance =='2')){
		if ((!empty($filter_summ))){
			$filter_chance_query = "(fl.list_chance BETWEEN '4' AND '7') AND ";
		}
		else{
			$filter_chance_query = "(fl.list_chance BETWEEN '4' AND '7')";
		}
	}
	else if ((!empty($filter_chance)) && ($filter_chance =='3')){
		if ((!empty($filter_summ))){
			$filter_chance_query = "fl.list_chance > '7' AND ";
		}
		else{
			$filter_chance_query = "fl.list_chance > '7'";
		}
	}
	else{
		$filter_chance_query = "";
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
							fl.list_spec,
							fl.list_link,
							fl.list_old,
							fp.partner_id,
							fp.partner_no,
							fp.partner_name,
							fc.customer_id,
							fc.customer_llc,
							fc.customer_name,
							fc.customer_city,
							fc.cust_cont_name,
							fc.customer_cont,
							fc.customer_email,
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
							fdp.dep_desc,
							fct.city_id,
							fct.city_name
							FROM funnel_list AS fl
										LEFT JOIN funnel_partner AS fp ON fl.list_pnm = fp.partner_id
										LEFT JOIN funnel_customer AS fc ON fl.list_cnm = fc.customer_id
										LEFT JOIN funnel_manufacturer AS fm ON fl.list_vendor = fm.vendor_id
										LEFT JOIN funnel_currency AS fcur ON fl.list_curr = fcur.currency_id
										LEFT JOIN funnel_chance AS fcan ON fl.list_chance = fcan.chance_id
										LEFT JOIN funnel_status AS fst ON fl.list_status = fst.status_id
										LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id
										LEFT JOIN funnel_dep AS fdp ON fus.department = fdp.dep_id
										LEFT JOIN funnel_city AS fct ON fc.customer_city = fct.city_id
											$filter_where	
											  $filter_date_query
											  $filter_city_query
											  $filter_partner_query
											  $filter_customer_query
											  $filter_line_query
											  $filter_author_query
											  $filter_dep_query
											  $filter_chance_query
										ORDER BY fl.list_jdate DESC LIMIT " . $pagination->skip() .", ". $pagination->take();
	//var_dump($query);
	$data = mysqli_query($dbc, $query);
}
//подготовка переменных для запроса с учетом прав пользователей
$filter_user_id = $_SESSION['user_id'];
$filter_user_dep = $_SESSION['department'];

if (!isset($_POST['submit'])){
	$query_arch = "WHERE fl.list_old = 0";
}
if ($user_perm == 0){
	$query_select = ", fl.list_author";
	$query_perm = "AND fl.list_author = $filter_user_id";
}
else if ($user_perm == 1) {
	$query_select = ", fus.department";
	$query_perm = "AND fus.department = $filter_user_dep";
}
else{
	$query_select = "";
	$query_perm = "";
}

//запрос по городу
$city_query =  "SELECT DISTINCT fct.city_id, fct.city_name, fl.list_old $query_select FROM funnel_list AS fl 
								LEFT JOIN funnel_customer AS fc ON fl.list_cnm = fc.customer_id 
								LEFT JOIN funnel_city AS fct ON fc.customer_city = fct.city_id 
								LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id $query_arch $query_perm";
$city_data = mysqli_query($dbc, $city_query);								
$citys =[];
while ($row = mysqli_fetch_array($city_data)) { 
	array_push($citys, $row);
}
//запрос по партнеру
$partner_query =  "SELECT DISTINCT fp.partner_id, fp.partner_no, fp.partner_name $query_select FROM funnel_list AS fl 
								LEFT JOIN funnel_partner AS fp ON fl.list_pnm = fp.partner_id 
								LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id $query_arch $query_perm";
$partner_data = mysqli_query($dbc, $partner_query);								
$partners =[];
while ($row = mysqli_fetch_array($partner_data)) { 
	array_push($partners, $row);
}
//запрос по заказчику
$customer_query =  "SELECT DISTINCT fc.customer_id, fc.customer_llc, fc.customer_name $query_select FROM funnel_list AS fl 
								LEFT JOIN funnel_customer AS fc ON fl.list_cnm = fc.customer_id
								LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id $query_arch $query_perm";
$customer_data = mysqli_query($dbc, $customer_query);								
$customers =[];
while ($row = mysqli_fetch_array($customer_data)) { 
	array_push($customers, $row);
}
//запрос по линейке / названию производителя
$vendor_query =  "SELECT DISTINCT	fm.vendor_id, fm.vendor_line, fm.vendor_name $query_select FROM funnel_list AS fl 
								LEFT JOIN funnel_manufacturer AS fm ON fl.list_vendor = fm.vendor_id
								LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id $query_arch $query_perm";
$vendor_data = mysqli_query($dbc, $vendor_query);								
$vendors =[];
while ($row = mysqli_fetch_array($vendor_data)) { 
	array_push($vendors, $row);
}
//запрос по шансу
$chance_query =  "SELECT DISTINCT fcan.chance_id, fcan.chance_num FROM funnel_list AS fl LEFT JOIN funnel_chance AS fcan ON fl.list_chance = fcan.chance_id $query_arch ORDER BY fcan.chance_id DESC";
$chance_data = mysqli_query($dbc, $chance_query);								
$chances =[];
while ($row = mysqli_fetch_array($chance_data)) { 
	array_push($chances, $row);
}
//запрос по автору
$author_query =  "SELECT DISTINCT fus.user_id, fus.username, fus.first_name, fus.last_name $query_select FROM funnel_list AS fl 
								LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id $query_arch $query_perm";
$author_data = mysqli_query($dbc, $author_query);								
$authors =[];
while ($row = mysqli_fetch_array($author_data)) { 
	array_push($authors, $row);
}
//запрос по департаменту
$dep_query =  "SELECT DISTINCT fdp.dep_id, fdp.dep_desc FROM funnel_list AS fl LEFT JOIN funnel_user AS fus ON fl.list_author = fus.user_id LEFT JOIN funnel_dep AS fdp ON fus.department = fdp.dep_id $query_arch";
$dep_data = mysqli_query($dbc, $dep_query);								
$departaments =[];
while ($row = mysqli_fetch_array($dep_data)) { 
	array_push($departaments, $row);
}
?>
	<!--Вывод окна фильтра-->
		<div class="col-12 p-box">
			<div class="row">
				<h4 class="h6 text-muted thin text-uppercase mb-3 ml-3">Фильтр</h4>
				<form id="filter" class="col-12 pl-0 pr-4" enctype="multipart/form-data" method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
				 <fieldset class="mb-3">
				 	<div class="">
					  <link rel="stylesheet" type="text/css" href="../styles/daterangepicker.css" />
						<input type="text" id="filter_date" class="form-control brd-bottom-0" name="filter_date" value="<?php echo (!empty($filter_dates)) ? $filter_dates : '' ?>" placeholder="по дате реализации..." autocomplete="off" />
					</div>
					<div class="marg-01">
					  <select id="filter_city" name="filter_city" class="select-btn selectpicker" data-live-search="true">
						<option value="">по городу...</option>
						<?php 
						foreach ($citys as $city) { 
							echo "<option " . ((!empty($filter_city) && ($filter_city == $city['city_id'])) ? 'selected ' : '') . "value= '" . $city['city_id'] . "'>" . $city['city_name'] . "</option>";}
						?>
					  </select>
					</div>
					<div class="marg-01">
					  <select id="filter_partner_name" name="filter_partner_name" class="select-btn selectpicker" data-live-search="true">
						<option value="">по партнеру...</option>
						<?php 
						foreach ($partners as $partner) { 
							echo "<option " . ((!empty($filter_partner_name) && ($filter_partner_name == $partner['partner_id'])) ? 'selected ' : '') . "value= '" . $partner['partner_id'] . "'>" . $partner['partner_name'] . " / " . $partner['partner_no'] . "</option>";}
						?>
					  </select>
					</div>
					<div class="marg-01">
					  <select name="filter_customer_name" id="filter_customer_name" class="select-btn selectpicker" data-live-search="true">
						<option value="">по заказчику...</option>
						<?php
						foreach ($customers as $customer) { 
							echo "<option " . ((!empty($filter_customer_name) && ($filter_customer_name == $customer['customer_id'])) ? 'selected ' : '') . "value= '" . $customer['customer_id'] . "'>" . $customer['customer_name'] . " / " . $customer['customer_llc'] . "</option>"; }
						?>
					  </select>
				    </div>  
					<div class="marg-01"><select id="filter_line_name" name="filter_line_name" class="select-btn selectpicker" data-live-search="true">
						<option value="">по линейке...</option>
						<?php
						foreach ($vendors as $vendor) { 				
							echo "<option " . ((!empty($filter_line_name) && ($filter_line_name == $vendor['vendor_id'])) ? 'selected ' : '') . "value= '" . $vendor['vendor_id'] . "'>" . $vendor['vendor_line'] . " / " . $vendor['vendor_name'] . "</option>"; }
						?>
					</select></div>  
					<?php if (($user_perm != 0)){?>
					<div class="marg-01">
					  <select id="filter_author" name="filter_author" class="select-btn selectpicker" data-live-search="true">
						<option value="">по автору...</option>
						<?php
							foreach ($authors as $author) { 
									echo "<option " . ((!empty($filter_author) && ($filter_author == $author['user_id'])) ? 'selected ' : '') . "value= '" . $author['user_id'] . "'>" . $author['first_name'] . " " . $author['last_name'] . "</option>";}
						?>
					  </select>
					</div> 
					<?php }?>	
					<?php if (($user_perm == 2)){?>
					<div class="marg-01"><select id="filter_dep" name="filter_dep" class="select-btn selectpicker">
						<option value="">по департаменту...</option>
						<?php
							foreach ($departaments as $departament) { 
						echo "<option " . ((!empty($filter_dep) && ($filter_dep == $departament['dep_id'])) ? 'selected ' : '') . "value= '" . $departament['dep_id'] . "'>" . $departament['dep_desc'] . "</option>"; }
						?>
					</select></div>
					<?php }?>
					<div class="marg-01 mb-3"><select id="filter_chance" name="filter_chance" class="select-btn selectpicker">
						<option value="">по шансу...</option>
						<option value="1" <?php echo ((!empty($filter_chance) && ($filter_chance == '1')) ? 'selected ' : '');?> > менее 40% </option>
						<option value="2" <?php echo ((!empty($filter_chance) && ($filter_chance == '2')) ? 'selected ' : '');?>> более 40% и менее 60% </option>
						<option value="3" <?php echo ((!empty($filter_chance) && ($filter_chance == '3')) ? 'selected ' : '');?>> более 70% </option>
					</select></div>
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="archive_switch" name="filter_arch" <?php if (($filter_arch == "on")) echo 'checked' ?>>
						<label class="custom-control-label text-lowercase ml-3 pl-1 arc_s" for="archive_switch">Показать архив</label>
					</div>
					</fieldset>
					<button class="btn btn-outline-info" type="submit" name="submit">Применить</button></form>
				
				</div>
			</div>	
			<script>
				//каледарь поиска
			$(document).ready(function(){
				$(function() {
					$('input[name="filter_date"]').daterangepicker({
						//"autoApply": true,
						"autoUpdateInput": false,								
						locale: {
							applyLabel: 'Готово',
							cancelLabel: 'Очистить'
						}
					});
					$('input[name="filter_date"]').on('apply.daterangepicker', function(ev, picker) {
						$(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
					});
					$('input[name="filter_date"]').on('cancel.daterangepicker', function(ev, picker) {
						$(this).val('');
					});
				});
			 });
			</script>
<?php	

<?php
   // открытие сессии
  require_once('../common/startsession.php');
  
  //Вывод заголовка страницы
  $page_title = " - Новый проект";
  require_once('header.php');
  
  require_once('../config/appvars.php');
  require_once('../config/config.php');
  
  //вывод меню навигации
  require_once('navmenu.php');
  
 // Убеждаемся что пользователь авторизовался.
  if (!isset($_SESSION['user_id'])) {
	$login_url = 'http://' . $_SERVER['HTTP_HOST'] . '../common/login.php';
    header('Location: ' . $login_url);
	exit();
  }
  else{
  
  // соединение с базой данных 
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 
  
  // Подготавливаем пустые массивы для цикла
  	$partners = [];
	$customers = [];
	$vendors = [];
	$currencys = [];
	$chances = [];
	$statuses = [];
	
	
	//достаем данные партнера
	$query = "SELECT * FROM funnel_partner";
	$data = mysqli_query ($dbc, $query);
	while ($row = mysqli_fetch_array($data)){
		array_push($partners, $row);
	}
	//достаем данные заказчика
	$query2 = "SELECT * FROM funnel_customer";
	$data2 = mysqli_query ($dbc, $query2);
	while ($row2 = mysqli_fetch_array($data2)){
		array_push($customers, $row2);
	}
	//достаем данные производителя
	$query3 = "SELECT * FROM funnel_manufacturer";
	$data3 = mysqli_query ($dbc, $query3);
	while ($row3 = mysqli_fetch_array($data3)){
		array_push($vendors, $row3);
	}
	//достаем данные о валюте
	$query4 = "SELECT * FROM funnel_currency";
	$data4 = mysqli_query ($dbc, $query4);
	while ($row4 = mysqli_fetch_array($data4)){
		array_push($currencys, $row4);
	}
	//достаем данные о вероятности
	$query5 = "SELECT * FROM funnel_chance";
	$data5 = mysqli_query ($dbc, $query5);
	while ($row5 = mysqli_fetch_array($data5)){
		array_push($chances, $row5);
	}
	//достаем данные о статусе
	$query6 = "SELECT * FROM funnel_status";
	$data6 = mysqli_query ($dbc, $query6);
	while ($row6 = mysqli_fetch_array($data6)){
		array_push($statuses, $row6);
	}

 if (isset($_POST['submit'])) {  
  //Собираем данные из заполненных ячеек методом $_POST
	$error_msg = "";
    $partner_name = mysqli_real_escape_string($dbc, trim($_POST['partner_name'])); 
    $customer_name = mysqli_real_escape_string($dbc, trim($_POST['customer_name']));
    $vendor_name = mysqli_real_escape_string($dbc, trim($_POST['vendor_name']));
    $sale_date = mysqli_real_escape_string($dbc, trim($_POST['sale_date']));
	$sale_date = date('Y-m-d', strtotime($sale_date));
    $project_amount = mysqli_real_escape_string($dbc, trim($_POST['project_amount']));
	$sale_currency = mysqli_real_escape_string($dbc, trim($_POST['sale_currency']));
	$project_chance = mysqli_real_escape_string($dbc, trim($_POST['project_chance']));
	$project_status = mysqli_real_escape_string($dbc, trim($_POST['project_status']));
	$project_pn = mysqli_real_escape_string($dbc, trim($_POST['project_number']));
	$project_desc = mysqli_real_escape_string($dbc, trim($_POST['project_desc']));
	$project_link = mysqli_real_escape_string($dbc, trim($_POST['project_link']));
	$project_spec = mysqli_real_escape_string($dbc, trim($_FILES['project_spec']['name']));
	$project_spec_type = $_FILES['project_spec']['type'];
    $project_spec_size = $_FILES['project_spec']['size']; 
	$project_arch = 0;
	$project_author = $_SESSION['user_id'];
		
    // Добавляем полученные данные в таблицу
      if (!empty($partner_name) && !empty($customer_name) && !empty($vendor_name) &&  !empty($sale_date)  
		  && !empty($project_amount) && !empty($sale_currency) && !empty($project_chance) && !empty($project_status)) {
			if ((empty($project_spec)) || ((!empty($project_spec)) && ($project_spec_size <= FN_MAXFILESIZE))) {
				if ((empty($project_spec)) || ((!empty($project_spec)) && ($_FILES['project_spec']['error'] == 0))) {
				  if(is_numeric($project_amount)){				
					  if (!empty($project_spec)) {
						$unique_project_spec = time().'_'. $project_spec;
						$target = iconv('utf-8','cp1251', FN_UPLOADPATH . $unique_project_spec);
						move_uploaded_file($_FILES['project_spec']['tmp_name'], $target);  
					  }
					   else {
						 $error_msg = '<p class="error">Произошла ошибка при переносе файла</p>';
				      }
						  $query = "INSERT INTO funnel_list (list_jdate, list_pnb, list_pnm, list_cnm, list_cllc, list_vendor, list_rdate, list_amount, list_curr, list_chance, list_status, list_pnum, list_author, list_desc, list_spec, list_link, list_old)"
						. " VALUES (NOW(), '$partner_name', '$partner_name', '$customer_name', '$customer_name', '$vendor_name', '$sale_date', '$project_amount', '$sale_currency', '$project_chance', '$project_status', '$project_pn', '$project_author', '$project_desc', '$unique_project_spec', '$project_link', '$project_arch' )";
						mysqli_query($dbc, $query);

						// Подтверждение положительного результата
						echo '<div class="container message"><p>Проект успешно занесен в базу. Вернуться к <a href="../index.php">списку проектов</a>?</p></div>';
						
						mysqli_close($dbc);
						exit();						
					 }
				  else {
					$error_msg = '<p class="error">Сумма проекта должна состоять из цифр</p>';
					$project_amount = "";
				  }
			  }
			  else{
				  $error_msg = '<p class="error">Произошла ошибка при загрузке файла</p>';
			  }
			}  
			else{
				$error_msg = '<p class="error">Загружаемый файл не должен быть больше 50 мб.</p>';
				@unlink($_FILES['project_spec']['tmp_name']);
			}
		  }
      else {
        $error_msg = '<p class="error">Вы запонили не все необходимые ячейки.</p>';
      }

 }
   mysqli_close($dbc);
}
  ?>
  <div class="container album py-5 bg-gray">
  
	  <form enctype="multipart/form-data" id="newproject" class="" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo FN_MAXFILESIZE; ?>" />
		<fieldset>
		  <legend><p class="thin text-uppercase text-center">Информация о новом проекте</p></legend>
		  <div class="input-group marg-01">
			  <label for="partner_name" class="input-group-text brd-bottom-0 brd-top-right-0">Партнер</label>
				<select id="partner_name" class="selectpicker form-control brd-bottom-0 marg-left-01" style="border-top-right-radius: .25rem" name="partner_name" data-live-search="true" required>
					<option value="">выберите партнера...</option>
					<?php foreach ($partners as $partner) { 
						echo "<option " . ((!empty($partner_name) && ($partner_name == $partner['partner_id'])) ? 'selected ' : '') . "value= '" . $partner['partner_id'] . "'>" . $partner['partner_name']. " / " . $partner['partner_no'] . "</option>"; } ?>
				</select>
		  </div>
		  <div class="input-group marg-01">
			<div class="d-flex position-relative"><span class="add-icon"><a id="add-customer" href="../common/newcustomer.php"><img src="../images/add1.svg" alt="Добавить заказчика" title="Добавить заказчика" data-toggle="tooltip" data-placement="top" /></a></span>
				<label for="customer_name" class="input-group-text brd-top-0 brd-bottom-0">Заказчик</label>
			</div>
				<select id="customer_name" class="selectpicker form-control brd-top-0 brd-bottom-0 marg-left-01" name="customer_name" data-live-search="true" required>
					<option value="">выберите заказчика...</option>
					<?php foreach ($customers as $customer) { 
						echo "<option " . ((!empty($customer_name) && ($customer_name == $customer['customer_id'])) ? 'selected ' : '') . "value= '" . $customer['customer_id'] . "'>" . $customer['customer_name'] . " / " . $customer['customer_llc'] . "</option>"; } ?>
				</select>
		  </div>
		  <div class="input-group marg-01">
			 <label for="vendor_name" class="input-group-text brd-bottom-0 brd-top-0">Линейка</label>
				<select id="vendor_name" class="selectpicker form-control brd-bottom-0 brd-top-0 marg-left-01" name="vendor_name" data-live-search="true" required>
					<option value="">выберите линейку...</option>
					<?php foreach ($vendors as $vendor) { 
						echo "<option " . ((!empty($vendor_name) && ($vendor_name == $vendor['vendor_id'])) ? 'selected ' : '') . "value= '" . $vendor['vendor_id'] . "'>" . $vendor['vendor_line']. "&nbsp; - &nbsp;" .$vendor['vendor_name']. "</option>"; } ?>
				</select>
		  </div>
		  <div class="input-group marg-01">
			  <label for="sale_date" class="input-group-text brd-top-0 brd-bottom-0">Дата реализации</label>
			  <input type="text" id="sale_date" class="form-control brd-top-0 brd-bottom-0 marg-left-01" name="sale_date" value="<?php if (!empty($sale_date)) echo ($sale_date); ?>" placeholder="Введите дату реализации проекта..." autocomplete="off" required />
		  </div>
		  <div class="input-group marg-01">
			  <label for="project_amount" class="input-group-text brd-bottom-0 brd-top-0">Сумма проекта</label>
			  <input type="text" id="project_amount" class="form-control brd-bottom-0 brd-top-0 marg-left-01" name="project_amount" value="<?php if (!empty($project_amount)) echo ($project_amount); ?>" placeholder="Введите сумму проекта..." autocomplete="off" required/>
		  </div>
		  <div class="input-group marg-01">
			  <label for="sale_currency" class="input-group-text brd-top-0 brd-bottom-0">Валюта</label>
			  <select id="sale_currency" class="selectpicker form-control brd-top-0 brd-bottom-0 marg-left-01" name="sale_currency" required>
					<option value="">выберите валюту...</option>
					<?php foreach ($currencys as $currency) { 
						echo "<option " . ((!empty($sale_currency) && ($sale_currency == $currency['currency_id'])) ? 'selected ' : '') . "value= '" . $currency['currency_id'] . "'>" . $currency['currency_name']. "</option>"; } ?>
			  </select>
		  </div>
		  <div class="input-group marg-01">
			  <label for="project_chance" class="input-group-text brd-bottom-0 brd-top-0">Вероятность:</label>
			  <select id="project_chance" class="selectpicker form-control brd-bottom-0 brd-top-0 marg-left-01" name="project_chance" required>
					<option value="">выберите вероятность реализации проекта...</option>
					<?php foreach ($chances as $chance) {
						echo "<option " . ((!empty($project_chance) && ($project_chance == $chance['chance_id'])) ? 'selected ' : '') . "value= '" . $chance['chance_id'] . "'>" . $chance['chance_num']. " %</option>"; } ?>
			  </select>
		  </div>
		  <div class="input-group mb-2">
			  <label for="project_status" class="input-group-text brd-top-0 brd-bottom-right-0">Статус проекта:</label>
			  <select id="project_status" class="selectpicker form-control brd-top-0 marg-left-01" name="project_status" required>
					<option value="">выберите статус проекта...</option>
					<?php foreach ($statuses as $status) { 
						echo "<option " . ((!empty($project_status) && ($project_status == $status['status_id'])) ? 'selected ' : '') . "value= '" . $status['status_id'] . "'>" . $status['status_desc']. "</option>"; } ?>
			  </select>
		  </div>
		  <div class="input-group marg-01">
			  <label for="project_number" class="input-group-text brd-bottom-0 brd-top-right-0">Номер проекта: </label>
			  <input type="text" id="project_number" class="input-form-control brd-bottom-0 marg-left-01" name="project_number" value="<?php if (!empty($project_pn)) echo ($project_pn); ?>" placeholder="Введите номер проекта или квоту (не обязательно)..." autocomplete="off"/>
		  </div>
		  <div class="input-group marg-01">
			  <label for="project_link" class="input-group-text brd-bottom-0 brd-top-right-0">Ссылка на проект: </label>
			  <input type="text" id="project_link" class="input-form-control brd-top-0 brd-bottom-0 marg-left-01" name="project_link" value="<?php if (!empty($project_link)) echo ($project_link); ?>" placeholder="Введите ссылку на проект (не обязательно)..." autocomplete="off"/>
		  </div>
		  <div class="input-group mb-2">
			  <label for="project_desc" class="input-group-text brd-top-0 brd-top-right-0 brd-bottom-right-0">Доп. информация:</label>
			  <textarea cols="10" class="input-form-control brd-top-0 brd-top-right-0 marg-left-01" name="project_desc" placeholder="Здесь можно оставить дополнительные данные по проекту (не обязательно) ..."><?php if (!empty($project_desc)) echo ($project_desc); ?></textarea>
		  </div>
		  <div class="input-group mb-4">
			<div class="input-group-prepend">
				<span class="input-group-text" id="project_spec_label">Спецификация:</span>
			</div>
			<div class="custom-file" lang="ru">
				<input type="file" id="project_spec" class="custom-file-input" name="project_spec" aria-describedby="project_spec_label" />
				<label for="project_spec" class="custom-file-label specification text-truncate">загрузить спецификацию или письмо (не обязательно) ...</label>
			</div>	
		  </div>
		</fieldset>
		<div class="row"><div class="col-md-6"><?php if(!empty($error_msg)){ echo ($error_msg);}?></div>
		<div class="col-md-6"><input type="submit" class="btn btn-primary float-right" value="Добавить проект" name="submit" /></div></div>
	  </form> 
  </div>
<script type="text/javascript">
$(document).ready(function(){
	$(function() {
		$('input[name="sale_date"]').daterangepicker({
			"autoUpdateInput": false,
			"singleDatePicker": true,
		});
		$('input[name="sale_date"]').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD.MM.YYYY'));
		});
		$('input[name="sale_date"]').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
		});
	});
	
	$("#project_amount").bind('paste', function() { return false;});
	
	bsCustomFileInput.init()
	
});
	</script>
  
  
  <?php
	//Вывод нижнего колонтитула
	require_once('footer.php');
?>
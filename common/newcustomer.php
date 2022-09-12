<?php
   // открытие сессии
  require_once('../common/startsession.php');

  //Вывод заголовка страницы
  $page_title = " - Новый заказчик";
  require_once('header.php');
  require_once('../config/appvars.php');
  require_once('../config/config.php');
  
 
 // Убеждаемся что пользователь авторизовался.
  if (!isset($_SESSION['user_id'])) {
	$login_url = 'http://' . $_SERVER['HTTP_HOST'] . '/common/login.php';
    header('Location: ' . $login_url);
	exit();
  }
  else{
	  
  //вывод меню навигации
  require_once('navmenu.php'); 
  
  // соединение с базой данных 
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

  //достаем список городов
	$citys = [];
	$query = "SELECT * FROM funnel_city";
	$data = mysqli_query ($dbc, $query);
	while ($row = mysqli_fetch_array($data)){
		array_push($citys, $row);
	}
	
 if (isset($_POST['submit'])) {  
  //Собираем данные из заполненных ячеек методом $_POST
	$error_msg = "";
    $customer_name = mysqli_real_escape_string($dbc, trim($_POST['customer_name']));
	$customer_llc = mysqli_real_escape_string($dbc, trim($_POST['customer_llc']));
	$customer_city = mysqli_real_escape_string($dbc, trim($_POST['customer_city']));
	$customer_contact = mysqli_real_escape_string($dbc, trim($_POST['customer_contact']));
	$customer_contact_name = mysqli_real_escape_string($dbc, trim($_POST['cust_contact_name']));
	$customer_email = mysqli_real_escape_string($dbc, trim($_POST['customer_email']));
	
	//достаем данные заказчика
	$query = "SELECT * FROM funnel_customer WHERE customer_llc = '$customer_llc'";
	$data = mysqli_query ($dbc, $query);
	if(mysqli_num_rows($data) == 0) {
		if(is_numeric($customer_llc))	{
			// Добавляем полученные данные в таблицу
			  if (!empty($customer_name) && !empty($customer_llc) && !empty($customer_city)){
			   
			   $query = "INSERT INTO funnel_customer (customer_llc, customer_name, customer_city, cust_cont_name, customer_cont, customer_email)" .
						"VALUES ('$customer_llc', '$customer_name', '$customer_city', '$customer_contact_name', '$customer_contact', '$customer_email')";
						
			   mysqli_query($dbc, $query);

				// Подтверждение положительного результата
				echo '<div class="container message"><p>Заказчик успешно занесен в базу данных. Вернуться к <a href="../common/newproject.php">новому проекту</a>?</p></div>';

				mysqli_close($dbc);
				exit();
			  }
			  else {
				$error_msg = '<p class="error">Вы запонили не все необходимые ячейки.</p>';
			  }
		}
		else{
			$error_msg = '<p class="error">ИНН должен состоять из цифр</p>';
		}
	}
	else {
		$error_msg = '<p class="error">Заказчик с таким ИНН уже существует.</p>';
	}
 } 
}
   mysqli_close($dbc);
  ?>
  <div class="container album py-5 bg-gray">
  
	  <form id="newcustomer" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<fieldset>
		  <legend><p class="thin text-uppercase text-center">Данные нового заказчика:</p></legend>
		  <div class="input-group marg-01">
			  <label for="customer_name" class="input-group-text brd-bottom-0 brd-top-right-0">Наименование: </label>
			  <input type="text" id="customer_name" class="form-control brd-bottom-0 marg-left-01" name="customer_name" value="<?php if (!empty($customer_name)) echo ($customer_name); ?>" placeholder="Введите название заказчика . . ." autocomplete="off" required/>
		  </div>
		  <div class="input-group marg-01">
			  <label for="customer_llc" class="input-group-text brd-top-0 brd-top-right-0">ИНН:</label>
			  <input type="text" id="customer_llc" class="form-control brd-top-0 brd-bottom-0 marg-left-01" name="customer_llc" value="<?php if (!empty($customer_llc)) echo ($customer_llc); ?>" placeholder="Введите ИНН заказчика . . ." autocomplete="off" required/>
		  </div>
		  <div class="input-group marg-01">
			  <label for="customer_city" class="input-group-text brd-top-0 brd-bottom-0">Валюта</label>
				<select id="customer_city" class="selectpicker form-control brd-top-0 brd-bottom-0 marg-left-01" name="customer_city"  data-live-search="true" required>
					<option value="">Выберите город . . .</option>
					<?php foreach ($citys as $city) { 
						echo "<option " . ((!empty($customer_city) && ($customer_city == $city['city_id'])) ? 'selected ' : '') . "value= '" . $city['city_id'] . "'>" . $city['city_name']. "</option>"; } ?>
				</select>
		  </div>
		  <div class="input-group marg-01">
			  <label for="cust_contact_name" class="input-group-text brd-top-0 brd-top-right-0">Имя конт. лица:</label>
			  <input type="text" id="cust_contact_name" class="form-control brd-top-0 brd-bottom-0 marg-left-01" name="cust_contact_name" value="<?php if (!empty($customer_contact_name)) echo ($customer_contact_name); ?>" placeholder="Введите контактное лицо - ФИО (не обязательно) . . ." autocomplete="off"/>
		  </div>
		  <div class="input-group marg-01">
			  <label for="customer_contact" class="input-group-text brd-top-0 brd-top-right-0">Тел. конт. лица:</label>
			  <input type="tel" id="customer_contact" class="form-control brd-top-0 brd-bottom-0 marg-left-01" name="customer_contact" value="<?php if (!empty($customer_contact)) echo ($customer_contact); ?>" placeholder="Введите телефон контактного лица (не обязательно) . . ." autocomplete="off"/>
		  </div>
		  <div class="input-group marg-01 mb-4">
			  <label for="customer_email" class="input-group-text brd-top-0 brd-top-right-0">EMAIL конт. лица:</label>
			  <input type="email" id="customer_email" class="form-control brd-top-0 brd-top-right-0 marg-left-01" name="customer_email" value="<?php if (!empty($customer_email)) echo ($customer_email); ?>" placeholder="Введите EMAIL конт. лица (не обязательно) . . ." autocomplete="off"/>
		  </div>
		</fieldset>
		<div class="row"><div class="col-md-6 error_msg"><?php if(!empty($error_msg)){ echo ($error_msg);}?></div>
		<div class="col-md-6"><input type="submit" class="btn btn-primary float-right" value="Добавить" name="submit" /></div></div>
	  </form> 
  </div>

<?php
	//Вывод нижнего колонтитула
	require_once('footer.php');
?>

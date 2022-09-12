<?php
   // открытие сессии
  require_once('/common/startsession.php');
  
  //Вывод заголовка страницы
  $page_title = " - Проекты";
  require_once('/common/header.php');
  
  require_once('/config/appvars.php');
  require_once('/config/config.php');
  
  //вывод меню навигации
  require_once('common/navmenu.php');
  
 // Убеждаемся что пользователь авторизовался.
  if (!isset($_SESSION['user_id'])) {
    echo '<p class="login">Для получения информации Вам необходимо авторизоваться.</a></p>';
  }
  else{
  // соединение с базой данных 
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

  // запрос в базу данных на получение сведений о приоритете просмотра пользователя
  $user_query = "SELECT user_full FROM funnel_user WHERE user_id = '" . $_SESSION['user_id'] . "'";
  $users = mysqli_query($dbc, $user_query);
  foreach ($users as $user){
	$user_perm = $user['user_full'];
  }
  // запрос в базу данных на получение списка проектов
   $query = "SELECT * FROM funnel_list AS fl
			INNER JOIN funnel_partner AS fp ON fl.list_pnm = fp.partner_id
			INNER JOIN funnel_customer AS fc ON fl.list_cnm = fc.customer_id 
			INNER JOIN funnel_manufacturer AS fm ON fl.list_vendor = fm.vendor_id
			INNER JOIN funnel_currency AS fcur ON fl.list_curr = fcur.currency_id
			INNER JOIN funnel_chance AS fcan ON fl.list_chance = fcan.chance_id
			INNER JOIN funnel_status AS fst ON fl.list_status = fst.status_id
			INNER JOIN funnel_user AS fus ON fl.list_author = fus.user_id";
  
  $data = mysqli_query($dbc, $query);
  
  	$dates_query = "SELECT DATE_FORMAT(list_jdate, '%d.%m.%Y') FROM funnel_list";
    $dates = mysqli_query($dbc, $dates_query);
	

  
  
  // Выводим в таблицу
  echo '<h3>Последние данные о конкурсах:</h3>';
  echo '<table>';
  while ($row = mysqli_fetch_array($data)) {  
    if ($user_perm != 0 ) {
      echo '<tr><td><div>' . $row['list_jdate'] . '</div></td><td><div>' . $row['partner_no'] . '</div></td><td><div>' . $row['partner_name'] . '</div></td>';
	  echo '<td><div>' . $row['customer_name'] . '</div></td><td><div>' . $row['customer_llc'] . '</div></td><td><div>' . $row['vendor_name'] . '</div></td>';
	  echo '<td><div>' . $row['list_rdate'] . '</div></td><td><div>' . $row['list_amount'] . '</div></td><td><div>' . $row['currency_name'] . '</div></td>';
	  echo '<td><div>' . $row['chance_num'] . '</div></td><td><div>' . $row['status_desc'] . '</div></td><td><div>' . $row['list_pnum'] . '</div></td>';
	  echo '<td><div>' . $row['username'] . '</div></td><td><div>' . $row['list_desc'] . '</div></td></tr>';
    }
   else {
		if ($row['user_id'] == $_SESSION['user_id']){
      echo '<tr><td><div>' . $row['list_jdate'] . '</div></td><td><div>' . $row['partner_no'] . '</div></td><td><div>' . $row['partner_name'] . '</div></td>';
	  echo '<td><div>' . $row['customer_name'] . '</div></td><td><div>' . $row['customer_llc'] . '</div></td><td><div>' . $row['vendor_name'] . '</div></td>';
	  echo '<td><div>' . $row['list_rdate'] . '</div></td><td><div>' . $row['list_amount'] . '</div></td><td><div>' . $row['currency_name'] . '</div></td>';
	  echo '<td><div>' . $row['chance_num'] . '</div></td><td><div>' . $row['status_desc'] . '</div></td><td><div>' . $row['list_pnum'] . '</div></td>';
	  echo '<td><div>' . $row['username'] . '</div></td><td><div>' . $row['list_desc'] . '</div></td></tr>';
		}
    }
}
  echo '</table>'; 
  
  mysqli_close($dbc);
  }
	//Вывод нижнего колонтитула
	require_once('/common/footer.php');

<?php
   // открытие сессии
  require_once('../common/startsession.php');
  
 // Убеждаемся что пользователь авторизовался.
  if (!isset($_SESSION['user_id'])) {
	$login_url = 'http://' . $_SERVER['HTTP_HOST'] . '/common/login.php';
    header('Location: ' . $login_url);
	exit();
  }
  else{
	  
  //Вывод заголовка страницы
  $page_title = " - Список архивных проектов";
  require_once('../common/header.php');
  require_once('../common/navmenu.php');
  require_once('../config/appvars.php');
  require_once('../config/config.php');
  require_once('../common/pagination.php');

  
  // соединение с базой данных 
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 
  
	/*		постраничная навигация		*/
	
  // Получаем общее количество записей в таблице
  $query  = mysqli_query($dbc, 'SELECT COUNT(*) AS count FROM funnel_list');  
  $result = mysqli_fetch_assoc($query);

  // Получаем объект класса постраничной навигации
  $pagination = new Pagination($result['count'], 50);
  
  /* end block */
	
  // запрос в базу данных на получение сведений о приоритете просмотра пользователя
	$user_query = "SELECT user_full FROM funnel_user WHERE user_id = '" . $_SESSION['user_id'] . "'";
	$users = mysqli_query($dbc, $user_query);
	foreach ($users as $user){
		$user_perm = $user['user_full'];
  }
  //задаем параметры фильтра
	$filter_query = 'fl.list_jdate';
  // запрос в базу данных на получение списка архивных проектов
   $query = "SELECT SQL_CALC_FOUND_ROWS 
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
										WHERE fl.list_old = 1
										ORDER BY $filter_query DESC LIMIT " . $pagination->skip() .", ". $pagination->take();
  
  $data = mysqli_query($dbc, $query);

  // Выводим на страницу

  echo '
	<div class="album py-4 bg-gray">
		<div class="container">
			<h3 class="h6 text-muted thin text-uppercase text-center">Последние данные о конкурсах:</h3>
			<div class="row">';
  while ($row = mysqli_fetch_array($data)) { 
	$list_id = $row['list_id']; 
	$partner_id = $row['partner_id'];
	$partner_name = $row['partner_name'];
	$partner_num = $row['partner_no'];
	$customer_id = $row['customer_id'];
	$customer_name = $row['customer_name'];
	$customer_llc = $row['customer_llc'];
	$currency_id = $row['currency_id'];
	$currency = $row['currency_name'];
	$amount = $row['list_amount'];
	$join_date = $row['list_jdate'];
	$vendor_id = $row['vendor_id'];
	$vendor = $row['vendor_name'];
	$line = $row['vendor_line'];
	$chance_id = $row['chance_id'];
	$chance = $row['chance_num'];
	$status_id = $row['status_id'];
	$status = $row['status_desc'];
	$project_num = $row['list_pnum'];
	$descr = $row['list_desc'];
	$username = $row['username'];
	$release_date = $row['list_rdate'];
	$now_date = date("d-m-Y");
	$old_projects = $row['list_old'];
	

	// Выводим список с учетом прав пользователей
	if (($user_perm == 1) || ($row['user_id'] == $_SESSION['user_id'])) {
	 if($old_projects != 0){
	  echo '<div class="col-md-12 p-box">
				<div class="row mt-2">
					<div class="col-md-6 fs10 text-muted"><span>№ <span class="project-number">' . $list_id . '</span></b></span><span> от ' . $join_date . '</span>
				</div>
				<div class="col-md-6 fs10 text-muted text-right">
					<span class="d-none">' . $vendor_id . '</span><span>Линейка:  </span><span>' . $line . ' | ' .$vendor . '</span>
				</div>
			</div>';
      echo '	<div class="card shadow-sm">
					<div class="card-body">';
	  if($chance <= 39){
		echo '			<div class="chance-box alert alert-danger">
							<span class="d-none">' . $chance_id . '</span><span>' . $chance . '%</span>
						</div>
							<span class="d-none">' . $status_id . '</span>
						<div class = "status-box">
							<span>' . $status . '</span>
						</div>';
	  }
	  elseif (($chance >= 40) && ($chance <= 69)){
		echo '<div class="chance-box alert alert-warning"><span class="d-none">' . $chance_id . '</span><span>' . $chance . '%</span></div> 
			  <span class="d-none">' . $status_id . '</span><div class = "status-box"><span>' . $status . '</span></div>';
	  }
	  elseif($chance >= 70){
		echo '<div class="chance-box alert alert-success"><span class="d-none">' . $chance_id . '</span><span>' . $chance . '%</span></div> 
			  <span class="d-none">' . $status_id . '</span><div class = "status-box"><span>' . $status . '</span></div>';
	  }
	  if($project_num != NULL) {
		echo '<span class="project-num">Номер проекта  </span><span class="project-num-box">' . $project_num . '</span>';
	  }
	  echo '<div class="col-md-10"><div class="row"><span class="d-none">' . $partner_id . '</span><div class="col-sm-2 text-muted thin">Партнёр:  </div><div class="col-sm-10">' . $partner_name . ' | <span>' . $partner_num . '</span></div></div></div>';
	  echo '<div class="col-md-10"><div class="row"><span class="d-none">' . $customer_id . '</span><div class="col-sm-2 text-muted thin">Заказчик:  </div><div class="col-sm-10">' . $customer_name . ' | <span>' . $customer_llc . '</span></div></div></div>';
	  if ($currency == 'USD'){
		echo '<div class="col-md-10"><div class="row"><div class="col-sm-2 text-muted thin">Сумма:  </div><div class="col-sm-10">$' . $amount . '</div></div></div>';									
	  }
	  elseif($currency == 'EUR'){
		 echo '<div class="col-md-10"><div class="row"><div class="col-sm-2 text-muted thin">Сумма:  </div><div class="col-sm-10">&#8364;' . $amount . '</div></div></div>';	
	  }
	  elseif($currency == 'RUR'){
		 echo '<div class="col-md-10"><div class="row"><div class="col-sm-2 text-muted thin">Сумма:  </div><div class="col-sm-10">' . $amount . ' &#8381;</div></div></div>';	
	  }												
	  if($descr != NULL) {
		echo '<div class="col-md-10"><div class="row"><div class="col-sm-2 text-muted thin">Доп. инфо:  </div><div class="col-sm-10">' . $descr . '</div></div></div>';
	  }
	  echo '</div></div>';
	  echo '<div class="row bb-1"><div class="col-md-6 fs10 text-muted"><span>Автор:  &nbsp;' . $username . '</span></div>';
	  if((strtotime($release_date)) < (strtotime($now_date))){
		echo '<div class="col-md-6 fs10 mb-2 text-right red"><span><b>Дата реализации: &nbsp;' . $release_date . '</b></span></div></div>';
	  }
	  else {
	    echo '<div class="col-md-6 fs10 mb-2 text-right green"><span><b>Дата реализации: &nbsp;' . $release_date . '</b></span></div></div>';
	  }
	  
	  if ($row['user_id'] == $_SESSION['user_id']){
				echo '<div class="edit-box">
						<ul class="navbar-nav flex-row">
							<li class="nav-item pl-4"><a class="archived" href="/common/archmove.php?list_id=' . $list_id . '" data-toggle="modal" data-target="#archive-btn" data-content="<a href=/common/archmove.php?list_id=' . $list_id . '>Восстановить</a>"><img class="edit-icon" src="../images/restore.svg" title="достать проект из архива"/></a></li>
								<li class="nav-item pl-4"><a class="archived" href="/common/remove.php?list_id=' . $list_id . '" data-toggle="modal" data-target="#delete-btn" data-content="<a href=/common/remove.php?list_id=' . $list_id . '>Удалить</a>"><img class="edit-icon" src="../images/trash.svg" title="Удалить проект" /></a></li>';
				echo '</ul></div>';			
	 }

					
			 echo "</div>";
	}
    }
}
	
	echo '<!-- модальное окно для отправки в архив -->	
			<div class="modal fade" id="archive-btn" tabindex="-1" role="dialog" aria-labelledby=".archived" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="archiveTitle">Отправить в архив</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							Вы уверены, что готовы нужно достать проект из архива?
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button> 
							<button type="button" class="btn btn-info archived"></button>
						</div>
					</div>
				</div>
			</div>';
	echo ' <!-- модальное окно для удаления-->
					<div class="modal fade" id="delete-btn" tabindex="-1" role="dialog" aria-labelledby=".remove" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="archiveTitle">Удалить проект</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						  <div class="modal-body text-uppercase small">
							Вы уверены, что готовы удалить проект?
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button> 
							<button type="button" class="btn btn-danger removed"></button>
							 
						  </div>
						</div>
					  </div>
					</div>';
  // Выводим постраничную навигацию
  echo '<div class="pagination-box col-md-12">' . $pagination->get() . '</div>';
  echo '</div></div></div>';
  
  mysqli_close($dbc);
  }
	//Вывод нижнего колонтитула
	require_once('../common/footer.php');
?>
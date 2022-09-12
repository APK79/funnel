<?php
	// открытие сессии
	session_start();
  
	// Убеждаемся что пользователь авторизовался.
	if (!isset($_SESSION['user_id'])) {
		//echo '<p class="login">Для получения информации Вам необходимо авторизоваться.</a></p>';
		$login_url = 'http://' . $_SERVER['HTTP_HOST'] . '/common/login.php';
		header('Location: ' . $login_url);
	}
	else{
	  
	require_once('../config/config.php');
	require_once('../common/header.php');

	// соединение с базой данных 
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

  //Получение данных методом $_GET для отправки в архив.
	if (isset($_GET['list_id'])){ 					
		$list_id = $_GET['list_id'];
	}
	
  // Удаляем из базы данных
	if((isset($list_id)) && ($list_id != NULL)){
		$query = "DELETE FROM funnel_list WHERE list_id = '$list_id'";
		mysqli_query($dbc, $query);
		
		echo '<div class="container message"><p>Проект № ' . $list_id . 'успешно удален. Вернуться к <a href="../index.php">списку проектов</a>?</p></div>';

	}
	else{
		echo '<div class="container message"><p>Нет данных для редактирования. Вернуться к <a href="../index.php">списку проектов</a>?</p></div>';
	}
	mysqli_close($dbc);
	
  
  }
?>
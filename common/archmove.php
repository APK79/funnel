<?php
	// открытие сессии
	session_start();
  
	// Убеждаемся что пользователь авторизовался.
	if (!isset($_SESSION['user_id'])) {
		$login_url = 'http://' . $_SERVER['HTTP_HOST'] . '/common/login.php';
		header('Location: ' . $login_url);
		exit();
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
	$query = "SELECT * FROM funnel_list WHERE list_id = '$list_id'";
	$data = mysqli_query($dbc, $query);
	$result = mysqli_fetch_assoc($data);
	$list_old = ($result['list_old']);
	$new_list_id = ($result['list_id']);
	
  // отправляем в архив
	if((isset($list_id)) && ($list_id == $new_list_id) && ($list_old == '0')){
		
		$query = "UPDATE funnel_list SET list_old = '1' WHERE list_id = '$list_id'";
		mysqli_query($dbc, $query);
		
		echo '<div class="container message"><p>Проект успешно отправлен в архив. Вы можете перейти в <a href="../common/archive.php">архив</a> или вернуться к <a href="../index.php">списку проектов</a>?</p></div>';		
	}
	//достаем из архива 
	elseif((isset($list_id)) && ($list_id == $new_list_id) && ($list_old == '1')){
		$query = "UPDATE funnel_list SET list_old = '0' WHERE list_id = '$list_id'";
		mysqli_query($dbc, $query);
		
		echo '<div class="container message"><p>Проект успешно восстановлен из архива. Вы можете вернуться к <a href="../index.php">списку проектов</a>?</p></div>';	
	}
	else{
		echo '<div class="container message"><p>Нет данных для редактирования. Вернуться к <a href="../index.php">списку проектов</a>?</p></div>';
	}
	mysqli_close($dbc);
	
  
  }
?>
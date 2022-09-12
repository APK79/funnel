<?php
	// открытие сессии
	session_start();
  
	// Убеждаемся что пользователь авторизовался.
	if (!isset($_SESSION['user_id'])) {
		$login_url = 'http://' . $_SERVER['HTTP_HOST'] . '../common/login.php';
		header('Location: ' . $login_url);
		exit();
	}
	else{
	  
	require_once('../config/config.php');
	require_once('../config/appvars.php');

	// соединение с базой данных 
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 		
	$list_id = $_POST['list_id'];
	$query = "SELECT list_spec FROM funnel_list WHERE list_id = '$list_id'";
	$data = mysqli_query($dbc, $query);
	$result = mysqli_fetch_array($data); 
	$file_name = iconv('utf-8','cp1251', $result['list_spec']);	
	
  // Удаляем из базы данных
	if((isset($list_id)) && ($list_id != NULL)){
		$query = "UPDATE funnel_list SET list_spec='' WHERE list_id = '$list_id'";
		mysqli_query($dbc, $query);
		unlink(FN_UPLOADPATH . $file_name);
		unset ($_SESSION['project_spec']);
	}
	mysqli_close($dbc);
  }
?>
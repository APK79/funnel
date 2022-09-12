<?php
	//Если пользователь вошел в приложение - удаление переменных сессий, приводящее к выходу из приложения
	session_start();
		// Удаление переменных сессии путем обнуления массива $_SESSION
		$_SESSION = array();
		
		// Удаление COOKIE, содержащего идентификатор сессии, установкой момента истечения срока действия
		if(isset($_COOKIE[session_name()])){
			setcookie(session_name(),'', time() - 3600);    // -1 час
		}
		// закрытие сессии
		session_destroy();
		
	setcookie('user_id', '', time() - 3600);
	setcookie('username', '', time() - 3600);

	// переадресация на главную 
	$home_url = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php';
    header('Location: ' . $home_url);
	exit();
?>
<?php
require_once ('../config/config.php');
	
	//старт сессии
	session_start();
	
	// обнуление сообщений об ошибке
	$error_msg = "";
	
	//Если пользователь еще не вошел в приложение - попытка войти
	if(!isset($_SESSION['user_id'])){	
		if (isset($_POST['submit'])){
			//connect database
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
										or die ('error connect database');
			//Получение введенных данных пользователем
			$user_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
			$user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));
			
			if(!empty($user_username) && !empty($user_password)){
				//Поиск имени пользователя и пароля в базе данных
				$query = "SELECT user_id, username, first_name, last_name, department, user_full, approwed FROM funnel_user WHERE username='$user_username' AND password = SHA1('$user_password')";
				$data = mysqli_query($dbc, $query);

				
				if(mysqli_num_rows($data) == 1){
					$row = mysqli_fetch_array($data);
					//Проверяем одобрение администратора
					if ($row['approwed'] != 0){
						//Вход в приложение прошел успешно, сохранение в Cockie имени пользователя и его идентификатора.
						//Переход на главную страницу
						$_SESSION['user_id'] = $row['user_id'];
						$_SESSION['username'] = $row['username'];
						$_SESSION['first_name'] = $row['first_name'];
						$_SESSION['last_name'] = $row['last_name'];
						$_SESSION['department'] = $row['department'];
						setcookie('user_id', $row['user_id'], time()+(60*60*24*30)); // срок действия 30 дней
						setcookie('username', $row['username'], time()+(60*60*24*30)); // срок действия 30 дней
						$home_url = ('http://' . $_SERVER['HTTP_HOST'] . '/index.php');
						header('location: ' . $home_url);
					}
					else{
						//Одобрение администратора не пройдено
						$error_msg = 'Извините, что бы войти в приложение, нужно одобрение администратора.';
					}
				}
				else{
					// Имя пользователя / пароль введены неверно.
					$error_msg = 'Введите правильно имя пользователя и пароль.';
				}
			}
			else{
				// Имя пользователя / пароль не введены.
					$error_msg = 'Введите правильно имя пользователя и пароль.';
			}
		}
		
	}
	else{
		//пользователь уже авторизовался. Редирект на главную.
		$home_url = ('http://' . $_SERVER['HTTP_HOST'] . '/index.php');
		header('location: ' . $home_url);
	}
?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php 
		echo '<title>Воронка продаж - Авторизация </title>'
	?>
  <link rel="stylesheet" href="../styles/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="../styles/styles.css" />
  
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>

</head>
<style>
body {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-align: center;
    align-items: center;
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
}
</style>
<body>
	<div class = "container text-center">
		<form class = "form-signin" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<fieldset>
				<div><img class="logimg" src="../images/confunnel.png" title="Проектная воронка" alt="Проектная воронка"/></div>
				<h1 class = "h3 mb-3 font-weight-normal">Авторизуйтесь:</h1>
				<label for = "name" class="sr-only">логин:</label>
				<input type ="text" id="username" name="username" class="form-control marg-01 brd-bottom-0" placeholder="email адрес" required autofocus value="<?php if (!empty($user_username)) echo $user_username; ?>" />
				<label for = "password" class="sr-only">пароль:</label>
				<input type ="password" id="password" name="password" class="form-control brd-top-0" placeholder="пароль" style="margin-bottom: 10px;"required /></input>
			</fieldset>
			<div class="checkbox mb-3">
				<label>
					<input type="checkbox" name="remember-me" value="remember-me"> Запомнить меня
				</label>
			</div>
			<input class="btn btn-primary btn-md btn-block" type="submit" name="submit" value="Готово"/>
		</form>
		<small class="text-muted">OCS &copy; 2019</small>
		<div>
		<?php
			if (empty($_SESSION['user_id'])){
				echo '<p class="error small">' . $error_msg .'</p>';
			?>
		</div>
		<!--<p><a href="/common/signup.php" class="registration">Регистрация</a></p>-->
		
<?php
	}
	else{
		//подтверждение успешного входа в приложение
		//echo '<p class="login">Вы вошли как ' . $_SESSION['username'] . '</p>';
		$home_url = ('http://' . $_SERVER['HTTP_HOST'] . '/index.php');
		header('location: ' . $home_url);
	}
?>
	</div>
</body>
</html>

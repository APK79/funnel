<!DOCTYPE html>
<html>
<head>
<title>Регистрация</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../styles/bootstrap.min.css" />
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<link href="../styles/styles.css" rel="stylesheet" type="text/css">
</head>
<?php
	require_once('../config/config.php');

	// обнуление сообщений об ошибке
	$error_msg = "";
	
  	//connect database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
							or die ('error connect database');
	
	$query_dep = 'SELECT * FROM funnel_dep';
	$dep_data = mysqli_query ($dbc, $query_dep);
	$departments = [];
	while ($row = mysqli_fetch_array($dep_data)){
		array_push($departments, $row);
	}
	
	if (isset($_POST['submit'])) {
		//извлечение профиля из суперглобального массива POST
		$username = mysqli_real_escape_string ($dbc, trim($_POST['username']));
		$password = mysqli_real_escape_string ($dbc, trim($_POST['password']));
		$password2 = mysqli_real_escape_string ($dbc, trim($_POST['password2']));
		$first_name = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
		$last_name = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
		$department = mysqli_real_escape_string($dbc, trim($_POST['department']));
	
		if (!empty($username) && !empty($password) && !empty($first_name) && !empty($last_name) && !empty($department) && !empty($password2) && ($password == $password2)) {
			
			//Проверка того, что никто не использует введенное имя пользователя, которое ввел новый пользователь
			$query = "SELECT * FROM funnel_user WHERE username = '$username'";
			$data = mysqli_query ($dbc, $query);
			
			if(mysqli_num_rows($data) == 0) {
				if (filter_var($username, FILTER_VALIDATE_EMAIL) !== false){
			
					//имя пользователя не используется, добавляем в базу данных нового пользователя.
					$query = "INSERT INTO funnel_user (username, password, join_date, first_name, last_name, department) VALUES ('$username', SHA1('$password'), NOW(), '$first_name', '$last_name', '$department')";
					mysqli_query ($dbc, $query);
					
					echo ('<div class="container message"><p>Ваша новая учетная запись успешно создана. </p><p><a href="../index.php">Перейти на страницу авторизации.</a></p></div>');
					
					mysqli_close ($dbc);
					exit();
				}
				else{
					$error_msg = '<div class="error marg-top-05 small text-center">Нужно обязательно ввести корпоративную электронную почту.</div>';
				}
			}
		else {
			$error_msg = '<div class="error marg-top-05 small text-center">Такой пользователь уже существует. </div>';
			$username = "";
		}
		}
		elseif ($password != $password2){
			$error_msg = '<div class="error marg-top-05 small text-center">Пароли не совпадают. </div>';
		}
	else {
		$error_msg = '<div class="error marg-top-05 small text-center">Вы должны ввести все данные, в том числе и пароль и подтверждение пароля.</div>';
	}
  }
	mysqli_close ($dbc);
?>
<body>
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
<div class="form-signin">
	<h1 class="h3 text-center">Регистрация</h1>
	<form id="signup" enctype="multipart/form-data" class="text-center was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<fieldset>
			<legend class="text-muted">Создание нового пользователя:</legend>
			<label for="firstname" class="sr-only">Имя:</label>
			<input type="text" id="firstname" class="form-control marg-01 brd-bottom-0" name="first_name" placeholder="Имя:" value="<?php if (!empty($first_name)) echo $first_name; ?>" autocomplete="off" required />
			<label for="lastname" class="sr-only">Фамилия:</label>
			<input type="text" id="lastname" class="form-control brd-top-0" name="last_name" placeholder="Фамилия:" value="<?php if (!empty($last_name)) echo $last_name; ?>" autocomplete="off" required/>
			  <div>
				  <label for="department" class="sr-only">Департамент</label>
					<select id="department" class="custom-select marg-bottom-05 marg-top-05" name="department" required>
						<option value="">выберите департамент...</option>
						<?php foreach ($departments as $dep) { 
								echo "<option " . ((!empty($department) && ($department == $dep['dep_id'])) ? 'selected ' : '') . "value= '" . $dep['dep_id'] . "'>" . $dep['dep_desc']	. "</option>"; 
						} ?>
					</select>
			  </div>	
			<label for = "username" class="sr-only">Эл. почта:</label>
			<input type ="email" id="username" class="form-control marg-01 brd-bottom-0" name="username" placeholder="Электронная почта" value="<?php if (!empty($username)) echo $username; ?>" autocomplete="off" required/>
			<label for = "password" class="sr-only">пароль:</label>
			<input type ="password" id="user_password" class="form-control marg-01 brd-bottom-0 brd-top-0" placeholder="Введите пароль..." name="password" autocomplete="off" required/></input>
			<input type ="password" id="user_password2" class="form-control brd-top-0 marg-bottom-10" placeholder="Повторите пароль..." name="password2" autocomplete="off" required/></input>
		</fieldset>
		<input type="submit" class="btn btn-md btn-primary btn-block" name="submit" value="Зарегистрироваться"/>
	</form>
	<div class="text-center marg-top-05"><small class="text-muted">OCS &copy; 2019</small></div>
	<div id="valid">
		<?php
			if(!empty($error_msg)){
				echo ($error_msg);
			}
		?>
	</div>
<script>

$(document).ready(function() {
 $('#username').blur(function() {
 if($(this).val() != '') {
	var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
		if(pattern.test($(this).val())){
			$('#username').removeClass('is-invalid').addClass('is-valid');
			 $('#valid').html('');
		} else {
			$('#username').removeClass('is-valid').addClass('is-invalid');
			$('#valid').html('<div class="error marg-top-05 small text-center">Почта введена не верно.</div>');
		}
 } else {
 // Предупреждающее сообщение
 $('#valid').html('<div class="error marg-top-05 small text-center">Введите электронную почту</div>');
 }
 });
});

</script>
	<!-- <p><a href="../index.php">Перейти на главную страницу</a></p> -->
</div>
</body>
</html>
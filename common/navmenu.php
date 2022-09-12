<header>
<?php
  if(isset($_SESSION['username'])){
	echo '<nav class="navbar navbar-expand navbar-dark bg-dark">';
	echo '<div class="container">';
	echo '<div class="col-3"><a class="navbar-brand thin" href="../index.php">Project List</a></div>';
	echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" 
			aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span></button>';
	echo '<div class="collapse navbar-collapse" id="navbar">';
    echo '<ul class="navbar-nav text-uppercase small" style="width: 100%">';
	echo '<li class="nav-item"><a class="nav-link" href="/common/newproject.php">Новый проект</a></li>';
	//echo '<li class="nav-item"><a class="nav-link" href="/common/newcustomer.php">Новый заказчик</a></li>'; 	
	echo '<li class="nav-item"><a class="nav-link" href="/common/archive.php">Архив</a></li>'; 	
	echo '<li class="nav-item"><a class="nav-link text-uppercase btn-exit" href="/common/logout.php">Выйти <span class="nav-name">' . $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] .'</span></a></li>'; 
	echo '</ul></div></div></nav>';	
  }
  else{
	echo '<nav class="site-header sticky-top py-1"><div class="container d-flex flex-column flex-md-row justify-content-between">';
	echo '<a class = "py-2" href="/common/login.php">Авторизация</a> ';
	echo '<a class = "py-2" href="/common/signup.php">Регистрация</a> ';
	echo '</div></nav>';
  }
?>
</header>
<?php
	session_start();

	require_once("code_func.php");
?>
<!doctype html>
<html class="no-js" lang="">

<?php include('head.php'); ?>

<body class="page-loading">
  <!-- page loading spinner -->
  <div class="pageload">
    <div class="pageload-inner">
      <div class="sk-rotating-plane"></div>
    </div>
  </div>
  <!-- /page loading spinner -->
  <div class="app signin v2 usersession">
    <div class="session-wrapper">
      <div class="session-carousel slide" data-ride="carousel" data-interval="5000">
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
		  <div class="item active fondo" style="background-image:url(images/sliders/slider1.jpg);"></div>
		  <div class="item  fondo" style="background-image:url(images/sliders/slider2.jpg);"></div>
      <div class="item  fondo" style="background-image:url(images/sliders/slider3.jpg);"></div>
		  <!--
          <div class="item" style="background-image:url(http://lorempixel.com/1200/800?3);background-size:cover;background-repeat: no-repeat;background-position: 50% 50%;">
          </div>
		  -->
        </div>
      </div>
      <div class="card bg-white no-border fondo-login">
        <div class="card-block padding-log">
          <form role="form" class="form-layout" action="" method="POST">
            <div class="text-center m-b">
			  		<img width="85%" src="images/LogoBlanco.png">
              <h4 class="bien">Bienveni<span>do</span></h4>
            </div>
            <div class="form-inputs p-b botones">
              <!-- <label class="text-uppercase">Ingresa tu Email</label> -->
              <input name="email" type="email" class="form-control input-lg" placeholder="Ingresar e-mail" required>
             <!--  <label class="text-uppercase">Ingresa tu Contraseña</label> -->
              <input name="password" type="password" class="form-control input-lg" placeholder="Contraseña" required>
              <!--<a ui-sref="user.forgot">¿Olvidaste tu contraseña?</a>-->
            </div>
            <button class="btn btn-primary btn-block btn-lg m-b boton-log" type="submit">Ingresar</button>
          </form>
        </div>
      </div>
      <div class="push"></div>
    </div>
  </div>
  <!-- build:js({.tmp,app}) scripts/app.min.js -->
  <script src="scripts/helpers/modernizr.js"></script>
  <script src="vendor/jquery/dist/jquery.js"></script>
  <script src="vendor/bootstrap/dist/js/bootstrap.js"></script>
  <script src="vendor/fastclick/lib/fastclick.js"></script>
  <script src="vendor/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
  <script src="scripts/helpers/smartresize.js"></script>
  <script src="scripts/constants.js"></script>
  <script src="scripts/main.js"></script>
  <!-- endbuild -->
</body>

<?php

	require_once("login.php");
/*
	if(isset($_SESSION["session_username"])){

		$url = 'admin.php';
		redirigir($url);
	}
*/
	if(isset($_POST["password"])){
		if(!empty($_POST['email']) && !empty($_POST['password'])) {
			$email=$_POST['email'];
			$password=$_POST['password'];
			$login = check_login($email, $password);
			if($login){
				$url = 'admin.php';
				redirigir($url);
			}
			else {
				$message = "Nombre de usuario ó contraseña invalida!";
			}
		}
		else {
			$message = "Todos los campos son requeridos!";
		}
	}

	if(isset($_POST["recoveremail"])){
		sendmail($_POST["recoveremail"]);
	}

?>

</html>

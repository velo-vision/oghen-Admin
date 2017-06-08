<?php

	/*
	function generatePassword($length = 8) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$count = mb_strlen($chars);

		for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= mb_substr($chars, $index, 1);
		}

		return $result;
	}


	function sendmail($mail){
		$password = generatePassword();

		wcode_mail("wcode@velosoft.mx","SISTEMA ORBIS",$mail,"ORBIS",$password);

		wcode_mailadmin("wcode@velosoft.mx","SISTEMA ORBIS",'lenin@velosoft.mx',"ORBIS", $mail);

		code_simple('UPDATE usuario SET password = sha2("'.$password.'",224) WHERE email = "'.$mail.'"');
	}
	*/
	/*
	function enviarmail(){
		$email = $_SESSION['session_email'];
		mailmessage();
	}*/

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	define("keyPass","2992001883");


	function extractCode($post){

		foreach ($post as $field => $k){
			global ${$field};
			${$field} = addslashes(htmlspecialchars(strip_tags(trim($k))));
		}

	}

	// This function only works for debug information and then abort the php code
	function print_d($data){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		die();
	}

	// This function print it the information with format
	function print_f($data){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}

	function is_login(){
		if(!isset($_SESSION["session_username"])) {
			header("location: index.php");
		}
	}

	function redirigir($url){
		echo '<script>';
		echo 'window.location.href = "'.$url.'"';
		echo '</script>;';
	}

	function addmessage($mensaje, $titulo, $aquien){
		#Estatus de mail 1 = Activo, 2 = Leido
		/*
		date_default_timezone_set('UTC');
		date_default_timezone_set("America/Mexico_City");
		setlocale(LC_TIME,"es_MX.UTF-8");
		$fecha = strftime("%d/%m/%Y");
		$hora = strftime("%H:%M:%S");
		*/

		$insert = new insert;
		$insert->table("mensaje");
		$insert->addValue("mensaje",$mensaje);
		#$insert->addValue("fecha_hora",$fecha.' '.$hora);
		$insert->addValue("titulo",$titulo);
		$insert->addValue("dequien",$_SESSION['session_email']);
		$insert->addValue("paraquien",$aquien);
		$insert->addValue("estatus",1);
		$insert->run();
	}

	if(isset($_GET['code'])){
		include("autoload.php");
		mensaje("ada", 29, 4, 104196);
	}

	function mensaje($comentario, $idencuesta, $calificacion, $ultimo, $debug = 0){

		$selectMensaje = new select;
		$selectMensaje->table("encuesta, local");
		$selectMensaje->addSelect("local.id as idLocal, local.telefonoencargado as telefonoencargado, local.nombre as nombreLocal, local.telefono as telefono");
		$selectMensaje->addCondition("encuesta.idlocal = local.id");
		$selectMensaje->addCondition("encuesta.id = ".$idencuesta);
		$selectMensaje->run();
		$numero = $selectMensaje->getResults();
		$celular = $numero[0]['telefono'];
		// print_r($selectMensaje->getError());
		// print_r($selectMensaje->getResults());
		// echo $numero;

		if($calificacion == 3){
			$sm = 'Regular';
		}
		if($calificacion == 4){
			$sm = 'Mala';
		}
		if($calificacion == 5){
			$sm = 'Muy mala';
		}

		$selectMensaje = new select;
		$selectMensaje->table("encuestarespondida");
		$selectMensaje->addCondition("id = ".$ultimo);
		$selectMensaje->run();

		$ultimos = $selectMensaje->getResults();

		$mensaje  = 'Ha habido una queja - '.$comentario.' Local: '.$numero[0]['nombreLocal'].' - '.$ultimos[0]['email']." - ".$ultimos[0]['telefono'];
		$empleado = "";
		if($ultimos[0]['idempleado']){
			$emp = new select;
			$emp->table("empleado");
			// $emp->addSelect("*");
			$emp->addCondition("id = ".$ultimos[0]['idempleado']);
			$emp->run();
			$e = $emp->getResults();
			$empleado = "Nombre de mesero: ".$e[0]['nombre'];
		}

		$mesa = "";
		if($ultimos[0]['mesa']) $mesa = "Numero de mesa: ".$ultimos[0]['mesa'];

		$mensaje = 'Ha habido una queja. '.$comentario.'. '.$numero[0]['nombreLocal'].'. '.$empleado.'. '.$mesa.'.'.$ultimos[0]['email']." - ".$ultimos[0]['telefono'].'.';






		/*
			Code actions
		*/
		if($debug || 1){
			$idLocal = $numero[0]['idLocal'];
			$selectCel = new select;
			$selectCel->table("celulares");
			$selectCel->addCondition("idLocal = ".$idLocal);
			$selectCel->run();
			$celulares = $selectCel->getResults();

			if(isset($celulares) && count($celulares)){
				foreach ($celulares as $key => $value) {
					# code...

					curl_setopt_array($ch = curl_init(), array(

							CURLOPT_URL => "https://www.smsmasivos.com.mx/sms/api.envio.new.php",
							CURLOPT_POST => TRUE,
							CURLOPT_RETURNTRANSFER => TRUE,
							CURLOPT_POSTFIELDS => array(
							#"apikey" => "977b36857c4a5436df0e8106023f957ef0a34851",
							"apikey" => "c9151a1d82d3edc69e57a99848677f02aee78920",
							"mensaje" => str_replace("@","@",$mensaje),
							"numcelular" => $value['numero'],
							"numregion" => "52",
							#"sandbox"=> 1,
							#"voz" => 1
											)
									)
							);

							$respuesta=curl_exec($ch);
							curl_close($ch);
							$respuesta=json_decode($respuesta);

							/*
							echo "<pre>";
							print_r($respuesta);
							echo "</pre>";
							*/

				}
			}


		}
		else{
			// End code actions
			#echo $mensaje;

			curl_setopt_array($ch = curl_init(), array(

					CURLOPT_URL => "https://www.smsmasivos.com.mx/sms/api.envio.new.php",
					CURLOPT_POST => TRUE,
					CURLOPT_RETURNTRANSFER => TRUE,
					CURLOPT_POSTFIELDS => array(
					#"apikey" => "977b36857c4a5436df0e8106023f957ef0a34851",
					"apikey" => "c9151a1d82d3edc69e57a99848677f02aee78920",
					"mensaje" => $mensaje,
					"numcelular" => $celular,
					"numregion" => "52",
					#"sandbox"=> 1,
					#"voz" => 1
									)
							)
					);

					$respuesta=curl_exec($ch);
					curl_close($ch);
					$respuesta=json_decode($respuesta);

					/*
					echo "<pre>";
					print_r($respuesta);
					echo "</pre>";
					*/

		}
	}


	function encripta( $q ) {
	    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
	    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
			$qEncoded = str_replace("/","codeSlash",$qEncoded);
			$qEncoded = str_replace(" ","codeEspacio",$qEncoded);
			$qEncoded = str_replace("+","codeMas",$qEncoded);
	    return( $qEncoded );
	}

	function desencripta( $q ) {
	    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
			$q = str_replace("codeEspacio"," ",$q);
			$q = str_replace("codeSlash","/",$q);
			$q = str_replace("codeMas","+",$q);
			// print_f($q);
	    $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
	    return( $qDecoded );
	}

?>

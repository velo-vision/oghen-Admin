<?php
	session_start();
	require("code_func.php");
	require("autoload.php");
	require_once("wcode_mailer/wcode_mailer.php");
	ini_set('display_errors', '1');

	$arr = array();
	#$arr = [];
	#$arr[];
	#ini_set('memory_limit', '-1');
	$_GET['token'] = '2992001883';

	if(!isset($_GET['token']) || $_GET['token'] != keyPass){
		echo  "<br><center><h2>Invalid Key</h2></center>";
		die();
	}

	if(isset($_GET['tipo'])){

		/*Cambiar a funcion contra inyecciones SQL*/
		extractCode($_GET);

		switch($tipo){
			case "estatusmensaje":
				$update = new update;
				$update->table("mensaje");
				$update->addSet("estatus",2);
				$update->addCondition("id = ".$id);

				if(!$update->run()) echo $update->getError();
				else echo 'true';
			break;
			case "uuid":
				$consulta = new select;
				$consulta->table("dispositivo");
				$consulta->addCondition("uuid = '".$_GET['id']."'");
				$consulta->run();
				if($consulta->getResults() == NULL) echo "failed";
				else echo "ok";
			break;
			case "pregunta":
				$consultan = new select;
				$consultan->table(" dispositivo, pregunta, encuesta ");
				$consultan->addSelect(" pregunta.id, pregunta.pregunta, pregunta.cons_tipopregunta as tipopregunta, pregunta.idencuesta ");
				$consultan->addCondition(" dispositivo.uuid = '".$_GET['uuid']."'");
				#$consultan->addCondition(" encuesta.idestatus = 1 ");
				$consultan->addCondition(" dispositivo.idlocal = encuesta.idlocal ");
				$consultan->addCondition(" pregunta.idencuesta = encuesta.id ");
				$consultan->addOrder("id");
				$consultan->typeOrder("ASC");
				$consultan->run();

				$preguntasbase = $consultan->getResults();

				if($consultan->getTotal()){
					for($i=0; $i < count($preguntasbase); $i++){
						$consultap = new select;
						$consultap->table("opciones_para_responder");
						$consultap->addCondition(" idpregunta = ".$preguntasbase[$i]['id']);
						$consultap->addOrder("id");
						$consultap->typeOrder("ASC");
						$consultap->run();
						#var_dump($consultap->getQuery());

						$respuestas = $consultap->getResults();
						echo '<pre>';
						print_r ($respuestas);
						echo '</pre>';
					}
				}
			break;
			case "premio":
				$consultan = new select;
				$consultan->table(" premio, dispositivo ");
				$consultan->addSelect(" premio.id, premio.nombre, premio.imagen, premio.descripcion, premio.valor, premio.idlocal ");
				$consultan->addCondition(" dispositivo.uuid = '".$_GET['uuid']."'");
				$consultan->addCondition(" dispositivo.idlocal = premio.idlocal ");
				$consultan->run();
				if($consultan->getResults() != NULL) {
					$resultados = $consultan->getResults();
					print json_encode($resultados);
				}else print json_encode('ERROR');
			break;
			case "local":
				$consultan = new select;
				$consultan->table(" local, dispositivo ");
				$consultan->addSelect(" local.id, local.idencuesta, local.nombre, local.logo, local.telefono, local.twitter, local.latitud, local.longitud ");
				$consultan->addCondition(" dispositivo.uuid = '".$_GET['uuid']."'");
				$consultan->addCondition(" dispositivo.idlocal = local.id ");
				$consultan->run();
				if($consultan->getResults() != NULL) {
					$resultados = $consultan->getResults();
					print json_encode($resultados);
				}else print json_encode('ERROR');
			break;
			case "empleado":
				$consultan = new select;
				$consultan->table(" empleado, dispositivo ");
				$consultan->addSelect(" empleado.id, empleado.nombre ");
				$consultan->addCondition(" dispositivo.uuid = '".$_GET['uuid']."'");
				$consultan->addCondition(" dispositivo.idlocal = empleado.idlocal ");
				$consultan->run();

				if($consultan->getResults() != NULL) {
					$resultados = $consultan->getResults();
					print json_encode($resultados);
				}else print json_encode('ERROR');
			break;
			case "preguntas":
				$consultap = new select;
				if($_GET['id'] != 0){
					$consultap->table("pregunta, local, encuesta");
					$consultap->addSelect("pregunta.id, pregunta.pregunta, pregunta.idencuesta");
					$consultap->addCondition("local.idusuariopendiente = ".$id);
					$consultap->addCondition("encuesta.idlocal = local.id");
					$consultap->addCondition("pregunta.idencuesta = encuesta.id");
					#$consultap->addCondition("pregunta.idencuesta = ".$idencuesta);
				}else{
					$consultap->table("pregunta");
					$consultap->addSelect("pregunta.id, pregunta.pregunta, pregunta.idencuesta");
					#$consultap->addCondition("pregunta.idencuesta = ".$idencuesta);
				}
				$consultap->run();
				if($consultap->getResults() != NULL) {
					$resultados = $consultap->getResults();
					print json_encode($resultados);
				}else print json_encode('ERROR');
			break;
			case "subrubros":
				$consultas = new select;
				$consultas->table("subrubro");
				$consultas->addCondition("idrubro = ".$_GET['id']);
				$consultas->run();
				if($consultas->getResults() != NULL) {
					$resultados = $consultas->getResults();
					print json_encode($resultados);
				}else print json_encode('ERROR');
			break;
			case "check":
				$consultam = new select;

				if($_SESSION['idtb'] != 1){
					$consultal = new select();
					$consultal->table("local");
					$consultal->addCondition("idusuariopendiente = ".$_SESSION['id_user']);
					$consultal->run();
					$result = $consultal->getResults();

					$consultam->table("encuestarespondida, encuesta");
					$consultam->addSelect("encuestarespondida.id, encuestarespondida.calificacion, encuestarespondida.fecha,encuestarespondida.comentario, encuestarespondida.latitud, encuestarespondida.longitud");
					$consultam->addCondition("encuestarespondida.idencuesta = encuesta.id");
					$consultam->addCondition("encuesta.idlocal = ".$result[0]['id']);
				}else{
					$consultam->table("encuestarespondida");
					$consultam->addSelect("encuestarespondida.id, encuestarespondida.calificacion, encuestarespondida.fecha, encuestarespondida.comentario, encuestarespondida.latitud, encuestarespondida.longitud");
				}

				$consultam->addOrder("id");
				$consultam->typeOrder("DESC");

				#$consultam->setLimit(1);
				#$consultam->limit(1);
				$consultam->run();

				#var_dump	($consultam->getQuery());

				if($consultam->getTotal()) {
					$resultados = $consultam->getResults();
					print json_encode($resultados);
				}else print json_encode('ERROR');
			break;
			case "todaspreguntas":

				$todo = array ();
				$uuid = $_GET['uuid'];

				$consulta = new select();
				$consulta->table("dispositivo as d, encuesta as e, local as l");
				$consulta->addCondition("d.uuid = '".$uuid."'");
				$consulta->addCondition("d.idlocal = l.id");
				$consulta->run();



				$idEncuesta = $consulta->getResults()[0]['idencuesta'];

				$pre = new select;
				$pre->table("pregunta");
				//$pre->addCondition(" baseAncla = 1 ");
				$pre->run();

				$preguntas = $pre->getResults();

				$pre = new select;
				$pre->table("opciones_para_responder");
				$pre->run();

				$opciones = $pre->getResults();

				$pArray = array();
				$oArray = array();
				$encuesta = array();

				foreach($preguntas as $pregunta){
					$pArray[$pregunta['id']] = $pregunta;
					if($pregunta['idencuesta'] == $idEncuesta && $pregunta['baseAncla'] == 1){
						$encuesta[$pregunta['id']] =  $pregunta;
						$encuesta[$pregunta['id']]["base"] = 1;
					}
				}

				foreach($opciones as $opcion){
					if(!count($oArray[$opcion['idpregunta']])) $oArray[$opcion['idpregunta']]['opciones'] = array();
					array_push($oArray[$opcion['idpregunta']]['opciones'],$opcion);
					if($opcion['idpreguntaancla']) $oArray[$opcion['idpregunta']]['ancla'] = 1;
				}
				$arreglo = $encuesta;

				for($i = 0 ; $i<count($encuesta) ; $i++){
					$enc = array_shift($arreglo);
					$encuesta[$enc['id']]['opciones'] = $oArray[$enc['id']];
					if($oArray[$enc['id']]['ancla'] == 1){
						foreach($oArray[$enc['id']]['opciones'] as $op){
							if($op['idpreguntaancla']){
								if(empty($encuesta[$op['idpreguntaancla']])){
									$encuesta[$op['idpreguntaancla']] = $pArray[$op['idpreguntaancla']];
									array_push($arreglo, $pArray[$op['idpreguntaancla']]);
								}
							}
						}
					}
				}

				if($encuesta) {
					print json_encode($encuesta);
				}else print json_encode('ERROR');
			break;
			case "guardarencuesta":
				$data = json_decode($_POST['data'], TRUE);

				$inserten = new insert;
			  $inserten->table("encuestarespondida");
			  $inserten->addValue("comentario", $data[0][0]);
			  $inserten->addValue("idencuesta", $data[0][1]);
			  $inserten->addValue("calificacion", $data[0][2]);
			  $inserten->addValue("latitud", $data[0][3]);
			  $inserten->addValue("longitud", $data[0][4]);
			  $inserten->addValue("idempleado", $data[0][5]);
				$ultimo = $inserten->run();

				if($ultimo) {
					if($data[0][2] == 4 || $data[0][2] == 5){
						mensaje($data[0][0], $data[0][1], $data[0][2]);
					}

			    print json_encode($ultimo);
			  }else print json_encode('ERROR');
			break;
			case "guardarrespuestas":
				$data = json_decode($_POST['data'], TRUE);
				foreach ($data as $key => $value) {

					if(is_string($value[1])){
						$inserter = new insert;
					  $inserter->table("respuesta");
						$inserter->addValue("idpregunta", $value[0]);
					  $inserter->addValue("respuesta", $value[1]);
					  $inserter->addValue("idencuestarespondida", $_GET['idencuesta']);

					  if($inserter->run()) {
					    print json_encode('HECHO');
					  }else print json_encode('ERROR');
					}else{
						$inserter = new insert;
					  $inserter->table("respuesta");
						$inserter->addValue("idpregunta", $value[0]);
					  $inserter->addValue("idopciones_para_responder", $value[1]);
						$inserter->addValue("idencuestarespondida", $_GET['idencuesta']);

					  if($inserter->run()) {
					    print json_encode('HECHO');
					  }else print json_encode('ERROR');
					}

				}
			break;
			case 'filtro':
				$table = '';
				$select = '';
			  $condition = '';
				/*
				Tipo = Empleado, etc.
				idtipo = idempleado, idpregunta, etc
				filtro = fecha, hora, etc
				fechainicio = valor fecha, valor hora
				fechafinal = valor fecha, valor hora
				*/

				/*
				Empleado - Rubro - Pregunta - Encuesta - Subrubro - Local*
				Fecha - Hora - Semana - Mes - Año
				*/
				$order = 'idEncuestaRespondida';

				if($idempleado != "none"){
					$table .= ', empleado as em ';
					$sel .= ', em.nombre as nombreEmpleado ';
					$condition .= ' em.id = e.idempleado ';
					$condition .= ' AND em.id = '.$idempleado.' AND ';
					#$condition .= $select->addCondition("e.id = ".$idtipo);
				}

				if($idrubro != "none"){
					$table .= ', rubro as ru, subrubro as sub';
					$sel .= ', ru.nombre as nombreRubro ';
					$condition .= ' pre.idencuesta = e.idencuesta ';
					$condition .= ' AND pre.idsubrubro = sub.id ';
					$condition .= ' AND sub.idrubro = ru.id ';
					$condition .= ' AND ru.id = '.$idrubro.' AND ';
				}

				if($idpregunta != "none"){
					$sel .= ', pre.pregunta ';
					$condition .= ' r.idpregunta = pre.id';
					$condition .= ' AND pre.id = '.$idpregunta.' AND ';

					$s = new select;
					$s->table("pregunta");
					$s->addSelect("cons_tipopregunta");
					$s->addCondition(" id = ".$idpregunta);
					$s->run();

					$cons = $s->getResults()[0]['cons_tipopregunta'];
					/*
					if($cons == 2){
						$order = 'idOpcion';
					}*/
				}

				if($idencuesta != "none"){
					$table .= ', encuesta as en';
					$sel .= ', en.nombre as nombreEncuesta ';
					$condition .= ' e.idencuesta = en.id ';
					$condition .= ' AND en.id = '.$idencuesta.' AND ';
				}

				$select = new select;
				$select->table('pregunta as pre, respuesta as r, encuestarespondida as e, opciones_para_responder as o'.$table);
				#$select->addSelect('DISTINCT e.id as idEncuestaRespondida, pre.id as idPregunta, pre.cons_tipopregunta, o.opcion, o.ponderacion as ponderacion, e.fecha as fecha, r.idrespuesta as idRespuesta'.$sel);
				$select->addSelect('DISTINCT e.id as idEncuestaRespondida, pre.cons_tipopregunta, o.idpregunta as idPregunta, o.id as idOpcion, o.opcion, o.ponderacion as ponderacion, DATE(e.fecha) as fecha, r.idrespuesta as idRespuesta'.$sel);
				#$select->addSelect('DISTINCT SUM(o.ponderacion) as total, e.fecha as fecha');
				$select->addCondition("e.id = r.idencuestarespondida");
				$select->addCondition("r.idopciones_para_responder = o.id");
				#$select->addCondition("r.idpregunta = o.idpregunta");
				$select->addCondition(' e.fecha BETWEEN "'.$fechainicio.'" AND "'.$fechafinal.'" AND '.$condition.' 1=1');
				/*
				$select->addOrder($order);
				$select->typeOrder("DESC");
				*/
				if($cons == 2){
					#$select->addOrder('idOpcion DESC, fecha');
					//múltiple



					$select->addOrder('fecha');
					$select->typeOrder("DESC");


				}else{
					$select->addOrder($order);
					$select->typeOrder("DESC");
				}

				$select->run();

				if( $cons == 2 ){
					//echo '<pre>';
					//print_r($select->getResults());
					$temp = $select->getResults();

					$arrayBueno = $opcionArray = $tempArray = $arrayMultiple = [];

					foreach ($temp as $key => $value) {
						# code...
						if(empty($tempArray[str_replace("-","",$value['fecha'])]) && !$tempArray[str_replace("-","",$value['fecha'])]){
							$tempArray[str_replace("-","",$value['fecha'])] = 1;
							$arrayMultiple[] = $value['fecha'];
						}
						$opcionArray[str_replace("-","",$value['fecha'])][$value['idOpcion']]['total'] = (!$opcionArray[str_replace("-","",$value['fecha'])][$value['idOpcion']]['total'])? 1: ($opcionArray[str_replace("-","",$value['fecha'])][$value['idOpcion']]['total'])+1;
						$opcionArray[str_replace("-","",$value['fecha'])]['fecha'] = $value['fecha'];
						$opcionArray[str_replace("-","",$value['fecha'])][$value['idOpcion']]['nombre'] = $value['opcion'];
					}
					foreach ($opcionArray as $key => $value) {
						# code...
						$arrayBueno[] = $value;
					}

					$resultados["fechas"] = $arrayMultiple;
					$resultados["respuestas"] = $arrayBueno;
					$resultados[0]['cons_tipopregunta'] = 2;
					$consultaCode = new select;
					$consultaCode->table("opciones_para_responder");
					$consultaCode->addSelect("id");
					$consultaCode->addSelect("opcion");
					$consultaCode->addCondition("idpregunta = ".$idpregunta);
					$consultaCode->run();
					$resultados['ids'] = $consultaCode->getResults();
				}
				else{
					$resultados = $select->getResults();
				}
				#var_dump($select->getQuery());

				if($select->getTotal()) {
					print json_encode($resultados);
				}else print json_encode('ERROR');

			break;
			case 'encuesta':

			$select = new select;
			$select->table("encuesta");
			$select->addCondition(" idlocal = ".$idlocal );
			$select->run();

			if($select->getTotal()) {
				$resultados = $select->getResults();
				print json_encode($resultados);
			}else print json_encode('ERROR');
			break;

			case 'ruleta':
			/*
      Ruleta
      Inactivo = 1
      Activo = 2
      */

			$select = new select;

			$select->table("ruleta, dispositivo, local, premio_ruleta");
			$select->addSelect("premio_ruleta.id, premio_ruleta.premio, premio_ruleta.cantidad");
			$select->addCondition(" dispositivo.uuid = '".$_GET['uuid']."'");
			$select->addCondition(" dispositivo.idlocal = ruleta.idlocal ");
			$select->addCondition(" ruleta.idlocal = local.id ");
			$select->addCondition(" ruleta.activa_inactiva = 2 ");
			$select->addCondition(" premio_ruleta.idruleta = ruleta.id ");
			$select->run();

			if($select->getTotal()) {
				$resultados = $select->getResults();

				print json_encode($resultados);
			}else print json_encode('ERROR');
			break;
			case "guardarpremio":
				$data = json_decode($_POST['data'], TRUE);

				$code = substr(md5(uniqid(mt_rand(), true)) , 0, 8);

				$inserten = new insert;
			  $inserten->table("premio_ganado");
			  $inserten->addValue("idruleta", (int)$data[0][0]);
			  $inserten->addValue("idpremio_ruleta", (int)$data[0][1]);
			  $inserten->addValue("idlocal", (int)$data[0][2]);
			  $inserten->addValue("email", $data[0][3]);
			  $inserten->addValue("codigo", $code);
				$inserten->run();

				$select = new select;
				$select->table("premio_ruleta");
				$select->addSelect("cantidad");
				$select->addCondition(" id = ".$data[0][1]);
				$select->run();
				$c = $select->getResults()[0];

				$c = $c['cantidad'] - 1;

				$update = new update;
				$update->table("premio_ruleta");
				$update->addSet("cantidad",(int)$c);
				$update->addCondition("id = ".$data[0][1]);
				$update->run();

				$selectl = new select;
				$selectl->table("local");
				$selectl->addSelect("logo");
				$selectl->addCondition(" id = ".$data[0][2]);
				$selectl->run();
				$l = $selectl->getResults()[0]['logo'];

				if($inserten->getTotal()) {
					mailpremio($data[0][3], 'Ganador', 'Cupon de Descuento', $code, $l, $data[0][4]);
			    //print json_encode($inserten->getQuery());
			    print json_encode('HECHO');
			  }else print json_encode('ERROR');
			break;
			case "idruleta":
				$select = new select;
				$select->table("ruleta, dispositivo, local");
				$select->addSelect(" ruleta.id ");
				$select->addCondition(" dispositivo.uuid = '".$_GET['uuid']."'");
				$select->addCondition(" dispositivo.idlocal = ruleta.idlocal ");
				$select->addCondition(" ruleta.idlocal = local.id ");
				$select->addCondition(" ruleta.activa_inactiva = 2 ");
				$select->run();

				if($select->getResults() != NULL) {
					$resultados = $select->getResults();
					print json_encode($resultados);
				}else print json_encode('ERROR');
			break;
			case "preguntasabiertas":
				$select = new select;
				$select->table(" pregunta, respuesta, encuestarespondida, encuesta");
				$select->addSelect("encuesta.nombre as encuesta, pregunta.pregunta as pregunta, respuesta.respuesta as respuesta, DATE(encuestarespondida.fecha) as fecha");
				$select->addCondition("encuestarespondida.idencuesta = encuesta.id");
				$select->addCondition("respuesta.idpregunta = pregunta.id");
				$select->addCondition("respuesta.idencuestarespondida = encuestarespondida.id");
				$select->addCondition(" pregunta.cons_tipopregunta = 4 ");
				if($encuesta != 'none'){
					$select->addCondition(" encuesta.id = ".$encuesta);
				}
				if($local != 'none'){
					$select->addCondition(" encuesta.idlocal = ".$local);
				}
				$select->addCondition(' encuestarespondida.fecha BETWEEN "'.$fechainicio.'" AND "'.$fechafinal.'" ');
				$select->run();

				if($select->getResults() != NULL) {
					$resultado = $select->getResults();
					$resultados['data'] = $resultado;
					print json_encode($resultados);
				}else print json_encode('ERROR');
			break;
		}
	}

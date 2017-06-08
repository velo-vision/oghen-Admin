<?php
	require("autoload.php");

	function check_login($user, $pass){

		$ps = new select;
		$ps->table("usuario");
		$ps->addCondition("email = '".$user."'");
		$ps->run();
		$resps = $ps->getResults();
		if($resps){
			$hash = $resps[0]['password'];
			$usr = 'usuario';
			$idtb = 1;
		}else{
			$ps2 = new select;
			$ps2->table("usuario_pendiente");
			$ps2->addCondition("email = '".$user."'");
			$ps2->run();
			$resps2 = $ps2->getResults();
			if($resps2){
				$hash = $resps2[0]['password'];
				$usr = 'usuario_pendiente';
				$idtb = 2;
			}else{
				$ps3 = new select;
				$ps3->table("encargado");
				$ps3->addCondition("email = '".$user."'");
				$ps3->run();
				$resps3 = $ps3->getResults();
				$hash = $resps3[0]['password'];
				$usr = 'encargado';
				$idtb = 3;
			}
		}

		if(password_verify($pass, $hash)){
			$consulta = new select;
			$consulta->table($usr);
			$consulta->addCondition("email = '".$user."'");
			$consulta->run();
			$resultados = $consulta->getResults();
		}else $resultados = false;

		if($resultados){
			$id_user = $resultados[0]['id'];
			$dbemail=$resultados[0]['email'];
			$dbnombre=$resultados[0]['nombre'];
			$_SESSION['session_username']=$dbnombre;
			$_SESSION['session_email']=$dbemail;
			$_SESSION['id_user'] = $id_user;
			$_SESSION['idtb'] = $idtb;

			return true;
		}
		else{
			echo '
				<div style="" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
					<div class="modal-dialog modal-sm">
					  <div class="modal-content">
						<div class="modal-header">
						  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <h4 class="modal-title" id="mySmallModalLabel">Mensaje</h4>
						</div>
						<div class="modal-body">
						  La contrase&ntilde;a es incorrecta o el usuario no existe.
						</div>
					  </div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				  </div><!-- /.modal -->
				<script>
					$(document).ready(function(){
						$(".modal").modal("show");
					});

				</script>
			';
			return false;
		}
	}

?>

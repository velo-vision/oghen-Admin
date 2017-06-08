<?php include("header.php"); ?>
<?php
  if( isset($_POST['email']) ){

    mailmessage($_POST['email'], $nombre, '¡Ganaste un premio!', 'Nos complace informarte que haz ganado un premio', '¡Felicidades!');
  }
?>

<!doctype html>
<html class="no-js" lang="">

<?php include("head.php"); ?>

<body class="page-loading">
  <!-- page loading spinner -->
  <div class="pageload">
    <div class="pageload-inner">
      <div class="sk-rotating-plane"></div>
    </div>
  </div>
  <!-- /page loading spinner -->
  <div class="app layout-fixed-header">

	<?php include("sidebar.php"); ?>

	<!-- content panel -->
    <div class="main-panel">

	  <?php include("topheader.php"); ?>

      <!-- main area -->
		<div class="main-content">
			<div class="col-md-6">
				<div class="card bg-white" id="fPrincipal">
					<div class="card-header">
						Premio
					</div>
					<div class="card-block">
						<form role="form" method="POST">
							<div class="form form-horizontal">
								<div class="form-group">
									<label class="col-sm-2 control-label">Email</label>
									<div class="col-sm-10">
										<input type="text" name="email" value="" class="form-control" required>
									</div>
								</div>

								<div class="row buttons" style="text-align: center;">
									<input class="btn btn-primary btn-sm loading-demo mr5" value="Enviar" style="padding: 15px;" type="submit" name="guardar">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-6">

			</div>
			<div class="col-md-12" style="padding: 20px 0px;">
				<img width="100%" src="images/banner-2.png">
			</div>
		</div>
      <!-- /main area -->
    </div>
    <!-- /content panel -->
    <!-- bottom footer -->
    <?php include("footer.php"); ?>
  <!-- endbuild -->
</body>

</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Busqueda de Boletos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" /> 
	<script type="text/javascript" src="../../webmaster/js/app.js"></script>
	<script type="text/javascript" src="../../webmaster/js/interfaz.js"></script>
	<link REL=StyleSheet HREF="../../webmaster/css/style.css" TYPE="text/css"></link>
	<link rel="stylesheet" type="text/css" href="../../webmaster/css/bootstrap.css"  >
	<link rel="stylesheet" type="text/css" href="../../webmaster/css/css.css"  >
</head>
<body>
<div class="container">

	<div class="row container">
		<div class="opcion_disponibilidad col-sm-12 col-md-8 col-lg-8 col-md-offset-2">
			<form action="index.php" class="" name="check_itinerary_form" onsubmit="return check_itinerary()">
				<div class="row">
					<div class="title_disponibilidad col-sm-12 col-md-12 col-lg-12">
						Consulta de Reservaci&oacute;n 
					</div>		
				</div>
				<br/>
				
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="pnr">Codigo de Reservaci&oacute;n :</label><br/>
								<input id="pnr" name="pnr" type="text" placeholder="" class="form-control input-md" required="required" />
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12 col-md-offset-4 col-sm-offset-4">
						 <div class="form-group">	 
											<!-- Button -->
							<div class="form-group">
								<div class="col-md-12">
									<input id="submit" class="btn btn-primary submit" name="submit" type="button" value="Consultar" onclick="return check_itinerary()"/>
								<br/>
								</div>

							</div>

						</div> 

					</div>
				</div><!-- Fin Row Form-->
			</form>
		</div><!-- Fin col-->
		
	</div><!-- Fin Row-->
	<br/>
	<div class="row container" >
		<div class=" col-sm-12 col-md-12 col-lg-12" id="checked_pnr">

		</div>		
	</div>	
</div><!-- Fin Container-->

    <script src="../../webmaster/js/jquery.js"></script>
    <script src="../../webmaster/js/bootstrap.js"></script>
</body>
</html>
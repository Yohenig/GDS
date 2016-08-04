
<?php
	 include_once('../../Config/propertiesAVAIL.php');
	 $properties= new PropertiesAVAIL();
	 $segments=$_POST['Segments'];
	 $count= $_POST["Segments"];
?>

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
<div  class="container">

	<div class="row container">
		<div class=" col-sm-12 col-md-6 col-lg-6 opcion_disponibilidad">
		   <form action="index.php" class="" name="booking_search_form" onsubmit="return booking_submit()">
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12 title_pasajeros">
						 Ingrese los Datos 
				</div>		
			</div>
			<br/>
			<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						 <label class="control-label"> Los campos marcados con <label class="label_datos_pasajeros">*</label>   son obligatorios  </label>
					</div>		
			</div>
			<br/>
			<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<label class="label_datos_pasajeros_principal control-label">Cuentanos en donde te podemos contactar para informarte sobre la Reserva</label> 
					</div>		
			</div>
			<br/>
			<div class="row">
				 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="correo_completo">Correo Electr&oacute;nico:</label><br/>
								<input id="correo_completo" name="correo_completo" type="email" placeholder="" class="form-control input-md" required="required"> <br/>
							</div>
						</div>    		 
				</div><!-- Fin col Form-->

			</div><!-- Fin Row Form-->
			<div class="row">
				 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="nombre_completo">Nombre Completo:</label><br/>
								<input id="nombre_completo" name="nombre_completo" type="text" placeholder="" class="form-control input-md" required="required"> <br/>
							</div>
						</div>    		 
				</div><!-- Fin col Form-->
			</div><!-- Fin Row Form-->
			<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="telefono_completo">N&uacute;mero Telef&oacute;nico:</label><br/>
								<input id="telefono_completo" name="telefono_completo" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
			</div><!-- Fin Row Form-->

			<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						 <hr class="hr_precio"></hr>
					</div>		
			</div>
			
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						 <label class="label_datos_pasajeros_principal">Datos de los Pasajeros</label>  
					</div>		
				</div>
				<br/>
				<div class="row container">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<label class="label_datos_pasajeros">Ingresa nombres y apellidos como aparecen en el Pasaporte</label> 
					</div>		
				</div>
				<br/>

				<?php if($_POST["PassengerTypeQuantityAdulto_0"]>0){for ($i=0; $i< $_POST["PassengerTypeQuantityAdulto_0"];$i++){ ?>
				<div class="row container">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<label class="">Pasajero <?php echo $i+1; ?> Adulto</label> 
					</div>		
				</div>
				<br/>
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="DocType<?php  echo $i; ?>">Tipo de Documento:</label><br/>
								<select id="DocType<?php  echo $i; ?>" class="form-control" name="DocType<?php echo $i; ?>" required>
									<option value="">Seleccione</option>
									<option value="NI">Cedula de Identidad</option>
									<option value="PP">Pasaporte</option>
									<option value="ID">Cedula de Extranjero</option>
								</select>
								 
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">	 
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="DocID<?php  echo $i; ?>">Cedula de Identidad:</label><br/>
								<input id="DocID<?php  echo $i; ?>" name="DocID<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="FirstName<?php  echo $i; ?>">Nombre(s):</label><br/>
								<input id="FirstName<?php  echo $i; ?>" name="FirstName<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">	
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="LastName<?php echo $i; ?>">Apellido(s):</label><br/>
								<input id="LastName<?php  echo $i; ?>" name="LastName<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->

				</div><!-- Fin Row Form-->
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="Telephone<?php  echo $i; ?>">Telefono:</label><br/>
								<input id="Telephone<?php  echo $i; ?>" name="Telephone<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="Email<?php echo $i; ?>">Email:</label><br/>
								<input id="Email<?php  echo $i; ?>" name="Email<?php echo $i; ?>" type="email" placeholder="" class="form-control input-md" required="required"> <br/>
							</div>
						</div>
					     		 
					 </div><!-- Fin col Form-->

				</div><!-- Fin Row Form-->

				<?php }} ?>

				<?php if($_POST["PassengerTypeQuantitybebe_0"]>0){for ($i=0; $i< $_POST["PassengerTypeQuantitybebe_0"];$i++){ ?>
				<div class="row container">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<label class="">Pasajero <?php echo $i+1;?> Infante</label> 
					</div>		
				</div>
				<br/>
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="DocTypeInfante<?php  echo $i; ?>">Tipo de Documento:</label><br/>
								<select id="DocTypeInfante<?php  echo $i; ?>" class="form-control" name="DocTypeInfante<?php echo $i; ?>" required>
									<option value="">Seleccione</option>
									<option value="NI">Cedula de Identidad</option>
									<option value="PP">Pasaporte</option>
									<option value="ID">Cedula de Extranjero</option>
								</select>
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">	 
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="DocIDInfante<?php  echo $i; ?>">Cedula de Identidad:</label><br/>
								<input id="DocIDInfante<?php  echo $i; ?>" name="DocIDInfante<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="FirstNameInfante<?php  echo $i; ?>">Nombre(s):</label><br/>
								<input id="FirstNameInfante<?php  echo $i; ?>" name="FirstNameInfante<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">	
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="LastNameInfante<?php echo $i; ?>">Apellido(s):</label><br/>
								<input id="LastNameInfante<?php  echo $i; ?>" name="LastNameInfante<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->

				</div><!-- Fin Row Form-->
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="TelephoneInfante<?php  echo $i; ?>">Telefono:</label><br/>
								<input id="TelephoneInfante<?php  echo $i; ?>" name="TelephoneInfante<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="EmailInfante<?php echo $i; ?>">Email:</label><br/>
								<input id="EmailInfante<?php  echo $i; ?>" name="EmailInfante<?php echo $i; ?>" type="email" placeholder="" class="form-control input-md" required="required"> <br/>
							</div>
						</div>
					     		 
					 </div><!-- Fin col Form-->

				</div><!-- Fin Row Form-->

				<?php }} ?>
			<?php if($_POST["PassengerTypeQuantityNino_0"]>0){for ($i=0; $i< $_POST["PassengerTypeQuantityNino_0"];$i++){ ?>
				<div class="row container">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<label class="">Pasajero <?php echo $i+1; ?> Nino</label> 
					</div>		
				</div>
				<br/>
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="DocTypeNino<?php  echo $i; ?>">Tipo de Documento:</label><br/>
								<select id="DocTypeNino<?php  echo $i; ?>" class="form-control" name="DocTypeNino<?php echo $i; ?>" required>
									<option value="">Seleccione</option>
									<option value="NI">Cedula de Identidad</option>
									<option value="PP">Pasaporte</option>
									<option value="ID">Cedula de Extranjero</option>
								</select>
								 
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">	 
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="DocIDNino<?php  echo $i; ?>">Cedula de Identidad:</label><br/>
								<input id="DocIDNino<?php  echo $i; ?>" name="DocIDNino<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="FirstNameNino<?php  echo $i; ?>">Nombre(s):</label><br/>
								<input id="FirstNameNino<?php  echo $i; ?>" name="FirstNameNino<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">	
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="LastNameNino<?php echo $i; ?>">Apellido(s):</label><br/>
								<input id="LastNameNino<?php  echo $i; ?>" name="LastNameNino<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->

				</div><!-- Fin Row Form-->
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="TelephoneNino<?php  echo $i; ?>">Telefono:</label><br/>
								<input id="TelephoneNino<?php  echo $i; ?>" name="TelephoneNino<?php  echo $i; ?>" type="text" placeholder="" class="form-control input-md" required="required">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="label_datos_pasajeros">*</label><label class="control-label" for="EmailNino<?php echo $i; ?>">Email:</label><br/>
								<input id="EmailNino<?php  echo $i; ?>" name="EmailNino<?php echo $i; ?>" type="email" placeholder="" class="form-control input-md" required="required"> <br/>
							</div>
						</div>
					     		 
					 </div><!-- Fin col Form-->

				</div><!-- Fin Row Form-->

				<?php }} ?>

				<br/>
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<label class="label_datos_pasajeros_principal control-label">Que deseas hacer con tu solicitud?</label> 
					</div>		
				</div>
				<br/>
				<div class="row">
				 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-6">
								 <label class="control-label" for="comprar">Comprar:</label>
								<input id="comprar" name="comprar" type="radio" value="compra" placeholder="" onclick="typeIsseu(this);"  required="required"> <br/>
							</div>
							<div class="col-md-6">
								<label class="control-label" for="comprar">Pagar en Agencia:</label>
								<input id="comprar" name="comprar" type="radio" value="agencia" placeholder=""  onclick="typeIsseu(this);"  required="required"> <br/>
							</div>
						</div>    		 
				</div><!-- Fin col Form-->

			</div><!-- Fin Row Form-->

				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12 col-md-offset-4">
						 <div class="form-group">	 
											<!-- Button -->
							<div class="form-group">
								<div class="col-md-12">
									<input id="submit" class="btn btn-primary submit" name="submit" type="submit" value="Reservaci&oacute;n" onclick="booking_submit();"/>
								</div>
							</div>
						</div> 

					</div>
				</div><!-- Fin Row Form-->
			</form>
		</div><!-- Fin col-->
		<div class="opcion_disponibilidad col-sm-12 col-md-3 col-lg-3  col-md-offset-2 ">
			<form  class="" name="information_reserva">
				<br/>
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<center><label class="label_datos_pasajeros_principal control-label">Resumen de la Reserva</label></center>
					</div>		
				</div>
				<br/>
				<div class="row ">
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									
									<label class="control-label label_reserva_pasajero" for="">
										<?php if($_POST["PassengerTypeQuantityAdulto_0"]>0){ ?>
											<?php echo $_POST["PassengerTypeQuantityAdulto_0"]; ?> Adulto(s):
										<?php } ?>
										<br/>
										<?php if($_POST["PassengerTypeQuantityNino_0"]>0){ ?>
											<?php echo $_POST["PassengerTypeQuantityNino_0"]; ?> Niño(s):
										<?php } ?>
										<br/>
										<?php if($_POST["PassengerTypeQuantitybebe_0"]>0){ ?>
											<?php echo $_POST["PassengerTypeQuantitybebe_0"]; ?> Infante(s):
										<?php } ?>

									</label><br/>
								</div>
								<div class="col-md-6">
									<?php if($_POST["PassengerTypeQuantityAdulto_0"]>0){ ?>
											 <label class="control-label" name="taxe" for=""><?php echo number_format($_POST["BaseFareAdulto_1"],2, ',', '.'); ?></label>
									<?php } ?>
									<br/>
										<?php if($_POST["PassengerTypeQuantityNino_0"]>0){ ?>
											 <label class="control-label" name="taxe" for=""><?php echo number_format($_POST["BaseFareNino_1"],2, ',', '.'); ?></label>
										<?php } ?>
									<br/>
									<?php if($_POST["PassengerTypeQuantitybebe_0"]>0){ ?>
											 <label class="control-label" name="taxe" for=""><?php echo number_format($_POST["BaseFareBebe_1"],2, ',', '.'); ?></label>
									<?php } ?>
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Imp + Tasas: </label><br/>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo number_format($_POST["ImpTasas_1"],2, ',', '.'); ?></label><br/>
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				<div class="row">
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Cargos:</label><br/>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo number_format($_POST["Cargos_1"],2, ',', '.'); ?></label><br/>
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form-->
				
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12 title_pasajeros">
						 <label class="label_total">Total: <?php echo  number_format($_POST["TotalFare_1"],2, ',', '.'); ?></label> 
					</div>		
				</div>
				<br/>
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<div class="form-group">
							<div class="col-md-12">
						 		<label class="href_itinerario">Itinerario</label>
						 	</div>
						 </div>
					</div>		
				</div>
				<br/>
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<div class="form-group">
							<div class="col-md-6">
						 		<label class="icon_ida">&nbsp;&nbsp;&nbsp;</label><label class="control-label label_reserva" for="">&nbsp; IDA</label>
						 	</div>
						</div>    	
					</div>		
				</div>
				<?php 

					
					for ($i=0; $i < $count ; $i++) { 
					  if($_POST["Destino_".$i]==1){
					 
				?>
				
				 
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label label_reserva" for=""> <?php echo $properties->_getDate(substr($_POST["DepartureDateTime_".$i],0,10)); ?></label>
							</div>
						</div> 
					</div>		
				</div>
				 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Sale:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo substr($_POST["DepartureDateTime_".$i],11,8)." "; ?></label>
									<label class="control-label label_city" for=""><?php  echo $properties->_getCity($_POST["DepartureAirport_".$i]); ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
				 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">LLega:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo substr($_POST["ArrivalDateTime_".$i],11,8)." "; ?></label>
									<label class="control-label label_city" for=""><?php  echo $properties->_getCity($_POST["ArrivalAirport_".$i]); ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
				 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Duraci&oacute;n:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo $_POST["JourneyDuration_".$i]; ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
				 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Aerol&iacute;nea:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo $properties->_getAirline($_POST["MarketingAirline_".$i]); ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
				 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Vuelo:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo $_POST["FlightNumber_".$i]; ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
				
				 
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						 <hr class="hr_precio2"></hr>
					</div>		
				</div>
				
				 <?php } }?>
				 <div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<a href="" class="href_itinerario">Detalles del Vuelo</a><br/>
								</div>
							</div>	 
					</div><!-- Fin col Form-->		
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						 <hr class="hr_precio"></hr>
					</div>		
				</div>
				
				
				<?php 
					 
					if($_POST["Type_0"]==2){

				?>

				 <div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<div class="form-group">
							<div class="col-md-6">
						 		<label class="icon_regreso">&nbsp;&nbsp;&nbsp;</label><label class="control-label label_reserva" for="">&nbsp; REGRESO</label>
						 	</div>
						</div>    	
					</div>		
				</div>
				<?php		

						 for ($j=0; $j < $count ; $j++) { 
					  		if($_POST["Destino_".$j]==2){
				 ?>

				
			 
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label label_reserva" for=""> <?php echo $properties->_getDate(substr($_POST["DepartureDateTime_".$j],0,10)); ?></label>
							</div>
						</div> 
					</div>		
				</div>
		 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Sale:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo substr($_POST["DepartureDateTime_".$j],11,8)." "; ?></label>
									<label class="control-label label_city" for=""><?php  echo $properties->_getCity($_POST["DepartureAirport_".$j]); ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
				 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">LLega:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo substr($_POST["ArrivalDateTime_".$j],11,8)." "; ?></label>
									<label class="control-label label_city" for=""><?php  echo $properties->_getCity($_POST["ArrivalAirport_".$j]); ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
			 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Duraci&oacute;n:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo $_POST["JourneyDuration_".$j]; ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
			 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Aerol&iacute;nea:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo $properties->_getAirline($_POST["MarketingAirline_".$j]); ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
			 
				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-6">
									<label class="control-label label_reserva" for="">Vuelo:</label>
								</div>
								<div class="col-md-6">
									<label class="control-label" for=""><?php echo $_POST["FlightNumber_".$j]; ?></label>
								</div>
							</div>
						    			 
					</div><!-- Fin col Form-->		
				</div>
				
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						 <hr class="hr_precio2"></hr>
					</div>		
				</div>
				
				 <?php }}?>

				  <div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<a href="" class="href_itinerario">Detalles del Vuelo</a><br/>
								</div>
							</div>	 
					</div><!-- Fin col Form-->		
				</div>

				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
						 <hr class="hr_precio"></hr>
					</div>		
				</div>
				 <?php } ?>

				

				<div class="row">
					<div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<a href="" class="href_itinerario">Condiciones y Restricciones</a><br/>
								</div>
							</div>	 
					</div><!-- Fin col Form-->		
				</div>
				<br/>
			</form>
		</div><!-- Fin col-->
	</div><!-- Fin Row-->

</div><!-- Fin Container-->

    <script src="../../webmaster/js/jquery.js"></script>
    <script src="../../webmaster/js/bootstrap.js"></script>
</body>
</html>
<?php



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Busqueda de Boletos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" /> 
	<meta name="viewport" content="width=device-width; initial-scale=1.0" />
 
 
     <link rel="stylesheet"  href=="../../webmaster/css/style.css" > 
	<link rel="stylesheet" href="../../webmaster/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../webmaster/css/bootstrap-datetimepicker.css">
	<link rel="stylesheet" type="text/css" href="../../webmaster/css/bootstrap-select.css"  >
	<link rel="stylesheet" type="text/css" href="../../webmaster/css/css.css"  >

  <script type="text/javascript" src="../../webmaster/js/app.js"></script>
	<script type="text/javascript" src="../../webmaster/js/interfaz.js"></script>	
	<script src="../../webmaster/js/jquery.js"></script>
	<script src="../../webmaster/js/bootstrap.js"></script>
	<script src="../../webmaster/js/moment-with-locales.js"></script>
	<script src="../../webmaster/js/bootstrap-datetimepicker.js"></script>
	<script src="../../webmaster/js/bootstrap-select.js"></script>
    <script src="../../webmaster/js/bootstrap-spinner.js"></script>
    <script src="../../webmaster/js/mousehold.js"></script>
    <script src="../../webmaster/js/bootstrap-modal-popover.js"></script>
     <script src="../../webmaster/js/moment.js"></script>
     <script type="text/javascript" src="../../webmaster/js/typeahead.js"></script> 
</head>
<body>
<div id="avail_search" class="container">
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-12">
			<ul class="nav nav-tabs ui-tabs-nav ui-helper-clearfix ui-widget-header ui-corner-all">
			  <li class="active"><a data-toggle="tab" href="#vuelos">Vuelos</a></li> 
			</ul>
			<div class="tab-content ">
				  <div id="vuelos" class="tab-pane fade in active">
				  
					    <form action="index.php" name="avail_search_form" onsubmit="return avail_search()">
					    	<div class="row">
						    	<div class="col-sm-12 col-md-9 col-lg-9">
						    		
							    	<div class="container row">
							    		<div class=" col-sm-12 col-md-2 col-lg-2">
							    			<div class="form-group">
												   <br/>
								    			<div class="input-group input-group-sm">
												  <span class="input-group-addon" id="sizing-addon2" > <span class="glyphicon glyphicon-arrow-right"></span></span>
												  <input type="text" class="form-control source"   id="source" name="source" placeholder="Origen" aria-describedby="sizing-addon2">
												</div>
											  
												</div>
							    		</div>
							    		<div class=" col-sm-12 col-md-2 col-lg-2">
							    			<div class="form-group">
												   <br/>
								    			<div class="input-group input-group-sm">
												  <span class="input-group-addon" id="sizing-addon3" > <span class="glyphicon glyphicon-arrow-right"></span></span>
												  <input type="text" class="form-control dest"   id="dest" name="dest" placeholder="Destino" aria-describedby="sizing-addon3">
												</div>
											  
												</div>
							    		</div>
							    		<div class=" col-sm-4 col-md-2 col-lg-2">
							    			 
							    				<br/>
							    			  <div class="form-group">
									            <div class='input-group date' id='datetimepickerdest1'>
									                <input type='text' name="dateDest1" class="form-control" />
									                <span class="input-group-addon">
									                    <span class="glyphicon glyphicon-calendar"></span>
									                </span>
									            </div>
									        </div>
										</div>
							    		<div class=" col-sm-4 col-md-2 col-lg-2"><br/>
							    			 <div class="form-group">
									            <div class='input-group date' id='datetimepickerdest2'>
									                <input type='text' name="dateDest2" class="form-control" />
									                <span class="input-group-addon">
									                    <span class="glyphicon glyphicon-calendar"></span>
									                </span>
									            </div>
									        </div>
							    		</div>
							    		<div class=" col-sm-4 col-md-2 col-lg-2">
							    			<br/>
							    			<div class="form-group"> 
							    				<a href="#popupBottom" role="button"  data-modal-position="relative" class="btn btn-default" data-toggle="modal-popover" data-placement="bottom"><span class="glyphicon glyphicon-user"></span> Pasajeros</a>
							    			

							    			<div id="popupBottom" class="popover">
											    <div class="arrow"></div>
											    <h3 class="popover-title">Pasajeros</h3>
											    <div class="popover-content">
											    	<div class="container row">
											    		<div class="col-sm-3 col-md-3">
											         		<label class="control-label" for="adulto">Adultos</label>
											    			<div class="input-group">
															    <span class="input-group-btn">
															        <button type="button" class="btn btn-default" data-value="-1" data-target="#adulto" data-toggle="spinner">
															            <span class="glyphicon glyphicon-minus-sign"></span>
															        </button>
															    </span>
															    <input type="text" data-ride="spinner" name="adulto" id="adulto" class="form-control input-number" value="1" data-min="0" data-max="9">
															    <span class="input-group-btn">
															        <button type="button" class="btn btn-default" data-value="1" data-target="#adulto" data-toggle="spinner" data-on="mousehold">
															            <span class="glyphicon glyphicon-plus-sign"></span>
															        </button>
															    </span>
															</div>
														</div>
													</div>
													<div class="container row">
											    		<div class="col-sm-3 col-md-3">
											         		<label class="control-label" for="mayor">3 Edad</label>
											    			<div class="input-group">
															    <span class="input-group-btn">
															        <button type="button" class="btn btn-default" data-value="-1" data-target="#mayor" data-toggle="spinner">
															            <span class="glyphicon glyphicon-minus-sign"></span>
															        </button>
															    </span>
															    <input type="text" data-ride="spinner" name="mayor" id="mayor" class="form-control input-number" value="0" data-min="0" data-max="9">
															    <span class="input-group-btn">
															        <button type="button" class="btn btn-default" data-value="1" data-target="#mayor" data-toggle="spinner" data-on="mousehold">
															            <span class="glyphicon glyphicon-plus-sign"></span>
															        </button>
															    </span>
															</div>
														</div>
													</div>
													<div class="container row">
											    		<div class="col-sm-3 col-md-3">
											         		<label class="control-label" for="nino">Ni&ntilde;os</label>
											    			<div class="input-group">
															    <span class="input-group-btn">
															        <button type="button" class="btn btn-default" data-value="-1" data-target="#nino" data-toggle="spinner">
															            <span class="glyphicon glyphicon-minus-sign"></span>
															        </button>
															    </span>
															    <input type="text" data-ride="spinner" name="nino" id="nino" class="form-control input-number" value="0" data-min="0" data-max="9">
															    <span class="input-group-btn">
															        <button type="button" class="btn btn-default" data-value="1" data-target="#nino" data-toggle="spinner" data-on="mousehold">
															            <span class="glyphicon glyphicon-plus-sign"></span>
															        </button>
															    </span>
															</div>
														</div>
													</div>
													<div class="container row">
											    		<div class="col-sm-3 col-md-3">
											         		<label class="control-label" for="bebe">Bebes</label>
											    			<div class="input-group">
															    <span class="input-group-btn">
															        <button type="button" class="btn btn-default" data-value="-1" data-target="#bebe" data-toggle="spinner">
															            <span class="glyphicon glyphicon-minus-sign"></span>
															        </button>
															    </span>
															    <input type="text" data-ride="spinner" name="bebe" id="bebe" class="form-control input-number" value="0" data-min="0" data-max="1">
															    <span class="input-group-btn">
															        <button type="button" class="btn btn-default" data-value="1" data-target="#bebe" data-toggle="spinner" data-on="mousehold">
															            <span class="glyphicon glyphicon-plus-sign"></span>
															        </button>
															    </span>
															</div>
														</div>
													</div>
											    </div>
											</div>

											</div> 
							    			 
							    			 
							    		</div>
							    		


							    		 
							    	</div>
							    	<div class=" container row">

							    		<div class=" col-sm-12 col-md-2 col-lg-2">
							    			<div class="form-group">
												   
												  	<br/>
												    <select id="typeDest" name="typeDest" class="form-control selectpicker show-menu-arrow show-tick" >
												      <option data-icon="glyphicon glyphicon-arrow-down" value="1">Ida</option>
												      <option  data-icon="glyphicon glyphicon-sort " value="2">Ida y Vuelta</option>
												    </select>
												 
												</div>
							    		</div>

							    		<div class=" col-sm-12 col-md-2 col-lg-2">
							    			<div class="form-group">
												  	 <br/>
												    <select id="cabin" name="cabin" class=" form-control selectpicker show-menu-arrow show-tick">
												      <option data-icon="glyphicon glyphicon-star-empty " value="Economy" selected="selected">Economy</option>
												      <option data-icon="glyphicon glyphicon-star" value="First">First</option>
												      <option data-icon="glyphicon glyphicon-briefcase" value="Business">Business</option>
												    </select>
												 
												</div>
							    		</div>

							    		<div class=" col-sm-12 col-md-2 col-lg-2"><br/>
							    			<div class="form-group">
												   
								    			<div class="input-group input-group-sm">
												  <span class="input-group-addon" id="sizing-addon3" > <span class="glyphicon glyphicon-plane"></span></span>
												  <input type="text" class="form-control" id="carrier" name="carrier" placeholder="Aerolinea" aria-describedby="sizing-addon3">
												</div>
											  
												</div>
							    		 
							    		</div>
							    		
							    			<div class=" col-sm-12 col-md-3 col-lg-3">
							    				<br/>
							    		 		<div class="form-group">
							    		 			<div class="input-group input-group-sm">
												  	 <span class="input-group-addon" id="sizing-addon3" >Vuelos Directos</span>												  	 
												    <select id="direct" name="direct" class="form-control selectpicker show-menu-arrow show-tick">
												      <option value="false">No</option>
												      <option value="true">Si</option>
												    </select>
												  </div>
												</div>

							    			</div>
							    			
							    	
							    	</div><br/>

						    	</div>

						    	<div class="col-sm-12 col-md-3 col-lg-3"><br/>
						    			<!-- Button -->
										<div class="form-group">
											 <div class="col-md-4 col-md-offset-2">
												<input id="submit" class="btn btn-primary submit" name="submit" type="button" value="Buscar" onclick="return avail_search()"/>
											</div>
											
										</div>

								</div>
							</div>
					    </form>
				  </div>
				  
				 
			</div>
				
		</div>	
	

	</div>			

</div>
 
<div class="container" >
	<div class="row" >
		<div id="avail_result" class=" col-sm-12 col-md-11 col-lg-11" ></div>
	</div>
</div>
<div class="container" >
	<div class="row" >
	<div id="booking_search" class="col-sm-12 col-md-10" ></div>
</div>
<div class="container" >
	<div class="row" >
	<div  id="issue" class="col-sm-12 col-md-8 col-lg-8 col-offset-2 "></div>
	</div>
</div>

<script type="text/javascript">
    $(function () {
        $('#datetimepickerdest1').datetimepicker({
             
            format: 'DD-MM-YYYY'
        });
        $('#datetimepickerdest2').datetimepicker({
            useCurrent: false, //Important! See issue #1075
            format: 'DD-MM-YYYY'
        });
        $("#datetimepickerdest1").on("dp.change", function (e) {
            $('#datetimepickerdest2').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepickerdest2").on("dp.change", function (e) {
            $('#datetimepickerdest1').data("DateTimePicker").maxDate(e.date);
        });
    });
</script>
<script>
	$(document).ready(function() {
 
	$('input.dest').typeahead({
	  name: 'country',
	  remote : '../../Controller/countryController.php?query=%QUERY'

	});
	$('input.source').typeahead({
	  name: 'country',
	  remote : '../../Controller/countryController.php?query=%QUERY'

	});
 
	})
</script>
   
</body>
</html>

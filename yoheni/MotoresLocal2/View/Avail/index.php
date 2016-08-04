<?php
function select_number($tipo) {
	$text = '';

	if($tipo=='adulto' || $tipo=='mayor' ){
		for ($i = 0 ; $i <= 9; $i++) {
				if ($i == 1 && $tipo!='mayor' ) {
					$text .= "<option selected=\"selected\" value=\"$i\">$i</option>";
				} else {
					$text .= "<option value=\"$i\">$i</option>";
				}
		}
	}
	if($tipo=='nino'){
		for ($i = 0 ; $i <= 9; $i++) {
				if ($i == 0) {
					$text .= "<option selected=\"selected\" value=\"$i\">$i</option>";
				} else {
					$text .= "<option value=\"$i\">$i</option>";
				}
		}
	}
	if($tipo=='bebe'){
		for ($i = 0 ; $i <= 1; $i++) {
				if ($i == 0) {
					$text .= "<option selected=\"selected\" value=\"$i\">$i</option>";
				} else {
					$text .= "<option value=\"$i\">$i</option>";
				}
		}
	}
	return $text;
}

function select_day() {
	$text = '';
	$day = date('d');
	for ($i = 1 ; $i <= 31; $i++) {
			if ($i < 10) $i = "0$i";
			if ($day == $i) {
				$text .= "<option selected=\"selected\" value=\"$i\">$i</option>";
			} else {
				$text .= "<option value=\"$i\">$i</option>";
			}
	}
	return $text;
}

function select_month() {
	$months = array('Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov');
	$text = '';
	$month = date('n');
	$year = date('Y');
	for ($i = $month ; $i < (12+$month); $i++) {
		if ($i == 12) {
			$text .= "<option value=\"$year-12-\">Dec $year</option>";
		} else {
			$mt = $m = $i%12;
			if ($m < 10) $mt = "0$m";
			$y = $year + floor($i/12);
			$text .= "<option value=\"$y-$mt-\">$months[$m] $y</option>";
		}
	}
	return $text;
}

function select_airport() {
	$text = '';
	foreach (array('AEP', 'COR', 'MDQ', 'SLA', 'NQN', 'BRC', 'LIM', 'CUZ', 'HUE', 'CIX', 'IQT') as $a)
		$text .= "<option value=\"$a\">$a</option>";
	return $text;
}
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
<div id="avail_search" class="container">
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-12">
			<ul class="nav nav-tabs ui-tabs-nav ui-helper-clearfix ui-widget-header ui-corner-all">
			  <li class="active"><a data-toggle="tab" href="#vuelos">Vuelos</a></li>
			   
			</ul>
			<div class="tab-content ">
				  <div id="vuelos" class="tab-pane fade in active">
				  
					    <form action="index.php" name="avail_search_form" onsubmit="return avail_search()">
					    		
					    	<div class="container row">
					    		<div class=" col-sm-12 col-md-6 col-lg-3">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="source">Origen:</label><br/>
										    <select id="source" name="source" class="form-control">
										      <option value="CCS">Caracas</option>
										    </select>
										  </div>
										</div>
					    		</div>
					    		<div class=" col-sm-12 col-md-6 col-lg-3">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="dest">Destino:</label><br/>
										    <select id="dest" name="dest" class="form-control">
										      <option value="AUA">ARUBA</option>
										    </select>
										  </div>
										</div>
					    		</div>
					    		<div class=" col-sm-12 col-md-6 col-lg-3">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="dateseld">Fecha de Salida: </label><br/>
										    <select name="dateseld"><?php echo select_day(); ?></select> of <select name="dateselmy"><?php echo select_month(); ?></select>
										  </div>
										</div>
					    		</div>
					    		<div class=" col-sm-12 col-md-6 col-lg-3">
					    			<div class="form-group">
										  <div class="col-md-12" >
										  	<label class="control-label" for="date2">Fecha de Regreso: </label><br/>
										    <select name="date2"><?php echo select_day(); ?></select> of <select name="dateselmy2"><?php echo select_month(); ?></select>
										  </div>
										</div>
					    		</div>


					    		 
					    	</div>
					    	<div class=" container row">
					    		<div class=" col-sm-12 col-md-6 col-lg-3">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="cabin">Clase:</label><br/>
										    <select id="cabin" name="cabin" class="form-control">
										      <option value="Economy" selected="selected">Economy</option>
										      <option value="First">First</option>
										      <option value="Business">Business</option>
										    </select>
										  </div>
										</div>
					    		</div>

					    		<div class=" col-sm-12 col-md-6 col-lg-3">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="viaje">Aerolinea:</label><br/>
										    <input id="carrier" name="carrier" type="search" placeholder="" class="form-control input-md" required="">
										  </div>
										</div>
					    			 
					    		</div>
					    		<div class=" col-sm-12 col-md-6 col-lg-2">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="viaje">Tipo de Viaje:</label><br/>
										    <select id="viaje" name="viaje" class="form-control">
										      <option value="Ida">Ida</option>
										      <option value="Ida_Vuelta">Ida y Vuelta</option>
										      <option value="Multiples_Destinos">Multiples Destinos</option>
										    </select>
										  </div>
										</div>
					    		</div>
					    			<div class=" col-sm-12 col-md-6 col-lg-2 col-lg-offset-1">
					    		 <div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="direct">Solo Vuelos Directos:</label><br/>
										    <select id="direct" name="direct" class="form-control">
										      <option value="false">No</option>
										      <option value="true">Si</option>
										    </select>
										  </div>
										</div>

					    		</div>
					    	
					    	</div><br/>
					    	<div class="container row">
					    		<div class=" col-sm-6 col-md-2 col-lg-2">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="adulto">Adulto</label>
										  	<label class="control-label" for="adulto">(+ 12 a&ntilde;os):</label>
										  	<select name="adulto" class="form-control"><?php echo select_number('adulto'); ?></select>
										  </div>
									</div>
					    		</div>
					    		<div class=" col-sm-6 col-md-2 col-lg-2">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="mayor">3 Edad</label>
										  	<label class="control-label" for="mayor">(+60 a&ntilde;os):</label>
										  	<select name="mayor" class="form-control"><?php echo select_number('mayor'); ?></select>
										  </div>
									</div>
					    		</div>
					    		<div class=" col-sm-6 col-md-2 col-lg-2">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="nino">Ni&ntilde;os</label>
										  	<label class="control-label" for="nino">(2-11 a&ntilde;os):</label>
										  	<select name="nino" class="form-control"><?php echo select_number('nino'); ?></select>
										  </div>
									</div>
					    		</div>	
					    		<div class=" col-sm-6 col-md-2 col-lg-2">
					    			<div class="form-group">
										  <div class="col-md-12">
										  	<label class="control-label" for="bebe">Bebes</label>
										  	<label class="control-label" for="bebe">(0-23m):</label>
										  	<select name="bebe" class="form-control"><?php echo select_number('bebe'); ?></select>
										  </div>
									</div>
					    		</div>

					    		<div class=" col-sm-12 col-md-2 col-lg-2 col-lg-offset-2">
					    		 	<div class="form-group">	 
										<!-- Button -->
											<div class="form-group">
												  <div class="col-md-12">
												  	<input id="submit" class="btn btn-primary submit" name="submit" type="button" value="Buscar" onclick="return avail_search()"/>
												  </div>
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
		<div id="avail_result" class=" col-sm-12 col-md-11 col-lg-11" style="visibility:hidden;height:0px;position:absolute;"></div>
	</div>
</div>
<div class="container" >
	<div class="row" >
	<div id="booking_search" class="col-sm-12 col-md-10" style="visibility:hidden;height:0px;position:absolute;"></div>
</div>
<div class="container" >
	<div class="row" >
	<div  id="issue" class="col-sm-12 col-md-8 col-lg-8 col-offset-2 " style="visibility:hidden;height:0px;position:absolute;"></div>
	</div>
</div>


    <script src="../../webmaster/js/jquery.js"></script>
    <script src="../../webmaster/js/bootstrap.js"></script>
</body>
</html>

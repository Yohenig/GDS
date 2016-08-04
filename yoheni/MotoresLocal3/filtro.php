<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Busqueda de Boletos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" /> 
	<script type="text/javascript" src="webmaster/js/app.js"></script>
	<script type="text/javascript" src="webmaster/js/interfaz.js"></script>
	<link REL=StyleSheet HREF="webmaster/css/style.css" TYPE="text/css"></link>
	<link rel="stylesheet" type="text/css" href="webmaster/css/bootstrap.css"  >
	<link rel="stylesheet" type="text/css" href="webmaster/css/css.css"  >
</head>
<body>
<div class="container">

		<input id="filtro" type="text" data-slider-ticks="[10,100,200,300,400]" data-slider-ticks-snap-bounds="30" data-slider-ticks-labels='["Bsf.10","Bsf.100","Bsf.200","Bsf.300","Bsf.400"]' />


</div><!-- Fin Container-->

    <script src="webmaster/js/jquery.js"></script>
    <script src="webmaster/js/bootstrap.js"></script>
    <script type="text/javascript">
    	$("#filtro").slider({
    		ticks: [10,100,200,300,400],
    		ticks_labels:["Bsf.10","Bsf.100","Bsf.200","Bsf.300","Bsf.400"],
    		ticks_snap_bounds:30
    	});

    	var slider= new Slider("filtro",{
    		ticks: [10,100,200,300,400],
    		ticks_labels:["Bsf.10","Bsf.100","Bsf.200","Bsf.300","Bsf.400"],
    		ticks_snap_bounds:30
    	});




    </script>
</body>
</html>
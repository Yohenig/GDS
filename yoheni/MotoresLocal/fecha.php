<?php
$date = date_create('2000/01/01');
echo date_format($date, 'd  F  Y H:i:s');
echo date("c");

setlocale(LC_TIME, 'es_ES.UTF-8');
$date = gmmktime(0,0,0,substr("2016-08-03",8,2),substr("2016-08-03",5,2),substr("2016-08-03",0,4));
$date_f= strftime("%d de %B de %Y", $date);
echo $date_f;
echo substr("2016-07-10",5,2);
echo substr("2016-08-03",8,2);
 

?>
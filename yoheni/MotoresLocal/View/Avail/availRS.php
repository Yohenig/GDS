<?php
 
 include_once('../../Config/HttpConnection.php');
 include_once('../../Config/propertiesAVAIL.php');
 include_once('../../Controller/XmlController.php');
 include_once('../../Model/Price.php');
$string = file_get_contents("../../Controller/OriginDestinationInformation.json");
$OriginDestinationInformation= json_decode($string, true);

$http = new HttpConnection();
$http->init();//Iniciar conexion con el servidor KIU
//echo $string ;

//echo "<pre>".htmlentities(print_r($OriginDestinationInformation, true)) ."</pre>";

 $properties= new PropertiesAVAIL();
 $count=0;$count2=0;
		//-------------------------------------------------------------------------------------------------
		if(count($OriginDestinationInformation)>0){

		$count=count($OriginDestinationInformation[1]['OriginDestinationOptions']['OriginDestinationOption']);

		if ($OriginDestinationInformation[1]['TypeDest']==2) {
			$count2=count($OriginDestinationInformation[2]['OriginDestinationOptions']['OriginDestinationOption']);			 
		}

		$html='	<div class="row container">';
		$html.=' <div class="filtro_resultados col-sm-12 col-md-2 col-lg-2">
									<div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
											<span class="title_filtrar">Filtrar Resultados</span> 
										</div>		
									</div>
									<div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
											<hr class="hr_precio" />
										</div>		 
									</div>
			    </div>';
		$html.='<div class="col-sm-12 col-md-9 col-lg-9 col-md-offset-1 col-lg-offset-1">
									<div class="row">
										<div class="title_disponibilidad col-sm-12 col-md-12 col-lg-12">
											Selecciona tu vuelo desde <span class="text_destinos">'.$properties->_getCity($OriginDestinationInformation[1]['OriginLocation'][0]).'</span> hasta <span class="text_destinos">'.$properties->_getCity($OriginDestinationInformation[1]['DestinationLocation'][0]).'</span>
										</div>		
									</div>';
	if ($OriginDestinationInformation[1]['TypeDest']==1) {	 
			for ($i=1; $i <=1 ; $i++) { 
				$v=1;
	
				    while($v<=$count){
				    	$html.='<!-- Opcion General-->
											<div class="row opcion_disponibilidad">';

									$html.='<div class="col-sm-12 col-md-9 col-lg-9">';
									$html.='<div class="row">
											<div class="col-sm-12 col-md-12 col-lg-12">';
											//Validacion de Escalas------------------------------------------------------------------------------
									$msg_stops="";
									$flightCount=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);
									$option_params = "{'SegmentsCount':" . $flightCount . ", 'Segments':Array(";
									if($flightCount<=1 ){
										$msg_stops="Directo";
										 
									}
									if($flightCount >1 ){
										$msg_stops= ($flightCount-1)." Escala(s)";
									
									}

									//-----------------------------------------------------------------------------------------------------
									$dataPrice = array();
									//Llamada al metodo AirPriceRQ para armar el xml-------------------------------------------------------------
										$TotalFare=0;$BaseFare=0;$ImpTasas=0;$Cargos=0;$BaseFareAdulto=0;$BaseBebe=0;$BaseFareNino=0; //Variables para totalizar el costo de todos los pasajero
							for($z=1; $z<=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);$z++){
								//echo $i.".".$v."<br/>";
								//echo "z: ".$z."<br/> "; 
								$dairport = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureAirport']['LocationCode'][0];
						 
								$aairport = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['ArrivalAirport']['LocationCode'][0];
								$flight = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['FlightNumber'][0];
								$time =  $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['JourneyDuration'][0];
								$ddatetime = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureDateTime'][0];
								$adatetime = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['ArrivalDateTime'][0];
								$stops = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['StopQuantity'][0];
								$airline = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['MarketingAirline']['CompanyShortName'][0];
								
								
								$airline2=$properties->_getAirline($airline);
								$dairport2=$properties->_getCity($dairport);
								$aairport2=$properties->_getCity($aairport);

								$meal = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['Meal']['MealCode'][0];						
								$adulto= $OriginDestinationInformation[1]['PassengerTypeQuantityAdulto'];
								$bebe= $OriginDestinationInformation[1]['PassengerTypeQuantityBebe'];
								$nino= $OriginDestinationInformation[1]['PassengerTypeQuantityNino'];
								$mayor=$OriginDestinationInformation[1]['PassengerTypeQuantityMayor'];
								$type=$OriginDestinationInformation[1]['TypeDest'];

								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['DepartureAirport']['LocationCode']=$dairport;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['ArrivalAirport']['LocationCode']=$aairport;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['FlightNumber']=$flight;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['JourneyDuration']=$time;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['DepartureDateTime']=$ddatetime;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['ArrivalDateTime']=$adatetime;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['StopQuantity']=$stops;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['MarketingAirline']['CompanyShortName']=$airline;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['Meal']['MealCode']=$meal;
								$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityAdulto']=$adulto;
								$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityBebe']=$bebe;
								$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityNino']=$nino;
								$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityMayor']=$mayor;
								$dataPrice['OriginDestinationOption'][1]['TypeDest']=$type;

								$available_classes = array();
								foreach ( $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['BookingClassAvail'] as $bca) {
									if (($bca['ResBookDesigQuantity'][0] >= '1') && ($bca['ResBookDesigQuantity'][0] <= '9')) {
										$available_classes[] = $bca['ResBookDesigCode'][0];

									}
								}
								if ($available_classes == array()) {
									$option = false;
									break;
								}
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['ResBookDesigCode']=$available_classes[0];
								$class_list = "Array('" . implode("', '", $available_classes) . "')";

								$option_params .= "{MarketingAirline:'$airline', FlightNumber:$flight, DepartureDateTime:'$ddatetime', ArrivalDateTime:'$adatetime', ";
								$option_params .= "PassengerTypeQuantityAdulto:'$adulto', PassengerTypeQuantityNino:$nino, PassengerTypeQuantity3edad:'$mayor', PassengerTypeQuantitybebe:'$bebe', JourneyDuration:'$time', ";
								$option_params .= "DepartureAirport:'$dairport', ArrivalAirport:'$aairport', Destino:'1', Type:'$type', ResBookDesigCode:'$available_classes[0]', Classes:$class_list},";
								

								$html.='<div class="row" style="margin-top:10px;">	
								               <div class="col-sm-12 col-md-3 col-lg-3">';
								$html.='		<img src="../../webmaster/img/btn-ida.png" widht="60px" height="20px" />';

								$html.='        </div>';

								//echo $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureDateTime'];

								  $html.=' 
								            <div class="col-sm-12 col-md-9 col-lg-9">
								               		<span>'.$properties->_getDate(substr($ddatetime,0,10)).'</span>
								               		<span> ,de </span><span>'.$dairport2.' ('.$dairport.')'.'</span>
								               		<span> a </span><span>'.$aairport2.' ('.$aairport.')'.'</span>
								             </div>';
								             
								   	$html.='  </div>'; // Cierre de 1 Fila
									$html.=' <div class="row " style="margin-top:10px;">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               		<span class="text_campos">Sale:</span><span  class="text_campos2" >'.substr($ddatetime,11,8).'</span>
								               		<span class="text_campos">LLega:</span><span  class="text_campos2"> '.substr($adatetime ,11,8).'</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               		<span  class="text_campos2">'.$time.'</span><span  class="text_campos3"> '.$msg_stops.' </span>
								               		<span ><img src="../../webmaster/img/btn-arw-r.png" widht="20px" height="20px" /></span>
								               		<span  class="text_campos2"> '.$airline2.' </span>
								               </div>
											</div>';// Cierre de 2 Fila
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-12 col-lg-12">
								               	 <hr class="hr_precio" />
								               </div>';
									$html.=' </div>';// Cierre de 3 Fila	

							}
							//echo "<br/>";
							
							
						 
							//echo "<br/>";
							$html.='</div>';
							$html.='</div>';
							$html.='</div>';
							 
							 //MANEJO DE PRECIOS COMBINADOS-----------------------------------------
							 				//Instancia con el xmlController
											$xmlController= new XmlController();
											$request=$xmlController->AirPriceRS($dataPrice);//llamado al metodo con el objeto @param priceModel2 para armar XML para obtener los precios por cada intineraio encontrado
											//echo "<pre>".htmlentities(print_r($dataPrice, true)) ."</pre>";
											$response= $http->post($request); // Procesando XML armado 
										 	//echo "<pre>".htmlentities(print_r($request, true)) ."</pre>";
											$xml = simplexml_load_string($response);//Tranformacion del xml en string
										 	//echo "<pre>".htmlentities(print_r($response, true)) ."</pre>";
											
										

											$price1=$xmlController->processAirPriceRQ($xml,$request,$response); //Procesando los datos resultantes del precio

											if($price1!=null){

												$TotalFare=$price1->getTotalFare();
												$BaseFare=$price1->getBaseFare();
												$ImpTasas=$price1->getImpTasas();
												$Cargos=$price1->getCargos();
												$BaseFareAdulto=$price1->getBaseFareAdulto();
												$BaseFareBebe=$price1->getBaseFareBebe();
												$BaseFareNino=$price1->getBaseFareNino();
												$Error=$price1->getError();
											}
							//--------------------------------------------------------------------
                            $option_params = substr($option_params,0,-1) . ")";
							$option_params .= ",'Isseu':Array({TotalFare:'$TotalFare',BaseFare:'$BaseFare',ImpTasas:'$ImpTasas',Cargos:'$Cargos',BaseFareAdulto:'$BaseFareAdulto',BaseFareBebe:'$BaseFareBebe',BaseFareNino:'$BaseFareNino'})";
							$option_params .="}";
							//echo  $option_params. "<br/>";

							$html.='<!-- Muestra los precios-->
									<div class="precio_disponibilidad col-sm-12 col-md-3 col-lg-3">';
								
							if($TotalFare!=0){

								$html.='
											<div class="row">	
								               <div class="col-sm-12 col-md-12 col-lg-12">
								               	<span class="text_titulo_precio">Total</span>
								               </div>
											</div>
											<div class="row">	
								               <div class="text_valor_precio col-sm-12 col-md-12 col-lg-12">
								               	<span class="text_valor_precio" >Bsf. '.number_format($TotalFare,2, ',', '.').'</span>
								               </div>
											</div>
											<div class="row">	
								               <div class="col-sm-12 col-md-12 col-lg-12">
								               	<hr class="hr_precio" />
								               </div>
											</div>';
								if($adulto>=1){	
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">'.$adulto.' Adulto:</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($BaseFareAdulto,2, ',', '.').'</span>
								               </div>
											</div>';									 
								}
								if($nino>=1){	
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">'.$nino.' Niño:</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($BaseFareNino,2, ',', '.').'</span>
								               </div>
											</div>';									 
								}
								if($bebe>=1){	
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">'.$bebe.' Infante:</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($BaseFareBebe,2, ',', '.').'</span>
								               </div>
											</div>';									 
								}
								  
								   
								 $html.=' <div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Imp+Tasas:</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($ImpTasas,2, ',', '.').'</span>
								               </div>
											</div>
											<div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Cargos:</span>
								               </div>
								                <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($Cargos,2, ',', '.').'</span>
								               </div>
											</div>

											<div class="row">	
								               <div class="col-sm-12 col-md-8 col-lg-12">
								               	<input  class="btn btn-primary" style="margin-top:10px;" name="submit" type="button" value="Seleccionar" onclick="reservation('.$option_params.')"/>
								               </div>
											</div>';


							}


							$html.='</div>';


							$html.='</div><!-- Fin Opcion General-->';	

				    	$v++;

					}// end While	
				
			} // end For Opcion 1
	}

	if ($OriginDestinationInformation[1]['TypeDest']==2) {
		 
			for ($i=1; $i <=1 ; $i++) { 
				$v=1;
				
				for ($j=$i+1; $j <=2 ; $j++) { 

				    while($v<=$count){
				    	
				    	
						for ($x=1; $x <=$count2 ; $x++) { 
									 $html.='<!-- Opcion General-->
											<div class="row opcion_disponibilidad">';
									$html.='<div class="col-sm-12 col-md-9 col-lg-9">';
									$html.='<div class="row">
											<div class="col-sm-12 col-md-12 col-lg-12">';
									

									//Validacion de Escalas------------------------------------------------------------------------------
									$msg_stops="";
									$flightCount=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);
									$flightCount2=count($OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment']);
									
									$option_params = "";					
									$option_params = "{'SegmentsCount':" . ($flightCount+$flightCount2). ", 'Segments':Array(";

									if($flightCount<=1 || $flightCount2<=1){
										$msg_stops="Directo";
										$msg_stops2=$msg_stops;
									}
									if($flightCount>1 || $flightCount2>1){
										$msg_stops= ($flightCount-1)." Escala(s)";
										$msg_stops2=($flightCount2-1)." Escala(s)";
									}

									//-----------------------------------------------------------------------------------------------------
									$dataPrice = array();
									//Llamada al metodo AirPriceRQ para armar el xml-------------------------------------------------------------
									$TotalFare=0;$BaseFare=0;$ImpTasas=0;$Cargos=0;$BaseFareAdulto=0;$BaseFareBebe=0;$BaseFareNino=0; //Variables para totalizar el costo de todos los pasajero


							for($z=1; $z<=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);$z++){
						 		

								$dairport = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureAirport']['LocationCode'][0];
								$aairport = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['ArrivalAirport']['LocationCode'][0];
								$flight = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['FlightNumber'][0];
								$time =  $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['JourneyDuration'][0];
								$ddatetime = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureDateTime'][0];
								$adatetime = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['ArrivalDateTime'][0];
								$stops = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['StopQuantity'][0];
								$airline = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['MarketingAirline']['CompanyShortName'][0];
								$meal = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['Meal']['MealCode'][0];						
								$adulto= $OriginDestinationInformation[1]['PassengerTypeQuantityAdulto'];
								$bebe= $OriginDestinationInformation[1]['PassengerTypeQuantityBebe'];
								$nino= $OriginDestinationInformation[1]['PassengerTypeQuantityNino'];
								$mayor=$OriginDestinationInformation[1]['PassengerTypeQuantityMayor'];
								$type=$OriginDestinationInformation[1]['TypeDest'];

								$airline2=$properties->_getAirline($airline);
								$dairport2=$properties->_getCity($dairport);
								$aairport2=$properties->_getCity($aairport);

								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['DepartureAirport']['LocationCode']=$dairport;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['ArrivalAirport']['LocationCode']=$aairport;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['FlightNumber']=$flight;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['JourneyDuration']=$time;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['DepartureDateTime']=$ddatetime;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['ArrivalDateTime']=$adatetime;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['StopQuantity']=$stops;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['MarketingAirline']['CompanyShortName']=$airline;
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['Meal']['MealCode']=$meal;




								$available_classes = array();
								foreach ( $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['BookingClassAvail'] as $bca) {
									if (($bca['ResBookDesigQuantity'][0] >= '1') && ($bca['ResBookDesigQuantity'][0] <= '9')) {
										$available_classes[] = $bca['ResBookDesigCode'][0];

									}
								}
								if ($available_classes == array()) {
									$option = false;
									break;
								}
								$dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$z]['ResBookDesigCode']=$available_classes[0];
								$class_list = "Array('" . implode("', '", $available_classes) . "')";

								$option_params .= "{MarketingAirline:'$airline', FlightNumber:$flight, DepartureDateTime:'$ddatetime', ArrivalDateTime:'$adatetime', ";
								$option_params .= "PassengerTypeQuantityAdulto:'$adulto', PassengerTypeQuantityNino:$nino, PassengerTypeQuantity3edad:'$mayor', PassengerTypeQuantitybebe:'$bebe', JourneyDuration:'$time', ";
								$option_params .= "DepartureAirport:'$dairport', ArrivalAirport:'$aairport', Destino:'1', Type:'$type', ResBookDesigCode:'$available_classes[0]', Classes:$class_list},";
								


								$html.='<div class="row" style="margin-top:10px;">	
								               <div class="col-sm-12 col-md-3 col-lg-3">';
								$html.='		<img src="../../webmaster/img/btn-ida.png" widht="60px" height="20px" />';

								$html.='        </div>';

								//echo $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureDateTime'];

								$html.=' 
								            <div class="col-sm-12 col-md-9 col-lg-9">
								               		<span>'.$properties->_getDate(substr($ddatetime,0,10)).'</span>
								               		<span> ,de </span><span>'.$dairport2.' ('.$dairport.')'.'</span>
								               		<span> a </span><span>'.$aairport2.' ('.$aairport.')'.'</span>
								             </div>';
								             
								   	$html.='  </div>'; // Cierre de 1 Fila
									$html.=' <div class="row " style="margin-top:10px;">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               		<span class="text_campos">Sale:</span><span  class="text_campos2" >'.substr($ddatetime,11,8).'</span>
								               		<span class="text_campos">LLega:</span><span  class="text_campos2"> '.substr($adatetime ,11,8).'</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               		<span  class="text_campos2">'.$time.'</span><span  class="text_campos3"> '.$msg_stops.' </span>
								               		<span ><img src="../../webmaster/img/btn-arw-r.png" widht="20px" height="20px" /></span>
								               		<span  class="text_campos2"> '.$airline.' </span>
								               </div>
											</div>';// Cierre de 2 Fila
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-12 col-lg-12">
								               	 <hr class="hr_precio" />
								               </div>';
									$html.=' </div>';// Cierre de 3 Fila
							}
							//echo "<br/>";

							if($x<=$count2){
							for($y=1; $y<=count($OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment']);$y++){
							//	echo "Segmentos ".count($OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'])."<br/>"; 
							//	echo $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['DepartureDateTime'];

								$dairport = $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['DepartureAirport']['LocationCode'][0];
								$aairport = $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['ArrivalAirport']['LocationCode'][0];
								$flight = $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['FlightNumber'][0];
								$time =  $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['JourneyDuration'][0];
								$ddatetime = $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['DepartureDateTime'][0];
								$adatetime = $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['ArrivalDateTime'][0];
								$stops = $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['StopQuantity'][0];
								$airline = $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['MarketingAirline']['CompanyShortName'][0];
								$meal = $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['Meal']['MealCode'][0];						
								$adulto= $OriginDestinationInformation[1]['PassengerTypeQuantityAdulto'];
								$bebe= $OriginDestinationInformation[1]['PassengerTypeQuantityBebe'];
								$nino= $OriginDestinationInformation[1]['PassengerTypeQuantityNino'];
								$mayor=$OriginDestinationInformation[1]['PassengerTypeQuantityMayor'];
								$type=$OriginDestinationInformation[1]['TypeDest'];

								$airline2=$properties->_getAirline($airline);
								$dairport2=$properties->_getCity($dairport);
								$aairport2=$properties->_getCity($aairport);

								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['DepartureAirport']['LocationCode']=$dairport;
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['ArrivalAirport']['LocationCode']=$aairport;
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['FlightNumber']=$flight;
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['JourneyDuration']=$time;
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['DepartureDateTime']=$ddatetime;
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['ArrivalDateTime']=$adatetime;
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['StopQuantity']=$stops;
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['MarketingAirline']['CompanyShortName']=$airline;
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['Meal']['MealCode']=$meal;
								$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityAdulto']=$adulto;
								$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityBebe']=$bebe;
								$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityNino']=$nino;
								$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityMayor']=$mayor;
								$dataPrice['OriginDestinationOption'][1]['TypeDest']=$type;


								$available_classes = array();
								foreach ( $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['BookingClassAvail'] as $bca) {
									if (($bca['ResBookDesigQuantity'][0] >= '1') && ($bca['ResBookDesigQuantity'][0] <= '9')) {
										$available_classes[] = $bca['ResBookDesigCode'][0];

									}
								}
								if ($available_classes == array()) {
									$option = false;
									break;
								}
								$dataPrice['OriginDestinationOption'][$j]['FlightSegment'][$y]['ResBookDesigCode']=$available_classes[0];

								$class_list = "Array('" . implode("', '", $available_classes) . "')";

								$option_params .= "{MarketingAirline:'$airline', FlightNumber:$flight, DepartureDateTime:'$ddatetime', ArrivalDateTime:'$adatetime', ";
								$option_params .= "PassengerTypeQuantityAdulto:'$adulto', PassengerTypeQuantityNino:$nino, PassengerTypeQuantity3edad:'$mayor', PassengerTypeQuantitybebe:'$bebe', JourneyDuration:'$time', ";
								$option_params .= "DepartureAirport:'$dairport', ArrivalAirport:'$aairport', Destino:'2', Type:'$type', ResBookDesigCode:'$available_classes[0]', Classes:$class_list},";
								
								$html.='<div class="row" style="margin-top:10px;">	
								               <div class="col-sm-12 col-md-3 col-lg-3">';
								$html.='		<img src="../../webmaster/img/btn-regreso.png" widht="60px" height="20px" />';

								$html.='        </div>';

								$html.=' 
								            <div class="col-sm-12 col-md-9 col-lg-9">
								               		<span>'.$properties->_getDate(substr($ddatetime,0,10)).'</span>
								               		<span> ,de </span><span>'.$dairport2.' ('.$dairport.')'.'</span>
								               		<span> a </span><span>'.$aairport2.' ('.$aairport.')'.'</span>
								             </div>';
								             
								   	$html.='  </div>'; // Cierre de 1 Fila
									$html.=' <div class="row " style="margin-top:10px;">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               		<span class="text_campos">Sale:</span><span  class="text_campos2" >'.substr($ddatetime,11,8) .'</span>
								               		<span class="text_campos">LLega:</span><span  class="text_campos2"> '.substr($adatetime ,11,8).'</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               		<span  class="text_campos2">'.$time.'</span><span  class="text_campos3"> '.$msg_stops.' </span>
								               		<span ><img src="../../webmaster/img/btn-arw-r.png" widht="20px" height="20px" /></span>
								               		<span  class="text_campos2"> '.$airline2.' </span>
								               </div>
											</div>';// Cierre de 2 Fila
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-12 col-lg-12">
								               	 <hr class="hr_precio" />
								               </div>';
									$html.=' </div>';// Cierre de 3 Fila
								
							$html.='<br/>';
							
							} //Fin del segundo for
						}

								
						 
							//echo "<br/>";
							$html.='</div>';
							$html.='</div>';
							$html.='</div>';
							 
							//MANEJO DE PRECIOS COMBINADOS----------------------------------------------------
									 		//Instancia con el xmlController
											$xmlController= new XmlController();
											$request=$xmlController->AirPriceRS($dataPrice);//llamado al metodo con el objeto @param priceModel2 para armar XML para obtener los precios por cada intinerario encontrado
											//echo "<pre>".htmlentities(print_r($dataPrice, true)) ."</pre>";
											$response= $http->post($request); // Procesando XML armado 
										 	//echo "<pre>".htmlentities(print_r($request, true)) ."</pre>";
											$xml = simplexml_load_string($response);//Tranformacion del xml en string
										 	//echo "<pre>".htmlentities(print_r($response, true)) ."</pre>";
											
										

											$price1=$xmlController->processAirPriceRQ($xml,$request,$response); //Procesando los datos resultantes del precio

											if($price1!=null){

												$TotalFare=$price1->getTotalFare();
												$BaseFare=$price1->getBaseFare();
												$ImpTasas=$price1->getImpTasas();
												$Cargos=$price1->getCargos();
												$BaseFareAdulto=$price1->getBaseFareAdulto();
												$BaseFareBebe=$price1->getBaseFareBebe();
												$BaseFareNino=$price1->getBaseFareNino();
												$Error=$price1->getError();
											}

					 		//------------------------------------------------------------------------------------
							$option_params = substr($option_params,0,-1) . ")";
							$option_params .= ",'Isseu':Array({TotalFare:'$TotalFare',BaseFare:'$BaseFare',ImpTasas:'$ImpTasas',Cargos:'$Cargos',BaseFareAdulto:'$BaseFareAdulto',BaseFareBebe:'$BaseFareBebe',BaseFareNino:'$BaseFareNino'})";
							$option_params .="}";
							
							//echo  $option_params. "<br/>";
							$html.='<!-- Muestra los precios-->
									<div class="precio_disponibilidad col-sm-12 col-md-3 col-lg-3">';
								
							if($TotalFare!=0){

								$html.='
											<div class="row">	
								               <div class="col-sm-12 col-md-12 col-lg-12">
								               	<span class="text_titulo_precio">Total</span>
								               </div>
											</div>
											<div class="row">	
								               <div class="text_valor_precio col-sm-12 col-md-12 col-lg-12">
								               	<span class="text_valor_precio" >Bsf. '.number_format($TotalFare, 2, ',', '.').'</span>
								               </div>
											</div>
											<div class="row">	
								               <div class="col-sm-12 col-md-12 col-lg-12">
								               	<hr class="hr_precio" />
								               </div>
											</div>';
								if($adulto>=1){	
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">'.$adulto.' Adulto:</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($BaseFareAdulto,2, ',', '.').'</span>
								               </div>
											</div>';									 
								}
								if($nino>=1){	
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">'.$nino.' Niño:</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($BaseFareNino,2, ',', '.').'</span>
								               </div>
											</div>';									 
								}
								if($bebe>=1){	
									$html.='<div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">'.$bebe.' Infante:</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($BaseFareBebe,2, ',', '.').'</span>
								               </div>
											</div>';									 
								}
								  
								   
								 $html.=' <div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Imp+Tasas:</span>
								               </div>
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($ImpTasas,2, ',', '.').'</span>
								               </div>
											</div>
											<div class="row">	
								               <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Cargos:</span>
								               </div>
								                <div class="col-sm-12 col-md-6 col-lg-6">
								               	<span class="text_datos_precio">Bsf. '.number_format($Cargos,2, ',', '.').'</span>
								               </div>
											</div>

											<div class="row">	
								               <div class="col-sm-12 col-md-8 col-lg-12">
								               	<input  class="btn btn-primary" style="margin-top:10px;" name="submit" type="button" value="Seleccionar" onclick="reservation('.$option_params.')"/>
								               </div>
											</div>';


							}else{
								 $html.='	<span class="text_titulo_precio">NO DISPONIBLE: '.$Error.'</span>';
							}

							$html.='</div>';

							$html.='</div><!-- Fin Opcion General-->';	
							
						$cont++;	

						}
					
					$v++;

					}// end While
					

					 
					 
				}// end for Opcion 2

				
			} // end For Opcion 1
	}
		$html.='</div><!-- Fin Panel Disponibilidad-->
				</div><!-- Fin Row-->';
		echo $html;
	}


$http->close();//Cerrar conexion al servidor KIU 
?>
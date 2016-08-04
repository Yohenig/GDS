<?php

 
include_once(RAIZ.'/Model/Price.php');
include_once(RAIZ.'/Config/propertiesView.php');
include_once(RAIZ.'/Model/Avail.php');

class XmlController{



	/**
	* Procesa los datos y construye el XML para consumer el 
    * servicio de disponibilidad, retornando el XML de la disponibilidad
	*@param $availModel Instancia del Modelo Avail que contiene todos los datos necesarios para armar el XML de Disponibilidad.
	*@return string XML que contiene el mensaje OTA de disponibilidad
	*
	*/
	public function AirAvailRS($availModel){
		$request = '<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<KIU_AirAvailRQ EchoToken=\"'.$availModel->getEchoToken().'\" TimeStamp=\"'.$availModel->getTimeStamp().'\" Target=\"'.$availModel->getParticion().'\" Version=\"3.0\" SequenceNmbr=\"'.$availModel->getSequenceNmbr().'\" PrimaryLangID=\"en-us\" DirectFlightsOnly=\"'.$availModel->getDirect().'\" MaxResponses=\"'.$availModel->getMaxResponses().'\" CombinedItineraries=\"'.$availModel->getCombined().'\">
					<POS>
						<Source AgentSine=\"'.$availModel->getSine().'\" TerminalID=\"'.$availModel->getDevice().'\">
						</Source>
					</POS>
					<SpecificFlightInfo>
						<Airline Code=\"'.$availModel->getCarrier().'\"/>
					</SpecificFlightInfo>';
		if($availModel->getTypeDest()=="1"){
			$request .= '<OriginDestinationInformation>
							<DepartureDateTime>'.$availModel->getDate().'</DepartureDateTime>
							<OriginLocation LocationCode=\"'.$availModel->getSource().'\"/>
							<DestinationLocation LocationCode=\"'.$availModel->getDest().'\"/>
						</OriginDestinationInformation>';
		}
		if($availModel->getTypeDest()=="2"){
			$request .= '<OriginDestinationInformation>
							<DepartureDateTime>'.$availModel->getDate().'</DepartureDateTime>
							<OriginLocation LocationCode=\"'.$availModel->getSource().'\"/>
							<DestinationLocation LocationCode=\"'.$availModel->getDest().'\"/>
						</OriginDestinationInformation>';
			$request .= '<OriginDestinationInformation>
							<DepartureDateTime>'.$availModel->getDateDest().'</DepartureDateTime>
							<OriginLocation LocationCode=\"'.$availModel->getDest().'\"/>
							<DestinationLocation LocationCode=\"'.$availModel->getSource().'\"/>
						</OriginDestinationInformation>';
		}
		$request .= '<TravelPreferences>
						<CabinPref Cabin=\"'.$availModel->getCabin().'\"/>
					</TravelPreferences>
					<TravelerInfoSummary>
						<AirTravelerAvail>';
		if(($availModel->getPassengerTypeQuantityAdulto())>0){
		$request .=			'<PassengerTypeQuantity Code=\"ADT\" Quantity=\"'.$availModel->getPassengerTypeQuantityAdulto().'\"/>';
		}
		if(($availModel->getPassengerTypeQuantitybebe())>0){
		$request .=			'<PassengerTypeQuantity Code=\"INF\" Quantity=\"'.$availModel->getPassengerTypeQuantitybebe().'\"/>';
		}
		if(($availModel->getPassengerTypeQuantityNino())>0){
		$request .=			'<PassengerTypeQuantity Code=\"CNN\" Quantity=\"'.$availModel->getPassengerTypeQuantityNino().'\"/>';
		}
		$request .=			'</AirTravelerAvail>
					</TravelerInfoSummary>
				</KIU_AirAvailRQ>';

		return $request;
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------
	function processAirAvailRQ2($xml,$availModel,$http){

		if($xml!=null){
				if ($xml->Error->ErrorCode) {
					echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
				} else {

				$i=1;
				$OriginDestinationInformation=array();
				foreach ($xml->OriginDestinationInformation as $odi) {
					
				    $OriginDestinationInformation[$i]= array('DepartureDateTime'=>$odi->DepartureDateTime,'OriginLocation'=>$odi->OriginLocation,'DestinationLocation'=>$odi->DestinationLocation);
					$j=1;
					$OriginDestinationOptions= null;
					 
					foreach ($odi->OriginDestinationOptions->OriginDestinationOption as $odo) {
						
						$x=1;
						
						
						foreach ($odo->FlightSegment as $fs) {
							
							$FlightSegment= array();

							$FlightSegment= array('DepartureDateTime'=>$fs['DepartureDateTime'],'ArrivalDateTime'=>$fs['ArrivalDateTime'],'StopQuantity'=>$fs['StopQuantity'], 'FlightNumber'=>$fs['FlightNumber'],'JourneyDuration'=>$fs['JourneyDuration']);

							$DepartureAirport=array('LocationCode' => $fs->DepartureAirport['LocationCode'] );
							$FlightSegment['DepartureAirport']=$DepartureAirport;

							$ArrivalAirport=array('LocationCode' => $fs->ArrivalAirport['LocationCode'] );
							$FlightSegment['ArrivalAirport']=$ArrivalAirport;

							$Equipment=array('AirEquipType' => $fs->Equipment['AirEquipType'] );
							$FlightSegment['Equipment']=$Equipment;

							$MarketingAirline=array('CompanyShortName' => $fs->MarketingAirline['CompanyShortName'] );
							$FlightSegment['MarketingAirline']=$MarketingAirline;

							$Meal=array('MealCode' =>  $fs->Meal['MealCode'] );
							$FlightSegment['Meal']=$Meal;

							$MarketingCabin=array('CabinType'=>$fs->MarketingCabin['CabinType'], 'RPH'=>$fs->MarketingCabin['RPH']);
							$FlightSegment['MarketingCabin']=$MarketingCabin;

							$bo=1;
							foreach ($fs->BookingClassAvail as $bca) {

								if (($bca['ResBookDesigQuantity'] >= '1') && ($bca['ResBookDesigQuantity'] <= '9')) {
									$BookingClassAvail=array('ResBookDesigCode'=>$bca['ResBookDesigCode'],'ResBookDesigQuantity'=>$bca['ResBookDesigQuantity'],'RPH'=>$bca['RPH']);
									$FlightSegment['BookingClassAvail'][$bo]=$BookingClassAvail;
									$bo++;
								}
							
							}

							// Armar objeto con el intinerario-----------------------------------------------------------------------------------
											/*$priceModel2=new AvailModel();
											$priceModel2->setDepartureAirport($fs->DepartureAirport['LocationCode']);
											$priceModel2->setArrivalAirport($fs->ArrivalAirport['LocationCode']);
											$priceModel2->setFlightNumber($fs['FlightNumber']);
											$priceModel2->setResBookDesigCode("Y");
											$priceModel2->setDepartureDateTime($fs['DepartureDateTime']);
											$priceModel2->setArrivalDateTime($fs['ArrivalDateTime']);
											$priceModel2->setMarketingAirline($fs->MarketingAirline['CompanyShortName']);
											
											$priceModel2->setPassengerTypeQuantityAdulto($availModel->getPassengerTypeQuantityAdulto());
											$priceModel2->setPassengerTypeQuantityNino($availModel->getPassengerTypeQuantityNino());
											$priceModel2->setPassengerTypeQuantity3edad($availModel->getPassengerTypeQuantity3edad());
											$priceModel2->setPassengerTypeQuantitybebe($availModel->getPassengerTypeQuantitybebe());

											$request=$this->AirPriceRS($priceModel2);//llamado al metodo con el objeto @param priceModel2 para armar XML para obtener los precios por cada intineraio encontrado
											$http->init();//Iniciar conexion con el servidor KIU
											$response= $http->post($request); // Procesando XML armado 
										//	echo "<pre>".htmlentities(print_r($request, true)) ."</pre>";
											$xml = simplexml_load_string($response);//Tranformacion del xml en string
										//	echo "<pre>".htmlentities(print_r($response, true)) ."</pre>";
											$http->close();//Cerrar conexion al servidor KIU 
										

											$price1=$this->processAirPriceRQ($xml,$request,$response); //Procesando los datos resultantes del precio

											if($price1!=null){

												$FlightSegment['TotalFare']=$price1->getTotalFare();
												$FlightSegment['BaseFare']=$price1->getBaseFare();
												$FlightSegment['ImpTasas']=$price1->getImpTasas();
												$FlightSegment['Cargos']=$price1->getCargos();
												$FlightSegment['BaseFareAdulto']=$price1->getBaseFareAdulto();
												$FlightSegment['BaseFareBebe']=$price1->getBaseFareBebe();
												$FlightSegment['BaseFareNino']=$price1->getBaseFareNino();
												$FlightSegment['Error']=$price1->getError();


											}*/

							//------------------------------------------------------------------------------------------------------------------------


							$OriginDestinationOptions['OriginDestinationOption'][$j]['FlightSegment'][$x]=$FlightSegment;
							
							 
						$x++;


						}//end FlightSegment 
						//echo "j: ".$j."<br/>";
						//echo "i: ".$i."<br/>";
						//$OriginDestinationOptions['OriginDestinationOption'][$j]=$OriginDestinationOption;
						 
						$OriginDestinationInformation[$i]['OriginDestinationOptions']=$OriginDestinationOptions;
						$j++;
						
					}// end OriginDestinationOption
					$i++;
				}//End OriginDestinationInformation
				return $OriginDestinationInformation;
				}
	}else{
				echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.MSG_ERROR.'</label></diV></diV>';
		}
	}

	//---------------------------------------------------------------------------------------------------------------------------------
	/*
|		Presentacion de Datos de la disponibilidad
	*/

	function processView($OriginDestinationInformation){
		header('Content-type: application/json');
		$json=json_encode($OriginDestinationInformation);

		$file = 'OriginDestinationInformation.json';
		if (file_exists($file)) {
			unlink($file);
			file_put_contents($file, $json);
		}else{
		$file2 = fopen('OriginDestinationInformation.json',"r+");
		file_put_contents($file2, $json);
		}
		header('Location:../View/Avail/availRS.php');
    }


	//--------------------------------------------------------------------

	/**
	* Metodo que permite procesar los datos de disponibilidad de KIU
	* @param $availModel Instancia del Modelo Avail que contiene todos los datos sobre la consulta de disponibilidad
	* @param $xml String del Xml con la respuesta de la disponibilidad solicitada
	* @param $http instancia de conexion CURL establecida
	* @param $request Xml construido para la solicitud de la disponibilidad
	* Campos utlizados:
	*  $html           => String que forma la vista de la disponbilidad.
	*  $typeD          => Tipo de Viaje (1) Ida (2) Uda y Vuelta. 
	*/

	function processAirAvailRQ($availModel,$xml,$request,$http){
			$html="";
			$typeD="";
			//Validación del xml de respuesta
			if($xml!=null){
				if ($xml->Error->ErrorCode) {
					echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
				} else {

					$html='	<div class="row container">';
					$html.='    <div class="filtro_resultados col-sm-12 col-md-2 col-lg-2">
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
											Selecciona tu vuelo desde <span class="text_destinos">'.$availModel->getSource().'</span> hasta <span class="text_destinos">'.$availModel->getDest().'</span>
										</div>		
									</div>';

					$typeD="";
					foreach ($xml->OriginDestinationInformation as $odi) {
						if($odi->OriginLocation==$availModel->getSource()){
							$typeD="1";
						}else{
							$typeD="2";
						}
					

						//echo "<h3>" . $odi->DepartureDateTime . ": " . $odi->OriginLocation . "->" . $odi->DestinationLocation . "</h3>";
						$option_number = 1;
						foreach ($odi->OriginDestinationOptions->OriginDestinationOption as $odo) {
							$priceModel= new AvailModel();
							$datos=array();
							$option = true;
							//$fn = 1;
							$c= 0;
							 $html.='<!-- Opcion General-->
								<div class="row opcion_disponibilidad"><div class="col-sm-12 col-md-9 col-lg-9">';
							$option_params = "{'SegmentsCount':" . count($odo->FlightSegment) . ", 'Segments':Array(";
							//$option_string = "<hr/><h4>Option $option_number</h4>";
							//$option_string .= '<table><tr><td>Source</td><td>Dest</td><td>Carrier</td><td>Flight</td><td>Departure</td><td>Arrival</td><td>Duration</td><td>Stops</td><td>Equi</td><td>Meal</td></tr>';
							$flightCount=count($odo->FlightSegment);
							foreach ($odo->FlightSegment as $fs) {


								$dairport = $fs->DepartureAirport['LocationCode'];
								$aairport = $fs->ArrivalAirport['LocationCode'];
								$flight = $fs['FlightNumber'];
								$time =  $fs['JourneyDuration'];
								$ddatetime = $fs['DepartureDateTime'];
								$adatetime = $fs['ArrivalDateTime'];
								$stops = $fs['StopQuantity'];
								$equipment = $fs->Equipment['AirEquipType'];
								$airline = $fs->MarketingAirline['CompanyShortName'];
								$meal = $fs->Meal['MealCode'];						
								$adulto= $availModel->getPassengerTypeQuantityAdulto();
								$bebe= $availModel->getPassengerTypeQuantitybebe();
								$nino= $availModel->getPassengerTypeQuantityNino();
								$mayor=$availModel->getPassengerTypeQuantity3edad();
								
								
								$available_classes = array();
								foreach ($fs->BookingClassAvail as $bca) {
									if (($bca['ResBookDesigQuantity'] >= '1') && ($bca['ResBookDesigQuantity'] <= '9')) {
										$available_classes[] = $bca['ResBookDesigCode'];

									}
								}
								if ($available_classes == array()) {
									$option = false;
									break;
								}
								
								$class_list = "Array('" . implode("', '", $available_classes) . "')";
								$datos[$c]= array('MarketingAirline'=>$airline, 'FlightNumber'=>$flight, 'DepartureDateTime'=>$ddatetime, 'ArrivalDateTime'=>$adatetime, 'DepartureAirport'=>$dairport, 'ArrivalAirport'=>$aairport, 'ResBookDesigCode'=>$available_classes[0],
									'PassengerTypeQuantityAdulto'=>$adulto, 'PassengerTypeQuantityNino'=>$nino,'PassengerTypeQuantity3edad'=>$mayor,'PassengerTypeQuantitybebe'=>$bebe);
								$option_params .= "{MarketingAirline:'$airline', FlightNumber:$flight, DepartureDateTime:'$ddatetime', ArrivalDateTime:'$adatetime', ";
								$option_params .= "PassengerTypeQuantityAdulto:'$adulto', PassengerTypeQuantityNino:$nino, PassengerTypeQuantity3edad:'$mayor', PassengerTypeQuantitybebe:'$bebe', JourneyDuration:'$time', ";
								$option_params .= "DepartureAirport:'$dairport', ArrivalAirport:'$aairport', ResBookDesigCode:'$available_classes[0]', Classes:$class_list},";
									
								$c++;

								

								//Prueba de Interfaz------------------------------------
								$msg_stops="";
								if($flightCount==1){
									$msg_stops="Directo";
								}
								if($flightCount>1){
									$msg_stops= ($flightCount-1)." Escala(s)";

								}
								$stops=$flightCount-1;

									
								 $html.='<div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="row" style="margin-top:10px;">	
							               <div class="col-sm-12 col-md-3 col-lg-3">';
							    if($typeD==1){
							    	 $html.='		<img src="../../webmaster/img/btn-ida.png" widht="60px" height="20px" />';
							    }
							    if($typeD==2){
							    	 $html.='		<img src="../../webmaster/img/btn-regreso.png" widht="60px" height="20px" />';
							    }
							   
							    $html.='    </div>
							               <div class="col-sm-12 col-md-9 col-lg-9">
							               		<span>'.substr($ddatetime,2,9).'</span><span> ,de </span><span>'.$dairport.'</span><span> a </span><span>'.$aairport.'</span>
							               </div>
										</div>
										<div class="row " style="margin-top:10px;">	
							               <div class="col-sm-12 col-md-6 col-lg-6">
							               		<span class="text_campos">Sale:</span><span  class="text_campos2" >'.substr($ddatetime,11,8).'</span><span class="text_campos">LLega:</span><span  class="text_campos2"> '.substr($adatetime,11,8).'</span>
							               </div>
							               <div class="col-sm-12 col-md-6 col-lg-6">
							               		<span  class="text_campos2">'.$time.'</span><span  class="text_campos3"> '.$msg_stops.' </span><span ><img src="../../webmaster/img/btn-arw-r.png" widht="20px" height="20px" /></span><span  class="text_campos2"> '.$airline.' </span>
							               </div>
										</div>
										<div class="row">	
							               <div class="col-sm-12 col-md-12 col-lg-12">
							               	 <hr class="hr_precio" />
							               </div>
							                
										</div>';
	 
								$html.='</div></div>';
								//-------------------------------------------------------

							/*	$priceModel->setDepartureAirport($dairport);
								$priceModel->setArrivalAirport($aairport);
								$priceModel->setFlightNumber($flight);
								$priceModel->setResBookDesigCode($available_classes[0]);
								$priceModel->setDepartureDateTime($ddatetime);
								$priceModel->setArrivalDateTime($adatetime);
								$priceModel->setMarketingAirline($airline);*/
					
							}
							 $html.='</div>';
							 $option_params = substr($option_params,0,-1) . ")}";
							 //echo $option_params;
							//if ($option) echo "$option_string<tr><td colspan=\"10\"><input type=\"button\" value=\"Add\" onclick=\"add_segment($option_params)\"/></td></tr>";
						//	echo '</table>';
							if ($option) $option_number++;
							 
							 
							//Llamada al metodo AirPriceRQ para armar el xml-------------------------------------------------------------
									$TotalFare=0;$BaseFare=0;$ImpTasas=0;$Cargos=0; //Variables para totalizar el costo de todos los pasajero
									$priceModel2=null;

								for ($i=0;$i<sizeof($datos);$i++) { // Recorrer Array armado con los intinerarios
									
									// Armar objeto con el intinerario----------------------------------
									$priceModel2=new AvailModel();
									$priceModel2->setDepartureAirport($datos[$i]["DepartureAirport"]);
									$priceModel2->setArrivalAirport($datos[$i]["ArrivalAirport"]);
									$priceModel2->setFlightNumber($datos[$i]["FlightNumber"]);
									$priceModel2->setResBookDesigCode($datos[$i]["ResBookDesigCode"]);
									$priceModel2->setDepartureDateTime($datos[$i]["DepartureDateTime"]);
									$priceModel2->setArrivalDateTime($datos[$i]["ArrivalDateTime"]);
									$priceModel2->setMarketingAirline($datos[$i]["MarketingAirline"]);
									//-------------------------------------------------------------------
								
									$request=$this->AirPriceRS($priceModel2,$datos);//llamado al metodo con el objeto @param priceModel2 para armar XML para obtener los precios por cada intineraio encontrado
									$http->init();//Iniciar conexion con el servidor KIU
									$response= $http->post($request); // Procesando XML armado 
									$xml = simplexml_load_string($response);//Tranformacion del xml en string
									$http->close();//Cerrar conexion al servidor KIU 
								

									$price=$this->processAirPriceRQ($xml,$request,$response); //Procesando los datos resultantes del precio

									if($price!=null){
										$TotalFare=$TotalFare+$price->getTotalFare();
										$BaseFare=$BaseFare+$price->getBaseFare();
										$ImpTasas=$ImpTasas+$price->getImpTasas();
										$Cargos=$Cargos+$price->getCargos();
									}
								}
							//-------------------------------------------------------------------------------------------------------------------
								$priceTotal= new PriceModel();
								$priceTotal->setTotalFare($TotalFare);
								$priceTotal->setBaseFare($BaseFare);
								$priceTotal->setImpTasas($ImpTasas);
								$priceTotal->setCargos($Cargos);
								$arrayPrice= array();
								$arrayPrice[0]= array('TotalFare'=>$TotalFare, 'BaseFare'=>$BaseFare, 'ImpTasas'=>$ImpTasas,'Cargos'=>$Cargos);
								 

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
							               	<span class="text_valor_precio" >Bsf. '.$TotalFare.'</span>
							               </div>
										</div>
										<div class="row">	
							               <div class="col-sm-12 col-md-12 col-lg-12">
							               	<hr class="hr_precio" />
							               </div>
										</div>
										<div class="row">	
							               <div class="col-sm-12 col-md-6 col-lg-6">';
							 $msg="";

							 if($adulto>=1)	$msg=$msg." ".$adulto." Adulto ";
							 if($nino>=1)	$msg=$msg." ".$nino." Niño ";
							 if($mayor>=1)	$msg=$msg." ".$mayor." 3 edad ";
							 if($bebe>=1)	$msg=$msg." ".$bebe." Bebe ";

							$html.='
							               	<span class="text_datos_precio">'.$msg.':</span>';

							 $html.='   </div>
							               <div class="col-sm-12 col-md-6 col-lg-6">
							               	<span class="text_datos_precio">Bsf. '.$BaseFare.'</span>
							               </div>
										</div>
										<div class="row">	
							               <div class="col-sm-12 col-md-6 col-lg-6">
							               	<span class="text_datos_precio">Imp+Tasas:</span>
							               </div>
							               <div class="col-sm-12 col-md-6 col-lg-6">
							               	<span class="text_datos_precio">Bsf. '.$ImpTasas.'</span>
							               </div>
										</div>
										<div class="row">	
							               <div class="col-sm-12 col-md-6 col-lg-6">
							               	<span class="text_datos_precio">Cargos:</span>
							               </div>
							                <div class="col-sm-12 col-md-6 col-lg-6">
							               	<span class="text_datos_precio">Bsf. '.$Cargos.'</span>
							               </div>
										</div>

										<div class="row">	
							               <div class="col-sm-12 col-md-8 col-lg-12">
							               	<input  class="btn btn-primary" style="margin-top:10px;" name="submit" type="button" value="Seleccionar" onclick="reservation('.$option_params.');"/>
							               </div>
										</div>
									';


						}


						$html.='</div>';

						$html.='</div><!-- Fin Opcion General-->';

						}



					}
				}
				$html.='</div><!-- Fin Panel Disponibilidad-->
						</div><!-- Fin Row-->';
				echo $html;

			}else{
				echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.MSG_ERROR.'</label></diV></diV>';
			}
			//echo '<br/><input type="button" value="Back" onclick="avail_new_search()">';

			//echo "<pre>REQUEST: " . htmlentities(print_r($request, true)) . "<br>" . "RESPONSE: " . htmlentities(print_r($response, true)) . "</pre>";
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------

	/**
	* Metodo que permite armar el XML de para consulta de precios de KIU--------------------------------------------------------------------------
	*/
	public function AirPriceRS($dataPrice){
		$priceModel= new AvailModel();
		$request = '<?xml version=\"1.0\" encoding=\"UTF-8\"?>
					<KIU_AirPriceRQ EchoToken=\"'.$priceModel->getEchoToken().'\" TimeStamp=\"'.$priceModel->getTimeStamp().'\" Target=\"'.$priceModel->getParticion().'\" Version=\"3.0\" SequenceNmbr=\"'.$priceModel->getSequenceNmbr().'\" PrimaryLangID=\"en-us\">
						<POS>
							<Source AgentSine=\"'.$priceModel->getSine().'\" PseudoCityCode=\"'.$priceModel->getCity().'\" ISOCountry=\"'.$priceModel->getCountry().'\" ISOCurrency=\"'.$priceModel->getCurrency().'\" TerminalID=\"'.$priceModel->getDevice().'\">
								<RequestorID Type=\"5\"/>
								<BookingChannel Type=\"1\"/>
							</Source>
						</POS>
						<AirItinerary>
							<OriginDestinationOptions>';
							 
					for ($i=1;$i<=sizeof($dataPrice['OriginDestinationOption']);$i++) {
						$request .= '<OriginDestinationOption>\n';

						for ($j=1; $j <= sizeof($dataPrice['OriginDestinationOption'][$i]['FlightSegment']); $j++) { 
							$ddatetime = $dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$j]['DepartureDateTime'];
							$adatetime = $dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$j]['ArrivalDateTime'];
							$flight = $dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$j]['FlightNumber'];
						$class = $dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$j]['ResBookDesigCode'];
						$source = $dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$j]['DepartureAirport']['LocationCode'];
						$dest = $dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$j]['ArrivalAirport']['LocationCode'];
						$airline = $dataPrice['OriginDestinationOption'][$i]['FlightSegment'][$j]['MarketingAirline']['CompanyShortName'];
						$request .= "\t\t\t\t<FlightSegment DepartureDateTime=\"$ddatetime\" ArrivalDateTime=\"$adatetime\" FlightNumber=\"$flight\" ResBookDesigCode=\"$class\" >
							<DepartureAirport LocationCode=\"$source\"/>
							<ArrivalAirport LocationCode=\"$dest\"/>
							<MarketingAirline Code=\"$airline\"/>
						</FlightSegment>\n";
						}
						
						
						$request .= '\t\t\t</OriginDestinationOption>';
					}
					/*$request .= '\t\t\t\t<FlightSegment DepartureDateTime=\"'.$priceModel->getDepartureDateTime().'\" ArrivalDateTime=\"'.$priceModel->getArrivalDateTime().'\" FlightNumber=\"'.$priceModel->getFlightNumber().'\" ResBookDesigCode=\"'.$priceModel->getResBookDesigCode().'\" >
										<DepartureAirport LocationCode=\"'.$priceModel->getDepartureAirport().'\"/>
										<ArrivalAirport LocationCode=\"'.$priceModel->getArrivalAirport().'\"/>
										<MarketingAirline Code=\"'.$priceModel->getMarketingAirline().'\"/>
									</FlightSegment>\n';*/
					//$request .= '\t\t\t</OriginDestinationOption>';
				//	}
					$request .= '
							</OriginDestinationOptions>
						</AirItinerary>
						<TravelerInfoSummary>
							<AirTravelerAvail>';
					if(($dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityAdulto'])>=1){
					$request .= '<PassengerTypeQuantity Code=\"ADT\" Quantity=\"'.$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityAdulto'].'\"/>';
					}
					if(($dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityBebe']) >0){
					$request .= '<PassengerTypeQuantity Code=\"INF\" Quantity=\"'.$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityBebe'].'\"/>';
					}
					if(($dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityNino'])>0){
					$request .= '<PassengerTypeQuantity Code=\"CNN\" Quantity=\"'.$dataPrice['OriginDestinationOption'][1]['PassengerTypeQuantityNino'].'\"/>';
					}

					$request .= '</AirTravelerAvail>
						</TravelerInfoSummary>
					</KIU_AirPriceRQ>';

		return $request;
	}
	
	public function AirPriceRS2(){

		$request ='<KIU_AirPriceRQ EchoToken="1" TimeStamp="2016-05-29T20:40:29+00:00" Target="Testing" Version="3.0" SequenceNmbr="1" PrimaryLangID="en-us">
	<POS>
		<Source AgentSine="BLAJ06406" PseudoCityCode="BLA" ISOCountry="VE" ISOCurrency="VEF" TerminalID="BLAJ064003">
			<RequestorID Type="5"/>
			<BookingChannel Type="1"/>
		</Source>
	</POS>
	<AirItinerary>
		<OriginDestinationOptions>
			<OriginDestinationOption>
				<FlightSegment DepartureDateTime="2016-10-10 09:00:00" ArrivalDateTime="2016-10-10 10:00:00" FlightNumber="2950" ResBookDesigCode="Y" >
					<DepartureAirport LocationCode="CCS"/>
					<ArrivalAirport LocationCode="PTY"/>
					<MarketingAirline Code="QL"/>
				</FlightSegment>
			</OriginDestinationOption>
			<OriginDestinationOption>
					<FlightSegment DepartureDateTime="2016-10-13 22:05:00" ArrivalDateTime="2016-10-14 07:20:00" FlightNumber="454" ResBookDesigCode="Y" >
					<DepartureAirport LocationCode="PTY"/>
					<ArrivalAirport LocationCode="EZE"/>
					<MarketingAirline Code="XX"/>
				</FlightSegment>
				<FlightSegment DepartureDateTime="2016-10-14 10:00:00" ArrivalDateTime="2016-10-14 17:30:00" FlightNumber="10" ResBookDesigCode="Y" >
					<DepartureAirport LocationCode="EZE"/>
					<ArrivalAirport LocationCode="CCS"/>
					<MarketingAirline Code="XX"/>
				</FlightSegment>
			</OriginDestinationOption>
		</OriginDestinationOptions>
	</AirItinerary>
	<TravelerInfoSummary>
		<AirTravelerAvail>
			<PassengerTypeQuantity Code="ADT" Quantity="1"/>
		</AirTravelerAvail>
	</TravelerInfoSummary>
</KIU_AirPriceRQ>';


		return $request;
	}



	//--------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Metodo que permite procesar los datos de la consulta de precios de KIU----------------------------------------------------------------------
	*/

	function processAirPriceRQ($xml,$request,$response){

		$price=null;
		$avail= new AvailModel();

		if($xml!=null){

			if ($xml->Error) {
				//echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
				//echo $xml;
				$price= new PriceModel();
				$price->setBaseFare(0.0);
				$price->setTotalFare(0.0);
				$price->setImpTasas(0.0);
				$price->setCargos(0.0);
				$price->setError($xml->Error->ErrorCode+" "+$xml->Error->ErrorMsg);


			} else {
				$price= new PriceModel();

				$imp_tasas=0; 
				$BaseFare=0;
				foreach ($xml->PricedItineraries->PricedItinerary as $pi) {

					$price->setBaseFare(doubleval($pi->AirItineraryPricingInfo->ItinTotalFare->BaseFare['Amount']));

					foreach ($pi->AirItineraryPricingInfo->ItinTotalFare->Taxes->Tax as $t) {
						$imp_tasas=doubleval($imp_tasas)+doubleval($t['Amount']);

					}

					$price->setCargos(doubleval($avail->getCargo()));
					$price->setTotalFare(doubleval($pi->AirItineraryPricingInfo->ItinTotalFare->TotalFare['Amount']));
					$price->setImpTasas($imp_tasas);
					}

					foreach ($pi->AirItineraryPricingInfo->PTC_FareBreakdowns->PTC_FareBreakdown as $ptc) {
						 if($ptc->PassengerTypeQuantity['Code']=='ADT'){
						 	$price->setBaseFareAdulto(doubleval($ptc->PassengerFare->BaseFare['Amount']));
						 }
						 if($ptc->PassengerTypeQuantity['Code']=='INF'){
						 	$price->setBaseFareBebe(doubleval($ptc->PassengerFare->BaseFare['Amount']));
						 }
						 if($ptc->PassengerTypeQuantity['Code']=='CNN'){
						 	$price->setBaseFareNino(doubleval($ptc->PassengerFare->BaseFare['Amount']));
						 }
					 
					}

				$price->setError("OK");

			}
		}else{
				echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.MSG_ERROR.'</label></diV></diV>';
		}
		//echo "<pre>REQUEST: " . htmlentities(print_r($request, true)) . "<br>" . "RESPONSE: " . htmlentities(print_r($response, true)) . "</pre>";
       return $price;

	}
	//--------------------------------------------------------------------------------------------------------------------------------------------

	/**
	* Metodo que permite construir el XML de AirBookRQ KIU--------------------------------------------------------------------------
	*/

	function AirBookRQ($availModel,$passengerTypeAdulto,$passengerTypebebe,$passengerTypeNino){
			$request = '<?xml version=\"1.0\" encoding=\"UTF-8\"?>
					<KIU_AirBookRQ EchoToken=\"'.$availModel->getEchoToken().'\" TimeStamp=\"'.$availModel->getTimeStamp().'\" Target=\"'.$availModel->getParticion().'\" Version=\"3.0\" SequenceNmbr=\"'.$availModel->getSequenceNmbr().'\" PrimaryLangID=\"en-us\">
						<POS>
							<Source AgentSine=\"'.$availModel->getSine().'\" PseudoCityCode=\"'.$availModel->getCity().'\" ISOCountry=\"'.$availModel->getCountry().'\" ISOCurrency=\"'.$availModel->getCurrency().'\" TerminalID=\"'.$availModel->getDevice().'\">
								<RequestorID Type=\"5\"/>
								<BookingChannel Type=\"1\"/>
							</Source>
						</POS>';
			

			if($availModel->getTypeDest()==1){
			$request .='<AirItinerary>';
			$request .='<OriginDestinationOptions>';
			$request .='<OriginDestinationOption>';
					$rph=0;
					for ($i=0;$i<$_POST['Segments'];$i++) {
						$ddatetime = $_POST["DepartureDateTime_$i"];
						$adatetime = $_POST["ArrivalDateTime_$i"];
						$flight = $_POST["FlightNumber_$i"];
						$class = $_POST["ResBookDesigCode_$i"];
						$source = $_POST["DepartureAirport_$i"];;
						$dest = $_POST["ArrivalAirport_$i"];
						$airline = $_POST["MarketingAirline_$i"];
						$rph = sprintf("%02d", $i + 1);
						
						$request .= '<FlightSegment DepartureDateTime=\"'.$ddatetime.'\" ArrivalDateTime=\"'.$adatetime.'\" FlightNumber=\"'.$flight.'\" ResBookDesigCode=\"'.$class.'\" RPH=\"'.$rph.'\">
										<DepartureAirport LocationCode=\"'.$source.'\"/>
										<ArrivalAirport LocationCode=\"'.$dest.'\"/>
										<MarketingAirline Code=\"'.$airline.'\"/>
									</FlightSegment>';
					}
			$request .= '</OriginDestinationOption>';
			$request .='</OriginDestinationOptions>';
			$request .='</AirItinerary>';

			}
			if($availModel->getTypeDest()==2){
					$request .='<AirItinerary>';
					$request .='<OriginDestinationOptions>
								<OriginDestinationOption>';
					$rph=0;
					for ($i=0;$i<$_POST['Segments'];$i++) {
						if ($_POST["Destino_$i"]==1) {
						
						$ddatetime = $_POST["DepartureDateTime_$i"];
						$adatetime = $_POST["ArrivalDateTime_$i"];
						$flight = $_POST["FlightNumber_$i"];
						$class = $_POST["ResBookDesigCode_$i"];
						$source = $_POST["DepartureAirport_$i"];;
						$dest = $_POST["ArrivalAirport_$i"];
						$airline = $_POST["MarketingAirline_$i"];
						$rph = sprintf("%02d", $i + 1);
						
						$request .= '<FlightSegment DepartureDateTime=\"'.$ddatetime.'\" ArrivalDateTime=\"'.$adatetime.'\" FlightNumber=\"'.$flight.'\" ResBookDesigCode=\"'.$class.'\" RPH=\"'.$rph.'\">
										<DepartureAirport LocationCode=\"'.$source.'\"/>
										<ArrivalAirport LocationCode=\"'.$dest.'\"/>
										<MarketingAirline Code=\"'.$airline.'\"/>
									</FlightSegment>';
						}
						}
					$request .= '</OriginDestinationOption>';
					 
					 
					$request .='<OriginDestinationOption>';

					for ($i=0;$i<$_POST['Segments'];$i++) {
						if ($_POST["Destino_$i"]==2) {
						
						$ddatetime = $_POST["DepartureDateTime_$i"];
						$adatetime = $_POST["ArrivalDateTime_$i"];
						$flight = $_POST["FlightNumber_$i"];
						$class = $_POST["ResBookDesigCode_$i"];
						$source = $_POST["DepartureAirport_$i"];;
						$dest = $_POST["ArrivalAirport_$i"];
						$airline = $_POST["MarketingAirline_$i"];
						$rph = sprintf("%02d", $i + 1);
						
						$request .= '<FlightSegment DepartureDateTime=\"'.$ddatetime.'\" ArrivalDateTime=\"'.$adatetime.'\" FlightNumber=\"'.$flight.'\" ResBookDesigCode=\"'.$class.'\" RPH=\"'.$rph.'\">
										<DepartureAirport LocationCode=\"'.$source.'\"/>
										<ArrivalAirport LocationCode=\"'.$dest.'\"/>
										<MarketingAirline Code=\"'.$airline.'\"/>
									</FlightSegment>';
						}
						}
					$request .= '</OriginDestinationOption>
							</OriginDestinationOptions>';
					$request .='</AirItinerary>';

			}
			$request .= '<TravelerInfo>';
			if(count($passengerTypeAdulto)>0){
				for($i=0; $i<count($passengerTypeAdulto); $i++){
					$bookModel= $passengerTypeAdulto[$i];		
						$request .= '<AirTraveler PassengerTypeCode=\"ADT\">
									<PersonName>
										<GivenName>'.$bookModel->getFirstName().'</GivenName>
										<Surname>'.$bookModel->getLastName().'</Surname>
									</PersonName>
									<Telephone PhoneNumber=\"'.$bookModel->getTelephone().'\"/>
									<Email>'.$bookModel->getEmail().'</Email>
									<Document DocID=\"'.$bookModel->getDocumentId().'\" DocType=\"'.$bookModel->getDocumentType().'\"></Document>';
						for ($i=1; $i <= $rph ; $i++) { 
							 $request .= '<TravelerRefNumber RPH=\"'.$i.'\"/>';
						}
						/*if($availModel->getTypeDest()==1){	
						if($rph==1)	$request .= '<TravelerRefNumber RPH=\"01\"/>';
						if($rph==2)	{$request .= '<TravelerRefNumber RPH=\"01\"/>';$request .= '<TravelerRefNumber RPH=\"02\"/>';}

						}
						if($availModel->getTypeDest()==2){			
						$request .= '<TravelerRefNumber RPH=\"01\"/>';
						$request .= '<TravelerRefNumber RPH=\"02\"/>';
						}*/
						$request .= '</AirTraveler>';
				}
			}
			for($i=0; $i<count($passengerTypebebe); $i++){
				$bookModel= $passengerTypebebe[$i];		
					$request .= '<AirTraveler PassengerTypeCode=\"INF\">
								<PersonName>
									<GivenName>'.$bookModel->getFirstName().'</GivenName>
									<Surname>'.$bookModel->getLastName().'</Surname>
								</PersonName>
								<Telephone PhoneNumber=\"'.$bookModel->getTelephone().'\"/>
								<Email>'.$bookModel->getEmail().'</Email>
								<Document DocID=\"'.$bookModel->getDocumentId().'\" DocType=\"'.$bookModel->getDocumentType().'\"></Document>';
						for ($i=1; $i <= $rph ; $i++) { 
							 $request .= '<TravelerRefNumber RPH=\"'.$i.'\"/>';
						}
						$request .= '</AirTraveler>';
							 
			}
			for($i=0; $i<count($passengerTypeNino); $i++){
				$bookModel= $passengerTypeNino[$i];		
					$request .= '<AirTraveler PassengerTypeCode=\"CNN\">
								<PersonName>
									<GivenName>'.$bookModel->getFirstName().'</GivenName>
									<Surname>'.$bookModel->getLastName().'</Surname>
								</PersonName>
								<Telephone PhoneNumber=\"'.$bookModel->getTelephone().'\"/>
								<Email>'.$bookModel->getEmail().'</Email>
								<Document DocID=\"'.$bookModel->getDocumentId().'\" DocType=\"'.$bookModel->getDocumentType().'\"></Document>';
						for ($i=1; $i <= $rph ; $i++) { 
							 $request .= '<TravelerRefNumber RPH=\"'.$i.'\"/>';
						}
						$request .= '</AirTraveler>';
			}
			$request .= '</TravelerInfo>
						<Ticketing TicketTimeLimit=\"1\" />
					</KIU_AirBookRQ>';


			return $request;
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------

	public function processAirBookRS($xml){
		if($xml!=null){
			if ($xml->Error) {
				echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
			} else {
				$i = 1;

				echo '<fieldset><legend>Booking:</legend>	<pre>';
				echo '<b>Code:</b><br/>' . $xml->BookingReferenceID['ID'] . '<br/><br/><b>Paxs:</b><br/>';
				foreach ($xml->TravelerInfo->AirTraveler as $pax) {
					echo $i . '. ' 
						. $pax->PersonName->GivenName . ' ' . $pax->PersonName->Surname . ' ' . $pax->Document['DocType'] . $pax->Document['DocID'] . ' (' . $pax['PassengerTypeCode'] . '), '
						. 'Tel.' . $pax->Telephone['PhoneNumber'] . ' '
						. $pax->Email;
					$i++;
				}

				echo '<br/><br/><b>Itinerary:</b><br/>';

				$i = 1;
				foreach ($xml->AirItinerary->OriginDestinationOptions as $odo) {
					foreach ($odo->OriginDestinationOption as $o) {
						foreach ($o->FlightSegment as $f) {
							echo $i . '. ' 
								. $f->MarketingAirline['Code']
								. $f['FlightNumber'] . ' '
								. $f->DepartureAirport['LocationCode'] 
								. ' (' . $f['DepartureDateTime'] . ') -> '
								. $f->ArrivalAirport['LocationCode']
								. ' (' . $f['ArrivalDateTime'] . ') <br/>';
							$i++;
						}
					}
				}
				echo '</pre>
				<input type="button" value="Pagar" class="btn btn-primary" onclick="window.location=\'../Issue/index.php?BookingID=' . $xml->BookingReferenceID['ID'] . '\'"/> 
				 
				</fieldset>';
			}
		}else{
			echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.MSG_ERROR_TICKET.'</label></diV></diV>';
		}
	}


	//-------Metodo para consulta de Reservacion-------------------------------------------------------------------------------------------------
	public function TravelItineraryReadRQ($availModel,$itineraryModel){


		$request = '<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<KIU_TravelItineraryReadRQ EchoToken=\"'.$availModel->getEchoToken().'\" TimeStamp=\"'.$availModel->getTimeStamp().'\" Target=\"'.$availModel->getParticion().'\" Version=\"3.0\" SequenceNmbr=\"'.$availModel->getSequenceNmbr().'\" PrimaryLangID=\"en-us\">
				<POS>
					<Source AgentSine=\"'.$availModel->getSine().'\" TerminalID=\"'.$availModel->getDevice().'\" >
					</Source>
				</POS>
				<UniqueID Type=\"14\" ID=\"'.$itineraryModel->getPnr().'\" >
				</UniqueID>
			</KIU_TravelItineraryReadRQ>';

		return $request;
	}
	//_-----------------------------------------------------------------------------------------------------------------------------------------------

	//--- Procesar consulta de Reservación------------------------------------------------------------------------------------------------------------

	public function processTravelItineraryReadRQ($xml){

		if($xml!=null){
			if ($xml->Error) {
				echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
			} else {
				$i = 1;

				echo '<fieldset><legend>Reservation:</legend>	<pre>';
				echo '<b>Code:</b><br/>' . "\t" . $xml->TravelItinerary->ItineraryRef['ID'] . '<br/><br/><b>Paxs:</b><br/>';
				foreach ($xml->TravelItinerary->CustomerInfos->CustomerInfo as $pax) {
					echo "\t" . $i . '. ' 
						. $pax->Customer->PersonName->GivenName . ' ' . $pax->Customer->PersonName->Surname . ' ' . $pax->Customer->Document['DocType'] . $pax->Customer->Document['DocID'] . ' (' . $pax->Customer['PassengerTypeCode'] . '), '
						. 'Tel.' . $pax->Customer->ContactPerson->Telephone . ' '
						. $pax->Customer->ContactPerson->Email . '<br/>';
					$i++;
				}

				echo '<br/><b>Itinerary:</b><br/>';
				if ($xml->xpath('/KIU_TravelItineraryRS/TravelItinerary/ItineraryInfo/ReservationItems') != array()) {
					$i = 1;
					foreach ($xml->TravelItinerary->ItineraryInfo->ReservationItems->Item as $odo) {
						echo "\t" . $i . '. ' 
						. $odo->Air->Reservation->MarketingAirline
						. $odo->Air->Reservation['FlightNumber'] . ' '
						. $odo->Air->Reservation->DepartureAirport['LocationCode'] 
						. ' (' . $odo->Air->Reservation['DepartureDateTime'] . ') -> '
						. $odo->Air->Reservation->ArrivalAirport['LocationCode']
						. ' (' . $odo->Air->Reservation['ArrivalDateTime'] . ') <br/>';
						$i++;
					}
				} else {
					echo "\tNOT AVAILABLE.<br/>";
				}
				
				echo '<br/><b>Pricing:</b><br/>';
				
				
				if ($xml->xpath('/KIU_TravelItineraryRS/TravelItinerary/ItineraryInfo/ItineraryPricing/Cost') != array()) {
					$i = 1;
					echo "\t" . 'Amount before taxes: ' . $xml->TravelItinerary->ItineraryInfo->ItineraryPricing->Cost['AmountBeforeTax'] . '<br/>';
					
					foreach ($xml->TravelItinerary->ItineraryInfo->ItineraryPricing->Taxes->Tax as $t) {
						echo "\t\t($t[TaxCode]) " . str_pad($t['Amount'], 16, " ", STR_PAD_LEFT) . "($t[CurrencyCode])" . "<br/>";
					}
					
					if ($xml->xpath('/KIU_TravelItineraryRS/TravelItinerary/ItineraryInfo/ItineraryPricing/Fees') != array()) {
						foreach ($xml->TravelItinerary->ItineraryInfo->ItineraryPricing->Fees->Fee as $f) {
							echo "\t\t($f[FeeCode]) " . str_pad($f['Amount'], 10, " ", STR_PAD_LEFT) . "($f[CurrencyCode])" . "<br/>";
						}
					}
					echo "\t" . 'Total amount after taxes and fees: ' . $xml->TravelItinerary->ItineraryInfo->ItineraryPricing->Cost['AmountAfterTax'] . "<br/>";
				} else {
					echo "\tNOT AVAILABLE.<br/>";
				}
				
				if ($xml->xpath('/KIU_TravelItineraryRS/TravelItinerary/Remarks') != array()) {
					echo '<br/><b>Remarks:</b><br/>';
					
					foreach ($xml->TravelItinerary->Remarks->Remark as $rmk) {
						echo "\tRemark: " . $rmk . "<br/>";
					}
				}
				
				echo '<br/><b>Ticketing:</b><br/>';
				$tickets = array();
				
				$pnr_exists = true;
				foreach ($xml->TravelItinerary->ItineraryInfo->Ticketing as $tkt) {
					if ($tkt['TicketingStatus'] == "3") {
						$tickets[]= $tkt['eTicketNumber'];
						echo "\tTicket $tkt[eTicketNumber] ISSUED pax $tkt[TravelerRefNumber].<br/>";
					} elseif ($tkt['TicketingStatus'] == "1") {
						echo "\tRESERVATION NOT ISSUED Timelimit $tkt[TicketTimeLimit].<br/>";
					} elseif ($tkt['TicketingStatus'] == "5") {
						$pnr_exists = false;
						echo "\tRESERVATION EXPIRED OR CANCELLED.<br/>";
					}
				}
				
			}//END ELSE
		}else{echo "Problemas de conexion.";}
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------

	//--------Metodo par efectuar Pago de la Reservación-----------------------------------------------------------------------------------------------------
	function AirDemandTicketRQ($availModel,$issueModel){
		$xmlModel= new XMLModel();
		$request = '<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<KIU_AirDemandTicketRQ EchoToken=\"'.$availModel->getEchoToken().'\" TimeStamp=\"'.$availModel->getTimeStamp().'\" Target=\"'.$availModel->getParticion().'\" Version=\"3.0\" SequenceNmbr=\"'.$availModel->getSequenceNmbr().'\" PrimaryLangID=\"en-us\" >
			<POS>
				<Source AgentSine=\"'.$availModel->getSine().'\" TerminalID=\"'.$availModel->getDevice().'\" ISOCountry=\"'.$availModel->getCountry().'\" ISOCurrency=\"'.$availModel->getCurrency().'\" >
					<RequestorID Type=\"5\"/>
					<BookingChannel Type=\"1\"/>
				</Source>
			</POS>
			<DemandTicketDetail TourCode=\"'.$xmlModel->getTourCode().'\">
			<BookingReferenceID ID=\"'.$issueModel->getBookingID().'\">
				<CompanyName Code=\"'.$xmlModel->getCompanyCode().'\"/>
			</BookingReferenceID>';
			switch ($issueModel->getPaymentType()) {
				case 5:
					$request .= '
			<PaymentInfo PaymentType=\"5\">
			<CreditCardInfo CardType=\"1\" CardCode=\"'.$issueModel->getCreditCardCode().'\" CardNumber=\"'.$issueModel->getCreditCardNumber().'\" SeriesCode=\"'.$issueModel->getCreditSeriesCode().'\" ExpireDate=\"'.$issueModel->getCreditExpireDate().'\"/>
			';
					break;
			
				case 6:
					$request .= '
			<PaymentInfo PaymentType=\"6\">
			<CreditCardInfo CardType=\"1\" CardCode=\"'.$issueModel->getDebitCardCode().'\" CardNumber=\"'.$issueModel->getDebitCardNumber().'\" SeriesCode=\"'.$issueModel->getDebitSeriesCode().'\" />
			';
					break;
			
				case 34:
					$request .= '
			<PaymentInfo PaymentType=\"34\" InvoiceCode=\"'.$issueModel->getInvoiceCode().'\">
			';
					break;
				case 37:
					$request .= '
			<PaymentInfo PaymentType=\"37\" MiscellaneousCode=\"'.$issueModel->getMiscellaneousCode().'\" Text=\"'.$issueModel->getText().'\">
			';
					break;
				case 1:
					$request .= "
			<PaymentInfo PaymentType=\"1\">
			";
					break;
			}
			$request .= '<ValueAddedTax VAT=\"'.$issueModel->getValueAddedTax().'\"/>
			</PaymentInfo>
			<Endorsement Info=\"THIS TICKET IS NONREFUNDABLE\"/>
			</DemandTicketDetail>
		</KIU_AirDemandTicketRQ>';

		return $request;
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------

	//--------Metodo para procesar informacion del Pago de la Reservación------------------------------------------------------------------------------------

	function processAirDemandTicketRS($xml){

	if($xml!=null){	
		if ($xml->Error) {
			echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
			echo "<input type=\"button\" value=\"Back\" class=\"btn btn-primary\" onclick=\"issue_result();\"/>";
			echo '<input type="button" value="Nueva Reservación" class="btn btn-primary" onclick="window.location=\'../Avail/index.php\'"/>';
		} else {
			echo "<pre>";
			foreach ($xml->TicketItemInfo as $ticket) {
				$TicketNumber = $ticket['TicketNumber'];
				$CommissionAmount = $ticket['CommissionAmount'];
				$TotalAmount = $ticket['TotalAmount'];
				$GivenName = $ticket->PassengerName->GivenName;
				$Surname = $ticket->PassengerName->Surname;
				echo "<hr/>	
					<b>Ticket:</b> $TicketNumber
					<b>Amount:</b>  $TotalAmount
					<b>Commision:</b>$CommissionAmount
					<b>GivenName:</b>$GivenName
					<b>Surname:</b>$Surname
				";
			}
			echo "</pre>";
			 
			echo '<input type="button" value="Nueva Reservación" class="btn btn-primary" onclick="window.location=\'../Avail/index.php\'"/>';
		}
	}else{
		echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.MSG_ERROR_RESERVATION.'</label></diV></diV>';
	}

	}
	//--------Metodo de Cancelacion de Reservación-------------------------------------------------------------------------------------------------------------
	public function CancelRQ($availModel,$cancelModel){

		$request = '<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<KIU_CancelRQ EchoToken=\"'.$availModel->getEchoToken().'\" TimeStamp=\"'.$availModel->getTimeStamp().'\" Target=\"'.$availModel->getParticion().'\" Version=\"3.0\" SequenceNmbr=\"'.$availModel->getSequenceNmbr().'\" PrimaryLangID=\"en-us\">
				<POS>
					<Source AgentSine=\"'.$availModel->getSine().'\" TerminalID=\"'.$availModel->getDevice().'\" >
					</Source>
				</POS>';

		if ($cancelModel->getPnr()!=null)$request .= '<UniqueID Type=\"14\" ID=\"'.$cancelModel->getPnr().'\" />';

		if ($cancelModel->getTicket()!=null) $request .= '<UniqueID Type=\"30\" ID=\"'.$cancelModel->getTicket().'\" />
			<Ticketing TicketTimeLimit=\"1\" />';

		$request .= '</KIU_CancelRQ>';

		return $request;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------

	//--------Metodo de procesar Cancelacion de Reservación------------------------------------------------------------------------------------------------------
	public function processCancelRS($xml,$cancelModel){
		if($xml!=null){
			if ($xml->Error->ErrorCode) {
				echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
			} else {
				if ($xml->xpath('/KIU_CancelRS/Ticketing') != array()) {
					$tl = $xml->Ticketing['TicketTimeLimit'];
					echo '<pre><b>TICKET</b>'.$cancelModel->getTicket().' HAS BEEN VOIDED.<br/><b>NEW RESERVATION TIMELIMIT:</b> $tl.</pre>';
				} else {
					echo '<pre><b>RESERVATION</b> '.$cancelModel->getPnr().' HAS BEEN CANCELLED.<br></pre>';
				}
			}
		}else{
			echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.MSG_ERROR.'</label></diV></diV>';
		}
	}
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------


}


?>
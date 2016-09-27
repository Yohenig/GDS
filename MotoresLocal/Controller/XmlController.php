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
	/**
	* Metodo que permite procesar los datos de disponibilidad de KIU
	* @param $availModel Instancia del Modelo Avail que contiene todos los datos sobre la consulta de disponibilidad
	* @param $xml String del Xml con la respuesta de la disponibilidad solicitada
	* @param $http instancia de conexion CURL establecida
	*/

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
		file_put_contents($file, $json);
		header('Location: ../View/Avail/availRS.php');
    }


	//--------------------------------------------------------------------------------------------------------------------------------------------

	/**
	* Metodo que permite armar el XML de para consulta de precios de KIU--------------------------------------------------------------------------
	*/
	public function AirPriceRS($dataPrice){
		$priceModel= new AvailModel();
		$priceModel->properties();
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
	

	//--------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Metodo que permite procesar los datos de la consulta de precios de KIU----------------------------------------------------------------------
	*/
	//Rafael Lizarazo AGREGADO PARA PROCESAR PRECIOS Y RESTRUCTURAR DATOS

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


	function processAirpriceRL($OriginDestinationInformation){

		#echo "<pre>".htmlentities(print_r($OriginDestinationInformation, true)) ."</pre>";

		$h=0;
		$http = new HttpConnection();
		$http->init();	
		$count=0;
		$count2=0;


		if(count($OriginDestinationInformation)>0){
			$count=count($OriginDestinationInformation[1]['OriginDestinationOptions']['OriginDestinationOption']);
		}
		
		if ($OriginDestinationInformation[1]['TypeDest']==2) {
			$count2=count($OriginDestinationInformation[2]['OriginDestinationOptions']['OriginDestinationOption']);			 
		}


		//----------------------------------------------------------------------------------------------------------------------------------------//
		//----------------------------------------------------------------------------------------------------------------------------------------//
		//----------------------------------------------------------------------------------------------------------------------------------------//
		//----------------------------------------------------------------------------------------------------------------------------------------//
		//DEVUELVE EL JSON RESTRUCTURADO CON LOS PRECIOS PARA VUELOS DE IDA
		if ($OriginDestinationInformation[1]['TypeDest']==1) {

			$currency = 'VEF';
			$OriginDestinationInformation[1]['TypeGDS']='1';
			$OriginDestinationInformation[1]['conversionRate']['conversionRateDetail']['currency']=$currency;

			//$OriginDestinationInformation = count($OriginDestinationInformation); 
			#$dataPrice=array();

			$validar_precios=array();

			for ($i=1; $i <=1 ; $i++) { 
						$v=1;
						$v2=0; //variable ṕara ordenar las recomendaciones

				while($v<=$count){
					//Informacion para vules directos/escala
					#$flightCount=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);
					#$option_params = "{'SegmentsCount':" . $flightCount . ", 'Segments':Array(";




					$dataPrice = array();
					//Variables para totalizar el costo de todos los pasajeros
					$TotalFare=0;
					$BaseFare=0;
					$ImpTasas=0;
					$Cargos=0;
					$BaseFareAdulto=0;
					$BaseFareBebe=0;
					$BaseFareNino=0;	
					
						for($z=1; $z<=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);$z++){

							$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['propFlightGrDetail'][0]['ref']=$v;					

							$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['MarketingAirline']['OperatingCarrier'][0]='0';
							$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['BookingClassAvail'][1]['productDetailQualifier'][0]='0';

							$airline = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['MarketingAirline']['CompanyShortName'][0];
							$dairport = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureAirport']['LocationCode'][0];	
							$aairport = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['ArrivalAirport']['LocationCode'][0];


							$flight = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['FlightNumber'][0];
							$time =  $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['JourneyDuration'][0];
							$ddatetime = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureDateTime'][0];
							$adatetime = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['ArrivalDateTime'][0];
							$stops = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['StopQuantity'][0];
							$meal = $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['Meal']['MealCode'][0];						
							$adulto= $OriginDestinationInformation[1]['PassengerTypeQuantityAdulto'];
							$bebe= $OriginDestinationInformation[1]['PassengerTypeQuantityBebe'];
							$nino= $OriginDestinationInformation[1]['PassengerTypeQuantityNino'];
							$mayor=$OriginDestinationInformation[1]['PassengerTypeQuantityMayor'];
							$type=$OriginDestinationInformation[1]['TypeDest'];



							$timeOfDeparture=substr($ddatetime, 10);
							$timeOfArrival=substr($adatetime, 10);
							$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['timeOfDeparture'][0]=$timeOfDeparture;
							$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['timeOfArrival'][0]=$timeOfArrival;



							//NOMBRE COMPLETO DE LA AEROLINEAS Y AEROPUERTOS
							#$airline2=$properties->_getAirline($airline);
							#$dairport2=$properties->_getCity($dairport);
							#$aairport2=$properties->_getCity($aairport);


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

							//----------------------------------------------------------------------------------------
							//option_params es información repetida es preferible armarlo en la vista.
							//-----------------------------------------------------------------------------------------
							#$option_params .= "{MarketingAirline:'$airline', FlightNumber:$flight, DepartureDateTime:'$ddatetime', ArrivalDateTime:'$adatetime', ";
							#$option_params .= "PassengerTypeQuantityAdulto:'$adulto', PassengerTypeQuantityNino:$nino, PassengerTypeQuantity3edad:'$mayor', PassengerTypeQuantitybebe:'$bebe', JourneyDuration:'$time', ";
							#$option_params .= "DepartureAirport:'$dairport', ArrivalAirport:'$aairport', Destino:'1', Type:'$type', ResBookDesigCode:'$available_classes[0]', Classes:$class_list},";
						}	

					//MANEJO DE PRECIOS COMBINADOS-----------------------------------------
	 				//Instancia con el xmlController

					$request=$this->AirPriceRS($dataPrice);
					$response= $http->post($request);
					$xml = simplexml_load_string($response);
					$price1=$this->processAirPriceRQ($xml,$request,$response);

						if($price1!=null){
							//Se agregan los precios al segmento de vuelos correspondientes
							//if ($price1->getTotalFare()!=0.0) {
							
							/*	
								$FlightSegment['TotalFare']=$price1->getTotalFare();
								$FlightSegment['BaseFare']=$price1->getBaseFare();
								$FlightSegment['ImpTasas']=$price1->getImpTasas();
								$FlightSegment['Cargos']=$price1->getCargos();
								$FlightSegment['BaseFareAdulto']=$price1->getBaseFareAdulto();
								$FlightSegment['BaseFareBebe']=$price1->getBaseFareBebe();
								$FlightSegment['BaseFareNino']=$price1->getBaseFareNino();
								$FlightSegment['Error']=$price1->getError();
								
								$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]=$FlightSegment;
							/**/	
								//------------------------------------------------------------------------------------------------------------------------------

								$fareTotal=$price1->getTotalFare();
								$fareImp=$price1->getImpTasas();

								$fareADT=$price1->getBaseFareAdulto();
								$fareCHD=$price1->getBaseFareNino();
								$fareINF=$price1->getBaseFareBebe();
								//$fareYCD=$price1->getBaseFare3raedad();

								if (in_array($fareTotal, $validar_precios)) {
								   
									// Colocar el id del FlightSegment en el segmentFlightRef que corresponde para agrupar por precio.
									
									$recpos = array_search($fareTotal, $validar_precios);

									$sfrc= count($OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][0]['referencingDetail']);
									$sfrc++;
									
									$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][$sfrc]['referencingDetail'][0]['refQualifier']='S';
									$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][$sfrc]['referencingDetail'][0]['refNumber']=$v	;
								

								}else {
									// Aqui se crea si no existe un precio, el cuerpo de la estructura del 'recommendation'

									array_push($validar_precios, $fareTotal);

								 
									$pos=1;
									$vpax=0; // variable para ordear el paxFare


									$OriginDestinationInformation[1]['recommendation'][$v2]['recPriceInfo']['monetaryDetail'][0]['amount']=$fareTotal;
									$OriginDestinationInformation[1]['recommendation'][$v2]['recPriceInfo']['monetaryDetail'][1]['amount']=$fareImp;

									$OriginDestinationInformation[1]['recommendation'][$v2]['segmentFlightRef'][0]['referencingDetail'][0]['refQualifier']='S';
									$OriginDestinationInformation[1]['recommendation'][$v2]['segmentFlightRef'][0]['referencingDetail'][0]['refNumber']=$v;

									if ($adulto!=0) {
										$paxADT['paxFareDetail']['paxFareNum'] = $pos++;
										$paxADT['paxFareDetail']['totalFareAmount'] = $fareADT;
										$paxADT['paxFareDetail']['totalTaxAmount'] = '0'; //No se de donde tomar los Impuestos de Adultos
										$paxADT['paxReference']['ptc'] = 'ADT';
										$OriginDestinationInformation[1]['recommendation'][$v2]['paxFareProduct'][$vpax]=$paxADT;
										$vpax++;
									}	

									if ($nino!=0){
										$paxCHD['paxFareDetail']['paxFareNum'] = $pos++;
										$paxCHD['paxFareDetail']['totalFareAmount'] = $fareCHD;
										$paxCHD['paxFareDetail']['totalTaxAmount'] = '0'; //No se de donde tomar los impuestos de los Niños
										$paxCHD['paxReference']['ptc'] = 'CHD';
										$OriginDestinationInformation[1]['recommendation'][$v2]['paxFareProduct'][$vpax]=$paxCHD;
										$vpax++;
									}

									if ($mayor!=0){
										$paxYCD['paxFareDetail']['paxFareNum'] = $pos++;
										$paxYCD['paxFareDetail']['totalFareAmount'] = '0'; //$fareYCD //No se de donde tomar los precios de los 3ra Edad
										$paxYCD['paxFareDetail']['totalTaxAmount'] = '0';  //No se de donde tomar los impuestos de los 3ra edad
										$paxYCD['paxReference']['ptc'] = 'YCD';
										$OriginDestinationInformation[1]['recommendation'][$v2]['paxFareProduct'][$vpax]=$paxYCD;
										$vpax++;
									}

									if ($bebe!=0){
										$paxINF['paxFareDetail']['paxFareNum'] = $pos++;
										$paxINF['paxFareDetail']['totalFareAmount'] = $fareINF;
										$paxINF['paxFareDetail']['totalTaxAmount'] = '0'; //No se de donde tomar los impuestos de los Infantes
										$paxINF['paxReference']['ptc'] = 'INF';
										$OriginDestinationInformation[1]['recommendation'][$v2]['paxFareProduct'][$vpax]=$paxINF;
										$vpax;
									}
									$v2++;
								}
							

						}
					

				$v++;
				}// end While

			}//end for

		} //FIN DEL RESTRUCTURAMIENTO DE JSON IDA



		//----------------------------------------------------------------------------------------------------------------------------------------//
		//----------------------------------------------------------------------------------------------------------------------------------------//
		//----------------------------------------------------------------------------------------------------------------------------------------//
		//----------------------------------------------------------------------------------------------------------------------------------------//
		//DEVUELVE EL JSON RESTRUCTURADO CON LOS PRECIOS PARA VUELOS DE IDA Y VUELTO
		if ($OriginDestinationInformation[1]['TypeDest']==2) {

			$currency = 'VEF';
			$OriginDestinationInformation[1]['TypeGDS']='1';
			$OriginDestinationInformation[1]['conversionRate']['conversionRateDetail']['currency']=$currency;

			#echo count($OriginDestinationInformation).'<br />';
			$validar_precios=array();
			$dataPrice=array();
			

			for ($i=1; $i <=1 ; $i++) { //INICIO DEL FOR "A"
				$v=1;
				$v2=0;
				
				for ($j=$i+1; $j <=2 ; $j++) { //INICIO DEL FOR "B"

				    while($v<=$count){
			    		#echo 'Mientras '.$v.' sea menor o iguala ';
						#echo $count.' <br /> SETEA!';


						for ($x=1; $x <=$count2 ; $x++) { //INICIO DEL FOR "C"

							$flightCount=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);
							$flightCount2=count($OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment']);

							$dataPrice = array();
							//Llamada al metodo AirPriceRQ para armar el xml-------------------------------------------------------------
							$TotalFare=0;
							$BaseFare=0;
							$ImpTasas=0;
							$Cargos=0;
							$BaseFareAdulto=0;
							$BaseFareBebe=0;
							$BaseFareNino=0; //Variables para totalizar el costo de todos los pasajero

							for($z=1; $z<=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);$z++){ //INICIO DEL FOR "D"

								$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['propFlightGrDetail'][0]['ref']=$v;
								$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['MarketingAirline']['OperatingCarrier'][0]='0';
								$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['BookingClassAvail'][1]['productDetailQualifier'][0]='0';

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

								$timeOfDeparture=substr($ddatetime, 10);
								$timeOfArrival=substr($adatetime, 10);
								$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['timeOfDeparture'][0]=$timeOfDeparture;
								$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['timeOfArrival'][0]=$timeOfArrival;

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

							} //FIN DEL FOR "D"

							if($x<=$count2){ 
								
								for($y=1; $y<=count($OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment']);$y++){ //INICIO DEL FOR "E"


									$OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['propFlightGrDetail'][0]['ref']=$v;
									$OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['MarketingAirline']['OperatingCarrier'][0]='0';
									$OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['BookingClassAvail'][1]['productDetailQualifier'][0]='0';


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

									$timeOfDeparture=substr($ddatetime, 10);
									$timeOfArrival=substr($adatetime, 10);
									$OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['timeOfDeparture'][0]=$timeOfDeparture;
									$OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['timeOfArrival'][0]=$timeOfArrival;


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



									#$OriginDestinationInformation = $dataPrice;


								}//FIN DEL FOR "E"

							} //FIN DEL IF

							//MANEJO DE PRECIOS COMBINADOS ------------------------------------------------------------------------------------------------------
																	 		
							$request=$this->AirPriceRS($dataPrice);//llamado al metodo con el objeto @param priceModel2 para armar XML para obtener los precios por cada intinerario encontrado
							$response= $http->post($request); // Procesando XML armado 
							$xml = simplexml_load_string($response);//Tranformacion del xml en string
							$price1=$this->processAirPriceRQ($xml,$request,$response); //Procesando los datos resultantes del precio


							if($price1!=null) {

								/*
								$FlightSegment['TotalFare']=$price1->getTotalFare();
								$FlightSegment['BaseFare']=$price1->getBaseFare();
								$FlightSegment['ImpTasas']=$price1->getImpTasas();
								$FlightSegment['Cargos']=$price1->getCargos();
								$FlightSegment['BaseFareAdulto']=$price1->getBaseFareAdulto();
								$FlightSegment['BaseFareBebe']=$price1->getBaseFareBebe();
								$FlightSegment['BaseFareNino']=$price1->getBaseFareNino();
								$FlightSegment['Error']=$price1->getError();

								$f++;
								$OriginDestinationInformation[3]['OriginDestinationOptions']['OriginDestinationOption'][$f]=$FlightSegment;

								/**/


								/*
								$FlightSegment['TotalFare']=$price1['TotalFare:PriceModel:private'];
								$FlightSegment['BaseFare']=$price1['BaseFare:PriceModel:private'];
								$FlightSegment['ImpTasas']=$price1['impTasas:PriceModel:private'];
								$FlightSegment['Cargos']=$price1['cargos:PriceModel:private'];
								$FlightSegment['BaseFareAdulto']=$price1['BaseFareAdulto:PriceModel:private'];
								$FlightSegment['BaseFareBebe']=$price1['BaseFareNino:PriceModel:private'];
								$FlightSegment['BaseFareNino']=$price1['BaseFareBebe:PriceModel:private'];
								$FlightSegment['Error']=$price1['Error:PriceModel:private'];
								/**/

								//--------------------------------------------------------------------------------------


								


								$fareTotal=$price1->getTotalFare();
								$fareImp=$price1->getImpTasas();

								$fareADT=$price1->getBaseFareAdulto();
								$fareCHD=$price1->getBaseFareNino();
								$fareINF=$price1->getBaseFareBebe();
								//$fareYCD=$price1->getBaseFare3raedad();

								if (in_array($fareTotal, $validar_precios)) {
								   
									// Colocar los id de los FlightSegment's correspondientes para agrupar las permutaciones por precio.
									
									$recpos = array_search($fareTotal, $validar_precios);

									$sfrc= count($OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef']);
									$sfrc++;


																		
									$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][$sfrc]['referencingDetail'][0]['refQualifier']='S';
									$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][$sfrc]['referencingDetail'][0]['refNumber']=$v;

									$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][$sfrc]['referencingDetail'][1]['refQualifier']='S';
									$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][$sfrc]['referencingDetail'][1]['refNumber']=$x;
								
									//$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][$sfrc]['referencingDetail'][2]['refQualifier']='B';
									//$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][$sfrc]['referencingDetail'][2]['refNumber']='???';

								}else {
									// Aqui se crea si no existe un precio, el cuerpo de la estructura del 'recommendation'

									array_push($validar_precios, $fareTotal);

								 
									$pos=1;
									$vpax=0; // variable para ordear el paxFare


									$OriginDestinationInformation[1]['recommendation'][$v2]['recPriceInfo']['monetaryDetail'][0]['amount']=$fareTotal;
									$OriginDestinationInformation[1]['recommendation'][$v2]['recPriceInfo']['monetaryDetail'][1]['amount']=$fareImp;

									$OriginDestinationInformation[1]['recommendation'][$v2]['segmentFlightRef'][0]['referencingDetail'][0]['refQualifier']='S';
									$OriginDestinationInformation[1]['recommendation'][$v2]['segmentFlightRef'][0]['referencingDetail'][0]['refNumber']=$v;

									$OriginDestinationInformation[1]['recommendation'][$v2]['segmentFlightRef'][0]['referencingDetail'][1]['refQualifier']='S';
									$OriginDestinationInformation[1]['recommendation'][$v2]['segmentFlightRef'][0]['referencingDetail'][1]['refNumber']=$x;

									//$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][0]['referencingDetail'][2]['refQualifier']='B';
									//$OriginDestinationInformation[1]['recommendation'][$recpos]['segmentFlightRef'][0]['referencingDetail'][2]['refNumber']='???';
									

									if ($adulto!=0) {
										$paxADT['paxFareDetail']['paxFareNum'] = $pos++;
										$paxADT['paxFareDetail']['totalFareAmount'] = $fareADT;
										$paxADT['paxFareDetail']['totalTaxAmount'] = '0'; //No se de donde tomar los Impuestos de Adultos
										$paxADT['paxReference']['ptc'] = 'ADT';
										$OriginDestinationInformation[1]['recommendation'][$v2]['paxFareProduct'][$vpax]=$paxADT;
										$vpax++;
									}	

									if ($nino!=0){
										$paxCHD['paxFareDetail']['paxFareNum'] = $pos++;
										$paxCHD['paxFareDetail']['totalFareAmount'] = $fareCHD;
										$paxCHD['paxFareDetail']['totalTaxAmount'] = '0'; //No se de donde tomar los impuestos de los Niños
										$paxCHD['paxReference']['ptc'] = 'CHD';
										$OriginDestinationInformation[1]['recommendation'][$v2]['paxFareProduct'][$vpax]=$paxCHD;
										$vpax++;
									}

									if ($mayor!=0){
										$paxYCD['paxFareDetail']['paxFareNum'] = $pos++;
										$paxYCD['paxFareDetail']['totalFareAmount'] = '0'; //$fareYCD //No se de donde tomar los precios de los 3ra Edad
										$paxYCD['paxFareDetail']['totalTaxAmount'] = '0';  //No se de donde tomar los impuestos de los 3ra edad
										$paxYCD['paxReference']['ptc'] = 'YCD';
										$OriginDestinationInformation[1]['recommendation'][$v2]['paxFareProduct'][$vpax]=$paxYCD;
										$vpax++;
									}

									if ($bebe!=0){
										$paxINF['paxFareDetail']['paxFareNum'] = $pos++;
										$paxINF['paxFareDetail']['totalFareAmount'] = $fareINF;
										$paxINF['paxFareDetail']['totalTaxAmount'] = '0'; //No se de donde tomar los impuestos de los Infantes
										$paxINF['paxReference']['ptc'] = 'INF';
										$OriginDestinationInformation[1]['recommendation'][$v2]['paxFareProduct'][$vpax]=$paxINF;
										$vpax;
									}
									$v2++;
								}

							}

						} //FIN DEL FOR "C"
						
					$v++;
					} //FIN DEL WHILE
				
				} //FIN DEL FOR "B"
			
			} //FIN DL FOR "A"									

		} //FIN DEL RESTRUCTURAMIENTO DE JSON IDA Y VUELTA



		$http->close();//Cerrar conexion al servidor KIU 

		#echo "<pre>".htmlentities(print_r($OriginDestinationInformation, true)) ."</pre>";

	return $OriginDestinationInformation;

	}
	

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
						if(isset($_POST["Destino_$i"])){
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
			$request .='</OriginDestinationOptions>';
			$request .='</AirItinerary>';

			}
			if($availModel->getTypeDest()==2){
					$request .='<AirItinerary>';
					$request .='<OriginDestinationOptions>
								<OriginDestinationOption>';
					$rph=0;
					for ($i=0;$i<$_POST['Segments'];$i++) {
						if(isset($_POST["Destino_$i"])){
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
					}
					$request .= '</OriginDestinationOption>';
					 
					 
					$request .='<OriginDestinationOption>';

					for ($i=0;$i<$_POST['Segments'];$i++) {
						if(isset($_POST["Destino_$i"])){
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

	public function processAirBookRS($xml, $pago){
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
				
				
				echo '</pre>';
				echo '</fieldset>';
				if($pago=="compra"){
				echo '<input type="button" value="Pagar" class="btn btn-primary" onclick="window.location=\'../Issue/index.php?BookingID=' . $xml->BookingReferenceID['ID'] . '\'"/> ';
				}else{
					echo '<input type="button" value="Nueva Reservación" class="btn btn-primary" onclick="window.location=\'../Avail/index.php\'"/>';
				}
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

	function processViewItinerary($itinerary){
		header('Content-type: application/json');
		$json=json_encode($itinerary);

		$file = 'itinerary.json';
		file_put_contents($file, $json);
		header('Location: ../View/Itinerary/itineraryRS.php');
    }




	//--------Metodo para efectuar Pago de la Reservación-----------------------------------------------------------------------------------------------------
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
		$issue= array();
		if($xml!=null){	
			if ($xml->Error) {
				echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
				echo "<input type=\"button\" value=\"Back\" class=\"btn btn-primary\" onclick=\"issue_result();\"/>";
				echo '<input type="button" value="Nueva Reservación" class="btn btn-primary" onclick="window.location=\'../Avail/index.php\'"/>';
			} else {
				$i=1;
				foreach ($xml->TicketItemInfo as $ticket) {
					$issue[$i]= array('TicketNumber'=>$ticket['TicketNumber'],
					'CommissionAmount'=>$ticket['CommissionAmount'],
					'TotalAmount'=>$ticket['TotalAmount'],
					'GivenName'=>$ticket->PassengerName->GivenName,
					'Surname'=>$ticket->PassengerName->Surname); 

					$i++;
				}
				 
			}
		}else{
			echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.MSG_ERROR_RESERVATION.'</label></diV></diV>';
		}
		return $issue;
	}

	function processViewAirDemandTicket($issue){
		header('Content-type: application/json');
		$json=json_encode($issue);

		$file = 'issue.json';
		file_put_contents($file, $json);
		header('Location: ../View/Issue/issueRS.php');
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
	

	//--------Metodo de procesar Cancelacion de Reservación------------------------------------------------------------------------------------------------------
	public function processCancelRS($xml,$cancelModel){
		$cancel=array();
		if($xml!=null){
			if ($xml->Error->ErrorCode) {
				echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.'Error '.$xml->Error->ErrorCode.' '.$xml->Error->ErrorMsg.'</label></diV></diV>';
			} else {
				$i=1;
				
				$cancel[$i]= array('TicketTimeLimit'=>$xml->Ticketing['TicketTimeLimit'],'Ticket'=>$cancelModel->getTicket(),'Pnr'=>$cancelModel->getPnr(),
				'xpath'=>$xml->xpath('/KIU_CancelRS/Ticketing')); 
			}
		}else{
			echo '<div class="container row"><div class="col-sm-12 col-md-12 col-lg-12"><label class="control-label alert_error">'.MSG_ERROR.'</label></diV></diV>';
		}
		return $cancel;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------
	function processViewCancel($cancel){
		header('Content-type: application/json');
		$json=json_encode($cancel);

		$file = 'cancel.json';
		file_put_contents($file, $json);
		header('Location: ../View/Cancel/cancelRS.php');
    }
    //-----------------------------------------------------------------------------------------------------------------------------------------------------------

}


?>
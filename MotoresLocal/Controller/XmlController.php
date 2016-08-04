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
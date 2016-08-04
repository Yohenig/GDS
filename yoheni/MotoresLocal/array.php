<?php 

/*$i=1;
foreach ($xml->OriginDestinationInformation as $odi) {

    $OriginDestinationInformation[$i]= array('DepartureDateTime'=>$odi->DepartureDateTime,'OriginLocation'=>$odi->OriginLocation,'DestinationLocation'=>$odi->DestinationLocation);

	$j=1;
	foreach ($odi->OriginDestinationOptions->OriginDestinationOption as $odo) {

		$x=1;
		foreach ($odo->FlightSegment as $fs) {

			$FlightSegment[$x]= array('DepartureDateTime'=>$fs['DepartureDateTime'],'ArrivalDateTime'=>$fs['ArrivalDateTime'],'StopQuantity'=>$fs['StopQuantity'], 'FlightNumber'=>$fs['FlightNumber'],'JourneyDuration'=>$fs['JourneyDuration']);

			$DepartureAirport=array('LocationCode' => $fs->DepartureAirport['LocationCode'] );
			$FlightSegment['DepartureAirport']=$DepartureAirport;

			$ArrivalAirport=array('LocationCode' => $fs->ArrivalAirport['LocationCode'] );
			$FlightSegment['ArrivalAirport']=$ArrivalAirport;

			$Equipment=array('AirEquipType' => $fs->Equipment['AirEquipType'] );
			$FlightSegment['Equipment']=$Equipment;

			$MarketingAirline=array('CompanyShortName' => $fs->MarketingAirline['CompanyShortName'] );
			$FlightSegment['MarketingAirline']=$MarketingAirline;

			$$Meal=array('MealCode' =>  $fs->Meal['MealCode'] );
			$FlightSegment['Meal']=$Meal;

			$MarketingCabin=array('CabinType'=>'', 'RPH'=>'');
			$FlightSegment['MarketingCabin']=$MarketingCabin;

			foreach ($fs->BookingClassAvail as $bca) {
				if (($bca['ResBookDesigQuantity'] >= '1') && ($bca['ResBookDesigQuantity'] <= '9')) {
					$BookingClassAvail=array('ResBookDesigCode'=>$bca['ResBookDesigCode'],'ResBookDesigQuantity'=>$bca['ResBookDesigQuantity'],'RPH'=>$bca['RPH']);
					$FlightSegment['BookingClassAvail']=$BookingClassAvail;
				}
			}

			$OriginDestinationOption[$j]['FlightSegment']=$FlightSegment[$x];
		$x++;
		}

		$OriginDestinationOptions['OriginDestinationOption']=$OriginDestinationOption[$j];
		$OriginDestinationInformation[$i]['OriginDestinationOptions']=$OriginDestinationOptions;
		$j++;
	}
	$i++;
}
*/



	$OriginDestinationInformation[1]= array('DepartureDateTime'=>'121','OriginLocation'=>'','DestinationLocation'=>'');


	$FlightSegment[1]= array('DepartureDateTime'=>'456','ArrivalDateTime'=>'','StopQuantity'=>'', 'FlightNumber'=>'','JourneyDuration'=>'');
	$FlightSegment[2]= array('DepartureDateTime'=>'678','ArrivalDateTime'=>'','StopQuantity'=>'', 'FlightNumber'=>'','JourneyDuration'=>'');
	$DepartureAirport=array('LocationCode' => '' );
	$FlightSegment['DepartureAirport']=$DepartureAirport;

	$ArrivalAirport=array('LocationCode' => '' );
	$FlightSegment['ArrivalAirport']=$ArrivalAirport;

	$Equipment=array('AirEquipType' => '' );
	$FlightSegment['Equipment']=$Equipment;

	$MarketingAirline=array('CompanyShortName' => '' );
	$FlightSegment['MarketingAirline']=$MarketingAirline;

	$$Meal=array('MealCode' => '' );
	$FlightSegment['Meal']=$Meal;

	$MarketingCabin=array('CabinType'=>'', 'RPH'=>'');
	$FlightSegment['MarketingCabin']=$MarketingCabin;

	$BookingClassAvail=array('ResBookDesigCode'=>'','ResBookDesigQuantity'=>'' ,'RPH'=>'');
	$FlightSegment['BookingClassAvail']=$BookingClassAvail;


	$OriginDestinationOption[1]['FlightSegment']=$FlightSegment;
	$OriginDestinationOption[2]['FlightSegment']=$FlightSegment;
	$OriginDestinationOptions['OriginDestinationOption']=$OriginDestinationOption;
	$OriginDestinationInformation[1]['OriginDestinationOptions']=$OriginDestinationOptions;




	$OriginDestinationInformation[2]= array('DepartureDateTime'=>'121','OriginLocation'=>'','DestinationLocation'=>'');


	$FlightSegment1[1]= array('DepartureDateTime'=>'789','ArrivalDateTime'=>'','StopQuantity'=>'', 'FlightNumber'=>'','JourneyDuration'=>'');
	$FlightSegment2[1]= array('DepartureDateTime'=>'1011','ArrivalDateTime'=>'','StopQuantity'=>'', 'FlightNumber'=>'','JourneyDuration'=>'');

	$DepartureAirport1=array('LocationCode' => '' );
	$FlightSegment1['DepartureAirport']=$DepartureAirport1;

	$ArrivalAirport1=array('LocationCode' => '' );
	$FlightSegment1['ArrivalAirport']=$ArrivalAirport1;

	$Equipment1=array('AirEquipType' => '' );
	$FlightSegment1['Equipment']=$Equipment1;

	$MarketingAirline1=array('CompanyShortName' => '' );
	$FlightSegment1['MarketingAirline']=$MarketingAirline1;

	$$Meal1=array('MealCode' => '' );
	$FlightSegment1['Meal']=$Meal1;

	$MarketingCabin1=array('CabinType'=>'', 'RPH'=>'');
	$FlightSegment['MarketingCabin']=$MarketingCabin1;

	$BookingClassAvail1=array('ResBookDesigCode'=>'','ResBookDesigQuantity'=>'' ,'RPH'=>'');
	$FlightSegment1['BookingClassAvail']=$BookingClassAvail1;


	$OriginDestinationOption1[1]['FlightSegment']=$FlightSegment1;
	$OriginDestinationOption1[2]['FlightSegment']=$FlightSegment2;
	$OriginDestinationOption1[3]['FlightSegment']=$FlightSegment1;
	$OriginDestinationOptions1['OriginDestinationOption']=$OriginDestinationOption1;

	$OriginDestinationInformation[2]['OriginDestinationOptions']=$OriginDestinationOptions1;
	
$count=count($OriginDestinationInformation[1]['OriginDestinationOptions']['OriginDestinationOption']);
$count2=count($OriginDestinationInformation[2]['OriginDestinationOptions']['OriginDestinationOption']);

for ($i=1; $i <=1 ; $i++) { 
	$v=1;
	for ($j=$i+1; $j <=2 ; $j++) { 
     
	    while($v<=$count){
			for ($x=1; $x <=$count2 ; $x++) { 

				echo $i.".".$v."<br/>";
				echo $j.".".$x."<br/>";

				for($z=1; $z<=count($OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment']);$z++){

					echo $OriginDestinationInformation[$i]['OriginDestinationOptions']['OriginDestinationOption'][$v]['FlightSegment'][$z]['DepartureDateTime'];
		
				}
				echo "<br/>";
				for($y=1; $y<=count($OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment']);$y++){

					echo $OriginDestinationInformation[$j]['OriginDestinationOptions']['OriginDestinationOption'][$x]['FlightSegment'][$y]['DepartureDateTime'];
		
				}
				echo "<br/>";
			}
		echo "<br/><br/>";
		$v++;

		}	 
	}
}


/*
echo "Cantidad de option principal ".count($OriginDestinationInformation);
for ($i=1; $i <= count($OriginDestinationInformation) ; $i++) { 

	echo "Cantidad de opciones ".count($OriginDestinationInformation[$i]['OriginDestinationOptions'][$i]['OriginDestinationOption'][$i]);
	for ($j=1; $j <= count($OriginDestinationInformation[$i]['OriginDestinationOptions'][$i]['OriginDestinationOption'][$i]) ; $j++) { 
	 echo "<pre>".$OriginDestinationInformation[$j]['OriginDestinationOptions'][$j]['OriginDestinationOption'][$j]['FlightSegment'][$j]['DepartureDateTime']."</pre>";
	}
}
*/


?>
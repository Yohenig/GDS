<?php
error_reporting(E_ALL);

/**Clases necesarias para el desarrollo de los procesos
*/
include_once('../Config/HttpConnection.php');
include_once('../Controller/XmlController.php');
include_once('../Model/Avail.php');


//Obterner información de la vista AVAIL
$availModel = new AvailModel();
$availModel->properties();
$availModel->setDirect($_GET['direct']);
$availModel->setCarrier($_GET['carrier']);
$availModel->setDate($_GET['date']);
$availModel->setDateDest($_GET['dateDest']);
$availModel->setTypeDest($_GET['typeDest']);
$availModel->setSource($_GET['source']);
$availModel->setDest($_GET['dest']);
$availModel->setCabin($_GET['cabin']);
$availModel->setPassengerTypeQuantityAdulto($_GET['adulto']);
$availModel->setPassengerTypeQuantityNino($_GET['nino']);
$availModel->setPassengerTypeQuantity3edad($_GET['mayor']);
$availModel->setPassengerTypeQuantitybebe($_GET['bebe']);
  


//Instancia con el xmlController
$xmlController= new XmlController();

$http = new HttpConnection();
$http->init();
//Llamada al metodo AirAvailRQ para armar el xml
$request=$xmlController->AirAvailRS($availModel);
//Obtener respuesta enviando el xml generado 
$response= $http->post($request);  
$http->close();
//Tranformacion del xml en string
$xml = simplexml_load_string($response);


$OriginDestinationInformation=$xmlController->processAirAvailRQ2($xml,$availModel,$http);

if(count($OriginDestinationInformation)>0){
$OriginDestinationInformation[1]['PassengerTypeQuantityAdulto']=$availModel->getPassengerTypeQuantityAdulto();
$OriginDestinationInformation[1]['PassengerTypeQuantityNino']=$availModel->getPassengerTypeQuantityNino();
$OriginDestinationInformation[1]['PassengerTypeQuantityMayor']=$availModel->getPassengerTypeQuantity3edad();
$OriginDestinationInformation[1]['PassengerTypeQuantityBebe']=$availModel->getPassengerTypeQuantitybebe();
$OriginDestinationInformation[1]['TypeDest']=$availModel->getTypeDest();
}
//echo "<pre>".htmlentities(print_r($OriginDestinationInformation, true)) ."</pre>";
//echo "<pre>".htmlentities(print_r($request, true)) ."</pre>";
//echo "<pre>".htmlentities(print_r($response, true)) ."</pre>";
$xmlController->processView($OriginDestinationInformation,$http );

?>

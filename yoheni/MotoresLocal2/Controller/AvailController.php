<?php
error_reporting(E_ALL);

/**Clases necesarias para el desarrollo de los procesos
*/
include('../Config/HttpConnection.php');
include('../Controller/XmlController.php');
include('../Model/Avail.php');


//Obterner información de la vista AVAIL
$availModel = new AvailModel();
$availModel->setDirect($_GET['direct']);
$availModel->setCarrier($_GET['carrier']);
$availModel->setDate($_GET['date']);
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

$xmlController->processAirAvailRQ($availModel,$xml,$request,$http);


/*//Estableciendo conexion con el WebService
$connect = new ConnectionWebService();
//Iniciando conexión con el WebService
$conn=$connect->StartConnection();
//Instancia con el xmlController
$xmlController= new XmlController();
//Llamada al metodo AirAvailRQ para armar el xml
$request=$xmlController->AirAvailRS($availModel);
//Obtener respuesta enviando el xml generado 
$response = $xmlController->SendXML($request,$conn,0);
//Tranformacion del xml en string
$xml = simplexml_load_string($response);

$xmlController->processAirAvailRQ($availModel,$xml,$request,$conn);

$connect->CloseConnection();*/


?>

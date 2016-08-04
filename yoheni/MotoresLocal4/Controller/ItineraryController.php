<?php
error_reporting(E_ALL);
/**Clases necesarias para el desarrollo de los procesos
*/
include('../Config/HttpConnection.php');
include('../Controller/XmlController.php');
include('../Model/Itinerary.php');

//Obterner información de la vista Booking

$availModel = new AvailModel();

$itineraryModel= new ItineraryModel();
$itineraryModel->setPnr($_GET['pnr']);

  


//Instancia con el xmlController
$xmlController= new XmlController();

$http = new HttpConnection();
$http->init();

//Generar XML---------------------------------------------------------- 
$request=$xmlController->TravelItineraryReadRQ($availModel,$itineraryModel);

//echo "<pre>".htmlentities(print_r($request, true)) ."</pre>";
//Obtener respuesta enviando el xml generado 

$response= $http->post($request);  

//Cerrar Conexión--------------------------------------------------------
$http->close();
//Tranformacion del xml en string----------------------------------------
$xml = simplexml_load_string($response);

$xmlController->processTravelItineraryReadRQ($xml);

//echo "<pre>".htmlentities(print_r($response, true)) ."</pre>";

?>
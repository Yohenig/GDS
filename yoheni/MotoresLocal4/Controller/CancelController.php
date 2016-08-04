<?php
error_reporting(E_ALL);
/**Clases necesarias para el desarrollo de los procesos
*/
include('../Config/HttpConnection.php');
include('../Controller/XmlController.php');
include('../Model/Avail.php');
include('../Model/Cancel.php');

//Obterner información de la vista Booking

$availModel = new AvailModel();

$cancelModel= new CancelModel();
$cancelModel->setPnr($_GET['pnr']);
$cancelModel->setTicket($_GET['ticket']);

  


//Instancia con el xmlController
$xmlController= new XmlController();

$http = new HttpConnection();
$http->init();

//Generar XML---------------------------------------------------------- 
$request=$xmlController->CancelRQ($availModel,$cancelModel);

//echo "<pre>".htmlentities(print_r($request, true)) ."</pre>";
//Obtener respuesta enviando el xml generado 
$response= $http->post($request);  

//Cerrar Conexión--------------------------------------------------------
$http->close();
//Tranformacion del xml en string----------------------------------------
$xml = simplexml_load_string($response);

$xmlController->processCancelRS($xml,$cancelModel);

//echo "<pre>".htmlentities(print_r($response, true)) ."</pre>";

?>
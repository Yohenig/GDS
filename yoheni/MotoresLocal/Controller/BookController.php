<?php
error_reporting(E_ALL);
/**Clases necesarias para el desarrollo de los procesos
*/
include('../Config/HttpConnection.php');
include('../Controller/XmlController.php');
include('../Model/Book.php');

//Obterner información de la vista Booking
$adultos= $_POST["PassengerTypeQuantityAdulto_0"];
$bebe= $_POST["PassengerTypeQuantitybebe_0"];
$nino= $_POST["PassengerTypeQuantityNino_0"];
$type= $_POST["Type_0"];
$pago=$_POST["pago"];

$availModel = new AvailModel();
$availModel->setTypeDest($type);
$PassengerTypeAdulto= array();
$PassengerTypebebe= array();
$PassengerTypeNino= array();
if($adultos > 0){
	for($i=0; $i<$adultos; $i++){
		$bookModel= new BookModel();
		$bookModel->setFirstName($_POST['FirstName'.$i]);
		$bookModel->setLastName($_POST['LastName'.$i]);
		$bookModel->setTelephone($_POST['Telephone'.$i]);
		$bookModel->setEmail($_POST['Email'.$i]);
		$bookModel->setDocumentId($_POST['DocID'.$i]);
		$bookModel->setDocumentType($_POST['DocType'.$i]);
		$PassengerTypeAdulto[$i]=$bookModel;
	}
}
if($bebe > 0){
	for($i=0; $i<$bebe; $i++){
		$bookModel= new BookModel();
		$bookModel->setFirstName($_POST['FirstNameInfante'.$i]);
		$bookModel->setLastName($_POST['LastNameInfante'.$i]);
		$bookModel->setTelephone($_POST['TelephoneInfante'.$i]);
		$bookModel->setEmail($_POST['EmailInfante'.$i]);
		$bookModel->setDocumentId($_POST['DocIDInfante'.$i]);
		$bookModel->setDocumentType($_POST['DocTypeInfante'.$i]);
		$PassengerTypebebe[$i]=$bookModel;
	}
}
if($nino > 0){
	for($i=0; $i<$nino; $i++){
		$bookModel= new BookModel();
		$bookModel->setFirstName($_POST['FirstNameNino'.$i]);
		$bookModel->setLastName($_POST['LastNameNino'.$i]);
		$bookModel->setTelephone($_POST['TelephoneNino'.$i]);
		$bookModel->setEmail($_POST['EmailNino'.$i]);
		$bookModel->setDocumentId($_POST['DocIDNino'.$i]);
		$bookModel->setDocumentType($_POST['DocTypeNino'.$i]);
		$PassengerTypeNino[$i]=$bookModel;
	}
}





//Instancia con el xmlController
$xmlController= new XmlController();

$http = new HttpConnection();
$http->init();

//Generar XML---------------------------------------------------------- 
$request=$xmlController->AirBookRQ($availModel,$PassengerTypeAdulto,$PassengerTypebebe,$PassengerTypeNino);

//echo "<pre>".htmlentities(print_r($request, true)) ."</pre>";
//Obtener respuesta enviando el xml generado 

$response= $http->post($request);  

//Cerrar Conexión--------------------------------------------------------
$http->close();
//Tranformacion del xml en string----------------------------------------
$xml = simplexml_load_string($response);

$xmlController->processAirBookRS($xml, $pago);
//echo "<pre>".htmlentities(print_r($response, true)) ."</pre>";

?>
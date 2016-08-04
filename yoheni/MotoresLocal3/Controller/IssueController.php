<?php
error_reporting(E_ALL);
/**Clases necesarias para el desarrollo de los procesos
*/
include('../Config/HttpConnection.php');
include('../Controller/XmlController.php');
include('../Model/Avail.php');
include('../Model/Issue.php');
 
//Obterner información de la vista Booking

$availModel = new AvailModel();

$issueModel= new IssueModel();
$issueModel->setPaymentType($_GET['PaymentType']);
$issueModel->setBookingID($_GET['BookingID']);  

if($issueModel->getPaymentType()==5){
 	$issueModel->setCreditCardCode($_GET['CreditCardCode']);
 	$issueModel->setCreditCardNumber($_GET['CreditCardNumber']);
 	$issueModel->setCreditSeriesCode($_GET['CreditSeriesCode']);
 	$issueModel->setCreditExpireDate($_GET['CreditExpireDate']);
}
if($issueModel->getPaymentType()==6){
 	$issueModel->setDebitCardCode($_GET['DebitCardCode']);
 	$issueModel->setDebitCardNumber($_GET['DebitCardNumber']);
 	$issueModel->setDebitSeriesCode($_GET['DebitSeriesCode']);
}
if($issueModel->getPaymentType()==34){
 	$issueModel->setInvoiceCode($_GET['InvoiceCode']);
}
if($issueModel->getPaymentType()==37){
 	$issueModel->setMiscellaneousCode($_GET['MiscellaneousCode']);
 	$issueModel->setText($_GET['Text']);
}
$issueModel->setValueAddedTax($_GET['VAT']);  


//Instancia con el xmlController
$xmlController= new XmlController();

$http = new HttpConnection();
$http->init();

//Generar XML---------------------------------------------------------- 
$request=$xmlController->AirDemandTicketRQ($availModel,$issueModel);

//echo "<pre>".htmlentities(print_r($request, true)) ."</pre>";
//Obtener respuesta enviando el xml generado 

$response= $http->post($request);  

//Cerrar Conexión--------------------------------------------------------
$http->close();
//Tranformacion del xml en string----------------------------------------
$xml = simplexml_load_string($response);

$xmlController->processAirDemandTicketRS($xml);

//echo "<pre>".htmlentities(print_r($response, true)) ."</pre>";

?>
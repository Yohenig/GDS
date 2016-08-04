<?php

/**
* Clase que Maneja todas las propiedades para la peticiones referente a AVAIL
*/

require_once('/../Config/propertiesAVAIL.php');

class AvailModel {

	private $EchoToken;
	private $TimeStamp;
	private $SequenceNmbr;
	private $particion;
	private $direct;
	private $maxresponses;
	private $combined;
	private $sine;
	private $device;
	private $carrier;
	private $date;
	private $source;
	private $dest;
	private $cabin;
	private $country;
    private $currency;
    private $city;
    private $cargo;
    private $DepartureAirport;
	private $ArrivalAirport;
	private $FlightNumber;
	private $ResBookDesigCode;
	private $ArrivalDateTime;
	private $MarketingAirline;
	private $DepartureDateTime;
	private $PassengerTypeQuantityAdulto;
	private $PassengerTypeQuantity3edad;
	private $PassengerTypeQuantityNino;
	private $PassengerTypeQuantitybebe;
	private $typeDest;


	function __construct(){
		$properties= new PropertiesAVAIL();
		$properties->_getPropertiesAVAIL();
	 	
	 	$this->EchoToken = ECHOTOKEN;
		$this->TimeStamp = TIMESTAMP;
		$this->SequenceNmbr = SEQUENCENMBR;
		$this->particion=PARTICION;
		$this->direct;
		$this->maxresponses=MAXRESPONSES;
		$this->combined=COMBINED;
		$this->sine=SINE;
		$this->device=DEVICE;
		$this->country=COUNTRY;
   		$this->currency=CURRENCY;
    	$this->city=CITY;
    	$this->cargo=CARGO;
	}

	public function getEchoToken(){
		return $this->EchoToken;
	}
	public function getTimeStamp(){
		return $this->TimeStamp;
	}
	public function getSequenceNmbr(){
		return $this->SequenceNmbr;
	}
	public function getCargo(){
		return $this->cargo;
	}

	public function setParticion($particion){
		$this->particion=$particion;
	}

	public function getParticion(){
		return $this->particion;
	}

	public function setDirect($direct){
		$this->direct=$direct;
	}
	public function getDirect(){
		return $this->direct;
	}
	public function setMaxResponses($maxresponses){
		$this->maxresponses=$maxresponses;
	}
	public function getMaxResponses(){
		return $this->maxresponses;
	}
	public function setCombined($combined){
		$this->combined=$combined;
	}
	public function getCombined(){
		return $this->combined;
	}
	public function setSine($sine){
		$this->sine=$sine;
	}
	public function getSine(){
		return $this->sine;
	}
	public function setDevice($device){
		$this->device=$device;
	}
	public function getDevice(){
		return $this->device;
	}
	public function setCarrier($carrier){
		$this->carrier=$carrier;
	}
	public function getCarrier(){
		return $this->carrier;
	}
	public function setDate($date){
		$this->date=$date;
	}
	public function getDate(){
		return $this->date;
	}
	public function setDateDest($dateDest){
		$this->dateDest=$dateDest;
	}
	public function getDateDest(){
		return $this->dateDest;
	}
	public function setSource($source){
		$this->source=$source;
	}
	public function getSource(){
		return $this->source;
	}
	public function setDest($dest){
		$this->dest=$dest;
	}
	public function getDest(){
		return $this->dest;
	}
	public function setCabin($cabin){
		$this->cabin=$cabin;
	}
	public function getCabin(){
		return $this->cabin;
	}
	
    public function getCountry(){
        return $this->country;
    }
     public function getCurrency(){
        return $this->currency;
    }
      public function getCity(){
        return $this->city;
    }


	public function setDepartureAirport($DepartureAirport){
		$this->DepartureAirport=$DepartureAirport;
	}
	public function getDepartureAirport(){
		return $this->DepartureAirport;
	}

	public function setArrivalAirport($ArrivalAirport){
		$this->ArrivalAirport=$ArrivalAirport;
	}
	public function getArrivalAirport(){
		return $this->ArrivalAirport;
	}

	public function setFlightNumber($FlightNumber){
		$this->FlightNumber=$FlightNumber;
	}
	public function getFlightNumber(){
		return $this->FlightNumber;
	}

	public function setResBookDesigCode($ResBookDesigCode){
		$this->ResBookDesigCode=$ResBookDesigCode;
	}
	public function getResBookDesigCode(){
		return $this->ResBookDesigCode;
	}

	public function setArrivalDateTime($ArrivalDateTime){
		$this->ArrivalDateTime=$ArrivalDateTime;
	}
	public function getArrivalDateTime(){
		return $this->ArrivalDateTime;
	}
	public function setMarketingAirline($MarketingAirline){
		$this->MarketingAirline=$MarketingAirline;
	}
	public function getMarketingAirline(){
		return $this->MarketingAirline;
	}
	public function setDepartureDateTime($DepartureDateTime){
		$this->DepartureDateTime=$DepartureDateTime;
	}
	public function getDepartureDateTime(){
		return $this->DepartureDateTime;
	}
	public function setPassengerTypeQuantityAdulto($PassengerTypeQuantityAdulto){
		$this->PassengerTypeQuantityAdulto=$PassengerTypeQuantityAdulto;
	}

	public function getPassengerTypeQuantityAdulto(){
		return $this->PassengerTypeQuantityAdulto;
	}
	public function setPassengerTypeQuantity3edad($PassengerTypeQuantity3edad){
		$this->PassengerTypeQuantity3edad=$PassengerTypeQuantity3edad;
	}

	public function getPassengerTypeQuantity3edad(){
		return $this->PassengerTypeQuantity3edad;
	}
	public function setPassengerTypeQuantitybebe($PassengerTypeQuantitybebe){
		$this->PassengerTypeQuantitybebe=$PassengerTypeQuantitybebe;
	}

	public function getPassengerTypeQuantitybebe(){
		return $this->PassengerTypeQuantitybebe;
	}
	public function setPassengerTypeQuantityNino($PassengerTypeQuantityNino){
		$this->PassengerTypeQuantityNino=$PassengerTypeQuantityNino;
	}

	public function getPassengerTypeQuantityNino(){
		return $this->PassengerTypeQuantityNino;
	}
	public function setTypeDest($typeDest){
		$this->typeDest=$typeDest;
	}
	public function getTypeDest(){
		return $this->typeDest;
	}
	 

}

?>
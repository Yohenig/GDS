<?php

class IssueModel{

	private $bookingID;
	private $paymentType;
	private $valueAddedTax;

	private $CreditCardCode;
	private $CreditCardNumber;
	private $CreditSeriesCode;
	private $CreditExpireDate;
	
	private $DebitCardCode;
	private $DebitCardNumber;
	private $DebitSeriesCode;

	private $InvoiceCode;
	
	private $MiscellaneousCode;


	public function setBookingID($bookingID){
		$this->bookingID=$bookingID;
	}

	public function getBookingID(){
		return $this->bookingID;
	}
	public function setPaymentType($paymentType){
		$this->paymentType=$paymentType;
	}

	public function getPaymentType(){
		return $this->paymentType;
	}
	public function setValueAddedTax($valueAddedTax){
		$this->valueAddedTax=$valueAddedTax;
	}

	public function getValueAddedTax(){
		return $this->valueAddedTax;
	}


	public function setCreditCardCode($CreditCardCode){
		$this->CreditCardCode=$CreditCardCode;
	}
	public function getCreditCardCode(){
		return $this->CreditCardCode;
	}
	public function setCreditCardNumber($CreditCardNumber){
		$this->CreditCardNumber=$CreditCardNumber;
	}
	public function getCreditCardNumber(){
		return $this->CreditCardNumber;
	}
	public function setCreditSeriesCode($CreditSeriesCode){
		$this->CreditSeriesCode=$CreditSeriesCode;
	}
	public function getCreditSeriesCode(){
		return $this->CreditSeriesCode;
	}
	public function setCreditExpireDate($CreditExpireDate){
		$this->CreditExpireDate=$CreditExpireDate;
	}
	public function getCreditExpireDate(){
		return $this->CreditExpireDate;
	}

	public function setDebitCardCode($DebitCardCode){
		$this->DebitCardCode=$DebitCardCode;
	}
	public function getDebitCardCode(){
		return $this->DebitCardCode;
	}
	public function setDebitCardNumber($DebitCardNumber){
		$this->DebitCardNumber=$DebitCardNumber;
	}
	public function getDebitCardNumber(){
		return $this->DebitCardNumber;
	}
	public function setDebitSeriesCode($DebitSeriesCode){
		$this->DebitSeriesCode=$DebitSeriesCode;
	}
	public function getDebitSeriesCode(){
		return $this->DebitSeriesCode;
	}

	public function setInvoiceCode($InvoiceCode){
		$this->InvoiceCode=$InvoiceCode;
	}
	public function getInvoiceCode(){
		return $this->InvoiceCode;
	}

	public function setMiscellaneousCode($MiscellaneousCode){
		$this->MiscellaneousCode=$MiscellaneousCode;
	}
	public function getMiscellaneousCode(){
		return $this->MiscellaneousCode;
	}


}


?>
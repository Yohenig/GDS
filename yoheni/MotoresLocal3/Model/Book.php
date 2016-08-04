<?php
	class BookModel{

		private $firstName;
		private $lastName;
		private $telephone;
		private $email;
		private $documentId;
		private $documentType;
		private $segments;


	
	public function setSegments($segments){
		$this->segments=$segments;
	}

	public function getSegments(){
		return $this->segments;
	} 

	public function setFirstName($firstName){
		$this->firstName=$firstName;
	}

	public function getFirstName(){
		return $this->firstName;
	} 

	public function setLastName($lastName){
		$this->lastName=$lastName;
	}

	public function getLastName(){
		return $this->lastName;
	}

	public function setTelephone($telephone){
		$this->telephone=$telephone;
	}

	public function getTelephone(){
		return $this->telephone;
	} 

	public function setEmail($email){
		$this->email=$email;
	}

	public function getEmail(){
		return $this->email;
	} 

	public function setDocumentId($documentId){
		$this->documentId=$documentId;
	}

	public function getDocumentId(){
		return $this->documentId;
	}
	public function setDocumentType($documentType){
		$this->documentType=$documentType;
	}

	public function getDocumentType(){
		return $this->documentType;
	}
 

	}

?>

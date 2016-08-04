<?php

class PriceModel{

	private $impTasas;
	private $cargos;
	private $BaseFare;
	private $TotalFare;
	private $BaseFareAdulto;
	private $BaseFareNino;
	private $BaseFareBebe;
	private $Error;

	public function setImpTasas($impTasas){
		$this->impTasas=$impTasas;
	}

	public function getImpTasas(){
		return $this->impTasas;
	}

	public function setCargos($cargos){
		$this->cargos=$cargos;
	}

	public function getCargos(){
		return $this->cargos;
	}


	public function setBaseFare($BaseFare){
		$this->BaseFare=$BaseFare;
	}

	public function getBaseFare(){
		return $this->BaseFare;
	}
	public function setTotalFare($TotalFare){
		$this->TotalFare=$TotalFare;
	}

	public function getTotalFare(){
		return $this->TotalFare;
	}
	public function setBaseFareAdulto($BaseFareAdulto){
		$this->BaseFareAdulto=$BaseFareAdulto;
	}

	public function getBaseFareAdulto(){
		return $this->BaseFareAdulto;
	}
	public function setBaseFareBebe($BaseFareBebe){
		$this->BaseFareBebe=$BaseFareBebe;
	}

	public function getBaseFareBebe(){
		return $this->BaseFareBebe;
	}

public function setBaseFareNino($BaseFareNino){
		$this->BaseFareNino=$BaseFareNino;
	}

	public function getBaseFareNino(){
		return $this->BaseFareNino;
	}
	public function setError($Error){
		$this->Error=$Error;
	}

	public function getError(){
		return $this->Error;
	}




}



?>
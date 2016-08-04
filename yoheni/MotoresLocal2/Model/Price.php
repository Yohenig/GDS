<?php

class PriceModel{

	private $impTasas;
	private $cargos;
	private $BaseFare;
	private $TotalFare;


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




}



?>
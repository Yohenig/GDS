<?php

class CancelModel{
	
	private $pnr;
	private $ticket;


	public function setPnr($pnr){
		$this->pnr=$pnr;
	}
	public function getPnr(){
		return $this->pnr;
	}
	public function setTicket($ticket){
		$this->ticket=$ticket;
	}
	public function getTicket(){
		return $this->ticket;
	}

}





?>
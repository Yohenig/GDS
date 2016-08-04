<?php

error_reporting(E_ALL);

class ConnectionWebService{

 
    private $conn;	


	public function StartConnection() {
		$this->conn=null;
		
		//Aperturando connection con CURL cURL te permite crear conexiones con distintos protocolos bajo distintos SO 
		//para poder conectar tus aplicaciones con otros entornos, como el web, Active Directory, etc
		$this->conn = curl_init();
		
		//set number of POST vars, POST data
		curl_setopt($this->conn,CURLOPT_POST, 1);
		curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->conn, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->conn, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt($this->conn, CURLOPT_HTTPHEADER, array(
			'Connection: Keep-Alive',
			'Keep-Alive: 300'
		));
			
		//Manejo de Errores en la conexión...	
		if (curl_errno($this->conn)) throw new Exception(curl_error($this->conn));
		
		return $this->conn;
		
	}

	//-----------------------------------------------------------------------------------------------------------------


	//Metodo para el cierre de conexiones aperturadas------------------------------------------------------------------
	function CloseConnection() {
		//Cierre de Conexión
		curl_close($this->conn);
	}

	//-----------------------------------------------------------------------------------------------------------------

}

?>
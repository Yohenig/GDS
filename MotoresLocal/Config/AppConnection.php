<?php

include_once('propertiesConnection.php');

	class Conexion{

		//variables necesarias para la conexion--------------------------

		private $server = SERVER_APP;
    	private $user = USER_APP;
   		private $pass = PASSWORD_APP;
    	private $data_base = DATABASE_APP;
    	private $conn=null;
        private $st=null;

		//----------------------------------------------------------------
               

		//Funcion para conectar y selecciona la base de datos ------------

		public function connect(){
			/*Se le asigna a la variable conexion el resultado de la la funcion mysql_connect la cual sirve para
			establecer la conexion con el paso de los parametros del servidor, el usuario y la clave.
            		La funcion or die sirve para atrapar algun error e imprime lo que usted le coloque entre los parentesis.*/
			
			$this->conn = @mysql_connect($this->server,$this->user,$this->pass) or die("No se puede establecer la conexion..");

			/*la variable mysql_select_db sirve para seleccionar la base de datos pasandole la misma y la variable 
 			de conexion que anteriormente obtuvo el valor. */

			@mysql_select_db($this->data_base,$this->conn)or die("No se puedo seleccionar la base de datos");
        			 
			 

	        }

		//Funcion para ejecutar las sentencias sql

		public function ejecutar($sql){

			/*Se le asigna a la variable st declarada lo que retorna la funcion mysql_query a la cual se le pasa la 
			variable sql y la conexion que esta en la variable conn.*/

			$this->st= @mysql_query($sql, $this->conn);

			//Retornas el valor de la consulta
			return $this->st;
		}

    

		//Permite cerrar las conexiones.

		public function close(){

			//Se valida si existe alguna conexion abierta y la cierra
        		if($this->conn){
            		@mysql_close($this->conn);
        		}
   	 	
		}

	}

?>
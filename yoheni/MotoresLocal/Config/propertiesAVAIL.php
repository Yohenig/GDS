<?php

include_once('AppConnection.php');

class PropertiesAVAIL{


    public function _getPropertiesAVAIL(){

        define('TIMESTAMP',date("c"));

        $con= new Conexion(); //Se instancia la conexion
        $con->connect();      //Convova la funcion de conectar a la base de datos
 
        //Sentencia sql para registrar
        $sql="select * from  gds_kiu_properties_avail";
         
        //Se invova la funcion ejecutar que esta en la clase conexion, para ejecutar las sentencia sql.
        $result= $con->ejecutar($sql);

        while($row=mysql_fetch_array($result)){  
          
            define('ECHOTOKEN',$row['echotoken']);
            define('PARTICION',$row['particion']);
            define('SEQUENCENMBR',$row['sequencenmbr']);
            define('DEVICE',$row['device']);
            define('SINE',$row['sine']);
            define('MAXRESPONSES',$row['maxresponses']);
            define('COMBINED',$row['combined']);
            define('CURRENCY',$row['currency']);
            define('COUNTRY',$row['country']);
            define('CITY',$row['city']);
            define('CARGO',$row['cargo']);
        }

        $con->close();

    }
     public function _getAirline($iata){

        $con= new Conexion(); //Se instancia la conexion
        $con->connect();      //Convova la funcion de conectar a la base de datos
 
        //Sentencia sql para registrar
        $sql="select nombre from  gds_airlines where codigo='".$iata."'";
         
        //Se invova la funcion ejecutar que esta en la clase conexion, para ejecutar las sentencia sql.
        $result= $con->ejecutar($sql);
        
        if ($row=mysql_fetch_array($result)){ 
            $result2= $row['nombre'];
        }

        $con->close();
        
        return $result2;

    }
    
    public function _getDate($date){
        setlocale(LC_TIME, 'es_ES.UTF-8');

        $date1 = gmmktime(0,0,0,substr($date,5,2),substr($date,8,2),substr($date,0,4));
        $date_f= strftime("%d de %b de %Y", $date1);
        
        return $date_f;

    }
    public function _getCity($city){

        $con= new Conexion(); //Se instancia la conexion
        $con->connect();      //Convova la funcion de conectar a la base de datos
 
        //Sentencia sql para registrar
        $sql="select city from  qro_cp_cities where airportCode='".$city."'";
         
        //Se invova la funcion ejecutar que esta en la clase conexion, para ejecutar las sentencia sql.
        $result= $con->ejecutar($sql);
        
        if ($row=mysql_fetch_array($result)){ 
            $result2= $row['city'];
        }

        $con->close();
        
        return $result2;

    }

}

?>
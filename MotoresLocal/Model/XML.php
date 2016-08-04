<?php

require('/../Config/propertiesXML.php');

class XMLModel{


	//Variables de la entidad-------------------------------------------------------
    private $servers=array('https://ssl00.kiusys.com/ws3/',
                'https://n10.kiusys.com/ws3/',
                'https://n30.kiusys.com/ws3/',
                'https://n40.kiusys.com/ws3/',
               'https://webservices-us.kiusys.com/ws3/');
    //Usuario de KIU
    private $user=USER;
    //Clave asignada al Usuario de KIU
    private $password=PASSWORD;
    //Codigo del Tour
    private $tourCode=TOURCODE;
    //Codigo de la Compañia
    private $CompanyCode=COMPANYCODE;


    //------------------------------------------------------------------------------
    

    //Manejo de Gettes y Setter de las variables -----------------------------------

    public function getServers(){
    	return $this->servers;
    }
    
    public function getUser(){
    	return $this->user;
    }

    public function getPassword(){
    	return $this->password;
    }

     public function gettourCode(){
        return $this->tourCode;
    }
     public function getCompanyCode(){
        return $this->CompanyCode;
    }

    //-------------------------------------------------------------------------------

}


?>
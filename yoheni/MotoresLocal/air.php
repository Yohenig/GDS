<?php
/**
 *
 */
defined('_JEXEC') or die;

/**
 * @subpackage	com_content
 */
class AawsControllerAir extends AawsController
{

    /**
     * @brief Procesa los datos y construye el XML para consumer el 
     * servicio de disponibilidad; retornando el XML de la disponibilidad
     * @param $data array Corresponde a los datos necesario para obtener disponibilidad
     * Campos usados en el arreglo:
     *   TRIP_TYPE           => Tipo de viaje, debe ser R, O o M
     *   B_LOCATION_n        => Codigo IATA de la ciudad de origen, ´n´ indica el segmento
     *                          en el que se utiliza, iniciando en 1
     *   E_LOCATION_n        => Codigo IATA de la ciudad de destino, ´n´ indica el segmento
     *                          en el que se utiliza, iniciando en 1
     *   B_DATE_n            => Fecha con el siguiente formato ´YYYY-MM-DD[THH:MM]´, indicando
     *                          la fecha y hora de salida. ´n´ indica el segmento en el que se
     *                          utiliza, iniciando en 1
     *   TRAVELLER_TYPE_ADT  => Indica el número de pasajeros tipo adulto
     *   TRAVELLER_TYPE_CHD  => Indica el número de pasajeros tipo niño
     *   TRAVELLER_TYPE_INF  => Indica el número de pasajeros tipo infante
     *   CABIN               => Tipo de cabina, debe ser Economy, First y Business
     *   AIRLINE             => Codigo IATA de la aerolinea preferida
     *   MAX_CONNECTIONS     => Número de conexiones para mostrar en la disponibilidad
     *   @return string XML que contiene el mensaje OTA de disponibilidad
     */
    public function processData($data)
    {
        if(!isset($data['TRIP_TYPE']) ||
           !isset($data['B_LOCATION_1']) ||
           !isset($data['E_LOCATION_1']) ||
           !isset($data['B_DATE_1']) ||
           !isset($data['TRAVELLER_TYPE_ADT']))
        {
           throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));
        }

         if(!in_array($data['TRIP_TYPE'], array('R', 'O', 'M')))
         {
             throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));
         }

         $filter = JFilterInput::getInstance();
         $info   = array(
             'origindestination' => array(),
             'travelertypes'     => array(),
             'preferences'       => array()
         );

         // Obteneiendo el itinerario
         $i     = 1;
         $index = '_1';
         $prevD = '';
         $date  = '';

         for(;;)
         {
             if( !isset($data['B_LOCATION'.$index]) ||
                 !isset($data['E_LOCATION'.$index]) ||
                 !isset($data['B_DATE'.$index]))
                 break;

             $date  = $tmpD = $filter->clean($data['B_DATE'.$index], 'string');
             $date  = $this->_getDate($date, $prevD);
             $prevD = $tmpD;

             $info['origindestination'][] = array(
                 'origin'      => substr($filter->clean($data['B_LOCATION'.$index], 'word'), 0, 7),
                 'destination' => substr($filter->clean($data['E_LOCATION'.$index], 'word'), 0, 7),
                 'datetime'    => $date,
                 'useHours'    => false
             );

             if($data['TRIP_TYPE'] == 'R' && $i == 1)
             {
                 $date  = $this->_getDate($filter->clean($data['B_DATE_2'], 'string'), $prevD);
                 $info['origindestination'][] = array(
                     'origin'      => substr($filter->clean($data['E_LOCATION'.$index], 'word'), 0, 7),
                     'destination' => substr($filter->clean($data['B_LOCATION'.$index], 'word'), 0, 7),
                     'datetime'    => $date,
                     'useHours'    => false
                 );

                 break;
             }

             if($data['TRIP_TYPE'] == 'O' && $i == 1)
                 break;

             if($data['TRIP_TYPE'] == 'M' && $i >= 6)
                 break;

             $index = '_'.++$i;
             $prevD = $date;
         }

         if(!count($info['origindestination']))
         {
             throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));
         }

         //Obteniendo los pasajeros
         $passengers = 0;
         foreach(array('ADT', 'CHD', 'INF','YCD') as $type)
         {
             $info['travelertypes'][$type] = $filter->clean($data['TRAVELLER_TYPE_'.$type], 'int');
             $passengers += $type != 'INF' ? $info['travelertypes'][$type] : 0;
         }

         if($passengers > 9 || $info['travelertypes']['ADT'] < $info['travelertypes']['INF'])
         {
             throw new Exception('COM_AAWS_PASSENGER_COUNT_ERROR');
         }

         //Obteniendo las preferencias del viajero
         // Clase
         if(isset($data['CABIN']))
         {
             $info['preferences']['cabin'] = $filter->clean($data['CABIN'], 'word');
         }

         // Aerolinea
         if(isset($data['AIRLINE']))
         {
             //$info['preferences']['airline'] = $filter->clean($data['AIRLINE'], 'word');
             $info['preferences']['airline'] =  $data['AIRLINE'];
         }

         // Conexiones
         if(isset($data['MAX_CONNECTIONS']))
         {
             $info['preferences']['connections'] = $filter->clean($data['MAX_CONNECTIONS'], 'string');
         }

         $params = JComponentHelper::getParams('com_aaws');
         $this->setWebService($params->get('air_service', ''));

         $objRequest          = new stdClass();
         $objRequest->request = $this->getXmlAvailabilityRequest($info);

         return $this->request('GetResponse', $objRequest);
    }

    /**
     * @brief Método que obtiene la disponbilidad y la presenta en pantalla
     *
     * Este método recibe por POST un arreglo con la información para consultar
     * la disponibilidad. Los datos de entrada deben estar dentro de la variable $_POST,
     * en la posición ´wsform´ y debe cumplir un formato igual al especificado como
     * parámetro de entrada ($data) de la función ´processData´, además se debe incluir
     * la posición ´ajax´ la cual indica si la respuesta debe ser Ajax (1)(para mostrar
     * el cargando) o debe ser directa (0).
     */
	public function availability()
	{
		
        try
        {
            $data = JRequest::getVar('wsform', array(), 'post');
            //Lineas adicionadas para presentar la disponibilidad haciendo uso del modulo de vuelos en oferta
            if(!$data['B_DATE_1'] && !$data['B_DATE_2'] && !$data['B_LOCATION_1'] && !$data['E_LOCATION_1']):
                if(JRequest::getVar('B_DATE_1')!='' && JRequest::getVar('B_DATE_2')!='' && JRequest::getVar('B_LOCATION_1')!='' && JRequest::getVar('E_LOCATION_1')!=''):
                  $data['TRIP_TYPE'] = JRequest::getVar('TRIP_TYPE');
                  $data['B_LOCATION_1'] = JRequest::getVar('B_LOCATION_1');
                  $data['E_LOCATION_1'] = JRequest::getVar('E_LOCATION_1');
                  $data['B_DATE_1'] = JRequest::getVar('B_DATE_1');
                  $data['B_DATE_2'] = JRequest::getVar('B_DATE_2');
                  $data['TRAVELLER_TYPE_ADT'] = JRequest::getVar('TRAVELLER_TYPE_ADT');
                  $data['CABIN'] = JRequest::getVar('CABIN');
                  $data['TRAVELLER_TYPE_YCD'] = JRequest::getVar('TRAVELLER_TYPE_YCD');
                  $data['TRAVELLER_TYPE_CHD'] = JRequest::getVar('TRAVELLER_TYPE_CHD');
                  $data['TRAVELLER_TYPE_INF'] = JRequest::getVar('TRAVELLER_TYPE_INF');
                  $data['AIRLINE'] = JRequest::getVar('AIRLINE');
                  $data['MAX_CONNECTIONS']= JRequest::getVar('MAX_CONNECTIONS');
                  $data['ajax']= JRequest::getVar('ajax');
                else:
                  $data=array();
                endif;
            endif;
			
            // Se valida que se tenga la informacion necesaria para mostrar disponibilidad
            if(!isset($data['TRIP_TYPE']) ||
               !isset($data['B_LOCATION_1']) ||
               !isset($data['E_LOCATION_1']) ||
               !isset($data['B_DATE_1']) ||
               !isset($data['TRAVELLER_TYPE_ADT']))
                throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));

            if(!in_array($data['TRIP_TYPE'], array('R', 'O', 'M')))
                throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));

            // Se obtienen los nombres de los aeropuertos y ciudades para colocar
            // en el QS. Esto se almacena en un campo `extra`
            $i        = 1;
            $index    = '_1';
            $airports = array();
            for(;;)
            {
                if( !isset($data['B_LOCATION'.$index]) ||
                    !isset($data['E_LOCATION'.$index]))
                    break;

                if(!isset($airports[$data['B_LOCATION'.$index]]))
                {
                    $airports[$data['B_LOCATION'.$index]] = $this->_getCityName($data['B_LOCATION'.$index]);
                }

                if(!isset($airports[$data['E_LOCATION'.$index]]))
                {
                    $airports[$data['E_LOCATION'.$index]] = $this->_getCityName($data['E_LOCATION'.$index]);
                }

                $index = '_' . ++$i;
            }

            $data['extra'] = implode(';', $airports);

            // Ahora se agrega la informacion de la aerolinea al mismo campo
            if(isset($data['AIRLINE']) && $data['AIRLINE'] != '')
            {
                $data['extra'] .= ';'.$this->_getAirlineName($data['AIRLINE']);
            }


            // Si la peticion es marcada sin ajax, se muestra una vista de cargando, mientras se vuelve a realizar
            // la misma peticion pero con ajax
            if(isset($data['ajax']) && $data['ajax'] == 0)
            {
                $data['ajax'] = 1;

                JRequest::setVar('view', 'availability');
                $view = $this->getView('availability', 'html');
                $view->assign('data', array('wsform' => $data));
                parent::display();
                return true;
            }

            $response = $this->processData($data);

            $this->renderXSLT($response, 'Availability');

            if(isset($data['ajax']) && $data['ajax'] == 1)
            {
                $app = JFactory::getApplication();
                $app->close();
            }
        }
        catch(Exception $e)
        {
            $lang  = JFactory::getLanguage();
            $error = property_exists($e, 'faultcode') ? preg_replace('/^s:(\d*)00$/', '$1', $e->faultcode) : 'Test 1';
            
            // Se personaliza el mensaje de error, en caso que exista una traduccion
            if($error != '' && $lang->hasKey('AIR_ERROR_'.$error))
            {
                $error = JText::_('AIR_ERROR_'.$error);
            }
            else
            {
                $error = $e->getMessage();
            }

            if(isset($data['ajax']) && $data['ajax'] == 1)
            {
                echo '<div class="error">'.$error.'</div>';
                $app = JFactory::getApplication();
                $app->close();
            }
            else
            {
                $app = JFactory::getApplication();
                $app->redirect('index.php', $error, 'error');
            }
        }
	}

    public function passenger()
    {
        try
        {
            $info = array();

            $data = JRequest::getVar('wsform', array(), 'post');

            if(!isset($data['recommendation']) || $data['recommendation'] == '')
                throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));

            if(!isset($data['correlation']) || $data['correlation'] == '')
                throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));

            if(!isset($data['sequence']) || $data['sequence'] == '')
                throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));

            // Si se hace el ingreso en la pantalla de de captura de datos de pasajeros,
            // se valida el usuario
            $user =& JFactory::getUser();
            if(isset($data['contactemail']) && isset($data['contactpassword']) && $user->id == 0)
                $this->_login();


            // Si la peticion es marcada sin ajax, se muestra una vista de cargando, mientras se vuelve a realizar
            // la misma peticion pero con ajax
            if(isset($data['ajax']) && $data['ajax'] == 0)
            {
                $data['ajax'] = 1;
                $information  = JRequest::getVar('info', array(), 'post');

                JRequest::setVar('view', 'passenger');
                $view = $this->getView('passenger', 'html');

                require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');

                $view->assign('data', array('wsform' => $data,
                                            'info'   => $information,
                                            'url'    => JRoute::_(AawsHelperRoute::getFlowRoute('air.passenger'))
                                      ));
                parent::display();
                return true;
            }

            $info['FareCode']      = $data['recommendation'];
            $info['CorrelationID'] = $data['correlation'];
            $info['SequenceNmbr']  = $data['sequence'];

            $i    = 0;
            $rphs = array();
            for(;;)
            {
                if(!isset($data['fo-'.$i]))
                    break;

                if(!preg_match('/^\d+$/', $data['fo-'.$i]))
                    throw new Exception(JText::_('COM_AAWS_DATA_ERROR'));

                $rphs[] = $data['fo-'.$i];
                $i++;
            }

            $info['rphs'] = join(' ', $rphs);
            $info['code'] = isset($data['code']) && $data['code'] != '' ? $data['code'] : 'PE';

            $params = JComponentHelper::getParams('com_aaws');
            $this->setWebService($params->get('air_service', ''));

            $objRequest          = new stdClass();
            $objRequest->request = $this->getXmlDetailRequest($info);
            $response = $this->request('GetResponse', $objRequest);

            //==== Variable de Sesion Donde se Guarda el XML de Respuesta usado para Evaluar el Churning 
            $session =& JFactory::getSession();
            $session->set('RespuestaInfo', $response);
            //===============================================================

            // Si se presenta el parametro code, quiere decir que es una peticion
            // para alguna restriccion de la tarifa, en caso que no, es la peticion
            // del formulario de pasajeros.
            $info = array();
           $xml  = new DOMDocument();
           $xml->loadXML($response);

           $xpath = new DOMXPath($xml);
           $xpath->registerNameSpace('ns0', 'http://www.opentravel.org/OTA/2003/05');
            if(isset($data['code']) && $data['code'] != '')
            {
                $results = $xpath->query('/ns0:OTA_AirLowFareSearchRS/@SequenceNmbr');
                foreach($results as $result)
                {
                    $info['SequenceNmbr'] = $result->nodeValue;
                }
                
                echo '<script type="text/javascript">';
                echo 'jQuery( document ).ready(function() {';
                echo 'jQuery("#tsequence").val(' . $info['SequenceNmbr'] . ');';
                echo '});';
                echo '</script>';


                $notesStarts = strpos($response, '<Notes>') + 7;
                $response = substr($response, $notesStarts, strpos($response, '</Notes>') - $notesStarts);
                echo '<pre>'.implode("\n", array_slice(explode("\n", $response), 3)).'</pre>';
            }
            else
            {
                $this->renderXSLT($response, 'Detail');
            }

            if(isset($data['ajax']) && $data['ajax'] == 1)
            {
                $app = JFactory::getApplication();
                $app->close();
            }
        }
        catch(Exception $e)
        {
            $lang  = JFactory::getLanguage();
            $error = property_exists($e, 'faultcode') ? preg_replace('/^s:(\d*)00$/', '$1', $e->faultcode) : '';

            // Se personaliza el mensaje de error, en caso que exista una traduccion
            if($error != '' && $lang->hasKey('AIR_ERROR_'.$error))
            {
                $error = JText::_('AIR_ERROR_'.$error);
            }
            else
            {
                $error = $e->getMessage();
            }

            if(isset($data['ajax']) && $data['ajax'] == 1)
            {
                echo '<div class="error">'.$error.'</div>';
                $app = JFactory::getApplication();
                $app->close();
            }
            else
            {
                $app = JFactory::getApplication();
                $app->redirect('index.php', $error, 'error');
            }
        }

    }

    public function booking()
    {
        //================================================================================
        $session =& JFactory::getSession();
        $respuesta = $session->get('RespuestaInfo');
        
        $objeto = simplexml_load_string($respuesta);
        $i = 0;
        foreach($objeto->PricedItineraries->PricedItinerary->AirItinerary->OriginDestinationOptions->OriginDestinationOption AS $destinos){
            $arrayPas['NumVuelo'][$i]   = (int)$destinos->FlightSegment['FlightNumber'];
            $arrayPas['AirVuelo'][$i]   = (string)$destinos->FlightSegment->OperatingAirline['Code'];
            $arrayPas['FechaVuelo'][$i] = (string)substr($destinos->FlightSegment['DepartureDateTime'], 0, 10);
            $i++;
        }
        //================================================================================

        try
        {
            // Se valida si el usuario esta registrado, para colocar ese correo de contacto
            $user = JFactory::getUser();
            if($user->get('id', 0) != 0)
            {
                $_POST['wsform']['contactemail']        = $user->get('email', '');
                $_POST['wsform']['contactname']         = $user->get('name', '');
                $_POST['wsform']['contactdocumenttype'] = $user->get('profile.documenttype', '');
                $_POST['wsform']['contactdocumentid']   = $user->get('profile.documentnumber', '');
            }


            $info = array();

            $data = JRequest::getVar('wsform', array(), 'post');
 
            if( !isset($data['tcorrelation']) || $data['tcorrelation'] == '' ||
                !isset($data['tsequence'])    || $data['tsequence']    == '' ||
                !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/', $data['lastday']))
            {
                throw new Exception(JText::_('COM_AAWS_DATA_ERROR'), 0);
            }

            if( !isset($data['paxtype'])       || !is_array($data['paxtype'])     || !count($data['paxtype'])     ||
                !isset($data['paxlastname'])   || !is_array($data['paxlastname']) || !count($data['paxlastname']) ||
                !isset($data['paymentmethod']) || $data['paymentmethod'] == '')
            {
                throw new Exception(JText::_('COM_AAWS_PAX_DATA_ERROR').'{#}', 1);
            }
 
       		//Validacion fecha tarjeta de credito
       		 
            if($data['carddate-month'][0]!=''){
            	 
            	if($data['carddate-month'][0]<=date('m') && $data['carddate-year'][0]<=date('Y')){
            		throw new Exception(JText::_('COM_AAWS_CONTACT_MONTH_ERROR').'{#carddate-month}',1);
            	}
            }
       		
            
            if(preg_match('/[°|!"#$%&\/()=+*~[\]{}^`;,:.]/', $data['contactname']))
            {
                throw new Exception(JText::_('COM_AAWS_CONTACT_NAME_ERROR').'{#contactname}', 1);
            }

            // Se valida que el correo sea valido
            if(!JMailHelper::isEmailAddress($data['contactemail']))
            {
                throw new Exception(JText::_('COM_AAWS_CONTACT_EMAIL_ERROR').'{#contactemail}', 1);
            }

            /*$phone  = '+ ('.trim($data['contactphonecountry']).') ';
            $phone .= trim($data['contactphonecode']).' ';
            $phone .= trim($data['contactphonenumber']);*/
            $phone = trim($data['contactphonenumber']);

            $types = array_count_values($data['paxtype']);
    
            if(isset($types['INF']))
            {
                if($types['INF'] > $types['ADT'])
                    throw new Exception(JText::_('COM_AAWS_PAX_INFANT_ERROR').'{#}', 1);
                else
                    $infants = $types['INF'];
            }
            else
                $infants = 0;

            $info['SequenceNmbr']  = $data['tsequence'];
            $info['CorrelationID'] = $data['tcorrelation'];
            $info['ContactPhone']  = $phone;
            $info['ContactMail']   = $data['contactemail'];

            // Fecha del ultimo vuelo, para validar las edades de los pasajeros
            $endDate = explode('T', $data['lastday']);
            $endDate = $endDate[0];

            $passengers = array();
            
            $i=0;
            foreach($data['paxtype'] as $key => $value)
            {
                if($data['paxemail'] !='' &&$data['paxemailconf']!=''){	
                    if( $data['paxemail'] != $data['paxemailconf'] )
                    {
                        throw new Exception(JText::_('COM_AAWS_EMAIL_DATA_ERROR').'{#paxemailconf_'.($key + 1).'}', 1);
                    }
                }
            	
                if(!in_array($value, array('ADT', 'CHD', 'INF','YCD')))
                {
                    throw new Exception(JText::_('COM_AAWS_PAX_TYPE_ERROR').'{#_'.($key + 1).'}', 1);
                }

                if(preg_match('/[0-9°|!"#$%&\/()=+*~[\]{}^`;,:.]/', $data['paxfirstname'][$key]))
                {
                    throw new Exception(JText::_('COM_AAWS_PAX_FIRSTNAME_ERROR').'{#paxfirstname_'.($key + 1).'}', 1);
                }

                if(preg_match('/[0-9°|!"#$%&\/()=+*~[\]{}^`;,:.]/', $data['paxlastname'][$key]))
                {
                    throw new Exception(JText::_('COM_AAWS_PAX_LASTNAME_ERROR').'{#paxlastname_'.($key + 1).'}', 1);
                }
			/*
                if(!preg_match('/^[A_Za-z0-9]+$/', $data['paxdocumentnumber'][$key]))
                {
                    throw new Exception(JText::_('COM_AAWS_PAX_DOCUMENTNUMBER_ERROR').'{#paxdocumentnumber_'.($key + 1).'}', 1);
                }
*/
                $day   = (int)$data['paxborndate-day'][$key];
                $month = (int)$data['paxborndate-month'][$key];
                $year  = (int)$data['paxborndate-year'][$key];

                $date1 = sprintf('%04d', $year).'-'.sprintf('%02d', $month).'-'.sprintf('%02d', $day);
                $date2 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));

                if($date1 != $date2)
                    throw new Exception(JText::_('COM_AAWS_DATE_FORMAT_ERROR').'{#paxborndate-day_'.($key + 1).'}', 1);

                $diff = strtotime($endDate) - strtotime($date1);

                /**
                 * La información de la edad se valida de acuerdo a un año estandar, por consiguiente
                 * al no tomar años bisiestos, el desface de la edad se puede presentar en 2 o 3 dias;
                 * es decir es probable que la persona ya halla cmumplido la edad, pero 2 o 3 dias antes
                 */
              
                switch($value)
                {
                case 'YCD':
                	 
                    if($diff / 31536000 <= 60 ){
 					throw new Exception(JText::_('COM_AAWS_YCD_BIRTHDATE').'{#paxborndate-year_'.($key + 1).'}', 1);
                    	
                    }
                    break;	
                case 'ADT':
                    if($diff / 31536000 <= 12 || $diff / 31536000 > 60 ){
                        throw new Exception(JText::_('COM_AAWS_ADT_BIRTHDATE').'{#paxborndate-year_'.($key + 1).'}', 1);
                    }
                    break;
                case 'CHD':
                    if($diff / 31536000 <= 2 || $diff / 31536000 > 12)
                        throw new Exception(JText::_('COM_AAWS_CHD_BIRTHDATE').'{#paxborndate-year_'.($key + 1).'}', 1);
                    break;
                case 'INF':
                    if($diff / 31536000 > 2)
                        throw new Exception(JText::_('COM_AAWS_INF_BIRTHDATE').'{#paxborndate-year_'.($key + 1).'}', 1);
                    break;
                }

                $nationality = $data['paxnationality'][$key];
                $lang        = JFactory::getLanguage(); 
                $lang        = substr($lang->getTag(), 3);
                $db          = JFactory::getDBO();
                $stmt        = $db->getQuery(true);

                // Select que me indica si el codigo del pais es válido
                $stmt->select('COUNT(1)');
                $stmt->from('#__qs_countries AS c');
                $stmt->where('c.codigo = '.$db->Quote($nationality));
                $stmt->where('c.lenguaje = '.$db->Quote($lang));

                $db->setQuery($stmt);
                if(!$db->loadResult())
                    throw new Exception(JText::_('COM_AAWS_NATIONALITY_ERROR').'{#paxnationality_'.($key + 1).'}', 1);

                /**
                 * Tipos de Documentos
                 *
                 *  5 National identity document
                 *  4 Foreing Document
                 *  2 Passport
                 *  1 Visa
                 *  20 Crew member certificate
                 *  21 Passport card
                 *  15 Redress number
                 *  16 Known traveler number
                 *   
                 */
                $documenttype = $data['paxdocumenttype'][$key];
                if(!in_array($documenttype, array('1', '2','3','4', '5', '15', '16', '20', '21')))
                    throw new Exception(JText::_('COM_AAWS_DOCUMENTTYPE_ERROR').'{#paxdocumenttype_'.($key + 1).'}', 1);

                $paxinfo = array();
                $paxinfo['Type']         = $value;
                $paxinfo['Treatment']    = $data['paxtreatment'][$key];
                $paxinfo['BirthDate']    = $date1;
                $paxinfo['FirstName']    = $data['paxfirstname'][$key];
                $paxinfo['LastName']     = $data['paxlastname'][$key];
                $paxinfo['Gender']       = $data['paxgender'][$key];
                $paxinfo['DocumentType'] = $documenttype;
                $paxinfo['Document']     = $data['paxdocumentnumber'][$key];
                $paxinfo['Address']     = $data['paxfiscal'][$key]; //js
                $paxinfo['Phone']     = $data['paxphone'][$key]; //js
                $paxinfo['Nationality']  = $nationality;
				$paxinfo['Airline']  	 = $data['airline'][$key];
				$paxinfo['FrecuentPassenger']  = $data['frecuentpassenger'][$key];

                //===================================================================
                $arrayPas['Pass'][$i]['TipDoc']   = $documenttype;
                $arrayPas['Pass'][$i]['NumDoc']   = $data['paxdocumentnumber'][$key];
                $arrayPas['Pass'][$i]['TipoPas']  = $value;
                $arrayPas['Pass'][$i]['Name']     = $data['paxfirstname'][$key];
                $arrayPas['Pass'][$i]['LastName'] = $data['paxlastname'][$key];
                //===================================================================


				if($value=='ADT'){
				$paxinfo['Email']  		 = $data['paxemail'][$key];
				$paxinfo['ConfEmail']    = $data['paxemailconf'][$key];
				}
                if($value == 'ADT' && $infants > 0)
                {
                    $paxinfo['hasInfant']  = 'true';
                    $infants--;
                }
                else
                    $paxinfo['hasInfant']  = 'false';

                $passengers[] = $paxinfo;
                $i++;
            }

            /*LLamado del Plugin evalChurning
                * Return TRUE o FALSE
                * + True  = El usuario no puede realizar la reserva por que se encuentra una registrada en el sistema.
                * + False = El usuario puede realizar la reserva por que no se encuentra ninguna registrada en el sistema. 
            */
            //=========================================================================
            JPluginHelper::importPlugin('amadeus','evalChurning');
            
            $dispatcher = JDispatcher::getInstance();   
            $isChurning = $dispatcher->trigger( "plgevalChurning", array($arrayPas));
            
            if($isChurning[0] === true){
                $app = JFactory::getApplication();
                $app->redirect('index.php', JText::_('COM_AAWS_CHURNING_MENSAGE'));
                
            }
            else
            {
                $info['Passengers'] = $passengers;

                $params = JComponentHelper::getParams('com_aaws');
                $this->setWebService($params->get('air_service', ''));

                // Se agrega la información de pasajeros
                $objRequest          = new stdClass();
                $objRequest->request = $this->getXmlAddDataPassenger($info);
                $response            = $this->request('GetResponse', $objRequest);
     
                /**
                 * Se confirma el Booking, para lo cual de la respuesta anterior
                 * se obtiene el CorrelationID y el SequenceNmbr
                 */
                $info = array();
                $xml  = new DOMDocument();
                $xml->loadXML($response);

                $xpath = new DOMXPath($xml);
                $xpath->registerNameSpace('ns0', 'http://www.opentravel.org/OTA/2003/05');

                // Obteniendo el CorrelationID
                $results = $xpath->query('/ns0:OTA_AirBookRS/@CorrelationID');
                foreach($results as $result)
                {
                    $info['CorrelationID'] = $result->nodeValue;
                }

                // Obteniendo el SequenceNmbr
                $results = $xpath->query('/ns0:OTA_AirBookRS/@SequenceNmbr');
                foreach($results as $result)
                {
                    $info['SequenceNmbr'] = $result->nodeValue;
                }

                // Se confirma el Booking
                $objRequest          = new stdClass();
                $objRequest->request = $this->getXmlBook($info);
                $response            = $this->request('GetResponse', $objRequest);

                

                /**
                 * START Se ejecutan los plugins despues de hacer el booking.
                 *
                 * Los plugin pueden implementar las siguientes funcionalidades comunes
                 *   1. Validar el usuario actual o crear uno para asignarle la orden (usar $_POST)
                 *   2. Guardar la orden en el HistoryOrder o componente diseñado para tal fucionalidad
                 *   3. O enviar la orden al medio de Pago contratado
                 *
                 * ES importante tener en cuenta que el orden de los plugins afecta el proceso, así
                 * que revisar bien esa parte
                 */
                JPluginHelper::importPlugin('amadeus');
                $dispatcher      = JDispatcher::getInstance();
                $user            = JFactory::getUser();
                //  $IDPaymentMethod = 'P2P';
         
                //  $answer = $dispatcher->trigger('onAfterBooking', array(&$response, &$user, $IDPaymentMethod));
     			$answer = $dispatcher->trigger('onAfterBooking', array(&$response, &$user));
               
     
                if(count($answer) == 0)
                {
                    $booking = explode('BookingReferenceID ID="', $response);
                    $booking = substr($booking[1], 0, 6);

                    $app = JFactory::getApplication();
                    $app->redirect(JRoute::_(''), JText::sprintf('COM_AAWS_BOOKING_SAVED', $booking), 'error');
                }
            }

            /**
             * En teoria cada plugin debe lanzar su excepción, y en caso que no arrojen excepción,
             * controlar el error o comportamiento deseado. Pero hasta aqui llega el flujo áereo.
             *
             * Si no existen plugins, se redirecciona al index y se muestra un mensaje de reserva exitosa.
             * END
             */
        }
        catch(Exception $e)
        {   
            if($e->getCode() != 0 && $e->getCode() != 500)
            {
                $data = JRequest::getVar('wsform', array(), 'post');
                JRequest::setVar('wsform', $data['detail'], 'post', true);
                unset($data['detail']);

                preg_match('/^([^{]*){#([^}]+)?}$/', $e->getMessage(), $matches);
                $data['error'] = array(
                    'message' => $matches[1],
                    'field'   => $matches[2]
                );

                JRequest::setVar('info', $data, 'post', true);

                $this->passenger();
            }
            else
            {
                $app = JFactory::getApplication();
                $app->redirect(JRoute::_(''), $e->getMessage(), 'error');
            }
        }
    }


    /**
     **************************************************************************
     * FUNCIONES QUE SE INTEGRAN CON LA CLASE PADRE, PARA USAR LOS METODOS
     * COMUNES
     **************************************************************************
     */
    protected function request($method, $request)
    {
        // Se obtiene el comando que se ejecuta, para informarselo al plugin
        $startPosition = strpos($request->request, 'mpa:Command') + 12;
        $command       = substr($request->request, $startPosition, strpos($request->request, '</mpa:Command>') -$startPosition);

        /**
         * START Se ejecutan los plugin que modifican el mensaje que se envia en el request
         */
        JPluginHelper::importPlugin('amadeus');
        $dispatcher = JDispatcher::getInstance();

        $answer = $dispatcher->trigger('onBeforeAirResponse', array(&$request, &$command));

        if(in_array(false, $answer, true))
            throw new Exception('COM_AAWS_BEFORE_AIR_ERROR');
        /**
         * END
         */

        // Se ejecuta el llamado al servicio de aereo
        $data          = parent::request($method, $request);

        // Se obtiene solo la respuesta OTA, removiendo el xml generado por el servicio
        $startPosition = strpos($data, 'mpa:Message') + 12;
        $response      = substr($data, $startPosition, strpos($data, '</mpa:Message>') - $startPosition);

        /**
         * START Se ejecutan los plugin que modifican el mensaje de la disponibilidad
         */
        $answer = $dispatcher->trigger('onAfterAirResponse', array(&$response, $command));

        if(in_array(false, $answer, true))
            throw new Exception('COM_AAWS_AFTER_AIR_ERROR');
        /**
         * END
         */

        return $response;
    }

    protected function _setVariablesTo(&$xslt, $template)
    {
        switch($template)
        {
        case 'Availability':
            $action = 'air.passenger';
            break;
        case 'Detail':
            // Se envia el año, para seleccionar en la fecha de nacimiento
            $xslt->setParameter('', 'year', date('Y'));

            $user = JFactory::getUser();
            if($user->id != 0)
            {
                $phone = explode('~', $user->get('profile.phone', ''));

                if(count($phone) != 3)
                {
                    $phone = array('', '', '');
                }

                $xslt->setParameter('', 'username', $user->get('name', ''));
                $xslt->setParameter('', 'email', $user->get('email', ''));
                $xslt->setParameter('', 'documenttype', $user->get('profile.documenttype', ''));
                $xslt->setParameter('', 'documentid', $user->get('profile.documentnumber', ''));
                $xslt->setParameter('', 'phcountry', $phone[0]);
                $xslt->setParameter('', 'phcode', $phone[1]);
                $xslt->setParameter('', 'phnumber', $phone[2]);
            }
            $action = 'air.booking';
            break;
        }

        require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');
        $xslt->setParameter('', 'action', JRoute::_(AawsHelperRoute::getFlowRoute($action)));
    }



    /**
     **************************************************************************
     * FUNCIONES PARA EL USO INTERNO DE LA CLASE
     **************************************************************************
     */
    private function _getDate($date, $prevD)
    {
        if(!preg_match('/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2})?$/', trim($date), $matches))
            throw new Exception('COM_AAWS_ERROR_DATE_WRONG');

        $date = $matches[0];

        if(strlen($date) == 10)
            $date  = $date.($prevD == $date ? 'T12:00:00' : 'T00:00:00');
        else
            $date = $date.':00';

        return $date;
    }

    private function _login()
    {
		$app    = JFactory::getApplication();
        $filter = JFilterInput::getInstance();

		$credentials = array();
		$credentials['username'] = $filter->clean($_POST['wsform']['contactemail'], 'username');
		$credentials['password'] = $_POST['wsform']['contactpassword'];

        return $app->login($credentials);
    }

    // Funcio que creal el documento XML final. Como este depende del servicio consumido,
    // debe estar a nivel de producto
    protected function endDOMDocument($OTAMessage, $command)
    {
        $root = $this->_DOMDocument->appendChild($this->_DOMDocument->createElement('Request'));

        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:mpa', 'http://amadeus.com/latam/mpa/2010/1');
        $root->appendChild($this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:Command', $command));
        $root->appendChild($this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:Version', '1.0'));
        $root->appendChild($this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:ResponseType', 'XML'));
        $root->appendChild($this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:Target', ''));
        $root->appendChild($this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:MaxExecutionTime', '20'));

        $message = $this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:Message');
        $message->appendChild($OTAMessage);
        $root->appendChild($message);

        $setting = $this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:Setting');
        $setting->setAttributeNode(new DOMAttr('Key', 'Language'));
        $setting->setAttributeNode(new DOMAttr('Value', 'ES'));

        $providersetting = $this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:ProviderSetting');
        $providersetting->appendChild($setting);

        $providersettings = $this->_DOMDocument->createElementNS('http://amadeus.com/latam/mpa/2010/1', 'mpa:ProviderSettings');
        $providersettings->appendChild($providersetting);

        $root->appendChild($providersettings);

        //$this->_DOMDocument->formatOutput = true;
        //echo $this->_DOMDocument->saveXML();
        //die();

        return substr($this->_DOMDocument->saveXML(), 22);
    }


    private function getXmlAvailabilityRequest($info)
    {
        $this->startDOMDocument();

        $ota = $this->_DOMDocument->createElement('OTA_AirLowFareSearchRQ');
        $ota->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', 'http://www.opentravel.org/OTA/2003/05');

        // Add Itinerary
        foreach($info['origindestination'] as $item)
        {
            // Add date
            $origindestination = $this->_DOMDocument->createElement('OriginDestinationInformation');
            $departuredatetime = $this->_DOMDocument->createElement('DepartureDateTime', $item['datetime']);
            if(!$item['useHours'])
                $departuredatetime->setAttributeNode(new DOMAttr('CrossDateAllowedIndicator', "true"));
            $origindestination->appendChild($departuredatetime);

            // Add Origin
            $location = $this->_DOMDocument->createElement('OriginLocation');
            $location->setAttributeNode(new DOMAttr('LocationCode', $item['origin']));
            $origindestination->appendChild($location);

            // Add Destination
            $location = $this->_DOMDocument->createElement('DestinationLocation');
            $location->setAttributeNode(new DOMAttr('LocationCode', $item['destination']));
            $origindestination->appendChild($location);

            $ota->appendChild($origindestination);
        }

        // Add Travel Preferences
        if(isset($info['preferences']) && count($info['preferences']))
        {
            $preferences = $this->_DOMDocument->createElement('TravelPreferences');

            if(isset($info['preferences']['cabin']))
            {
                // Add Cabin preference
                $preference = $this->_DOMDocument->createElement('CabinPref');
                $preference->setAttributeNode(new DOMAttr('Cabin', $info['preferences']['cabin']));
                $preferences->appendChild($preference);
            }

            if(isset($info['preferences']['airline']) && strlen($info['preferences']['airline']) == 2)
            {
                // Add Airline preference
                $preference = $this->_DOMDocument->createElement('VendorPref');
                $preference->setAttributeNode(new DOMAttr('Code', $info['preferences']['airline']));
                $preference->setAttributeNode(new DOMAttr('CodeContext', 'Include'));
                $preferences->appendChild($preference);
            }

            if(isset($info['preferences']['connections']) && $info['preferences']['connections'] != '')
            {
                $connections = (int) $info['preferences']['connections'];

                // Add Cabin preference
                $preference = $this->_DOMDocument->createElement('FlightTypePref');
                if($connections == 0)
                {
                    $preference->setAttributeNode(new DOMAttr('DirectAndNonStopOnlyInd', true));
                }
                else
                {
                    $preference->setAttributeNode(new DOMAttr('FlightType', 'Connection'));
                    $preference->setAttributeNode(new DOMAttr('MaxConnections', $connections));
                }
                $preferences->appendChild($preference);
            }

            $ota->appendChild($preferences);
        }

        // Add passengers Info
        $travelerinfosummary = $this->_DOMDocument->createElement('TravelerInfoSummary');
        $airtraveleravail    = $this->_DOMDocument->createElement('AirTravelerAvail');
        foreach($info['travelertypes'] as $type => $number)
        {
            $paxtype = $this->_DOMDocument->createElement('PassengerTypeQuantity');
            $paxtype->setAttributeNode(new DOMAttr('Code', $type));
            $paxtype->setAttributeNode(new DOMAttr('Quantity', $number));

            $airtraveleravail->appendChild($paxtype);
        }

        $travelerinfosummary->appendChild($airtraveleravail);
        $ota->appendChild($travelerinfosummary);

        return $this->endDOMDocument($ota, 'AirLowFareSearch');
    }

    private function getXmlDetailRequest($info)
    {
        $this->startDOMDocument();

        $ota = $this->_DOMDocument->createElement('OTA_AirLowFareSearchRQ');
        $ota->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', 'http://www.opentravel.org/OTA/2003/05');
        $ota->setAttributeNode(new DOMAttr('SequenceNmbr', $info['SequenceNmbr']));
        $ota->setAttributeNode(new DOMAttr('CorrelationID', $info['CorrelationID']));

        $travelerinfosummary = $this->_DOMDocument->createElement('TravelerInfoSummary');

        $pricerequestinformation = $this->_DOMDocument->createElement('PriceRequestInformation');

        $negotiatedfarecode = $this->_DOMDocument->createElement('NegotiatedFareCode');
        $negotiatedfarecode->setAttributeNode(new DOMAttr('CodeContext', $info['FareCode']));
        /**
         * Codigos de restricciones:
         *
         * PE => Sanciones
         * SU => Recargos
         * SO => Paradas Nocturas
         * TF => Transitos
         * EL => Aplicación Viaje
         * FL => Validez Del Viaje
         * MN => Mínimo
         * MX => Máximo
         * TR => Restricciones Del Viaje
         * MD => Misceláneos
         */
        $negotiatedfarecode->setAttributeNode(new DOMAttr('Code', $info['code']));

        $pricerequestinformation->appendChild($negotiatedfarecode);
        $travelerinfosummary->appendChild($pricerequestinformation);

        $ota->appendChild($travelerinfosummary);

        $travelpreferences = $this->_DOMDocument->createElement('TravelPreferences');
        $travelpreferences->setAttributeNode(new DOMAttr('OriginDestinationRPHs', $info['rphs']));
        $ota->appendChild($travelpreferences);

        return $this->endDOMDocument($ota, 'AirDetail');
    }

    private function getXmlAddDataPassenger($info)
    {
        $this->startDOMDocument();

        $ota = $this->_DOMDocument->createElement('OTA_AirBookRQ');
        $ota->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', 'http://www.opentravel.org/OTA/2003/05');
        $ota->setAttributeNode(new DOMAttr('SequenceNmbr', $info['SequenceNmbr']));
        $ota->setAttributeNode(new DOMAttr('CorrelationID', $info['CorrelationID']));

        $travelerinfo = $this->_DOMDocument->createElement('TravelerInfo');
        $first        = true;
        foreach($info['Passengers'] as $pax)
        {
            $airtraveler = $this->_DOMDocument->createElement('AirTraveler');
            $airtraveler->setAttributeNode(new DOMAttr('PassengerTypeCode', $pax['Type']));
            $airtraveler->setAttributeNode(new DOMAttr('Gender', $pax['Gender']));
            $airtraveler->setAttributeNode(new DOMAttr('BirthDate', $pax['BirthDate']));
            $airtraveler->setAttributeNode(new DOMAttr('AccompaniedByInfant', $pax['hasInfant']));
			
            $personname = $this->_DOMDocument->createElement('PersonName');

            if($pax['Treatment'] != '')
            $personname->appendChild($this->_DOMDocument->createElement('NamePrefix', $pax['Treatment']));
            $personname->appendChild($this->_DOMDocument->createElement('GivenName', $pax['FirstName']));
            $personname->appendChild($this->_DOMDocument->createElement('Surname', $pax['LastName']));
            $document = $this->_DOMDocument->createElement('Document');
            $document->setAttributeNode(new DOMAttr('DocType', $pax['DocumentType']));
            $document->setAttributeNode(new DOMAttr('DocID', $pax['Document']));
            $document->setAttributeNode(new DOMAttr('DocHolderNationality', $pax['Nationality']));
            // Agregando datos comunes a todos los viajeros
            $airtraveler->appendChild($personname);
            $airtraveler->appendChild($document);

            if($first && $pax['Type'] == 'ADT')
            {
                $telephone = $this->_DOMDocument->createElement('Telephone');
                $telephone->setAttributeNode(new DOMAttr('PhoneLocationType', '6'));
                $telephone->setAttributeNode(new DOMAttr('PhoneNumber', $info['ContactPhone']));

                $email = $this->_DOMDocument->createElement('Email', $info['ContactMail']);
                $email->setAttributeNode(new DOMAttr('EmailType', '1'));

                $comment1 = $this->_DOMDocument->createElement('Comment', 'PR');
                $comment1->setAttributeNode(new DOMAttr('Name', 'PnrType'));

                $comment2 = $this->_DOMDocument->createElement('Comment', 'true');
                $comment2->setAttributeNode(new DOMAttr('Name', 'TSA'));

                // Agregando datos de contacto del primer pasajero adulto
                $airtraveler->appendChild($telephone);
                $airtraveler->appendChild($email);
                $airtraveler->appendChild($comment1);
                $airtraveler->appendChild($comment2);

                $first = false;
            }
            else
            {
                $comment = $this->_DOMDocument->createElement('Comment', 'AC');
                $comment->setAttributeNode(new DOMAttr('Name', 'PnrType'));
                $airtraveler->appendChild($comment);
            }

            $travelerinfo->appendChild($airtraveler);
        }

        $ota->appendChild($travelerinfo);

        $fulfillment = $this->_DOMDocument->createElement('Fulfillment');
        $paymentdetails = $this->_DOMDocument->createElement('PaymentDetails');
        $paymentdetail = $this->_DOMDocument->createElement('PaymentDetail');
        $paymentdetail->setAttributeNode(new DOMAttr('PaymentType', '6'));
        $paymentdetails->appendChild($paymentdetail);
        $receipt = $this->_DOMDocument->createElement('Receipt');
        $receipt->setAttributeNode(new DOMAttr('DistribType', '16'));

        $fulfillment->appendChild($paymentdetails);
        $fulfillment->appendChild($receipt);

        $ota->appendChild($fulfillment);
		 return $this->endDOMDocument($ota, 'AirAddDataPassenger');
        
    }

    private function getXmlBook($info)
    {
        $this->startDOMDocument();

        $ota = $this->_DOMDocument->createElement('OTA_AirBookRQ');
        $ota->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', 'http://www.opentravel.org/OTA/2003/05');
        $ota->setAttributeNode(new DOMAttr('SequenceNmbr', $info['SequenceNmbr']));
        $ota->setAttributeNode(new DOMAttr('CorrelationID', $info['CorrelationID']));

        return $this->endDOMDocument($ota, 'AirBook');
    }

    private function _getCityName($iata)
    {
        $db    = JFactory::getDBO();
        $query = $db->getQuery(true);
        $lang  = JFactory::getLanguage(); 
        $lang  = substr($lang->getTag(), 3);

        $query->select('iata, ciudad, aeropuerto, idcity')
            ->from('#__qs_cities')
            ->where('iata = '.$db->Quote($iata))
            ->where('lenguaje = '.$db->Quote($lang));

        $db->setQuery($query);
        $row = $db->loadObject();

        if($row != null)
        {
            if($row->ciudad == null)
            {
                $query->clear('where');

                $query->where('id = '.$db->Quote($row->idcity))
                    ->where('lenguaje = '.$db->Quote($lang));

                $db->setQuery($query);
                $city = $db->loadObject();

                $row ->ciudad = $city->ciudad;
            }

            if($row->aeropuerto != '')
            {
                return $row->iata.'|'.$row->ciudad.', '.$row->aeropuerto.' ('.$row->iata.')';
            }
            else
            {
                return $row->iata.'|'.$row->ciudad.' ('.$row->iata.')';
            }
        }
        else
        {
            return $iata.'|'.$iata;
        }
    }

    private function _getAirlineName($iata)
    {
        $db    = JFactory::getDBO();
        $query = $db->getQuery(true);
        $lang  = JFactory::getLanguage(); 
        $lang  = substr($lang->getTag(), 3);

        $query->select('codigo, nombre')
            ->from('#__qs_airlines')
            ->where('codigo = '.$db->Quote($iata));

        $db->setQuery($query);
        $row = $db->loadObject();

        if($row != null)
        {
            return $row->codigo.'|'.$row->nombre;
        }
        else
        {
            return $iata.'|'.$iata;
        }
    }

}

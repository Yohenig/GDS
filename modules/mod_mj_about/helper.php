<?php
# About Module for Master                                         
# Copyright (C) 2015 by mojoomla.com
# Homepage   : www.mojoomla.com               
# Author     : mojoomla.com                     
# Email      : sales@mojoomla.com             
# Version    : 1.0                                    
# license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
//No Direct Access to this file
class modMjAbout
{
	
    public static function getmodMjAbout($params)
    {        
       $app = JFactory::getApplication();
	   $template   = $app->getTemplate(true);
	   $params     = $template->params; 

	  //section1
	   
	  // $image1 = $params->get('about_image_1');
	  // $title_1 = $params->get('about_title_1');
	   
	   //section2
	   	  
	    $about_title_1 = $params->get('about_title1');
	    $about_desc_1 = $params->get('about_desc_1');
	   
	   //section3
	  	
	  	$about_title_2 = $params->get('about_title2');
	    $about_desc_2 = $params->get('about_desc_2');
	   
	    //section4
	    
	    $about_title_3 = $params->get('about_title3');
	    $about_desc_3 = $params->get('about_desc_3');
	    
	    //section5
	     
	    $about_title_4 = $params->get('about_title4');
	    $about_desc_4 = $params->get('about_desc_4');
	    
	    //section6
	    
	    $about_title_5 = $params->get('about_title5');
	    $about_desc_5 = $params->get('about_desc_5');
	    
	   $about_data = array();
	   
		   
	   if($about_title_1 != "")
	   {
	   	$two = array($about_title_1,$about_desc_1);
	   	array_push($about_data, $two);
	   }
		
	   if($about_title_2 != "")
	   {
	   	$three = array($about_title_2,$about_desc_2);
	   	array_push($about_data, $three);
	   }
	   
	   if($about_title_3 != "")
	   {
	   	$four = array($about_title_3,$about_desc_3);
	   	array_push($about_data, $four);
	   }
	   if($about_title_4 != "")
	   {
	   	$five = array($about_title_4,$about_desc_4);
	   	array_push($about_data, $five);
	   }
	   if($about_title_5 != "")
	   {
	   	$six = array($about_title_5,$about_desc_5);
	   	array_push($about_data, $six);
	   }
	   
	   return $about_data;
    }
}
?>
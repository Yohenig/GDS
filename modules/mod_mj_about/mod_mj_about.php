<?php
# About Module for Master                                         
# Copyright (C) 2015 by mojoomla.com
# Homepage   : www.mojoomla.com               
# Author     : mojoomla.com                     
# Email      : sales@mojoomla.com             
# Version    : 1.0                                    
# license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Include the syndicate functions only once
require_once( dirname(__FILE__).'/helper.php' );

$about = modMjAbout::getmodMjAbout($params);

require JModuleHelper::getLayoutPath('mod_mj_about');
?>  
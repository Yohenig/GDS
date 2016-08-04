<?php
/*
* @package Master
* @copyright (C) 2015 by mojoomla.com - All rights reserved!
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author mojoomla.com <sales@mojoomla.com>
*/

?>
<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );

// Theme params
$logoFile=$this->params->get('logoFile');

$co_value1=$this->params->get('co_value1');
$co_value2=$this->params->get('co_value2');
$co_value3=$this->params->get('co_value3');
$co_value4=$this->params->get('co_value4');
//Social Link
$s_link1=$this->params->get('s_link1');
$s_link2=$this->params->get('s_link2');
$s_link3=$this->params->get('s_link3');
$s_link4=$this->params->get('s_link4');
$s_link5=$this->params->get('s_link5');
$s_link6=$this->params->get('s_link6');
$s_link7=$this->params->get('s_link7');
$s_link8=$this->params->get('s_link8');
// Copyright
$copyright=$this->params->get('copyright');
// Contact Details
$con_heading=$this->params->get('con_heading');
$con_title=$this->params->get('con_title');
$con_button=$this->params->get('con_button');

$con_name1=$this->params->get('con_name1');
$con_value1=$this->params->get('con_value1');
$con_name2=$this->params->get('con_name2');
$con_value2=$this->params->get('con_value2');

//Color Options

$theme_color= $this->params->get('theme_color');
$_SESSION['theme_color'] = $theme_color;

//Layout Options
$chooselayout = $this->params->get('chooselayout');

// Pattern oprtion

$choosepattern = $this->params->get('choosepattern');

?>
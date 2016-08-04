<?php
/**
 * @package     Modest.Site
 * @subpackage  Templates.Modest
 *
 * @copyright   Copyright (C) 2005 - 2015 mojoomla.com - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
ini_set("display_errors","0");
//Preset Options

$preset = $this->params->get('preset_detail');
if($preset == 'preset1')
{
	require("preset/preset1_index.php");	
}
else if($preset == 'preset2')
{
	require("preset/preset2_index.php");	
}

?>


<?php 

/*
// K2 Multiple Extra fields Filter and Search module by Andrey M
// molotow11@gmail.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

require_once (dirname(__FILE__).DS.'helper.php');

// Main params
$moduleclass_sfx = $params->get('moduleclass_sfx', '');			// Module Class Suffix
$getTemplate = $params->get('getTemplate','Default');
$resultf = $params->get('resultf', '');					// Results phrase
$noresult = $params->get('noresult', '');					// Noresults
$page_heading = $params->get('page_heading', '');

$filters = $params->get('filters', '');						// Select Extra field

$connected_fields = $params->get('connected_fields', '');
$connected_show_all = $params->get('connected_show_all', '');

$cols = $params->get('cols', '');
$elems = $params->get('elems', 0);
$ajax_results = $params->get('ajax_results', 0);
$ajax_container = $params->get('ajax_container', '.results_container');

$filter_template = $params->get('results_template', 0);
$template_selector = $params->get('template_selector', 0);

//Restriction params 
$restrict = $params->get('restrict', 0);
$restmode = $params->get('restmode', 0);
$restcat = $params->get('restcat', '');
$restsub = $params->get('restsub', 1);

//Ordering
$ordering = $params->get('ordering', 0);	
$ordering_default = $params->get('ordering_default', '');
$ordering_extra = $params->get('ordering_extra', 1);
$ordering_default_method = $params->get('ordering_default_method', 'asc');

// Search button params
$showtitles = $params->get('showtitles', 1);					// Show extrafields titles
$button = $params->get('button', 1);							// Show submit button
$button_text = $params->get('button_text', JText::_('MOD_K2_FILTER_BUTTON_SEARCH'));	// Submit button text
$onchange = $params->get('onchange', 0);

$clear_btn = $params->get('clear_btn', 0);	

$acounter = $params->get('acounter', 0);
$acompleter = $params->get('acompleter', 0);

//Itemid
$itemidv = $params->get('itemidv', 0);
if($itemidv == 0) {
	$itemid = JRequest::getInt("Itemid");
}
else {
	$itemid = $params->get('itemid', '');
}

$allrequired = $params->get('allrequired', 0);
$dynobox = $params->get('dynobox', 0);

$document = &JFactory::getDocument();
$document->addStylesheet(JURI::base() . 'modules/mod_k2_filter/assets/filter.css');	

if(!JPluginHelper::isEnabled('system', 'k2filter')) {
	echo "K2 Filter plugin is not published.<br />";
}

if($filters == "") {
	echo "Select search fields in the module options! <br />";
	return;
}

$field_types = Array();

$filters = explode("\r\n", $filters);	
foreach($filters as $k=>$filter) {
	$filter = explode(":", $filter);
	$field_types[$k] = new JObject;
	if($filter[0] == 'extrafield') {
		$field_types[$k]->id = $filter[1];
		$field_types[$k]->type = $filter[2];
		if($filter[2] == '') {
			$field_types[$k]->type = 'select';
		}
		$field_types[$k]->content = modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($filter[1], 'value'));
		$field_types[$k]->name = modK2FilterHelper::pull($filter[1], 'name');	
	}
	else {
		$field_types[$k]->id = '1000'.$k;
		$field_types[$k]->type = $filter[0];
	}
}

//Connected fields
if($connected_fields != "") {
	$connected_fields = explode("\n", $connected_fields);
	
	foreach($connected_fields as $k=>$connected) {
		if($connected != "") {
			$connected_fields[$k] = explode("=>", $connected);
		}
		else {
			unset($connected_fields[$k]);
		}
	}
}

require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'template'));

?>
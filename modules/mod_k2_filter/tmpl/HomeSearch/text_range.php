<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="k2filter-field-text-range">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<input class="inputbox" style="width: 40%;" name="searchword<?php echo $field->id;?>-from" type="text" <?php if (JRequest::getVar('searchword'.$field->id.'-from')) echo ' value="'.JRequest::getVar('searchword'.$field->id.'-from').'"'; ?> /> - 
		
		<input class="inputbox" style="width: 40%;" name="searchword<?php echo $field->id;?>-to" type="text" <?php if (JRequest::getVar('searchword'.$field->id.'-to')) echo ' value="'.JRequest::getVar('searchword'.$field->id.'-to').'"'; ?> />
	</div>

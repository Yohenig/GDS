<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery("input.datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>

	<div class="k2filter-field-created">
	
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_CREATED'); ?>
		</h3>
		
		<input style="width: 40%;" class="datepicker inputbox" name="created-from" type="text" <?php if (JRequest::getVar('created-from')) echo ' value="'.JRequest::getVar('created-from').'"'; ?> /> - 
		<input style="width: 40%;" class="datepicker inputbox" name="created-to" type="text" <?php if (JRequest::getVar('created-to')) echo ' value="'.JRequest::getVar('created-to').'"'; ?> />
	</div>


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
		
		<input class="datepicker inputbox" name="created" type="text" <?php if (JRequest::getVar('created')) echo ' value="'.JRequest::getVar('created').'"'; ?> />
	</div>

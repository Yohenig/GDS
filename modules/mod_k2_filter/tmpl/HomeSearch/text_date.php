<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery("input.datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>
	
	<div class="k2filter-field-text-date">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<input class="datepicker inputbox" name="searchword<?php echo $field->id;?>" type="text" <?php if (JRequest::getVar('searchword'.$field->id)) echo ' value="'.JRequest::getVar('searchword'.$field->id).'"'; ?> />
	</div>


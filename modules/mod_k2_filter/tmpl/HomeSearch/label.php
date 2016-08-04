<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$values = modK2FilterHelper::getExtraValues($field->id, $params);

$values = implode(",", $values);
$values = explode(",", $values);

$selected = JRequest::getVar("flabel");

?>
	
<script>
	jQuery(document).ready(function() {
		jQuery("a.flabel").click(function() {
			var val = jQuery(this).text();
			jQuery("input[name=flabel]").val(val);
			submit_form_<?php echo $module->id; ?>();
		});
	});
</script>

	<div class="k2filter-field-label">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<div>
			<?php 
				if(count($values)) {
					foreach($values as $value) {
			?>
					<a class="flabel" href="javascript: return false;"
					<?php if($selected == $value) echo " style='text-decoration: underline;'"; ?>
					><?php echo $value; ?></a>
			<?php
					}
				}
			?>
			<div class="K2FilterClear"></div>
		</div>
		<input name="flabel" type="hidden" <?php if (JRequest::getVar('flabel')) echo ' value="'.JRequest::getVar('flabel').'"'; ?> />
	</div>


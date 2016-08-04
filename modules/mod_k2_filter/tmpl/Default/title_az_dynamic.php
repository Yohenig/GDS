<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if($restmode == 1) {	
	$view = JRequest::getVar("view");
	$task = JRequest::getVar("task");
					
	if($view == "itemlist" && $task == "category") 
		$restcata = JRequest::getInt("id");
	else if($view == "item") {
		$id = JRequest::getInt("id");
		$restcata = modK2FilterHelper::getParent($id);
	}
	else {
		$restcata = JRequest::getVar("restcata");
	}
}

$letters = Array();
require_once (JPATH_SITE.DS.'plugins'.DS.'system'.DS.'k2filter'.DS.'K2Filter'.DS.'models'.DS.'itemlistfilter.php');
$current_val = JRequest::getVar("ftitle_az", "");

JRequest::setVar("task", "filter");
JRequest::setVar("moduleId", $module->id);
JRequest::setVar("restcata", $restcata);

$letters[0][0] = "#";
JRequest::setVar("ftitle_az", "num");
$letters[0][1] = (int)K2ModelItemlistfilter::getTotal();

foreach(range('a', 'z') as $k=>$letter) {
	$letters[($k+1)][0] = $letter;
	JRequest::setVar("ftitle_az", $letter);
	$letters[($k+1)][1] = (int)K2ModelItemlistfilter::getTotal();
}
JRequest::setVar("ftitle_az", $current_val);

?>
	
<script>
	jQuery(document).ready(function() {
		var ftitle_az = jQuery("input[name=ftitle_az]").val();
		
		jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").each(function() {
			if(ftitle_az == jQuery(this).text()) {
				jQuery(this).css("font-weight", "bold").addClass("active");
			}
			if(ftitle_az == "num" && jQuery(this).text() == "#") {
				jQuery(this).css("font-weight", "bold").addClass("active");
			}
		});
	
		jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").click(function() {
			jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").css("font-weight", "normal");

			if(jQuery(this).hasClass("active") == 0) {
				jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").removeClass("active");
				jQuery(this).css("font-weight", "bold").addClass("active");
				
				var value = jQuery(this).html();
				if(value == '#') {
					jQuery("input[name=ftitle_az]").val("num");
				}
				else {
					jQuery("input[name=ftitle_az]").val(value);
				}
			}
			else {
				jQuery(this).css("font-weight", "normal").removeClass("active");
				jQuery("input[name=ftitle_az]").val("");
			}
			
			<?php if($onchange) : ?>
			submit_form_<?php echo $module->id; ?>();
			<?php endif; ?>
			
			return false;
		});
	});
</script>

	<div class="k2filter-field-title-az">
	
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TITLE_AZ'); ?>
		</h3>
		<?php endif; ?>

		<?php foreach($letters as $letter) : ?>
			<?php if($letter[1] > 0) : ?>
			<a class="title_az" href="#"><?php echo $letter[0]; ?></a>
			<?php else : ?>
			<a class="title_az nonActive" href="#"><?php echo $letter[0]; ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
		
		<input name="ftitle_az" type="hidden" <?php if (JRequest::getVar('ftitle_az')) echo ' value="'.JRequest::getVar('ftitle_az').'"'; ?> />
        <div class="clr"></div>
	</div>


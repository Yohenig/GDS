<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<?php if($elems > 0) : ?>
	<script type="text/javascript">					
		jQuery(document).ready(function () {
			jQuery("div.filter_cat_hidden").hide();
			jQuery("a.expand_filter_cat").click(function() {
				jQuery("div.filter_cat_hidden").slideToggle("fast");
				return false;
			});
		});
	</script>
	<?php endif; ?>

	<div class="k2filter-field-category-checkbox">
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_CATEGORY_HEADER'); ?>
		</h3>
		<?php endif; ?>
		
		<div class="options_container">
		<?php echo $category_options; ?>
		<?php if($elems > 0) : ?>
			</div>
			<p>
				<a href="#" class="button expand expand_filter_cat"><?php echo JText::_("MOD_K2_FILTER_MORE"); ?></a>
			</p>
		<?php endif; ?>
		</div>
    </div>
<?php 

/*
// K2 Multiple Extra fields Filter and Search module by Andrey M
// molotow11@gmail.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$language = JFactory::getLanguage();
$currentLang = $language->getTag();
list($shortLang) = explode("-", $currentLang);

?>
<script type="text/javascript">
	if (typeof jQuery == 'undefined') {
		document.write('<scr'+'ipt type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery-1.10.2.min.js"></scr'+'ipt>');
		document.write('<scr'+'ipt>jQuery.noConflict();</scr'+'ipt>');
	}
	
	if (typeof jQuery.ui == 'undefined') {
		document.write('<scr'+'ipt type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery-ui-1.10.4.custom.min.js"></scr'+'ipt>');
		document.write('<link type="text/css" href="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/smoothness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />');
	}
	
	document.write('<scr'+'ipt type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.multiselect.js"></scr'+'ipt>');
	document.write('<link type="text/css" href="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.multiselect.css" rel="stylesheet" />');	document.write('<scr'+'ipt type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.multiselect.filter.js"></scr'+'ipt>');
	document.write('<link type="text/css" href="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.multiselect.filter.css" rel="stylesheet" />');
	
	document.write('<scr'+'ipt type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.ui.touch-punch.min.js"></scr'+'ipt>');
	
	jQuery(document).ready(function() {	
		jQuery("#K2FilterBox<?php echo $module->id; ?> form").submit(function() {
			<?php if($allrequired) : ?>
			if(!check_required<?php echo $module->id; ?>()) {
				return false;
			}
			<?php endif; ?>
			jQuery(this).find("input, select").each(function() {
				if(jQuery(this).val() == '') {
					jQuery(this).attr("name", "");
				}
			});
		});
		<?php if($ajax_results == 1) : ?>
		jQuery("#K2FilterBox<?php echo $module->id; ?> input[type=submit]").click(function() {
			<?php if($allrequired) : ?>
			if(!check_required<?php echo $module->id; ?>()) {
				return false;
			}
			<?php endif; ?>
			ajax_results<?php echo $module->id; ?>();
			return false;
		});
		<?php endif; ?>
	});
	
	function submit_form_<?php echo $module->id; ?>() {
		<?php if($ajax_results == 1) : ?>
		ajax_results<?php echo $module->id; ?>();
		return false;
		<?php endif; ?>	
		jQuery("#K2FilterBox<?php echo $module->id; ?> form").submit();
	}
	
</script>

<div id="K2FilterBox<?php echo $module->id; ?>" class="K2FilterBlock<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php if($params->get('descr') != "") : ?>
	<p><?php echo $params->get('descr'); ?></p>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter&Itemid='.$itemid); ?>" name="K2Filter<?php echo $module->id; ?>" method="get">
  		<?php $app =& JFactory::getApplication(); if (!$app->getCfg('sef')): ?>
		<input type="hidden" name="option" value="com_k2" />
		<input type="hidden" name="view" value="itemlist" />
		<input type="hidden" name="task" value="filter" />
		<?php endif; ?>
		
	  <div class="k2filter-table">

<?php for($k = 0; $k < count($field_types); $k++) { 
		$field = $field_types[$k];
?>
		
		<div class="k2filter-cell k2filter-cell<?php echo $k; ?>"<?php if($cols) echo ' style="width: '. (100/$cols - 2) .'%;"'?>>
		
		<?php			
			switch($field->type) {
			
				case 'text' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'text'));
				break;
				
				case 'text_range' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'text_range'));
				break;			
			
				case 'text_date' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'text_date'));
				break;
				
				case 'text_date_range' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'text_date_range'));
				break;
				
				case 'text_az' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'text_az'));
				break;
				
				case 'select' :				
					if($connected_fields != "") {					
						foreach($connected_fields as $key=>$connected) {
							if($connected[0] == $field->name) {							
								require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'select_connected_parent'));
								
								echo "</div>";
								if($cols && ($k+1) % $cols == 0 && ($k+1) != count($field_types)) {
									echo '<div style="clear: both;"></div>';
								}
															
								for($n = 1; $n < count($connected); $n++) {
									
									$k++;
									
									?>
									<div class="k2filter-cell k2filter-cell<?php echo $k; ?>"<?php if($cols) echo ' style="width: '. (100/$cols - 2) .'%;"'?>>
									<?php

									$connected_name = $connected[$n];
									$last_child = '';
									if(($n+1) == count($connected)) {
										$last_child = ' lastchild';
									}
									require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'select_connected_child'));
									
									echo "</div>";
									if($cols && ($k+1) % $cols == 0 && ($k+1) != count($field_types)) {
										echo '<div style="clear: both;"></div>';
									}
								}
								
								unset($connected_fields[$key]);
								continue 3;
							}
							else {
								require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'select'));
							}
						}						
					}
					else {
						require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'select'));
					}
				break;
				
				case 'select_autofill' :
					$values = modK2FilterHelper::getExtraValues($field->id, $params);
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'select_autofill'));
				break;
		
				case 'multi' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'multi'));
				break;
		
				case 'multi_select' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'multi_select'));
				break;
				
				case 'multi_select_autofill' :
					$field->content = modK2FilterHelper::getExtraValues($field->id, $params);
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'multi_select_autofill'));
				break;
		
				case 'slider' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'slider'));
				break;
				
				case 'slider_range' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'slider_range'));
				break;
				
				case 'slider_range_autofill' :
					$field->content = modK2FilterHelper::getExtraValues($field->id, $params);
					if($field->content) {
						foreach($field->content as $val_k=>$value) {
							$field->content[$val_k] = floatval($value);
							if(floatval($value) == 0) {
								unset($field->content[$val_k]);
							}
						}
						sort($field->content);
					}
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'slider_range_autofill'));
				break;
		
				case 'radio' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'radio'));
				break;
				
				case 'label' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'label'));
				break;

				case 'title' :			
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'title'));
				break;
				
				case 'title_az' :				
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'title_az'));
				break;
				
				case 'item_text' :				
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'item_text'));
				break;

				case 'item_all' :			
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'item_all'));
				break;
				
				case 'item_id' :			
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'item_id'));
				break;				
				
				case 'tag_text' :
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'tag_text'));
				break;		
				
				case 'tag_select' :
					$restcata = 0;					
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
					$tags = modK2FilterHelper::getTags($params, $restcata);
					if(count($tags)) {
						require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'tag_select'));
					}
				break;
				
				case 'tag_multi' :
					$restcata = 0;					
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
					$tags = modK2FilterHelper::getTags($params, $restcata);					
					if(count($tags)) {
						require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'tag_multi'));
					}
				break;
				
				case 'tag_multi_select' :
					$restcata = 0;					
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
					$tags = modK2FilterHelper::getTags($params, $restcata);					
					if(count($tags)) {
						require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'tag_multi_select'));
					}
				break;
				
				case 'category_select' :
					ob_start();
					modK2FilterHelper::treeselectbox($params, 0, 0, $module->id);
					$category_options = ob_get_contents();
					ob_end_clean();
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'category_select'));
				break;			
			
				case 'category_multiple' :
					ob_start();
					modK2FilterHelper::treeselectbox_multi($params, 0, 0, $elems, $module->id);
					$category_options = ob_get_contents();
					ob_end_clean();
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'category_checkbox'));
				break;
				
				case 'category_multiple_select' :
					ob_start();
					modK2FilterHelper::treeselectbox_multi_select($params, 0, 0, $module->id);
					$category_options = ob_get_contents();
					ob_end_clean();
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'category_select_multiple'));
				break;
				
				case 'authors_select' :		
					$authors = modK2FilterHelper::getAuthors($params);
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'authors_select'));
				break;
				
				case 'authors_select_multiple' :		
					$authors = modK2FilterHelper::getAuthors($params);
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'authors_select_multiple'));
				break;
				
				case 'created' :				
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'created'));
				break;
				
				case 'created_range' :				
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'created_range'));
				break;
				
				case 'publish_up' :			
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'publish_up'));
				break;
				
				case 'publish_up_range' :				
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'publish_up_range'));
				break;
				
				case 'publish_down' :				
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'publish_down'));
				break;
				
				case 'publish_down_range' :				
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'publish_down_range'));
				break;
				
				case 'price_range' :				
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'price_range'));
				break;
				
				default:
					require (JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'select'));
			}
		?>
		</div>
		<?php
		if($cols && ($k+1) % $cols == 0 && ($k+1) != count($field_types)) {
			echo '<div style="clear: both;"></div>';
		}
	}
?>		<div style="clear: both;"></div>
	</div><!--/k2filter-table-->
	
	<?php if($restrict == 1) : ?>
		<?php if($restmode == 1) : ?>			
			<?php 
				$restcata = "";
				$view = JRequest::getVar("view");
				
				if($view == "itemlist") { 
					$restcata = JRequest::getInt("id");
				}
				else if($view == "item") {
					$id = JRequest::getInt("id");
					$restcata = modK2FilterHelper::getParent($id);
				}
			?>
			
			<?php if($restcata != "") : ?>
				<input type="hidden" name="restcata" value="<?php echo $restcata; ?>" />
			<?php endif; ?>
			
			<?php $restauto = JRequest::getInt("restcata"); ?>
			<?php if($restauto != "" && $restcata == "") : ?>
				<input type="hidden" name="restcata" value="<?php echo $restauto; ?>" />
			<?php endif; ?>
			
		<?php endif; ?>
	<?php endif; ?>
	
	<input type="hidden" name="orderby" value="<?php echo JRequest::getVar("orderby"); ?>" />
	<input type="hidden" name="orderto" value="<?php echo JRequest::getVar("orderto"); ?>" />
	
	<input type="hidden" name="template_id" value="<?php echo JRequest::getVar("template_id"); ?>" />
	
	<input type="hidden" name="moduleId" value="<?php echo $module->id; ?>" />

	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
	
	<?php if ($button):?>
	<input type="submit" value="<?php echo $button_text; ?>" class="button <?php echo $moduleclass_sfx; ?>" />
	<?php endif; ?>
	
	<?php if ($clear_btn):?>
		<script type="text/javascript">
			<!--
			function clearSearch_<?php echo $module->id; ?>() {
				jQuery("#K2FilterBox<?php echo $module->id; ?> form select").each(function () {
					jQuery(this).val(-1);
				});
				
				jQuery("#K2FilterBox<?php echo $module->id; ?> form input.inputbox").each(function () {
					jQuery(this).val("");
				});		

				jQuery(".ui-slider").each(function() {
					var slider_min = jQuery(this).slider("option", "min");
					var slider_max = jQuery(this).slider("option", "max");
					jQuery(this).slider("option", "values", [slider_min, slider_max]);
				});
				jQuery("#K2FilterBox<?php echo $module->id; ?> form input.slider_val").each(function () {
					jQuery(this).val("");
				});
						
				jQuery("#K2FilterBox<?php echo $module->id; ?> form input[type=checkbox]").each(function () {
					jQuery(this).removeAttr('checked');
				});

				jQuery("#K2FilterBox<?php echo $module->id; ?> form input[name=flabel]").val("");				
				
				jQuery("#K2FilterBox<?php echo $module->id; ?> form input[type=radio]").each(function () {
					jQuery(this).removeAttr('checked');
				});
				
				jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").css("font-weight", "normal").removeClass("active");
				jQuery("input[name=ftitle_az]").val("");
				
				jQuery(".k2filter-field-multi select").each(function() {
					jQuery(this).multiselect("uncheckAll").multiselect("refresh");
				});		
						
				jQuery(".k2filter-field-tag-multi select").multiselect("uncheckAll").multiselect("refresh");
				jQuery(".k2filter-field-category-select-multiple select").multiselect("uncheckAll").multiselect("refresh");
				jQuery(".k2filter-field-author-multi select").multiselect("uncheckAll").multiselect("refresh");
				
				jQuery("select.lastchild").attr("disabled", "disabled").find("option:gt(0)").remove();
				
				jQuery("<?php echo $ajax_container; ?>").html("");
				
				submit_form_<?php echo $module->id; ?>();
			}
			//-->
		</script>	

		<input type="button" value="<?php echo JText::_('MOD_K2_FILTER_BUTTON_CLEAR'); ?>" class="button reset <?php echo $moduleclass_sfx; ?>" onclick="clearSearch_<?php echo $module->id; ?>()" />
	
	<?php endif; ?>
	
  </form>
  
  <?php if($acounter) : ?>
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#K2FilterBox<?php echo $module->id; ?> form").change(function() {
					acounter<?php echo $module->id; ?>();
				});
			});
			
			function acounter<?php echo $module->id; ?>() {
				jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter").html("<p><img src='<?php echo JURI::root(); ?>media/k2/assets/images/system/loader.gif' /></p>");
					
				jQuery.ajax({
					data: jQuery("#K2FilterBox<?php echo $module->id; ?> form").serialize() + "&format=count",
					type: jQuery("#K2FilterBox<?php echo $module->id; ?> form").attr('method'),
					url: "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter&format=count'); ?>",
					success: function(response) {
						jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter").html("<p>"+response+" <?php echo JText::_("MOD_K2_FILTER_ACOUNTER_TEXT"); ?></p>");
						jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter").show();
					}
				});
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>();
				<?php endif; ?>
			}
		</script>
		
		<div class="acounter"></div>
  
  <?php endif; ?>

  <?php if($ajax_results == 1) : ?>
	<script type="text/javascript">
		
		function ajax_results<?php echo $module->id; ?>() {	
			jQuery("<?php echo $ajax_container; ?>").html("<p><img src='<?php echo JURI::root(); ?>media/k2/assets/images/system/loader.gif' /></p>");
		
			var url = "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter&format=raw'); ?>";
			var data = jQuery("#K2FilterBox<?php echo $module->id; ?> form").find(":input").filter(function () {
							return jQuery.trim(this.value).length > 0
						}).serialize();
						
			jQuery.ajax({
				data: data + "&format=raw",
				type: "get",
				url: url,
				success: function(response) {	
					jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter").hide();
					jQuery("<?php echo $ajax_container; ?>").html(response);				
					history.pushState({}, '', "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter'); ?>" + "?" + data);
				}
			});
		}

		jQuery(document).ready(function() {
			
			jQuery('body').on("click", "<?php echo $ajax_container; ?> div.k2Pagination a, <?php echo $ajax_container; ?> .pagination a", function() {
				jQuery("<?php echo $ajax_container; ?>").html("<p><img src='<?php echo JURI::root(); ?>media/k2/assets/images/system/loader.gif' /></p>");
				
				var module_pos = jQuery("<?php echo $ajax_container; ?>").offset();
				window.scrollTo(module_pos.left, module_pos.top - 70);
				
				var url = jQuery(this).attr('href');
				
				jQuery.ajax({
					type: "GET",
					url: url + "&format=raw",
					success: function(response) {
						jQuery("<?php echo $ajax_container; ?>").html(response);					
					}
				});
				return false;
			});
			
		});
		
	</script>
  
	<?php if($ajax_container == '.results_container') : ?>
    <div class="results_container"></div>
	<?php endif; ?>
  <?php endif; ?>
  
  <?php if($params->get("connected_fields") != "") : ?>
	
	<script type="text/javascript">
		
		jQuery(document).ready(function() {
			
			jQuery('select.child').each(function() {
				if(jQuery(this).siblings().find(".dynoloader").length == 0) {
					jQuery(this).parent().prepend("<div class='dynoloader' style='display: none;'><img src='<?php echo JURI::root(); ?>media/k2/assets/images/system/loader.gif' /></div>");
				}
			});

			jQuery.urlParam = function(name){
				var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
				if(results) {
					return results[1];
				}
				return '';
			}		
	
			jQuery('#K2FilterBox<?php echo $module->id; ?> select.connected:enabled').each(function() {
				var elem  = jQuery(this);
				var name  = encodeURIComponent(jQuery(this).attr("rel"));
				var value = encodeURIComponent(jQuery(this).find("option:selected").val());
				var index = jQuery(this).find("option:selected").index();

				var next  = jQuery(this).parents('.k2filter-cell').nextAll().find("select.connected").eq(0);
				
				var data = 'name='+name+'&value='+value+'&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
				
				<?php if($connected_show_all) : ?>
				if(index == 0) {
					var data_all = 'name='+name+'&value=getall&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
					jQuery.ajax({
						data: data_all,
						url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
						success: function(response) {
							next.append(response);
							next.removeAttr("disabled");
							var get_array = new Array();
							<?php foreach($_GET as $k=>$val) : ?>
							get_array.push("<?php echo $k; ?>");
							<?php endforeach; ?>

							next.find("option").each(function() {
								if(jQuery(this).attr("data-extra-id") !== undefined) {
									var param = 'searchword' + jQuery(this).attr("data-extra-id");
									if(jQuery.inArray(param, get_array) != -1) {
										var get_var = decodeURIComponent(jQuery.urlParam(param).replace(/\+/g, " "));
										if(jQuery(this).text() == get_var) {
											jQuery(this).attr('selected', 'selected');
										}
									}
								}
							});
						}
					});
				}
				<?php endif; ?>

				if(index != 0) {
					jQuery.ajax({
						data: data,
						url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
						success: function(response) {
							next.append(response);
							next.removeAttr("disabled");

							if(jQuery(response).filter("option").length != 0) {
								var nextId = next.find("option:last-child").text();
								next.find("option:last-child").remove();
							}
							if(nextId) {
								next.attr("name", "searchword"+nextId);
							}
							
							var param = 'searchword'+nextId;
							var get_var = decodeURIComponent(jQuery.urlParam(param).replace(/\+/g, " "));

							if(get_var != 'null') {
								next.find("option").each(function() {
									if(jQuery(this).text() == get_var) {
										jQuery(this).attr('selected', 'selected');
									}
								});
								
								next_checker  = next.parents('.k2filter-cell').next().find("select");
								if(next_checker.length == 0) {
									next_checker =  next.parents('.k2filter-row').next().find('.k2filter-cell:eq(0)').find("select");
								}

								if(next_checker.length != 0) {
									elem  = next;
									name  = encodeURIComponent(next.attr("rel"));
									value = encodeURIComponent(next.find("option:selected").val());
									index = next.find("option:selected").index();
									
									next  = next.parents('.k2filter-cell').nextAll().find("select.connected").eq(0);
									
									data = 'name='+name+'&value='+value+'&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
							
									jQuery.ajax({
										data: data,
										url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
										success: function(response) {
											next.append(response);
											next.removeAttr("disabled");

											if(jQuery(response).filter("option").length != 0) {
												var nextId = next.find("option:last-child").text();
												next.find("option:last-child").remove();
											}
											if(nextId) {
												next.attr("name", "searchword"+nextId);
											}
											
												param = 'searchword'+nextId;
												get_var = decodeURIComponent(jQuery.urlParam(param).replace(/\+/g, " "));
												
												if(get_var != 'null') {
													next.find("option").each(function() {
														if(jQuery(this).text() == get_var) {
															jQuery(this).attr('selected', 'selected');
														}
													});
													
													next_checker  = next.parents('.k2filter-cell').next().find("select");
													if(next_checker.length == 0) {
														next_checker =  next.parents('.k2filter-row').next().find('.k2filter-cell:eq(0)').find("select");
													}

													if(next_checker.length != 0) {
														elem  = next;
														name  = encodeURIComponent(next.attr("rel"));
														value = encodeURIComponent(next.find("option:selected").val());
														index = next.find("option:selected").index();
														
														next  = next.parents('.k2filter-cell').nextAll().find("select.connected").eq(0);
														
														data = 'name='+name+'&value='+value+'&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
													
														jQuery.ajax({
															data: data,
															url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
															success: function(response) {
																next.append(response);
																next.removeAttr("disabled");

																if(jQuery(response).filter("option").length != 0) {
																	var nextId = next.find("option:last-child").text();
																	next.find("option:last-child").remove();
																}
																if(nextId) {
																	next.attr("name", "searchword"+nextId);
																}
																
																	param = 'searchword'+nextId;
																	get_var = decodeURIComponent(jQuery.urlParam(param).replace(/\+/g, " "));
																	
																	if(get_var != 'null') {
																		next.find("option").each(function() {
																			if(jQuery(this).text() == get_var) {
																				jQuery(this).attr('selected', 'selected');
																			}
																		});
																	}
															}
														});
													};
												}
										}
									});
								};
							};
						}
					});
				}
			});
			
			jQuery('#K2FilterBox<?php echo $module->id; ?> select.connected').change(function() {
				var elem  = jQuery(this);
				var name  = encodeURIComponent(jQuery(this).attr("rel"));
				var value = encodeURIComponent(jQuery(this).find("option:selected").val());

				var index = jQuery(this).find("option:selected").index();
				
				var next  = jQuery(this).parents('.k2filter-cell').nextAll().find("select.connected").eq(0);
				
				next.parent().find('.dynoloader').show();
				
				<?php if($connected_show_all) : ?>
				var extra_id = jQuery(this).find("option:selected").attr('data-extra-id');
				if(extra_id !== undefined) {
					elem.attr("name", "searchword" + extra_id);
				}
				<?php endif; ?>
				
				if(!next.hasClass('connected') || jQuery(this).hasClass('lastchild')) {
					return;
				}
				
				var data = 'name='+name+'&value='+value+'&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
				
				//disable all next selects
				var elemIndex = jQuery('#K2FilterBox<?php echo $module->id; ?> select.connected').index(this);
				var nextAll  = jQuery(this).parents('#K2FilterBox<?php echo $module->id; ?>').find('select.connected:gt('+elemIndex+')');
				
				nextAll.each(function() {
					jQuery(this).attr("disabled", 'disabled');
					jQuery(this).find("option").not(':eq(0)').remove();
					if(jQuery(this).hasClass("lastchild")) {
						return false;
					}
				});
				
				if(index == 0) {
					<?php if($connected_show_all) : ?>
					var data_all = 'name='+name+'&value=getall&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
					jQuery.ajax({
						data: data_all,
						url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
						success: function(response) {
							next.append(response);
							next.removeAttr("disabled");
							next.parent().find('.dynoloader').hide();
						}
					});
					return false;
					<?php else : ?>
					next.parent().find('.dynoloader').hide();
					return false;
					<?php endif; ?>
				}

				jQuery.ajax({
					data: data,
					url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
					success: function(response) {
						next.find('option').not(':eq(0)').remove();
						next.append(response);
						next.removeAttr("disabled");
						next.parent().find('.dynoloader').hide();

						if(jQuery(response).filter("option").length != 0) {
							var nextId = next.find("option:last-child").text();
							next.find("option:last-child").remove();
						}
						if(nextId) {
							next.attr("name", "searchword"+nextId);
						}
						else {
							next.removeAttr("name");
						}
					}
				});
				
				<?php if($acounter) : ?>

					jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter").html("<p><img src='<?php echo JURI::root(); ?>media/k2/assets/images/system/loader.gif' /></p>");
						
					jQuery.ajax({
						data: jQuery("#K2FilterBox<?php echo $module->id; ?> form").serialize() + "&format=count",
						type: jQuery("#K2FilterBox<?php echo $module->id; ?> form").attr('method'),
						url: "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter&format=count'); ?>",
						success: function(response) {
							jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter").html("<p>"+response+" <?php echo JText::_("MOD_K2_FILTER_ACOUNTER_TEXT"); ?></p>");
							jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter").show();
						}
					});
					<?php if($onchange) : ?>
					submit_form_<?php echo $module->id; ?>();
					<?php endif; ?>
				
				<?php endif; ?>
				
				return false;
			});
		
		});
	
	</script>
  
  <?php endif; ?>
  
  <?php if($acompleter) : ?>
  
	<script type="text/javascript">
	
		jQuery(document).ready(function() {
			var availableTags<?php echo $module->id; ?> = [
				
				<?php 
				
					$restcata = 0;
					
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
				
					$tags = modK2FilterHelper::getTags($params, $restcata); 
					
				?>
				
				<?php foreach($tags as $k=>$tag) {
					echo "\"" . $tag->tag . "\"";
					if(($k+1) != count($tags)) {
						echo ", ";
					}
				}
				?>
			];
			
			jQuery("#K2FilterBox<?php echo $module->id; ?> input.inputbox").autocomplete({
				<?php if($acounter) : ?>
				select: function(event, ui) {
					jQuery(this).val(ui.item.value);
					acounter<?php echo $module->id; ?>()
				},
				<?php endif; ?>
				source: function(request, response) {
					var filteredArray = jQuery.map(availableTags<?php echo $module->id; ?>, function(item) {
						if(item.toUpperCase().indexOf(request.term.toUpperCase()) == 0){
							return item;
						}
						else{
							return null;
						}
					});
					response(filteredArray);
				}
			});
		});
	</script>
  
  <?php endif; ?>
  
  <?php if($allrequired) : ?>
	<script type="text/javascript">
		function check_required<?php echo $module->id; ?>() {
			var checker = 1;
			jQuery("#K2FilterBox<?php echo $module->id; ?> select, #K2FilterBox<?php echo $module->id; ?> input.inputbox").each(function() {
				if(jQuery(this).val() == "") {
					checker = 0;
				}
			});
			if(checker == 0) {
				return false;
			}
			return true;
		}
	</script>
  <?php endif; ?>
  
  <?php if($dynobox) : ?>	
		<script type="text/javascript">
			var checker = 0;
			var initbox = '';
			var init_selected_count = 0;
		
			jQuery(document).ready(function() {
				jQuery("#K2FilterBox<?php echo $module->id; ?> form").change(function(event) {
					dynobox<?php echo $module->id; ?>(event.target);
					if(checker == 0) {
						initbox = event.target;
						jQuery("#K2FilterBox<?php echo $module->id; ?> form").find("select").each(function() {
							if(this.value.length > 0) {
								init_selected_count++;
							}
						});
						checker = 1;
					}
					else {
						var init_selected = jQuery(event.target).find("option:selected");
						if(
							jQuery(event.target).attr("name") == jQuery(initbox).attr("name") 
							&& 
							(init_selected.hasClass("empty") || init_selected.length == 0)
						) {
							checker = 0;
							init_selected_count = 0;
						}
					}
				});
				
				jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-cell").each(function() {
					var select = jQuery(this).find("select");
					if(select.length > 0) {
						jQuery(this).prepend("<div class='dynoloader' style='display: none;'><img src='<?php echo JURI::root(); ?>media/k2/assets/images/system/loader.gif' /></div>");
					}
				});
			}); 
			
			function dynobox<?php echo $module->id; ?>(target) {
				var form = jQuery("#K2FilterBox<?php echo $module->id; ?> form");
				var url = "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter'); ?>";
				var fields = form.find("select");
				
				var parent_block = jQuery(target).parent().parent();
				form.find('div.k2filter-cell').not(parent_block).find(".dynoloader").show();
				
				var field_type = "";
				var field_id = "";
				<?php 
					foreach($field_types as $field) {
						echo "field_type += '&field_type[]={$field->type}';\r\n"; 
						echo "field_id += '&field_id[]={$field->id}';\r\n"; 
					}
				?>
				
				var data = form.find(":input").filter(function () {
								return jQuery.trim(this.value).length > 0
							}).serialize();
				
				var selected_count_current = 0;
				jQuery("#K2FilterBox<?php echo $module->id; ?> form").find("select").each(function() {
					if(this.value.length > 0) {
						selected_count_current++;
					}
				});

				jQuery.ajax({
					dataType: "json",
					data: data + "&format=dynobox" + field_type + field_id,
					type: "GET",
					url: url,
					success: function(res) {
						if(res.length > 0) {
							res.each(function(field) {
								if(field.name == 'category_select' || field.name == 'category_multiple_select') {
									var filter = form.find("select[name*=category]");

									if(typeof filter.attr("name") === 'undefined') {
										return;
									}
									
									filter.find("option").not(".empty").each(function(k) {
										var form_val = jQuery(this).val();
										if(
											jQuery.inArray(form_val, field.values) > -1 
											|| 
											(
												((target.value.length == 0 && selected_count_current == init_selected_count)
												||
												jQuery(target).attr("name") == jQuery(initbox).attr("name")
												)
												&& 
												jQuery(initbox).attr("name").indexOf("category") > -1
											)
										) {
											jQuery(this).show();
											if(filter.next().attr("type") == "button") {
												filter.multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).show();
											}
										}
										else {
											if(jQuery(target).attr("name") != filter.attr("name")) {
												jQuery(this).hide();
												if(filter.next().attr("type") == "button") {
													filter.multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).hide();
												}
											}
										}
									});	
								}
								else if(field.name == 'tag_select' || field.name == 'tag_multi_select') {
									var filter = form.find("select[name*=ftag]");
									if(filter.length == 0) {
										filter = form.find("select[name*=taga]");
									}
									if(typeof filter.attr("name") === 'undefined') {
										return;
									}
									
									filter.find("option").not(".empty").each(function(k) {
										var form_val = jQuery(this).text();
										if(
											jQuery.inArray(form_val, field.values) > -1 
											|| 
											(
												((target.value.length == 0 && selected_count_current == init_selected_count)
												||
												jQuery(target).attr("name") == jQuery(initbox).attr("name")
												)
												&& 
												(jQuery(initbox).attr("name").indexOf("ftag") > -1 
												|| jQuery(initbox).attr("name").indexOf("taga") > -1) 
											)
										) {
											jQuery(this).show();
											if(filter.next().attr("type") == "button") {
												filter.multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).show();
											}
										}
										else {
											if(jQuery(target).attr("name") != filter.attr("name")) {
												jQuery(this).hide();
												if(filter.next().attr("type") == "button") {
													filter.multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).hide();
												}
											}
										}
									});	
								}
								else {
									var filter = form.find("select[name=searchword"+field.id+"]");
									if(filter.length == 0) {
										filter = form.find("select[name=searchword"+field.id+"\\[\\]]");
									}
									if(filter.length == 0) {
										filter = form.find("select[name=array"+field.id+"\\[\\]]");
									}
									
									if(typeof filter.attr("name") === 'undefined') {
										return;
									}
									
									filter.find("option").not(".empty").each(function(k) {
										var form_val = jQuery(this).text();
										if(
											jQuery.inArray(form_val, field.values) > -1 
											|| 
											(
												((target.value.length == 0 && selected_count_current == init_selected_count)
												||
												jQuery(target).attr("name") == jQuery(initbox).attr("name")
												)
												&& 
												filter.attr("name") == jQuery(initbox).attr("name")
											)
										) {
											jQuery(this).show();
											if(filter.next().attr("type") == "button") {
												filter.multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).show();
											}
										}
										else {
											if(jQuery(target).attr("name") != filter.attr("name")) {
												jQuery(this).hide();
												if(filter.next().attr("type") == "button") {
													filter.multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).hide();
												}
											}
										}
									});									
								}
							});
						}
						else {
							form.find("select").each(function() {
								if(jQuery(target).attr("name") == jQuery(this).attr("name")) return;
								jQuery(this).find("option").not(".empty").hide();
								if(jQuery(this).next().attr("type") == "button") {
									jQuery(this).multiselect("widget").find(".ui-multiselect-checkboxes li").hide();
								}
							});
						}
						form.find(".dynoloader").hide();
					}
				});
			}
		</script>
  <?php endif; ?>
  
  <div style="clear:both;"></div>
</div><!-- k2-filter-box -->
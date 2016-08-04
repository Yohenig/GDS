<?php
/**
 * @version		$Id: generic.php 1492 2012-02-22 17:40:09Z joomlaworks@gmail.com $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<script type="text/javascript">

	if (typeof jQuery == 'undefined') {
		document.write('<scr'+'ipt type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery-1.8.2.min.js"></scr'+'ipt>');
		document.write('<scr'+'ipt>jQuery.noConflict();</scr'+'ipt>');
	}	
	
	//pagination fix
	jQuery(document).ready(function() {
		jQuery('div.k2Pagination a').each(function() {
			if(typeof jQuery(this).attr('href') === 'undefined') return;
			console.log(jQuery(this).attr('href'));
			jQuery(this).attr('href', jQuery(this).attr('href') + "&Itemid=<?php echo JRequest::getVar('Itemid'); ?>");
		});
	});

</script>

<!-- Start K2 Generic (search/date) Layout -->
<div id="k2Container" class="itemListView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')): ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<?php if($this->params->get('genericFeedIcon',1)): ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>
	
		<!--added K2FSM -->
		<?php if($this->resultf != "") : ?>
			<div class="sort-by-section clearfix search_res">
				<?php echo JText::_($this->resultf); ?>
				<?php if($this->result_count != 0) : ?>
				<?php echo "(".$this->result_count.")" ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		
		<?php
			require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
			$moduleId = JRequest::getInt("moduleId");
			$moduleParams = modK2FilterHelper::getModuleParams($moduleId);
		?>

		<?php if($moduleParams->ordering == 1 && $this->result_count != 0) : ?>
		<p style="float: right;">
			<select name="orderby" onChange="document.K2Filter<?php echo $moduleId; ?>.orderby.value=this.value; submit_form_<?php echo $moduleId; ?>();">
				
				<?php $filterLang =& JFactory::getLanguage();
					  $filterLang->load("mod_k2_filter");
				?>
			
				<option value=""><?php echo JText::_("MOD_K2_FILTER_ORDERING_VALUE"); ?></option>

				<option value="date" <?php if (JRequest::getVar('orderby') == "date") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "date") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_DATE'); ?></option>
				<option value="alpha" <?php if (JRequest::getVar('orderby') == "alpha") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "alpha") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_TITLE'); ?></option>
				<option value="order" <?php if (JRequest::getVar('orderby') == "order") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "order") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_ORDER'); ?></option>
				<option value="featured" <?php if (JRequest::getVar('orderby') == "featured") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "featured") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_FEATURED'); ?></option>
				<option value="hits" <?php if (JRequest::getVar('orderby') == "hits") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "hits") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_HITS'); ?></option>
				<option value="rand" <?php if (JRequest::getVar('orderby') == "rand") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "rand") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_RANDOM'); ?></option>
				<option value="best" <?php if (JRequest::getVar('orderby') == "best") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "best") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_RATING'); ?></option>
				<option value="id" <?php if (JRequest::getVar('orderby') == "id") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "id") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_ID'); ?></option>	
				
				<?php if($moduleParams->ordering_extra == 1) : ?>
					<?php foreach($this->extras as $extra) : ?>
					
						<option value="<?php echo $extra->id;?>" <?php if(JRequest::getVar("orderby") == $extra->id) echo "selected=selected"; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == $extra->id) echo "selected=selected"; ?>>
							<?php echo $extra->name; ?>
						</option>
					
					<?php endforeach; ?>
				<?php endif; ?>
				
				<option value="k2store" <?php if (JRequest::getVar('orderby') == "k2store") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "k2store") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_K2STORE'); ?></option>
				
			</select>
			
			<?php $order_method = JRequest::getVar("orderto", ""); ?>
			
			<?php if($order_method != "") : ?>
				<?php if($order_method == "desc") : ?>
					<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.orderto.value='asc'; submit_form_<?php echo $moduleId; ?>(); return false;">
						<img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/sort_desc.png" border="0" alt="<?php echo JText::_('MOD_K2_FILTER_ORDERING_DESC'); ?>" title="<?php echo JText::_('MOD_K2_FILTER_ORDERING_DESC'); ?>" width="12" height="12" />
					</a>		
				<?php else : ?>
					<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.orderto.value='desc'; submit_form_<?php echo $moduleId; ?>(); return false;">
						<img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/sort_asc.png" border="0" alt="<?php echo JText::_('MOD_K2_FILTER_ORDERING_ASC'); ?>" title="<?php echo JText::_('MOD_K2_FILTER_ORDERING_ASC'); ?>" width="12" height="12">
					</a>
				<?php endif; ?>
			<?php else : ?>
				<?php $order_method = $moduleParams->ordering_default_method; ?>

				<?php if($order_method == "desc") : ?>
					<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.orderto.value='asc'; submit_form_<?php echo $moduleId; ?>(); return false;">
						<img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/sort_desc.png" border="0" alt="<?php echo JText::_('MOD_K2_FILTER_ORDERING_DESC'); ?>" title="<?php echo JText::_('MOD_K2_FILTER_ORDERING_DESC'); ?>" width="12" height="12" />
					</a>		
				<?php else : ?>
					<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.orderto.value='desc'; submit_form_<?php echo $moduleId; ?>(); return false;">
						<img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/sort_asc.png" border="0" alt="<?php echo JText::_('MOD_K2_FILTER_ORDERING_ASC'); ?>" title="<?php echo JText::_('MOD_K2_FILTER_ORDERING_ASC'); ?>" width="12" height="12">
					</a>
				<?php endif; ?>
			<?php endif; ?>
			
		</p>
		<?php endif; ?>
		<div style="clear: both;"></div>
		
		<?php if($moduleParams->template_selector == 1 && $this->result_count != 0) : ?>
		<span class="template_selector" style="float: right;">
			<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.template_id.value='0'; submit_form_<?php echo $moduleId; ?>(); return false;"><img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/generic.png" /></a>
			<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.template_id.value='1'; submit_form_<?php echo $moduleId; ?>(); return false;"><img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/generic_table.png" /></a>
		</span>
		<div style="clear: both;"></div>
		<?php endif; ?>
		
		<!--///added K2FSM -->

	<?php if(count($this->items)): ?>
	<div class="genericItemList">
		<?php foreach($this->items as $item): ?>

		<!-- Start K2 Item Layout -->
		<div class="catItemView list_view groupLeading">
		   
		 <?php if($this->params->get('genericItemImage') && !empty($item->imageGeneric)): ?>
		  <!-- Item Image -->
		  <div class="listing_left col-md-4 col-sm-4 col-xs-12 no-padding-left">
			 <span class="catItemImage">
				<a href="<?php echo $item->link; ?>" title="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>">
					<img src="<?php echo $item->imageGeneric; ?>" alt="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>" style="width:<?php echo $this->params->get('itemImageGeneric'); ?>px; height:auto;" />
				</a>
			  </span>
		  </div>
		  <?php endif; ?>
			  
		<div class="listing_middle col-md-6 col-sm-6 col-xs-12"> 	  
			<!-- Plugins: BeforeDisplay -->
			<?php echo $item->event->BeforeDisplay; ?>

			<!-- K2 Plugins: K2BeforeDisplay -->
			<?php echo $item->event->K2BeforeDisplay; ?>

			<div class="catItemHeader">
				<?php if($this->params->get('genericItemDateCreated')): ?>
				<!-- Date created -->
				<span class="catItemDateCreated">
					<?php echo JHTML::_('date', $item->created , JText::_('K2_DATE_FORMAT_LC2')); ?>
				</span>
				<?php endif; ?>
			
			 <div class="catHeaderLeft col-md-7 col-sm-12 col-xs-12 no-padding">
				  <?php if($this->params->get('genericItemTitle')): ?>
				  <!-- Item title -->
				  <h3 class="catItemTitle">
					<?php if ($this->params->get('genericItemTitleLinked')): ?>
						<a href="<?php echo $item->link; ?>">
						<?php echo $item->title; ?>
					</a>
					<?php else: ?>
					<?php echo $item->title; ?>
					<?php endif; ?>
					
					<?php if($item->params->get('genericItemExtraFields') && count($item->extra_fields)): ?>
					  <!-- Location extra fields -->
					 
							<?php foreach ($item->extra_fields as $key=>$extraField): ?>
							<?php if($extraField->value != ''): ?>
							 <?php if($extraField->name == 'Location'): ?>
							  <span class="catItemExtraFieldsValue"><i class="fa fa-map-marker"></i> <?php echo $extraField->value; ?></span>
							<?php endif; ?>
							<?php endif; ?>
							<?php endforeach; ?>
						
					  <?php endif; ?> 
				  </h3>
				  <?php endif; ?>
			</div><!-- End catHeaderLeft  -->
			
			<div class="catHeaderRight col-md-5 col-sm-12 col-xs-12 no-padding">
				  <?php if($item->params->get('genericItemExtraFields') && count($item->extra_fields)): ?>
				  <!-- Item extra fields -->
				 
						<?php foreach ($item->extra_fields as $key=>$extraField): ?>
						<?php if($extraField->value != ''): ?>
						 <?php if($extraField->name == 'Hotel Features'): ?>
						  <?php
							$fea=explode(",",$extraField->value);
							//print_r($fea);
							for($i=0; $i<count($fea); $i++){
							?>
							<span class="fa-stack fa-lg">
								<i class="<?php echo $fea[$i];?> fa-stack-1x"></i>
						   </span>
						<?php } endif; ?>
						<?php endif; ?>
						<?php endforeach; ?>
					
				  <?php endif; ?> 
	       </div><!-- End catHeaderRight  -->
		  
		  </div><!-- End Cat iTem header -->
		  
		  <!-- Plugins: AfterDisplayTitle -->
		  <?php echo $item->event->AfterDisplayTitle; ?>

		  <!-- K2 Plugins: K2AfterDisplayTitle -->
		  <?php echo $item->event->K2AfterDisplayTitle; ?>

		   <div class="catItemBody">
		  
			  <!-- Plugins: BeforeDisplayContent -->
			  <?php echo $item->event->BeforeDisplayContent; ?>

			  <!-- K2 Plugins: K2BeforeDisplayContent -->
			  <?php echo $item->event->K2BeforeDisplayContent; ?>
		  
			  
			  
			  <?php if($this->params->get('genericItemIntroText')): ?>
			  <!-- Item introtext -->
			  <div class="catItemIntroText">
			  	<?php echo $item->introtext; ?>
			  </div>
			  <?php endif; ?>
			  
			  <!-- Plugins: AfterDisplayContent -->
			  <?php echo $item->event->AfterDisplayContent; ?>

			  <!-- K2 Plugins: K2AfterDisplayContent -->
			  <?php echo $item->event->K2AfterDisplayContent; ?>

			  <div class="clr"></div>
		  </div>
		  
		</div><!-- End Listing Middle -->
		  
		  <div class="listing_right col-md-2 col-sm-2 col-xs-12 no-padding">
		  <?php if($this->params->get('genericItemExtraFields') && count($item->extra_fields)): ?>
		  <!-- Item extra fields -->  
		   <div class="catItemExtraFields">
			<ul>
				<?php foreach ($item->extra_fields as $key=>$extraField): ?>
				<?php if($extraField->value != ''): ?>
				<?php if(($extraField->name == 'Price') OR ($extraField->name == 'Room')): ?>
				<li class="<?php echo ($key%2) ? "odd" : "even"; ?> type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?>">
					
					<span class="catItemExtraFieldsValue"><?php echo $extraField->value; ?></span>
				</li>
				<?php endif; ?>
				<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<div class="clr"></div>
	    </div>
		  <?php endif; ?>
		  
		  <!-- Plugins: AfterDisplay -->
		  <?php echo $item->event->AfterDisplay; ?>

		  <!-- K2 Plugins: K2AfterDisplay -->
		  <?php echo $item->event->K2AfterDisplay; ?>
		  
			<?php if($this->params->get('genericItemCategory')): ?>
			<!-- Item category name -->
			<div class="genericItemCategory">
				<span><?php echo JText::_('K2_PUBLISHED_IN'); ?></span>
				<a href="<?php echo $item->category->link; ?>"><?php echo $item->category->name; ?></a>
			</div>
			<?php endif; ?>
			
			<?php if ($this->params->get('genericItemReadMore')): ?>
			<!-- Item "read more..." link -->
			<div class="catItemReadMore">
				<a class="k2ReadMore" href="<?php echo $item->link; ?>">
					<?php echo JText::_('K2_READ_MORE'); ?>
				</a>
			</div>
			<?php endif; ?>
			
			<div class="catItemRatingBlock">
				<div class="itemRatingForm">
				  <div id="itemRatingLog<?php echo $item->id; ?>" class="itemRatingLog"><?php echo $item->numOfvotes; ?></div>
					<ul class="itemRatingList">
						<li class="itemCurrentRating" id="itemCurrentRating<?php echo $item->id; ?>" style="width:<?php echo $item->votingPercentage; ?>%;"></li>
						<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_1_STAR_OUT_OF_5'); ?>" class="one-star">1</a></li>
						<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_2_STARS_OUT_OF_5'); ?>" class="two-stars">2</a></li>
						<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_3_STARS_OUT_OF_5'); ?>" class="three-stars">3</a></li>
						<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_4_STARS_OUT_OF_5'); ?>" class="four-stars">4</a></li>
						<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_5_STARS_OUT_OF_5'); ?>" class="five-stars">5</a></li>
					</ul>
				</div>
					<div class="clr"></div>
		    </div>

			</div><!-- End Rigt LIsting  -->
		</div>
		<!-- End K2 Item Layout -->
		
		<?php endforeach; ?>
	</div>

	<!-- Pagination -->
	<?php if($this->pagination->getPagesLinks()): ?>
	<div class="k2Pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
		<div class="clr"></div>
		<?php echo $this->pagination->getPagesCounter(); ?>
	</div>
	<?php endif; ?>

	<?php endif; ?>
	
</div>
<!-- End K2 Generic (search/date) Layout -->

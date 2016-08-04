<?php
/**
 * @version		2.6.x
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

?>

<!-- Start K2 Tag Layout -->
<div id="k2Container" class="tagView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')): ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<?php if($this->params->get('tagFeedIcon',1)): ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

	<?php if(count($this->items)): ?>
	<div class="tagItemList">
		<?php foreach($this->items as $item): ?>

		<!-- Start K2 Item Layout -->
		<div class="tagItemView catItemView list_view groupLeading">
			
			<?php if($item->params->get('tagItemImage',1) && !empty($item->imageGeneric)): ?>
			  <!-- Item Image -->
			 <div class="listing_left col-md-4 col-sm-4 col-xs-12 no-padding-left">
				 <span class="catItemImage">
				    <a href="<?php echo $item->link; ?>" title="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>">
				    	<img src="<?php echo $item->imageGeneric; ?>" alt="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>" style="width:<?php echo $item->params->get('itemImageGeneric'); ?>px; height:auto;" />
				    </a>
				  </span>
			  </div>
			  <?php endif; ?>
			
		<div class="listing_middle col-md-6 col-sm-6 col-xs-12"> 
			<div class="catItemHeader">
				<?php if($item->params->get('tagItemDateCreated',1)): ?>
				<!-- Date created -->
				<span class="tagItemDateCreated">
					<?php echo JHTML::_('date', $item->created , JText::_('K2_DATE_FORMAT_LC2')); ?>
				</span>
				<?php endif; ?>
			<div class="catHeaderLeft col-md-7 col-sm-12 col-xs-12 no-padding">
			  <?php if($item->params->get('tagItemTitle',1)): ?>
			  <!-- Item title -->
			 <h3 class="catItemTitle">
			  	<?php if ($item->params->get('tagItemTitleLinked',1)): ?>
					<a href="<?php echo $item->link; ?>">
			  		<?php echo $item->title; ?>
			  	</a>
			  	<?php else: ?>
			  	<?php echo $item->title; ?>
			  	<?php endif; ?>
				
				<?php if($item->params->get('tagItemExtraFields',0) && count($item->extra_fields)): ?>
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
			</div>
			
			<div class="catHeaderRight col-md-5 col-sm-12 col-xs-12 no-padding">
				 <?php if($item->params->get('tagItemExtraFields',0) && count($item->extra_fields)): ?>
				  <!-- Item extra fields -->
				 
						<?php foreach ($item->extra_fields as $key=>$extraField): ?>
						<?php if($extraField->value != ''): ?>
						 <?php if($extraField->name == 'Inflight Features'): ?>
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
			</div>
	   
		  </div>

		  <div class="catItemBody">
			  
			  
			  <?php if($item->params->get('tagItemIntroText',1)): ?>
			  <!-- Item introtext -->
			 <div class="catItemIntroText">
			  	<?php echo $item->introtext; ?>
			  </div>
			  <?php endif; ?>

			  <div class="clr"></div>
		  </div>
		  
		  <div class="clr"></div>
		</div> 
		<div class="listing_right col-md-2 col-sm-2 col-xs-12 no-padding">
		  <?php if($item->params->get('tagItemExtraFields',0) && count($item->extra_fields)): ?>
		
			  <!-- Item extra fields -->
			  <div class="catItemExtraFields">
					<ul>
						<?php foreach ($item->extra_fields as $key=>$extraField): ?>
						<?php if($extraField->value != ''): ?>
						<?php if(($extraField->name == 'Base fare') OR ($extraField->name == 'Airline')): ?>
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
		  
			<?php if($item->params->get('tagItemCategory')): ?>
			<!-- Item category name -->
			<div class="tagItemCategory">
				<span><?php echo JText::_('K2_PUBLISHED_IN'); ?></span>
				<a href="<?php echo $item->category->link; ?>"><?php echo $item->category->name; ?></a>
			</div>
			<?php endif; ?>
			
			<?php if ($item->params->get('tagItemReadMore')): ?>
			<!-- Item "read more..." link -->
			<div class="catItemReadMore">
				<a class="k2ReadMore" href="<?php echo $item->link; ?>">
					<?php echo JText::_('K2_READ_MORE'); ?>
				</a>
			</div>
			<?php endif; ?>
            
			
		  </div>	
		</div>
		<!-- End K2 Item Layout -->
		<div class="clr"></div>
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
<!-- End K2 Tag Layout -->

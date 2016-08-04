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
$app_deal = JFactory::getApplication();
		 $template_deal   = $app_deal->getTemplate(true);
		 $params_deal     = $template_deal->params;
		 $deal_img=$params_deal->get('deal_img');
		 $deal_title=$params_deal->get('deal_title');
		 
		 $tit=explode(' ',$deal_title);	
		 //print_r($tit);
		 //$title1 = substr($deal_title, 0, strpos($deal_title, ' '));
		 //$title2 = substr($deal_title, strpos($deal_title, ' '));
?>      
<div class="service-bg">			
				<img src="<?php echo $deal_img;?>" alt="service-bg"/>
				<h3><?php echo $tit[0];?><span class="service_span"> <?php echo $tit[1]; ?> <?php echo $tit[2]; ?></span>
				<br>
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tit[3];?><span class="service_span"> <?php echo $tit[4];?> <?php echo $tit[5];?></span>
				</h3>
				
			</div>
<div id="k2ModuleBox<?php echo $module->id; ?>" class="k2ItemsBlock<?php if($params->get('moduleclass_sfx')) echo ' '.$params->get('moduleclass_sfx'); ?>">

	<?php if($params->get('itemPreText')): ?>
	<p class="modulePretext"><?php echo $params->get('itemPreText'); ?></p>
	<?php endif; ?>

	<?php if(count($items)): ?>
  <div>
    <?php foreach ($items as $key=>$item):	?>
    <div class="col-md-6 <?php if(count($items)==$key+1) echo ' lastItem'; ?>">
      <div class="service-box-inner">
      <!-- Plugins: BeforeDisplay -->
      <?php echo $item->event->BeforeDisplay; ?>

      <!-- K2 Plugins: K2BeforeDisplay -->
      <?php echo $item->event->K2BeforeDisplay; ?>
	  
	   <?php if($params->get('itemImage')): ?>
      <div class="deal_img col-md-4 col-sm-5 <?php echo ($key%2) ? "" : "pull-right";?>">
	      <?php if($params->get('itemImage') && isset($item->image)): ?>
	      <a class="moduleItemImage" href="<?php echo $item->link; ?>" title="<?php echo JText::_('K2_CONTINUE_READING'); ?> &quot;<?php echo K2HelperUtilities::cleanHtml($item->title); ?>&quot;">
	      	<img class="img-circle" src="<?php echo $item->image; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($item->title); ?>"/>
			 
			  <?php if($params->get('itemExtraFields') && count($item->extra_fields)): ?>
					<?php foreach ($item->extra_fields as $extraField): ?>
							<?php if($extraField->value != '' and $extraField->name=='Font Awesome Icon' ): ?>
							 <div class="deal_fa_icon">
								<i class="<?php echo $extraField->value; ?>"></i>
							  </div>	
							<?php endif; ?>
					<?php endforeach; ?>
			  <?php endif; ?>
	      </a>
	      <?php endif; ?>
       
      </div>
      <?php endif; ?>
	
	 

     

      <!-- Plugins: AfterDisplayTitle -->
      <?php echo $item->event->AfterDisplayTitle; ?>

      <!-- K2 Plugins: K2AfterDisplayTitle -->
      <?php echo $item->event->K2AfterDisplayTitle; ?>

      <!-- Plugins: BeforeDisplayContent -->
      <?php echo $item->event->BeforeDisplayContent; ?>

      <!-- K2 Plugins: K2BeforeDisplayContent -->
      <?php echo $item->event->K2BeforeDisplayContent; ?>
       
	  <div class="col-md-8 content-box col-sm-7"> 
		  <?php if($params->get('itemTitle')): ?>
		  <h3 class="block-title">
			<a class="moduleItemTitle" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
		 </h3>
		  <?php endif; ?>
		  
		  <?php if($params->get('itemIntroText')): ?>
			<?php echo $item->introtext; ?>
		  <?php endif; ?>  
	  </div>
	   
      <?php if($params->get('itemAuthorAvatar')): ?>
      <a class="k2Avatar moduleItemAuthorAvatar" rel="author" href="<?php echo $item->authorLink; ?>">
				<img src="<?php echo $item->authorAvatar; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($item->author); ?>" style="width:<?php echo $avatarWidth; ?>px;height:auto;" />
			</a>
      <?php endif; ?>
  
    
		
      <?php if($params->get('itemAuthor')): ?>
      <div class="moduleItemAuthor">
	      <?php echo K2HelperUtilities::writtenBy($item->authorGender); ?>
	
				<?php if(isset($item->authorLink)): ?>
				<a rel="author" title="<?php echo K2HelperUtilities::cleanHtml($item->author); ?>" href="<?php echo $item->authorLink; ?>"><?php echo $item->author; ?></a>
				<?php else: ?>
				<?php echo $item->author; ?>
				<?php endif; ?>
				
				<?php if($params->get('userDescription')): ?>
				<?php echo $item->authorDescription; ?>
				<?php endif; ?>
				
			</div>
			<?php endif; ?>

      

     

      <?php if($params->get('itemVideo')): ?>
      <div class="moduleItemVideo">
      	<?php echo $item->video ; ?>
      	<span class="moduleItemVideoCaption"><?php echo $item->video_caption ; ?></span>
      	<span class="moduleItemVideoCredits"><?php echo $item->video_credits ; ?></span>
      </div>
      <?php endif; ?>

      

      <!-- Plugins: AfterDisplayContent -->
      <?php echo $item->event->AfterDisplayContent; ?>

      <!-- K2 Plugins: K2AfterDisplayContent -->
      <?php echo $item->event->K2AfterDisplayContent; ?>

      <?php if($params->get('itemDateCreated')): ?>
      <span class="moduleItemDateCreated"><?php echo JText::_('K2_WRITTEN_ON') ; ?> <?php echo JHTML::_('date', $item->created, JText::_('K2_DATE_FORMAT_LC2')); ?></span>
      <?php endif; ?>

      <?php if($params->get('itemCategory')): ?>
      <?php echo JText::_('K2_IN') ; ?> <a class="moduleItemCategory" href="<?php echo $item->categoryLink; ?>"><?php echo $item->categoryname; ?></a>
      <?php endif; ?>

      <?php if($params->get('itemTags') && count($item->tags)>0): ?>
      <div class="moduleItemTags">
      	<b><?php echo JText::_('K2_TAGS'); ?>:</b>
        <?php foreach ($item->tags as $tag): ?>
        <a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if($params->get('itemAttachments') && count($item->attachments)): ?>
			<div class="moduleAttachments">
				<?php foreach ($item->attachments as $attachment): ?>
				<a title="<?php echo K2HelperUtilities::cleanHtml($attachment->titleAttribute); ?>" href="<?php echo $attachment->link; ?>"><?php echo $attachment->title; ?></a>
				<?php endforeach; ?>
			</div>
      <?php endif; ?>

			<?php if($params->get('itemCommentsCounter') && $componentParams->get('comments')): ?>		
				<?php if(!empty($item->event->K2CommentsCounter)): ?>
					<!-- K2 Plugins: K2CommentsCounter -->
					<?php echo $item->event->K2CommentsCounter; ?>
				<?php else: ?>
					<?php if($item->numOfComments>0): ?>
					<a class="moduleItemComments" href="<?php echo $item->link.'#itemCommentsAnchor'; ?>">
						<?php echo $item->numOfComments; ?> <?php if($item->numOfComments>1) echo JText::_('K2_COMMENTS'); else echo JText::_('K2_COMMENT'); ?>
					</a>
					<?php else: ?>
					<a class="moduleItemComments" href="<?php echo $item->link.'#itemCommentsAnchor'; ?>">
						<?php echo JText::_('K2_BE_THE_FIRST_TO_COMMENT'); ?>
					</a>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if($params->get('itemHits')): ?>
			<span class="moduleItemHits">
				<?php echo JText::_('K2_READ'); ?> <?php echo $item->hits; ?> <?php echo JText::_('K2_TIMES'); ?>
			</span>
			<?php endif; ?>

			<?php if($params->get('itemReadMore') && $item->fulltext): ?>
			<a class="moduleItemReadMore" href="<?php echo $item->link; ?>">
				<?php echo JText::_('K2_READ_MORE'); ?>
			</a>
			<?php endif; ?>

      <!-- Plugins: AfterDisplay -->
      <?php echo $item->event->AfterDisplay; ?>

      <!-- K2 Plugins: K2AfterDisplay -->
      <?php echo $item->event->K2AfterDisplay; ?>
       </div>
      
    </div>
    <?php endforeach; ?>
    
  </div>
  <?php endif; ?>

	<?php if($params->get('itemCustomLink')): ?>
	<a class="moduleCustomLink" href="<?php echo $params->get('itemCustomLinkURL'); ?>" title="<?php echo K2HelperUtilities::cleanHtml($itemCustomLinkTitle); ?>"><?php echo $itemCustomLinkTitle; ?></a>
	<?php endif; ?>

	<?php if($params->get('feed')): ?>
	<div class="k2FeedIcon">
		<a href="<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&format=feed&moduleID='.$module->id); ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

</div>

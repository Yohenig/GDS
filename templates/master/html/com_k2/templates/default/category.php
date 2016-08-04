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

<?php
	//List and Grid View
		$list=1;
		$grid=0;
		$get = JRequest::get('get');
		if(isset($get['display']) && $get['display']=='grid'){
			$grid=1;
			$list=0;
		}else if(isset($get['display']) && $get['display']=='list')
		{
			$grid=0;
			$list=1;	
		}else { $grid=0; $list=1;}
		//echo 'Joomla current URI is ' . JURI::current() . "\n";
		
		// Shorting on Rating
		$asc_rating=0;
		$dsc_rating=1;
		$get_sort_rating = JRequest::get('get_sort_rating');
		if(isset($get_sort_rating['rating']) && $get_sort_rating['rating']=='asc'){
			$asc_rating=1;
		$dsc_rating=0;
		}else if(isset($get_sort_rating['rating']) && $get_sort_rating['rating']=='dsc')
		{
			$asc_rating=0;
		$dsc_rating=1;	
		}else { $asc_rating=0;
		$dsc_rating=0;}
		
		// Shorting on Title
		$asc_name=0;
		$dsc_name=1;
		$get_sort_name = JRequest::get('get_sort_name');
		if(isset($get_sort_name['name']) && $get_sort_name['name']=='asc'){
			$asc_name=1;
		$dsc_name=0;
		}else if(isset($get_sort_name['name']) && $get_sort_name['name']=='dsc')
		{
			$asc_name=0;
		$dsc_name=1;	
		}else { $asc_name=0;
		$dsc_name=0;}
		
		// Shorting on Hits
		$asc_hits=0;
		$dsc_hits=1;
		$get_sort_hits = JRequest::get('get_sort_hits');
		if(isset($get_sort_hits['hits']) && $get_sort_hits['hits']=='asc'){
			$asc_hits=1;
		$dsc_hits=0;
		}else if(isset($get_sort_hits['hits']) && $get_sort_hits['hits']=='dsc')
		{
			$asc_hits=0;
		$dsc_hits=1;	
		}else { $asc_hits=0;
		$dsc_hits=0;}
	 ?>
	 
	<script type="text/javascript">
	
	function showListViewJS()
	{ 		
	  window.location.href="<?php echo JURI::current();?>?display=list";
	}
	function showGridViewJS()
	{
		window.location.href="<?php echo JURI::current();?>?display=grid";
	}
	function ascRatingShortingJS()
	{
		window.location.href="<?php echo JURI::current();?>?rating=asc";
	}
	function dscRatingShortingJS()
	{
		window.location.href="<?php echo JURI::current();?>?rating=dsc";
	}
	function ascNameShortingJS()
	{
		window.location.href="<?php echo JURI::current();?>?name=asc";
	}
	function dscNameShortingJS()
	{
		window.location.href="<?php echo JURI::current();?>?name=dsc";
	}
	function ascHitsShortingJS()
	{
		window.location.href="<?php echo JURI::current();?>?hits=asc";
	}
	function dscHitsShortingJS()
	{
		window.location.href="<?php echo JURI::current();?>?hits=dsc";
	}
	
	
	</script>

<!-- Start K2 Category Layout -->
<div id="k2Container" class="sort_by itemListView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')): ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<?php if($this->params->get('catFeedIcon')): ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

	<?php if(isset($this->category) || ( $this->params->get('subCategories') && isset($this->subCategories) && count($this->subCategories) )): ?>
	<!-- Blocks for current category and subcategories -->
	<div class="itemListCategoriesBlock">

		<?php if(isset($this->category) && ( $this->params->get('catImage') || $this->params->get('catTitle') || $this->params->get('catDescription') || $this->category->event->K2CategoryDisplay )): ?>
		<!-- Category block -->
		<div class="itemListCategory">

			<?php if(isset($this->addLink)): ?>
			<!-- Item add link -->
			<span class="catItemAddLink">
				<a class="modal" rel="{handler:'iframe',size:{x:990,y:650}}" href="<?php echo $this->addLink; ?>">
					<?php echo JText::_('K2_ADD_A_NEW_ITEM_IN_THIS_CATEGORY'); ?>
				</a>
			</span>
			<?php endif; ?>

			<?php if($this->params->get('catImage') && $this->category->image): ?>
			<!-- Category image -->
			<img alt="<?php echo K2HelperUtilities::cleanHtml($this->category->name); ?>" src="<?php echo $this->category->image; ?>" style="width:<?php echo $this->params->get('catImageWidth'); ?>px; height:auto;" />
			<?php endif; ?>

			<?php if($this->params->get('catTitle')): ?>
			<!-- Category title -->
			<h2><?php echo $this->category->name; ?><?php if($this->params->get('catTitleItemCounter')) echo ' ('.$this->pagination->total.')'; ?></h2>
			<?php endif; ?>

			<?php if($this->params->get('catDescription')): ?>
			<!-- Category description -->
			<p><?php echo $this->category->description; ?></p>
			<?php endif; ?>

			<!-- K2 Plugins: K2CategoryDisplay -->
			<?php echo $this->category->event->K2CategoryDisplay; ?>

			<div class="clr"></div>
		</div>
		<?php endif; ?>

		<?php if($this->params->get('subCategories') && isset($this->subCategories) && count($this->subCategories)): ?>
		<!-- Subcategories -->
		<div class="itemListSubCategories">
			<h3><?php echo JText::_('K2_CHILDREN_CATEGORIES'); ?></h3>

			<?php foreach($this->subCategories as $key=>$subCategory): ?>

			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('subCatColumns'))==0))
				$lastContainer= ' subCategoryContainerLast';
			else
				$lastContainer='';
			?>

			<div class="subCategoryContainer<?php echo $lastContainer; ?>"<?php echo (count($this->subCategories)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('subCatColumns'), 1).'%;"'; ?>>
				<div class="subCategory">
					<?php if($this->params->get('subCatImage') && $subCategory->image): ?>
					<!-- Subcategory image -->
					<a class="subCategoryImage" href="<?php echo $subCategory->link; ?>">
						<img alt="<?php echo K2HelperUtilities::cleanHtml($subCategory->name); ?>" src="<?php echo $subCategory->image; ?>" />
					</a>
					<?php endif; ?>

					<?php if($this->params->get('subCatTitle')): ?>
					<!-- Subcategory title -->
					<h2>
						<a href="<?php echo $subCategory->link; ?>">
							<?php echo $subCategory->name; ?><?php if($this->params->get('subCatTitleItemCounter')) echo ' ('.$subCategory->numOfItems.')'; ?>
						</a>
					</h2>
					<?php endif; ?>

					<?php if($this->params->get('subCatDescription')): ?>
					<!-- Subcategory description -->
					<p><?php echo $subCategory->description; ?></p>
					<?php endif; ?>

					<!-- Subcategory more... -->
					<a class="subCategoryMore" href="<?php echo $subCategory->link; ?>">
						<?php echo JText::_('K2_VIEW_ITEMS'); ?>
					</a>

					<div class="clr"></div>
				</div>
			</div>
			<?php if(($key+1)%($this->params->get('subCatColumns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>

			<div class="clr"></div>
		</div>
		<?php endif; ?>

	</div>
	<?php endif; ?>



	<?php if((isset($this->leading) || isset($this->primary) || isset($this->secondary) || isset($this->links)) && (count($this->leading) || count($this->primary) || count($this->secondary) || count($this->links))): ?>
	
	
	<div class="sort-by-section clearfix">
		<h4 class="sort-by-title block-sm"><?php echo JText::_('K2_SORT_RESULT'); ?>:</h4>
		<ul class="sort-bar clearfix block-sm">
			<li class="sort-by-name">
			  <span><?php echo JText::_('K2_NAME'); ?></span>
		     
			   <div class="sorting_data">
				<?php if(isset($_REQUEST['name'])){ ?>
				<?php if($_REQUEST['name']=='dsc'){ ?>
				<a onclick="ascNameShortingJS()" title="Ascending Order" class="ascending"><i class="fa fa-caret-up"></i></a>
				<a title="Descending order" class="active_shorting"><i class="fa fa-caret-down"></i></a> 
				
				<?php } else{ ?>
				<a title="Ascending Order" class="ascending active_shorting"><i class="fa fa-caret-up"></i></a>
				  <a onclick="dscNameShortingJS()" title="Descending order"><i class="fa fa-caret-down"></i></a> 
					
				<?php }	?>
				<?php }else{ ?>
				<a onclick="ascNameShortingJS()" title="Ascending Order" class="ascending"><i class="fa fa-caret-up"></i></a>
				 <a title="Descending order" class="active_shorting"><i class="fa fa-caret-down"></i></a> 
				
				<?php } ?>
			   </div>
	
			
			</li>
			<li class="sort-by-rating"><span><?php echo JText::_('K2_SHORT_RATING'); ?></span>
				<div class="sorting_data">
				<?php if(isset($_REQUEST['rating'])){ ?>
				<?php if($_REQUEST['rating']=='dsc'){ ?>
				<a onclick="ascRatingShortingJS()" title="Ascending Order" class="ascending"><i class="fa fa-caret-up"></i></a>
				<a title="Descending order" class="active_shorting"><i class="fa fa-caret-down"></i></a> 
				
				<?php } else{ ?>
				<a title="Ascending Order" class="ascending active_shorting"><i class="fa fa-caret-up"></i></a>
				  <a onclick="dscRatingShortingJS()" title="Descending order"><i class="fa fa-caret-down"></i></a> 
					
				<?php }	?>
				<?php }else{ ?>
				<a onclick="ascRatingShortingJS()" title="Ascending Order" class="ascending"><i class="fa fa-caret-up"></i></a>
				 <a title="Descending order" class="active_shorting"><i class="fa fa-caret-down"></i></a> 
				
				<?php } ?>
			   </div>
			</li>
			<li class="sort-by-popularity"><span><?php echo JText::_('K2_SHORT_POPULARITY'); ?></span>
			
				 <div class="sorting_data">
				<?php if(isset($_REQUEST['hits'])){ ?>
				<?php if($_REQUEST['hits']=='dsc'){ ?>
				<a onclick="ascHitsShortingJS()" title="Ascending Order" class="ascending"><i class="fa fa-caret-up"></i></a>
				<a title="Descending order" class="active_shorting"><i class="fa fa-caret-down"></i></a> 
				
				<?php } else{ ?>
				<a title="Ascending Order" class="ascending active_shorting"><i class="fa fa-caret-up"></i></a>
				  <a onclick="dscHitsShortingJS()" title="Descending order"><i class="fa fa-caret-down"></i></a> 
					
				<?php }	?>
				<?php }else{ ?>
				<a onclick="ascHitsShortingJS()" title="Ascending Order" class="ascending"><i class="fa fa-caret-up"></i></a>
				 <a title="Descending order" class="active_shorting"><i class="fa fa-caret-down"></i></a> 
				
				<?php } ?>
			   </div>
			</li>
		</ul>
                                
		<ul class="swap-tiles clearfix block-sm">
		  <?php if(isset($_REQUEST['display'])){ ?>
				<?php if($_REQUEST['display']=='list'){ ?>
				 <li class="swap-grid">
					<a onclick="showGridViewJS()" title="<?php echo JText::_('K2_GRID_VIEW'); ?>"><i class="fa fa-th"></i></a> 
				 </li>
				  <li class="swap-list active">										 
					<a title="<?php echo JText::_('K2_LIST_VIEW'); ?>" class="ListView"><i class="fa fa-bars"></i></a>
				  </li>	
					<?php }else{ ?>
					<li class="swap-grid active">
						<a title="<?php echo JText::_('K2_GRID_VIEW'); ?>"><i class="fa fa-th"></i></a> 
					</li>
					<li class="swap-list">
						<a onclick="showListViewJS()" title="<?php echo JText::_('K2_LIST_VIEW'); ?>" class="ListView"><i class="fa fa-bars"></i></a>
					</li>	
					<?php } ?>
					<?php }else{ ?>
					 <li class="swap-grid">
						<a onclick="showGridViewJS()" title="<?php echo JText::_('K2_GRID_VIEW'); ?>"><i class="fa fa-th"></i></a> 
					 </li>
					 <li class="swap-list active">										 
						<a title="<?php echo JText::_('K2_LIST_VIEW'); ?>" class="ListView"><i class="fa fa-bars"></i></a>
					 </li>	
				<?php } ?>
		   
		</ul>
    </div>
	<!-- Item list -->
	<div class="itemList">
            
		<?php if(isset($this->leading) && count($this->leading)):
			
		   // Shorting on Rating
			if($asc_rating==0 && $dsc_rating==1){
				
		    usort($this->leading, function($a, $b)
			{
			return $a->votingPercentage < $b->votingPercentage;
			
			});
		  }
		   else if($asc_rating==1 && $dsc_rating==0)
		    {
				  
			 usort($this->leading, function($a, $b)
			{
				return $a->votingPercentage > $b->votingPercentage;
			
			});
		   }
		   
		   // Shorting on Name(Title)
			if($asc_name==0 && $dsc_name==1)
			{	
				usort($this->leading, function($a, $b)
				{
					return strcmp($b->title, $a->title);
				});
			}
			else if($asc_name==1 && $dsc_name==0)
		    {
				usort($this->leading, function($a, $b)
				{
				   return strcmp($a->title, $b->title);
			
				});
			}
			
			 // Shorting on Name(Title)
			if($asc_hits==0 && $dsc_hits==1)
			{	
				usort($this->leading, function($a, $b)
				{
					return strcmp($b->hits, $a->hits);
				});
			}
			else if($asc_hits==1 && $dsc_hits==0)
		    {
				usort($this->leading, function($a, $b)
				{
				   return strcmp($a->hits, $b->hits);
			
				});
			}
		?>
		
		<!-- Leading items -->
		<div id="itemListLeading">
		
		    <?php //var_dump($this->leading); ?>
			
			<?php foreach($this->leading as $key=>$item): ?>

			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('num_leading_columns'))==0) || count($this->leading)<$this->params->get('num_leading_columns') )
				$lastContainer= ' itemContainerLast';
			else
				$lastContainer='';
			?>
			
			<div class="itemContainer<?php echo $lastContainer; ?>"<?php echo (count($this->leading)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('num_leading_columns'), 1).'%;"'; ?>>
				<?php
					// Load category_item.php by default
					$this->item=$item;
					
					//print_r($this->item);
					
					
					if($list==1 && $grid==0)
					{ 
						echo $this->loadTemplate('item');
					}
					else if($list==0 && $grid==1)
					{	
						echo $this->loadTemplate('item_grid');
					}
				?>
			</div>
			<?php if(($key+1)%($this->params->get('num_leading_columns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($this->primary) && count($this->primary)): ?>
		<!-- Primary items -->
		<div id="itemListPrimary">
			<?php foreach($this->primary as $key=>$item): ?>
			
			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('num_primary_columns'))==0) || count($this->primary)<$this->params->get('num_primary_columns') )
				$lastContainer= ' itemContainerLast';
			else
				$lastContainer='';
			?>
			
			<div class="itemContainer<?php echo $lastContainer; ?>"<?php echo (count($this->primary)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('num_primary_columns'), 1).'%;"'; ?>>
				<?php
					// Load category_item.php by default
					$this->item=$item;
					echo $this->loadTemplate('item');
				?>
			</div>
			<?php if(($key+1)%($this->params->get('num_primary_columns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($this->secondary) && count($this->secondary)): ?>
		<!-- Secondary items -->
		<div id="itemListSecondary">
			<?php foreach($this->secondary as $key=>$item): ?>
			
			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('num_secondary_columns'))==0) || count($this->secondary)<$this->params->get('num_secondary_columns') )
				$lastContainer= ' itemContainerLast';
			else
				$lastContainer='';
			?>
			
			<div class="itemContainer<?php echo $lastContainer; ?>"<?php echo (count($this->secondary)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('num_secondary_columns'), 1).'%;"'; ?>>
				<?php
					// Load category_item.php by default
					$this->item=$item;
					echo $this->loadTemplate('item');
				?>
			</div>
			<?php if(($key+1)%($this->params->get('num_secondary_columns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($this->links) && count($this->links)): ?>
		<!-- Link items -->
		<div id="itemListLinks">
			<h4><?php echo JText::_('K2_MORE'); ?></h4>
			<?php foreach($this->links as $key=>$item): ?>

			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('num_links_columns'))==0) || count($this->links)<$this->params->get('num_links_columns') )
				$lastContainer= ' itemContainerLast';
			else
				$lastContainer='';
			?>

			<div class="itemContainer<?php echo $lastContainer; ?>"<?php echo (count($this->links)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('num_links_columns'), 1).'%;"'; ?>>
				<?php
					// Load category_item_links.php by default
					$this->item=$item;
					echo $this->loadTemplate('item_links');
				?>
			</div>
			<?php if(($key+1)%($this->params->get('num_links_columns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

	</div>

	<!-- Pagination -->
	<?php if($this->pagination->getPagesLinks()): ?>
	<div class="k2Pagination">
		<?php if($this->params->get('catPagination')) echo $this->pagination->getPagesLinks(); ?>
		<div class="clr"></div>
		<?php if($this->params->get('catPaginationResults')) echo $this->pagination->getPagesCounter(); ?>
	</div>
	<?php endif; ?>

	<?php endif; ?>
</div>
<!-- End K2 Category Layout -->

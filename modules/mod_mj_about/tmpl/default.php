<?php
# About Module for Master
# Copyright (C) 2015 by mojoomla.com
# Homepage   : www.mojoomla.com
# Author     : mojoomla.com
# Email      : sales@mojoomla.com
# Version    : 1.0
# license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 
 
 // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$app_why = JFactory::getApplication();
$template_why   = $app_why->getTemplate(true);
$params_why     = $template_why ->params;
	
$img=$params_why->get('about_image_1');
$title=$params_why->get('about_title_1');
?>
<div class="container">
<div class="row">

<div class="about_1 col-md-3 col-sm-3 col-xs-12 no-padding">
   <div class="preset2_about">
	<img class="about_img" src="<?php echo $img;?>" alt="image"/>
   </div>
   <div class="preset2_about about_info">
	<p class="txt_top"><?php  echo $title;?> <i class="fa fa-arrow-right"></i></p>
   
   </div>	
</div>

<div class="div_2 col-md-9 col-sm-9 col-xs-12">
<?php $j=0;
				for($i=0; $i< count($about); $i++){ ?>
<div class="about_text">
<div class="about_title"><?php echo $about[$j][0]; ?></div>
<p class="about_desc"><?php echo $about[$j][1]; ?></p>
</div>

<?php $j++; } ?>
</div>
</div>
</div>
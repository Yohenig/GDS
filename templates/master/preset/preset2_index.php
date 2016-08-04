<?php
/**
 * @package     Master.Site
 * @subpackage  Templates.Master
 *
 * @copyright   Copyright (C) 2005 - 2015 mojoomla.com - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
ini_set("display_errors","0");

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Getting params from template
$params = $app->getTemplate(true)->params;


$sitename = $app->get('sitename');
$menu = JSite::getMenu();
$menu1 = $menu->getActive();

$isroot = $user->authorise('core.admin');

// Responsive Layout
$responsive_layout=$this->params->get('responsive_layout');

	
    // Add Stylesheets
    $doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/bootstrap/bootstrap.min.css');
    $doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/owl-carousel/owl.carousel.css'); //Core Owl Carousel CSS File  *	v1.3.3 
    $doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/owl-carousel/owl.theme.css');// Core Owl Carousel CSS Theme  File  *	v1.3.3
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/owl-carousel/owl.transitions.css');// Core Owl Carousel CSS Transition  File  *	v1.3.3
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/fonts/font-awesome.min.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/fonts/elegant/elegant-icon.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/animate/animate.min.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/lightbox2/css/lightbox.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/libraries/video/YTPlayer.css');
    $doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/components.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/header.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/style.css');
    $doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/media.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/k2.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/master_responsive.css');
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/js/jquery_accordion/jquery.akordeon.css');
	
	if($isroot=='1'){ 
		// Front-end module editing
		$doc->addStyleSheet($this->baseurl .'/templates/' . $this->template . '/css/front-end/template.css');
	}
 
	$doc->addStyleSheet('http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic');
	$doc->addStyleSheet('http://fonts.googleapis.com/css?family=Montserrat:400,700');
	$doc->addStyleSheet('http://fonts.googleapis.com/css?family=Roboto:400,300italic,300,100italic,100,400italic,500,500italic,700,700italic,900,900italic');
	$doc->addStyleSheet('http://fonts.googleapis.com/css?family=Raleway');
// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8'))
{
	$cls = "col-md-6 col-sm-12 col-xs-12";
}
elseif ($this->countModules('position-7') && !$this->countModules('position-8'))
{
	$cls = "col-md-9 col-sm-12 col-xs-12";
}
elseif (!$this->countModules('position-7') && $this->countModules('position-8'))
{
	$cls = "col-md-9 col-sm-12 col-xs-12";
}
else
{
	$cls = "col-md-12 col-sm-12 col-xs-12";
}
if ($menu->getActive() == $menu->getDefault()) 
{
	$pages='home_page';
}
else
{
	$pages='inner_page';
}
		

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	 <?php if($responsive_layout=='1'){ ?>
	   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <?php } ?>
	<jdoc:include type="head" />
	<script  type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jui/bootstrap.min.js"></script>
</head>
<body class="master_preset2" data-offset="200" data-spy="scroll" data-target=".primary-navigation">

<?php
	require("php/variables.php");
	include("php/themecolor.php");
?>
<?php
if($chooselayout=='boxed')
{ ?>
<?php $path= JURI::base()."templates/".$this->template."/images/elements/pattern_".$choosepattern.".png"; ?>
<style scoped>
 body {
    background-image: url("<?php echo $path ;?>");
 }
</style>
<div class="wrapper_boxed">
  <?php }
?>	

<div class="site_wrapper">
	<!-- Header Section -->
	<header id="header-section" class="header-section">
	  <div class="container">
	    <div class="row">
		<div class="col-md-2 col-sm-2 col-xs-6 logo-block">
			<a href="<?php echo $this->baseurl; ?>"><img src="<?php echo $logoFile;?>" alt="logo"/></a>
		</div>
		<?php if ($this->countModules('mj-master-menu')) { ?>
		<div class="col-md-10 col-sm-12 col-xs-12 menu-block">
		  <jdoc:include type="modules" name="mj-master-menu" style="master" />
		 </div>
		<?php } ?>
		</div>
	   </div>	
	</header>
	<!-- Header Section Over -->
	
	<!-- Breadcrumb Section -->
	<?php if ($this->countModules('mj-master-breadcrumbs')) { ?>
	<div class="breadcrumbs-section">
		<div class="container">
		  <div class="row">
			<jdoc:include type="modules" name="mj-master-breadcrumbs" style="master" />
		  </div>	
		</div>
	</div>
	<?php } ?>
	<!-- Breadcrumb Section Over -->
	
<!-- Slider Section -->
	<?php if ($this->countModules('mj-master-slideshow2')) { ?>
	
	
	<div id="photo-slider" class="slider-section">
		<jdoc:include type="modules" name="mj-master-slideshow2" style="master" />
	</div>
	<?php } ?>
	<!-- Slider Section Over-->
	
	<?php if ($this->countModules('mj-master-slide')) { ?>
	<div class="slider-text p2_slide">
		<div class="container">
		   <div class="row">
				<jdoc:include type="modules" name="mj-master-slide" style="master" />
			</div> 
		</div>	
	</div>
	<?php } ?>

	<?php if ($this->countModules('mj-master-about')) { ?>
	<div class="about_master">
		<jdoc:include type="modules" name="mj-master-about" style="master" />
	</div>
	<?php } ?>
	
	<?php if ($this->countModules('mj-master-top-destination')) { ?>
	<section id="destination-section" class="destination-section">
	  <div class="container">
		   <div class="row">
				<jdoc:include type="modules" name="mj-master-top-destination" style="master" />
			</div>
        </div> 			
	</section>
	<?php } ?>
	
	<!-- Features Section -->
	<?php if ($this->countModules('mj-master-feature-tour')) { ?>
	<section id="features-section" class="features-section-tour">
			<jdoc:include type="modules" name="mj-master-feature-tour" style="master" />
	</section>
	<?php } ?>
	<!-- Features Section Over -->
	
	<!-- Testimonial & Client Section -->
	<?php if ($this->countModules('mj-master-testimonial2')) { ?>
	<div id="testi-section" class="testi-section features_sec32">
		<div class="container">
			<div class="row">
				<jdoc:include type="modules" name="mj-master-testimonial2" style="master"/>
			</div>	
		</div>		
	</div>
	<?php } ?>
	<!-- Testimonial & Client Section Over -->
	
	<!-- Statistics Section -->
	<?php if ($this->countModules('mj-master-counter')) { ?>
	<div id="statistics-section" class="statistics-section">
		<div class="container">
			<jdoc:include type="modules" name="mj-master-counter" style="master" />
		</div>
	</div>
	<!-- Statistics Section Over -->
	<?php } ?>
	
	<!-- Blog Post -->
	<?php if ($this->countModules('mj-master-blogpost')) { ?>
	<section id="blog-section" class="blog-section">
		<jdoc:include type="modules" name="mj-master-blogpost" style="master" />
	</section>
	<?php } ?>
	<!-- Blog Post Over -->
	
	
	<!-- Main Content Section -->
	
	<div id="brag-about-section" class="brag-about-section <?php echo $pages; ?>">
		  <div class="container"> 
            	<div class="row">  
				     <?php if($this->countModules('position-8')) { ?>
						  <div class="col-md-3 col-sm-6 col-xs-12 left_sidebar">
							<jdoc:include type="modules" name="position-8" style="master_sidebar" />
						  </div>
				    <?php } ?>
					
				    <div class="<?php echo $cls; ?>">
						<jdoc:include type="message" />
						<jdoc:include type="component" />
					</div>
					
				   <?php if($this->countModules('position-7')) { ?>
						  <div class="col-md-3 col-sm-6 col-xs-12 right_sidebar">
							<jdoc:include type="modules" name="position-7" style="master_sidebar" />
						  </div>
				   <?php } ?>
				</div>
		   </div>		
    </div>
	<!-- Main Content Section Over -->
	
	<!-- Social Section -->
	<div id="social-section" class="social-main">
	  
	  <?php if(!empty($s_link1)){?>
		<div class=" icon-social-google no-padding">
			<a target="_blank" href="<?php echo $s_link1;?>"><i class="social_googleplus "></i></a>
		</div>
	  <?php } ?>
       <?php if(!empty($s_link2)){?>	  
		<div class="icon-social-tumblr no-padding">
			<a target="_blank" href="<?php echo $s_link2;?>"><i class="social_tumblr"></i></a>
		</div>
	   <?php } ?>	
	   <?php if(!empty($s_link3)){?>	  
		<div class="icon-social-facebook no-padding">
			<a target="_blank" href="<?php echo $s_link3;?>"><i class=" social_facebook"></i></a>
		</div>
	   <?php } ?>
       <?php if(!empty($s_link4)){?>	   
		<div class="icon-social-vimoe no-padding">
			<a target="_blank" href="<?php echo $s_link4;?>"><i class="social_vimeo "></i></a>
		</div>
	   <?php } ?>
       <?php if(!empty($s_link5)){?>	   	   
		<div class="icon-social-twitter no-padding">
			<a target="_blank" href="<?php echo $s_link5;?>"><i class="social_twitter "></i></a>
		</div>
	   <?php } ?>
       <?php if(!empty($s_link6)){?>	   
		<div class="icon-social-rss no-padding">
			<a target="_blank" href="<?php echo $s_link6;?>"><i class="social_rss "></i></a>
		</div>
	   <?php } ?>
       <?php if(!empty($s_link7)){?>	  	   
		<div class="icon-social-dribble no-padding">
			<a target="_blank" href="<?php echo $s_link7;?>"><i class="social_dribbble "></i></a>
		</div>
	   <?php } ?>
      <?php if(!empty($s_link8)){?>	  	   
		<div class="icon-social-pinterest no-padding">
			<a target="_blank" href="<?php echo $s_link8;?>"><i class="social_pinterest "></i></a>
		</div>
	  <?php } ?>

	</div>
	<!-- Social Section Over -->
	
	<!-- Footer Section -->
	<footer id="footer-section" class="footer-section">
	  <div class="container">
	  
	
	 <?php if($this->countModules('mj-master-footer-preset2')) { ?>
	  <div class="footer_content"> 
	    <jdoc:include type="modules" name="mj-master-footer-preset2" style="master_footer" />
	  </div>
	 <?php } ?>	

		<!--<a id="back-to-top" class="back-top pull-right"><i class="arrow_up"></i> Go on top</a>-->
		<!--<script src="http://t1.extreme-dm.com/f.js" id="eXF-qpixel-0" async defer></script>-->
		<div class="mj-copyright text-center col-md-12 col-xs-12 col-sm-12">
		  <?php echo $copyright; ?>
        </div>    		
	  </div>	
	</footer>
	<!-- Footer Section -->
	
	<!-- Light Box -->
	<div class="modal light-box fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="false">
	<!-- Modal -->
		<div class="container">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<div class="modal-dialog">
					<div class="modal-content">
						<div id="contact" class="modal-body">
							<div class="popup-form">
							<button type="button" class="close icon_close" data-dismiss="modal" aria-label="Close"></button>	
							<jdoc:include type="modules" name="mj-master-contactus" style="master_news" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-1"></div>
		</div>
	</div>
	<!-- Light Box -->
	
</div> <!-- End Site Wrapper -->
	 <?php
if($chooselayout=='boxed')
{ ?>
</div> <!-- End Wrapper Boxed -->
<?php }
?>	

	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5/html5shiv.min.js"></script>
      <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5/respond.min.js"></script>
    <![endif]-->
	
	<!-- jQuery Include -->
	
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/modernizr/modernizr.custom.13711.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/jquery.easing.min.js"></script><!-- Easing Animation Effect -->

	<script  type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/bootstrap/ie-emulation-modes-warning.js"></script> <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/bootstrap/ie10-viewport-bug-workaround.js"></script> <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<!-- Font Elegant -->
	<!--[if lte IE 7]><script src="libraries/fonts/elegant/lte-ie7.js"></script><![endif]-->
	
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/portfolio-filter/jquery.quicksand.js"></script> <!-- Quicksand v1.4 -->
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/jquery.superslides.min.js"></script> <!-- Superslides - v0.6.3-wip -->

	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/roundabout.js"></script> <!-- service Rounded slider -->
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/roundabout_shapes.js"></script> <!-- service Rounded slider -->
	
    <script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/jquery.animateNumber.min.js"></script>	<!-- Used for Animated Numbers -->
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/jquery.appear.js"></script> <!-- It Loads jQuery when element is appears -->
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/jquery.knob.js"></script> <!-- Used for Loading Circle -->
	
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/wow.min.js"></script>
	
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/owl-carousel/owl.carousel.min.js"></script> <!-- Core Owl Carousel CSS File  *	v1.3.3 -->
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/video/jquery.mb.YTPlayer.js"></script>
     
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>//js/jquery_accordion/jquery.akordeon.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/libraries/lightbox2/js/lightbox.min.js"></script>
	<script type="text/javascript">		 
		
		jQuery(function(jQuery) {
			jQuery('.statistics-section').each(function ()
			{
				var $this = jQuery(this);
				var myVal = jQuery(this).data("value");

				$this.appear(function()
				{
					jQuery('#project0').animateNumber({ number: <?php echo $co_value1;?> }, 2000);
					jQuery('#project1').animateNumber({ number: <?php echo $co_value2;?> }, 2000);
					jQuery('#project2').animateNumber({ number: <?php echo $co_value3;?> }, 2000);
					jQuery('#project3').animateNumber({ number: <?php echo $co_value4;?> }, 2000);
				});
			});
			jQuery("#owl-client").owlCarousel({
		 
			  navigation : false, // Show next and prev buttons
			  slideSpeed : 300,
			  paginationSpeed : 400,
			  singleItem:true
		 
			  // "singleItem:true" is a shortcut for:
			  // items : 1, 
			  // itemsDesktop : false,
			  // itemsDesktopSmall : false,
			  // itemsTablet: false,
			  // itemsMobile : false
		 
		  });

			
		   // Js for Slide On homepage
			var owl_slide = jQuery(".main_slide");
			owl_slide.owlCarousel({
				 
				  navigation : true, // Show next and prev buttons
				  slideSpeed : 300,
				  paginationSpeed : 400,
				  singleItem:true,
				  pagination:false,
				  transitionStyle : "goDown"
			  });
			  
		   jQuery('#brand-image-slider').owlCarousel({
	     
	          autoPlay: 3000, //Set AutoPlay to 3 seconds
	          items : 4,
	          itemsDesktop : [1199,3],
	          itemsDesktopSmall : [979,3],
			  itemsTablet : [768,2],
			  itemsMobile :	[479,1],
			  responsive : true
	     
	      });
		   var owl = jQuery("#k2-top-desti");
		   owl.owlCarousel({
				items: 4,
				autoPlay: false,
				itemsDesktop : [1199,3],
				itemsDesktopSmall : [979,3],
				itemsTablet: [768,2],
				itemsMobile : [479,1],
				pagination: true
			
		  });
			  // Custom Navigation Events
			  jQuery(".next_destination").click(function(){
				owl.trigger('owl.next');
			  })
			  jQuery(".prev_destination").click(function(){
				owl.trigger('owl.prev');
			  })
		 
		   jQuery('.akordeon').akordeon({ buttons: true, toggle: true });
		});

		
		
   </script>
	
	<!-- Customized Scripts -->
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/functions.js"></script>
    
	<script>
	
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-60724205-1', 'auto');
		ga('send', 'pageview');
	</script>
 
</body>
</html>

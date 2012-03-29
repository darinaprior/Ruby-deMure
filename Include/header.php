<?php
include 'check_status.php';
?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Ruby deMure - <?php echo $sPageTitle ?></title>
	<meta name="DESCRIPTION" content="Ruby deMure Luxury Burlesque Accessories - <?php echo $sPageTitle ?>" />
	<meta name="KEYWORDS" content="Ruby deMure, Ruby de Mure, burlesque, tassels, nipple tassels, pasties, <?php echo $sPageKeywords ?>, ireland, hand made, hand-made, dublin" />
	<link rel="SHORTCUT ICON" href="/images/favicon.ico">
	<link rel="STYLESHEET" href="css/styles.css">
	<?php
	// Add the "tabs" stylesheet if necessary
	if ($has_tabs) {
		echo '<link rel="STYLESHEET" href="css/tabs.css">';
	}
	// Add the "image zoom" stylesheet if necessary
	if ($hasZoom) {
		echo '<link rel="STYLESHEET" href="/jqzoom_ev-2.3/css/jqzoom.css">';
	}
	// Add the "row scroller" stylesheet if necessary
	if ($hasRowScroller) {
		echo '<link rel="STYLESHEET" href="/css/rowscroller.css">';
	}
	?>

	<!-- Search box styles and scripts -->
	<link rel="stylesheet" type="text/css" href="css/search.css" />
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" src="js/search.js"></script>
	
	<?php
	// IE needs some tweaks...
	$browser = getenv("HTTP_USER_AGENT");
	define("MSIE", strpos($browser, "MSIE") !== false);
	if (MSIE)
	{
		echo '<link rel="STYLESHEET" href="css/styles_ie.css">';
		if ($has_tabs) {
			echo '<link rel="STYLESHEET" href="css/tabs_ie.css">';
		}
	}
	?>
	
	<script type="text/javascript" src="js/jquery.1.3.2.min.js"></script>
	
	<?php // Set up the MenuMatic Navigation ?>
	<link rel="stylesheet" type="text/css" href="menumatic/css/MenuMatic.css" media="screen" charset="utf-8" />
	<!--[if lt IE 7]>
		<link rel="stylesheet" type="text/css" href="menumatic/css/MenuMatic-ie6.css" media="screen" charset="utf-8" />
	<![endif]-->
	<script type="text/javascript" src="js/mootools-1.2.1.js"></script>
	
	<script type="text/javascript" src="menumatic/js/MenuMatic_0.68.3.js" charset="utf-8"></script>
	<script type="text/javascript" >
		window.addEvent('domready', function() {			
			var myMenu = new MenuMatic();
		});
	</script>
	
	<?php // For some reason, need to always include this JS even if no image gallery (to do with noconflict) ?>
	<script type="text/javascript" src="pikachoose/js/jquery_pika.js"></script>
	
	<script type="text/javascript">$jq = jQuery.noConflict();</script>
		
	<?php
	// Set up the PikaChoose image gallery if necessary
	if ($bGallery)
	{
		?>
		<link rel="stylesheet" type="text/css" href="pikachoose/css/pika.css" />
		<script type="text/javascript" src="pikachoose/js/pikachoose-min.js"></script>
		<script type="text/javascript">
			<!--
			$jq(document).ready(function(){
				$jq("#pikame").PikaChoose();
     		});
			-->
		</script>
		<?php
	}//if $bGallery
	?>
	
	<?php
	/*
	* Set up the jQuery Cycle slideshow if necessary
	* Note: we assume all slideshows are inside elements (e.g. divs) with class "cycle"
	* We also assume each of those elements has an integer ID.  This is used to
	* 	a) identify the slideshow so we can make it work
	*	b) determine the delay before kicking off the 1st transition on that slideshow
	* 
	* e.g. <div class="cycle" id="0"> - 1st transition will have the usual delay
	* <div class="cycle" id="1"> - 1st transition will have the usual delay minus 0.5 seconds
	* <div class="cycle" id="2"> - 1st transition will have the usual delay minus 1 second
	* <div class="cycle" id="3"> - 1st transition will have the usual delay minus 1.5 seconds
	* and so on...
	*/
	if ($bSlideshow)
	{
		?>
		<script type="text/javascript" src="js/jquery.cycle.lite.min.js"></script>
		<style>
			.cycle img { display: none }
			.cycle img.first { display: block }
		</style>
		<script type="text/javascript">
			$jq(document).ready(function(){
				// Get all elements that have the "cycle" class applied
				var arrSlideshows = new Array();
				arrSlideshows = $jq('.cycle');
				
				// For each one, call createCycleSlideshow to make it a slideshow
				$jq.each(arrSlideshows, function(index, value) {
					createCycleSlideshow(value['id']);
				});
				
				function createCycleSlideshow(slideshowId)
				{
					var delayMs = getDelay(slideshowId);
					$jq('#'+slideshowId).cycle({
						fx:			'fade'
						,timeout:	3000	// 3 secs
						,speed:		1500	// 1.5 secs
						,delay:  -delayMs
						/*
					        ,after:     function() {
					        	/* If the image has a title, print it in the cycle_caption element *
					        	$jq('#cycle_caption').html(this.title);
					        }
					        */
					});
				}//function createCycleSlideshow
				
				// Function to get a semi-random delay in milliseconds for a
				// slideshow to reduce stress on CPU
				function getDelay(id)
				{
					// Use the element's ID to choose an elements from an array
					// of 10 delay periods
					var delayMs = 0;
					var delayMsValues = Array(0, 500, 1000, 1500, 2000);
					var iElem = id%5;
					delayMs = delayMsValues[iElem];
					return delayMs;
				}//function getRandomDelay
	
			});//ready
		</script>
		<?php
	}//if $bGallery
	?>
	
	<?php
	// Set up the jQuery Colorbox modal if necessary
	if ($bModal)
	{
		?>
		<link rel="stylesheet" type="text/css" href="colorbox/css/colorbox.css" />
		<script type="text/javascript" src="colorbox/js/jquery.colorbox-min.js"></script>
		<script type="text/javascript">
			<!--
			$jq(document).ready(function(){
				$jq(".modalLink").click(function(){
					var showElem = this.href;
					showElem = showElem.substring( showElem.lastIndexOf("#"), showElem.length );					
					$jq(".modalLink").colorbox({opacity: 0.75, inline:true, href:showElem});
				});
			});
			-->
		</script>
		<?php
	}//if $bModal
	?>
	
	<?php
	// Add the "tabs" jQuery if necessary
	if ($has_tabs) {
		echo '<script type="text/javascript" src="js/tabs.js"></script>';
	}
	
	// Add the "image zoom" jQuery if necessary
	if ($hasZoom) {
		echo '<script type="text/javascript" src="/jqzoom_ev-2.3/js/jqzoom-core.js"></script>';
	}
	
	// Add the "row scroller" jQuery if necessary
	if ($hasRowScroller) {
		echo '<script type="text/javascript" src="/js/rowscroller.js"></script>';
		//echo '<script type="text/javascript" src="/js/rowscroller_horizontal.js"></script>';
	}
	?>
	
	<?php // Set up my custom tooltips ?>
	<script type="text/javascript" src="js/tt.js"></script>
	<script type="text/javascript">
		$jq(document).ready(function(){
			fnRmTooltip(".tt","rmTooltip");
		});
	</script>
	
</head>

<body class="body">

<!-- centering table -->
<table style="width:100%;">
<tr>
<td align="center">

<table class="tblBody" cellpadding="0">
	<tr>
		<td class="tdR1C1">&nbsp;</td>
		<td class="tdR1C2">
			<a href="/">
				<img src="/images/blank_logo.gif" width="200px" height="100px" />
			</a>
		</td>
		<td class="tdR1C3">&nbsp;</td>
	</tr>
	<tr>
		<td class="tdR2C1"></td>
		<td class="tdR2C2">
			<?php
			// Navigation menu
			include("navigation_stripped.php");
			?>
		</td>
		<td class="tdR2C3"></td>
	</tr>
	<tr>
		<td class="tdR3C1"></td>
		<td class="tdR3C2"></td>
		<td class="tdR3C3"></td>
	</tr>
</table>
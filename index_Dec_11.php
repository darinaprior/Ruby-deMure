<!-- Christmas modal -->
<div id="christmas_modal" style="display:none;">
	<div id="dialog" class="window">
		<table class="tblStdFullCentre" bgcolor="#fff" cellpadding="5">
			<tr>
				<td colspan="2">
					<a href="collection.php?cid=4">
						<img src="images/flyer_xmas_08.jpg" 
							title="The Christmas Collection" 
							width="221" height="450" border="0" />
					</a>
				</td>
			</tr>
			<tr>
				<td align="left">
					<a href="collection.php?cid=4"><b>Go to Christmas Collection &raquo;</b></a>
				</td>
				<td align="right">
					<a href="#" class="close"><b>Close X</b></a>
				</td>
			</tr>
		</table>
	</div>
	<div id="mask"></div>
</div>

<?php
$sPageTitle = 'Home Page';
$sPageKeywords = 'home, index';
$bSlideshow = true;	// set up jQuery Cycle slideshow
include("Include/connection.php");
include("Include/header.php");
?>

<table class="tblBody" cellpadding="0">
	<tr>
		<td class="tdR4C1">&nbsp;</td>
		<td class="tdR4C2">

			<!-- MAIN CONTENT - SCROLL IF NECESSARY -->
			<div class="dvMainScroll">
				<div class="dvMainPadding">

				<table class="tblStdFull">
					<tr>
						<td>
							<table class="tblStd" align="center">
								<tr><td colspan="2" class="tdWelcome">Welcome to Ruby deMure</td></tr>
								<tr><td><br/></td></tr>
								<tr>
									<td colspan="2" class="tdPurveyor">
										Luxury Burlesque Accessories
									</td>
								</tr>
								<tr><td><br/></td></tr>
								<tr>
									<td style="font-size:14px;">
										<a href="off-the-rack.php">
											<img src="http://rubydemure.com/images/home_ready.jpg" />
										</a>
									</td>
									<td>
										<a href="off-the-rack.php">
											<h3>Start shopping now &raquo;</h3>
										</a>
									</td>
								</tr>
								<tr>
									<td style="font-size:14px;">
										<a href="bespoke.php"><img src="http://rubydemure.com/images/thumbs/products/bespoke/Epiphany Demeanour/eyepatch_09/epiphany1.jpg" /></a>
									</td>
									<td>
										<a href="bespoke.php">
											<h3>View bespoke work &raquo;</h3>
										</a>
									</td>
								</tr>
								<tr>
									<td colspan="2" align="center">
										<br/>
										<a href="vouchers.php"><h3>Gift vouchers available - click here</h3></a>
										<img src="http://rubydemure.com/images/home/irish_flag_20.jpg" />
										<span style="vertical-align:top;">
											Support Irish business!
										</span>
									</td>
								</tr>
							</table>
						</td>
						<td style="height: 390px; width: 339px; margin: 0px; padding:0px; vertical-align: middle;">
							<?php //jQuery Cycle slideshow - see http://www.malsup.com/jquery/cycle/ ?>
							<div class="cycle" id="0" style="height: 350px;">
								<img src="http://rubydemure.com/images/home/1.jpg" width="334" height="337" class="first" title="slide 1" />
								<img src="http://rubydemure.com/images/home/2_pasties1.jpg" width="334" height="337" title="Pasties" />
								<img src="http://rubydemure.com/images/home/2_pasties2.jpg" width="334" height="337" title="Pasties" />
								<img src="http://rubydemure.com/images/home/3_eyepatches.jpg" width="334" height="337" title="Eye-patches" />
								<img src="http://rubydemure.com/images/home/4_stocking_toppers.jpg" width="334" height="337" title="Stocking Toppers" />
								<img src="http://rubydemure.com/images/home/5_garters.jpg" width="334" height="337" title="Garters" />
								<img src="http://rubydemure.com/images/home/6_collars.jpg" width="334" height="337" title="Collars" />
								<img src="http://rubydemure.com/images/home/7_brooches_and_more.jpg" width="334" height="337" title="Brooches and more..." />
								<img src="http://rubydemure.com/images/home/8_bespoke_service.jpg" width="334" height="337" title="Bespoke service" />
								<img src="http://rubydemure.com/images/home/9_seasonal_items.jpg" width="334" height="337" title="Seasonal pieces" />
								<img src="http://rubydemure.com/images/home/10_finest_materials.jpg" width="334" height="337" title="Finest materials" />
							</div>
							<?php //<p id="cycle_caption"></p> ?>
						</td>
					</tr>
				</table>				
				</div>
			</div>
			<!-- end of MAIN CONTENT -->
			
		</td>
		<td class="tdR4C3">&nbsp;</td>
	</tr>
</table>
<?php include("Include/footer.php"); ?>

<style>
/* Z-index of #mask must lower than #christmas_modal .window */
#mask {
  position:absolute;
  z-index:9000;
  background-color:#461B37;
  display:none;
}
#christmas_modal .window {
  position:absolute;
  width:231px;
  height:500px;
  display:none;
  z-index:9999;
  padding:20px;
} 
</style>

<script type="text/javascript">
	$jq(document).ready(function(){	
		<!--
		var id = '#dialog';
		
		$jq('#christmas_modal').show();
		
		//Get the screen height and width
		var maskHeight = $jq(document).height();
		var maskWidth = $jq(window).width();
		
		//Set height and width to mask to fill up the whole screen
		$jq('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect     
		$jq('#mask').fadeIn(1000);    
		$jq('#mask').fadeTo("slow",0.8);  
		
		//Get the window height and width
		var winH = $jq(window).height();
		var winW = $jq(window).width();
		
		//Set the popup window to center
		$jq('#dialog').css('top',  winH/2-$jq('#dialog').height()/2);
		$jq('#dialog').css('left', winW/2-$jq('#dialog').width()/2);
		
		//transition effect
		$jq('#dialog').fadeIn(2000);
		
		//if close button is clicked
		$jq('.window .close').click(function (e) {
			//Cancel the link behavior
			e.preventDefault();
			$jq('#mask, .window').fadeOut(1000);
		});
		
		//if mask is clicked
		$jq('#mask').click(function () {
			$jq(this).fadeOut(1000);
			$jq('.window').fadeOut(1000);
		});
		-->
	});//ready
</script>
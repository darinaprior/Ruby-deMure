<?php
$sPageTitle		= 'Home Page';
$sPageKeywords	= 'home, index';
$bSlideshow		= true;	// set up jQuery Cycle slideshow
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
							<table class="tblStd">
								<tr><td class="tdWelcome">Welcome to Ruby deMure</td></tr>
								<tr><td><br/></td></tr>
								<tr>
									<td class="tdPurveyor">
										Luxury Burlesque Accessories
									</td>
								</tr>
								<tr><td><br/></td></tr>
								<tr>
									<td style="font-size:14px;">
										Ruby deMure is Ireland's leading purveyor of luxury Burlesque accessories, specialising in bespoke nipple tassels and pasties, designed especially for you and handmade with top-quality materials.
										<br/><br/>Established in 2005, Ruby makes accessories for stage performers and for those of us a little more - shall we say - demure!
										<br/><br/>
									</td>
								</tr>
							</table>
						</td>
						<td style="height: 390px; width: 339px; margin: 0px; padding:0px; vertical-align: middle;">
							<?php //jQuery Cycle slideshow - see http://www.malsup.com/jquery/cycle/ ?>
							<div class="cycle" id="0" style="height: 350px;">
								<img src="images/home/1.jpg" width="334" height="337" />
								<img src="images/home/2.jpg" width="334" height="337" />
								<img src="images/home/3.jpg" width="334" height="337" />
								<img src="images/home/4.jpg" width="334" height="337" />
								<img src="images/home/5.jpg" width="334" height="337" />
								<img src="images/home/6.jpg" width="334" height="337" />
								<img src="images/home/7.jpg" width="334" height="337" />
								<img src="images/home/8.jpg" width="334" height="337" />
								<img src="images/home/9.jpg" width="334" height="337" />
								<img src="images/home/10.jpg" width="334" height="337" />
							</div>
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

<?php
include("Include/connection.php");

$sPageTitle	= 'Testimonials';
$sPageKeywords	= 'Testimonials, Comments';
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
					<tr><td><br/></td></tr>
					<tr>
						<td valign="top">
							<table class="tblBreadcrumbs">
								<tr>
									<td colspan="2">
										You are here:&nbsp;
										<a href="/">Home</a> &gt;
										Testimonials
									</td>
								</tr>
							</table>
							<table class="tblStdFull" align="center" cellspacing="0" cellpadding="0">
								<tr><td colspan="2" class="tdHeading">Testimonials</td></tr>
								<tr>
									<td>
										<table class="tblStd" cellspacing="10">
											<tr>
												<td valign="top">
													<table class="tblStdFull">
														<center>What are people saying about their Ruby deMure purchases?</center>
														<?php
														// Get the testimonials to show
														$qTestimonials	= 'select * 
																from Testimonial 
																where Status = 1 
																and MainPage = 1
																order by date desc, TestimonialID desc;';
														$rsTestimonials	= mysql_query($qTestimonials, $cnRuby);
														while ($recTestimonials = mysql_fetch_array($rsTestimonials)) {
															$testId		= $recTestimonials['TestimonialID'];
															$testName	= $recTestimonials['Name'];
															$testComment	= $recTestimonials['Comment'];
															
															if ($testName == '') {
																$testName = 'anonymous';
															}
															
															// Print it out
															echo '<tr>
																<td>
																	<br/>"'.$testComment.'" - <i>'.$testName.'</i>
																</td>
															</tr>';
														}//while $recTestimonials
														?>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>		
							</table>
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
<?php
include("Include/connection.php");

$sPageTitle	= 'Testimonials';
$sPageKeywords	= 'Testimonials, Comments, Reviews';
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
														// Get the testimonials (reviews) to show
														$qTestimonials	= 'select * 
																from review 
																where status = 1 
																and main_page = 1
																order by priority desc, date desc, id desc;';
														$rsTestimonials	= mysql_query($qTestimonials, $cnRuby);
														while ($recTestimonials = mysql_fetch_array($rsTestimonials)) {
															$testId		= $recTestimonials['id'];
															$testName	= $recTestimonials['name'];
															$testComment	= $recTestimonials['comment'];
															
															if ($testName == '') {
																$testName = 'anonymous';
															}
															
															// Print it out in a styled box
															?>
															<tr>
																<td>
																	<br/>
																	<div class="testimonial" width="100%">
																		<table class="tblStdFull">
																			<tr>
																				<td width="7%"><img src="/images/quote_open.png" /></td>
																				<td width="60%"><?php echo $testComment; ?></td>
																				<td width="26%"><?php echo '- <i>'.$testName.'</i>'; ?></td>
																				<td width="7%" align="right"><img src="/images/quote_close.png" /></td>
																			</tr>
																		</table>
																	</div>
																</td>
															</tr>
															<?php
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
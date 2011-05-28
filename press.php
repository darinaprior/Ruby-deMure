<?php
$sPageTitle		= 'Press & Publicity';
$sPageKeywords	= 'press, publicity, promotion, exhibition';
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
					<tr><td><br/></td></tr>
					<tr>
						<td valign="top">
							<table class="tblBreadcrumbs">
								<tr>
									<td colspan="2">
										You are here:&nbsp;
										<a href="/">Home</a> &gt;
										Publicity
									</td>
								</tr>
							</table>
							<table class="tblStdFull" align="center" cellspacing="0" cellpadding="0">
								<tr><td colspan="2" class="tdHeading">Press, Exhibitions and Shows</td></tr>
								<tr><td>Just a selection...click an item to view more images</tr>
								<tr>
									<td align="center">
										<table class="tblStdFull" cellspacing="0" cellpadding="0">
											<tr><td>&nbsp;</td></tr>
											<?php
											// Get current publicity details
											$qPublicity	= "select * from Publicity where Display = 1 order by StartDate desc";
											$rsPublicity	= mysql_query($qPublicity, $cnRuby);
											while ($recPublicity = mysql_fetch_array($rsPublicity))
											{
												$publicityId		= $recPublicity['PublicityID'];
												$publicityTypeId	= $recPublicity['PublicityTypeId'];
												$title				= $recPublicity['Title'];
												$description		= $recPublicity['Description'];
												$startDate			= $recPublicity['StartDate'];
												$endDate			= $recPublicity['EndDate'];
												$location			= $recPublicity['Location'];
												$defaultImageID		= $recPublicity['DefaultImageID'];
												?>
					
												<tr class="cursor" onClick="window.location='gallery_publicity.php?pubid=<?=$publicityId?>'">
													<td style="vertical-align:top;">
														<?php		
														// Get default image
														$qImage	= "select PublicityImage.Filepath from PublicityImage inner join Publicity on PublicityImage.PublicityImageID = Publicity.DefaultImageID where Publicity.PublicityID = ".$publicityId." and Filepath <> 'img_not_avail.gif' limit 1";
														$rsImage = mysql_query($qImage, $cnRuby);
														$recImage = mysql_fetch_array($rsImage);
														$Filepath = $recImage['Filepath'];
														// we want the thumbnail
														$Filepath	= str_replace("images/", "images/thumbs/", $Filepath);
														?>
														<img src="<?=$Filepath?>" />
													</td>
													<td width="10">&nbsp;</td>
													<td>
														<?php
														// Type of publicity
														$qType = "select PublicityType.Type from PublicityType inner join Publicity on PublicityType.PublicityTypeID = Publicity.PublicityTypeID where Publicity.PublicityID = ".$publicityId." limit 1";
														$rsType = mysql_query($qType, $cnRuby);
														$recType = mysql_fetch_array($rsType);
														echo "<b>".strtoupper($recType['Type']).": </b>";
														
														// Date
														$sStart	= strtotime($startDate);
														$sEnd	= strtotime($endDate);
														if ($startDate == $endDate)
															echo "<b>".date("j M Y", $sStart)."</b>";
														else
														{
															if (date("Y", $startDate) == date("Y", $endDate))
															{
																if (date("m", $startDate) == date("m", $endDate))
																	echo "<b>".date("j", $sStart);
																else
																	echo "<b>".date("j M", $sStart);
															}
															else
															{
																echo "<b>".date("j M Y", $sStart);
															}
															echo " - ".date("j M Y", $sEnd)."</b>";
														}//if
														
														// Other details
														echo "<b>: $title</b>";
														if ($location)
															echo "<br/><span style='font-size:9px;'>$location</span>";
														if ($description)
															echo "<br/>$description";
														?>
													</td>
												</tr>
												<tr><td>&nbsp;</td></tr>
												<?php
											}
											?>
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

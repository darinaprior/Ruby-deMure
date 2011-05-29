<?php
$sPageTitle		= 'Ready-Made items';
$sPageKeywords	= 'ready-made, stock, off-the-shelf';
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
					<tr><td><br/></td></tr>
					<tr>
						<td valign="top">
							<table class="tblBreadcrumbs">
								<tr>
									<td colspan="3">
										You are here:&nbsp;
										<a href="/">Home</a> &gt;
										Ready-Made
									</td>
								</tr>
							</table>
							<table class="tblStdFullCentre" cellspacing="0" cellpadding="0">
								<tr>
									<td align="center" width="70%" style="vertical-align:top;">
										<table>
											<tr>
												<td align="center">
													<table class="tblStdFull" cellspacing="0" cellpadding="0">
														<tr><td colspan="6" align="left">These are my current collections.  Watch this space for changes every season.</td></tr>
														<tr><td>&nbsp;</td></tr>
														<tr>
															<?php
															// Get current collection details
															$iCol = 1;
															$qCollection	= "select * from Collection where Current = 1 order by Priority desc";
															$rsCollection	= mysql_query($qCollection, $cnRuby);
															while ($recCollection = mysql_fetch_array($rsCollection))
															{
																$CollectionID	= $recCollection['CollectionID'];
																$Title			= $recCollection['Title'];
																?>
								
																<td class="tdTitleReadyMade" style="width:25%; margin:0px; padding-left:5px; vertical-align:top;">
																	<?php //jQuery Cycle slideshow - see http://www.malsup.com/jquery/cycle/ ?>
																	<div class="cycle" id="<?php echo $CollectionID; ?>" style="height: 90px;">
																	
																		<?php		
																		// Get default images for all the products in the collection
																		$qImages	= "select ProductImage.Filepath from ProductImage inner join Product on ProductImage.ProdImageID = Product.DefaultImageID where Product.CollectionID = ".$CollectionID." and Filepath <> 'img_not_avail.gif'";
																		$rsImages	= mysql_query($qImages, $cnRuby);
																		
																		// Add each image to the collection's slideshow
																		while ($recImages = mysql_fetch_array($rsImages))
																		{
																			$Filepath	= $recImages['Filepath'];
																			// we want the thumbnail
																			$Filepath	= str_replace("images/", "images/thumbs/", $Filepath);
																			?>
																			<img src="<?=$Filepath?>" />
																			<?php
																		}
																		?>
																	</div>
																	<a href="collection.php?cid=<?php echo $CollectionID; ?>"><?php echo $Title; ?></a>
																</td>
																<?php
																if ($iCol == 4)	// 4 collections per row
																{
																	echo '</tr><tr>';
																	$iCol = 0;
																}
																$iCol++;
															}//while
																
															// Fill out the row, if necessary
															while ($iCol <= 4)
															{
																echo '<td>&nbsp;</td>';
																$iCol++;
															}
															?>
														</tr>
													</table>
												</td>
											</tr>
											<?php
											// Get older collection details
											$qCollection	= "select * from Collection where Current = 0 order by Priority desc";
											$rsCollection	= mysql_query($qCollection, $cnRuby);
											$numRows		= mysql_num_rows($rsCollection);
											if ($numRows > 0)
											{
												?>
												<tr>
													<td align="center">
														<table class="tblStdFull" cellspacing="0" cellpadding="0">
															<tr><td colspan="6" align="left">And here is a selection of previous collections - limited stock available.</td></tr>
															<tr><td>&nbsp;</td></tr>
															<tr>
																<?php
																// Get current collection details
																$iCol = 1;
																while ($recCollection = mysql_fetch_array($rsCollection))
																{
																	$CollectionID	= $recCollection['CollectionID'];
																	$Title			= $recCollection['Title'];
																	?>
								
																	<td class="tdTitleReadyMade" style="width:25%; margin:0px; padding-left:5px; vertical-align:top;">
																		<?php //jQuery Cycle slideshow - see http://www.malsup.com/jquery/cycle/ ?>
																		<div class="cycle" id="<?php echo $CollectionID; ?>" style="height: 90px;">
																	
																			<?php		
																			// Get default images for all the products in the collection
																			$qImages	= "select ProductImage.Filepath from ProductImage inner join Product on ProductImage.ProdImageID = Product.DefaultImageID where Product.CollectionID = ".$CollectionID." and Filepath <> 'img_not_avail.gif'";
																			$rsImages	= mysql_query($qImages, $cnRuby);
																		
																			// Add each image to the collection's slideshow
																			while ($recImages = mysql_fetch_array($rsImages))
																			{
																				$Filepath	= $recImages['Filepath'];
																				// we want the thumbnail
																				$Filepath	= str_replace("images/", "images/thumbs/", $Filepath);
																				?>
																				<img src="<?php echo $Filepath; ?>" />
																				<?php
																			}
																			?>
																		</div>
																		<a href="collection.php?cid=<?php echo $CollectionID; ?>"><?php echo $Title; ?></a>
																	</td>
																	<?php
																	if ($iCol == 4)	// 4 collections per row
																	{
																		echo '</tr><tr>';
																		$iCol = 1;
																	}
																	$iCol++;
																}//while
																
																// Fill out the row, if necessary
																while ($iCol <= 4)
																{
																	echo '<td>&nbsp;</td>';
																	$iCol++;
																}
																?>
															</tr>
														</table>
													</td>
												</tr>
												<?php
											}//if $numRows
											?>		
										</table>											
									</td>
									
									<td align="center" width="50%" style="vertical-align:top;">
										<table class="tblStdFull" cellspacing="0" cellpadding="0">
											<tr><td colspan="3" class="tdHeading">Ready-Made Items</td></tr>	
										</table>
										<p align="left" />Once I started making accessories, I found I couldn't stop coming up with designs.  It's quite addictive.  Every season brings new ideas and every occasion conjures up vivid images of the events and the times we all love.  And as soon as one idea pops into my head, it sprouts new ones all around it.
										<br/><br/>So I started building collections of stock items that I sell "off the rack".  To the left are some that I have brought to life, and there are many more on the way!
										<br/><br/><a href="bespoke.php">Click here for Bespoke Work...</a>
										<br/><br/><a href="vouchers.php">...or here for Gift Vouchers.</a>
									</td>
									
								</tr>
								<tr><td><br/></td></tr>
				
								<?php /* develop search facility here in future - by colour, style, materials etc. */ ?>
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

<?php
if ($cnRuby)
	mysql_close($cnRuby);
?>
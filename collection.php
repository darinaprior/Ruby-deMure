<?php
if ($_GET['cid'] != "")
	$g_iCollectID = $_GET['cid'];
else
	$g_iCollectID = -1;

if ($g_iCollectID == -1)
{
	?><a href="/">No collection ID supplied.  Please return to the home page by clicking here.</a><?php
}
else
{
	include("Include/connection.php");

	// Get collection details from main [Collection] table
	$qCollection	= "select * from Collection where CollectionID = ".$g_iCollectID." limit 1";
	$rsCollection	= mysql_query($qCollection, $cnRuby);
	$recCollection	= mysql_fetch_array($rsCollection);
	$Title			= $recCollection['Title'];
	$Description	= $recCollection['Description'];
	$Date			= $recCollection['Date'];
	
	$sPageTitle		= $Title;
	$sPageKeywords	= 'collection, '.$Title.', ready-made, off the shelf, stock';
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
							<td valign="top">


								<table class="tblStd" cellspacing="0">
									<tr>
										<td colspan="3">
											You are here:&nbsp;
											<a href="/">Home</a> &gt;
											<a href="off-the-rack.php">Ready-Made</a> &gt;
											<?=$Title?>
										</td>
									</tr>
								</table>

								<table class="tblStdFullCentre" cellspacing="0">
									<tr><td colspan="3" class="tdHeading"><?=$Title?></td></tr>
									<tr><td colspan="3"><?=$Description?></td></tr>
									<?php
									if ($g_iCollectID == 2)	// Valentine's
									{
										?>
										<tr>
											<td>
												Below are the pieces in my Valentine Collection. I also have other items that are perfect for Valentine's day.
												<br/>Check out these links:
												&nbsp;&nbsp;&nbsp;&nbsp;<a href="browse.php?type=3&val=2"><b/>See all Hearts&raquo;</b></a>
												&nbsp;&nbsp;&nbsp;&nbsp;<a href="browse.php?type=2&val=3"><b/>See all Red products&raquo;</b></a>
												&nbsp;&nbsp;&nbsp;&nbsp;<a href="browse.php?type=2&val=7"><b/>See all Pink products&raquo;</b></a>
											</td>
										</tr>
										<tr><td><br/></td></tr>
										<?php
									}
									?>
									<tr>
										<td colspan="3">
											<table class="tblStdFullCentre" style="padding:5px;">
												<tr>
													<td colspan="8">
														Click on the pictures to see more details and larger images.
														<!--<br/><a href="sizing.html" target="_blank">Click here for sizing details.</a><br/><br/>-->
													</td>
												</tr>
												<tr>
													<?php
													// Get the products in this collection
													$iCount = 1;
													$iRow	= 1;
													$qProducts	= "select *, case when NumRemaining < 1 then 1 else 0 end as OutOfStock from Product where CollectionID = ".$g_iCollectID." order by OutOfStock, Priority desc";
													$rsProducts	= mysql_query($qProducts, $cnRuby);
													while ($recProduct = mysql_fetch_array($rsProducts))
													{
														$ProdID			= $recProduct['ProdID'];
														$Title			= $recProduct['Title'];
														$TotalCost		= $recProduct['TotalCost'];
														$DefaultImageID	= $recProduct['DefaultImageID'];
														$numRemaining	= $recProduct['NumRemaining'];
										
														// Get default product image
														if ($DefaultImageID == null)
														{
															$Filepath		= "images/img_not_avail_300.gif";
															$FilepathThumb	= "images/img_not_avail_90.gif";
														}
														else
														{
															$qDefImage		= "select * from ProductImage where ProdImageID = ".$DefaultImageID." limit 1";
															$rsDefImage		= mysql_query($qDefImage, $cnRuby);
															$recDefImage	= mysql_fetch_array($rsDefImage);
															$Filepath		= $recDefImage['Filepath'];
															// The following code re: img_not_avail is prob not needed!
															if (is_numeric( stripos($Filepath,"img_not_avail") ))
																$FilepathThumb	= str_replace("images/img_not_avail.gif", "images/thumbs/img_not_avail_90.gif", $Filepath);
															else
																$FilepathThumb	= str_replace("images/", "images/thumbs/", $Filepath);
														}
											
														// Get the sizes available
														$sSizes		= "";
														$qSizes		= "select Size.Name from ProductSize inner join Size on ProductSize.SizeID = Size.SizeID WHERE ProductSize.ProdID = ".$ProdID;
														$rsSizes	= mysql_query($qSizes, $cnRuby);
														while ($recSize = mysql_fetch_array($rsSizes))
														{
															$sSizes = $sSizes.$recSize['Name'].", ";
														}
														$sSizes = substr($sSizes, 0, -2);	// strip trailing comma and space
														
														if ($iCount > 4)
														{
															$iCount = 2;
															echo '</tr><tr><td><br/></td></tr><tr>';
														}
														else
														{
															$iCount++;
														}
														?>
														<td align="center" style="cursor:pointer;">
															<a href="product.php?pid=<?=$ProdID?>" style="color:#330000;" class="aNoBold">
																<img src="<?=$FilepathThumb?>" title="<?=$Title?>" border="0">
																<br/><b><?=$Title?></b>
																<?php
																if ($numRemaining > 0)
																{
																	?>
																	<br/>&euro;<?=$TotalCost?>
																	<?php
																	if ($sSizes != '')
																		echo " ($sSizes)	";
																}
																else
																{
																	?><br/>OUT OF STOCK<?php
																}
																?>
															</a>
														</td>
														<td width="5"></td>
														<?php
													}
													?>
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
	<?php
	include("Include/footer.php");
	
	if ($cnRuby)
		mysql_close($cnRuby);
		
}//if $g_iCollectID
?>

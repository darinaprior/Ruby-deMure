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
	require_once 'Include/connection.php';

	// Get collection details from main [Collection] table
	$qCollection	= "select * from Collection where CollectionID = ".$g_iCollectID." limit 1";
	$rsCollection	= mysql_query($qCollection, $cnRuby);
	$recCollection	= mysql_fetch_array($rsCollection);
	$Title			= $recCollection['Title'];
	$Description	= $recCollection['Description'];
	$Date			= $recCollection['Date'];
	
	$sPageTitle		= $Title;
	$sPageKeywords	= 'collection, '.$Title.', ready-made, off the shelf, stock';
	require_once 'Include/header.php';
	require_once 'Include/functions.php';
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
												Check out these links:
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
														$numRemaining	= $recProduct['NumRemaining'];
										
														// Get just one product image
														$sql = 'SELECT DISTINCT filepath
															FROM product_image
															WHERE product_id = ?
															ORDER BY id
															LIMIT 1';
														$stmt = $mysqli->prepare($sql);
														if ($stmt) {
															$stmt->bind_param('i', intval($ProdID));
															$stmt->bind_result($path);
															$stmt->execute();
															$stmt->fetch();
															
															// Get the full filepath
															$fullPath = getFullImagePath($path, 3/*stock*/, TRUE/*thumbnail*/);
															$stmt->close();
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
																<img src="<?php echo $fullPath; ?>" title="<?=$Title?>" border="0">
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
	require_once 'Include/footer.php';
	
	if ($cnRuby)
		mysql_close($cnRuby);
		
}//if $g_iCollectID
?>

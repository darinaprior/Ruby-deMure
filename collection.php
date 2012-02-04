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
											<?php echo $Title; ?>
										</td>
									</tr>
								</table>

								<table class="tblStdFullCentre" cellspacing="0">
									<tr>
										<td class="hand_collection">
											<b>HOVER</b> over images
											<br/>for quick-view
											<br/><br/><b>CLICK</b> images for full details
										</td>
										<td>
											<span class="heading"><?php echo $Title; ?></span>
											<br/><br/><?php echo $Description; ?>
											<?php
											if ($g_iCollectID == 2) {
												// Valentine's
												?>
												<table class="tblStdFullCentre" cellspacing="0">
													<tr>
														<td>
															Check out these links:
															&nbsp;&nbsp;&nbsp;&nbsp;<a href="browse.php?type=3&val=2"><b/>See all Hearts&raquo;</b></a>
															&nbsp;&nbsp;&nbsp;&nbsp;<a href="browse.php?type=2&val=3"><b/>See all Red products&raquo;</b></a>
															&nbsp;&nbsp;&nbsp;&nbsp;<a href="browse.php?type=2&val=7"><b/>See all Pink products&raquo;</b></a>
														</td>
													</tr>
												</table>
												<?php
											}
											?>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<table class="tblStdFullCentre" style="padding:5px;">
												<tr>
													<?php
													// Get the products in this collection
													$products = array();
													$iCount = 1;
													$sql = 'SELECT
															p.ProdID,
															p.Title,
															p.TotalCost,
															MAX(pos.num_in_stock) AS MaxStock
														FROM Product p
														LEFT JOIN product_option_size pos ON p.ProdID = pos.product_id
														WHERE p.CollectionID = ?
														AND p.Priority IS NOT NULL
														GROUP BY p.ProdID
														ORDER BY MaxStock DESC, p.Priority DESC';
													$stmt = $mysqli->prepare($sql);
													if ($stmt) {
														$stmt->bind_param('i', intval($g_iCollectID));
														$stmt->bind_result($productId, $productTitle, $totalCost, $maxStock);
														$stmt->execute();
														while ($stmt->fetch()) {
															// Store the main product details
															$products[$productId] = array(
																'Title' => $productTitle,
																'Cost' => $totalCost,
																'MaxStock' => $maxStock,
															);
														}//while
														$stmt->close();
													}//if $stmt
													
													// Loop through the products
													foreach ($products as $id => $details) {
															
														// Get just one product image
														$path = '';
														$sql = 'SELECT DISTINCT filepath
															FROM product_image
															WHERE product_id = ?
															ORDER BY id
															LIMIT 1';
														$stmt = $mysqli->prepare($sql);
														if ($stmt) {
															$stmt->bind_param('i', intval($id));
															$stmt->bind_result($path);
															$stmt->execute();
															$stmt->fetch();
															
															// Get the full filepath and add it back to the products array
															$fullPath = getFullImagePath($path, 3/*stock*/, TRUE/*thumbnail*/);
															$stmt->close();
														}
															
														// Get the colours available, but only the first colour in each "option"
														// e.g. if it's available in "red and white" and "black and white" then we'll
														// just display "red" and "black" as the available colours
														$colourValues = array();
														$sql = 'SELECT DISTINCT
																c.HexValue
															FROM product_option_colour poc
															INNER JOIN product_option po ON poc.option_id = po.id
															INNER JOIN Colour c ON poc.colour_id = c.ColourID
															WHERE po.product_id = ?
															AND poc.priority = 0';
														$stmt = $mysqli->prepare($sql);
														if ($stmt) {
															$stmt->bind_param('i', intval($id));
															$stmt->bind_result($colourValue);
															$stmt->execute();
															while ($stmt->fetch()) {
																$colourValues[] = $colourValue;
															}
															$stmt->close();
														}
															
														// Get the sizes available
														$sizes = array();
														$sql = 'SELECT DISTINCT s.NameAbbrev
															FROM product_option_size pos
															INNER JOIN Size s ON pos.size_id = s.SizeID
															WHERE pos.product_id = ?
															ORDER BY s.SizeID';
														$stmt = $mysqli->prepare($sql);
														if ($stmt) {
															$stmt->bind_param('i', intval($id));
															$stmt->bind_result($size);
															$stmt->execute();
															while ($stmt->fetch()) {
																$sizes[] = $size;
															}
															$stmt->close();
														}
														
														// Now print out the image and price or "OUT OF STOCK" and add
														// a styled tooltip with the colours and sizes available
														$productName = $details['Title'];
														$cost = '&euro;'.formatPrice($details['Cost']);
														if ($details['MaxStock'] == 0) {
															$costString = 'OUT OF STOCK';
															$quickViewCost = $cost.' (OUT OF STOCK)';
														} else {
															$costString = $cost;
															$quickViewCost = $cost;
														}
														?>
														<td align="center" style="cursor:pointer;">
															<a href="product.php?pid=<?php echo $id; ?>" style="color:#330000;" class="aNoBold">
																<?php
																// Format the styled tooltip
																$quickView = formatCollectionTooltip($productName, $quickViewCost, $colourValues, $sizes);
																
																// Put it on the image itself
																echo '<img class="tt" src="'.$fullPath.'" border="0" title="'.$quickView.'" />';
																
																// Follow with the title and price
																echo '<br/><b>'.$productName.'</b>';
																echo '<br/>'.$costString;
																?>
															</a>
														</td>
														<td width="5"></td>
														<?php
														// Show 5 products per row
														if ($iCount % 5 == 0) {
															$iCount = 1;
															echo '</tr><tr>';
														} else {
															$iCount++;
														}
													}//foreach $products
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

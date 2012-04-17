<?php
$sPageTitle = 'Ready-Made items';
$sPageKeywords = 'ready-made, readymade, stock, off-the-shelf, off-the-rack';
$bSlideshow = true;	// set up jQuery Cycle slideshow
require_once 'Include/header.php';
require_once 'Include/functions.php';

/** Connect to DB */
require_once 'Include/connection.php';

/** Get current collection details */
$collections = array();
$sql = 'SELECT 
		CollectionID, 
		Title 
	FROM Collection 
	WHERE Current = 1 
	ORDER BY Priority DESC';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($id, $title);
	$stmt->execute();
	while ($stmt->fetch()) {
		$collections[] = array(
			'Id' => $id,
			'Title' => $title,
			'Images' => array() /* blank for now */,
		);
	}
	$stmt->close();
}

/** Get one image for each product in each collection */
foreach ($collections as $key => $collection) {
	$images = array();
	$sql = 'SELECT DISTINCT
			MIN(pi.id),
			pi.filename,
			p.ProdID
		FROM product_image pi
		INNER JOIN Product p ON pi.product_id = p.ProdID
		INNER JOIN product_collection pc ON p.ProdID = pc.product_id
		WHERE pc.collection_id = ?
		AND pi.filename IS NOT NULL
		GROUP BY p.ProdID
		ORDER BY pi.id';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_param('i', intval($collection['Id']));
		$stmt->bind_result($minId, $imageName, $productId);
		$stmt->execute();
		while ($stmt->fetch()) {
			/**
			 * Add the full filepath for the "small" version of 
			 * this image to this collection's "Images" array
			 */
			$collections[$key]['Images'][] = getProductImagePath($productId, $imageName, 3/*small*/);
		}
		$stmt->close();
	}//if $stmt
}//foreach
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
	<tr>
		<td colspan="6" align="left">
			These are my current collections.  Watch this space for changes every season.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<?php
		/** Loop through the collections and display the image(s) and name for each */
		$iCol = 0;
		foreach ($collections as $collection) {
		
			// 4 collections per row
			if ($iCol == 4)	{
				echo '</tr><tr><td><br/></td></tr><tr>';
				$iCol = 0;
			}
			
			$cid = $collection['Id'];
			$title = $collection['Title'];
			$images = $collection['Images'];
			?>
			<td class="tdTitleReadyMade" 
				style="width:25%; margin:0px; padding-left:5px; vertical-align:top; font-size:14px;"
			>
				<a href="collection.php?cid=<?php echo $cid; ?>">
					<?php
					/** Write the collection's title underneath */
					echo $title;
					/** Only use the jQuery Cycle functionality if there's more than 1 image */
					if (is_array($images) && COUNT($images) > 1) {
						echo '<div class="cycle" id="'.$cid.'" style="height: 90px;">';
						foreach ($images as $filepath) {
							echo '<img src="'.$filepath.'" />';
						}//foreach
						echo '</div>';
					} else {
						/** Otherwise, just display single static image */
						echo '<img src="'.$images[0].'" />';
					}
					?>
				</a>
			</td>
			<?php
			$iCol++;
		}//foreach
			
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
?>
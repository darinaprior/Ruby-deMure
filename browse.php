<?php
$browseTypeId	= -1;
$browseValueId	= -1;
if ($_GET['type'] != "")
	$browseTypeId	= $_GET['type'];
if ($_GET['val'] != "")
	$browseValueId	= $_GET['val'];

if ( ($browseTypeId == -1) || ($browseValueId == -1) ) {
	?><a href="/">Type or value not supplied.  Please return to the home page by clicking here.</a><?php
} else {
	/** Connect to the DB */
	require_once 'Include/connection.php';
	
	/**
	 * Set up the appropriate prepared MySQL statements,
	 * based on the type and value the user is browsing
	 */
	switch($browseTypeId) {
		case 1:
			/* Product types */
			$browseType = 'Product Type';
			$sqlBrowseName = 'SELECT IfNull(NavName, Description) FROM ProductType WHERE ProdTypeID = ? LIMIT 1';
			$sqlProducts = 'SELECT DISTINCT
						p.ProdID,
						p.Title,
						p.TotalCost,
						p.CategoryID 
					FROM Product p
					WHERE p.Priority IS NOT NULL
					AND p.ProductTypeID = ?
					ORDER BY p.Priority DESC';
			break;
		case 2:
			/* Product colours */
			$browseType = 'Colour';
			$sqlBrowseName = 'SELECT Description FROM Colour WHERE ColourID = ? LIMIT 1';
			$sqlProducts = 'SELECT DISTINCT
						p.ProdID,
						p.Title,
						p.TotalCost,
						p.CategoryID 
					FROM Product p
					INNER JOIN product_option po ON p.ProdID = po.product_id
					INNER JOIN product_option_colour poc ON po.id = poc.option_id
					WHERE p.Priority IS NOT NULL
					AND poc.colour_id = ?
					ORDER BY poc.priority ASC, p.Priority DESC';
			break;
		case 3:
			/* Product shapes */
			$browseType = 'Shape';
			$sqlBrowseName = 'SELECT Description FROM Shape WHERE ShapeID = ? LIMIT 1';
			$sqlProducts = 'SELECT DISTINCT
						p.ProdID,
						p.Title,
						p.TotalCost,
						p.CategoryID 
					FROM Product p
					WHERE p.Priority IS NOT NULL
					AND p.ShapeID = ?
					ORDER BY p.Priority DESC';
			break;
		case 4:
			/* Product sizes */
			$browseType = 'Size';
			$sqlBrowseName = 'SELECT Name FROM Size WHERE SizeID = ? LIMIT 1';
			$sqlProducts = 'SELECT DISTINCT
						p.ProdID,
						p.Title,
						p.TotalCost,
						p.CategoryID 
					FROM Product p
					INNER JOIN product_option_size pos ON p.ProdID = pos.product_id
					WHERE p.Priority IS NOT NULL
					AND pos.size_id = ?
					ORDER BY p.Priority DESC';
			break;
		case 5:
			/* Product materials */
			$browseType = 'Material';
			$sqlBrowseName = 'SELECT Name FROM Material WHERE MaterialID = ? LIMIT 1';
			$sqlProducts = 'SELECT DISTINCT
						p.ProdID,
						p.Title,
						p.TotalCost,
						p.CategoryID 
					FROM Product p
					INNER JOIN ProductMaterial pm ON p.ProdID = pm.ProdID
					WHERE p.Priority IS NOT NULL
					AND pm.MaterialID = ?
					ORDER BY pm.Order ASC, p.Priority DESC';
			break;
	}//switch
	
	/** Now run the query to get the browse value name e.g. "Red" */
	$stmt = $mysqli->prepare($sqlBrowseName);
	if ($stmt) {
		$stmt->bind_param('i', $browseValueId);
		$stmt->bind_result($description);
		$stmt->execute();
		$stmt->fetch();
		$browseValue = $description;
		$stmt->close();
	}
	
	/** Now run the query to get all the matching products and store them in the main array */
	$productsToDisplay = array();
	$stmt = $mysqli->prepare($sqlProducts);
	if ($stmt) {
		$stmt->bind_param('i', $browseValueId);
		$stmt->bind_result($id, $title, $cost, $category);
		$stmt->execute();
		while ($stmt->fetch()) {
			$productsToDisplay[] = array(
				'Id' => $id,
				'Title' => $title,
				'Cost' => $cost,
				'CategoryId' => $category,
			);
		}
		$stmt->close();
	}
	
	$sPageTitle = "Browse by $browseType: $browseValue";
	$sPageKeywords = $sPageTitle;
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
											<?php echo $sPageTitle; ?>
										</td>
									</tr>
								</table>

								<table class="tblStdFullCentre" cellspacing="0">
									<tr><td colspan="3" class="tdHeading"><?=$sPageTitle?></td></tr>
									<tr>
										<td>
											<table class="tblStdFullCentre" style="padding:5px;">
												<tr>
													<td colspan="20">
														Click on the pictures to see more details and larger images.
														<!--<br/><a href="sizing.html" target="_blank">Click here for sizing details.</a><br/><br/>-->
													</td>
												</tr>
												<tr>
													<?php
/** Loop through the products and print them out with one image and their price and sizes */
$iCount = 1;
foreach ($productsToDisplay as $product) {
	$id = $product['Id'];
	$title = $product['Title'];
	$cost = $product['Cost'];
	$categoryId = $product['CategoryId'];

	// Get just one product image
	$fullPath = getSingleImageForProduct($id, 3/*small*/);
	
	// Get the sizes available
	$sizes = array();
	$sql = 'SELECT DISTINCT s.Name
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
	// Put together in a string
	$sizesString = implode(', ', $sizes);
	?>
	<td width="20%" align="center" style="cursor:pointer;">
		<a href="product.php?pid=<?php echo $id; ?>" style="color:#330000;" class="aNoBold">
			<img src="<?php echo $fullPath; ?>" title="<?php echo $title; ?>" border="0">
			<br/><b><?php echo $title; ?></b>
			<?php
			// don't show prices for bespoke
			if ($categoryId != 1) {
				?><br/>&euro;<? echo $cost ?><?php
				if ($sSizes != '')
					echo " ($sizesString)";
			} else {
				?><br/>(bespoke)<?php
			}
			?>
		</a>
	</td>
	<td width="5"></td>
	<?php
	if ($iCount == 5) {
		?></tr><tr><td><br/></td></tr><tr><?php
		$iCount = 1;
	} else {
		$iCount++;
	}
}//foreach $productsToDisplay
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
		
}//if $browseTypeId
?>
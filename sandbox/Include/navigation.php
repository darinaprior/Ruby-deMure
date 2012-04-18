<?php /* <-- N.B. LEAVE THE SPACE HERE */

require_once 'connection.php';

/* Ready-made collections */
$collections = array();
$sql = 'SELECT DISTINCT 
		CollectionID, 
		Title 
	FROM Collection 
	WHERE Current IS NOT NULL 
	ORDER BY Priority DESC';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($collectionId, $title);
	$stmt->execute();
	while ($stmt->fetch()) {
		$collections[$collectionId] = $title;
	}
	$stmt->close();
}

/* Product types */
$productTypes = array();
$sql = 'SELECT DISTINCT 
		pt.ProdTypeID, 
		IfNull(pt.NavName, pt.Description) 
	FROM ProductType pt 
	INNER JOIN Product p ON pt.ProdTypeID = p.ProductTypeID';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($prodTypeId, $name);
	$stmt->execute();
	while ($stmt->fetch()) {
		$productTypes[$prodTypeId] = $name;
	}
	$stmt->close();
}

/* Product colours */
$colours = array();
$sql = 'SELECT DISTINCT 
		c.ColourID, 
		c.Description,
		COUNT(poc.id) AS NumProds 
	FROM Colour c 
	INNER JOIN product_option_colour poc ON c.ColourID = poc.colour_id 
	GROUP BY poc.colour_id 
	ORDER BY NumProds DESC 
	LIMIT 15';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($colourId, $description, $numProds);
	$stmt->execute();
	while ($stmt->fetch()) {
		$colours[$colourId] = $description;
	}
	$stmt->close();
}

/* Product shapes */
$shapes = array();
$sql = 'SELECT DISTINCT 
		s.ShapeID, 
		s.Description, 
		COUNT(s.ShapeID) AS NumProds 
	FROM Shape s 
	INNER JOIN Product p ON s.ShapeID = p.ShapeID 
	GROUP BY s.ShapeID 
	ORDER BY NumProds DESC 
	LIMIT 15';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($shapeId, $description, $numProds);
	$stmt->execute();
	while ($stmt->fetch()) {
		$shapes[$shapeId] = $description;
	}
	$stmt->close();
}

/* Product sizes */
$sizes = array();
$sql = 'SELECT DISTINCT 
		s.SizeID, 
		s.Name, 
		COUNT(pos.id) AS NumProds 
	FROM Size s 
	INNER JOIN product_option_size pos ON s.SizeID = pos.size_id 
	GROUP BY pos.size_id 
	ORDER BY NumProds DESC 
	LIMIT 15';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($sizeId, $name, $numProds);
	$stmt->execute();
	while ($stmt->fetch()) {
		$sizes[$sizeId] = $name;
	}
	$stmt->close();
}

/* Product materials */
$materials = array();
$sql = 'SELECT DISTINCT 
		m.MaterialID, 
		m.Name, 
		COUNT(m.MaterialID) AS NumProds 
	FROM Material m  
	INNER JOIN ProductMaterial pm ON m.MaterialID = pm.MaterialID 
	WHERE m.IncludeInNav = 1 
	GROUP BY m.MaterialID 
	ORDER BY NumProds DESC 
	LIMIT 15';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($materialId, $name, $numProds);
	$stmt->execute();
	while ($stmt->fetch()) {
		$materials[$materialId] = $name;
	}
	$stmt->close();
}

/* Publicity types */
$publicityTypes = array();
$sql = 'SELECT DISTINCT 
		PublicityTypeId, 
		Type 
	FROM PublicityType 
	WHERE Priority >= 0 
	ORDER BY Priority DESC';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($id, $type);
	$stmt->execute();
	while ($stmt->fetch()) {
		$publicityTypes[$id] = $type;
	}
	$stmt->close();
}

/* Link categories */
$linkCategories = array();
$sql = 'SELECT DISTINCT 
		LinkCategoryID, 
		Category 
	FROM LinkCategory 
	WHERE Priority IS NOT NULL 
	ORDER BY Priority DESC';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($id, $category);
	$stmt->execute();
	while ($stmt->fetch()) {
		$linkCategories[$id] = $category;
	}
	$stmt->close();
}
/* N.B. LEAVE THE SPACE HERE --> */ ?>

<table class="tblStdFull">
	<tr>
		<td width="100%">
			<table class="tblStdFull">
				<tr>
					<td align="center" class="tdNav">
						<ul id="nav">
							<li><a href="/">Home</a></li>
							<li>
								<a href="#">Shop</a>
								<ul>
									<li><a href="bespoke.php">Bespoke Work</a></li>
									<li><a href="off-the-rack.php">Ready-Made</a>
										<ul>
											<li><a href="off-the-rack.php">All Collections</a></li>
											<?php 
											foreach ($collections as $colId => $colTitle)
											{
												?><li><a href="collection.php?cid=<?php echo $colId ?>"><?php echo $colTitle ?></a></li><?php 
											}
											?>
										</ul>
									</li>
									<li><a href="vouchers.php">Gift Vouchers</a></li>
									<li>
										<a href="off-the-rack.php">Browse by</a>
										<ul>
											<li><a href="#">Product Type</a>
												<ul>
													<?php 
													foreach ($productTypes as $id => $name)
													{
														?><li><a href="browse.php?type=1&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
											<li><a href="#">Colour</a>
												<ul>
													<?php 
													foreach ($colours as $id => $name)
													{
														?><li><a href="browse.php?type=2&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
											<li><a href="#">Shape</a>
												<ul>
													<?php 
													foreach ($shapes as $id => $name)
													{
														?><li><a href="browse.php?type=3&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
											<li><a href="#">Size</a>
												<ul>
													<?php 
													foreach ($sizes as $id => $name)
													{
														?><li><a href="browse.php?type=4&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
											<li><a href="#">Material</a>
												<ul>
													<?php 
													foreach ($materials as $id => $name)
													{
														?><li><a href="browse.php?type=5&val=<?php echo $id ?>"><?php echo $name ?></a></li><?php 
													}
													?>
												</ul>
											</li>
										</ul>
									</li>
								</ul>
							</li>
							<li>
								<a href="press.php">Publicity</a>
								<ul>
									<?php 
									foreach ($publicityTypes as $publicityTypeId => $publicityType)
									{
										?><li><a href="press.php?typeid=<?php echo $publicityTypeId ?>"><?php echo $publicityType ?></a></li><?php 
									}
									?>
								</ul>
							</li>
							<li>
								<a href="links.php">Community</a>
								<ul>
									<?php 
									foreach ($linkCategories as $catId => $catName)
									{
										?><li><a href="links.php?catid=<?php echo $catId ?>"><?php echo $catName ?></a></li><?php 
									}
									?>
								</ul>
							</li>
							<li>
								<a href="#">Information</a>
								<ul>
									<li><a href="faq.php">FAQ</a></li>
									<li><a href="testimonials.php">Testimonials</a></li>
									<li><a href="sizing.php">Sizing Chart</a></li>
									<li><a href="about.php">About Ruby</a></li>
									<li><a href="contact.php">Contact</a></li>
									<li><a href="sitemap.php">Sitemap</a></li>
								</ul>
							</li>
						</ul>
					</td>
					<td align="right">
						<div>
							<form id="searchform">
								<div>
									<p>Search</p>
									<input id="inputString" type="text" size="15" value="" onkeyup="lookup(this.value, 6);" />
								</div>
								<div id="suggestions"></div>
							</form>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
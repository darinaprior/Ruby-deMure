<?php
/*
* THIS SCRIPT DELETES EVERYTHING IN THE search TABLE AND ADDS EVERYTHING FRESH
* USED FOR THE SEARCH BOX ON THE WEBSITE
* NOTE IT'S PROBABLY VERY INEFFICIENT!  BUT IT'LL DO FOR NOW
*/

require_once '../Include/connection.php';
require_once '../Include/functions.php';

if(!$mysqli) {
	// Show error if we cannot connect.
	echo 'ERROR: Failed to connect to DB';
} else {
	// STEP 1: GET DATA...
	
	// Get all the details we want to store and save them to one big array
	$searchContents = array();
	
	// Products, along with one image for each
	$sql = 'SELECT 
			p.ProdID, 
			p.CategoryID, 
			p.Title, 
			p.Description, 
			MIN(IFNULL(pi.priority, 1000))/*get highest-priority image*/, 
			pi.filename
		FROM Product p 
		LEFT JOIN product_image_TEMP pi on p.ProdID = pi.product_id
		WHERE p.Priority is not null
		AND pi.filename IS NOT NULL
		GROUP BY p.ProdID';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result(
			$id,
			$productCategoryId,
			$title,
			$desc,
			$imgId,
			$imgName
		);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
		
			// We want the thumbnail images
			$imgThumb = getProductImagePath($id, $imgName, 4/*thumb*/);
			
			// Add to array
			$searchContents[] = array(
				'category_id' => 1, /* products */
				'product_category_id' => $productCategoryId,
				'title' => $title,
				'description' => $desc,
				'url' => '/product.php?pid='.$id,
				'image_filepath' => $imgThumb
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	// Collections
	$sql = 'SELECT 
			c.CollectionID, 
			c.Title, 
			c.Description
		FROM Collection c 
		WHERE c.Current is not null';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title, $desc);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 2, /* collections */
				'product_category_id' => NULL,
				'title' => $title,
				'description' => $desc,
				'url' => '/collection.php?cid='.$id,
				'image_filepath' => NULL
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	// Materials
	$sql = 'SELECT DISTINCT
			m.MaterialID, 
			m.Name, 
			m.Description
		FROM Material m 
		INNER JOIN ProductMaterial pm on m.MaterialID = pm.MaterialID
		INNER JOIN Product p on pm.ProdID = p.ProdID
		WHERE p.Priority is not null';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title, $desc);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 3, /* materials */
				'product_category_id' => NULL,
				'title' => $title,
				'description' => $desc,
				'url' => '/browse.php?type=5&val='.$id,
				'image_filepath' => NULL
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	// Colours
	$sql = 'SELECT DISTINCT
			c.ColourID, 
			c.Description
		FROM Colour c
		INNER JOIN product_option_colour poc ON c.ColourID = poc.colour_id
		INNER JOIN product_option po ON poc.option_id = po.id
		INNER JOIN Product p on po.product_id = p.ProdID';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 4, /* colours */
				'product_category_id' => NULL,
				'title' => $title,
				'description' => NULL,
				'url' => '/browse.php?type=2&val='.$id,
				'image_filepath' => NULL
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	// Sizes
	$sql = 'SELECT DISTINCT
			s.SizeID, 
			s.Name
		FROM Size s
		INNER JOIN product_option_size pos ON s.SizeID = pos.size_id
		INNER JOIN Product p on pos.product_id = p.ProdID';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 6, /* sizes */
				'product_category_id' => NULL,
				'title' => $title,
				'description' => NULL,
				'url' => '/browse.php?type=4&val='.$id,
				'image_filepath' => NULL
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	// Product types
	$sql = 'SELECT DISTINCT
			pt.ProdTypeID,
			IFNULL(pt.NavName, pt.Description)
		FROM ProductType pt
		INNER JOIN Product p on pt.ProdTypeID = p.ProductTypeID';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 7, /* product types */
				'product_category_id' => NULL,
				'title' => $title,
				'description' => NULL,
				'url' => '/browse.php?type=1&val='.$id,
				'image_filepath' => NULL
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	// Shapes
	$sql = 'SELECT DISTINCT
			s.ShapeID,
			s.Description
		FROM Shape s
		INNER JOIN Product p on s.ShapeID = p.ProductTypeID';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 8, /* shapes */
				'product_category_id' => NULL,
				'title' => $title,
				'description' => NULL,
				'url' => '/browse.php?type=3&val='.$id,
				'image_filepath' => NULL
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	// Reviews
	$sql = 'SELECT
			r.id,
			IFNULL(r.name, \'anonymous\'),
			r.comment
		FROM review r
		INNER JOIN Product p ON r.product_id = p.ProdID
		WHERE r.status = 1 /* approved */
		AND r.main_page = 1 /* show on main page */';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title, $description);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 9, /* reviews */
				'product_category_id' => NULL,
				'title' => $title,
				'description' => $description,
				'url' => '/testimonials.php',
				'image_filepath' => NULL
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	
	
	// STEP 2: PROCESS DATA...
	
	// If we have any data to put in, delete whatever's already in the search table first
	$sql = 'DELETE FROM search;';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->execute();
		$stmt->close();
	}//if $stmt
	
	// Now go through our whole array and add everything to the search table
	foreach ($searchContents as $search) {
		$sql = 'INSERT INTO search (category_id, product_category_id, title, description, url, image_filepath)
			VALUES (?, ?, ?, ?, ?, ?);';
		$stmt = $mysqli->prepare($sql);
		if ($stmt) {
			$stmt->bind_param(
				'iissss',
				$mysqli->real_escape_string($search['category_id']),
				$mysqli->real_escape_string($search['product_category_id']),
				$mysqli->real_escape_string($search['title']),
				$mysqli->real_escape_string($search['description']),
				$mysqli->real_escape_string($search['url']),
				$mysqli->real_escape_string($search['image_filepath'])
			);
			$stmt->execute();
			$stmt->close();
		}//if $stmt
	}//foreach
	
}//if $mysqli

echo 'DONE';
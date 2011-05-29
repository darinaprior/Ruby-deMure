<?php
/*
* THIS SCRIPT DELETED EVERYTHING IN THE search TABLE AND ADDS EVERYTHING FRESH
* USED FOR THE SEARCH BOX ON THE WEBSITE
* NOTE IT'S PROBABLY VERY INEFFICIENT!  BUT IT'LL DO FOR NOW
*/

$db = new mysqli('localhost', 'rubydemu_ruby', 'thangi8F', 'rubydemu_dbRubydemure');

if(!$db) {
	// Show error if we cannot connect.
	echo 'ERROR: Failed to connect to DB';
} else {
	// Get all the details we want to store and save them to one big array
	$searchContents = array();
	
	// Products
	$sql = 'SELECT 
			p.ProdID, 
			p.Title, 
			p.Description, 
			pi.Filepath
		FROM Product p 
		LEFT JOIN ProductImage pi on p.DefaultImageID = pi.ProdImageID
		WHERE p.Priority is not null';
	$stmt = $db->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title, $desc, $img);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
		
			// We want the thumbnail images
			$imgThumb = str_replace("images/", "images/thumbs/", $img);
			
			// Add to array
			$searchContents[] = array(
				'category_id' => 1, /* products */
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
	$stmt = $db->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title, $desc);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 2, /* collections */
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
	$stmt = $db->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $title, $desc);
		$stmt->execute();
		while ($result = $stmt->fetch()) {
			// Add to array
			$searchContents[] = array(
				'category_id' => 3, /* materials */
				'title' => $title,
				'description' => $desc,
				'url' => '/browse.php?type=5&val='.$id,
				'image_filepath' => NULL
			);
		}//while
		$stmt->close();
	}//if $stmt
	
	// If we have any data to put in, delete whatever's already in the search table first
	$sql = 'DELETE FROM search;';
	$stmt = $db->prepare($sql);
	if ($stmt) {
		$stmt->execute();
		$stmt->close();
	}//if $stmt
	
	// Now go through our whole array and add everything to the search table
	foreach ($searchContents as $search) {
		$sql = 'INSERT INTO search (category_id, title, description, url, image_filepath)
			VALUES (?, ?, ?, ?, ?);';
		$stmt = $db->prepare($sql);
		if ($stmt) {
			$stmt->bind_param(
				'issss',
				$db->real_escape_string($search['category_id']),
				$db->real_escape_string($search['title']),
				$db->real_escape_string($search['description']),
				$db->real_escape_string($search['url']),
				$db->real_escape_string($search['image_filepath'])
			);
			$stmt->execute();
			$stmt->close();
		}//if $stmt
	}//foreach
	
}//if $db

echo 'DONE';
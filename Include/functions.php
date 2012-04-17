<?php 
/** THIS FILE CONTAINS COMMON FUNCTIONS **/

/**
 * Takes an array of colour names and formats them into a human-readable string
 * @param	array	$colours		- one-dimensional array of colour names
 * @param	bool	$suppressTitleCase	- OPTIONAL (default:false)
 				- should capitalisation of the first char be suppressed?
 * @return	string	- the formatted string
 * @example	formatColourString(array('Black','WHITE','red','yELloW')) => 'Black, white, red and yellow'
 * @example	formatColourString(array('BLACK')) => 'Black'
 * @example	formatColourString(array('BLACK'), true) => 'black'
 * @author	Darina Prior
 */
function formatColourString($colours, $suppressTitleCase=false)
{
	$colourString = '';
	
	// Split away the last colour so we can insert
	// the word "and" (if necessary)
	$lastColour = $colours[COUNT($colours)-1];
	unset($colours[COUNT($colours)-1]);
	
	// Put the string together
	if (COUNT($colours) > 0) {
		$colourString .= implode(', ', $colours).' and ';
	}
	$colourString .= $lastColour;
	
	// Make the sentence title case
	$colourString = strtolower($colourString);
	if (!$suppressTitleCase) {
		$colourString = ucfirst($colourString);
	}
	
	// Return the string
	return $colourString;
}

/**
 * Gets the root-relative filepath for a product image given the path stored in the DB, the 
 * category ID of the product and whether or not we want the thumbnail
 * 
 * @param	string	$path		- the bulk of the filepath, as stored in the DB (without "/images/bespoke" etc.)
 * @param	int	$categoryId	- the ID of the category the product's in e.g. 1=bespoke
 * @param	bool	$isThumb	- OPTIONAL(default:false) - should we return the thumbnail path?
 * 
 * @return	string	$fullPath	- the full filepath from the root directory (starting with "/")
 * 
 * @author	Darina Prior
 */
function getFullImagePath($path, $categoryId, $isThumb=FALSE)
{
	// Start with the basics
	$fullPath = '/images';
	
	// Handle blank images
	if (!isset($path) || $path == '') {
		if ($isThumb) {
			$fullPath .= '/img_not_avail_90.gif';
		} else {
			$fullPath .= '/img_not_avail_300.gif';
		}
	} else {
		// Thumbnail?
		if ($isThumb) {
			$fullPath .= '/thumbs';
		}
	
		// Fill in the category bit
		$fullPath .= '/products';
		switch($categoryId) {
			case 1:
				$fullPath .= '/bespoke';
				break;
			case 2:
				$fullPath .= '/pick_n_mix';
				break;
			case 3:
				$fullPath .= '/stock';
				break;
			default:
				// stay in the main images folder
		}
		
		// Finally, add on whatever's in the database
		$fullPath .= '/'.$path;
	}
	
	// Return the full path
	return $fullPath;
}

/**
 * Checks if the given product image size selection is valid e.g. 1=full, 2=medium etc.
 * 
 * @param	int	$size	- the size selection
 * @return	TRUE if valid, FALSE if not
 * @author	Darina Prior
 */
function isValidProductImageSize($size)
{
	switch($size) {
		case 1:
		case 2:
		case 3:
		case 4:
			return TRUE;
			break;
	}
	return FALSE;
}

/**
 * Gets the root-relative filepath for a product image given the filename stored in the DB, the 
 * product ID and what size we want e.g. medium
 * 
 * @param	int	$productId	- the ID of the category the product's in e.g. 1=bespoke
 * @param	string	$filename	- the bulk of the filepath, as stored in the DB (without "/images/bespoke" etc.)
 * @param	int	$size		- OPTIONAL(default:1) 1=full, 2=medium, 3=small, 4=thumbnail
 * 
 * @return	string	$fullPath	- the full filepath from the root directory (starting with "/")
 * 
 * @author	Darina Prior
 */
function getProductImagePath($productId, $filename, $size=1)
{
	/** Check that the selected size is valid */
	if (!isValidProductImageSize($size)) {
		return '';
	}
	
	// Start with the basics
	$fullPath = '/images';
	
	// Handle blank images
	if (!isset($filename) || $filename == '') {
		switch($size) {
			case 1:
				// Full size
				$fullPath .= '/img_not_avail_700.gif';
				break;
			case 2:
				// Medium size
				$fullPath .= '/img_not_avail_200.gif';
				break;
			case 3:
				// Small size
				$fullPath .= '/img_not_avail_90.gif';
				break;
			case 4:
				// Thumb size
				$fullPath .= '/img_not_avail_50.gif';
				break;
			default:
				// Default to medium
				$fullPath .= '/img_not_avail_200.gif';
		}//switch
	} else {
		// Product images are under the "products" directory
		$fullPath .= '/products';
		
		// Use the correct path for the image size we want
		switch($size) {
			case 1:
				// Full size
				$fullPath .= '/full';
				break;
			case 2:
				// Medium size
				$fullPath .= '/medium';
				break;
			case 3:
				// Small size
				$fullPath .= '/small';
				break;
			case 4:
				// Thumb size
				$fullPath .= '/thumb';
				break;
			default:
				// Default to medium
				$fullPath .= '/medium';
		}//switch
		
		// All the product images are in a subdirectory named with the product's ID
		$fullPath .= '/'.$productId;
		
		// Finally, add on the filename
		$fullPath .= '/'.$filename;
	}
	
	// Return the full path
	return $fullPath;
}

/**
 * Gets the root-relative filepath for a search results image given the path stored in the DB and
 * the search result category ID
 * 
 * @param	string	$path			- bulk of filepath as stored in the DB (without "/images/bespoke" etc.)
 * @param	int	$searchCategoryId	- ID of search result category e.g. 2=collections
 * @param	int	$productCategoryId	- ID of product category e.g. 1=bespoke 
 * @return	string	$fullPath	- the full filepath from the root directory (starting with "/")
 * @author	Darina Prior
 */
function getSearchImagePath($path, $searchCategoryId, $productCategoryId)
{
	// Start with the basics
	$fullPath = '/images';

	// For the search results images, we use the thumbnail size
	$fullPath .= '/thumbs';

	// Fill in the category bit
	// At the moment, we ONLY have images for products - ignore the rest
	$fullPath .= '/products';
	if ($searchCategoryId == 1) {
		// Products
		switch($productCategoryId) {
			case 1:
				$fullPath .= '/bespoke';
				break;
			case 2:
				$fullPath .= '/pick_n_mix';
				break;
			case 3:
				$fullPath .= '/stock';
				break;
			default:
				// stay in the upper directory
		}//switch
	}//if
	
	// Finally, add on whatever's in the database
	$fullPath .= '/'.$path;
	
	// Return the full path
	return $fullPath;
}

/**
 * Formats the given cost to EITHER no decimal places (if integer number) OR 2 decimal places
 * 
 * @param	float	$cost		- a product cost
 * @return	string	$formatted	- the formatted cost
 * @author	Darina Prior
 */
function formatPrice($cost)
{
	if(floor($cost) == $cost) {
		return number_format($cost, 0); // 0 decimal places
	}
	return number_format($cost, 2); 	// 2 decimal places
}

/**
 * Formats the styled tooltip for a product on the collection page
 * 
 * @param	string	$name		- name of the product
 * @param	string	$cost		- the formatted cost along with the currency
 * @param	array	$colours	- array of available hex colour values
 * @param	array	$sizes		- array of available sizes
 * 
 * @return	string	$tt	- the styled tooltip as a HTML string
 * 
 * @author	Darina Prior
 */
function formatCollectionTooltip($name, $cost, $colours, $sizes)
{
	// N.B. Nothing can be in double quotes as this whole string will later go in double quotes
	// Attributes must all be inside escaped single quotes e.g. class=\'myclass\'
	
	// Also deal with any quotes that might be in the title or in the sizes e.g. 17" meaning 17inch
	$name = str_replace('"', '\'\'', $name);
	foreach ($sizes as $key => $size) {
		$sizes[$key] = str_replace('"', '\'\'', $size);
	}
	
	// Start with the name and price
	$tt = '';
	$tt = '<table class=\'tblStd\' cellpadding=\'2\'>';
	$tt .= '<tr><td colspan=\'2\' align=\'center\'><b>'.$name.'</b></td></tr>';
	$tt .= '<tr><td colspan=\'2\' align=\'center\'>'.$cost.'</td></tr>';
	
	// Then colours (little colour blocks)
	$tt .= '<tr><td>Colours:</td><td><table class=\'tblStd\' cellpadding=\'0\'><tr>';
	foreach ($colours as $hex) {
		$tt .= '<td class=\'colour_block_collection\' style=\'background-color:#'.$hex.';\'></td>';
	}
	$tt .= '</tr></table></td></tr>';
	
	// Then sizes
	$tt .= '<tr><td>Sizes:</td><td>'.implode(' / ', $sizes).'</td></tr>';
	$tt .= '</table>';
	
	// Return the formatted string
	return $tt;
}

/**
 * Gets all "live" collections from the database
 * 
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLiveCollections()
{
	$collections = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Get data from DB */
	$sql = 'SELECT DISTINCT 
			CollectionID, 
			Title 
		FROM Collection 
		WHERE Current IS NOT NULL 
		ORDER BY Priority DESC';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($collectionId, $name);
		$stmt->execute();
		while ($stmt->fetch()) {
			$collections[$collectionId] = $name;
		}
		$stmt->close();
	}
	
	/** Return the array */
	return $collections;
}

/**
 * Gets all product types that have at least one product associated with them
 * 
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLiveProductTypes()
{
	$productTypes = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Get data from DB */
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
	
	/** Return the array */
	return $productTypes;
}

/**
 * Gets all colours that have at least one product associated with them
 * 
 * @param	int	$limit	OPTIONAL(default:null) - limit to this many results (null or 0 will return ALL)
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLiveColours($limit=null)
{
	$colours = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Set up the limit if one was specified */
	$limitClause = '';
	if (isset($limit) && $limit > 0) {
		$limitClause = 'LIMIT ?';
	}
	
	/** Get data from DB */
	$sql = 'SELECT DISTINCT 
			c.ColourID, 
			c.Description,
			COUNT(poc.id) AS NumProds 
		FROM Colour c 
		INNER JOIN product_option_colour poc ON c.ColourID = poc.colour_id 
		GROUP BY poc.colour_id 
		ORDER BY NumProds DESC 
		'.$limitClause;
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		if (isset($limit) && $limit > 0) {
			$stmt->bind_param('i', $limit);
		}
		$stmt->bind_result($colourId, $name, $numProds);
		$stmt->execute();
		while ($stmt->fetch()) {
			$colours[$colourId] = $name;
		}
		$stmt->close();
	}
	
	/** Return the array */
	return $colours;
}

/**
 * Gets all shapes that have at least one product associated with them
 * 
 * @param	int	$limit	OPTIONAL(default:null) - limit to this many results (null or 0 will return ALL)
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLiveShapes($limit=null)
{
	$shapes = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Set up the limit if one was specified */
	$limitClause = '';
	if (isset($limit) && $limit > 0) {
		$limitClause = 'LIMIT ?';
	}
	
	/** Get data from DB */
	$sql = 'SELECT DISTINCT 
			s.ShapeID, 
			s.Description, 
			COUNT(s.ShapeID) AS NumProds 
		FROM Shape s 
		INNER JOIN Product p ON s.ShapeID = p.ShapeID 
		GROUP BY s.ShapeID 
		ORDER BY NumProds DESC 
		'.$limitClause;
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		if (isset($limit) && $limit > 0) {
			$stmt->bind_param('i', $limit);
		}
		$stmt->bind_result($shapeId, $name, $numProds);
		$stmt->execute();
		while ($stmt->fetch()) {
			$shapes[$shapeId] = $name;
		}
		$stmt->close();
	}
	
	/** Return the array */
	return $shapes;
}

/**
 * Gets all sizes that have at least one product associated with them
 * 
 * @param	int	$limit	OPTIONAL(default:null) - limit to this many results (null or 0 will return ALL)
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLiveSizes($limit=null)
{
	$sizes = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Set up the limit if one was specified */
	$limitClause = '';
	if (isset($limit) && $limit > 0) {
		$limitClause = 'LIMIT ?';
	}
	
	/** Get data from DB */
	$sql = 'SELECT DISTINCT 
			s.SizeID, 
			s.Name, 
			COUNT(pos.id) AS NumProds 
		FROM Size s 
		INNER JOIN product_option_size pos ON s.SizeID = pos.size_id 
		GROUP BY pos.size_id 
		ORDER BY NumProds DESC 
		'.$limitClause;
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		if (isset($limit) && $limit > 0) {
			$stmt->bind_param('i', $limit);
		}
		$stmt->bind_result($sizeId, $name, $numProds);
		$stmt->execute();
		while ($stmt->fetch()) {
			$sizes[$sizeId] = $name;
		}
		$stmt->close();
	}
	
	/** Return the array */
	return $sizes;
}

/**
 * Gets all materials that have at least one product associated with them
 * 
 * @param	int	$limit	OPTIONAL(default:null) - limit to this many results (null or 0 will return ALL)
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLiveMaterials($limit=null)
{
	$materials = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Set up the limit if one was specified */
	$limitClause = '';
	if (isset($limit) && $limit > 0) {
		$limitClause = 'LIMIT ?';
	}
	
	/** Get data from DB */
	$sql = 'SELECT DISTINCT 
			m.MaterialID, 
			m.Name, 
			COUNT(m.MaterialID) AS NumProds 
		FROM Material m  
		INNER JOIN ProductMaterial pm ON m.MaterialID = pm.MaterialID 
		WHERE m.IncludeInNav = 1 
		GROUP BY m.MaterialID 
		ORDER BY NumProds DESC 
		'.$limitClause;
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		if (isset($limit) && $limit > 0) {
			$stmt->bind_param('i', $limit);
		}
		$stmt->bind_result($materialId, $name, $numProds);
		$stmt->execute();
		while ($stmt->fetch()) {
			$materials[$materialId] = $name;
		}
		$stmt->close();
	}
	
	/** Return the array */
	return $materials;
}

/**
 * Gets all "live" publicity types from the database
 * 
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLivePublicityTypes()
{
	$publicityTypes = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Get data from DB */
	$sql = 'SELECT DISTINCT 
			PublicityTypeId, 
			Type 
		FROM PublicityType 
		WHERE Priority >= 0 
		ORDER BY Priority DESC';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $name);
		$stmt->execute();
		while ($stmt->fetch()) {
			$publicityTypes[$id] = $name;
		}
		$stmt->close();
	}
	
	/** Return the array */
	return $publicityTypes;
}

/**
 * Gets all "live" link categories from the database
 * 
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLiveLinkCategories()
{
	$linkCategories = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Get data from DB */
	$sql = 'SELECT DISTINCT 
			LinkCategoryID, 
			Category 
		FROM LinkCategory 
		WHERE Priority IS NOT NULL 
		ORDER BY Priority DESC';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($id, $name);
		$stmt->execute();
		while ($stmt->fetch()) {
			$linkCategories[$id] = $name;
		}
		$stmt->close();
	}
	
	/** Return the array */
	return $linkCategories;
}

/**
 * Gets all "live" products from the database
 * 
 * @param	int	$categoryId	OPTIONAL(default:null) 
 * 			- 1=bespoke, 2=pick&mix, 3=readymade, 0/null=ALL
 * @return	array(<id> => <name>, <id> => <name>...)
 * @author	Darina Prior
 */
function getAllLiveProducts($categoryId=null)
{
	$products = array();
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Set up the category clause if one was specified */
	$categoryClause = '';
	if (isset($categoryId) && $categoryId > 0) {
		$categoryClause = 'AND CategoryID = ?';
	}
	
	/** Get data from DB */
	$sql = 'SELECT DISTINCT
			ProdID, 
			Title 
		FROM Product 
		WHERE Priority IS NOT NULL 
		'.$categoryClause.' 
		ORDER BY ProdID';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		if (isset($categoryId) && $categoryId > 0) {
			$stmt->bind_param('i', $categoryId);
		}
		$stmt->bind_result($id, $name);
		$stmt->execute();
		while ($stmt->fetch()) {
			$products[$id] = $name;
		}
		$stmt->close();
	}
	
	/** Return the array */
	return $products;
}

/**
 * Get the hex value of the given colour id from the database
 * 
 * @param	int	$colourId	- the ID of the colour
 * @return	string $hex OR FALSE on fail
 * @author	Darina Prior
 */
function getColourHex($colourId)
{
	$hex = FALSE;
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Try to get the value from the DB */
	$sql = 'SELECT HexValue
		FROM Colour
		WHERE ColourID = ?
		LIMIT 1';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_param('i', intval($colourId));
		$stmt->bind_result($hex);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
	}//if $stmt
	
	/** Return the value */
	return $hex;
}

/**
 * Get single image filepath for the given product ID
 * 
 * @param	int	$productId	- the ID of the product
 * @param	int	$size		- OPTIONAL(default:1) 1=full, 2=medium, 3=small, 4=thumbnail
 * @return	string $imagePath OR FALSE on fail
 * @author	Darina Prior
 */
function getSingleImageForProduct($productId, $size=1)
{
	/** Check that the selected size is valid */
	if (!isValidProductImageSize($size)) {
		return FALSE;
	}
	
	$imagePath = FALSE;
	$imageName = '';
	
	/**
	 * Try to access mysqli connection from outside scope of function.
	 * Otherwise, connect to DB here.
	 */
	global $mysqli;
	if (!isset($mysqli)) {
		require_once 'connection.php';
	}
	
	/** Try to get the highest priority image name from the DB */
	$sql = 'SELECT filename
		FROM product_image
		WHERE product_id = ?
		AND filename IS NOT NULL
		ORDER BY IFNULL(priority, 1000), id
		LIMIT 1';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_param('i', intval($productId));
		$stmt->bind_result($imageName);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
	}//if $stmt
	
	/** Now get the full path for the desired size */
	$imagePath = getProductImagePath($productId, $imageName, $size);
	
	/** Return the result */
	return $imagePath;
}
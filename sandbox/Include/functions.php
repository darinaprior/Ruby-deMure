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
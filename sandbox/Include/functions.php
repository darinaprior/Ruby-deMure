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
function getFullImagePath_TEMP($path, $categoryId, $size=1)
{
	// Start with the basics
	$fullPath = '/images';
	
	// Handle blank images
	if (!isset($path) || $path == '') {
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
	
		// Fill in the category bit
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
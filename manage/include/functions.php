<?php
/** THIS FILE CONTAINS COMMON FUNCTIONS FOR THE MANAGE APPLICATION **/

/**
 * Note: JS CROPPING CODE IN SOME FUNCTIONS HERE ADAPTED FROM...
 * 
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */

/** Some constants required */
define('PRODUCT_IMAGE_MAX_DIMENSION_FULL', 700);
define('PRODUCT_IMAGE_MAX_DIMENSION_FULL_LOWRES', 300);
define('PRODUCT_IMAGE_MAX_DIMENSION_MEDIUM', 200);
define('PRODUCT_IMAGE_MAX_DIMENSION_SMALL', 90);
define('PRODUCT_IMAGE_MAX_DIMENSION_THUMB', 50);

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
 * Create a product image with exact dimensions.  Currently used for the FULL and
 * MEDIUM product images
 * 
 * @param	bool	$isLowRes	- OPTIONAL(default:FALSE) limit to the smaller "full" size
 * @return	
 * @author	Darina Prior
 */
function uploadProductImageExact(
	$sourcePath, $sourceWidth, $sourceHeight, $sourceX, $sourceY,
	$isLowRes=FALSE
)
{
	$error = '';
	
	try {
		
		/** 
		 * The source image is stored in a "temp" subdirectory under the "full" directory
		 * Remove "temp" to get the image path for the full resolution image
		 * Then for the other sizes, just replace "full" with "medium" etc.
		 */
		$destPathLarge = str_replace('temp/', '', $sourcePath);
		$destPathMedium = str_replace('/full/', '/medium/', $destPathLarge);
		
		/** Get the aspect ratio of the user's selection (width to height) */
		$aspectRatio = ($sourceWidth/$sourceHeight);
		
		/** Use the correct dimension for the full image (check for low res) */
		$maxDimensionFull = PRODUCT_IMAGE_MAX_DIMENSION_FULL;
		if ($isLowRes) {
			$maxDimensionFull = PRODUCT_IMAGE_MAX_DIMENSION_FULL_LOWRES;
		}
		
		/** Specify the width and height of the DESTINATION images */
		if ($aspectRatio == 1) {
			// Square selection
			$destWidthLarge = $maxDimensionFull;
			$destHeightLarge = $maxDimensionFull;
			$destWidthMedium = PRODUCT_IMAGE_MAX_DIMENSION_MEDIUM;
			$destHeightMedium = PRODUCT_IMAGE_MAX_DIMENSION_MEDIUM;
			
		} else if ($aspectRatio > 1) {
			// Landscape
			$destWidthLarge = $maxDimensionFull;
			$destHeightLarge = ($maxDimensionFull / $aspectRatio);
			$destWidthMedium = PRODUCT_IMAGE_MAX_DIMENSION_MEDIUM;
			$destHeightMedium = (PRODUCT_IMAGE_MAX_DIMENSION_MEDIUM / $aspectRatio);
			
		} else {
			// Portrait
			$destWidthLarge = ($maxDimensionFull * $aspectRatio);
			$destHeightLarge = $maxDimensionFull;
			$destWidthMedium = (PRODUCT_IMAGE_MAX_DIMENSION_MEDIUM * $aspectRatio);
			$destHeightMedium = PRODUCT_IMAGE_MAX_DIMENSION_MEDIUM;
		}
		
		/** The destination X and Y coords are always 0 */
		$destLargeX = 0;
		$destLargeY = 0;
		$destMediumX = 0;
		$destMediumY = 0;
		
		/** Create the SOURCE image resource */
		$sourceImage = imagecreatefromjpeg($sourcePath);
		
		/** 
		 * Create the DESTINATION image resources with the correct height and width
		 * Use truecolor to avoid problems when resampling
		 */
		$destImageLarge = ImageCreateTrueColor($destWidthLarge, $destHeightLarge);
		$destImageMedium = ImageCreateTrueColor($destWidthMedium, $destHeightMedium);
				
		/** 
		 * Copy and resize the selected part of the source image, with resampling, to...
		 * ...the large image
		 */
		imagecopyresampled(
			$destImageLarge,
			$sourceImage,
			$destLargeX,
			$destLargeY,
			$sourceX,
			$sourceY,
			$destWidthLarge,
			$destHeightLarge,
			$sourceWidth,
			$sourceHeight
		);
		/** ...the medium image */
		imagecopyresampled(
			$destImageMedium,
			$sourceImage,
			$destMediumX,
			$destMediumY,
			$sourceX,
			$sourceY,
			$destWidthMedium,
			$destHeightMedium,
			$sourceWidth,
			$sourceHeight
		);
		
		/** Output to the destination image files */
		imagejpeg($destImageLarge,  $destPathLarge, 100/*quality*/);
		imagejpeg($destImageMedium,  $destPathMedium, 100/*quality*/);
		
	} catch (Exception $e) {
		$error .= '<br/>ERROR in function uploadProductImageExact: '.$e->getMessage();
	}
	
	/** If there was an error, return the error message.  Otherwise, return TRUE */
	if (isset($error) && $error != '') {
		return $error;
	}
	return TRUE;
}

/**
 * Create a product image that's ALWAYS limited to square dimensions.
 * Currently used for the SMALL and THUMBNAIL product images
 * 
 * @param	
 * @return	
 * @author	Darina Prior
 */
function uploadProductImageSquare($sourcePath, $sourceWidth, $sourceX, $sourceY)
{
	$error = '';
	
	try {
		$sourceHeight = $sourceWidth;	// square image
		
		/** 
		 * The source image is stored in a "temp" subdirectory under the "full" directory
		 * Remove "temp" to get the image path for the full resolution image
		 * Then for the other sizes, just replace "full" with "medium" etc.
		 */
		$destPathFull = str_replace('temp/', '', $sourcePath);
		$destPathSmall = str_replace('/full/', '/small/', $destPathFull);
		$destPathThumb = str_replace('/full/', '/thumb/', $destPathFull);
		
		/** Specify the width, height and X and Y coords of the destination images */
		$destWidthSmall = PRODUCT_IMAGE_MAX_DIMENSION_SMALL;
		$destHeightSmall = PRODUCT_IMAGE_MAX_DIMENSION_SMALL;
		$destWidthThumb = PRODUCT_IMAGE_MAX_DIMENSION_THUMB;
		$destHeightThumb = PRODUCT_IMAGE_MAX_DIMENSION_THUMB;
		$destSmallX = 0;
		$destSmallY = 0;
		$destThumbX = 0;
		$destThumbY = 0;
		
		/** Create the SOURCE image resource */
		$sourceImage = imagecreatefromjpeg($sourcePath);
		
		/** 
		 * Create the DESTINATION image resources with the correct height and width
		 * Use truecolor to avoid problems when resampling
		 */
		$destImageSmall = ImageCreateTrueColor($destWidthSmall, $destHeightSmall);
		$destImageThumb = ImageCreateTrueColor($destWidthThumb, $destHeightThumb);
				
		/** 
		 * Copy and resize the selected part of the source image, with resampling, to...
		 * ...the small image
		 */
		imagecopyresampled(
			$destImageSmall,
			$sourceImage,
			$destSmallX,
			$destSmallY,
			$sourceX,
			$sourceY,
			$destWidthSmall,
			$destHeightSmall,
			$sourceWidth,
			$sourceHeight
		);
		/** ...the thumbnail image */
		imagecopyresampled(
			$destImageThumb,
			$sourceImage,
			$destThumbX,
			$destThumbY,
			$sourceX,
			$sourceY,
			$destWidthThumb,
			$destHeightThumb,
			$sourceWidth,
			$sourceHeight
		);
		
		/** Output to the destination image files */
		imagejpeg($destImageSmall,  $destPathSmall, 100/*quality*/);
		imagejpeg($destImageThumb,  $destPathThumb, 100/*quality*/);
		
	} catch (Exception $e) {
		$error .= '<br/>ERROR in function uploadProductImageSquare: '.$e->getMessage();
	}
	
	/** If there was an error, return the error message.  Otherwise, return TRUE */
	if (isset($error) && $error != '') {
		return $error;
	}
	return TRUE;
}

/**
 * Map image to product in database
 * 
 * @param	
 * @return	
 * @author	Darina Prior
 */
function mapImageToProduct($productId, $filename, $caption)
{
	$error = '';
	
	/** Access variables outside the scope of this function */
	global $mysqli;
	
	try {
		/** Handle empty captions */
		$formatCaption = ($caption != '') ? $caption : NULL;
		
		/** First, check if the record already exists */
		$numRows = 0;
		$sql = 'SELECT COUNT(*) AS NumRows
			FROM product_image
			WHERE product_id = ?
			AND filename = ?';
		$stmt = $mysqli->prepare($sql);
		if ($stmt) {
			$stmt->bind_param(
				'is',
				$productId,
				$filename
			);
			$stmt->bind_result($numRows);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		} else {
			$error .= '<br/>ERROR in function mapImageToProduct: could not prepare MySQL statement - '.$mysqli->error;
		}//if $stmt
		
		/** Insert into the database if not already there */
		if ($numRows < 1) {
			$sql = 'INSERT INTO product_image (product_id, filename, caption)
				VALUES (?, ?, ?)';
			$stmt = $mysqli->prepare($sql);
			if ($stmt) {
				$stmt->bind_param(
					'iss',
					$productId,
					$filename,
					$formatCaption
				);
				if (!$stmt->execute()) {
					$error .= '<br/>ERROR in function mapImageToProduct: could not insert record into database - '.$mysqli->error;
				}
				$stmt->close();
			} else {
				$error .= '<br/>ERROR in function mapImageToProduct: could not prepare MySQL statement - '.$mysqli->error;
			}//if $stmt
		}//if $numRows
		
	} catch (Exception $e) {
		$error .= '<br/>ERROR in function mapImageToProduct: '.$e->getMessage();
	}
	
	/** If there was an error, return the error message.  Otherwise, return TRUE */
	if (isset($error) && $error != '') {
		return $error;
	}
	return TRUE;
}
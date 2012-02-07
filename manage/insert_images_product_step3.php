<?php
require_once 'include/check_login.php';
/**
 * N.B. JS CROPPING CODE ADAPTED FROM...
 * 
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */

// Get the user inputs
$productId = 0;
$directory = '';
$caption = '';
if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '') {
	$productId = intval($_REQUEST['pid']);
}
if (isset($_REQUEST['path']) && $_REQUEST['path'] != '') {
	$directory = $_REQUEST['path'];
}
if (isset($_REQUEST['cap']) && $_REQUEST['cap'] != '') {
	$caption = $_REQUEST['cap'];
}

/** 
 * Get the full path to the TEMP directory under the user's specified directory
 * (relative to current directory)
 */
$fullPathTemp = '../images/products/'.$directory.'/temp/';

/** 
 * Get all the images in the TEMP directory inside the user's specified directory
 * Only accept jpg, JPG, png and PNG
 */
$filetypes = '{*.jpg,*.JPG,*.png,*.PNG}';
$options = GLOB_BRACE;
$images = glob($fullPathTemp.$filetypes, $options);

// If the user just posted, process the FIRST image in the directory
require_once 'include/connection.php';
$processedImages = FALSE;
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	try {
		// Source and destination filepaths MUST BE relative to the current directory
		$sourceFile = $images[0];
		$destMainFile = str_replace('temp/', '', $sourceFile);
		$destThumbFile = str_replace('images/', 'images/thumbs/', $destMainFile);
		
		// Specify the width and height and the X and Y coords of the SOURCE image
		$sourceWidth = intval($_POST['w']);
		$sourceHeight = intval($_POST['h']);
		$sourceX = intval($_POST['x']);
		$sourceY = intval($_POST['y']);
		
		// Specify the width and height and the X and Y coords of the DESTINATION images
		$destMainWidth = 300;
		$destMainHeight = 300;
		$destMainX = 0;
		$destMainY = 0;
		$destThumbWidth = 90;
		$destThumbHeight = 90;
		$destThumbX = 0;
		$destThumbY = 0;
		
		// Create the SOURCE image resource
		$sourceImage = imagecreatefromjpeg($sourceFile);
		
		// Create the DESTINATION image resource with the correct height and width
		// Use truecolor to avoid problems when resampling
		$destMainImage = ImageCreateTrueColor($destMainWidth, $destMainHeight);
		$destThumbImage = ImageCreateTrueColor($destThumbWidth, $destThumbHeight);
				
		// Copy and resize the selected part of the source image with resampling
		imagecopyresampled(
			$destMainImage,
			$sourceImage,
			$destMainX,
			$destMainY,
			$sourceX,
			$sourceY,
			$destMainWidth,
			$destMainHeight,
			$sourceWidth,
			$sourceHeight
		);
		imagecopyresampled(
			$destThumbImage,
			$sourceImage,
			$destThumbX,
			$destThumbY,
			$sourceX,
			$sourceY,
			$destThumbWidth,
			$destThumbHeight,
			$sourceWidth,
			$sourceHeight
		);
		
		// Output to the destination image files
		imagejpeg($destMainImage,  $destMainFile, 100/*quality*/);
		imagejpeg($destThumbImage,  $destThumbFile, 100/*quality*/);
		
		// Finally, delete the source file from the temp directory
		if (!unlink($sourceFile)) {
			$error .= '<br/>ERROR: Unable to delete the source file from the temp directory';
		}
		
		// Image processed (with or without errors)
		$processedImages = TRUE;
		
		/** 
		 * Now map the image to the product in the database
		 * Source file is in format "../images/products/USER_PATH/temp/IMAGE_NAME"
		 * We want to strip out "../images/products" AND the first part of 
		 * the user path e.g. "stock" AND the "temp" part
		 * So we'll end up with "USER_PATH_WITHOUT_FIRST_PART/IMAGE_NAME"
		 */				
		$parts = explode('/', $sourceFile);
		$newParts = array_slice($parts, 4);	// dump the 1st 4 parts e.g."../images/products/stock"
		$dbFilepath = implode('/', $newParts);
		$dbFilepath = str_replace('temp/', '', $dbFilepath);	// get rid of the "temp/" part
		
		// Handle empty captions
		$formatCaption = ($caption != '') ? $caption : NULL;
		
		/** @todo CHECK FIRST THAT THIS RECORD DOESN'T ALREADY EXIST! */
		// Insert into the database
		$sql = 'INSERT INTO product_image (product_id, filepath, caption)
			VALUES (?, ?, ?)';
		$stmt = $mysqli->prepare($sql);
		if ($stmt) {
			$stmt->bind_param(
				'iss',
				$productId,
				$dbFilepath,
				$formatCaption
			);
			if (!$stmt->execute()) {
				$error .= '<br/>ERROR: could not insert record into database - '.$mysqli->error;
			}
		} else {
			$error .= '<br/>ERROR: could not prepare MySQL statement - '.$mysqli->error;
		}//if $stmt
		
	} catch (Exception $e) {
		$processedImages = TRUE;
		$error .= '<br/>ERROR: '.$e->getMessage();
	}
	
	/** Get all the images again as one of them may have been deleted! */
	$images = array();
	$images = glob($fullPathTemp.$filetypes, $options);
	
	/** If there are no images left, delete the TEMP directory */
	if (COUNT($images) == 0) {
		rmdir($fullPathTemp);
	}
}

// Get details for this product
$productType = '';
$productName = '';
$sql = 'SELECT
		pt.Description,
		p.Title
	FROM Product p
	INNER JOIN ProductType pt ON p.ProductTypeID = pt.ProdTypeID
	WHERE p.ProdID = ?';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_param('i', $productId);
	$stmt->bind_result($productType, $productName);
	$stmt->execute();
	$stmt->fetch();
}//if $stmt

// Show the rest of the page...
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Ruby deMure - Add images for <?php echo $productName; ?> - Step 3</title>
	<meta name="DESCRIPTION" content="" />
	<meta name="KEYWORDS" content="" />
	<link rel="STYLESHEET" href="css/styles.css" type="text/css" >
	
	<!-- jCrop styles and scripts -->
	<link rel="stylesheet" href="jcrop/css/jquery.Jcrop.css" type="text/css" />
	<script src="/js/jquery.min.js"></script>
	<script src="/jcrop/js/jquery.Jcrop.js"></script>
	<script language="Javascript">
		$(function(){

			$('#cropbox').Jcrop({
				aspectRatio: 1,
				onSelect: updateCoords
			});

		});
		function updateCoords(c)
		{
			$('#x').val(c.x);
			$('#y').val(c.y);
			$('#w').val(c.w);
			$('#h').val(c.h);
		};
		function checkCoords()
		{
			if (parseInt($('#w').val())) return true;
			alert('Please select a crop region then press submit.');
			return false;
		};
	</script>
</head>

<body>

<?php
echo $headerTable;
?>

<!-- centering table -->
<table style="width:100%;">
<tr>
<td align="center">
<h2>Add image(s) for <?php echo $productName; ?> (<?php echo $productType; ?>) - Step 3</h2>

<?php
/** 
 * Rather than trying to figure out how to have multiple jCrop forms on one page
 * I'll just force one image at a time (always the first one in the directory)
 */
echo 'Processing 1 of '.COUNT($images).' images...';
foreach ($images as $key => $path) {
	if ($key == 0) {
		echo '<br/><font color="blue">Processing now: '.$path.'</font>';
	} else {
		echo '<br/>'.$path;
	}
}

/** Now show a jCrop form for just the first image */
$relativePath = $images[0];
$url = str_replace('../images/', 'http://rubydemure.com/images/', $relativePath);
$parts = explode('/', $relativePath);
$imageName = $parts[COUNT($parts)-1];
?>
<table border="1">
	<?php
	if ($processedImages) {
		if (isset($error) && $error != '') {
			echo '<tr><td style="color:red;"><h2>'.$error.'</h2></td></tr>';
		} else {
			echo '<tr><td style="color:green;"><h2>DONE!</h2></td></tr>';
		}//if $error
	}//if $processedImages
	?>
	<tr>
		<td colspan="9" style="font-size:12px;">
			<div id="outer" class="outer">
			<div class="jcExample">
			<div class="article">
				<h3><?php echo $imageName; ?></h3>
				<img src="<?php echo $url; ?>" id="cropbox" style="max-height:500px; max-width:500px;" />
				<form method="post" onsubmit="return checkCoords();">
					<input type="text" id="cap" name="cap" value="" />
					<br/>
					<input type="hidden" id="x" name="x" />
					<input type="hidden" id="y" name="y" />
					<input type="hidden" id="w" name="w" />
					<input type="hidden" id="h" name="h" />
					<input type="hidden" id="pid" name="pid" value="<?php echo $productId; ?>" />
					<input type="hidden" id="path" name="path" value="<?php echo $directory; ?>" />
					<input type="submit" value="Crop, resize and save" />
				</form>
				The image will be automatically resized to the correct dimensions for 
				the main and thumbnail images.  It will be saved to the appropriate directories
				and added to the database.
			</div>
			</div>
			</div>
		</td>
	</tr>		
</table>

<!-- end of centering table -->	
</td>
</tr>
</table>

</body>
</html>
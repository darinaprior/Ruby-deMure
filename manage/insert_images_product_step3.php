<?php
require_once 'include/check_login.php';
require_once 'include/connection.php';
require_once 'include/functions.php';

/** Get the user inputs */
$productId = 0;
$isLowRes = FALSE;
$caption = '';
if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '') {
	$productId = intval($_REQUEST['pid']);
}
if (isset($_REQUEST['lowres']) && $_REQUEST['lowres'] == 'on') {
	$isLowRes = TRUE;
}
if (isset($_REQUEST['cap']) && $_REQUEST['cap'] != '') {
	$caption = $_REQUEST['cap'];
}

/** 
 * Get the full path to the TEMP directory under the user's specified directory
 * (relative to current directory)
 */
$fullPathTemp = '../images/products/full/'.$productId.'/temp/';

/** Get all the images in there (only accept jpg, JPG, png and PNG) */
$filetypes = '{*.jpg,*.JPG,*.png,*.PNG}';
$images = glob($fullPathTemp.$filetypes, GLOB_BRACE);

/** Did the user just post one of the forms? */
$processingMain = FALSE;
$processingThumbs = FALSE;
$processingThumbsAuto = FALSE;
$sourcePath = '';
$sourceWidth = 0;
$sourceHeight = 0;
$sourceX = 0;
$sourceY = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($_POST['action'] == 'main') {
		$processingMain = TRUE;
	} else if ($_POST['action'] == 'thumbs') {
		$processingThumbs = TRUE;
	}
	
	/** 
	 * Get the full path (relative to current directory) for the SOURCE image
	 * i.e. the FIRST image in the temp directory - we're processing one at a time
	 */
	$sourcePath = $images[0];
	
	/** Specify the width and height and the X and Y coords of the SOURCE image */
	$sourceWidth = intval($_POST['w']);
	$sourceHeight = intval($_POST['h']);
	$sourceX = intval($_POST['x']);
	$sourceY = intval($_POST['y']);
}//if POST

/** 
 * If the user just posted the "main" form, process the first image in the 
 * directory to FULL and MEDIUM images
 */
$error = '';
$mainProcessed = FALSE;
if ($processingMain) {

	try {
		/** Try to upload the FULL and MEDIUM images (exact dimensions selected) */
		$uploaded = uploadProductImageExact(
			$sourcePath, $sourceWidth, $sourceHeight, $sourceX, $sourceY, $isLowRes
		);
		if ($uploaded !== TRUE) {
			$error .= $uploaded;
		}
		
 		/** Image processed (with or without errors) */
		$mainProcessed = TRUE;
		
		/** Map the filename and caption to the product (if not already mapped) */
		$parts = explode('/', $sourcePath);
		$filename = $parts[COUNT($parts)-1];
		$mapped = mapImageToProduct($productId, $filename, $caption);
		if ($mapped !== TRUE) {
			$error .= $mapped;
		}
		
		/** 
		 * If the user selected a square area, we don't have to get their input for
		 * the small images - just process them automatically 
		 */
		if ($sourceWidth == $sourceHeight) {
			$processingThumbsAuto = TRUE;
		}
		
	} catch (Exception $e) {
		$mainProcessed = TRUE;
		$error .= '<br/>ERROR: '.$e->getMessage();
	}
}

/** 
 * If the user just posted the "thumbs" form OR if we've just processed a SQUARE "main" image, 
 * then process the first image in the directory to SMALL and THUMBNAIL images
 */
$thumbsProcessed = FALSE;
if ($processingThumbs || $processingThumbsAuto) {

	/** Turn off $mainProcessed so we don't mix up the forms later on */
	$mainProcessed = FALSE;

	try {
		/** 
		 * If we're processing automatically (i.e. it's a square selection) then
		 * use the original "temp" file as the source image.
		 * But if we're here because the user just submitted the "thumbs" form
		 * then use the recently created LARGE file as the source image.
		 */
		if ($processingThumbsAuto) {
			$sourcePathForThumbs = $sourcePath;
		} else {
			$sourcePathForThumbs = str_replace('/temp', '', $sourcePath);
		}
		
		/** Try to upload the SMALL and THUMBNAIL images (always square) */
		$uploaded = uploadProductImageSquare($sourcePathForThumbs, $sourceWidth, $sourceX, $sourceY);
		if ($uploaded !== TRUE) {
			$error .= $uploaded;
		}
		
 		/** Image processed (with or without errors) */
		$thumbsProcessed = TRUE;
		
		/** Delete the original source file from the TEMP directory */
		if (!unlink($sourcePath)) {
			$error .= '<br/>ERROR: Unable to delete the source file from the temp directory';
		}
		
	} catch (Exception $e) {
		$thumbsProcessed = TRUE;
		$error .= '<br/>ERROR: '.$e->getMessage();
	}
	
	/** Get all the images again as one of them may have been deleted! */
	$images = array();
	$images = glob($fullPathTemp.$filetypes, GLOB_BRACE);
	
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
			// Initialise the Jcrop box and restrict to square selection by default
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
		
		// Once the page has loaded, allow user to remove the "square" restriction
		$(document).ready(function() {
			$("#restrict_dimensions").click(function() {
				
				// Scrap the Jcrop box
				$.Jcrop('#cropbox').destroy();
				
				// Re-initialise the Jcrop box...
				if ($("#restrict_dimensions").is(":checked")) {
					// ...with square restriction re-applied
					$('#cropbox').Jcrop({
						aspectRatio: 1,
						onSelect: updateCoords
					});
				} else {
					// ...with square restriction removed
					$('#cropbox').Jcrop({
						onSelect: updateCoords
					});
				}
			});
		});
		
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
	if ($mainProcessed || $thumbsProcessed) {
		if (isset($error) && $error != '') {
			echo '<tr><td style="color:red;"><h2>'.$error.'</h2></td></tr>';
		} else {
			echo '<tr><td style="color:green;"><h2>DONE!</h2></td></tr>';
		}//if $error
	}//if
	
	
	if ($mainProcessed) {
		/** 
		 * THUMBS FORM
		 * When selecting an area for the smaller images, we want to work from the
		 * larger image we've just created rather than the original source file
		 */
		$url = str_replace('/temp', '', $url);
		$action = 'thumbs';
		$formTitle = 'SMALLER IMAGES';
		$showInputRestrict = FALSE;
		$showInputLowRes = FALSE;
		$showInputCaption = FALSE;
		$instructions = 'The image will be automatically resized to the correct dimensions for 
				the SMALL and THUMBNAIL images.  It will be saved to the 
				appropriate directories and the TEMP source file will be deleted.';
	} else {
		/** MAIN FORM */
		$action = 'main';
		$formTitle = 'MAIN IMAGES';
		$showInputRestrict = TRUE;
		$showInputLowRes = TRUE;
		$showInputCaption = TRUE;
		$instructions = 'The image will be automatically resized to the correct dimensions for 
				the FULL and MEDIUM images.  It will be saved to the 
				appropriate directories and added to the database.';
		$instructions .= '<br/>For square selections, the SMALL and THUMBNAIL images will
				also be created automatically.';
		$instructions .= '<br/>For non-square selections, the next step will be for the
				SMALL and THUMBNAIL images.';
	}//if $mainProcessed
	?>
	<tr>
		<td colspan="9" style="font-size:12px;">
			<div id="outer" class="outer">
			<div class="jcExample">
			<div class="article">
				<h3><?php echo $formTitle; ?> [<?php echo $imageName; ?>]</h3>
				
				<?php
				if ($showInputRestrict) {
					echo '<input type="checkbox" id="restrict_dimensions" checked="checked" />';
					echo 'Restrict to square';
				}
				?>
				
				<img src="<?php echo $url; ?>" id="cropbox" />
				<form method="post" onsubmit="return checkCoords();">
					<?php
					if ($showInputRestrict) {
						echo '<input type="checkbox" id="lowres" name="lowres" />';
						echo 'LOW RES (MAX 300px)<br/>';
					}
					/** Only show the caption input for the "main" form */
					if ($showInputCaption) {
						echo 'Caption: <input type="text" id="cap" name="cap" value="" />';
					}
					?>
					<br/>
					<input type="hidden" id="x" name="x" />
					<input type="hidden" id="y" name="y" />
					<input type="hidden" id="w" name="w" />
					<input type="hidden" id="h" name="h" />
					<input type="hidden" id="pid" name="pid" value="<?php echo $productId; ?>" />
					<input type="hidden" name="action" value="<?php echo $action; ?>" />
					<input type="submit" value="Crop, resize and save" />

				</form>
				<?php echo $instructions; ?>
			</div>
			</div>
			</div>
		</td>
	</tr>
	
	<?php
	/** Form to move on to the next step **/
	?>
	<tr>
		<td colspan="9" style="font-size:12px;">
			<form method="get" action="sort_order_product_images.php">
				<input type="hidden" name="pid" value="<?php echo $productId; ?>" />
				<input type="submit" value="Continue to sort order >>" />
			</form>
		</td>
	</tr>
</table>

<!-- end of centering table -->	
</td>
</tr>
</table>

</body>
</html>
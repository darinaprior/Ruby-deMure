<?php
// If the user just posted, save the file to the directory
$uploaded = FALSE;
$error = '';
$productId = 0;
$directory = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$productId = intval($_POST['pid']);
	$directory = $_POST['path'];
	
	try {
		// Get the details we'll need from the file the user selected
		$fileName = $_FILES['userfile']['name'];
		$fileTempName = $_FILES['userfile']['tmp_name'];
		
		// We'll upload to a TEMP directory inside the requested directory
		// Path must be relative to current directory
		$tempDirectory = '../images/products/'.$directory.'/temp/';
		if(!file_exists($tempDirectory) || !is_dir($tempDirectory)) {
			if (!mkdir($tempDirectory)) {
				$error = 'ERROR: failed to create TEMP directory';
			}
		}
		
		// Now try to upload the file
		$uploadFile = $tempDirectory.basename($fileName);
		if ( move_uploaded_file($fileTempName, $uploadFile) ) {
			$uploaded = TRUE;
		} else {
			$error = 'ERROR: failed to upload the file';
		}
		
	} catch (Exception $e) {
		$error = 'ERROR: '.$e->getMessage();
	}
} else {
	if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '') {
		$productId = intval($_REQUEST['pid']);
	}
	if (isset($_REQUEST['path']) && $_REQUEST['path'] != '') {
		$directory = $_REQUEST['path'];
	}
}//if POST

// Otherwise show the page 
require_once 'include/check_login.php';
require_once 'include/connection.php';

// Get details for this product
// Get the user inputs
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
	<title>Ruby deMure - Add images for <?php echo $productName; ?> - Step 2</title>
	<meta name="DESCRIPTION" content="" />
	<meta name="KEYWORDS" content="" />
	<link rel="STYLESHEET" href="css/styles.css">
</head>

<body>

<?php
echo $headerTable;
?>

<!-- centering table -->
<table style="width:100%;">
<tr>
<td align="center">
<h2>Add image(s) for <?php echo $productName; ?> (<?php echo $productType; ?>) - Step 2</h2>

<table>
	<?php
	if ($uploaded) {
		echo '<tr><td style="color:green;"><h2>DONE!</h2></td></tr>';
	}
	if (isset($error) && $error != '') {
		echo '<tr><td style="color:red;"><h2>'.$error.'</h2></td></tr>';
	}
	?>
	<tr>
		<td colspan="9" style="font-size:12px;">
			<form enctype="multipart/form-data" method="POST">
				<input name="userfile" type="file" style="width:500px;" />
				<br/>
				
				<input type="hidden" name="MAX_FILE_SIZE" value="512000" />
				<input type="hidden" name="pid" value="<?php echo $productId; ?>" />
				<input type="hidden" name="path" value="<?php echo $directory; ?>" />
				<input type="submit" value="Upload file" />
			</form>
			<br/><br/>
			<form method="get" action="insert_images_product_step3.php">
				<input type="hidden" name="pid" value="<?php echo $productId; ?>" />
				<input type="hidden" name="path" value="<?php echo $directory; ?>" />
				<input type="submit" value="Continue to step 3 >>" />
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
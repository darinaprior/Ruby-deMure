<?php
// If the user just posted, check the directory and move on to step 2
$productId = 0;
$error = '';
$directory = '';
if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '') {
	$productId = intval($_REQUEST['pid']);
}
if (isset($_REQUEST['path']) && $_REQUEST['path'] != '') {

	try {
		$directory = $_REQUEST['path'];
		$fullPath = '../images/products/'.$directory;
		
		// Check that the directory exists
		if(!file_exists($fullPath) || !is_dir($fullPath)) {
			$error = 'ERROR: The directory does not exist';
		}
		/*
		// Check that it's writable		
		if (is_writable($fullPath)) {
			$error = 'ERROR: The directory is not writable';
		}
		*/
	} catch (Exception $e) {
		$error = 'ERROR: '.$e->getMessage();
	}
	
	// If OK then redirect to step 2
	if ($error == '') {
		$redirect = 'insert_images_product_step2.php';
		$redirect .= '?pid='.$productId;
		$redirect .= '&path='.$directory;
		header('Location: '.$redirect);
	}
}

// Otherwise show the page 
require_once 'include/check_login.php';
require_once 'include/connection.php';

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
	<title>Ruby deMure - Add images for <?php echo $productName; ?> - Step 1</title>
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
<h2>Add image(s) for <?php echo $productName; ?> (<?php echo $productType; ?>) - Step 1</h2>

<table>
	<?php
	if (isset($error) && $error != '') {
		echo '<tr><td style="color:red;"><h2>'.$error.'</h2></td></tr>';
	}//if $error
	?>
	<tr>
		<td colspan="9" style="font-size:12px;">
			<h3>Specify the MAIN image directory within the "products" directory</h3>
			e.g. if the FULL SIZE images will go in
			<b>"/images/products/stock/valentine/disco"</b>
			<br/>then specify here 
			<b>"stock/valentine/disco"</b>
			<br/>All other sized image will sort themselves out e.g. thumbnails
			<br/><br/>N.B.  The directory must already exist
			<br/><br/>
			<form method="get">
				<input type="text" id="path" name="path" value="<?php echo $directory; ?>" />
				<input type="hidden" id="pid" name="pid" value="<?php echo $productId; ?>" />
				<input type="submit" value="Continue to step 2 >>" />
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
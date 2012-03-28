<?php
// If the user just posted, check the directory and move on to step 2
$productId = 0;
$error = '';
$directory = '';
if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '') {
	$productId = intval($_REQUEST['pid']);
}
if (isset($_REQUEST['process']) && $_REQUEST['process'] == 1) {

	try {
		$directory = $_REQUEST['path'];
		$pathFull = '../images/products/full/'.$productId;
		$pathMedium = '../images/products/medium/'.$productId;
		$pathSmall = '../images/products/small/'.$productId;
		$pathThumb = '../images/products/thumb/'.$productId;
		
		// Create the directories if they don't exist
		if (!is_dir($pathFull)) {
			if (!mkdir($pathFull)) {
				$error .= '<br/>ERROR: Unable to create FULL directory '.$pathFull;
			}
		}
		if (!is_dir($pathMedium)) {
			if (!mkdir($pathMedium)) {
				$error .= '<br/>ERROR: Unable to create MEDIUM directory '.$pathMedium;
			}
		}
		if (!is_dir($pathSmall)) {
			if (!mkdir($pathSmall)) {
				$error .= '<br/>ERROR: Unable to create SMALL directory '.$pathSmall;
			}
		}
		if (!is_dir($pathThumb)) {
			if (!mkdir($pathThumb)) {
				$error .= '<br/>ERROR: Unable to create THUMB directory '.$pathThumb;
			}
		}
		
	} catch (Exception $e) {
		$error .= '<br/>ERROR: '.$e->getMessage();
	}
	
	// If OK then redirect to step 2
	if ($error == '') {
		$redirect = 'insert_images_product_step2.php';
		$redirect .= '?pid='.$productId;
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
			<h4>The following steps will place images in these directories 
			(and create them if necessary):</h4>
			/images/products/full/<?php echo $productId;?>
			<br/>/images/products/medium/<?php echo $productId;?>
			<br/>/images/products/small/<?php echo $productId;?>
			<br/>/images/products/thumb/<?php echo $productId;?>
			<br/><br/>
			<form method="get">
				<input type="hidden" id="pid" name="pid" value="<?php echo $productId; ?>" />
				<input type="hidden" name="process" value="1" />
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
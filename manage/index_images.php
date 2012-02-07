<?php
require_once 'include/check_login.php';
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Ruby deMure - Image Management</title>
	<meta name="DESCRIPTION" content="" />
	<meta name="KEYWORDS" content="" />
	<link rel="STYLESHEET" href="css/styles.css">
</head>

<body>

<?php
echo $headerTable;

require_once 'include/connection.php';

// Get all products
$products = array();
$sql = 'SELECT
		pt.Description,
		p.ProdID,
		p.Title
	FROM Product p
	INNER JOIN ProductType pt ON p.ProductTypeID = pt.ProdTypeID
	ORDER BY pt.ProdTypeID, p.ProdID';
$stmt = $mysqli->prepare($sql);
if ($stmt) {
	$stmt->bind_result($type, $id, $name);
	$stmt->execute();
	while ($stmt->fetch()) {
		$products[] = array(
			'Type' => $type,
			'Id' => $id,
			'Name' => $name,
		);
	}//while
}//if $stmt
?>

<!-- centering table -->
<table style="width:100%;">
<tr>
<td align="center">

<table border="1">
	<tr>
		<td colspan="9" style="font-size:12px;">
			<form action="insert_images_product_step1.php" method="get">
				Add product images for 
				<select name="pid">
					<option value="0">-- Select product --</option>
					<?php
					foreach ($products as $product) {
						echo '<option value="'.$product['Id'].'">';
						echo strtoupper($product['Type']);
						echo ': '.$product['Id'];
						echo ' - '.$product['Name'];
						echo '</option>';
					}//foreach
					?>
				</select>
				<input type="submit" value="GO" />
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
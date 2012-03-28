<?php
require_once 'include/check_login.php';
require_once 'include/connection.php';
?>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Sort Orders - index</title>
		<meta name="DESCRIPTION" content="" />
		<meta name="KEYWORDS" content="" />
		<link rel="STYLESHEET" href="css/styles.css">
	</head>

	<body>
	<?php echo $headerTable; ?>

	<!-- start centering table -->
	<table style="width:100%;">
	<tr>
	<td align="center">

		<table>
			<tr><td><a href="sort_order_collections.php">Rearrange Collections</a></td></tr>
			<tr><td><a href="sort_order_bespoke.php">Rearrange Bespoke Products</a></td></tr>
			<tr><td><a href="sort_order_ready_made.php">Rearrange Ready-Made Products</a></td></tr>
			<tr><td><a href="sort_order_product_images.php">Rearrange Product Images</a></td></tr>
		</table>

	<!-- end centering table -->	
	</td>
	</tr>
	</table>

	</body>
	</html>
<?php
if ($cnRuby) {
	mysql_close($cnRuby);
}
?>
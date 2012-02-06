<?php
require_once 'include/check_login.php';

if (!$_REQUEST['pid'] && !$_POST['productid'])
	echo 'No product ID supplied.  <a href="index.php">Click here to return to index</a>';
else
{
	$productId = $_REQUEST['pid'];

	require_once 'include/connection.php';

	if ($_POST)
	{
		$productId = $_POST['productid'];
		
		// Delete from subsidiary tables first
		
		// Delete this product's colours
		$qDelete = "delete from ProductColour where ProdID = ".$productId;
		mysql_query($qDelete) or die(mysql_error());
		
		// Delete this product's materials
		$qDelete = "delete from ProductMaterial where ProdID = ".$productId;
		mysql_query($qDelete) or die(mysql_error());
		    
		// Delete this product's sizes
		$qDelete = "delete from ProductSize where ProdID = ".$productId;
		mysql_query($qDelete) or die(mysql_error());
		    
		// Delete this product's images (this won't remove them from the server)
		$qDelete = "delete from ProductImage where ProdID = ".$productId;
		mysql_query($qDelete) or die(mysql_error());
		
		
		// Then delete from the main table
		$qDelete = "delete from Product where ProdID = ".$productId;
		mysql_query($qDelete) or die(mysql_error());
	
		
		echo '<br/><br/>Product deleted.';
		echo '<br/><a href="index.php">Click here to return to the products list</a>';
		
	}
	else	// not POST
	{
		$sql	= 'select * from Product where ProdID = '.$productId;
		$set	= mysql_query($sql, $cnRuby);
		$row = mysql_fetch_assoc($set);
		$product = array(
			'Title'		=> $row['Title']
			,'Desc'			=> $row['Description']
			,'Cost'			=> $row['Cost']
			,'NumRemaining'	=> $row['NumRemaining']
			,'OtherDetails'	=> $row['OtherDetails']
		);
		?>

		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>Delete product - <?php echo $product['Title']; ?></title>
			<meta name="DESCRIPTION" content="" />
			<meta name="KEYWORDS" content="" />
			<link rel="STYLESHEET" href="css/styles.css">
		</head>

		<body>
		<?php echo $headerTable; ?>

		<!-- centering table -->
		<table style="width:100%;">
		<tr>
		<td align="center">

		<table>
			<tr>
				<td><b>Title</b></td>
				<td><?php echo $product['Title']; ?></td>
			</tr>
			<tr>
				<td><b>Description</b></td>
				<td><?php echo $product['Desc']; ?></td>
			</tr>
			<tr>
				<td><b>Cost</b></td>
				<td><?php echo $product['Cost']; ?></td>
			</tr>
			<tr>
				<td><b>Number Remaining</b></td>
				<td><?php echo $product['NumRemaining']; ?></td>
			</tr>
			<tr>
				<td><b>Other Details</b></td>
				<td><?php echo $product['OtherDetails']; ?></td>
			</tr>
		</table>
		
		<form name="frmAddProduct" action="delete_product.php" method="post">
		<table>
			<tr><td><br/><br/></td></tr>
			<tr>
				<td colspan="3" align="center">
					Are you sure you wish to delete this product?<br/>
					<input type="hidden" id="productid" name="productid" 
						value="<?php echo $productId; ?>">
					<input type="submit" value="Delete" />
				</td>
			</tr>
		</table>
		</form>

		<!-- end of centering table -->	
		</td>
		</tr>
		</table>

		</body>
		</html>
	<?php
	}//if ($_POST)

	if ($cnRuby)
		mysql_close($cnRuby);
}//if $_REQUEST['pid']
?>

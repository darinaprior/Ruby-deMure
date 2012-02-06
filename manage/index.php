<?
require_once 'include/check_login.php';
?>

<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Ruby deMure - Product Management</title>
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

<?php
// Get querystring values
if ($_REQUEST['showall'] == '1')
	$showAll = true;
else
	$showAll = false;
	
$sort = '';
if (isset($_REQUEST['sort']))
	$sort = $_REQUEST['sort'];
?>

<table border="1">
	<tr>
		<td colspan="10" style="font-size:12px;">
			<br/><a href="insert_product.php">Add new product &raquo;</a>
			
			<?php
			// Display filtering links
			if ($showAll)
				$link = '<a href="index.php">Show only current products &raquo;</a>';
			else
				$link = '<a href="'.$_SERVER['PHP_SELF'].'?showall=1">Show all products (current and not) &raquo;</a>';
			?>
			<br/><br/><?php echo $link ?>
		</td>
	</tr>
	
	<?php // Display column headings with links for sorting ?>
	<tr>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=<?php echo $showAll; ?>&sort=id">
				ID
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=<?php echo $showAll; ?>&sort=ca">
				Category
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=<?php echo $showAll; ?>&sort=co">
				Collection
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=<?php echo $showAll; ?>&sort=ty">
				Product Type
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=<?php echo $showAll; ?>&sort=ti">
				Title
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=<?php echo $showAll; ?>&sort=re">
				Num Remaining
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=<?php echo $showAll; ?>&sort=pr">
				Priority
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=<?php echo $showAll; ?>&sort=im">
				Default Image
			</a>
		</td>
		<td colspan="2">&nbsp;</a></td>
	</tr>
	<?php
	
	// Get product details from main [Product] table
	$productId		= '';
	$category		= '';
	$collection		= '';
	$prodType		= '';
	$title			= '';
	$numRemaining	= 0;
	$priority		= 0;
	$defaultImage	= '';
		
	require_once 'include/connection.php';
	
	// Filtering
	if ($showAll)
		$where = '';
	else
		$where = 'where p.Priority is not null';
		
	$qProduct = "select
			p.ProdID, ca.Description as CategoryName, co.Title as CollectionName, 
			pt.Description as ProdTypeName, p.Title, p.NumRemaining, p.Priority, 
			pi.Filepath
		from Product p
		left outer join Category ca on p.CategoryID = ca.ProdCatID
		left outer join Collection co on p.CollectionID = co.CollectionID
		left outer join ProductType pt on p.ProductTypeID = pt.ProdTypeID
		left outer join ProductImage pi on p.DefaultImageID = pi.ProdImageID
		$where
		order by p.Priority desc, p.Title";
	$rsProduct	= mysql_query($qProduct, $cnRuby);
	
	// Loop through results and store in an array
	$products = array();
	while ($product = mysql_fetch_array($rsProduct))
	{
		$products[] = array(
			'ID'			=> $product['ProdID'],
			'Category'		=> $product['CategoryName'],
			'Collection'	=> $product['CollectionName'],
			'ProductType'	=> $product['ProdTypeName'],
			'Title'			=> $product['Title'],
			'NumRemaining'	=> $product['NumRemaining'],
			'Priority'		=> $product['Priority'],
			'DefaultImage'	=> $product['Filepath']
		);
	}//while
	
	// We can use array_multisort to sort the multi-dimensional array,
	// but have to store data by column first
	$idCols			= array();
	$catCols		= array();
	$collCols		= array();
	$typeCols		= array();
	$titleCols		= array();
	$remainCols		= array();
	$priorityCols	= array();
	$imageCols		= array();
	foreach ($products as $key => $row)
	{
		$idCols[$key]		= $row['ID'];
		$catCols[$key]		= $row['Category'];
		$collCols[$key]		= $row['Collection'];
		$typeCols[$key]		= $row['ProductType'];
		$titleCols[$key]	= $row['Title'];
		$remainCols[$key]	= $row['NumRemaining'];
		$priorityCols[$key]	= $row['Priority'];
		$imageCols[$key]	= $row['DefaultImage'];
	}//foreach

	// Sort the data by user's choice (if any) and then ID descending
	// Use $products as last parameter to store back to that array
	switch($sort)
	{
		case 'ca':
			array_multisort($catCols, $idCols, SORT_DESC, $products);
			break;
		case 'co':
			array_multisort($collCols, $idCols, SORT_DESC, $products);
			break;
		case 'ty':
			array_multisort($typeCols, $idCols, SORT_DESC, $products);
			break;
		case 'ti':
			array_multisort($titleCols, $idCols, SORT_DESC, $products);
			break;
		case 're':
			array_multisort($remainCols, $idCols, SORT_DESC, $products);
			break;
		case 'pr':
			array_multisort($priorityCols, $idCols, SORT_DESC, $products);
			break;
		case 'im':
			array_multisort($imageCols, $idCols, SORT_DESC, $products);
			break;
		default:
			array_multisort($idCols, SORT_DESC, $products);
			break;
	}
	
	// Display the results
	foreach ($products as $product)
	{	
		// The default image
		if ( ($product['DefaultImage'] == null)
			|| ($product['DefaultImage'] == '')
			|| (is_numeric( stripos($product['DefaultImage'],"img_not_avail") ))
		)
		{
			$defaultImage	= '<font color="red">NOT SET</font>';
		}
		else
		{
			$parts = explode('/', $product['DefaultImage']);
			$defaultImage	= $parts[COUNT($parts)-1];
		}
		?>
		<tr>
			<td><?php echo $product['ID']?></td>
			<td><?php echo $product['Category']?></td>
			<td><?php echo $product['Collection'] ?></td>
			<td><?php echo $product['ProductType']?></td>
			<td><?php echo $product['Title'] ?></td>
			<td><?php echo $product['NumRemaining']?></td>
			<td><?php echo $product['Priority'] ?></td>
			<td><?php echo $defaultImage?></td>
			<td><a href="edit_product.php?pid=<?php echo $product['ID'] ?>">Edit &raquo;</a></td>
			<td><a href="delete_product.php?pid=<?php echo $product['ID'] ?>">Delete &raquo;</a></td>
		</tr>
		<?php

	}//while
	?>
		
</table>

<!-- end of centering table -->	
</td>
</tr>
</table>

</body>
</html>

<?php
if ($cnRuby)
	mysql_close($cnRuby);
?>

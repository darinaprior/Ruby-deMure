<?
require_once 'include/check_login.php';
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Ruby deMure - Collection Management</title>
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
$sort = '';
if (isset($_REQUEST['sort']))
	$sort = $_REQUEST['sort'];
?>

<table border="1">
	<tr>
		<td colspan="9" style="font-size:12px;">
			<br/><a href="insert_collection.php">Add new collection &raquo;</a>
		</td>
	</tr>
	
	<?php // Display column headings with links for sorting ?>
	<tr>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=id">
				ID
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=ti">
				Title
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=de">
				Description
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=da">
				Date
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=pr">
				Priority
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=cu">
				Current?
			</a>
		</td>
		<td class="tdHeading" style="white-space:nowrap;">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=nu">
				# Products
			</a>
		</td>
		<td colspan="2">&nbsp;</a></td>
	</tr>
	<?php
	
	// Get collection details from main [Collection] table
	require_once 'include/connection.php';
	
	$sql = 'select
			c.*
			,(select COUNT(*) from Product where CollectionID = 
				c.CollectionID) as NumProds
		from Collection c';
	$set = mysql_query($sql, $cnRuby);
	
	// Loop through results and store in an array
	$collections = array();
	while ($row = mysql_fetch_array($set))
	{
		$collections[] = array(
			'Id'			=> $row['CollectionID'],
			'Title'			=> $row['Title'],
			'Description'	=> $row['Description'],
			'Date'			=> $row['Date'],
			'Priority'		=> $row['Priority'],
			'Current'		=> $row['Current'],
			'NumProds'		=> $row['NumProds']
		);
	}//while
	
	// We can use array_multisort to sort the multi-dimensional array,
	// but have to store data by column first
	$idCols			= array();
	$titleCols		= array();
	$descCols		= array();
	$dateCols		= array();
	$priorityCols	= array();
	$currentCols	= array();
	$numProdCols	= array();
	foreach ($collections as $key => $row)
	{
		$idCols[$key]		= $row['Id'];
		$titleCols[$key]	= $row['Title'];
		$descCols[$key]		= $row['Description'];
		$dateCols[$key]		= $row['Date'];
		$priorityCols[$key]	= $row['Priority'];
		$currentCols[$key]	= $row['Current'];
		$numProdCols[$key]	= $row['NumProds'];
	}//foreach

	// Sort the data by user's choice (if any) and then ID descending
	// Use $collections as last parameter to store back to that array
	switch($sort)
	{
		case 'ti':
			array_multisort($titleCols, $idCols, SORT_DESC, $collections);
			break;
		case 'de':
			array_multisort($descCols, $idCols, SORT_DESC, $collections);
			break;
		case 'da':
			array_multisort($dateCols, $idCols, SORT_DESC, $collections);
			break;
		case 'pr':
			array_multisort($priorityCols, $idCols, SORT_DESC, $collections);
			break;
		case 'cu':
			array_multisort($currentCols, $idCols, SORT_DESC, $collections);
			break;
		case 'nu':
			array_multisort($numProdCols, $idCols, SORT_DESC, $collections);
			break;
		default:
			array_multisort($idCols, SORT_DESC, $collections);
			break;
	}
	
	// Display the results
	foreach ($collections as $collection)
	{
		?>
		<tr>
			<td><?php echo $collection['Id']?></td>
			<td><?php echo $collection['Title'] ?></td>
			<td><?php echo $collection['Description']?></td>
			<td><?php echo $collection['Date']?></td>
			<td><?php echo $collection['Priority'] ?></td>
			<td><?php echo $collection['Current'] ?></td>
			<td><?php echo $collection['NumProds'] ?></td>
			<td style="white-space:nowrap;">
				<a href="edit_collection.php?cid=<?php echo $collection['Id'] ?>">
					Edit &raquo;
				</a>
			</td>
			<td style="white-space:nowrap;">
				<a href="delete_collection.php?cid=<?php echo $collection['Id'] ?>">
					Delete &raquo;
				</a>
			</td>
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

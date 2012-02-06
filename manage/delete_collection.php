<?php
require_once 'include/check_login.php';

if (!$_REQUEST['cid'] && !$_POST['collectionid'])
	echo 'No collection ID supplied.  <a href="index_collection.php">Click here to return to index</a>';
else
{
	$collectionId = $_REQUEST['cid'];

	require_once 'include/connection.php';

	if ($_POST)
	{
		$collectionId = $_POST['collectionid'];
		
		// Delete from the main table
		$qDelete = "delete from Collection where CollectionID = ".$collectionId;
		mysql_query($qDelete) or die(mysql_error());
	
		echo '<br/><br/>Collection deleted.';
		echo '<br/><a href="index_collection.php">Click here to return to the collections list</a>';
		
	}
	else	// not POST
	{
		$sql	= 'select * from Collection where CollectionID = '.$collectionId;
		$set	= mysql_query($sql, $cnRuby);
		$row = mysql_fetch_assoc($set);
		$collection = array(
			'Title'	=> $row['Title']
			,'Desc'		=> $row['Description']
			,'Date'		=> $row['Date']
			,'Priority'	=> $row['Priority']
			,'Current'	=> $row['Current']
		);
		?>

		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>Delete collection - <?php echo $collection['Title']; ?></title>
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
				<td><?php echo $collection['Title']; ?></td>
			</tr>
			<tr>
				<td><b>Description</b></td>
				<td><?php echo $collection['Desc']; ?></td>
			</tr>
			<tr>
				<td><b>Date</b></td>
				<td><?php echo $collection['Date']; ?></td>
			</tr>
			<tr>
				<td><b>Priority</b></td>
				<td><?php echo $collection['Priority']; ?></td>
			</tr>
			<tr>
				<td><b>Current</b></td>
				<td><?php echo $collection['Current']; ?></td>
			</tr>
		</table>
		
		<form name="frm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table>
			<tr><td><br/><br/></td></tr>
			<tr>
				<td colspan="3" align="center">
					Are you sure you wish to delete this collection?<br/>
					<input type="hidden" id="collectionid" name="collectionid" 
						value="<?php echo $collectionId; ?>">
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
}//if $_REQUEST['cid']
?>

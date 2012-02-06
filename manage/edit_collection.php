<?php
require_once 'include/check_login.php';

if (!$_REQUEST['cid'] && !$_POST['collectionid'])
	echo 'No collection ID supplied.  <a href="index_collection.php">Click here to return to collection index</a>';
else
{
	$collectionId = $_REQUEST['cid'];

	require_once 'include/connection.php';

	if ($_POST)
	{
		$collectionId = $_POST['collectionid'];
		
		// Get the form data
		$title			= $_POST['title'];
		$description	= $_POST['description'];
		$date			= $_POST['date'];
		$priority		= $_POST['priority'];
		$current		= $_POST['current'];
	
		// Update the collection in the database
		$qUpdate = 'update Collection
			set `Title` = "'.$title.'",
			`Description` = "'.$description.'",
			`Date` = "'.$date.'",
			`Priority` = '.$priority.',
			`Current` = '.$current.'
		where CollectionID = '.$collectionId;
		mysql_query($qUpdate) or die(mysql_error());
	
		echo '<br/><br/>Collection updated.';
		echo '<br/><a href="index_collection.php">Click here to return to the collections list</a>';
		echo '<br/><a href="index.php">Click here to return to the products list</a>';
		
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
			<title>Edit collection - <?php echo $collection['Title']; ?></title>
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
		// Priorities
		$priorities	= array();
		$sql	= "select Priority from Collection order by Priority";
		$set	= mysql_query($sql, $cnRuby);
		while ($row = mysql_fetch_assoc($set))
		{
			$priorities[] = $row['Priority'];
		}//while
		
		// Strip out duplicates
		$priorities = array_unique($priorities);
		?>

		<form name="frm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table>
			<tr>
				<td><b>Title</b></td>
				<td>
					<input type="text" name="title" id="title" 
						value="<?php echo $collection['Title']; ?>" />
				</td>
			</tr>
			<tr>
				<td><b>Description</b></td>
				<td colspan="2">
					<textarea name="description" id="description" cols="80"><?php echo $collection['Desc']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Date (yyyy-mm-dd)</b></td>
				<td><input type="text" name="date" id="date" 
					value="<?php echo $collection['Date']; ?>" /></td>
			</tr>
			<tr>
				<td><b>Priority</b></td>
				<td>
					<select name="priority" id="priority">
						<?php
						$highestPriority = 0;
						foreach ($priorities as $prior)
						{
							echo '<option value="'.$prior.'"';
							if ($prior == $collection['Priority'])
								echo ' selected';
							echo '>'.$prior;
							if ($prior == '')
								echo '** NULL - PLEASE SELECT A PRIORITY **';
							else if ($prior == 0)
								echo ' (lowest priority)';
							echo '</option>';
							$highestPriority = intval($prior);
						}//foreach
						$highestPriority++;
						?>
						<option value="<?php echo $highestPriority; ?>">
							<?php echo $highestPriority; ?> (NEW - highest priority)
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><b>Current</b></td>
				<td>
					<select name="current" id="current">
						<option value="null" 
							<?php
							if ($collection['Current'] == '')
								echo " selected";
							?>
						>
							NULL (discontinued - don't display on website)
						</option>
						<option value="0" 
							<?php
							if ($collection['Current'] == 0)
								echo " selected";
							?>
						>
							0 (old - show in bottom section on website)
						</option>
						<option value="1" 
							<?php
							if ($collection['Current'] == 1)
								echo " selected";
							?>
						>
							1 (current - show in top section on website)
						</option>
					</select>
				</td>
			</tr>
			<tr><td><br/></td></tr>
			<tr>
				<td colspan="3" align="center">
					<input type="hidden" id="collectionid" name="collectionid" 
						value="<?php echo $collectionId; ?>">
					<input type="submit" value="Update" />
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

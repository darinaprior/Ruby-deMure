<?php
require_once 'include/check_login.php';
require_once 'include/connection.php';

if ($_POST)
{
	// Get the form data
	$title			= $_POST['title'];
	$description	= $_POST['description'];
	$date			= $_POST['date'];
	$priority		= $_POST['priority'];
	$current		= $_POST['current'];
	
	// Insert the new collection into the database
	$qInsert = "insert into Collection (Title, Description, Date, Priority, Current) ";
	$qInsert .= ' values (';
	$qInsert .= "'$title', '$description', '$date', $priority, $current);";
	mysql_query($qInsert) or die(mysql_error());
	$collectionId = mysql_insert_id();
	
	echo '<br/><br/>New collection inserted (ID '.$collectionId.').';
	echo '<br/><a href="insert_collection.php">Click here to add another</a>';
	echo '<br/><a href="index_collection.php">Click here to return to the collections list</a>';
}
else	// not POST
{
	?>

	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Insert collection</title>
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
				<input type="text" name="title" id="title" value="" />
			</td>
		</tr>
		<tr>
			<td><b>Description</b></td>
			<td>
				<textarea name="description" id="description" cols="80"></textarea>
			</td>
		</tr>
		<tr>
			<td><b>Date (yyyy-mm-dd)</b></td>
			<td><input type="text" name="date" id="date" value="" /></td>
		</tr>
		<tr>
			<td><b>Priority</b></td>
			<td>
				<select name="priority" id="priority">
					<?php
					$highestPriority = 0;
					foreach ($priorities as $prior)
					{
						if ($prior != '')	// don't let the user choose NULL if it's there!
						{
							echo '<option value="'.$prior.'">'.$prior;
							if ($prior == 0)
								echo ' (lowest priority)';
							echo '</option>';
							$highestPriority = intval($prior);
						}//if not null
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
					<option value="null">
						NULL (discontinued - don't display on website)
					</option>
					<option value="0">
						0 (old - show in bottom section on website)
					</option>
					<option value="1">
						1 (current - show in top section on website)
					</option>
				</select>
			</td>
		</tr>
		<tr><td><br/></td></tr>
		<tr>
			<td align="center"><input type="submit" value="Insert" /></td>
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
?>

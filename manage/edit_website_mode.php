<?php
require_once 'include/check_login.php';

require_once 'include/connection.php';

if ($_GET)
{
	// Get the form data
	$siteid		= $_GET['siteid'];
	$mode		= $_GET['mode'];

	// Update the website mode in the database
	$qUpdate = 'update website set mode = '.$mode.' where id = '.$siteid.';';
	mysql_query($qUpdate) or die(mysql_error());

	echo '<br/><br/>Website updated.';
	echo '<br/><a href="index.php">Click here to return to the products list</a>';
	
}
else	// not POST
{
	$websites = array();
	$sql	= 'select * from website';
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$websites[] = array(
			'Id'		=> $row['id']
			,'Address'	=> $row['address']
			,'Mode'		=> $row['mode']
		);
	}//while
	?>

	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Edit website mode - <?php echo $websites['Title']; ?></title>
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
	foreach ($websites as $site) {
		?>
		<form name="frmEditMode_<?php echo $site['Id']; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<table>
			<tr>
				<td><?php echo $site['Address']; ?></td>
				<td>
					<select name="mode" id="mode">
						<option value="0" <?php if ($site['Mode'] == 0) { echo ' selected'; } ?> >Suspended</option>
						<option value="1" <?php if ($site['Mode'] == 1) { echo ' selected'; } ?> >Live</option>
						<option value="2" <?php if ($site['Mode'] == 2) { echo ' selected'; } ?> >Maintenance</option>
					</select>
				</td>
				<td>
					<input type="hidden" id="siteid" name="siteid" value="<?php echo $site['Id']; ?>">
					<input type="submit" value="Update" />
				</td>
			</tr>
		</table>
		</form>
		<?php
	}//foreach $websites
	?>

	<!-- end of centering table -->	
	</td>
	</tr>
	</table>

	</body>
	</html>
<?php
}//if ($_GET)

if ($cnRuby)
	mysql_close($cnRuby);
?>

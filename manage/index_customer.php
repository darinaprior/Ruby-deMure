<?
require_once 'include/check_login.php';
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Ruby deMure - Customer Management</title>
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
			<br/><a href="insert_customer.php">Add new customer &raquo;</a>
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
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=na">
				Name
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=ad">
				Address
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=em">
				Email
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=hi">
				Hide Name?
			</a>
		</td>
		<td class="tdHeading">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort=di">
				Discount
			</a>
		</td>
		<td colspan="3">&nbsp;</a></td>
	</tr>
	<?php
	
	// Get customer details from main [Customer] table
	require_once 'include/connection.php';
	
	$sql = 'select * from Customer';
	$set = mysql_query($sql, $cnRuby);
	
	// Loop through results and store in an array
	$customers = array();
	while ($row = mysql_fetch_array($set))
	{
		$customers[] = array(
			'Id'		=> $row['CustID'],
			'Name'		=> $row['FirstName'].' '.$row['LastName'],
			'Address'	=> $row['Address'],
			'Email'		=> $row['Email'],
			'Href'		=> $row['WebsiteLink'],
			'Privacy'	=> $row['Privacy'],
			'Discount'	=> $row['Discount']
		);
	}//while
	
	// We can use array_multisort to sort the multi-dimensional array,
	// but have to store data by column first
	$idCols			= array();
	$nameCols		= array();
	$addressCols	= array();
	$emailCols		= array();
	$privacyCols	= array();
	$discountCols	= array();
	foreach ($customers as $key => $row)
	{
		$idCols[$key]		= $row['Id'];
		$nameCols[$key]		= $row['Name'];
		$addressCols[$key]	= $row['Address'];
		$emailCols[$key]	= $row['Email'];
		$privacyCols[$key]	= $row['Privacy'];
		$discountCols[$key]	= $row['Discount'];
	}//foreach

	// Sort the data by user's choice (if any) and then ID descending
	// Use $customers as last parameter to store back to that array
	switch($sort)
	{
		case 'na':
			array_multisort($nameCols, $idCols, SORT_DESC, $customers);
			break;
		case 'ad':
			array_multisort($addressCols, $idCols, SORT_DESC, $customers);
			break;
		case 'em':
			array_multisort($emailCols, $idCols, SORT_DESC, $customers);
			break;
		case 'hi':
			array_multisort($privacyCols, $idCols, SORT_DESC, $customers);
			break;
		case 'di':
			array_multisort($discountCols, $idCols, SORT_DESC, $customers);
			break;
		default:
			array_multisort($idCols, SORT_DESC, $customers);
			break;
	}
	
	// Display the results
	foreach ($customers as $customer)
	{
		?>
		<tr>
			<td><?php echo $customer['Id']?></td>
			<td><?php echo $customer['Name'] ?></td>
			<td>
				<?php
				$addressLines = explode(',', $customer['Address']);
				foreach ($addressLines as $line)
				{
					echo $line.'<br/>';
				}
				?>
			</td>
			<td><?php echo $customer['Email']?></td>
			<td>
				<?php
				if ($customer['Privacy'] == 0)
					echo 'No';
				else
					echo 'Yes';
				?>
			</td>
			<td><?php echo floatval($customer['Discount']) ?>%</td>
			<td style="white-space:nowrap;">
				<?php
				if ($customer['Href'] != '')
				{
					echo '<a target="_blank" href="'.$customer['Href'].'">';
					echo 'View Site &raquo;';
					echo '</a>';
				}
				else
				{
					echo '&nbsp;';
				}
				?>
			</td>
			<td style="white-space:nowrap;">
				<a href="edit_customer.php?cid=<?php echo $customer['Id'] ?>">
					Edit &raquo;
				</a>
			</td>
			<td style="white-space:nowrap;">
				<a href="delete_customer.php?cid=<?php echo $customer['Id'] ?>">
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

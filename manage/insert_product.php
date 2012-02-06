<?php
require_once 'include/connection.php';
require_once 'include/check_login.php';

if ($_POST)
{
	// Product Type ID (insert new if necessary)
	if ($_POST['selProductType'] == 'NEW')
	{
		$qInsert = 'insert into ProductType (Description, NavName)';
		$qInsert .= 'values ("'.$_POST['txtProductType'].'", null)';
		mysql_query($qInsert) or die(mysql_error());
		$productTypeId = mysql_insert_id();
	}
	else
	{
		$productTypeId = $_POST['selProductType'];
	}
		
	// Shape ID (insert new if necessary)
	if ($_POST['selShape'] == 'NEW')
	{
		$qInsert = 'insert into Shape (Description)';
		$qInsert .= 'values ("'.$_POST['txtShape'].'")';
		mysql_query($qInsert) or die(mysql_error());
		$shapeId = mysql_insert_id();
	}
	else
	{
		$shapeId = $_POST['selShape'];
	}
		
	// Category ID (insert new if necessary)
	if ($_POST['selCategory'] == 'NEW')
	{
		$qInsert = 'insert into ProductType (Description)';
		$qInsert .= 'values ("'.$_POST['txtCategory'].'")';
		mysql_query($qInsert) or die(mysql_error());
		$categoryId = mysql_insert_id();
	}
	else
	{
		$categoryId = $_POST['selCategory'];
	}
	
	// Collection ID (insert new if necessary)
	if ($_POST['selCollection'] == 'NEW')
	{
		$qInsert = 'insert into Collection (Title, Description, Date, Priority, Current)';
		$qInsert .= 'values ("'.$_POST['txtCollection'].'", null, "'.date('Y-m-d').'", null, 1)';
		mysql_query($qInsert) or die(mysql_error());
		$collectionId = mysql_insert_id();
	}
	else
	{
		$collectionId = $_POST['selCollection'];
	}
	
	// Customer ID (insert new if necessary)
	if (isset($_POST['hideName']))
		$hideName	= 1;
	else
		$hideName	= 0;
	if ($_POST['selCustomer'] == 'NEW')
	{
		$qInsert = 'insert into Customer (FirstName, LastName, Privacy)';
		$qInsert .= 'values ("'.$_POST['txtCustFirstName'].'", "'.
			$_POST['txtCustLastName'].'", '.$hideName.')';
		mysql_query($qInsert) or die(mysql_error());
		$customerId = mysql_insert_id();
	}
	else
	{
		$customerId = $_POST['selCustomer'];
	}
	
	// Get the rest of the form data
	$title			= $_POST['title'];
	$description	= $_POST['description'];
	$cost			= $_POST['cost'];
	if (isset($_POST['tassel']))
		$hasTassel	= 1;
	else
		$hasTassel	= 0;
	$other			= $_POST['other'];
	$remaining		= $_POST['remaining'];
	
	// Insert the new product into the database
	$qInsert = "insert into Product (ProductTypeID, Title, Description, 
		DateCreated, Cost, VAT, TotalCost, ShapeID, CategoryID, CollectionID, 
		HasTassel, DefaultImageID, OtherDetails, CustID, Priority, NumRemaining)";
	$qInsert .= ' values (';
	$qInsert .= "$productTypeId, '$title', '$description', '".date('Y-m-d')."', ";
	$qInsert .= "$cost, 0, $cost, $shapeId, $categoryId, $collectionId, ";
	$qInsert .= "$hasTassel, null, '$other', $customerId, 0, $remaining";
	$qInsert .= ");";
	
	mysql_query($qInsert) or die(mysql_error());
	$productId = mysql_insert_id();
	
	
	// Add colour(s) for the new product
    $selectedColours = $_POST['colour'];
	if (COUNT($selectedColours) > 0)
    {	foreach ($selectedColours as $key => $value)
		{
			$colourPriority = $_POST['colour_priority_'.$value];
			$qInsert = "insert into ProductColour (ProdID, ColourID, `Order`)";
			$qInsert .= "values ($productId, $value, $colourPriority);";
			mysql_query($qInsert) or die(mysql_error());
		}
	}
    
	// Add material(s) for the new product
    $selectedMaterials = $_POST['material'];
	if (COUNT($selectedMaterials) > 0)
    {	foreach ($selectedMaterials as $key => $value)
		{
			$materialPriority = $_POST['material_priority_'.$value];
			$qInsert = "insert into ProductMaterial (ProdID, MaterialID, `Order`)";
			$qInsert .= "values ($productId, $value, $materialPriority);";
			mysql_query($qInsert) or die(mysql_error());
		}
	}
        
	// Add size(s) for the new product
    $selectedSizes = $_POST['size'];
    if (COUNT($selectedSizes) > 0)
    {
    	foreach ($selectedSizes as $key => $value)
		{
			$qInsert = "insert into ProductSize (ProdID, SizeID)";
			$qInsert .= "values ($productId, $value);";
			mysql_query($qInsert) or die(mysql_error());
		}
    } 

	echo '<br/><br/>New product inserted.';
	echo '<br/><a href="insert_product.php">Click here to add another</a>';
	echo '<br/><a href="index.php">Click here to return to the products list</a>';
}
else	// not POST
{
	?>
	<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->

	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Insert product</title>
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
	// Product types
	$productTypes	= array();
	$sql	= "select ProdTypeID, Description from ProductType order by Description";
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$productTypes[] = array(
			'Id'	=> $row['ProdTypeID']
			,'Desc'	=> $row['Description']
		);
	}//while
	
	// Shapes
	$shapes	= array();
	$sql	= "select ShapeID, Description from Shape order by Description";
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$shapes[] = array(
			'Id'	=> $row['ShapeID']
			,'Desc'	=> $row['Description']
		);
	}//while
	
	// Customers
	$customers	= array();
	$sql	= "select CustID, FirstName, LastName from Customer order by FirstName";
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$customers[] = array(
			'Id'			=> $row['CustID']
			,'FirstName'	=> $row['FirstName']
			,'LastName'		=> $row['LastName']
		);
	}//while
	
	// Categories
	$categories	= array();
	$sql	= "select ProdCatID, Description from Category order by Description";
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$categories[] = array(
			'Id'	=> $row['ProdCatID']
			,'Desc'	=> $row['Description']
		);
	}//while
	
	// Collections
	$collections	= array();
	$sql	= "select CollectionID, Title, Description from Collection where Current is not null order by Title";
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$collections[] = array(
			'Id'	=> $row['CollectionID']
			,'Title'=> $row['Title']
			,'Desc'	=> $row['Description']
		);
	}//while
	
	// Colours
	$colours	= array();
	$sql	= "select ColourID, Description from Colour order by Description";
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$colours[] = array(
			'Id'	=> $row['ColourID']
			,'Desc'	=> $row['Description']
		);
	}//while
	
	// Materials
	$materials	= array();
	$sql	= "select MaterialID, Name from Material order by Name";
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$materials[] = array(
			'Id'	=> $row['MaterialID']
			,'Name'	=> $row['Name']
		);
	}//while
	
	// Sizes
	$sizes	= array();
	$sql	= "select SizeID, Name from Size order by Size.Order";
	$set	= mysql_query($sql, $cnRuby);
	while ($row = mysql_fetch_assoc($set))
	{
		$sizes[] = array(
			'Id'	=> $row['SizeID']
			,'Name'	=> $row['Name']
		);
	}//while
	?>

	<form name="frmAddProduct" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<table>
		<tr>
			<td><b>Product Type</b></td>
			<td>
				<select name="selProductType" id="selProductType">
					<?php
					foreach ($productTypes as $type)
					{
						echo '<option value="'.$type['Id'].'">'.$type['Desc'].'</option>';
					}//foreach
					?>
					<option value="NEW">New type - type on right &raquo;</option>
				</select>
			</td>
			<td>
				<input type="text" name="txtProductType" id="txtProductType" value="" />
			</td>
		</tr>
		<tr>
			<td><b>Category</b></td>
			<td>
				<select name="selCategory" id="selCategory">
					<?php
					foreach ($categories as $cat)
					{
						echo '<option value="'.$cat['Id'].'">'.$cat['Desc'].'</option>';
					}//foreach
					?>
					<option value="NEW">New category - type on right &raquo;</option>
				</select>
			</td>
			<td>
				<input type="text" name="txtCategory" id="txtCategory" value="" />
			</td>
		</tr>
		<tr>
			<td><b>Collection</b></td>
			<td>
				<select name="selCollection" id="selCollection">
					<option value="null">None</option>
					<?php
					foreach ($collections as $collection)
					{
						echo '<option value="'.$collection['Id'].'">'.$collection['Title'].'</option>';
					}//foreach
					?>
					<option value="NEW">New collection - type on right &raquo;</option>
				</select>
			</td>
			<td>
				<input type="text" name="txtCollection" id="txtCollection" value="" />
			</td>
		</tr>
		<tr>
			<td><b>Title</b></td>
			<td><input type="text" name="title" id="title" value="" /></td>
		</tr>
		<tr>
			<td><b>Description</b></td>
			<td colspan="2">
				<textarea name="description" id="description" cols="80"></textarea>
			</td>
		</tr>
		<tr>
			<td><b>Cost</b></td>
			<td><input type="text" name="cost" id="cost" value="" /></td>
		</tr>
		<tr>
			<td><b>Shape</b></td>
			<td>
				<select name="selShape" id="selShape">
					<?php
					foreach ($shapes as $shape)
					{
						echo '<option value="'.$shape['Id'].'">'.$shape['Desc'].'</option>';
					}//foreach
					?>
					<option value="NEW">New shape - type on right &raquo;</option>
				</select>
			</td>
			<td>
				<input type="text" name="txtShape" id="txtShape" value="" />
			</td>
		</tr>
		<tr>
			<td><b>Has Tassel</b></td>
			<td><input type="checkbox" name="tassel" id="tassel" value="" /></td>
		</tr>
		<tr>
			<td><b>Customer</b></td>
			<td>
				<select name="selCustomer" id="selCustomer">
					<option value="null">None</option>
					<?php
					foreach ($customers as $customer)
					{
						echo '<option value="'.$customer['Id'].'">'.
							$customer['FirstName'].' '.$customer['LastName'].
							'</option>';
					}//foreach
					?>
					<option value="NEW">New customer - type on right &raquo;</option>
				</select>
			</td>
			<td>
				<input type="text" name="txtCustFirstName" id="txtCustFirstName" value="" size="10" />
				<input type="text" name="txtCustLastName" id="txtCustLastName" value="" size="10" />
				<input type="checkbox" name="hideName" id="hideName" value="" /> Hide Name
			</td>
		</tr>
		<tr>
			<td><b>Number Remaining</b></td>
			<td><input type="text" name="remaining" id="remaining" value="" /></td>
		</tr>
		<tr>
			<td><b>Other Details</b></td>
			<td colspan="2">
				<textarea name="other" id="other" cols="80"></textarea>
			</td>
		</tr>
		<tr><td><br/></td></tr>
		<tr>
			<td colspan="3">
				<table>
					<tr>
						<td valign="top">
							<table>
								<tr><td class="tdHeading"><b>Colours</b></td></tr>
								<tr>
									<td>
										<table cellpadding="0" style="font-size:10px;">
											<tr><td>Colours</td><td>Priority (0=highest)</td></tr>
											<?php
											foreach ($colours as $colour)
											{
												?>
												<tr>
													<td>
														<input type="checkbox"
															name="colour[]"
															value="<?php echo $colour['Id']; ?>">
														&nbsp;<?php echo $colour['Desc']; ?>
													</td>
													<td>
														<select style="font-size:10px;"
															name="colour_priority_<?php echo $colour['Id']; ?>"
															id="colour_priority_<?php echo $colour['Id']; ?>">
															<?php
															for ($i = 0; $i <= 10; $i++)
															{
																echo '<option value="'.$i.'">'.$i.'</option>';
															}//for
															?>
														</select>
													</td>
												</tr>
												<?php
											}//foreach
											?>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td width="50">&nbsp;</td>
						<td valign="top">
							<table>
								<tr><td class="tdHeading"><b>Materials</b></td></tr>
								<tr>
									<td>
										<table cellpadding="0" style="font-size:10px;">
											<tr><td>Materials</td><td>Priority (0=highest)</td></tr>
											<?php
											foreach ($materials as $material)
											{
												?>
												<tr>
													<td>
														<input type="checkbox"
															name="material[]"
															value="<?php echo $material['Id']; ?>">
														&nbsp;<?php echo $material['Name']; ?>
													</td>
													<td>
														<select style="font-size:10px;"
															name="material_priority_<?php echo $material['Id']; ?>"
															id="material_priority_<?php echo $material['Id']; ?>">
															<?php
															for ($i = 0; $i <= 10; $i++)
															{
																echo '<option value="'.$i.'">'.$i.'</option>';
															}//for
															?>
														</select>
													</td>
												</tr>
												<?php
											}//foreach
											?>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td width="50">&nbsp;</td>
						<td valign="top">
							<table>
								<tr><td class="tdHeading"><b>Sizes</b></td></tr>
								<tr>
									<td>
										<?php
										foreach ($sizes as $size)
										{
											echo '<input type="checkbox" 
												name="size[]" 
												value="'.$size['Id'].'"> '.
												$size['Name'].
												'<br/>';
										}//foreach
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td><br/><br/></td></tr>
					<tr>
						<td colspan="3" align="center"><input type="submit" value="Insert" /></td>
					</tr>
				</table>
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
?>

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
		$priority		= $_POST['priority'];
	
		// Update the product in the database
		$qUpdate = 'update Product
			set ProductTypeID = '.$productTypeId.',
			Title = "'.$title.'",
			Description = "'.$description.'",
			Cost = '.$cost.',
			TotalCost = '.$cost.',
			ShapeID = '.$shapeId.',
			CategoryID = '.$categoryId.',
			CollectionID = '.$collectionId.',
			ProductTypeID = '.$productTypeId.',
			HasTassel = '.$hasTassel.',
			OtherDetails = "'.$other.'",
			CustID = '.$customerId.',
			Priority = '.$priority.',
			NumRemaining = '.$remaining.'
		where ProdID = '.$productId;
		mysql_query($qUpdate) or die(mysql_error());
	
		// Delete old selected colours
		$qDelete = "delete from ProductColour where ProdID = ".$productId;
		mysql_query($qDelete) or die(mysql_error());
		
		// Add new selected colours
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
		
		// Delete old selected materials
		$qDelete = "delete from ProductMaterial where ProdID = ".$productId;
		mysql_query($qDelete) or die(mysql_error());
		
		// Add new selected colours
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
		    
		// Delete old selected colours
		$qDelete = "delete from ProductSize where ProdID = ".$productId;
		mysql_query($qDelete) or die(mysql_error());
		
		// Add new selected colours
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

		echo '<br/><br/>Product update.';
		echo '<br/><a href="index.php">Click here to return to the products list</a>';
		
	}
	else	// not POST
	{
		$sql	= 'select * from Product where ProdID = '.$productId;
		$set	= mysql_query($sql, $cnRuby);
		$row = mysql_fetch_assoc($set);
		$product = array(
			'ProdTypeId'	=> $row['ProductTypeID']
			,'CatId'		=> $row['CategoryID']
			,'CollId'		=> $row['CollectionID']
			,'Title'		=> $row['Title']
			,'Desc'			=> $row['Description']
			,'Cost'			=> $row['Cost']
			,'ShapeId'		=> $row['ShapeID']
			,'HasTassel'	=> $row['HasTassel']
			,'CustId'		=> $row['CustID']
			,'Priority'	=> $row['Priority']
			,'NumRemaining'	=> $row['NumRemaining']
			,'OtherDetails'	=> $row['OtherDetails']
		);
		?>

		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>Edit product - <?php echo $product['Title']; ?></title>
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

		<form name="frmAddProduct" action="edit_product.php" method="post">
		<table>
			<tr>
				<td valign="top"><b>Priority</b></td>
				<td>
					<select name="priority" id="priority">
<?php
if (isset($product['Priority'])) {
$priority = $product['Priority'];
} else {
$priority = 0;
}
?>
						<option value="<?php echo $priority; ?>" 
							<?php
							if ($product['priority'] >= 0)
								echo " selected";
							?>
						>
							Leave as is (or set to 0 if currently null)
						</option>
						<option value="null" 
							<?php
							if ($product['Priority'] == '')
								echo " selected";
							?>
						>
							NULL (discontinued - don't display on website)
						</option>
					</select>
<br/>Note: Set the order of products in the <a href="index_sort.php">Sort Orders section</a>
				</td>
			</tr>
<tr><td><br/><br/></td></tr>
			<tr>
				<td><b>Product Type</b></td>
				<td>
					<select name="selProductType" id="selProductType">
						<?php
						foreach ($productTypes as $type)
						{
							echo '<option value="'.$type['Id'].'"';
							if ($type['Id'] == $product['ProdTypeId'])
								echo ' selected';
							echo '>'.$type['Desc'].'</option>';
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
							echo '<option value="'.$cat['Id'].'"';
							if ($cat['Id'] == $product['CatId'])
								echo ' selected';
							echo '>'.$cat['Desc'].'</option>';
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
							echo '<option value="'.$collection['Id'].'"';
							if ($collection['Id'] == $product['CollId'])
								echo ' selected';
							echo '>'.$collection['Title'].'</option>';
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
				<td>
					<input type="text" name="title" id="title" 
						value="<?php echo $product['Title']; ?>" />
				</td>
			</tr>
			<tr>
				<td><b>Description</b></td>
				<td colspan="2">
					<textarea name="description" id="description" cols="80"><?php echo $product['Desc']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Cost</b></td>
				<td><input type="text" name="cost" id="cost" 
					value="<?php echo $product['Cost']; ?>" /></td>
			</tr>
			<tr>
				<td><b>Shape</b></td>
				<td>
					<select name="selShape" id="selShape">
						<?php
						foreach ($shapes as $shape)
						{
							echo '<option value="'.$shape['Id'].'"';
							if ($shape['Id'] == $product['ShapeId'])
								echo ' selected';
							echo '>'.$shape['Desc'].'</option>';
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
				<td>
					<input type="checkbox" name="tassel" id="tassel" value="" 
					<?php
					if ($product['HasTassel'] == 1)
						echo ' checked';
					?>
					/>
				</td>
			</tr>
			<tr>
				<td><b>Customer</b></td>
				<td>
					<select name="selCustomer" id="selCustomer">
						<option value="null">None</option>
						<?php
						foreach ($customers as $customer)
						{
							echo '<option value="'.$customer['Id'].'"';
							if ($customer['Id'] == $product['CustId'])
								echo ' selected';
							echo '>'.
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
				<td><input type="text" name="remaining" id="remaining" value="<?php echo $product['NumRemaining']; ?>" /></td>
			</tr>
			<tr>
				<td><b>Other Details</b></td>
				<td colspan="2">
					<textarea name="other" id="other" cols="80"><?php echo $product['OtherDetails']; ?></textarea>
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
												// Get the selected colours
												$productColours	= array();
												$sql	= 'select * 
													from ProductColour
													where ProdID = '.$productId;
												$set	= mysql_query($sql, $cnRuby);
												while ($row = mysql_fetch_assoc($set))
												{
													$productColours[] = array(
														'Id'		=> $row['ColourID']
														,'Order'	=> $row['Order']
													);
												}//while
		
												foreach ($colours as $colour)
												{
													// Was this colour selected?
													$isSelected	= false;
													$selOrder	= 0;
													foreach ($productColours as $selCol)
													{
														if ($colour['Id'] == $selCol['Id'])
														{
															$isSelected	= true;
															$selOrder	= $selCol['Order'];
															break;	// exit foreach loop
														}
													}
													
													// Print out the checkbox and order drop-down
													?>
													<tr>
														<td>
															<input type="checkbox"
																name="colour[]"
																value="<?php echo $colour['Id']; ?>"
																<?php
																if ($isSelected)
																	echo ' checked';
																?>
																>
															&nbsp;<?php echo $colour['Desc']; ?>
														</td>
														<td>
															<select style="font-size:10px;"
																name="colour_priority_<?php echo $colour['Id']; ?>"
																id="colour_priority_<?php echo $colour['Id']; ?>">
																<?php
																for ($i = 0; $i <= 10; $i++)
																{
																	echo '<option value="'.$i.'"';
																	if ($isSelected && ($i == $selOrder) )
																		echo ' selected';
																	echo '>'.$i.'</option>';
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
												// Get the selected materials
												$productMaterials	= array();
												$sql	= 'select * 
													from ProductMaterial
													where ProdID = '.$productId;
												$set	= mysql_query($sql, $cnRuby);
												while ($row = mysql_fetch_assoc($set))
												{
													$productMaterials[] = array(
														'Id'		=> $row['MaterialID']
														,'Order'	=> $row['Order']
													);
												}//while
												
												foreach ($materials as $material)
												{
													// Was this material selected?
													$isSelected	= false;
													$selOrder	= 0;
													foreach ($productMaterials as $selMat)
													{
														if ($material['Id'] == $selMat['Id'])
														{
															$isSelected	= true;
															$selOrder	= $selMat['Order'];
															break;	// exit foreach loop
														}
													}
													
													// Print out the checkbox and order drop-down
													?>
													<tr>
														<td>
															<input type="checkbox"
																name="material[]"
																value="<?php echo $material['Id']; ?>"
																<?php
																if ($isSelected)
																	echo ' checked';
																?>
																>
															&nbsp;<?php echo $material['Name']; ?>
														</td>
														<td>
															<select style="font-size:10px;"
																name="material_priority_<?php echo $material['Id']; ?>"
																id="material_priority_<?php echo $material['Id']; ?>">
																<?php
																for ($i = 0; $i <= 10; $i++)
																{
																	echo '<option value="'.$i.'"';
																	if ($isSelected && ($i == $selOrder) )
																		echo ' selected';
																	echo '>'.$i.'</option>';
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
											// Get the selected sizes
											$productSizes	= array();
											$sql	= 'select * 
												from ProductSize
												where ProdID = '.$productId;
											$set	= mysql_query($sql, $cnRuby);
											while ($row = mysql_fetch_assoc($set))
											{
												$productSizes[] = array(
													'Id'		=> $row['SizeID']
													,'Order'	=> $row['Order']
												);
											}//while
											
											foreach ($sizes as $size)
											{
												// Was this material selected?
												$isSelected	= false;
												foreach ($productSizes as $selSize)
												{
													if ($size['Id'] == $selSize['Id'])
													{
														$isSelected	= true;
														break;	// exit foreach loop
													}
												}
												
												// Print out the checkbox
												echo '<input type="checkbox" 
													name="size[]" 
													value="'.$size['Id'].'"';
												if ($isSelected)
													echo ' checked';
												echo '> '.
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
							<td colspan="3" align="center">
								<input type="hidden" id="productid" 
									name="productid" 
									value="<?php echo $productId; ?>">
								<input type="submit" value="Update" />
							</td>
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
}//if $_REQUEST['pid']
?>

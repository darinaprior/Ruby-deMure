<?php
$browseTypeId	= -1;
$browseValueId	= -1;
if ($_GET['type'] != "")
	$browseTypeId	= $_GET['type'];
if ($_GET['val'] != "")
	$browseValueId	= $_GET['val'];

if ( ($browseTypeId == -1) || ($browseValueId == -1) )
{
	?><a href="/">Type or value not supplied.  Please return to the home page by clicking here.</a><?php
}
else
{
	switch($browseTypeId)
	{
		case 1:	// product type
			$browseType		= 'Product Type';
			$sqlBrowseValue	= "select IfNull(NavName, Description) as Description from ProductType where ProdTypeID = $browseValueId limit 1";
			$sqlMain		= "select ProdID, Title, TotalCost, CategoryID from Product where ProductTypeID = $browseValueId order by Priority desc";
			break;
		case 2:	// colour
			$browseType		= 'Colour';
			$sqlBrowseValue	= "select Description from Colour where ColourID = $browseValueId limit 1";
			$sqlMain		= "select p.ProdID, Title, TotalCost, CategoryID from Product p inner join ProductColour pc on p.ProdID = pc.ProdID where pc.ColourID = $browseValueId order by pc.Order asc, p.Priority desc";
			break;
		case 3:	// shape
			$browseType		= 'Shape';
			$sqlBrowseValue	= "select Description from Shape where ShapeID = $browseValueId limit 1";
			$sqlMain		= "select ProdID, Title, TotalCost, CategoryID from Product where ShapeID = $browseValueId order by Priority desc";
			break;
		case 4:	// size
			$browseType		= 'Size';
			$sqlBrowseValue	= "select Name as Description from Size where SizeID = $browseValueId limit 1";
			$sqlMain		= "select p.ProdID, Title, TotalCost, CategoryID from Product p inner join ProductSize ps on p.ProdID = ps.ProdID where ps.SizeID = $browseValueId order by p.Priority desc";
			break;
		case 5:	// materials
			$browseType		= 'Material';
			$sqlBrowseValue	= "select Name as Description from Material where MaterialID = $browseValueId limit 1";
			$sqlMain		= "select p.ProdID, Title, TotalCost, CategoryID from Product p inner join ProductMaterial pm on p.ProdID = pm.ProdID where pm.MaterialID = $browseValueId order by pm.Order asc, p.Priority desc";
			break;
	}
	
	require_once 'Include/connection.php';
	$rset	= mysql_query($sqlBrowseValue, $cnRuby);
	while ($record = mysql_fetch_array($rset))
	{
		$browseValue = $record['Description'];
	}
														
	$title	= "Browse by $browseType: $browseValue";
	
	$sPageTitle		= $title;
	$sPageKeywords	= $title;
	require_once 'Include/header.php';
	require_once 'Include/functions.php';
	?>

	<table class="tblBody" cellpadding="0">
		<tr>
			<td class="tdR4C1">&nbsp;</td>
			<td class="tdR4C2">

				<!-- MAIN CONTENT - SCROLL IF NECESSARY -->
				<div class="dvMainScroll">
					<div class="dvMainPadding">

					<table class="tblStdFull">

						<tr>
							<td valign="top">


								<table class="tblStd" cellspacing="0">
									<tr>
										<td colspan="3">
											You are here:&nbsp;
											<a href="/">Home</a> &gt;
											<?=$title?>
										</td>
									</tr>
								</table>

								<table class="tblStdFullCentre" cellspacing="0">
									<tr><td colspan="3" class="tdHeading"><?=$title?></td></tr>
									<tr>
										<td>
											<table class="tblStdFullCentre" style="padding:5px;">
												<tr>
													<td colspan="8">
														Click on the pictures to see more details and larger images.
														<!--<br/><a href="sizing.html" target="_blank">Click here for sizing details.</a><br/><br/>-->
													</td>
												</tr>
												<tr>
													<?php
													// Get the products matching the criteria
													$iCount = 1;
													$rset	= mysql_query($sqlMain, $cnRuby);
													while ($record = mysql_fetch_array($rset))
													{
														$ProdID			= $record['ProdID'];
														$title			= $record['Title'];
														$TotalCost		= $record['TotalCost'];
														$categoryId		= $record['CategoryID'];
										
														// Get just one product image
														$sql = 'SELECT DISTINCT filepath
															FROM product_image
															WHERE product_id = ?
															ORDER BY id
															LIMIT 1';
														$stmt = $mysqli->prepare($sql);
														if ($stmt) {
															$stmt->bind_param('i', intval($ProdID));
															$stmt->bind_result($path);
															$stmt->execute();
															$stmt->fetch();
															
															// Get the full filepath
															$fullPath = getFullImagePath($path, $categoryId, TRUE/*thumbnail*/);
															$stmt->close();
														}
														
														// Get the sizes available
														$sSizes		= "";
														$qSizes		= "select Size.Name from ProductSize inner join Size on ProductSize.SizeID = Size.SizeID WHERE ProductSize.ProdID = ".$ProdID;
														$rsSizes	= mysql_query($qSizes, $cnRuby);
														while ($recSize = mysql_fetch_array($rsSizes))
														{
															$sSizes = $sSizes.$recSize['Name'].", ";
														}
														$sSizes = substr($sSizes, 0, -2);	// strip trailing comma and space
														?>
														<td align="center" style="cursor:pointer;">
															<a href="product.php?pid=<?=$ProdID?>" style="color:#330000;" class="aNoBold">
																<img src="<?php echo $fullPath; ?>" title="<?php echo $title; ?>" border="0">
																<br/><b><?php echo $title; ?></b>
																<?php
																// don't show prices for bespoke
																if ($categoryId != 1) {
																	?><br/>&euro;<? echo $TotalCost ?><?php
																	if ($sSizes != '')
																		echo " ($sSizes)";
																} else {
																	?><br/>(bespoke)<?php
																}
																?>
															</a>
														</td>
														<td width="5"></td>
														<?php
														if ($iCount == 4)
														{
															?></tr><tr><td><br/></td></tr><tr><?php
															$iCount = 1;
														}
														else
														{
															$iCount++;
														}
													}
													?>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				
					</div>
				</div>
				<!-- end of MAIN CONTENT -->
			
			</td>
			<td class="tdR4C3">&nbsp;</td>
		</tr>
			
	</table>
	<?php
	require_once 'Include/footer.php';
	
	if ($cnRuby)
		mysql_close($cnRuby);
		
}//if $browseTypeId
?>

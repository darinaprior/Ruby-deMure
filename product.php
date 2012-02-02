<?php
if ($_GET['pid'] != "")
	$g_iProdID = $_GET['pid'];
else
	$g_iProdID = -1;

require_once 'Include/connection.php';

if ($g_iProdID == -1)
{
	?><a href="/">No product ID supplied.  Please return to the home page by clicking here.</a><?php
}
else
{
	// Get product details from main [Product] table
	$qProduct		= "select * from Product where ProdID = ".$g_iProdID." limit 1";
	$rsProduct		= mysql_query($qProduct, $cnRuby);
	$recProduct		= mysql_fetch_array($rsProduct);
	$Title			= $recProduct['Title'];
								
	$sPageTitle		= $Title;
	$sPageKeywords	= $Title.', product';
	$has_tabs = TRUE;	// this page contains tabs
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
								<?php
								$ProductTypeID	= $recProduct['ProductTypeID'];
								$Description	= $recProduct['Description'];
								$Cost			= $recProduct['Cost'];
								$VAT			= $recProduct['VAT'];
								$TotalCost		= $recProduct['TotalCost'];
								$ShapeID		= $recProduct['ShapeID'];
								$CategoryID		= $recProduct['CategoryID'];
								$CollectionID	= $recProduct['CollectionID'];
								$HasTassel		= $recProduct['HasTassel'];
								$otherDetails	= $recProduct['OtherDetails'];
								$CustID			= $recProduct['CustID'];
								$NumRemaining	= $recProduct['NumRemaining'];
			
								// Get associated details from other tables
								$qType		= "select Description from ProductType where ProdTypeID = ".$ProductTypeID." limit 1";
								$rsType		= mysql_query($qType, $cnRuby);
								$recType	= mysql_fetch_array($rsType);
								$Type		= $recType['Description'];
			
								$qShape		= "select Description from Shape where ShapeID = ".$ShapeID." limit 1";
								$rsShape	= mysql_query($qShape, $cnRuby);
								$recShape	= mysql_fetch_array($rsShape);
								$Shape		= $recShape['Description'];
			
								$qCategory		= "select Description from Category where ProdCatID = ".$CategoryID." limit 1";
								$rsCategory		= mysql_query($qCategory, $cnRuby);
								$recCategory	= mysql_fetch_array($rsCategory);
								$Category		= $recCategory['Description'];
			
								if ($CollectionID != null) {
									$qCollection	= "select Title from Collection where CollectionID = ".$CollectionID." limit 1";
									$rsCollection	= mysql_query($qCollection, $cnRuby);
									$recCollection	= mysql_fetch_array($rsCollection);
									$Collection		= $recCollection['Title'];
								} 
				
								// Get the colour and size options, along with the associated numbers available
								// First, get the option IDs for this product
								$options = array();
								$sql = 'SELECT DISTINCT id, lead_time, purchase_instructions
									FROM product_option
									WHERE product_id = ?';
								$stmt = $mysqli->prepare($sql);
								if ($stmt) {
									$stmt->bind_param('i', intval($g_iProdID));
									$stmt->bind_result($optionId, $leadTime, $purchaseInstructions);
									$stmt->execute();
									while ($stmt->fetch()) {
										// Use the ID as the KEY and set up the 
										// (mostly empty) structured array we'll want as the VALUE
										$options[$optionId] = array(
											'Colours' => array(),
											'Sizes' => array(),
											'LeadTime' => $leadTime,
											'PurchaseInstructions' => $purchaseInstructions,
										);
									}
									$stmt->close();
								}
								
								// Loop through the option KEYs, getting each option's colours
								// and storing them in our VALUE array							
								$sql = 'SELECT DISTINCT
										c.Description,
										c.HexValue
									FROM Colour c
									INNER JOIN product_option_colour poc ON c.ColourID = poc.colour_id
									WHERE poc.option_id = ?
									ORDER BY poc.priority ASC';
								$stmt = $mysqli->prepare($sql);
								if ($stmt) {
									$stmt->bind_result($colourName, $hex);
									foreach ($options as $optionId => $details) {
										$stmt->bind_param('i', intval($optionId));
										$stmt->execute();
										while ($stmt->fetch()) {
											$options[$optionId]['Colours'][] = array(
												'Name' => $colourName,
												'Hex' => $hex,
											);
										}
									}
									$stmt->close();
								}
								
								// Now do the same for the sizes along with the associated numbers available							
								$sql = 'SELECT DISTINCT 
										s.Name,
										pos.num_in_stock
									FROM Size s
									INNER JOIN product_option_size pos ON s.SizeID = pos.size_id
									WHERE pos.option_id = ?
									ORDER BY s.SizeID ASC';
								$stmt = $mysqli->prepare($sql);
								if ($stmt) {
									$stmt->bind_result($sizeName, $numInStock);
									foreach ($options as $optionId => $details) {
										$stmt->bind_param('i', intval($optionId));
										$stmt->execute();
										while ($stmt->fetch()) {
											$options[$optionId]['Sizes'][] = array(
												'Size' => $sizeName,
												'NumInStock' => $numInStock,
											);
										}
									}
									$stmt->close();
								}
								
								// Get any reviews
								$reviews = array();
								$sql = 'SELECT comment, name
									FROM review
									WHERE product_id = ?
									AND status = 1';
								$stmt = $mysqli->prepare($sql);
								if ($stmt) {
									$stmt->bind_param('i', intval($g_iProdID));
									$stmt->bind_result($comment, $name);
									$stmt->execute();
									while ($stmt->fetch()) {
										$reviews[] = array(
											'Comment' => $comment,
											'Name' => $name,
										);
									}
									$stmt->close();
								}
				
								// Get the materials used
								$qMaterials	= "select Material.Name from ProductMaterial inner join Material on ProductMaterial.MaterialID = Material.MaterialID where ProductMaterial.ProdID = ".$g_iProdID." order by ProductMaterial.Order asc";
								$rsMaterials	= mysql_query($qMaterials, $cnRuby);	
								$numMaterials	= mysql_num_rows($rsMaterials);
								
								if ($numMaterials == 0) {
									$sMaterials = '';
								} else {
									if ($numMaterials > 4) {
										$colLimit	= 4;
										$sMaterials	= '<table class="tblStd" cellspacing="0" cellpadding="0"><tr><td valign="top"><ul>';
									} else if ($numMaterials > 3 && $otherDetails != '') {
										$colLimit	= 3;
										$sMaterials	= '<table class="tblStd" cellspacing="0" cellpadding="0"><tr><td valign="top"><ul>';
									} else {
										$colLimit	= -1;
										$sMaterials	= "<ul>";
									}
									
									$iCol = 0;
									while ($recMaterial = mysql_fetch_array($rsMaterials)) {
										if ($iCol == $colLimit) {
											$sMaterials	.= '</ul></td><td valign="top"><ul>';
											$iCol = 0;
										}
										$sMaterials .= "<li>".$recMaterial['Name']."</li>";
										$iCol++;
									}
									
									if ($colLimit > 0) {
										$sMaterials .= '</ul></td></tr></table>';
									} else {
										$sMaterials .= '</ul>';
									}
								}//if $numMaterials
									
								// Get the customer's name, if stored
								if ($CustID != null) {
									$qCustomer		= "select Privacy, FirstName, LastName from Customer where CustID = ".$CustID." limit 1";
									$rsCustomer		= mysql_query($qCustomer, $cnRuby);
									$recCustomer	= mysql_fetch_array($rsCustomer);
									if ($recCustomer['Privacy'] == 1)
										$Customer	= "Anonymous";
									else
										$Customer	= $recCustomer['FirstName']." ".$recCustomer['LastName'];
								} 
								?>

								<table class="tblStd" cellspacing="0">
									<tr>
										<td colspan="3">
											You are here:&nbsp;
											<a href="/">Home</a> &gt;
											<?php
											if ($CategoryID == 1)
											{
												?>
												<a href="bespoke.php"><?=$Category?></a> &gt;
												<?php
											}
											else if ($CategoryID == 3)
											{
												?>
												<a href="off-the-rack.php"><?=$Category?></a> &gt;
												<a href="collection.php?cid=<?=$CollectionID?>"><?=$Collection?></a> &gt;
												<?php
											}
											?>
											<?=$Title?>
										</td>
									</tr>
								</table>

								<table class="tblStdFullCentre" cellspacing="0" cellpadding="0">
									<tr>
										<td colspan="2" align="center">
											<h2>
											<?=$Title?>
											<?php
											if ($CategoryID != 1) {
												echo '&nbsp;&nbsp;&euro;'.number_format($TotalCost, 2);
											}
											?>
											</h2>
										</td>
									</tr>
									<tr>
										<td valign="top" width="40%">
											<table class="tblStdFullCentre">
												<?php
												// Get the first 3 images for this product
												$images = array();
												$sql = 'SELECT DISTINCT filepath
													FROM product_image
													WHERE product_id = ?
													AND filepath IS NOT NULL
													ORDER BY id
													LIMIT 3';
												$stmt = $mysqli->prepare($sql);
												if ($stmt) {
													$stmt->bind_param('i', intval($g_iProdID));
													$stmt->bind_result($path);
													$stmt->execute();
													while ($stmt->fetch()) {
														// Get the full filepath
														$fullPathThumb = getFullImagePath($path, $CategoryID, TRUE/*thumbnail*/);
														$images[] = $fullPathThumb;
													}
													$stmt->close();
												}
												
												foreach ($images as $path) {
													?>
													<td align="center">
														<div style="width:90px; height:90px;">
															<a href="gallery.php?pid=<?php echo $g_iProdID; ?>">
																<img src="<?php echo $path; ?>" alt="More images" border="0" />
															</a>
														</div>
													</td>
													<?php
												}//foreach
												?>
												</tr>
												<tr><td colspan="3"><a href="gallery.php?pid=<?=$g_iProdID?>"><b>Click for image gallery &raquo;</b></a></td></tr>

												<tr><td><br/><br/></td></tr>
												<?php
												// General info about the product type
												if ($ProductTypeID == 1)	// pasties
												{
													?>
													<tr>
														<td width="100%" colspan="3" style="font-size:10px; text-align:left;">
															All of my 
															<?php
															if ($HasTassel == 1)
															{
																?>tasseled <?php
															}
															?>
															pasties:
															<ul>
																<li>are hand-made with an emphasis on quality and finish</li>
																<li>have sewn seams (not glued seams)</li>
																<?php
																if ($HasTassel == 1)
																{
																	?><li>have tassels that are handmade from hand-dyed fringe</li><?php
																}
																?>
																<li>have comfortable and durable leatherette backing</li>
																<li>come in a lovely giftbox with care card and tape to stick them on!</li>
															</ul>
														</td>
													</tr>
													<?php
												}//if pasties
												else if ($ProductTypeID == 4)	// stocking toppers
												{
													?>
													<tr>
														<td width="100%" colspan="3" style="font-size:10px; text-align:left;">
															All of my stocking toppers:
															<ul>
																<li>are hand-made with an emphasis on quality and finish</li>
																<li>can be used with both stockings and hold-ups</li>
																<li>have comfortable felt backing</li>
																<li>have metal garter grips (not plastic)</li>
																<li>come in a lovely giftbox with care card and instructions</li>
															</ul>
														</td>
													</tr>
													<?php
												}//if stocking toppers
												else if ($ProductTypeID == 5)	// eye-patch
												{
													?>
													<tr>
														<td width="100%" colspan="3" 
															style="font-size:10px; text-align:left;">
															All of my eye-patches:
															<ul>
																<li>are hand-made with an emphasis on quality and finish</li>
																<li>have sewn seams (not glued seams)</li>
																<li>have comfortable and durable leatherette backing</li>
																<li>are attached with adjustable elastic</li>
																<li>come in a lovely giftbox</li>
															</ul>
														</td>
													</tr>
													<?php
												}//if eye-patch
												else if ($ProductTypeID == 10)	// bow-tie
												{
													?>
													<tr>
														<td width="100%" colspan="3" 
															style="font-size:10px; text-align:left;">
															All of my bow-ties:
															<ul>
																<li>are hand-made with an emphasis on quality and finish</li>
																<li>can be made to fit your neck size</li>
																<li>can be made to match other items</li>
																<li>come in a lovely giftbox</li>

															</ul>
															<a href="/sizing.php#bowties">Click here for sizing details</a>
															<br/><a href="http://www.videojug.com/film/how-to-tie-a-bow-tie" class="external" target="_blank">And here for video instructions on how to tie your bow tie</a
															<br/><br/>
														</td>
													</tr>
													<?php
												}//if eye-patch
												?>

												<tr>
													<td colspan="3" align="center">
														<?php
														if ($CategoryID == 1)
														{
															?>
															<a href="bespoke.php">&lt;&nbsp;Back to <?=$Category?></a>
															<?php
														}
														else if ($CategoryID == 3)
														{
															?>
															<a href="collection.php?cid=<?=$CollectionID?>">&lt;&nbsp;Back to <?=$Collection?></a>
															<?php
														}
														?>
													</td>
												</tr>
											</table>
										</td>
										<td valign="top">
											<table class="tblStdFull" cellpadding="5">
												<?php
												// How to buy / get in touch (for bespoke)
												if ($CategoryID == 1) {
													?>
													<tr>
														<td colspan="2" valign="top" align="center">
															<a href="mailto:info@rubydemure.com">To get your own bespoke accessories, email Ruby... click here!</a>
														</td>
													</tr>
													<?php
												} else  {
													?>
													<tr>
														<td colspan="2" valign="top" align="center">
															<font size="1">
															<a href="mailto:info@rubydemure.com">
																<?php
																if ($NumRemaining > 0)
																{
																	?>Online shopping isn't quite set up yet - click here to put in an order!<?php
																}
																else
																{
																	?>OUT OF STOCK - click here to order (please give at least 14 days notice)<?php
																}
																?>
															</a>
															</font>
														</td>
													</tr>
													<?php
												}
												
												// The rest of the details go on tabs...
												?>
												<tr>
													<td colspan="2" valign="top">
													
														<!-- TAB HEADERS -->
														<div id="tabs_container">
														    <ul id="tabs">
														        <li class="active"><a href="#tab1">Details</a></li>
														        <?php
														        // Ignore the "Colours & Sizes" tab for bespoke
														        if ($CategoryID != 1) {
														        	?>
														        	<li><a href="#tab2">Colours &amp; Sizes</a></li>
														        	<?php
														        }
														        if (COUNT($reviews) > 0) {
														        	?>
														        	<li><a href="#tab3">Reviews</a></li>
														        	<?php
														        }
														        ?>
														        <li><a href="#tab4">Payment</a></li>
														        <li><a href="#tab5">Shipping</a></li>
														    </ul>
														</div>
														<!-- END TAB HEADERS -->	
														
														<!-- TAB CONTENTS -->
														<div id="tabs_content_container">
														
														    <!-- TAB 1: DETAILS -->
														    <div id="tab1" class="tab_content" style="display: block;">
													        	<table class="tblStdFull" cellpadding="2">
													        		<tr><td colspan="2"><?php echo $Description; ?></td></tr>
													        		<tr><td><br/></td></tr>
																<?php
																if ($CategoryID == 1) {
																	?><tr><td>Made for:</td><td><?php echo $Customer; ?></td></tr><?php
																} else {
																	?><tr><td>Collection:</td><td><?php echo $Collection; ?></td></tr><?php
																}
																?>
																<tr>
																	<td valign="top" style="white-space:nowrap;">Basic Details:</td>
																	<td>
																		<?=$Shape?>-shaped 
																		<?php
																		if ($HasTassel == 1) {
																			echo 'tasseled ';
																		}
																		echo strtolower($Type).' ('.strtolower($Category).')';
																		
																		// For bespoke only, show the colours (no separate colours tab)
														        			// Split the colour names away from the hex values (we only need the names here)
														        			$colours = array();
														        			foreach ($options as $option) {
																			foreach ($option['Colours'] as $colour) {
																			    $colours[] = $colour['Name'];
																			}
																		}
																		if ($CategoryID == 1) {
																			echo ' in '.formatColourString($colours, true/*suppress title case*/);
																		}
																		?>
																	</td>
																</tr>
																<?php
																if ($sMaterials != "") {
																	?><tr><td valign="top">Materials:</td><td><?=$sMaterials?></td></tr><?php
																}
																?>
																<tr><td colspan="2"><?=$otherDetails?></td></tr>
															</table>
														    </div>
														    
														    <!-- TAB 2: COLOURS AND SIZES (ignore for bespoke) -->
														    <?php
														    if ($CategoryID != 1) {
														    	?>
															    <div id="tab2" class="tab_content">
														        	<table class="tblStdFull" cellpadding="2">
														        		<tr>
														        			<td colspan="2">
														        				<span id="colour_click_hidden"></span>
														        				<span id="colour_click"></span>
														        			</td>
														        		</tr>
														        		<?php
														        		// Loop through the options...
														        		foreach ($options as $optionId => $details) {
														        			// Start the row for this option
														        			echo '<tr>';
														        		
														        			// Split the colour names and hex values apart
														        			$colours = array();
														        			$hexValues = array();
																		foreach ($details['Colours'] as $colour) {
																		    $colours[] = $colour['Name'];
																		    $hexValues[] = $colour['Hex'];
																		}
														        			$colourString = formatColourString($colours);
														        			
														        			// Also take note of the lead time and purchase instructions, if any
														        			$leadTime = $details['LeadTime'];
														        			$purchaseInstructions = $details['PurchaseInstructions'];
														        			
														        			// LEFT: Print out little blocks of the colours, followed by the colour names
														        			echo '<td style="border: solid 1px #0F050D; vertical-align: top;">';
														        			echo '<table><tr>';
														        			foreach ($hexValues as $hex) {
														        				echo '<td style="height:20px; width:20px; background-color:#'.$hex.';"></td>';
														        			}
														        			echo '</tr></table>';
														        			echo $colourString;
														        			echo '</td>';
														        			
														        			// RIGHT: Now loop through the sizes and print them with their
														        			// availability (in an inner table)
														        			$sizes = $details['Sizes'];
														        			echo '<td style="border: solid 1px #0F050D; vertical-align: top;">';
														        			echo '<table class="tblStdFull" cellpadding="2">';
														        			$showLeadTime = FALSE;
														        			foreach ($sizes as $sizeDetails) {
														        				$num = $sizeDetails['NumInStock'];
														        				
														        				// If I've set num-in-stock to be 101, that means
														        				// this product is ALWAYS available - just show size
														        				if ($num == 101) {
															        				echo '<tr><td>'.$sizeDetails['Size'].'</td></tr>';
														        				} else {
															        				echo ($num == 0) ? '<tr style="color:#777;">' : '<tr>';
															        				echo '<td>'.$sizeDetails['Size'].'</td>';
															        				echo '<td>.....</td>';
														        					echo ($num == 0) ? '<td>OUT OF STOCK</td>' : '<td>'.$num.' available</td>';
															        				echo '</tr>';
															        			}
														        			}//foreach
									        					
									        								// Follow the available sizes with any lead time or insructions necessary 
													        				if ($leadTime != '' || $purchaseInstructions != '') {
														        				echo '<tr><td>';
														        				echo ($leadTime != '') ? 'Allow at least '.$leadTime.' for production time.<br/>' : '';
														        				echo ($purchaseInstructions != '') ? $purchaseInstructions : '';
														        				echo '</td></tr>';
														        			}//$leadTime
														        			
														        			echo '</table></td>';
														        			
														        			// End the row for this option
														        			echo '</tr>';
														        		}
													        			?>
																</table>
															    </div>
															<?php
														    }//if $CategoryID
														    ?>
														
														    <!-- TAB 3: REVIEWS -->
														    <?php
														    if (COUNT($reviews) > 0) {
														    	?>
															    <div id="tab3" class="tab_content">
															    	<?php
															    	foreach ($reviews as $review) {
															    		?>
															    		<div class="testimonial" width="100%">
																		<table class="tblStdFull" cellpadding="4">
																			<tr>
																				<td width="7%"><img src="/images/quote_open.png" /></td>
																				<td width="60%"><?php echo $review['Comment']; ?></td>
																				<td width="26%"><?php echo '- <i>'.$review['Name'].'</i>'; ?></td>
																				<td width="7%" align="right"><img src="/images/quote_close.png" /></td>
																			</tr>
																		</table>
																	</div>
															    		<?php
															    	}
															    	?>
															    </div>
															<?php
														    }//if $reviews
														    ?>
														    
														    <!-- TAB 4: PAYMENT -->
														    <div id="tab4" class="tab_content">
													        	<table class="tblStdFull" cellpadding="2">
													        		<tr>
													        			<td valign="top"><b>PAYPAL</b></td>
													        			<td>
													        				I accept payments directly to PayPal. When you place an order, 
													        				just tell me you want to pay by PayPal.
													        				<br/>I'll send a PayPal invoice to your email address and you can pay directly from there.</b>
													        				<br/><br/>
													        			</td>
													        		</tr>
													        		<tr>
													        			<td valign="top"><b>CHEQUE</b></td>
													        			<td>
													        				You can send a cheque made payable to "Ruby deMure" to:
													        				<br/>Ruby deMure
													        				<br/>4A Richmond Avenue
													        				<br/>Monsktown
													        				<br/>Co. Dublin
													        				<br/>Ireland
													        				<br/><br/>
													        			</td>
													        		</tr>
													        		<tr>
													        			<td valign="top"><b>CASH</b></td>
													        			<td>
													        				If you are based in Dublin, Ireland, I can meet you in person and you can pay in cash. 
													        				Or if you buy directly at a show or fair, you can of course pay in cash.
													        			</td>
													        		</tr>
															</table>	
														    </div>
														    
														    <!-- TAB 5: SHIPPING -->
														    <div id="tab5" class="tab_content">
															<b>Where do you ship to?</b>
															<br/>I ship to anywhere in the world.
															
															<br/><br/><b>How much does shipping cost?</b>
															<br/>I am gathering info on shipping costs to all locations.
															I will post it on the site as soon as possible, but in the 
															meantime you can ask me by <a href="contact.php">contacting me here</a>
															
															<br/><br/><b>Do you combine shipping?</b>
															<br/>Yes. If you buy more than one item from me at the same time, 
															I can ship them together so you can save on shipping costs. 
															When ordering, make sure that you let me know you're ordering more than one item. 
															I will let you know the total for all items and combined shipping.
														    </div>														    
														</div>
														<!-- END TAB CONTENTS -->
													</td>
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

}//if $g_iProdID

if ($cnRuby)
	mysql_close($cnRuby);
?>

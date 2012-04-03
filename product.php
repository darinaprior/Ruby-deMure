<?php
// Get the user's selections
$selectedProductId = -1;
$selectedOptionId = -1;
if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != "") {
	$selectedProductId = $_REQUEST['pid'];
}
if (isset($_REQUEST['oid']) && $_REQUEST['oid'] != "") {
	$selectedOptionId = $_REQUEST['oid'];
}

require_once 'Include/connection.php';

if ($selectedProductId == -1) {
	?><a href="/">No product ID supplied.  Please return to the home page by clicking here.</a><?php
} else {
	// Get product details from main [Product] table
	$qProduct		= "select * from Product where ProdID = ".$selectedProductId." limit 1";
	$rsProduct		= mysql_query($qProduct, $cnRuby);
	$recProduct		= mysql_fetch_array($rsProduct);
	$id			= $recProduct['ProdID'];
	if ($selectedProductId != $id) {
		?><a href="/">Product not found.  Please return to the home page by clicking here.</a><?php
		exit;
	}
	
	$Title = $recProduct['Title'];	
	$sPageTitle = $Title;
	$sPageKeywords = $Title.', product';
	$has_tabs = TRUE;	// this page contains tabs
	$hasZoom = TRUE;	// this page contains image zoom functionality
	$hasRowScroller = TRUE;	// this page contains image scrolling functionality
	require_once 'Include/header.php';
	require_once 'Include/functions.php';
	?>
	
	<script type="text/javascript">
		$jq(document).ready(function(){
			// Initialise the image zoom functionality
			var options = {
				zoomWidth: 300,
				zoomHeight: 300,
				title: true,
				showEffect: 'fadein',
				hideEffect: 'fadeout'
			};
			$jq('.jqz_anchor').jqzoom(options);
			
			// Initialise the thumbnail scroller
			$jq('.scroller').rowscroller({
				navUp: '#scroller-up',
				navDown: '#scroller-down',
				visibleRows: 2,
				rowsToScroll: 2,
				itemsPerRow: 5,
				navDisabledClass: 'disabled'
			});
		});
	</script>
	
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
									$stmt->bind_param('i', intval($selectedProductId));
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
								// Also take note if this product is completely out of stock
								$allOutOfStock = TRUE;
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
											if ($numInStock > 0) {
												$allOutOfStock = FALSE;
											}
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
									$stmt->bind_param('i', intval($selectedProductId));
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
								$qMaterials	= "select Material.Name from ProductMaterial inner join Material on ProductMaterial.MaterialID = Material.MaterialID where ProductMaterial.ProdID = ".$selectedProductId." order by ProductMaterial.Order asc";
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
												echo '&nbsp;&nbsp;&euro;'.formatPrice($TotalCost);
											}
											?>
											</h2>
										</td>
									</tr>
									<tr>
										<td valign="top" width="40%">
											<table class="tblStdFullCentre">
												<?php
												// Get all the images for this product (3 sizes for each)
												$images = array();
												
												// Filter on option ID if the user has selected one
												$whereOption = '';
												if ($selectedOptionId > 0) {
													$whereOption = 'AND (option_id = ? OR option_id IS NULL) ';
												}
												$sql = 'SELECT DISTINCT filename, caption
													FROM product_image_TEMP
													WHERE product_id = ?
													'.$whereOption.'
													AND filename IS NOT NULL
													ORDER BY IFNULL(priority, 1000), id';
												$stmt = $mysqli->prepare($sql);
												if ($stmt) {
													if ($selectedOptionId > 0) {
														$stmt->bind_param(
															'ii',
															intval($selectedProductId),
															intval($selectedOptionId)
														);
													} else {
														$stmt->bind_param('i', intval($selectedProductId));
													}
													$stmt->bind_result($name, $caption);
													$stmt->execute();
													while ($stmt->fetch()) {
														// Get the filepaths for the various image sizes
														$images[] = array(
															'Full' => getProductImagePath($selectedProductId, $name, 1/*full*/),
															'Medium' => getProductImagePath($selectedProductId, $name, 2/*medium*/),
															'Thumb' => getProductImagePath($selectedProductId, $name, 4/*thumb*/),
															'Caption' => htmlspecialchars($caption),
														);
													}//while
													$stmt->close();
												}
												
												// Make sure we have at least one image
												if (COUNT($images) == 0) {
													$images[] = array(
														'Full' => getProductImagePath($selectedProductId, '', 1/*full*/),
														'Medium' => getProductImagePath($selectedProductId, '', 2/*medium*/),
														'Thumb' => getProductImagePath($selectedProductId, '', 4/*thumb*/),
														'Caption' => '',
													);
												}
												
												// START - image gallery (using jQZoom and rowscroller)
												?>
												<tr>
													<td style="text-align:center; height:200px; width:100%; padding-left:48px;">
														<a href="<?php echo $images[0]['Full']; ?>" class="jqz_anchor" rel="jqz_gallery">
														    <img src="<?php echo $images[0]['Medium']; ?>" title="<?php echo $images[0]['Caption']; ?>" />
														</a>
													</td>
												</tr>
												<tr><td>Hover above to zoom.  Click below to change the image.</td></tr>
												<tr>
													<td>
														<table cellpadding="0" cellspacing="0">
															<tr>
																<td>
																	<div class="scroller-nav">
																		<a href="#" id="scroller-up"></a>
																	</div>
																</td>
																<td>
																	<div class="scroller-content">
																		<div class="scroller">
																			<?php
																			foreach ($images as $key => $paths) {
																				?>
																				<div>
																					<a <?php if($key == 0) { echo 'class="zoomThumbActive"';} ?> 
																						href="javascript:void(0);" 
																						rel="{
																							gallery: 'jqz_gallery', 
																							smallimage: '<?php echo $paths['Medium']; ?>', 
																							largeimage: '<?php echo $paths['Full']; ?>', 
																							zoomWrapperTitle: '<?php echo $paths['Caption']; ?>'
																						}">
																					    <img src="<?php echo $paths['Thumb']; ?>" width="50" height="50">
																					</a>
																				</div>
																				<?php
																			}//foreach
																			?>
																		</div>
																	</div>
																</td>
																<td>
																	<div class="scroller-nav">
																		<a href="#" id="scroller-down"></a>
																	</div>
																</td>	
															</tr>
														</table>
													</td>
												</tr>
												<?php
												// END - image gallery (using jQZoom and rowscroller)
												?>
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
															<?php
															if ($allOutOfStock) {
																?>
																<a href="mailto:info@rubydemure.com">
																	OUT OF STOCK - click here to order (please give at least 14 days notice)
																</a>
																<?php
															} else {
																?>
																<a href="mailto:info@rubydemure.com">
																	Online shopping isn't quite set up yet - click here to put in an order!
																</a>
																<?php
																
																/* DEBUG *
																// START PAYPAL ADD-TO-CART FORM
																?>
																<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
																	<input type="hidden" name="cmd" value="_s-xclick">
																	<input type="hidden" name="hosted_button_id" value="48B3VTYAJFBTG">
																	<table class="tblStdFull">
																		<tr>
																			<td>
																				<input type="hidden" name="on0" value="Colour">Colour
																			</td>
																			<td>
																				<select name="os0">
																					<option value="Red and gold">Red and gold </option>
																					<option value="Black and gold">Black and gold </option>
																					<option value="Blue and gold">Blue and gold </option>
																					<option value="Pink and gold">Pink and gold </option>
																					<option value="Purple and gold">Purple and gold </option>
																					<option value="White and gold">White and gold </option>
																					<option value="Yellow and gold">Yellow and gold </option>
																					<option value="Green and gold">Green and gold </option>
																				</select>
																			</td>
																			<td>
																				<input type="hidden" name="on1" value="Size">Size
																			</td>
																			<td>
																				<select name="os1">
																					<option value="Medium">Medium </option>
																				</select>
																			</td>
																			<td>
																				<input type="image" name="submit" border="0" 
																					src="https://www.paypalobjects.com/en_US/i/btn/btn_cart_LG.gif" 
																					alt="PayPal - The safer, easier way to pay online!" />
																				<img alt="" border="0" width="1" height="1" 
																					src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" />
																			</td>
																		</tr>
																	</table>
																</form>
																<?php
																// END PAYPAL ADD-TO-CART FORM
																/* DEBUG */
																
															}//if $allOutOfStock
															?>
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
														        if ($ProductTypeID == 1 || $ProductTypeID == 4 || $ProductTypeID == 5 || $ProductTypeID == 10) {
														        	?>
														        	<li><a href="#tab4">General Info</a></li>
														        	<?php
														        }
														        ?>
														        <li><a href="#tab5">Payment</a></li>
														        <li><a href="#tab6">Shipping</a></li>
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
																// Bespoke only:
																if ($CategoryID == 1) {
																	?><tr><td>Made for:</td><td><?php echo $Customer; ?></td></tr><?php
																}
																// Don't show basic details for garters or collars
																if ($ProductTypeID != 6 && $ProductTypeID != 11) {
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
																}
																if (isset($sMaterials) && $sMaterials != '') {
																	echo '<td valign="top">Materials:</td><td>'.$sMaterials.'</td></tr>';
																}
																if (isset($otherDetails) && $otherDetails != '') {
																	echo '<tr><td><br/></td></tr>';
																	echo '<tr><td colspan="2">'.$otherDetails.'</td></tr>';
																}//if
																?>
															</table>
														    </div>
														    
														    <!-- TAB 2: COLOURS AND SIZES (ignore for bespoke) -->
														    <?php
														    if ($CategoryID != 1) {
														    	?>
															    <div id="tab2" class="tab_content">
														        	<table class="tblStdFull" cellpadding="2">
												        				<form method="GET">
												        					<input type="hidden" id="pid" name="pid" value="<?php echo $selectedProductId; ?>" />
												        					<input type="submit" class="button" value="Show ALL photos" />
												        				</form>
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
														        			// At the right of the cell, show button to filter images to only this option id
														        			?>
														        			<td style="border: solid 1px #0F050D; vertical-align: top;">
														        				<form method="GET">
														        					<input type="hidden" id="pid" name="pid" value="<?php echo $selectedProductId; ?>" />
														        					<input type="hidden" id="oid" name="oid" value="<?php echo $optionId; ?>" />
														        					<span style="float:right;">
														        						<input type="submit" class="button" value="Show photos">
														        					</span>
														        				</form>
													        					<table>
													        						<tr>
															        					<?php
																	        			foreach ($hexValues as $hex) {
																	        				echo '<td style="height:20px; width:20px; background-color:#'.$hex.';"></td>';
																	        			}
																	        			?>
																	        		</tr>
																	        	</table>
													        					<?php echo $colourString; ?>
														        			</td>
														        			<?php
														        			
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
														    
														    <!-- TAB 4: GENERAL INFO -->
														    <div id="tab4" class="tab_content">
													        	<table class="tblStdFull" cellpadding="2">
													        		<?php
																// General info about the product type
																switch($ProductTypeID) {
																	case 1:
																		// pasties
																		?>
																		<tr>
																			<td width="100%" colspan="3">
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
																		break;
																	case 4:
																		// stocking toppers
																		?>
																		<tr>
																			<td width="100%" colspan="3">
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
																		break;
																	case 5:
																		// eye-patch
																		?>
																		<tr>
																			<td width="100%" colspan="3">
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
																		break;
																	case 10:
																		// bow-tie
																		?>
																		<tr>
																			<td width="100%" colspan="3">
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
																		break;
																}//switch
																?>
															</table>	
														    </div>
														    
														    <!-- TAB 5: PAYMENT -->
														    <div id="tab5" class="tab_content">
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
														    
														    <!-- TAB 6: SHIPPING -->
														    <div id="tab6" class="tab_content">
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

}//if $selectedProductId

if ($cnRuby)
	mysql_close($cnRuby);
?>
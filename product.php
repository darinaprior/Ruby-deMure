<?php
if ($_GET['pid'] != "")
	$g_iProdID = $_GET['pid'];
else
	$g_iProdID = -1;

include("Include/connection.php");

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
	include("Include/header.php");
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
								$DefaultImageID	= $recProduct['DefaultImageID'];
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
			
								if ($CollectionID != null)
								{
									$qCollection	= "select Title from Collection where CollectionID = ".$CollectionID." limit 1";
									$rsCollection	= mysql_query($qCollection, $cnRuby);
									$recCollection	= mysql_fetch_array($rsCollection);
									$Collection		= $recCollection['Title'];
								} 
				
								// Get the colours
								$sColours	= "";
								$qColours	= "select Colour.Description from ProductColour inner join Colour on ProductColour.ColourID = Colour.ColourID where ProductColour.ProdID = ".$g_iProdID." order by ProductColour.Order asc";
								$rsColours	= mysql_query($qColours, $cnRuby);
								while ($recColour = mysql_fetch_array($rsColours))
								{
									$sColours = $sColours.$recColour['Description'].", ";
								}
								$sColours = substr($sColours, 0, -2);	// strip trailing comma and space
				
								// Get the sizes available
                                                                $sSizes		= "";
								if ($ProductTypeID != 4 && $ProductTypeID != 9) {	// not for stocking toppers or brooches
								  $qSizes		= "select Size.Name from ProductSize inner join Size on ProductSize.SizeID = Size.SizeID where ProductSize.ProdID = ".$g_iProdID;
								  $rsSizes	= mysql_query($qSizes, $cnRuby);
								  while ($recSize = mysql_fetch_array($rsSizes))
								  {
									$sSizes = $sSizes.$recSize['Name'].", ";
								  }
								  $sSizes = substr($sSizes, 0, -2);	// strip trailing comma and space
                                                                }//if $ProductTypeID
				
								// Get any testimonials
								$sComment	= "<ul>";
								$qComment	= "select Comment, Name from Testimonial where ProdID = ".$g_iProdID." and Status = 1";
								$rsComment	= mysql_query($qComment, $cnRuby);
								while ($recComment = mysql_fetch_array($rsComment))
								{
									$sComment = $sComment."<li>'".$recComment['Comment']."' - <i>".$recComment['Name']."</i></li>";
								}
								if ($sComment != "<ul>")
									$sComment = $sComment."</ul>";
								else
									$sComment = "";
				
								// Get the materials used
								$qMaterials	= "select Material.Name from ProductMaterial inner join Material on ProductMaterial.MaterialID = Material.MaterialID where ProductMaterial.ProdID = ".$g_iProdID." order by ProductMaterial.Order asc";
								$rsMaterials	= mysql_query($qMaterials, $cnRuby);	
								$numMaterials	= mysql_num_rows($rsMaterials);
								
								if ($numMaterials == 0)
								{
									$sMaterials = '';
								}
								else
								{
									if ($numMaterials > 4)
									{
										$colLimit	= 4;
										$sMaterials	= '<table class="tblStd" cellspacing="0" cellpadding="0"><tr><td valign="top"><ul>';
									}
									else if ($numMaterials > 3 && ($otherDetails != '' || $sComment != '') )
									{
										$colLimit	= 3;
										$sMaterials	= '<table class="tblStd" cellspacing="0" cellpadding="0"><tr><td valign="top"><ul>';
									}
									else
									{
										$colLimit	= -1;
										$sMaterials	= "<ul>";
									}
									
									$iCol = 0;
									while ($recMaterial = mysql_fetch_array($rsMaterials))
									{
										if ($iCol == $colLimit)
										{
											$sMaterials	.= '</ul></td><td valign="top"><ul>';
											$iCol = 0;
										}
										$sMaterials .= "<li>".$recMaterial['Name']."</li>";
										$iCol++;
									}
									
									if ($colLimit > 0)
										$sMaterials .= '</ul></td></tr></table>';
									else
										$sMaterials .= '</ul>';
								}//if $numMaterials
									
								// Get the customer's name, if stored
								if ($CustID != null)
								{
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
											if ($CategoryID != 1)
											{
												?>
												&nbsp;&nbsp;&euro; <?=number_format($TotalCost, 2)?>
												<?php
											}
											?>
											</h2>
										</td>
									</tr>
									<tr>
										<td valign="top" width="40%">
											<table class="tblStdFullCentre">
												<?php
												// Get default product image
												if ($DefaultImageID == null)
												{
													$Filepath		= "images/img_not_avail_300.gif";
												}
												else
												{
													$qDefImage		= "select * from ProductImage where ProdImageID = ".$DefaultImageID." limit 1";
													$rsDefImage		= mysql_query($qDefImage, $cnRuby);
													$recDefImage	= mysql_fetch_array($rsDefImage);
													$ProdImageID	= $recDefImage['ProdImageID'];
													$Filepath		= $recDefImage['Filepath'];
												}
												
												// Get all product images (thumbnails)
												if ($DefaultImageID != null)
												{
													$qImages	= "select * from ProductImage where ProdID = ".$g_iProdID." order by ProdImageID limit 3";
													$rsImages	= mysql_query($qImages, $cnRuby);
													$iCount		= 1;
													?>
													<tr>
														<?php
														while ($recImage = mysql_fetch_array($rsImages))
														{
															$ProdImageID	= $recImage['ProdImageID'];
															$Filepath		= $recImage['Filepath'];
															// The following code re: img_not_avail is prob not needed!
															if (is_numeric( stripos($Filepath,"img_not_avail") ))
																$FilepathThumb	= str_replace("images/img_not_avail.gif", "images/thumbs/img_not_avail_90.gif", $Filepath);
															else
																$FilepathThumb	= str_replace("images/", "images/thumbs/", $Filepath);
															?>
															<td align="center">
																<div style="width:90px; height:90px;">
																	<a href="gallery.php?pid=<?=$g_iProdID?>">
																		<img src="<?=$FilepathThumb?>" alt="More images" border="0" />
																	</a>
																</div>
															</td>
															<?php
															if ($iCount == 3)
															{
																?></tr><tr><?php
																$iCount = 1;
															}
															else
															{
																$iCount++;
															}
														}
													}
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
												}//if // stocking toppers
												if ($ProductTypeID == 5)	// eye-patch
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
												<!-- Cost and description -->
												<?php
												if ($CategoryID != 1)
												{
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
												?>
												<tr><td colspan="2" valign="top"><?=$Description?></td></tr>
								
												<!-- Bespoke - get in touch -->
												<?php
												if ($CategoryID == 1)
												{
													?>
													<tr>
														<td colspan="2" valign="top" align="center">
															<a href="mailto:info@rubydemure.com">To get your own bespoke accessories, email Ruby... click here!</a>
														</td>
													</tr>
													<?php
												}
												?>
								
												<!-- Other details -->
												<?php
												if ($CategoryID == 1)
												{
													?><tr><td>Made For:</td><td><?=$Customer?></td></tr><?php
												}
												else
												{
													?><tr><td>Collection:</td><td><?=$Collection?></td></tr><?php
												}
												?>
												<tr>
													<td valign="top">Basic Details:</td>
													<td>
														<?php
														echo $Shape;
														if ($ShapeID != 1 && $ShapeID != 5 && $ShapeID != 10) {
															echo '-shaped';
														}
														echo '&nbsp;';
														if ($HasTassel == 1)
														{
															?>tasseled <?php
														}
														?>
														<?=strtolower($Type)?> (<?=strtolower($Category)?>)
														in <?=$sColours?>
													</td>
												</tr>
								
												<?php
												if ($sMaterials != "")
												{
													?><tr><td valign="top">Materials:</td><td><?=$sMaterials?></td></tr><?php
												}
												?>
								
												<?php
												if ($CategoryID != 1 && $sSizes != "")
												{
													?><tr><td>Sizes Available:</td><td><?=$sSizes?></td></tr><?php
												}
												?>
								
												<?php
												if ($sComment != "")
												{
													?><tr><td valign="top">Testimonials:</td><td><?=$sComment?></td></tr><?php
												}
												?>
								
												<tr><td colspan="2"><?=$otherDetails?></td></tr>

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
	include("Include/footer.php");

}//if $g_iProdID

if ($cnRuby)
	mysql_close($cnRuby);
?>

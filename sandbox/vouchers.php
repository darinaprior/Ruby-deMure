<?php
$sPageTitle		= 'Gift Vouchers';
$sPageKeywords	= 'gift vouchers, vouchers, credit';
$bModal = true;	// set up the modal window
require_once "Include/connection.php";
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
					<tr><td><br/></td></tr>
					<tr>
						<td valign="top">
							<table class="tblBreadcrumbs">
								<tr>
									<td colspan="2">
										You are here:&nbsp;
										<a href="/">Home</a> &gt;
										Gift Vouchers
									</td>
								</tr>
							</table>
							<table class="tblStdFull" cellspacing="0" cellpadding="0">
								<tr><td colspan="2" class="tdHeading">Gift Vouchers</td></tr>
								<tr>
									<td>
										<br/>Below are the details of the vouchers I currently have on offer.
										<p/>Consultations may take place in person if the customer is based in Dublin,
										<br/>or else over the phone or by email.
										
										<table class="tblStd" cellspacing="0" cellpadding="3" style="margin-left:50px;">
											<tr><td>&nbsp;</td></tr>
											<?php
											// Get current vouchers on offer
											$qVoucher	= "select * from Voucher inner join VoucherType on Voucher.TypeID = VoucherType.TypeID where Available = 1 order by Priority desc";
											$rsVoucher	= mysql_query($qVoucher, $cnRuby);
											while ($recVoucher = mysql_fetch_array($rsVoucher))
											{
												$voucherId	= $recVoucher['VoucherID'];
												$type		= $recVoucher['Type'];
												$name		= $recVoucher['Name'];
												$price		= $recVoucher['Price'];
												$image		= $recVoucher['ImageFileName'];
												?>
												<tr>
													<td><?php echo '"'.$name.'" '.$type; ?></td>
													<td><?php echo $price; ?></td>
													<td>
														<a class='modalLink' href="#colorbox_<?php echo $voucherId; ?>">
															View details &raquo;
														</a>
														<div style='display:none'>
															<div class="cboxInner" id="colorbox_<?php echo $voucherId; ?>">
																<table class="tblStd" cellspacing="0" cellpadding="3">
																	<?php
																	// Name and price
																	echo '<tr><td class="tdHeading">'.$name.' ('.$price.')</td></tr>';
																	
																	// What's included?
																	$qContents	= "select * from VoucherContents where VoucherId = ".$voucherId;
																	$rsContents	= mysql_query($qContents, $cnRuby);
																	echo '<tr><td><ul>';
																	while ($recContents = mysql_fetch_array($rsContents))
																	{
																		echo '<li>'.$recContents['Details'].'</li>';
																	}//while $recContents
																	echo '</ul></td></tr>';
																	
																	// Other details?
																	$qContents	= "select * from VoucherOtherDetails where VoucherId = ".$voucherId;
																	$rsContents	= mysql_query($qContents, $cnRuby);
																	while ($recContents = mysql_fetch_array($rsContents))
																	{
																		echo '<tr><td>'.$recContents['Details'].'</td></tr>';
																	}//while $recContents
																	
																	// Image
																	if ($image)
																	{
																		?>
																		<tr>
																			<td><img src="images/vouchers/<?php echo $image; ?>" title="SAMPLE: <?php echo $name.' '.$type; ?>" width="592" height="280" border="0" /></td>
																		</tr>
																		<?php
																	}
																	else
																	{
																		?>
																		<tr>
																			<td><img src="" width="592" height="10" border="0" /></td>
																		</tr>
																		<?php
																	}//if
																	?>
																</table>
															</div>
														</div>
													</td>
												</tr>
												<tr><td>&nbsp;</td></tr>
												<?php
											}//while $recVoucher
											?>
										</table>
									</td>									
									<td>
										<img src="images/vouchers/voucher_gift_sample_group.gif" />
									</td>
								</tr>
								<tr>
									<td>
										<a href="contact.php">
											To purchase a Gift Voucher, contact Ruby by clicking here...
										</a>
										<br/>If you have any questions, just hollar!
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
<?php include("Include/footer.php"); ?>
<?php
$g_iNumPerPage = 3;

if ($_GET['currpage'] != "")
	$g_iCurrPage = $_GET['currpage'];
else
	$g_iCurrPage = 1;
?>

<?php
$sPageTitle		= 'Bespoke work';
$sPageKeywords	= 'bespoke, tailored, personalised';
require_once 'Include/connection.php';
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
					<tr><td><br/></td></tr>
					<tr>
						<td valign="top">
							<table class="tblBreadcrumbs">
								<tr>
									<td colspan="3">
										You are here:&nbsp;
										<a href="/">Home</a> &gt;
										Bespoke
									</td>
								</tr>
							</table>
							<table class="tblStdFullCentre" cellspacing="0" cellpadding="0">
								<tr>
									<td align="center" width="50%" style="vertical-align:top;">
									
										<?php
										// SET UP PAGING
										$qNumProds		= "select COUNT(*) as NumProds from Product where CategoryID = 1 and Priority is not null";
										$rsNumProds		= mysql_query($qNumProds, $cnRuby);
										$recNumProds	= mysql_fetch_array($rsNumProds);
										$iNumProds		= $recNumProds['NumProds'];
										$iCurrRow		= 1;
										$iStartRow		= ($g_iCurrPage-1)*$g_iNumPerPage;
										?>
						
										<?php // SHOW RESULTS ?>
										<table class="tblStd" cellspacing="0" cellpadding="0">
											<tr><td><br/><br/></td></tr>
											<?php					
// Get current product details
$qProduct	= "select * from Product where CategoryID = 1 and Priority is not null order by Priority desc limit ".($g_iCurrPage * $g_iNumPerPage);								
$rsProduct	= mysql_query($qProduct, $cnRuby);

// Display results
while ($recProduct = mysql_fetch_array($rsProduct))
{
	// Skip to correct "page" of results and don't go past last record
	if ( ($iCurrRow > $iStartRow) && ($iCurrRow < $iNumProds) )	
	{
		$ProductID	= $recProduct['ProdID'];
		$Title		= $recProduct['Title'];
		$Date		= $recProduct['DateCreated'];
		$CustID		= $recProduct['CustID'];
		$Priority	= $recProduct['Priority'];
		?>
		<tr>
			<td style="vertical-align:middle;">
				<a href="product.php?pid=<?php echo $ProductID; ?>">
					<?php
					// Get first 2 images for this product
					$sql = 'SELECT DISTINCT filename
						FROM product_image
						WHERE product_id = ?
						AND filename IS NOT NULL
						ORDER BY 
							use_for_bespoke DESC, 
							IFNULL(priority, 1000), 
							id
						LIMIT 2';
					$stmt = $mysqli->prepare($sql);
					if ($stmt) {
						$stmt->bind_param('i', intval($ProductID));
						$stmt->bind_result($imageName);
						$stmt->execute();
						while ($stmt->fetch()) {
							// Get the full filepath
							$fullPath = getProductImagePath($ProductID, $imageName, 3/*small*/);
							echo '<img src="'.$fullPath.'" />';
						}
						$stmt->close();
					}//if $stmt
					?>
				</a>
			</td>
			<td  class="tdTitleBespoke">
				<a class="aNoBold" href="product.php?pid=<?php echo $ProductID; ?>">
					&nbsp;<?=$Title?>
				</a>
			</td>
		<tr/>
		<?php
	}	// (if ($iCurrRow > $iStartRow))
	$iCurrRow++;
}	// (while ($recProduct = mysql_fetch_array($rsProduct)))
											?>
										</table>
										
										<?php // SHOW PAGING ?>
										<table class="tblStd" width="50%">
											<tr style="font-weight:bold;">
												<?php
												// "Previous" block
												if ($g_iCurrPage > 1) {
													?>
													<td>
														<a href="bespoke.php?currpage=<?php echo ($g_iCurrPage-1); ?>">
															<table class="tblStd" cellspacing="5" cellpadding="0"><tr><td class="tdPagingOn">&laquo;</td></tr></table>
														</a>
													</td>
													<?php
												} else {
													?><td><table class="tblStd" cellspacing="5" cellpadding="0"><tr><td class="tdPagingOff">&laquo;</td></tr></table></td><?php
												}
												
												// Block for each page
												for ($i = 1; $i < ($iNumProds/$g_iNumPerPage)+1; $i++) {
													if ($i == $g_iCurrPage) {
														?><td><table class="tblStd" cellspacing="5" cellpadding="0"><tr><td class="tdPagingOff"><?=$i?></td></tr></table></td><?php
													} else {
														?>
														<td>
															<a href="bespoke.php?currpage=<?php echo $i; ?>">
																<table class="tblStd" cellspacing="5" cellpadding="0"><tr><td class="tdPagingOn"><?=$i?></td></tr></table>
															</a>
														</td>
														<?php
													}
												}
												echo '<td>';
												
												// "Next" block
												if ($g_iCurrPage < ($iNumProds/$g_iNumPerPage)) {
													?>
													<td>
														<a href="bespoke.php?currpage=<?php echo ($g_iCurrPage+1); ?>">
															<table class="tblStd" cellspacing="5" cellpadding="0"><tr><td class="tdPagingOn">&raquo;</td></tr></table>
														</a>
													</td>
													<?php
												} else {
													?><td><table class="tblStd" cellspacing="5" cellpadding="0"><tr><td class="tdPagingOff">&raquo;</td></tr></table></td><?php
												}
												?>
											</tr>
											<tr><td><br/></td></tr>
										</table>
										
									</td>
									
									<td width="100%">
										<table class="tblStdFullCentre" cellspacing="0" cellpadding="0">
											<tr>
												<td align="right" width="100%">
													<table width="295" cellspacing="0" cellpadding="0">
														<tr>
															<td style="
																background-image: url('/images/hand_bespoke.jpg');
																background-repeat:	no-repeat;
																background-color:	#0F050D;
																text-align:		left;
																width:		100%;
																height:		96px;
																font-size:	10px;
																padding-left:	10px;"
															>
																	<font size="2"><b>be&middot;spoke</b></font>
																	&nbsp;&nbsp;[bi-spohk]
																	<br/><i>-adjective</i>
																	<br/>British. (of clothes) made to
																	<br/>individual order; custom-made.
															</td>
														</tr>
													</table>
												</td>
												
											</tr>
											<tr><td><br/></td></tr>
											<tr><td colspan="3" class="tdHeading">Bespoke Work</td></tr>	
											<tr>
												<td colspan="3" align="left">
													<p/>The work I find most rewarding by far is my bespoke service - designing and creating tailored little works of art especially for someone. This is where I can not only let my creative self run wild, but I get to share the whole experience with my client.
													<p/>I've worked with a diverse range of clients - from stage performers, through blushing brides to the girl next door... and with men and women alike!
													<p/>To find out more about what's involved in getting your own bespoke items, <a href="contact.php">contact Ruby</a>.
												</td>
											</tr>
										</table>
									</td>
								</tr>
				
								<?php /* develop search facility here in future - by colour, style, materials etc. */ ?>
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
<?php require_once 'Include/footer.php'; ?>

<?php
if ($cnRuby)
	mysql_close($cnRuby);
?>
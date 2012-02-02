<?php
if ($_GET['pid'] != "")
	$g_iProdID = $_GET['pid'];
else
	$g_iProdID = -1;

$sPageTitle		= 'Image Gallery';
$sPageKeywords	= 'photos, images, gallery';
$bGallery		= true;
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

				<?php
				if ($g_iProdID == -1)
				{
					?>
					<br/><br/>
					<a href="/">No product ID supplied.  Please return to the home page by clicking here.</a>
					<?php
				}
				else
				{
					// Get product details from main [Product] table
					$qProduct		= "select * from Product where ProdID = ".$g_iProdID." limit 1";
					$rsProduct		= mysql_query($qProduct, $cnRuby);
					$recProduct		= mysql_fetch_array($rsProduct);
					$Title			= $recProduct['Title'];
					$CategoryID		= $recProduct['CategoryID'];
					$CollectionID	= $recProduct['CollectionID'];
			
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
								<a href="product.php?pid=<?=$g_iProdID?>"><?=$Title?></a> &gt;
								Gallery
							</td>
						</tr>
					</table>

					<table class="tblStdFull">
						<tr>
							<td>
								<ul id="pikame">
									<?php
									// Get all product images
									$sql = 'SELECT filepath, caption
										FROM product_image
										WHERE product_id = ?
										ORDER BY id';
									$stmt = $mysqli->prepare($sql);
									if ($stmt) {
										$stmt->bind_param('i', intval($g_iProdID));
										$stmt->bind_result($path, $caption);
										$stmt->execute();
										while ($stmt->fetch()) {
											// Get the full filepath
											$fullPath = getFullImagePath($path, $CategoryID);
											?>
											<li>
												<img src="<?php echo $fullPath; ?>"/>
												<?php
												if (isset($caption) && $caption != '') {
													echo '<span><div class="pikaCaption">'.$caption.'</div></span>';
												}
												?>
											</li>
											<?php
										}
										$stmt->close();
									}
									?>
								</ul>
							</td>
						</tr>
					</table>
					<?php
				}
				?>
				
				</div>
			</div>
			<!-- end of MAIN CONTENT -->
			
		</td>
		<td class="tdR4C3">&nbsp;</td>
	</tr>
			
</table>
<?php require_once 'Include/footer.php'; ?>

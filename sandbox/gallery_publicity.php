<?php
if ($_GET['pubid'] != "")
	$g_iPublicityID = $_GET['pubid'];
else
	$g_iPublicityID = -1;

$sPageTitle		= 'Publicity Gallery';
$sPageKeywords	= 'photos, images, gallery';
$bGallery		= true;
include("Include/connection.php");
include("Include/header.php");
?>

<table class="tblBody" cellpadding="0">
	<tr>
		<td class="tdR4C1">&nbsp;</td>
		<td class="tdR4C2">

			<!-- MAIN CONTENT - SCROLL IF NECESSARY -->
			<div class="dvMainScroll">
				<div class="dvMainPadding">

				<?php
				if ($g_iPublicityID == -1)
				{
					?>
					<br/><br/>
					<a href="/">No publicity ID supplied.  Please return to the home page by clicking here.</a>
					<?php
				}
				else
				{
					// Get publicity details from main [Publicity] table
					$qPublicity		= 'select
									p.PublicityID, p.Title, p.PublicityTypeID, pt.Type
								from Publicity p
								inner join PublicityType pt 
									on p.PublicityTypeID = pt.PublicityTypeID
								where PublicityID = '.$g_iPublicityID.'
								limit 1';
					$rsPublicity		= mysql_query($qPublicity, $cnRuby);
					$recPublicity		= mysql_fetch_array($rsPublicity);
					$publicityId		= $recPublicity['PublicityID'];
					$title			= $recPublicity['Title'];
					$typeId			= $recPublicity['PublicityTypeID'];
					$typeName		= $recPublicity['Type'];
					?>

					<table class="tblStd" cellspacing="0">
						<tr>
							<td colspan="3">
								You are here:&nbsp;
								<a href="/">Home</a> &gt;
								<a href="press.php">Publicity</a> &gt;
								<a href="press.php?typeid=<?php echo $typeId; ?>"><?php echo $typeName; ?></a> &gt;
								<?=$title?> Image Gallery
							</td>
						</tr>
					</table>

					<table class="tblStdFull">
						<tr>
							<td>
								<ul id="pikame">
									<?php
									// Get all publicity images
									$qImages	= "select * from PublicityImage where PublicityID = ".$g_iPublicityID." order by PublicityImageID";
									$rsImages	= mysql_query($qImages, $cnRuby);
								
									while ($recImage = mysql_fetch_array($rsImages))
									{
										$PublicityImageID	= $recImage['PublicityImageID'];
										$Filepath		= $recImage['Filepath'];
										$caption		= $recImage['Caption'];
										// The following code re: img_not_avail is prob not needed!
										if (is_numeric( stripos($Filepath,"img_not_avail") ))
											$FilepathThumb	= str_replace("images/img_not_avail.gif", "images/thumbs/img_not_avail_90.gif", $Filepath);
										else
											$FilepathThumb	= str_replace("images/", "images/thumbs/", $Filepath);
										?>
										<li>
											<img src="<?=$Filepath?>"/>
											<?php
											if ($caption)
												echo '<span><div class="pikaCaption">'.$caption.'</div></span>';
											?>
										</li>
										<?php
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
<?php include("Include/footer.php"); ?>

<?php
if ($_GET['catid'] != "")
	$linkCategoryId = $_GET['catid'];
else
	$linkCategoryId = -1;
	
include("Include/connection.php");

// Get category details from [LinkCategory] table
if ($linkCategoryId == -1)
{
	$category		= 'Community';
}
else
{
	$qCategory		= "select * from LinkCategory where LinkCategoryID = ".$linkCategoryId." and Priority is not null limit 1";
	$rsCategory		= mysql_query($qCategory, $cnRuby);
	$recCategory	= mysql_fetch_array($rsCategory);
	if ($recCategory['Category'])	// result found
	{
		$category	= $recCategory['Category'];
	}
	else
	{
		$linkCategoryId = -1;
		$category		= 'Community';
	}//if $recCategory['Category']
}//if $linkCategoryId

$sPageTitle		= $category;
$sPageKeywords	= $category.', Burlesque Community';
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
										<?php
										if ($linkCategoryId != -1)
											echo '<a href="links.php">Community</a> &gt;';
										echo $category
										?>
									</td>
								</tr>
							</table>
							<table class="tblStdFull" align="center" cellspacing="0" cellpadding="0">
								<tr><td colspan="2" class="tdHeading"><?php echo $category ?></td></tr>
								<tr>
									<td>
										<table class="tblStd" cellspacing="10">
											<tr>
												<td valign="top">
													<table class="tblStd">
														<?php
														if ($linkCategoryId == -1)
														{
															?>
															Burlesque is a big and beautiful community.  And it's really growing in Ireland.  In this section of the site are links to various members of the Burlesque community as well as others who I feel deserve a shout out!
															<?php
															// Get the link categories
															$qCats	= "select * from LinkCategory where Priority is not null order by Priority desc;";
															$rsCats	= mysql_query($qCats, $cnRuby);
															while ($recCats = mysql_fetch_array($rsCats))
															{
																$catId		= $recCats['LinkCategoryID'];
																$catName	= $recCats['Category'];
																$catDesc	= $recCats['Description'];
																
																// In each category, get up to 3 available link images
																$qLinks		= "select ImageFileName from Link where LinkCategoryID = ".$catId." and ImageFileName is not null order by rand() limit 3;";
																$rsLinks	= mysql_query($qLinks, $cnRuby);
																
																echo '<tr>';
																echo '<td rowspan="2"><a href="links.php?catid='.$catId.'">';
																while ($recLinks = mysql_fetch_array($rsLinks))
																{
																	echo '<img class="tt" src="images/links/'.$recLinks['ImageFileName'].'" title="'.$recLinks['Title'].'" border="0" width="40">';
																}//while $recLinks
																echo '</td>';
																echo '<td style="padding-top:10px;"><a href="links.php?catid='.$catId.'" class="aDark"><b>'.$catName.'</b></td>';
																echo '</tr>';
																echo '<tr><td><a href="links.php?catid='.$catId.'" class="aDark aNoBold">'.$catDesc.'</td></tr>';
															}//while $recCats
														}
														else
														{
															$qCat	= "select Description from LinkCategory where LinkCategoryID = ".$linkCategoryId;
															$rsCat	= mysql_query($qCat, $cnRuby);
															while ($recCat = mysql_fetch_array($rsCat))
															{
																echo '<tr><td colspan="3" align="center">'.$recCat['Description'].'<br/><br/></td></tr>';
															}
															
															$qLinks		= "select * from Link where LinkCategoryID = ".$linkCategoryId.";";
															$rsLinks	= mysql_query($qLinks, $cnRuby);
															while ($recLinks = mysql_fetch_array($rsLinks))
															{
																$hRef	= $recLinks['HRef'];
																$title	= $recLinks['Title'];
																$desc	= $recLinks['Description'];
																$image	= $recLinks['ImageFileName'];
																if ($linkCategoryId == 1)
																	echo '<tr><td><br/></td></tr>';	// creative circle - add extra space
																WriteLinkRow($hRef, $image, $title, $desc);
															}//while $recLinks
														}//if $linkCategoryId
														?>
														
													</table>
												</td>
											</tr>
										</table>
									</td>

									<td valign="top" align="right">
										<table class="tblStd" style="background-color:#ffffff;">
											<tr><td colspan="3">Follow Ruby...</td></tr>
											<tr>
												<td style="vertical-align:top;">
													<a href="http://www.twitter.com/rubydemure" target="_blank" class="aDark aNoBold">
														<img src="images/links/logo_twitter.jpg" title="Twitter" border="0">
													</a>
												</td>
												<td width="10">&nbsp;</td>
												<td>
													<a href="http://www.facebook.com/profile.php?id=551783815" target="_blank" class="aDark aNoBold">
														<img src="images/links/logo_facebook.jpg" title="Facebook" border="0">
													</a>
												</td>
											</tr>
											<tr>
												<td style="vertical-align:top;">
													<a href="http://www.bebo.com/RubydeMure" target="_blank" class="aDark aNoBold">
														<img src="images/links/logo_bebo.jpg" title="Bebo" border="0">
													</a>
												</td>
												<td width="10">&nbsp;</td>
												<td>
													<a href="http://www.myspace.com/rubydemure" target="_blank" class="aDark aNoBold">
														<img src="images/links/logo_MySpace.jpg" title="MySpace" border="0">
													</a>
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
<?php include("Include/footer.php"); ?>

<?php
function WriteLinkRow($sLink, $sLogo, $sTitle, $Text)
{
	?>
	<tr>
		<td style="vertical-align:top;">
			<a href="<?=$sLink?>" target="_blank" class="aDark aNoBold">
				<img src="images/links/<?=$sLogo?>" alt="<?=$sTitle?>" border="0" width="40">
			</a>
		</td>
		<td>
			<a href="<?=$sLink?>" target="_blank" class="aDark aNoBold">
				<b><?=$sTitle?></b>
				<br/><?=$Text?>
			</a>
		</td>
	</tr>
	<?php
}
?>

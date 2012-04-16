<?php
require_once 'Include/connection.php';	// connect to DB
require_once 'Include/functions.php';	// for some common functions

/** Get all the IDs and names we'll need from the database */
$bespokeProducts = getAllLiveProducts(1/*bespoke*/);
$collections = getAllLiveCollections();
$readymadeProducts = getAllLiveProducts(3/*readymade*/);
$productTypes = getAllLiveProductTypes();
$colours = getAllLiveColours();
$shapes = getAllLiveShapes();
$sizes = getAllLiveSizes();
$materials = getAllLiveMaterials();
$publicityTypes = getAllLivePublicityTypes();
$linkCategories = getAllLiveLinkCategories();

/**
 * Put them all into one big "sections" array along with
 * any other direct links we'll want e.g. "Home"
 */
$sections = array();
$sections[] = array(
	'Parent' => null,
	'Section' => 'Home',
	'SectionLink' => '/',
	'ItemLink' => null,
	'ImageType' => null,
	'Items' => null
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Bespoke Work',
	'SectionLink' => 'bespoke.php',
	'ItemLink' => 'product.php?pid=',
	'ImageType' => 0/*product images*/,
	'Items' => $bespokeProducts
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Ready-Made',
	'SectionLink' => 'off-the-rack.php',
	'ItemLink' => null,
	'ImageType' => null,
	'Items' => null
);
$sections[] = array(
	'Parent' => 'Ready-Made',
	'Section' => 'Collections',
	'SectionLink' => null,
	'ItemLink' => 'collection.php?cid=',
	'ImageType' => null,
	'Items' => $collections
);
$sections[] = array(
	'Parent' => 'Ready-Made',
	'Section' => 'Products',
	'SectionLink' => null,
	'ItemLink' => 'product.php?pid=',
	'ImageType' => 0/*product images*/,
	'Items' => $readymadeProducts
);
$sections[] = array(
	'Parent' => 'Ready-Made',
	'Section' => 'Product Types',
	'SectionLink' => null,
	'ItemLink' => 'browse.php?type=1&val=',
	'ImageType' => null,
	'Items' => $productTypes
);
$sections[] = array(
	'Parent' => 'Ready-Made',
	'Section' => 'Colours',
	'SectionLink' => null,
	'ItemLink' => 'browse.php?type=2&val=',
	'ImageType' => 1/*colour blocks*/,
	'Items' => $colours
);
$sections[] = array(
	'Parent' => 'Ready-Made',
	'Section' => 'Shapes',
	'SectionLink' => null,
	'ItemLink' => 'browse.php?type=3&val=',
	'ImageType' => null,
	'Items' => $shapes
);
$sections[] = array(
	'Parent' => 'Ready-Made',
	'Section' => 'Sizes',
	'SectionLink' => null,
	'ItemLink' => 'browse.php?type=4&val=',
	'ImageType' => null,
	'Items' => $sizes
);
$sections[] = array(
	'Parent' => 'Ready-Made',
	'Section' => 'Materials',
	'SectionLink' => null,
	'ItemLink' => 'browse.php?type=5&val=',
	'ImageType' => null,
	'Items' => $materials
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Gift Vouchers',
	'SectionLink' => 'vouchers.php',
	'ItemLink' => null,
	'ImageType' => null,
	'Items' => null
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Publicity',
	'SectionLink' => 'press.php',
	'ItemLink' => 'press.php?typeid=',
	'ImageType' => null,
	'Items' => $publicityTypes
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Links',
	'SectionLink' => 'links.php',
	'ItemLink' => 'links.php?catid=',
	'ImageType' => null,
	'Items' => $linkCategories
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Frequently Asked Questions',
	'SectionLink' => 'faq.php',
	'ItemLink' => null,
	'ImageType' => null,
	'Items' => null
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Testimonials',
	'SectionLink' => 'testimonials.php',
	'ItemLink' => null,
	'ImageType' => null,
	'Items' => null
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Sizing Charts',
	'SectionLink' => 'sizing.php',
	'ItemLink' => null,
	'ImageType' => null,
	'Items' => null
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'About Ruby',
	'SectionLink' => 'about.php',
	'ItemLink' => null,
	'ImageType' => null,
	'Items' => null
);
$sections[] = array(
	'Parent' => null,
	'Section' => 'Contact',
	'SectionLink' => 'contact.php',
	'ItemLink' => null,
	'ImageType' => null,
	'Items' => null
);

/** Start rendering the page */
$sPageTitle = 'Sitemap';
$sPageKeywords = 'sitemap';
include("Include/header.php");

/** Add the jQuery for hiding and showing items */
?>
<script type="text/javascript">
	<!--
	$jq(document).ready(function(){
		hideEverything();
	});
	
	function hideEverything()
	{
		// Hide all the expandable sections
		$jq("div[id*=section_]").hide();
	}
	function showHideSection(sectionId)
	{
		var isHidden = $jq("div[id*=section_"+sectionId+"_item_]:first").is(":hidden");
		if (isHidden) {
			// Show the section
			$jq("div[id*=section_"+sectionId+"_item_]").show();
			// Change to "minus" image so user can collapse section later
			$jq("img[id=expand_"+sectionId+"]").attr({ src:'/images/buttons/minus_dark.gif' });
		} else {
			// Hide the section
			$jq("div[id*=section_"+sectionId+"_item_]").hide();
			// Change to "plus" image so user can expand section later
			$jq("img[id=expand_"+sectionId+"]").attr({ src:'/images/buttons/plus_dark.gif' });
		}
	}
	-->
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
					<table class="tblStdFull" cellspacing="0" cellpadding="0">
						<tr>
							<td class="tdHeading">Sitemap</td>
						</tr>
						<tr>
							<td>
							<?php
							
							/** Loop through all the sections we gathered */
							foreach ($sections as $sectionId => $sectionDetails) {
								$parent = $sectionDetails['Parent'];
								$sectionName = $sectionDetails['Section'];
								$sectionLink = $sectionDetails['SectionLink'];
								$itemLink = $sectionDetails['ItemLink'];
								$imageType = $sectionDetails['ImageType'];
								$items = $sectionDetails['Items'];
								
								/** Is this a subsection? */
								$isSubSection = FALSE;
								if (isset($parent) && $parent != '') {
									$isSubSection = TRUE;
								}
								
								/** Does this section have sub-items in it? */
								$hasItems = FALSE;
								if (is_array($items) && COUNT($items) > 0) {
									$hasItems = TRUE;
								}
								
								/** Do we want to show item images? */
								$showImages = FALSE;
								if (isset($imageType)) {
									$showImages = TRUE;
								}
								
								/** Start SECTION or SUBSECTION DIV */
								if ($isSubSection) {
									echo '<div class="sitemap_subsection">';
								} else {
									echo '<div class="sitemap_section">';
								}
								
								/** If there are sub-items, allow expand/collapse */
								if ($hasItems) {
									?>
									<img class="expand" 
										id="expand_<?php echo $sectionId; ?>" 
										src="/images/buttons/plus_dark.gif" 
										onClick="showHideSection(<?php echo $sectionId; ?>);" />
									<?php
								}
								
								/** Print SECTION NAME (with/without hyperlink) */
								if (isset($sectionLink) && $sectionLink != '') {
									echo '<a href="'.$sectionLink.'">'
										.$sectionName
										.'</a><br/>';
								} else {
									echo $sectionName;
								}
								
								/** Loop through items, if there are any */
								if ($hasItems) {
									foreach ($items as $itemId => $itemName) {
										
										/** Start ITEM DIV */
										echo '<div id="section_'.$sectionId.'_item_'.$itemId.'" class="sitemap_item">';
										/** Print ITEM IMAGE if necessary */
										if ($showImages) {
$image = '';
switch($imageType) {
	case 0:
		// product images
		$path = getSingleImageForProduct($itemId, 4/*thumb*/);
		if ($path) {
			$image = '<img src="'.$path.'" width="50" height="50" />';
		}
		break;
	case 1:
		// colour blocks
		$hex = getColourHex($itemId);
		if ($hex) {
			$image = '<table style="display:inline;">
					<tr>
						<td style="background-color:#'.$hex.'; width:20px; height:20px;">
							&nbsp;
						</td>
					</tr>
				</table>';
		}
		break;
}//switch
echo $image;
										}//if $imageType
										
										/** Print ITEM NAME (with/without hyperlink) */
										echo '<a href="'.$itemLink.$itemId.'">'
											.$itemName
											.'</a><br/>';
										
										/** Finish ITEM DIV */
										echo '</div>';
									}//foreach
								}//if $hasItems
								
								/** Finish SECTION or SUBSECTION DIV */
								echo '</div>';
							}
							?>
							</td>
						</tr>
					</table>
	</table>

	</div><!--dvMainPadding-->
</div><!--dvMainScroll-->
<!-- end of MAIN CONTENT -->

</td>
<td class="tdR4C3">&nbsp;</td>
</tr>
</table>
<?php
require_once 'Include/footer.php';
?>
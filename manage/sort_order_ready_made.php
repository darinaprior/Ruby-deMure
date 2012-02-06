<?php
if ($_POST)	// the user just submitted
{
	// Split the new order out into array
	$idsInNewOrder = array();
	if (isset($_POST['order_ids']))
	{
		$idsInNewOrder = explode(',', $_POST['order_ids']);
	}
	// We need to start with the LAST element and
	// set that to 0.  Easiest to reverse the array
	$idsInNewOrder = array_reverse($idsInNewOrder);

	// Open DB connection
	require_once 'include/connection.php';

	// Loop through the IDs, setting the priority for each
	$priority = 0;	// 0 = lowest priority
	foreach ($idsInNewOrder as $id)
	{
		// Update the product in the database
		$qUpdate = 'update Product
			set Priority = '.$priority.'
			where ProdID = '.$id;
		mysql_query($qUpdate) or die(mysql_error());

		// Increment priority
		$priority++;
	}

	// Close DB connection
	if ($cnRuby)
		mysql_close($cnRuby);

	echo '<br/><br/>New order saved.';
	echo '<br/><a href="'.$_SERVER['PHP_SELF'].'">Click here to view again</a>';
	echo '<br/><a href="index_sort.php">Click here to return to the Sort Orders Index</a>';
}
else	// not POST
{
$colId = $_REQUEST['collectionid'];
	require_once 'include/check_login.php';
	?>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Ruby deMure - Manage Ready-Made Sort Order</title>
		<meta name="DESCRIPTION" content="" />
		<meta name="KEYWORDS" content="" />
		<link rel="STYLESHEET" href="css/styles.css">
		<link type="text/css" href="http://www.rubydemure.com/css/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
		<link type="text/css" href="demos.css" rel="stylesheet" />
		<script type="text/javascript" src="http://www.rubydemure.com/js/jquery.1.3.2.min.js"></script>
		<script src="http://www.rubydemure.com/js/jquery_ui/jquery-ui-1.8.1.custom.min.js"></script>
		<style type="text/css">
			.column { width: 250px; padding-top: 20px; }
			.portlet { margin: 0 1em 1em 0; }
			.portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
			.portlet-header .ui-icon { float: right; }
			.portlet-content { padding: 0.4em; }
			.ui-sortable-placeholder * { visibility: hidden; }
		</style>
		<script type="text/javascript">
			$(function() {
				// When user changes order, store the new order in hidden variable
				$('.column').sortable({
					update: function() {
					
						var order_ids = $('#sortList').sortable('toArray').toString();
						$('#order_ids').val(order_ids);
					}
				});

				// CSS stuff
				$(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
					.find(".portlet-header")
						.addClass("ui-widget-header ui-corner-all")
						.prepend('<span class="ui-icon ui-icon-minusthick"></span>')
						.end()
					.find(".portlet-content");
				$(".portlet-header .ui-icon").click(function() {
					$(this).toggleClass("ui-icon-minusthick").toggleClass("ui-icon-plusthick");
					$(this).parents(".portlet:first").find(".portlet-content").toggle();
				});
				$(".column").disableSelection();
			});
		</script>
	</head>

	<body>

	<?php echo $headerTable; ?>

	<!-- centering table -->
	<table style="width:100%;">
	<tr>
	<td align="center">

		<div>
			<br/><a href="index_sort.php">Back to Sort Order Index &raquo;</a>
		</div>
		<?php
		// Open DB connection
		require_once 'include/connection.php';

		// Get collection details from main [Collection] table
		$qCollection = "select
				CollectionID, Title
			from Collection
			where Current = 1
			order by Title";
		$rsCollection	= mysql_query($qCollection, $cnRuby);
	
		// Show results to user in a drop-down list
?>
<div>
<form method="get">
<select id="collectionid" name="collectionid" onChange="submit();">
<option value="0">-- Please select one --</option>
<?php
		while ($collection = mysql_fetch_array($rsCollection)) {
?>
<option value="<?php echo $collection['CollectionID']; ?>"
<?php
if ($collection['CollectionID'] == $colId) {
echo ' selected';
}
?>
>
<?php echo $collection['Title']; ?>
</option>
<?php
		}//while
?>
</select>
</form>
</div>
<?php

if ($colId > 0) {

		// Get product details from main [Product] table
		$qProduct = "select
				p.ProdID, pt.Description as ProdTypeName,
				p.Title, p.Priority, pi.Filepath
			from Product p
			left outer join ProductType pt on p.ProductTypeID = pt.ProdTypeID
			left outer join ProductImage pi on p.DefaultImageID = pi.ProdImageID
			where p.CollectionID = ".$colId."
and p.CategoryID = 3 /* ready-made */
			order by p.Priority desc, p.Title";
		$rsProduct	= mysql_query($qProduct, $cnRuby);
	
		// Loop through results and store in an array
		$products = array();
		while ($product = mysql_fetch_array($rsProduct)) {
			$products[] = array(
				'ID'		=> $product['ProdID'],
				'ProductType'	=> $product['ProdTypeName'],
				'Title'		=> $product['Title'],
				'Priority'	=> $product['Priority'],
				'DefaultImage'	=> $product['Filepath']
			);
		}//while

		// Close DB connection
		if ($cnRuby)
			mysql_close($cnRuby);

		// Display the results as sortable divs
		?>
		<div class="column" id="sortList">
			<?php
			foreach ($products as $product)
			{	
				// The default image
				$Filepath = $product['DefaultImage'];

				// We want the thumbnail
				if (is_numeric( stripos($Filepath,"img_not_avail") ))
					$FilepathThumb	= str_replace("images/img_not_avail.gif", "images/thumbs/img_not_avail_90.gif", $Filepath);
				else
					$FilepathThumb	= str_replace("images/", "images/thumbs/", $Filepath);

				// Link to them in the main site (not manage subdomain)
				$FilepathThumb = 'http://rubydemure.com/'.$FilepathThumb;
				?>

				<div class="portlet" id="<?php echo $product['ID']; ?>">
					<div class="portlet-header"><?php echo strtoupper($product['Title']) ?></div>
					<div class="portlet-content">
						<img src="<? echo $FilepathThumb; ?>" alt="IMAGE NOT FOUND" border="0" style="float:left;" />
						<?php
						echo 'ID: '.$product['ID'];
						echo '<br/>Type: '.$product['ProductType'];
						echo '<br/>Current Priority: '.$product['Priority'];
						?>
					</div>
				</div>
				<?php

			}//while
			?>
		</div>
		<div>
			<form name="frm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<input type="hidden" id="order_ids" name="order_ids" value="" />
				<input type="submit" value="Save New Order" />
			</form>
		</div>
<?php
}//if $colId
?>

	<!-- end of centering table -->	
	</td>
	</tr>
	</table>

	</body>
	</html>

	<script type="text/javascript">
		// Store the starting order in hidden variable
		$(document).ready(function() {
			var order_ids = $('#sortList').sortable('toArray').toString();
			$('#order_ids').val(order_ids);
		});
	</script>
	<?php
}//if $_POST
?>

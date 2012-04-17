<?php
require_once 'include/check_login.php';
require_once 'include/functions.php';

// Open DB connection
require_once 'include/connection.php';

// Get the product id
$productId = $_REQUEST['pid'];

// the user just submitted
$error = '';
if ($_POST) {
	// Split the new order out into array
	$idsInNewOrder = array();
	if (isset($_POST['order_ids']))
	{
		$idsInNewOrder = explode(',', $_POST['order_ids']);
	}
	
	// Loop through the IDs, setting the priority for each
	$priority = 0;	// 0 = lowest priority
	foreach ($idsInNewOrder as $id) {
		// Update the product image in the database
		$sql = 'UPDATE product_image
			SET priority = ?
			WHERE id = ?';
		$stmt = $mysqli->prepare($sql);
		if ($stmt) {
			$stmt->bind_param(
				'ii',
				$priority,
				$id
			);
			if (!$stmt->execute()) {
				$error .= '<br/>ERROR: could not update record in database - '.$mysqli->error;
			}
		} else {
			$error .= '<br/>ERROR: could not prepare MySQL statement - '.$mysqli->error;
		}//if $stmt

		// Increment priority
		$priority++;
	}

	if (isset($error) && $error != '') {
		echo '<font color="red">'.$error;
	} else {
		echo '<br/><br/>New order saved.';
		echo '<br/><a href="'.$_SERVER['PHP_SELF'].'?pid='.$productId.'">Click here to view again</a>';
		echo '<br/><a href="index_sort.php">Click here to return to the Sort Orders Index</a>';
	}//if $error
	
} else {
	// not POST
	
	// Get all products
	$products = array();
	$sql = 'SELECT
			pt.Description,
			p.ProdID,
			p.Title
		FROM Product p
		INNER JOIN ProductType pt ON p.ProductTypeID = pt.ProdTypeID
		ORDER BY pt.ProdTypeID, p.ProdID';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_result($type, $id, $name);
		$stmt->execute();
		while ($stmt->fetch()) {
			$products[] = array(
				'Type' => $type,
				'Id' => $id,
				'Name' => $name,
			);
		}//while
	}//if $stmt
	
	// Get name and type of this product
	$productType = '';
	$productName = '';
	$sql = 'SELECT
			pt.Description,
			p.Title
		FROM Product p
		INNER JOIN ProductType pt ON p.ProductTypeID = pt.ProdTypeID
		WHERE p.ProdID = ?';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_param('i', $productId);
		$stmt->bind_result($productType, $productName);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
	}//if $stmt
	
	// Get all the images for this product
	$images = array();
	$sql = 'SELECT DISTINCT id, filename, priority
		FROM product_image
		WHERE product_id = ?
		AND filename IS NOT NULL
		ORDER BY IFNULL(priority, 1000), id';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_param('i', intval($productId));
		$stmt->bind_result($id, $name, $priority);
		$stmt->execute();
		while ($stmt->fetch()) {
			// Get the filepath for the THUMBNAIL image
			$images[] = array(
				'Id' => $id,
				'Filename' => $name,
				'Path' => getProductImagePath($productId, $name, 4/*thumb*/),
				'Priority' => $priority,
			);
		}//while
		$stmt->close();
	}//if
	?>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Ruby deMure - Manage Product Images Sort Order</title>
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
	<div>
		<br/><a href="index_sort.php">Back to Sort Order Index &raquo;</a>
	</div>
	
	<!-- product select -->
	<table width="100%">
		<tr>
			<td colspan="9" align="center" style="font-size:12px;">
				<form method="get">
					<br/>
					<select name="pid" onClick="submit();">
						<option value="0">-- Select product --</option>
						<?php
						foreach ($products as $product) {
							echo '<option value="'.$product['Id'].'"';
							if ($productId == $product['Id']) {
								echo ' selected';
							}
							echo '>';
							echo strtoupper($product['Type']);
							echo ': '.$product['Id'];
							echo ' - '.$product['Name'];
							echo '</option>';
						}//foreach
						?>
					</select>
				</form>
			</td>
		</tr>		
	</table>
	
	<!-- sort form -->
	<table style="width:100%;">
	<tr>
	<td align="center">
		
		<h2>Reorder images for <?php echo $productName; ?> (<?php echo $productType; ?>)</h2>
		
		<?php
		// Display the images as sortable divs
		?>
		<div class="column" id="sortList">
			<?php
			foreach ($images as $image) {
				$id = $image['Id'];
				$filename = $image['Filename'];
				$path = $image['Path'];
				$priority = $image['Priority'];
			
				// Link to them in the main site (not manage subdomain)
				$url = 'http://rubydemure.com/'.$path;
				?>

				<div class="portlet" id="<?php echo $id; ?>">
					<div class="portlet-header"><?php echo $filename; ?></div>
					<div class="portlet-content">
						<img src="<?php echo $url; ?>" alt="IMAGE NOT FOUND" border="0" style="float:left;" />
						<?php
						echo 'ID: '.$id;
						echo '<br/>Current Priority: '.$priority;
						?>
					</div>
				</div>
				<?php

			}//while
			?>
		</div>
		<div>
			<form name="frm" method="post">
				<input type="hidden" id="order_ids" name="order_ids" value="" />
				<input type="submit" value="Save New Order" />
			</form>
		</div>

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

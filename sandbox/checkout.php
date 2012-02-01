<?php
$sPageTitle = 'Checkout';
$sPageKeywords = 'checkout, cart, basket, pay, buy';
include("Include/header.php");
?>

<script type="text/javascript" src="js/simpleCart.min.js"></script>
<script type="text/javascript">
	simpleCart.currency = EUR;
	simpleCart.checkoutTo = PayPal;
	simpleCart.email = "admin@rubydemure.com";
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

							<table class="tblStd" cellspacing="0">
								<tr>
									<td colspan="3">
										You are here:&nbsp;
										<a href="/">Home</a> &gt;
										Checkout

<right>											
Your shopping cart: <span class="simpleCart_quantity"></span> items. Total <div class="simpleCart_total"></div>
<a href="checkout.php">Checkout</a>
<a href="javascript:;" class="simpleCart_empty">Empty</a>
</right>


									</td>
								</tr>
							</table>

							<table class="tblStdFullCentre" cellspacing="0" cellpadding="0">
								<tr>
									<td colspan="2" align="center">
										<div class="simpleCart_items"></div>
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
?>

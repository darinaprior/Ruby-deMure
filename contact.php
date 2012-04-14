<?php
$sPageTitle = 'Contact Ruby';
$sPageKeywords = 'contact, phone, email';
include("Include/header.php");

/** Get contact details from file */
include("Include/contact_details.php");
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
								Contact Details
							</td>
						</tr>
					</table>
					<table class="tblStdFull" cellspacing="0" cellpadding="0">
						<tr>
							<td colspan="3" class="tdHeading">
								Need to Contact Ruby?
							</td>
						</tr>
						<tr><td><br/></td></tr>
						<tr>
							<td colspan="3">
								<p/>Please contact Ruby using one of the emails below, or at the <b>telephone number <?php echo $g_sPhoneMain; ?></b>
								<p/>
								<table class="tblStd" cellpadding="5">
									<tr>
										<td><b/>For product information and sales:</b></td>
										<td>
											<?php
											echo '<a href="mailto:';
											echo $g_sEmailMain;
											echo '">';
											echo $g_sEmailMain;
											echo '</a>';
											?>
										</td>
									</tr>
									<tr>
										<td><b/>For customer service:</b></td>
										<td>
											<?php
											echo '<a href="mailto:';
											echo $g_sEmailCustomerService;
											echo '">';
											echo $g_sEmailCustomerService;
											echo '</a>';
											?>
										</td>
									</tr>
									<tr>
										<td><b/>For comments and feedback on products:</b></td>
										<td>
											<?php
											echo '<a href="mailto:';
											echo $g_sEmailComments;
											echo '">';
											echo $g_sEmailComments;
											echo '</a>';
											?>
										</td>
									</tr>
									<tr>
										<td><b/>For feedback on the website and for technical issues:</b></td>
										<td>
											<?php
											echo '<a href="mailto:';
											echo $g_sEmailAdmin;
											echo '">';
											echo $g_sEmailAdmin;
											echo '</a>';
											?>
										</td>
									</tr>
									<tr>
										<td><b/>For marketing and resellers:</b></td>
										<td>
											<?php
											echo '<a href="mailto:';
											echo $g_sEmailMarketing;
											echo '">';
											echo $g_sEmailMarketing;
											echo '</a>';
											?>
										</td>
									</tr>
									<tr>
										<td><b/>Miscellaneous:</b></td>
										<td>
											<?php
											echo '<a href="mailto:';
											echo $g_sEmailMain;
											echo '">';
											echo $g_sEmailMain;
											echo '</a>';
											?>
										</td>
									</tr>
								</table>
								<br/><br/>
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
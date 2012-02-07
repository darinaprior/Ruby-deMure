<?php
session_start();
if ( !isset($_SESSION["rdem_username"]) )
{
	header("location:login.php");
}
else
{
	$loginName = $_SESSION["rdem_first_name"]." ".$_SESSION["rdem_last_name"];
	$headerTable = '<table width="100%" style="border:solid 1px #0F050D; 
			background-color:#F1C9E1;" cellpadding="3" cellspacing="1">
		<tr>
			<td>
				<a href="index.php">Products</a>
			</td>
			<td>
				<a href="index_collection.php">Collections</a>
			</td>
			<td>
				<a href="index_customer.php">Customers</a>
			</td>
			<td>
				<a href="index_sort.php">Sort Orders</a>
			</td>
			<td>
				<a href="index_images.php">Images</a>
			</td>
			<td>&nbsp;</td>
			<td>
				<a href="edit_website_mode.php"><font color="red">CHANGE WEBSITE MODE</font></a>
			</td>
			<td align="right">
				You are logged in as '.$loginName.'
				&nbsp;<a href="logout.php">Logout</a>
			</td>
		</tr>
	</table>';
}

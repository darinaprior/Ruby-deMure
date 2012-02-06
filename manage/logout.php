<?
session_start();
if ( session_destroy() )
	header("location:login.php");
else
	echo "<br/>Error logging out!";

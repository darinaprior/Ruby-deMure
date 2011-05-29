<?php
$read = file_get_contents("navigation.php");
//$read = preg_replace('/\s\t\n\r\f\s+/', '', $read);

// Remove new lines and tabs, but leave spaces in place
$read = str_replace('	', '', $read);	
$read = str_replace('
', '', $read);

$write	= fopen("navigation_stripped.php","w");
fwrite($write, $read);
fclose($write);

echo "File successfully updated.";
?>

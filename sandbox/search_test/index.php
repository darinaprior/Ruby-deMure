<?php
require_once 'config.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title>search test</title>
        <link href="<?php echo STATIC_SERVER?>www/css/wwwprimary.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript" src="js/script.js"></script>
	</head>
<body>
<div>
	<form id="searchform">
		<div>
			Search <input type="text" size="30" value="" id="inputString" onkeyup="lookup(this.value);" />
		</div>
		<div id="suggestions"></div>
	</form>
</div>
</body>
</html>
<?php
require_once 'include/connection.php';

if ($_POST) {
	$error = '';
	$sql = 'SELECT
			username,
			password,
			first_name,
			last_name
		FROM manager
		INNER JOIN manager_login
		WHERE username = ?';
	$stmt = $mysqli->prepare($sql);
	if ($stmt) {
		$stmt->bind_param(
			's',
			$mysqli->real_escape_string($_POST['username'])
		);
		$stmt->bind_result($user, $pass, $firstName, $lastName);
		$stmt->execute();
		$stmt->fetch();
		if ($user == '') {
			$error = 'The username you entered was not found';
		} else {
			if ($pass != md5($_POST['password'])) {
				$error = 'The password you entered does not match the username';
			} else {
				// Save details to session and redirect to index
				session_start();
				$_SESSION["rdem_username"] = $user;
				$_SESSION["rdem_first_name"] = $firstName;
				$_SESSION["rdem_last_name"] = $lastName;
				header("location:index.php");
			}
		}
	} else {
		$error = 'ERROR: failed to prepare statement';
	}//if $stmt

}//if POST
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Ruby deMure - Product Management Login</title>
	<meta name="DESCRIPTION" content="" />
	<meta name="KEYWORDS" content="" />
	<link rel="STYLESHEET" href="css/styles.css">
</head>

<body>

<!-- centering table -->
<table style="width:100%;">
<tr>
<td align="center">


<table>
	<?php
	if($error != '') {
		?>
		<tr>
			<td style="text-align:center; color:red; border:solid 1px red;">
				<?php echo $error; ?>
			</td>
		</tr>
		<tr><td><br/></td></tr>
		<?
	}
	?>
	<tr>
	<form name="frmLogin" method="post">
		<td align="center">
		<table style="border:solid 1px #0F050D; background-color:#F1C9E1;" 
			cellpadding="3" cellspacing="1">
			<tr>
				<td colspan="2" class="tdHeading">Manager Login</td>
			</tr>
			<tr>
				<td>Username:</td>
				<td>
					<input type="text" name="username" id="username">
				</td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="password" id="password"></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submit" value="Login">
				</td>
			</tr>
		</table>
		</td>
	</form>
	</tr>
</table>

<!-- end of centering table -->	
</td>
</tr>
</table>

</body>
</html>

<script language="javascript">
	document.getElementById("username").focus();
</script>
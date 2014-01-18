<?php
$base = "";
require_once $base."includes.php";
require_once $base."include/admin.php";

$admin = new Admin;

if (!$admin->checkRights())
{
	include $base."controlers/loginControler.php";
		
	$interface = new _Interface;
	$interface->setTitle("Login Page");
	$interface->addBodyAttribute("class", "login");
	echo $interface->getHeader();
	?>
		<div class="outer">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<fieldset class="inner">
					<legend class="bold">Login to admin panel</legend>
					<div class="padding5">
						<label>Login:</label> <input type="text" name="login" class="textbox" />
					</div>
					<div class="padding5">
						<label>Password:</label> <input type="password" name="password" class="textbox" /></br>
					</div>
					<?php echo $errorMsg; ?>
					<input type="submit" name="submit" value="Login" class="button" /><br>
				</fieldset>
			</form>
		</div><?php
	echo $interface->getFooter();
}
else
{
	if ($_GET['logout'] == "true")
	{
		$admin->clearSession();
		header("Location: ".$base."index.html");
	}
	else
		header("Location: ".$base."admin.php");
}

?>

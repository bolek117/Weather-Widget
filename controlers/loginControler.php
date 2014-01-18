<?

if (isset($_POST['submit']) && $_POST['submit'] == "Login") // if login request fetched
{
	if ($admin->checkCredentials($_POST['login'], $_POST['password']))
	{
		header("Location: ".$base."admin.php");
	}
	else
	{
		header("Location: ".$_SERVER['PHP_SELF']."?errno=1");
		die();
	}
}

$errorMsg = "<div class=\"error\">";
if (isset($_GET['errno']) && !empty($_GET['errno']))
{
	switch($_GET['errno'])
	{
		case 1:
			$errorMsg .= "Wrong login/password pair.";
		break;
		default:
			$errorMsg .= "Unknown error occured.";
	}
}
else
	$errorMsg .= "&nbsp;";
	
$errorMsg .= "</div>\n";

?>

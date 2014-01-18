<?php

if (!isset($admin) || !$admin->checkRights())
{
	header("Location: ../login.php");
	die("Authentication needed");
}
else
{
	if (isset($_POST['saveMappings']) && $_POST['saveMappings'] == "Save mappings")
	{
		if (!isNum($_POST['provider']))
		{
			header("Location: ".$_SERVER['PHP_SELF']."?errno=1");
			die();
		}
		else
		{
			$mapping = new Mapping($_POST['provider']);
			$mapping->updateMappings($_POST['mapping']);
		}
	}
	else if (isset($_POST['saveStates']) && $_POST['saveStates'] == "Save states")
	{
		$providers = new ProvidersList($_POST['country']);
		$provider = $providers->getProvider($_POST['provider']);

		if ($provider)
		{
			if (!isset($_POST['location']) || empty($_POST['location']))
				$_POST['location'] = array("");
				
			$result = $provider->updateLocationsAttributes($_POST['location']);
			if ($result != true)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?errno=1");
			die();
			}
		}
		else
		{	
			header("Location: ".$_SERVER['PHP_SELF']."?errno=2");
			die();
		}
			
	}
	else if (isset($_POST['addCountry']) && !empty($_POST['addCountry']))
	{
		$providers = new ProvidersList;
		$provider = $providers->getProvider($_POST['provider']);
		if ($provider)
		{
			$provider->loadLocationsFromService($_POST['newCountry']);
		}
		else
		{	
			header("Location: ".$_SERVER['PHP_SELF']."?errno=2");
			die();
		}
	}
	else if (isset($_POST['saveProvider']) && !empty($_POST['saveProvider']))
	{
		$providers = new ProvidersList;
		$provider = $providers->getProvider($_POST['provider']);
		if ($provider)
		{
			$provider->updateDetails($_POST['editDisplayedName'], $_POST['editUri']);
		}
		else
		{	
			header("Location: ".$_SERVER['PHP_SELF']."?errno=2");
			die();
		}
	}
	else if (isset($_POST['addProvider']) && !empty($_POST['addProvider']))
	{
		$providers = new ProvidersList;
		if (!empty($_POST['newName']) && !empty($_POST['newUri']))
			$providers->addProvider($_POST['newName'], $_POST['newUri']);
	}
	else if (isset($_POST['deleteProvider']) && !empty($_POST['deleteProvider']))
	{
		$providers = new ProvidersList;
		$provider = $providers->getProvider($_POST['provider']);
		if ($provider)
		{
			$providers->deleteProvider($provider->getId());
		}
		else
		{	
			header("Location: ".$_SERVER['PHP_SELF']."?errno=2");
			die();
		}
	}
	else if (isset($_POST['deleteCountry']) && !empty($_POST['deleteCountry']))
	{
		$providers = new ProvidersList;
		$provider = $providers->getProvider($_POST['provider']);
		if ($provider)
		{
			$provider->deleteCountry($_POST['country']);
		}
		else
		{	
			header("Location: ".$_SERVER['PHP_SELF']."?errno=2");
			die();
		}
	}
	else if (isset($_POST['setTimeout']) && !empty($_POST['setTimeout']))
	{
		$settings = new Settings;
		if (!$settings->setSoapTimeout($_POST['soapTimeout']))
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
				$errorMsg .= "Saving error occured.";
			break;
			case 2:
				$errorMsg .= "Provider not found.";
			break;
			default:
				$errorMsg .= "Unknown error occured.";
		}
	}
	else
		$errorMsg .= "&nbsp;";
		
	$errorMsg .= "</div>\n";
}

?>

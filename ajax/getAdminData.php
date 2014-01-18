<?php
$base = "../";
require_once $base."includes.php";
require_once $base."include/admin.php";

$result = null;
$admin = new Admin;

if (!$admin->checkRights())
{
	$result = "auth_needed";
}
else
{
	switch($_GET['request'])
	{
		case "getMappings":
		{
			$providers = new ProvidersList;
			$provider = $providers->getProvider($_GET['parameter']);
			if ($provider != null)
			{
				$result = $provider->getMappings();
			}
			else
				$result = "not_found";
		}
		break;
		case "getCitiesList":
		{
			$parameters = explode("|", $_GET['parameter']);

			$provider = $parameters[0];
			$country = $parameters[1];
			
			$providers = new ProvidersList($country);
			$provider = $providers->getProvider($provider);
			if ($provider != null)
			{
				$result = $provider->getCitiesList($country, true);
			}
			else
				$result = "not_found";
		}
		break;
		case "getProviderDetails":
		{
			$providers = new ProvidersList;
			$provider = $providers->getProvider($_GET['parameter']);

			if ($provider != null)
			{
				$result["name"] = $provider->getName();
				$result["uri"] = $provider->getUri();
			}
			else
				$result = "not_found";
		}
		break;
		case "getSoapTimeout":
		{
			$settings = new Settings;
			$result = array('soapTimeout' => $settings->getSoapTimeout());
		}
		break;
	}
}

header('Content-type: application/json');
echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);

?>

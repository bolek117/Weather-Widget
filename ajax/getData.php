<?php
$base = "../";
require_once $base."includes.php";

$result = null;

switch($_GET['request'])
{
	case "getProvidersList":
	{
		$providers = new ProvidersList;
		$result = $providers->getAllProviders();
	}
	break;
	case "getCountriesList":
	{
		$providers = new ProvidersList;
		$provider = $providers->getProvider($_GET['parameter']);
		if ($provider != null)
			$result = $provider->getCountriesList();
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
			$result[] = $provider->getCitiesList($country);
		}
		else
			$result = "not_found";
	}
	break;
	case "getWeather":
	{
		$parameters = explode("|", $_GET['parameter']);
		
		$providers = new ProvidersList;
		$provider = $providers->getProvider($parameters[0]);
		if ($provider != null)
		{
			$weather = new Weather($provider);
			$result = $weather->getWeather($parameters[1], $parameters[2]);
		}
		else
		{
			$result = "not_found";
		}
	}
	break;
}

header('Content-type: application/json');
echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);

?>

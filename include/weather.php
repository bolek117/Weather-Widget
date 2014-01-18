<?php

class Weather
{
	private $values = array();
	private $provider;

	public function __construct($provider)
	{
		$this->setProvider($provider);
	}

	public function setProvider($provider)
	{
		$this->provider = $provider;
	}

	public function getProvider()
	{
		return $this->provider;
	}

	public function getWeather($country, $city)
	{
		$provider = $this->getProvider();
		if ($provider->locationExists($country, $city))
		{
			$soap_obj = new Soap($provider->getUri());
			$soap = $soap_obj->getSoap();
			
			try
			{
				$result = $soap->__soapCall($provider->getCommandsMapping('GetWeather'), array(array(
					$provider->getFieldsMapping('list_argument_country') => $country,
					$provider->getFieldsMapping('list_argument_city') => $city
				)));
			}
			catch (Exception $e)
			{
				$this->getLocalCopy($country, $city, $provider->getId());
				$this->values[$country][$city]["Status"] = "Failed. Local copy presented.";
				return $this->values[$country][$city];
			}

			$response_mask = $this->getProvider()->getCommandsMapping('GetWeatherResult');
			$response = $result->$response_mask;

			$response = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $response); // not elegant, but necessary
			
			$xml = new SimpleXMLElement($response);

			foreach($xml->children() as $value)
				$this->values[$country][$city][$value->getName()] = (string)$value;

			$this->saveCopyToDatabase($country, $city, $this->values[$country][$city]);

			return $this->values[$country][$city];
		}
		else
		{
			return null;
		}
	}

	public function saveCopyToDatabase($country, $city, $arr)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$arr = serialize($arr);
			$locationId = $this->provider->getLocationId($country, $city);

			$query = $db->prepare("SELECT weather_id FROM weather WHERE weather_locationId=?");
			if ($query)
			{
				$query->bind_param("i", $locationId);
				$query->execute();
				$query->store_result();

				if ($query->num_rows != 0)
				{
					$query = $db->prepare("UPDATE weather SET weather_data=?, weather_timestamp=? WHERE weather_locationId=?");
					if ($query)
					{
						$query->bind_param("sii", $arr, time(), $locationId);
						$query->execute();

						if ($db->affected_rows == 0)
							throw new Exception("Save to database failed.");
					}
					else
					{
						throw new Exception($db->error);
					}
				}
				else
				{
					$query = $db->prepare("INSERT INTO weather (weather_providerId, weather_locationId, weather_data, weather_timestamp) VALUES (?, ?, ?, ?)");
					if ($query)
					{
						$query->bind_param("iisi", $this->getProvider()->getId(), $locationId, $arr, time());
						$query->execute();

						if ($db->affected_rows == 0)
							throw new Exception("Save to database failed.");
					}
					else
					{
						throw new Exception($db->error);
					}
				}

				$query->close();
			}
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
		}
	}

	public function getLocalCopy($country, $city)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$locationId = $this->getProvider()->getLocationId($country, $city);
			
			if ($locationId)
			{
				$query = $db->prepare("SELECT weather_data FROM weather WHERE weather_locationId=?");
				if ($query)
				{
					$query->bind_param("i", $locationId);
					$query->execute();
					$query->store_result();
					$query->bind_result($data);

					if ($query->num_rows > 0)
					{
						$query->fetch();
						$this->values[$country][$city] = unserialize($data);
					}
					else
						throw new Exception("Local copy not found (2)");
				}
				else
				{
					throw new Exception("Local copy not found (1)");
					exit();
				}

				$query->close();
			}
			else
			{
				throw new Exception("Location does not exists.");
				exit();
			}
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
		}
	}

};

?>

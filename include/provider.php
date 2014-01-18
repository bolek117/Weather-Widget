<?php

class Provider extends Mapping
{
	private $name;
	private $uri;
	private $locations = array();

	public function __construct($id, $name, $uri, $country="")
	{
		parent::__construct($id);
		
		try
		{
			if (!isNum($id) || $id <= 0)
			{
				throw new Exception("ID must be an positive integer.");
				exit();
			}

			$this->setId($id);
			$this->setName($name);
			$this->setUri($uri);

			if ($country != "")
				$this->loadLocationsFromDB($country);
			else
				$this->loadLocationsFromDB();
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
			exit();
		}
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setUri($uri)
	{
		$this->uri = $uri;
	}

	public function getUri()
	{
		return $this->uri;
	}

	public function loadLocationsFromService($country)
	{
		$soap_obj = new Soap($this->getUri());
		$soap = $soap_obj->getSoap();

		if (!$soap)
			exit();

		if (empty($this->commandsMapping))
			$this->loadCommandMappingFromDB();

		if (empty($this->fieldsMapping))
				$this->loadFieldsMappingFromDB();

		if (!empty($this->commandsMapping) && !empty($this->fieldsMapping))
		{
			try
			{
				$result = $soap->__soapCall($this->commandsMapping['GetCitiesByCountry'], array(array($this->fieldsMapping['list_argument_country'] => $country)));
			}
			catch(Exception $e)
			{
				echo "Exception caught: ".$e->getMessage()."\n";
				die();
			}
			$response_mask = $this->commandsMapping['GetCitiesByCountryResult'];
			$response = $result->$response_mask;
			
			$xml = new SimpleXMLElement($response);
			$element = $xml->xpath($this->fieldsMapping['list_element']);
							
			$this->loadLocationsFromDB();
			
			foreach($element as $value)
			{
				$city_mask = $this->fieldsMapping['list_city'];
				$city = strtolower((string)$value->$city_mask);

				$country_mask = $this->fieldsMapping['list_country'];
				$country = strtolower((string)$value->$country_mask);
				
				if (!array_key_exists($country, $this->locations))
					$this->locations[$country] = array();

				$add = true;
				foreach($this->locations[$country] as $value)
				{
					if ((string)$city == $value['name'])
					{
						$add = false;
						break;
					}
				}
				
				if ($add)
				{
					$this->addLocation($country, (string)$city);
				}
			}
		}
		else
		{
			echo "Unable to get commands or fields mapping.";
			exit();
		}
	}

	public function loadLocationsFromDB()
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		if (func_num_args() > 0)
		{
			$query = $db->prepare("SELECT location_id, location_country, location_city, location_enabled FROM locations WHERE location_providerId=? AND location_country=?");
			$query->bind_param("is", $this->getId(), func_get_arg(0));
		}
		else
		{
			$query = $db->prepare("SELECT location_id, location_country, location_city, location_enabled FROM locations WHERE location_providerId=?");
			$query->bind_param("i", $this->getId());
		}
		$query->execute();
		$query->store_result();
		$query->bind_result($id, $country, $city, $enabled);

		$locations = array();
		if ($query->num_rows != 0)
		{
			while ($query->fetch())
				$locations[$country][] = array('id' => $id, 'name' => $city, 'available' => (bool)$enabled);
		}
		else
			$fieldsMapping = array();

		$this->locations = $locations;				
		$query->close();
			
	}

	public function addLocation($country, $city)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$query = $db->prepare("INSERT INTO locations (location_providerId, location_country, location_city) VALUES (?, ?, ?)");
			if ($query)
			{
				$query->bind_param("iss", $this->getId(), strtolower($country), strtolower($city));
				$query->execute();
				$this->locations[$country][] = array('name' => $city, 'available' => 1);
			}
			else
			{
				throw new Exception("Save to database failed.");
				exit();
			}		
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
		}
	}

	public function locationExists($country, $city)
	{
		$city = strtolower($city);
		$country = strtolower($country);
		
		if (array_key_exists($country, $this->locations))
		{
			foreach($this->locations[$country] as $value)
			{
				if ($value['name'] == $city)
					return true;
			}
			
			return false;
		}
		else
			return false;
	}

	public function getLocationId($country, $city)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$query = $db->prepare("SELECT location_id FROM locations WHERE location_country=? AND location_city=?");
			if ($query)
			{
				$query->bind_param("ss", strtolower($country), strtolower($city));
				$query->execute();
				$query->store_result();
				$query->bind_result($locationId);

				if ($query->num_rows > 0)
				{
					while ($query->fetch())
						return $locationId;
				}
				else
					return null;
			}
			else
			{
				throw new Exception("Location not found.");
				exit();
			}	
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
		}
	}

	public function getCommandsMapping($name)
	{
		if (array_key_exists($name, $this->commandsMapping))
			return $this->commandsMapping[$name];
		else
			return null;
	}

	public function getFieldsMapping($name)
	{
		if (array_key_exists($name, $this->fieldsMapping))
			return $this->fieldsMapping[$name];
		else
			return null;
	}

	public function getCountriesList()
	{
		$result = array();
		foreach($this->locations as $key => $value)
			$result[] = $key;

		return $result;
	}

	public function getCitiesList($country, $all=false)
	{
		if (array_key_exists($country, $this->locations))
		{
			$result = array();
			foreach($this->locations[$country] as $value)
			{
				if ($value['available'] || $all == true)
				{
					$locationId = $this->getLocationId($country, $value['name']);
					$result[$locationId]['name'] = $value['name'];
					if ($all == true)
						$result[$locationId]['enabled'] = $value['available'];
				}
			}
		}
		else
			$result = "not_found";
			
		return $result;
	}

	public function updateLocationsAttributes($attr)
	{
		$db_conn = new Database();
		$db = $db_conn->handle();
		
		foreach($this->locations as $ckey => $country)
		{
			foreach($country as $key => $value)
			{
				if ($attr && !array_key_exists($key, $attr))
				{
					$this->locations[$ckey][$key]['avaliable'] = false;
				}
				else
				{
					$this->locations[$ckey][$key]['avaliable'] = true;
				}

				$query = $db->prepare("UPDATE locations SET location_enabled=? WHERE location_id=?");
				if ($query)
				{
					$enabled = (int)$this->locations[$ckey][$key]['avaliable'];
					$query->bind_param("ii", $enabled, $key);
					$result = $query->execute();
					if (!$result)
						return false;
				}
				else
				{
					return false;
				}
			}
		}

		return true;
	}

	public function updateDetails($name, $uri)
	{
		$db_conn = new Database();
		$db = $db_conn->handle();
		
		$query = $db->prepare("UPDATE providers SET provider_name=?, provider_uri=? WHERE provider_id=?");
		if ($query)
		{
			$query->bind_param("ssi", $name, $uri, $this->getId());
			$result = $query->execute();
			if (!$result)
				return false;
		}
		else
			return false;

		return true;
	}

	public function deleteCountry($country)
	{
		$db_conn = new Database();
		$db = $db_conn->handle();
		
		$query = $db->prepare("DELETE FROM locations WHERE location_providerId=? AND location_country=?");
		if ($query)
		{
			$query->bind_param("is", $this->getId(), $country);
			$result = $query->execute();
			if (!$result)
				return false;
		}
		else
			return false;

		return true;
	}
};

?>

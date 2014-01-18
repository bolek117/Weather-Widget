<?php

class Mapping
{
	private $id;
	protected $commandsMapping = array();
	protected $fieldsMapping = array();

	public function __construct($id)
	{
		$this->id = $id;
		$this->loadCommandMappingFromDB();
		$this->loadFieldsMappingFromDB();
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}
	
	public function addFieldMapping($field, $xpath)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$query = $db->prepare("INSERT INTO fieldsMapping (field_providerId, field_field, field_xpath) VALUES (?, ?, ?)");
			if ($query)
			{
				$query->bind_param("iss", $this->getId(), $field, $xpath);
				$query->execute();
				$this->fieldsMapping[$field] = $xpath;
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

	public function loadFieldsMappingFromDB()
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		$query = $db->prepare("SELECT field_field,field_xpath FROM fieldsMapping WHERE field_providerId=?");
		$query->bind_param("i", $this->getId());
		$query->execute();
		$query->store_result();
		$query->bind_result($field, $xpath);

		if ($query->num_rows != 0)
		{
			while ($query->fetch())
			{
				$fieldsMapping[$field] = $xpath;
			}
		}
		else
		{
			$this->makeEmptyMappings("field");
			$fieldsMapping = array();
		}

		$this->fieldsMapping = $fieldsMapping;				
		$query->close();
	}

	public function addCommandMapping($orginal, $provider)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$query = $db->prepare("INSERT INTO commandMapping (command_providerId, command_orginal, command_provider) VALUES (?, ?, ?)");
			if ($query)
			{
				$query->bind_param("iss", $this->getId(), $orginal, $provider);
				$query->execute();
				$this->commandMapping[$orginal] = $provider;
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

	public function loadCommandMappingFromDB()
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$query = $db->prepare("SELECT command_orginal, command_provider FROM commandsMapping WHERE command_providerId=?");
			$query->bind_param("i", $this->getId());
			$query->execute();
			$query->store_result();
			$query->bind_result($orginal, $provider);

			if ($query->num_rows != 0)
			{
				while ($query->fetch())
				{
					$commandsMapping[$orginal] = $provider;
				}
			}
			else
			{
				$this->makeEmptyMappings("commands");
				$commandsMapping = array();
			}

			$this->commandsMapping = $commandsMapping;				
			$query->close();
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
		}
	}

	public function getMappings()
	{
		$result = array_merge($this->commandsMapping, $this->fieldsMapping);
		return $result;		
	}

	public function updateMappings($mappings)
	{
		foreach($mappings as $key => $value)
		{
			if (array_key_exists($key, $this->fieldsMapping))
				$this->updateMapping("field", $key, $value);
			else
			{
				if (array_key_exists($key, $this->commandsMapping))
					$this->updateMapping("command", $key, $value);
			}
		}
	}

	public function updateMapping($type, $key, $value)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();
			
		if ($type == "field")
		{
			$this->fieldsMapping[$key] = $value;

			try
			{
				$query = $db->prepare("UPDATE fieldsMapping SET field_xpath=? WHERE field_field=?");
				if ($query)
				{
					$query->bind_param("ss", $value, $key);
					$query->execute();
				}
				else
					throw new Exception($db->error);
			}
			catch (Exception $e)
			{
				echo "Exception caught: ".$e->getMessage()."\n";
			}
		}
		else 
		{
			$this->commandsMapping[$key] = $value;

			try
			{
				$query = $db->prepare("UPDATE commandsMapping SET command_provider=? WHERE command_orginal=?");
				if ($query)
				{
					$query->bind_param("ss", $value, $key);
					$query->execute();
				}
				else
					throw new Exception($db->error);
			}
			catch (Exception $e)
			{
				echo "Exception caught: ".$e->getMessage()."\n";
			}
		}
	}

	public function makeEmptyMappings($type)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();
			
		if ($type == "field")
		{
			try
			{
				$query = $db->prepare("INSERT INTO fieldsMapping (field_providerId, field_field) VALUES
					(?, 'list_element'),
					(?, 'list_country'),
					(?, 'list_city'),
					(?, 'list_argument_country'),
					(?, 'list_argument_city')
				");
				if ($query)
				{
					$id = $this->id;
					$query->bind_param("iiiii", $id, $id, $id, $id, $id);
					$query->execute();
				}
				else
					throw new Exception($db->error);
			}
			catch (Exception $e)
			{
				echo "Exception caught: ".$e->getMessage()."\n";
			}
		}
		else 
		{
			try
			{
				$query = $db->prepare("INSERT INTO commandsMapping (command_providerId, command_orginal) VALUES
					(?, 'GetCitiesByCountry'),
					(?, 'GetCitiesByCountryResult'),
					(?, 'GetWeather'),
					(?, 'GetWeatherResult')
				");
				if ($query)
				{
					$id = $this->id;
					$query->bind_param("iiii", $id, $id, $id, $id);
					$query->execute();
				}
				else
					throw new Exception($db->error);
			}
			catch (Exception $e)
			{
				echo "Exception caught: ".$e->getMessage()."\n";
			}
		}
	}
};

?>

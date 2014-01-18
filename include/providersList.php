<?php

class ProvidersList
{
	private $pList = array();

	public function __construct()
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		if (func_num_args() > 0)
			$this->loadFromDB(func_get_arg(0));
		else
			$this->loadFromDB();
	}

	public function loadFromDB()
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		$result = $db->query("SELECT * FROM providers ORDER BY 'provider_id'");
		if ($result->num_rows != 0)
		{
			while($row = $result->fetch_assoc())
			{
				if (func_num_args() > 0)
					$provider = new Provider($row['provider_id'], $row['provider_name'], $row['provider_uri'], func_get_arg(0));
				else
					$provider = new Provider($row['provider_id'], $row['provider_name'], $row['provider_uri']);
				$this->pList[] = $provider;
			}
		}
		else
		{
			echo "Providers database is empty.";
			exit();
		}
	}

	public function addProvider($provider_name, $provider_uri)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$query = $db->prepare("SELECT provider_id FROM providers WHERE provider_name=?");
			if ($query)
			{
				$query->bind_param("s", $provider_name);
				$query->execute();
				$query->store_result();

				if ($query->num_rows != 0)
					return null;
			}
			
			$query = $db->prepare("INSERT INTO providers (provider_name, provider_uri) VALUES (?, ?)");
			if ($query)
			{
				$query->bind_param("ss", $provider_name, $provider_uri);
				$query->execute();
				$this->pList[] = new Provider($query->insert_id, $provider_name, $provider_uri);

				$query->close();
			}
			else
			{
				throw new Exception("[addProvider] Save to database failed.");
				exit();
			}	
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
		}
	}

	public function deleteProvider($providerId)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		$data[] = array("providers", "provider_id");
		$data[] = array("commandsMapping", "command_providerId");
		$data[] = array("fieldsMapping", "field_providerId");
		$data[] = array("locations", "location_providerId");
		$data[] = array("weather", "weather_providerId");

		try
		{
			foreach($data as $value)
			{
				$query = $db->prepare("DELETE FROM ".$value[0]." WHERE ".$value[1]."=?");
				if ($query)
				{
					$query->bind_param("i", $providerId);
					$query->execute();
				}
				else
					throw new Exception($db->error);
			}
			
			$this->loadFromDB();
			
			$query->close();
			return true;
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
		}
	}

	public function getProvider($providerId)
	{
		foreach($this->pList as $provider)
		{
			if ($provider->getId() == $providerId)
				return $provider;
		}
		return null;
	}

	public function getAllProviders()
	{
		$result = array();
		foreach($this->pList as $provider)
			$result[$provider->getId()] = $provider->getName();

		return $result;
	}
	
	public function count()
	{
		return count($this->pList);
	}
};

?>

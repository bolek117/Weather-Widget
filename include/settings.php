<?php

class Settings
{
	private static $soapTimeout;

	public function __construct()
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$query = "SELECT * FROM `setup`";
			$result = $db->query($query);
			if ($result)
			{
				try
				{
					if ($result->num_rows != 0)
					{
						while($row = $result->fetch_assoc())
						{
							switch($row['setup_name'])
							{
								case "SOAP_timeout":
									$this->setSoapTimeout($row['setup_value']);
								break;
							}
						}
					}
					else
						throw new Exception("Configuration not found");
				}
				catch (Exception $e)
				{
					echo "Exception caught: ".$e->getMessage()."\n";
					exit();
				}
			}
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
		}
	}
	
	public function setSoapTimeout($timeout)
	{
		$this->soapTimeout = $timeout;
		$db_conn = new Database;
		$db = $db_conn->handle();

		$query = $db->prepare("UPDATE setup SET setup_value=? WHERE setup_name='SOAP_timeout'");
		if ($query)
		{
			$query->bind_param("i", $timeout);
			$query->execute();
			return true;
		}
		else
			return null;
	}

	public function getSoapTimeout()
	{
		return $this->soapTimeout;
	}
	
};

?>

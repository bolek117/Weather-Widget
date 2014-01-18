<?php

class Database
{
	private $host;
	private $username;
	private $password;
	private $database;
	private $handle;
	private $basePath;

	public function __construct()
	{
		$this->handle = null;
		
		if (func_num_args() == 4)
		{
			$this->setHost(func_get_arg(0));
			$this->setUsername(func_get_arg(1));
			$this->setPassword(func_get_arg(2));
			$this->setDatabase(func_get_arg(3));
		}
		else
		{
			try
			{
				$base = $this->findConfigFile();
				if ($base == "not_found")
					throw new Exception("Config file not found");
					
				include($base."config.php");
			}
			catch (Exception $e)
			{
				echo "Exception caught: ".$e->getMessage()."\n";
				die();
			}

			$this->setHost($dbHost);
			$this->setUsername($dbUsername);
			$this->setPassword($dbPassword);
			$this->setDatabase($dbDatabase);
		}

		try
		{
			@ $this->handle = new mysqli($this->getHost(), $this->getUsername(), $this->getPassword(), $this->getDatabase());
			
			if (mysqli_connect_errno())
			{
				$this->handle = null;
				throw new Exception(mysqli_connect_error());
				exit();
			}
		}
		catch (Exception $e)
		{
			echo "<p>Exception caught: ".$e->getMessage()."</p>\n";
		}
	}

	public function __destruct()
	{
		if ($this->handle != null)
			$this->handle->close();
	}
	
	public function setHost($host)
	{
		$this->host = $host;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function setUsername($username)
	{
		$this->username = $username;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setDatabase($database)
	{
		$this->database = $database;
	}

	public function getDatabase()
	{
		return $this->database;
	}

	public function handle()
	{
		try
		{
			if (!$this->handle)
			{
				throw new Exception("MySQL connection failed");
				return null;
			}
			else
				return $this->handle;
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
			die();
		}
	}

	public function findConfigFile()
	{
		$base = "";
		for($i=0;$i<10;$i++)
		{
			if (file_exists($base."config.php"))
			{
				$this->basePath = $base;
				return $base;
			}
			else
			{					
				$base .= "../";
			}
		}

		return "not_found";
	}
	
};

?>

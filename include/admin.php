<?php

class Admin
{
	private $lifetime = 1800;	// 30 minutes
	private $path = "/";
	private $domain = "";
	private $secure = false;
	private $httponly = true;
	
	public function __construct()
	{
		session_set_cookie_params($this->lifetime, $this->path, $this->domain, $this->secure, $this->httponly);
		session_start();
	}

	public function clearSession()
	{
		foreach($_SESSION as $key => $value)
		{
			$_SESSION[$key] = null;
			unset($_SESSION[$key]);
		}

		setcookie(session_id(), "", time()-3600*24);
		session_destroy();
		session_write_close();
	}
	
	public function checkCredentials($username, $password)
	{
		$db_conn = new Database;
		$db = $db_conn->handle();

		try
		{
			$password = hash("sha512", $password);
			$query = $db->prepare("SELECT admin_id FROM admin WHERE admin_username=? AND admin_password=?");
			if ($query)
			{
				$query->bind_param("ss", $username, $password);
				$query->execute();
				$query->store_result();

				if ($query->num_rows > 0)
				{
					$_SESSION['isAdmin'] = true;
					return true;
				}
				else
				{
					$this->clearSession();
					return false;
				}					
			}
			else
			{
				throw new Exception($db->error);
				return false;
			}
		}
		catch (Exception $e)
		{
			echo "Exception caught: ".$e->getMessage()."\n";
			return false;
		}
	}

	public function checkRights()
	{
		if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == true)
			return true;
		else
			return false;
	}
}

?>

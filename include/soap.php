<?php

class Soap
{
	private $soap;

	public function __construct($uri)
	{
		$settings = new Settings;
		try
		{
			$soap = new SoapClient($uri, array('connection_timeout' => $settings->getSoapTimeout()));
		}
		catch (Exception $e)
		{
			$this->setSoap(null);
			return;
		}
		
		$this->setSoap($soap);
	}

	public function setSoap($soap)
	{
		$this->soap = $soap;
	}
	
	public function getSoap()
	{
		return $this->soap;
	}

};

?>

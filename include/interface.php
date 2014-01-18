<?php

class _Interface
{
	private $charset = "utf-8";
	private $header = array();
	private $page;
	private $bodyAttr;

	public function __construct()
	{
		$this->preparePage();

		header('Content-type: text/html; charset='.$this->charset);
		$this->addHeaderContent("<meta charset=\"".$this->charset."\">");
		$this->addHeaderContent("<LINK href=\"./include/styles.css\" rel=\"stylesheet\" type=\"text/css\">");
	}

	public function makeHeader()
	{
		$this->header[] = "<!DOCTYPE html>\n<html>\n\t<head>\n";
		$this->header[] = array();
		$this->header[] = "\t</head>\n";
	}

	public function preparePage()
	{
		$this->makeHeader();
		
		$this->page = "";
		
		$this->page .= $this->getHeader();
		
		$this->page .= $this->getFooter();

		return $this->page;
	}

	public function addHeaderContent($content)
	{
		$this->header[1][] = $content;
	}

	public function setTitle($title)
	{
		$this->addHeaderContent("<title>$title</title>");
	}

	public function getHeader()
	{
		$result = "";
		$result .= $this->header[0];
		foreach($this->header[1] as $value)
			$result .= "\t\t".$value."\n";

		$result .= $this->header[2];
		$result .= "\t<body";

		if (!empty($this->bodyAttr))
		{
			foreach($this->bodyAttr as $key => $value)
				$result .= " $key=\"$value\"";
		}
			
		$result .= ">\n";

		return $result;
	}

	public function getFooter()
	{
		return "\n\t</body>\n</html>";
	}

	public function addBodyAttribute($key, $value)
	{
		$this->bodyAttr[$key] = $value;
	}
};

?>

<?php

function isNum($value)
{
	if (preg_match('#^[1-9]\d*$#', $value))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function _var_dump($var)
{
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}

?>

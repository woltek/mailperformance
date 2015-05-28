<?php

class utils {
	
	public static function isNotNull($param)
	{
		if($param != null)
			return true;
		else
			return false;
	}
}
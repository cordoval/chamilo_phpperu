<?php

abstract class SearchSource
{
	private static $instance;
	
	abstract function search($query);
	
	static function factory($type)
	{
		if(!self :: $instance)
		{
			$file = dirname(__FILE__) . '/sources/' . $type . '_search_source.class.php';
			
			if(file_exists($file))
			{
				require_once($file);
				$class = Utilities :: underscores_to_camelcase($type) . 'SearchSource';
				self :: $instance = new $class();
			}
			else
			{
				throw new Exception(Translation :: get('CouldNotLoadSearchSource', array('TYPE', $type)));
			}
		}
		
		return self :: $instance;
	}
}
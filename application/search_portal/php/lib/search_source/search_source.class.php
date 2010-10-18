<?php

abstract class SearchSource
{
	private static $instance;
	
	/**
	 * Singleton
	 * @param String $type
	 * @return SearchSource the selected source
	 */
	static function factory($type)
	{
		if(!self :: $instance)
		{
			$file = WebApplication :: get_application_class_lib_path('search_portal') . 'search_source/sources/' . $type . '_search_source.class.php';
			
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
	
	abstract function retrieve_search_results($query, $offset = 0, $max_objects = -1, $user = null);
	abstract function count_search_results($query, $user = null);	
}
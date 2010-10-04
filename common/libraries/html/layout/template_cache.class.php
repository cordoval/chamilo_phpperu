<?php 

abstract class TemplateCache
{
	function factory($theme, $type)
	{
		$file = dirname(__file__) . '/template_cache/' . $type . '_template_cache.class.php';
		
		if(!file_exists($file))
			die('Could not load type ' . $type . ' as template cache');
		
		require_once($file);
			
		$classname = ucfirst($type) . 'TemplateCache';
		return new $classname($theme);
	}
	
	abstract function cache($handle, $uncompiled_code, $compiled_code);
	abstract function retrieve_from_cache($handle, $uncompiled_code);
}

?>
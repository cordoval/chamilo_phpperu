<?php 

class FileTemplateCache extends TemplateCache
{
	private $cache_path;
	
	function FileTemplateCache($theme)
	{
		$this->cache_path = Path :: get(SYS_PATH) . 'files/cache/layout/' . $theme . '/';
		
		if(!is_dir($this->cache_path))
			mkdir($this->cache_path, 0775, true);	
	}
	
	function cache($handle, $uncompiled_code, $compiled_code)
	{
		$path = $this->get_path($handle, $uncompiled_code);
		file_put_contents($path, $compiled_code);
		
		return true;
	}
	
	function retrieve_from_cache($handle, $uncompiled_code)
	{
		$path = $this->get_path($handle, $uncompiled_code);
		
		if(file_exists($path))
		{
			return file_get_contents($path);
		}
		
		return false;
	}
	
	private function get_path($handle, $uncompiled_code)
	{
		return $this->cache_path . $this->get_filename($handle, $uncompiled_code);
	}
	
	private function get_filename($handle, $uncompiled_code)
	{
		return md5($handle);
	}
}

?>
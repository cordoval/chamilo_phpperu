<?php
require_once Path :: get_common_libraries_path() . '/html/menu/tree_menu/tree_menu_data_provider.class.php';

abstract class GradebookTreeMenuDataProvider extends TreeMenuDataProvider
{
	const PARAM_ID = 'category_id';
	private $type;
	
	public static function factory($application, $url)
	{
		$file = Path :: get_application_path() . '/lib/' . $application . '/' . $application . '_gradebook_tree_menu_data_provider.class.php';
		if(file_exists($file))
		{
			require_once $file;
			$class_name = ucfirst($application) . 'GradebookTreeMenuDataProvider';
			return new $class_name($url);
		}
	}
	
	public function get_type()
	{
		return $this->type;
	}
	
	public function set_type($type)
	{
		$this->type = $type;
	}
    
    public function get_id_param()
    {
    	return self :: PARAM_ID;
    }
}
?>
<?php
class GradebookTreeMenuDataProvider
{
	public static function factory($application)
	{
		$file = Path :: get_application_path() . '/lib/' . $application . '/' . $application . '_gradebook_tree_menu_data_provider.class.php';
		if(file_exists($file))
		{
			require_once $file;
			$class_name = ucfirst($application) . 'GradebookTreeMenuDataProvider';
			return new $class_name;
		}
	}
}
?>
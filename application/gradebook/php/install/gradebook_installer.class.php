<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_data_manager.class.php';
require_once Path :: get_common_libraries_path() . 'installer.class.php';

class GradebookInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function GradebookInstaller($values)
    {
		parent :: __construct($values, GradebookDataManager :: get_instance());
    }

    function install_extra()
    {
    	if (!$this->create_formats())
    	{
    		return false;
    	}
    	else
    	{
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('FormatsAdded'));
    	}
//    	$sql_file = Path :: get_application_path() . 'lib/gradebook/install/dump.sql';
//    	$query = file_get_contents($sql_file);
//    	$queries = explode('@@@@', $query);
//
//    	$dm = $this->get_data_manager();
//    	$connection = $dm->get_connection();
//
//    	foreach($queries as $query)
//    	{
//			$statement = $connection->query(trim($query));
//
//			if (MDB2 :: isError($statement))
//			{
//				return false;
//			}
//    	}

		return true;
    }

    function create_formats()
    {
    	$root = dirname(__FILE__) . '/../evaluation_format/';
    	$folders = Filesystem :: get_directory_content($root, Filesystem :: LIST_DIRECTORIES, false);
    	foreach($folders as $folder)
    	{
    		if(Text :: char_at($folder, 0) != '.')
    		{
    			$formats = Filesystem :: get_directory_content($root.$folder.'/', Filesystem :: LIST_FILES, false);
    			require_once $root . 'evaluation_format.class.php';
    			foreach($formats as $format)
    			{
    				$ev = EvaluationFormat :: factory($format, 1, $folder);

	    			$format = new Format();
	    			$format->set_title($ev->get_evaluation_format_name());
	    			$format->set_active($ev->get_default_active_value());

	    			if($format->create())
	    			{
	    				$this->add_message(self :: TYPE_NORMAL, Translation :: get('FormatAdded') . ' ' . $format->get_title());
	    			}
	    			else
	    			{
	    				return false;
	    			}
    			}
    		}
    	}
        return true;
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>
<?php
require_once dirname(__FILE__).'/../gradebook_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';

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
//    	$database = $dm->get_database();
//    	$connection = $database->get_connection();
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
    			if (!file_exists($root . $folder . '/' . $folder . '_evaluation_format.class.php'))
    			{
    				return false;
    			}
    			$gef = new GradebookEvaluationFormat();
    			$gef->set_evaluation_format($folder);
    			$gef->set_active(1);

    			if($gef->create())
    			{
    				$this->add_message(self :: TYPE_NORMAL, Translation :: get('FormatAdded') . ' ' . $folder);
    			}
    			else
    			{
    				return false;
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
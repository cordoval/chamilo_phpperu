<?php
namespace common\libraries\application_generator;

use common\libraries\Utilities;

/**
 * Dataclass generator used to generate data managers
 * @author Sven Vanpoucke
 */
class DataManagerGenerator
{
    private $template;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(dirname(__FILE__));
    }

    function generate_data_managers($data_manager_location, $database_location, $application_name, $classes, $author)
    {
        if (! is_dir($data_manager_location))
            mkdir($data_manager_location, 0777, true);
        
        if (! is_dir($database_location))
            mkdir($database_location, 0777, true);
        
        $dm_file = fopen($data_manager_location . Utilities :: camelcase_to_underscores($application_name) . '_data_manager.class.php', 'w+');
        $dm_interface_file = fopen($data_manager_location . Utilities :: camelcase_to_underscores($application_name) . '_data_manager_interface.class.php', 'w+');
        $database_file = fopen($database_location . 'database.class.php', 'w+');
        
        if ($dm_file && $database_file)
        {
            $this->template->set_filenames(array('datamanager' => 'data_manager.template', 'database' => 'data_manager_database.template', 'datamanager_interface' => 'data_manager_interface.template'));
            
            $this->template->assign_vars(array('APPLICATION_NAME' => Utilities :: underscores_to_camelcase($application_name), 
                    'L_APPLICATION_NAME' => Utilities :: camelcase_to_underscores($application_name), 'AUTHOR' => $author,
                    'NAMESPACE' => 'application\\' . $application_name));
            
            foreach ($classes as $class)
            {
                $class_lower = Utilities :: camelcase_to_underscores($class);
                $alias = substr($class_lower, 0, 2) . substr($class_lower, - 2);
                $class2 = substr($class_lower, - 1) == 'y' ? substr($class_lower, 0, strlen($class_lower) - 1) . 'ie' : $class_lower;
                $class2 .= 's';
                
                $this->template->assign_block_vars("OBJECTS", array('OBJECT_CLASS' => $class, 'L_OBJECT_CLASS' => $class_lower, 'L_OBJECT_CLASSES' => $class2, 'OBJECT_ALIAS' => $alias));
            }
            
            $string = trim($this->template->pparse_return('datamanager'));
            fwrite($dm_file, $string);
            fclose($dm_file);
            
            $string = trim($this->template->pparse_return('database'));
            fwrite($database_file, $string);
            fclose($database_file);

            $string = trim($this->template->pparse_return('datamanager_interface'));
            fwrite($dm_interface_file, $string);
            fclose($dm_interface_file);
        }
    }
}

?>
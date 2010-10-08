<?php

/**
 * Manager generator used to generate managers
 * @author Sven Vanpoucke
 */
class ManagerGenerator
{
    private $template;

    /**
     * Constructor
     */
    function ManagerGenerator()
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(dirname(__FILE__));
    }

    function generate_managers($location, $application_name, $classes, $author)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        $manager_file = fopen($location . Utilities :: camelcase_to_underscores($application_name) . '_manager.class.php', 'w+');
        
        if ($manager_file)
        {
            $this->template->set_filenames(array('manager' => 'manager.template'));
            
            $this->template->assign_vars(array('APPLICATION_NAME' => Utilities :: underscores_to_camelcase($application_name), 'L_APPLICATION_NAME' => Utilities :: camelcase_to_underscores($application_name), 'AUTHOR' => $author));
            
            foreach ($classes as $class)
            {
                $class_lower = Utilities :: camelcase_to_underscores($class);
                $class_upper = strtoupper($class_lower);
                $class2 = substr($class, - 1) == 'y' ? substr($class, 0, strlen($class) - 1) . 'ie' : $class;
                $class2 .= 's';
                $class2_lower = Utilities :: camelcase_to_underscores($class2);
                $class2_upper = strtoupper($class2_lower);
                
                $this->template->assign_block_vars("OBJECTS", array('OBJECT_CLASS' => $class, 'OBJECT_CLASSES' => $class2, 'L_OBJECT_CLASS' => $class_lower, 'U_OBJECT_CLASS' => $class_upper, 'L_OBJECT_CLASSES' => $class2_lower, 'U_OBJECT_CLASSES' => $class2_upper));
            }
            
            $string = trim($this->template->pparse_return('manager'));
            fwrite($manager_file, $string);
            fclose($manager_file);
        }
    }
}

?>
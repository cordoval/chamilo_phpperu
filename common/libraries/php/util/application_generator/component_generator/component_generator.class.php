<?php

/**
 * Component generator used to generate components
 * @author Sven Vanpoucke
 */
class ComponentGenerator
{
    private $template;

    /**
     * Constructor
     */
    function ComponentGenerator()
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(dirname(__FILE__));
    }

    function generate_components($location, $application_name, $classes, $author, $options)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        $browser_file = fopen($location . 'browser.class.php', 'w+');
        
        if ($browser_file)
        {
            $this->template->set_filenames(array('general_browser_component' => 'general_browser_component.template', 'browser_component' => 'browser_component.template', 'creator_component' => 'creator_component.template', 'updater_component' => 'updater_component.template', 'deleter_component' => 'deleter_component.template', 'sortable_browser_component' => 'sortable_browser_component.template'));
            
            $this->template->assign_vars(array('APPLICATION_NAME' => Utilities :: underscores_to_camelcase($application_name), 'L_APPLICATION_NAME' => Utilities :: camelcase_to_underscores($application_name), 'AUTHOR' => $author));
            
            foreach ($classes as $class)
            {
                $class2 = substr($class, - 1) == 'y' ? substr($class, 0, strlen($class) - 1) . 'ie' : $class;
                $class2 .= 's';
                $class2_lower = Utilities :: camelcase_to_underscores($class2);
                
                $this->template->assign_block_vars("OBJECTS", array(

                'L_OBJECT_CLASSES' => $class2_lower, 'OBJECT_CLASSES' => $class2));
            }
            
            $string = trim($this->template->pparse_return('general_browser_component'));
            fwrite($browser_file, $string);
            fclose($browser_file);
        }
        
        $components = array('browser', 'creator', 'updater', 'deleter');
        
        foreach ($classes as $class)
        {
            $class_lower = Utilities :: camelcase_to_underscores($class);
            $class_upper = strtoupper($class_lower);
            
            $class2 = substr($class, - 1) == 'y' ? substr($class, 0, strlen($class) - 1) . 'ie' : $class;
            $class2 .= 's';
            $class2_lower = Utilities :: camelcase_to_underscores($class2);
            $class2_upper = strtoupper($class2_lower);
            
            $this->template->assign_vars(array('L_OBJECT_CLASSES' => $class2_lower, 'U_OBJECT_CLASSES' => $class2_upper, 'OBJECT_CLASSES' => $class2, 'L_OBJECT_CLASS' => $class_lower, 'U_OBJECT_CLASS' => $class_upper, 'OBJECT_CLASS' => $class));
            
            foreach ($components as $component)
            {
                if ($component == 'browser')
                    $component_file = fopen($location . $class2_lower . '_' . $component . '.class.php', 'w+');
                else
                    $component_file = fopen($location . $class_lower . '_' . $component . '.class.php', 'w+');
                
                if ($component == 'browser' && $options[$class_lower]['table'] == 1)
                    $component = 'sortable_browser';
                
                $string = trim($this->template->pparse_return($component . '_component'));
                fwrite($component_file, $string);
                fclose($component_file);
            }
        
        }
    }
}

?>
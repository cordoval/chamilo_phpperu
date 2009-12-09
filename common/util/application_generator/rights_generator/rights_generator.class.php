<?php

/**
 * Dataclass generator used to generate rights xml files
 * @author Sven Vanpoucke
 */
class RightsGenerator
{
    private $template;

    /**
     * Constructor
     */
    function RightsGenerator()
    {
        $this->template = new Phpbb2Template();
        $this->template->set_rootdir(dirname(__FILE__));
    }

    /**
     * Generate a rights xml file with the given info
     * @param string $location - The location of the class
     * @param string $application_name - The name of the application
     */
    function generate_right_files($location, $application_name)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        $file = fopen($location . Utilities :: camelcase_to_underscores($application_name) . '_locations.xml', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('rights' => 'rights.template'));
            
            $this->template->assign_vars(array('APPLICATION_NAME' => $application_name));
            
            $string = trim($this->template->pparse_return('rights'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}

?>
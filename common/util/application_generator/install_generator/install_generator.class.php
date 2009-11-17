<?php

/**
 * Dataclass generator used to generate install files
 * @author Sven Vanpoucke
 */
class InstallGenerator
{
    private $template;

    /**
     * Constructor
     */
    function InstallGenerator()
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(dirname(__FILE__));
    }

    /**
     * Generate install files with the given info
     * @param string $location - The location of the class
     * @param string $application_name - The name of the application
     */
    function generate_install_files($location, $application_name, $author)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        $file = fopen($location . Utilities :: camelcase_to_underscores($application_name) . '_installer.class.php', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('install' => 'install.template'));
            
            $this->template->assign_vars(array('APPLICATION_NAME' => Utilities :: camelcase_to_underscores($application_name), 'C_APPLICATION_NAME' => Utilities :: underscores_to_camelcase($application_name), 'AUTHOR' => $author));
            
            $string = trim($this->template->pparse_return('install'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}

?>
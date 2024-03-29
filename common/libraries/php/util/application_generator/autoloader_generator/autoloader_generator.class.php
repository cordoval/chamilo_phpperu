<?php
namespace common\libraries\application_generator;

use common\libraries\Utilities;

/**
 * Dataclass generator used to generate autoloader files
 * @author Sven Vanpoucke
 */
class AutoloaderGenerator
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

    /**
     * Generate install files with the given info
     * @param string $location - The location of the class
     * @param string $application_name - The name of the application
     */
    function generate_autoloader($location, $application_name, $classes, $author, $options)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        $file = fopen($location . 'autoloader.class.php', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('autoloader' => 'autoloader.template'));
            $this->template->assign_vars(array('L_APPLICATION_NAME' => $application_name, 'AUTHOR' => $author, 'NAMESPACE' => 'application\\' . $application_name));

            foreach ($classes as $class)
            {
                $class_lower = Utilities :: camelcase_to_underscores($class);
                $this->template->assign_block_vars('OBJECTS', array('L_OBJECT_CLASS' => $class_lower));

                if($options[$class_lower]['table'] == 1)
                {
                    $this->template->assign_block_vars('OBJECTS.TABLE', array());
                }
            }

            $string = trim($this->template->pparse_return('autoloader'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}

?>
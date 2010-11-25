<?php
namespace common\libraries\application_generator;

use common\libraries\Utilities;
use common\libraries\Text;

/**
 * Dataclass generator used to generate autoloader files
 * @author Sven Vanpoucke
 */
class PackageInfoGenerator
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
    function generate_package_info($location, $application_name, $author)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        $file = fopen($location . 'package.info', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('package_info' => 'package_info.template'));
            $this->template->assign_vars(array('L_APPLICATION_NAME' => $application_name, 'AUTHOR' => $author, 
                    'C_APPLICATION_NAME' => Utilities :: underscores_to_camelcase($application_name),
                    'APPLICATION_NAME_FIRST_LETTER' => Text :: char_at($application_name, 0)));

            $string = trim($this->template->pparse_return('package_info'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}

?>
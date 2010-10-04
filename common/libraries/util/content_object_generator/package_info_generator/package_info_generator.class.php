<?php

/**
 * Package info generator used to generate package info files with given properties
 * @author Hans De Bisschop
 */
class PackageInfoGenerator
{
    private $template;

    /**
     * Constructor
     */
    function PackageInfoGenerator()
    {
    }

    /**
     * Generate a package.info file
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    function generate_package_info($xml_definition, $author)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(dirname(__FILE__));
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/package.info', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('package_info' => 'package_info.template'));
            
            $name = Utilities :: underscores_to_camelcase_with_spaces($xml_definition['name']);
            
            $this->template->assign_vars(array('CONTENT_OBJECT_NAME' => $name, 'CODE' => $xml_definition['name'], 'FOLDER' => $xml_definition['name']{0}, 'AUTHOR' => $author));
            
            $string = $this->template->pparse_return('package_info');
            fwrite($file, $string);
            fclose($file);
        }
    }

    /**
     * Generate a settings file
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    function generate_settings($xml_definition)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(dirname(__FILE__));
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $xml_definition['name'] . '/settings';
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/settings_' . $xml_definition['name'] . '.xml', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('settings' => 'settings.template'));
            
            $name = Utilities :: underscores_to_camelcase_with_spaces($xml_definition['name']);
            
            $this->template->assign_vars(array('CODE' => $xml_definition['name']));
            
            $string = $this->template->pparse_return('settings');
            fwrite($file, $string);
            fclose($file);
        }
    }
}

?>
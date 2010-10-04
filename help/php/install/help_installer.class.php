<?php
/**
 * $Id: help_installer.class.php 226 2009-11-13 14:44:03Z chellee $ 
 * @package help.install
 */
/**
 * This installer can be used to create the storage structure for the
 * group application.
 */
class HelpInstaller extends Installer
{

    /**
     * Constructor
     */
    function HelpInstaller($values)
    {
        parent :: __construct($values, HelpDataManager :: get_instance());
        
    //$this->install_extra($values);
    }

    function install_extra()
    {
        if (! $this->install_help_items())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('HelpItemsInstalled'));
        }
        
        return true;
    }

    function install_help_items()
    {
        $path = dirname(__FILE__) . '/../help_items/';
        
        $files = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES);
        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                $data = $this->extract_xml_file($file);
                
                if ($data)
                {
                    $filename = basename($file);
                    $language = substr($filename, 0, strlen($filename) - 4);
                    
                    $items = $data['help_item'];
                    foreach ($items as $item)
                    {
                        $help_item = new HelpItem();
                        $help_item->set_name($item['name']);
                        $help_item->set_language($language);
                        $help_item->set_url($item['url']);
                        $help_item->create();
                    }
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
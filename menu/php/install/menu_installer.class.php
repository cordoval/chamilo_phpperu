<?php
/**
 * $Id: menu_installer.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.install
 */
/**
 *	This installer can be used to create the storage structure for the
 * menu application.
 */
class MenuInstaller extends Installer
{
    private $values;

    /**
     * Constructor
     */
    function MenuInstaller($values)
    {
        $this->values = $values;
        parent :: __construct($values, MenuDataManager :: get_instance());
    }

    /**
     * Runs the install-script.
     * @todo This function now uses the function of the RepositoryInstaller
     * class. These shared functions should be available in a common base class.
     */
    function install_extra()
    {
        if (! $this->create_basic_menu())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('MenuCreated'));
        }
        
        return true;
    }

    function create_basic_menu()
    {
        $values = $this->values;
        
        $packages = $this->get_package_info();
        foreach ($packages as $category => $package)
        {
            $navigation_item = new NavigationItem();
            $navigation_item->set_title(Translation :: get(Utilities :: underscores_to_camelcase($category)));
            $navigation_item->set_application('root');
            $navigation_item->set_section('root');
            $navigation_item->set_category(0);
            $navigation_item->set_is_category(1);
            $navigation_item->create();
            
            $id = $navigation_item->get_id();
            
            $used = false;
            
            foreach ($package as $application)
            {
                $application_name = $application['name'];
                $application = $application['code'];
                
                if (isset($values['install_' . $application]))
                {
                    $sub_nav_item = new NavigationItem();
                    $sub_nav_item->set_title(Translation :: get(str_replace(' ', '', $application_name)));
                    $sub_nav_item->set_application($application);
                    $sub_nav_item->set_section($application);
                    $sub_nav_item->set_category($id);
                    $sub_nav_item->set_is_category(0);
                    $sub_nav_item->create();
                    $used = true;
                }
            }
            
            if (! $used)
            {
                $navigation_item->delete();
            }
        }
        
        /*foreach ($menu_applications as $application => $name)
        {
            // TODO: Temporary fix.
            if (isset($values['install_' . $application]) && $application != '.svn')
            {
                $navigation_item = new NavigationItem();
                $navigation_item->set_title($name);
                $navigation_item->set_application($application);
                $navigation_item->set_section($application);
                $navigation_item->set_category(0);
                $navigation_item->create();
            }
        }*/
        
        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }

    function get_package_info()
    {
        $packages = array();
        $applications = WebApplication :: load_all_from_filesystem(false);
        
        foreach ($applications as $application)
        {
            $xml_data = file_get_contents(Path :: get_application_path() . 'lib/' . $application . '/package.info');
            
            if ($xml_data)
            {
                $unserializer = new XML_Unserializer();
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('package', 'dependency'));
                
                // unserialize the document
                $status = $unserializer->unserialize($xml_data);
                
                if (! PEAR :: isError($status))
                {
                    $data = $unserializer->getUnserializedData();
                    if (! isset($packages[$data['package'][0]['category']]))
                    {
                        $packages[$data['package'][0]['category']] = array();
                    }
                    $packages[$data['package'][0]['category']][] = $data['package'][0];
                }
            }
        }
        
        ksort($packages);
        
        return $packages;
    }
}
?>
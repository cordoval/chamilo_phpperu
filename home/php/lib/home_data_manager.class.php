<?php
/**
 * $Id: home_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package home.lib
 *
 * This is a skeleton for a data manager for the Home application.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class HomeDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return UserDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_home_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'HomeDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    function retrieve_block_properties($application, $component)
    {
        if (WebApplication :: is_application($application))
        {
            $path = dirname(__FILE__) . '/../../application/' . $application . '/php/block/' . $application . '_' . $component . '.xml';
        }
        else
        {
            $path = dirname(__FILE__) . '/../../' . $application . '/php/block/' . $application . '_' . $component . '.xml';
        }
        
        if (file_exists($path))
        {
            $doc = new DOMDocument();
            $doc->load($path);
            $object = $doc->getElementsByTagname('block')->item(0);
            $name = $object->getAttribute('name');
            $xml_properties = $doc->getElementsByTagname('property');
            foreach ($xml_properties as $index => $property)
            {
                $properties[$property->getAttribute('name')] = $property->getAttribute('default');
            }
            
            return $properties;
        }
        else
        {
            return null;
        }
    }

    function create_block_properties($block)
    {
        $homeblockconfigs = self :: retrieve_block_properties($block->get_application(), $block->get_component());
        
        foreach ($homeblockconfigs as $variable => $value)
        {
            $homeblockconfig = new HomeBlockConfig($block->get_id());
            {
                $homeblockconfig->set_variable($variable);
                $homeblockconfig->set_value($value);
                
                if (! $homeblockconfig->create())
                {
                    return false;
                }
            }
        }
        
        return true;
    }
}
?>
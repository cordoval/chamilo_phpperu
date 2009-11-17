<?php
/**
 * $Id: home_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package home.lib
 */

/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class HomeDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function HomeDataManager()
    {
        $this->initialize();
    }

    /**
     * Initializes the data manager.
     */
    abstract function initialize();

    abstract function get_next_home_column_id();

    abstract function get_next_home_block_id();

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
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'HomeDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function count_home_rows($condition = null);

    abstract function count_home_columns($condition = null);

    abstract function count_home_blocks($condition = null);

    abstract function retrieve_home_rows($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_home_column($id);

    abstract function retrieve_home_columns($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_home_block($id);

    abstract function retrieve_home_blocks($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_home_tab($id);

    abstract function retrieve_home_tab_blocks($home_tab);

    abstract function retrieve_home_tabs($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function truncate_home($user_id);

    abstract function retrieve_home_row_at_sort($parent, $sort, $direction);

    abstract function retrieve_home_column_at_sort($parent, $sort, $direction);

    abstract function retrieve_home_block_at_sort($parent, $sort, $direction);

    abstract function retrieve_home_tab_at_sort($user, $sort, $direction);

    abstract function update_home_block($home_block);

    abstract function update_home_block_config($home_block_config);

    abstract function update_home_column($home_column);

    abstract function update_home_row($home_row);

    abstract function update_home_tab($home_tab);

    abstract function create_home_row($home_row);

    abstract function create_home_column($home_column);

    abstract function create_home_block($home_block);

    abstract function create_home_block_config($home_block_config);

    abstract function delete_home_row($home_row);

    abstract function delete_home_tab($home_tab);

    abstract function delete_home_column($home_column);

    abstract function delete_home_block($home_block);

    abstract function delete_home_block_config($home_block_config);

    abstract function delete_home_block_configs($home_block);

    abstract function retrieve_home_block_config($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function count_home_block_config($condition = null);

    function retrieve_block_properties($application, $component)
    {
        if (WebApplication :: is_application($application))
        {
            $path = dirname(__FILE__) . '/../../application/lib/' . $application . '/block/' . $application . '_' . $component . '.xml';
        }
        else
        {
            $path = dirname(__FILE__) . '/../../' . $application . '/block/' . $application . '_' . $component . '.xml';
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
        $homeblockconfigs = $this->retrieve_block_properties($block->get_application(), $block->get_component());
        
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
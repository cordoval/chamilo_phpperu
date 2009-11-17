<?php

/**
 * $Id: local_package_browser_cell_renderer.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component.local_package_browser
 */
class LocalPackageBrowserCellRenderer
{

    function render_cell($default_property, $data)
    {
        $data = $data[$default_property];
        
        if (is_null($data))
        {
            $data = '-';
        }
        
        return $data;
    }

    function get_properties()
    {
        $properties = array();
        $properties[] = 'Name';
        $properties[] = '';
        return $properties;
    }

    function get_property_count()
    {
        return count($this->get_properties());
    }

    function get_prefix()
    {
        return '';
    }
}
?>
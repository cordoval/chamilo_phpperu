<?php
/**
 * $Id: diagnoser_cellrenderer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.diagnoser
 */
class DiagnoserCellRenderer
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
        return array('', 'Section', 'Setting', 'Current', 'Expected', 'Comment');
    }

    function get_prefix()
    {
        return '';
    }
}
?>
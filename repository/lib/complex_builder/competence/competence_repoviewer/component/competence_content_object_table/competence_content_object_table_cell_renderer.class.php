<?php
/**
 * $Id: competence_content_object_table_cell_renderer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/content_object_table/content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/competence_content_object_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class CompetenceContentObjectTableCellRenderer extends ContentObjectTableCellRenderer
{
	function render_cell($column, $content_object)
    {
    	switch ($column->get_name())
        {
            case 'children':
                return $this->get_children_titles();
        }
        
        return parent :: render_cell($column, $content_object);
    }
    
    function get_children_titles()
    {
    	
    }
}
?>
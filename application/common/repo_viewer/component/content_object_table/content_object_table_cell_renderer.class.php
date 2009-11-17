<?php
/**
 * $Id: content_object_table_cell_renderer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/content_object_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class ContentObjectTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    private $table_actions;

    /**
     * Constructor.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     */
    function ContentObjectTableCellRenderer($table_actions)
    {
        $this->table_actions = $table_actions;
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $content_object)
    {
        if ($column === ContentObjectTableColumnModel :: get_action_column())
        {
            return $this->get_publish_links($content_object);
        }
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the links to publish or edit and publish a learning object.
     * @param ContentObject $content_object The learning object for which the
     * links should be returned.
     * @return string A HTML-representation of the links.
     */
    private function get_publish_links($content_object)
    {
        $toolbar_data = array();
        $table_actions = $this->table_actions;
        
        foreach ($table_actions as $table_action)
        {
            $table_action['href'] = str_replace('%d', $content_object->get_id(), $table_action['href']);
            $toolbar_data[] = $table_action;
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
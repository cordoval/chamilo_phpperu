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
        $this->set_table_actions($table_actions);
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
    protected function get_publish_links($content_object)
    {
        $toolbar = $this->get_table_actions();

        $table_actions = $toolbar->get_items();
        $object_toolbar = new Toolbar();
        foreach ($table_actions as $table_action)
        {
            $object_table_action = clone $table_action;
            $object_table_action->set_href(str_replace('%d', $content_object->get_id(), $table_action->get_href()));
            $object_toolbar->add_item($object_table_action);
        }

        return $object_toolbar->as_html();
    }

    protected function get_table_actions()
    {
        return $this->table_actions;
    }

    protected function set_table_actions($table_actions)
    {
        $this->table_actions = $table_actions;
    }
}
?>
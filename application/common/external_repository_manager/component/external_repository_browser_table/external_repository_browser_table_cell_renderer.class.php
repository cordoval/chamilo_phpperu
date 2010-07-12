<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/external_repository_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../table/default_external_repository_object_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ExternalRepositoryBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ExternalRepositoryBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === ExternalRepositoryBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }

        //        switch ($column->get_name())
        //        {
        //            case ContentObject :: PROPERTY_TYPE :
        //                return '<a href="' . htmlentities($this->browser->get_type_filter_url($content_object->get_type())) . '">' . parent :: render_cell($column, $content_object) . '</a>';
        //            case ContentObject :: PROPERTY_TITLE :
        //                $title = parent :: render_cell($column, $content_object);
        //                $title_short = Utilities :: truncate_string($title, 53, false);
        //                return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '" title="' . $title . '">' . $title_short . '</a>';
        //        }
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($content_object)
    {
        $toolbar = new Toolbar();
        return $toolbar->as_html();
    }
}
?>
<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/picasa_external_repository_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../../table/default_external_repository_object_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class PicasaExternalRepositoryTableCellRenderer extends DefaultExternalRepositoryObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function PicasaExternalRepositoryTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $external_object)
    {
        if ($column === PicasaExternalRepositoryTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($external_object);
        }

        switch ($column->get_name())
        {
            case ExternalRepositoryObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $external_object);
                $title_short = Utilities :: truncate_string($title, 50, false);
                return '<a href="' . htmlentities($this->browser->get_external_repository_object_viewing_url($external_object)) . '" title="' . $title . '">' . $title_short . '</a>';
            case PicasaExternalRepositoryObject :: PROPERTY_LICENSE :
                return $external_object->get_license_icon();
        }
        return parent :: render_cell($column, $external_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($external_repository_object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->browser->get_external_repository_object_actions($external_repository_object));
        return $toolbar->as_html();
    }
}
?>
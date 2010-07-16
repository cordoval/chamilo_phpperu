<?php
/**
 * $Id: external_repository_instance_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/external_repository_instance_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../external_repository_instance_table/default_external_repository_instance_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ExternalRepositoryInstanceBrowserTableCellRenderer extends DefaultExternalRepositoryInstanceTableCellRenderer
{
    private $browser;

    /**
     * Constructor
     * @param ExternalRepositoryInstanceManager $browser
     */
    function ExternalRepositoryInstanceBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $external_repository)
    {
        if ($column === ExternalRepositoryInstanceBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($external_repository);
        }
        
        switch ($column->get_name())
        {
            //            case ContentObject :: PROPERTY_TYPE :
        //                return '<a href="' . htmlentities($this->browser->get_type_filter_url($external_repository->get_type())) . '">' . parent :: render_cell($column, $external_repository) . '</a>';
        //            case ContentObject :: PROPERTY_TITLE :
        //                $title = parent :: render_cell($column, $external_repository);
        //                $title_short = Utilities :: truncate_string($title, 53, false);
        //                return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($external_repository)) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        return parent :: render_cell($column, $external_repository);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($external_repository)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_url(array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_UPDATE_INSTANCE, ExternalRepositoryInstanceManager :: PARAM_INSTANCE => $external_repository->get_id())), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_url(array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_DELETE_INSTANCE, ExternalRepositoryInstanceManager :: PARAM_INSTANCE => $external_repository->get_id())), ToolbarItem :: DISPLAY_ICON));
        //        $toolbar->add_items($this->browser->get_content_object_actions($external_repository));
        return $toolbar->as_html();
    }
}
?>
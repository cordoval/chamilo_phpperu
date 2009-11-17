<?php
/**
 * $Id: alexia_publication_browser_table_cell_renderer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexiar.alexiar_manager.component.alexiapublicationbrowser
 */
require_once dirname(__FILE__) . '/alexia_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/alexia_publication_table/default_alexia_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../alexia_manager.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class AlexiaPublicationBrowserTableCellRenderer extends DefaultAlexiaPublicationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param AlexiaManagerBrowserComponent $browser
     */
    function AlexiaPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $alexia_publication)
    {
        if ($column === AlexiaPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($alexia_publication);
        }
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $alexia_publication);
                $title_short = Utilities :: truncate_string($title, 53, false);
                return '<a href="' . htmlentities($alexia_publication->get_publication_object()->get_url()) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        
        return parent :: render_cell($column, $alexia_publication);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $alexia The alexia object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($alexia_publication)
    {
        $toolbar_data = array();
        
        $viewing_url = $this->browser->get_publication_viewing_url($alexia_publication);
        $toolbar_data[] = array('href' => $viewing_url, 'label' => Translation :: get('View'), 'img' => Theme :: get_common_image_path() . 'action_details.png');
        
        if ($this->browser->get_user()->is_platform_admin() || $alexia_publication->get_publisher() == $this->browser->get_user()->get_id())
        {
            $edit_url = $this->browser->get_publication_editing_url($alexia_publication);
            $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            $delete_url = $this->browser->get_publication_deleting_url($alexia_publication);
            $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
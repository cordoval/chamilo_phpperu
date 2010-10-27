<?php
namespace application\alexia;

use common\libraries\WebApplication;
use repository\ContentObject;
use common\libraries\Utilities;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Toolbar;
/**
 * $Id: alexia_publication_browser_table_cell_renderer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexiar.alexiar_manager.component.alexiapublicationbrowser
 */
require_once WebApplication :: get_application_class_lib_path('alexia') . 'alexia_manager/component/alexia_publication_browser/alexia_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('alexia') . 'tables/alexia_publication_table/default_alexia_publication_table_cell_renderer.class.php';

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
        
    	$toolbar = new Toolbar(); 
        
        $viewing_url = $this->browser->get_publication_viewing_url($alexia_publication);
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_details.png', $viewing_url, ToolbarItem :: DISPLAY_ICON));
        
        if ($this->browser->get_user()->is_platform_admin() || $alexia_publication->get_publisher() == $this->browser->get_user()->get_id())
        {
            $edit_url = $this->browser->get_publication_editing_url($alexia_publication);
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $edit_url, ToolbarItem :: DISPLAY_ICON));
            
            $delete_url = $this->browser->get_publication_deleting_url($alexia_publication);
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $delete_url, ToolbarItem :: DISPLAY_ICON, true));
        }
        
        return $toolbar->as_html();
    }
}
?>
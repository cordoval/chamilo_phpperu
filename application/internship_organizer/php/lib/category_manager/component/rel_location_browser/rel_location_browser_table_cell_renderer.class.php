<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\Theme;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/rel_location_browser/rel_location_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/category_rel_location_table/default_category_rel_location_table_cell_renderer.class.php';

class InternshipOrganizerCategoryRelLocationBrowserTableCellRenderer extends DefaultInternshipOrganizerCategoryRelLocationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function InternshipOrganizerCategoryRelLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $categoryrellocation)
    {
        if ($column === InternshipOrganizerCategoryRelLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($categoryrellocation);
        }
        
        //        return parent :: render_cell($column, $region);
        

        // Add special features here
        //        switch ($column->get_name())
        //        {
        //            // Exceptions that need post-processing go here ...
        //            case InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID :
        //               
        //                return $location->get_name();
        //        }
        return parent :: render_cell($column, $categoryrellocation);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($categoryrellocation)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_category_rel_location_unsubscribing_url($categoryrellocation), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }

    function render_id_cell($categoryrellocation)
    {
        return $categoryrellocation->get_category_id().'|'.$categoryrellocation->get_location_id();
    }

}
?>
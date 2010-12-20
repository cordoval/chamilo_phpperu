<?php
namespace application\internship_organizer;

use common\libraries\Toolbar;
use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\ToolbarItem;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/subscribe_location_browser/subscribe_location_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/location_table/default_location_table_cell_renderer.class.php';

class SubscribeLocationBrowserTableCellRenderer extends DefaultInternshipOrganizerLocationTableCellRenderer
{

    private $browser;
    private $category;

    function __construct($browser, $category)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->category = $category;
    }

    // Inherited
    function render_cell($column, $location)
    {
        if ($column === SubscribeLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($location);
        }

        return parent :: render_cell($column, $location);
    }

    function render_id_cell($location)
    {
        return $this->category->get_id() . '|' . $location->get_id();
    }

    /**
     * Gets the action links to display
     * @param Location $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($location)
    {

        $toolbar = new Toolbar();

        $subscribe_url = $this->browser->get_category_rel_location_subscribing_url($this->category, $location);

        $toolbar->add_item(new ToolbarItem(Translation :: get('Subscribe'), Theme :: get_common_image_path() . 'action_subscribe.png', $subscribe_url, ToolbarItem :: DISPLAY_ICON));

        return $toolbar->as_html();
    }
}
?>
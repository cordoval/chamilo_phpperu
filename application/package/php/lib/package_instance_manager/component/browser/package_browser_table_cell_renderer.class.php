<?php
namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Utilities;
/**
 * @package package.tables.package_language_table
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'tables/package_table/default_package_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class PackageBrowserTableCellRenderer extends DefaultPackageTableCellRenderer
{
    /**
     * The browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $package)
    {
        if ($column === PackageBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($package);
        }
        
        switch ($column->get_name())
        {
            case Package :: PROPERTY_NAME :
                return $package->get_name();
            case Package :: PROPERTY_VERSION :
                return $package->get_version();
            case Package :: PROPERTY_DESCRIPTION :
                return $package->get_description();
            case Package :: PROPERTY_CYCLE_PHASE :
                return $package->get_cycle_phase();
            case Package :: PROPERTY_STATUS :
                return $package->get_status();
        }
        
        return parent :: render_cell($column, $package);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($package)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_package_url($package), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_package_url($package), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }
}
?>
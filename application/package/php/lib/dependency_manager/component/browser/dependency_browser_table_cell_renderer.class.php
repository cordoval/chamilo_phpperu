<?php
namespace application\package;

use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Utilities;
/**
 * @package package.tables.package_language_table
 */

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class DependencyBrowserTableCellRenderer extends DefaultDependencyTableCellRenderer
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
        if ($column === DependencyBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($package);
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
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_dependency_url($package), ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_dependency_url($package), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }
}
?>
<?php
namespace application\gradebook;

use common\libraries\Utilities;
use common\libraries\WebApplication;
use common\libraries\Path;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;

require_once WebApplication :: get_application_class_lib_path('gradebook') . 'tables/gradebook_publication_table/default_gradebook_publication_table_cell_renderer.class.php';
require_once Path :: get_common_libraries_class_path() . '/datetime/datetime_utilities.class.php';

class GradebookExternalPublicationBrowserTableCellRenderer extends DefaultGradebookPublicationTableCellRenderer
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
    function render_cell($column, $external_item)
    {
        if ($column === GradebookExternalPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($external_item);
        }

        return parent :: render_cell($column, $external_item);
    }

    /**
     * Gets the action links to display
     * @param Format $evaluation_format The evaluation format for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($external_item)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Browser', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_external_evaluations_on_publications_viewer_url($external_item), ToolbarItem :: DISPLAY_ICON));

        $user = $this->browser->get_user();

        if ($user->is_platform_admin())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_edit_external_evaluation_url($external_item), ToolbarItem :: DISPLAY_ICON));
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_external_evaluation_url($external_item), ToolbarItem :: DISPLAY_ICON, true));
        }

        return $toolbar->as_html();
    }

}

?>
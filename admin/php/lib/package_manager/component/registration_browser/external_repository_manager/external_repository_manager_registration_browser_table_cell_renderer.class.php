<?php
namespace admin;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\PlatformSetting;

use repository\RepositoryManager;

/**
 * $Id: external_repository_manager_registration_browser_table_cell_renderer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component.registration_browser
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/external_repository_manager/external_repository_manager_registration_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/tables/registration_table/default_registration_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ExternalRepositoryManagerRegistrationBrowserTableCellRenderer extends DefaultRegistrationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $registration)
    {
        if ($column === ExternalRepositoryManagerRegistrationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($registration);
        }

        switch ($column->get_name())
        {
            case Registration :: PROPERTY_CATEGORY :
                return Translation :: get(Utilities :: underscores_to_camelcase($registration->get_category()), null, RepositoryManager :: APPLICATION_NAME);
                break;
        }

        return parent :: render_cell($column, $registration);
    }

    function get_browser()
    {
        return $this->browser;
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($registration)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(new ToolbarItem(Translation :: get('ViewRegistration'), Theme :: get_common_image_path() . 'action_details.png', $this->browser->get_registration_view_url($registration), ToolbarItem :: DISPLAY_ICON));

        if (! $registration->is_up_to_date())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Update', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_update.png', $this->browser->get_registration_update_url($registration), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('PackageIsAlreadyUpToDate'), Theme :: get_common_image_path() . 'action_update_na.png', '', ToolbarItem :: DISPLAY_ICON));
        }

        if ($registration->is_active())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Deactivate', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_deactivate.png', $this->browser->get_registration_deactivation_url($registration), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Activate', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_activate.png', $this->browser->get_registration_activation_url($registration), ToolbarItem :: DISPLAY_ICON));

        }

        //		$toolbar->add_item(new ToolbarItem(Translation :: get('Deinstall', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path().'action_deinstall.png',
        //					$this->browser->get_registration_removal_url($registration), ToolbarItem :: DISPLAY_ICON,true));


        return $toolbar->as_html();
    }
}
?>
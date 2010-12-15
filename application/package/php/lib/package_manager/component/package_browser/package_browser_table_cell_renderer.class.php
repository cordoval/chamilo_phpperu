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
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/package_browser/package_browser_table_column_model.class.php';
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
        
        //        if ($this->browser instanceof PackageManagerPackageBrowserComponent)
        //        {
        $can_edit = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
        $can_delete = PackageRights :: is_allowed(PackageRights :: DELETE_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
        
        if ($can_edit)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_package_url($package), ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($can_delete)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_package_url($package), ToolbarItem :: DISPLAY_ICON, true));
        }
        //        }
        //        else
        //        {
        //            $can_translate = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: VIEW_RIGHT, $package->get_id(), 'package_language');
        //            $can_lock = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $package->get_id(), 'package_language');
        //
        //            if ($can_lock)
        //            {
        //                if ($this->browser->can_language_be_locked($package))
        //                {
        //                    $toolbar->add_item(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png', $this->browser->get_lock_language_url($package), ToolbarItem :: DISPLAY_ICON));
        //                }
        //                else
        //                {
        //                    $toolbar->add_item(new ToolbarItem(Translation :: get('LockNa'), Theme :: get_common_image_path() . 'action_lock_na.png', null, ToolbarItem :: DISPLAY_ICON));
        //                }
        //
        //                if ($this->browser->can_language_be_unlocked($package))
        //                {
        //                    $toolbar->add_item(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path() . 'action_unlock.png', $this->browser->get_unlock_language_url($package), ToolbarItem :: DISPLAY_ICON));
        //                }
        //                else
        //                {
        //                    $toolbar->add_item(new ToolbarItem(Translation :: get('UnlockNa'), Theme :: get_common_image_path() . 'action_unlock_na.png', null, ToolbarItem :: DISPLAY_ICON));
        //                }
        //            }
        //
        //            if ($can_translate || $can_lock)
        //            {
        //                if (! $can_lock)
        //                {
        //                    $status = VariableTranslation :: STATUS_NORMAL;
        //                }
        //
        //                $translation = $this->browser->retrieve_first_untranslated_variable_translation($package_language->get_id(), null, $status);
        //
        //                if ($translation)
        //                {
        //                    $toolbar->add_item(new ToolbarItem(Translation :: get('TranslateFirstEmptyTranslation'), Theme :: get_image_path() . 'action_quickstart.png', $this->browser->get_update_variable_translation_url($translation), ToolbarItem :: DISPLAY_ICON));
        //                }
        //            }
        //        }
        

        return $toolbar->as_html();
    }
}
?>
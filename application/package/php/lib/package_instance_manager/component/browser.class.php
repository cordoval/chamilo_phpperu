<?php
namespace application\package;

use common\libraries\WebApplication;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\ToolbarItem;
use common\libraries\ConditionProperty;
use common\libraries\Application;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */
/**
 * package component which allows the user to browse his package_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageInstanceManagerBrowserComponent extends PackageInstanceManager
{
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header();
        echo '<a name="top"></a>';
        echo $this->action_bar->as_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_table();
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $table = new PackageBrowserTable($this, array(Application :: PARAM_APPLICATION => 'package', Application :: PARAM_ACTION => PackageInstanceManager :: ACTION_BROWSE), $this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application :: PARAM_ACTION => PackageInstanceManager :: ACTION_CREATE))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('UpdateList'), Theme :: get_image_path() . 'action_update.png', $this->get_url(array(Application :: PARAM_ACTION => PackageInstanceManager :: ACTION_SYNCHRONIZE))));
        //        if (count($this->get_user_languages(PackageRights :: EDIT_RIGHT)) > 0)
        //        {
        //        	$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageApplications'), Theme :: get_image_path() . 'action_manage.png', $this->get_url(array(Application :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS))));
        //        }
        //
        //        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportTranslations'), Theme :: get_common_image_path() . 'action_export.png', $this->get_export_translations_url()));
        //
        //        if (count($this->get_user_languages(PackageRights :: VIEW_RIGHT)) > 0)
        //        {
        //        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ImportTranslations'), Theme :: get_common_image_path() . 'action_import.png', $this->get_import_variable_translations_url()));
        //        }
        //
        //             $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url()));
        return $action_bar;
    }

    function get_condition()
    {
        $properties[] = new ConditionProperty(Package :: PROPERTY_CODE);
        $properties[] = new ConditionProperty(Package :: PROPERTY_SECTION);
        
        return $this->action_bar->get_conditions($properties);
    }

    //    function get_user_languages($right)
    //    {
    //		$language_location = PackageRights :: get_languages_subtree_root();
    //		$languages = $language_location->get_children();
    //
    //		$available_languages = array();
    //
    //		while ($language = $languages->next_result())
    //		{
    //			$has_right = PackageRights :: is_allowed_in_languages_subtree($right, $language->get_identifier(), $language->get_type());
    //
    //			if ($has_right)
    //			{
    //				$available_languages[] = $language->get_identifier();
    //			}
    //		}
    //
    //		return $available_languages;
    //    }
    

    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('package_browser');
    }
}
?>
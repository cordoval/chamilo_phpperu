<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\ToolbarItem;
use common\libraries\ConditionProperty;
use common\libraries\AdministrationComponent;
use common\libraries\Application;
use common\libraries\Utilities;

/**
 * @package application.package.package.component
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/package_browser/package_browser_table.class.php';

/**
 * package component which allows the user to browse his package_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerAdminPackageBrowserComponent extends PackageManager implements AdministrationComponent
{
	private $actionbar;

	function run()
	{
		$this->actionbar = $this->get_action_bar();

//		$can_edit = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
//		$can_delete = PackageRights :: is_allowed(PackageRights :: DELETE_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
//		$can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
//
//		if (!$can_edit && !$can_delete && !$can_add)
//		{
//		    Display :: not_allowed();
//		}

		$this->display_header();

		echo $this->actionbar->as_html();
		echo $this->get_table();

		$this->display_footer();
	}

	function get_table()
	{
		$table = new PackageBrowserTable($this, array(Application :: PARAM_APPLICATION => 'package', Application :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE), null);
		return $table->as_html();
	}

 	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

//        $can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
//        if ($can_add)
//        {
//            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_package_language_url()));
//        }
//        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url()));

        return $action_bar;
    }

	function get_condition()
    {
    	$properties[] = new ConditionProperty(Package :: PROPERTY_NAME);
    	$properties[] = new ConditionProperty(Package :: PROPERTY_VERSION);

    	return $this->actionbar->get_conditions($properties);
    }
    
    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('package_admin_browser');
    }

}
?>
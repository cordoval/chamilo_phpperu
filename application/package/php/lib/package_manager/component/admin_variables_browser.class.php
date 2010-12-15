<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\ToolbarItem;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\ConditionProperty;
use common\libraries\AndCondition;
use common\libraries\AdministrationComponent;
use common\libraries\Breadcrumb;
use common\libraries\Application;
use common\libraries\Utilities;

/**
 * @package application.package.package.component
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/variable_browser/variable_browser_table.class.php';

/**
 * package component which allows the user to browse his variables
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerAdminVariablesBrowserComponent extends PackageManager implements AdministrationComponent
{
	private $actionbar;

	function run()
	{
		$language_pack_id = Request :: get(PackageManager :: PARAM_LANGUAGE_PACK);

		$this->actionbar = $this->get_action_bar();

		$can_edit = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');
		$can_delete = PackageRights :: is_allowed(PackageRights :: DELETE_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');
		$can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');

		if (!$can_edit && !$can_delete && !$can_add)
		{
		    Display :: not_allowed();
		}

		$this->display_header();

		echo $this->actionbar->as_html();
		echo $this->get_table();

		$this->display_footer();
	}

	function get_table()
	{
		$table = new VariableBrowserTable($this, array(Application :: PARAM_APPLICATION => 'package', Application :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_VARIABLES), $this->get_condition());
		return $table->as_html();
	}

	function get_action_bar()
    {
        $language_pack_id = Request :: get(PackageManager :: PARAM_LANGUAGE_PACK);
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');
        if ($can_add)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_variable_url(Request :: get(PackageManager :: PARAM_LANGUAGE_PACK))));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_admin_browse_variables_url($language_pack_id)));
        $action_bar->set_search_url($this->get_admin_browse_variables_url($language_pack_id));

        return $action_bar;
    }

    function get_condition()
    {
    	$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, Request :: get(PackageManager :: PARAM_LANGUAGE_PACK));

    	$properties[] = new ConditionProperty(Variable :: PROPERTY_VARIABLE);
    	$condition = $this->actionbar->get_conditions($properties);
    	if($condition)
    		$conditions[] = $condition;

    	return new AndCondition($conditions);
    }
    
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('package_admin_variables_browser');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('PackageManagerAdminLanguagePacksBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_LANGUAGE_PACK);
    }
}
?>
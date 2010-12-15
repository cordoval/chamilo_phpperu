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

require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/language_pack_browser/language_pack_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'forms/language_pack_browser_filter_form.class.php';

/**
 * package component which allows the user to browse his language_packs
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerAdminLanguagePacksBrowserComponent extends PackageManager implements AdministrationComponent
{
	private $actionbar;
	private $form;

	function run()
	{
		$this->actionbar = $this->get_action_bar();

		$can_edit = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, PackageRights :: LOCATION_LANGUAGE_PACKS, 'manager');
		$can_delete = PackageRights :: is_allowed(PackageRights :: DELETE_RIGHT, PackageRights :: LOCATION_LANGUAGE_PACKS, 'manager');
		$can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_LANGUAGE_PACKS, 'manager');

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
		$this->form = new LanguagePackBrowserFilterForm($this, $this->get_url());
		$table = new LanguagePackBrowserTable($this, array(Application :: PARAM_APPLICATION => 'package', Application :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS), $this->get_condition());

		$html[] = $this->form->display();
        $html[] = $table->as_html();
        return implode("\n", $html);
	}

	function get_condition()
    {
        $form = $this->form;

        $condition = $form->get_filter_conditions();
        if($condition)
        	$conditions[] = $condition;

        $properties[] = new ConditionProperty(LanguagePack :: PROPERTY_NAME);
    	$ab_condition = $this->actionbar->get_conditions($properties);
    	if($ab_condition)
    		$conditions[] = $ab_condition;

    	if(count($conditions) > 0)
    		return new AndCondition($conditions);
    }

	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_LANGUAGE_PACKS, 'manager');
        if ($can_add)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_language_pack_url()));
        }

        $action_bar->set_search_url($this->get_admin_browse_language_packs_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png',
        	$this->get_admin_browse_language_packs_url()));

        return $action_bar;
    }

    function get_package_language()
    {
    	return null;
    }
    
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('package_admin_language_packs_browser');
    }
}
?>
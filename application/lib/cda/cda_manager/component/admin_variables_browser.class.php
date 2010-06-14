<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/variable_browser/variable_browser_table.class.php';

/**
 * cda component which allows the user to browse his variables
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerAdminVariablesBrowserComponent extends CdaManager
{
	private $actionbar;

	function run()
	{
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);

		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), Translation :: get('BrowseVariables')));
		$this->actionbar = $this->get_action_bar();

		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');
		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');
		$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');

		if (!$can_edit && !$can_delete && !$can_add)
		{
		    Display :: not_allowed($trail);
		}

		$this->display_header($trail);

		echo $this->actionbar->as_html();
		echo $this->get_table();

		$this->display_footer();
	}

	function get_table()
	{
		$table = new VariableBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_VARIABLES), $this->get_condition());
		return $table->as_html();
	}

	function get_action_bar()
    {
        $language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');
        if ($can_add)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddVariable'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_variable_url(Request :: get(CdaManager :: PARAM_LANGUAGE_PACK))));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_admin_browse_variables_url($language_pack_id)));
        $action_bar->set_search_url($this->get_admin_browse_variables_url($language_pack_id));

        return $action_bar;
    }

    function get_condition()
    {
    	$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, Request :: get(CdaManager :: PARAM_LANGUAGE_PACK));

    	$properties[] = new ConditionProperty(Variable :: PROPERTY_VARIABLE);
    	$condition = $this->actionbar->get_conditions($properties);
    	if($condition)
    		$conditions[] = $condition;

    	return new AndCondition($conditions);
    }
}
?>
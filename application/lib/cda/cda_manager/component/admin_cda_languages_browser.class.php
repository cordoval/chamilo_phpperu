<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/cda_language_browser/cda_language_browser_table.class.php';

/**
 * cda component which allows the user to browse his cda_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerAdminCdaLanguagesBrowserComponent extends CdaManagerComponent
{
	private $actionbar;

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AdminBrowseLanguages')));
		$this->actionbar = $this->get_action_bar();

		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, 'cda_language', 'manager');
		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, 'cda_language', 'manager');
		$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, 'cda_language', 'manager');

		if (!$can_edit && !$can_delete && !$can_add)
		{
		    Display :: not_allowed();
		}

		$this->display_header($trail);

		echo $this->actionbar->as_html();
		echo $this->get_table();

		$this->display_footer();
	}

	function get_table()
	{
		$table = new CdaLanguageBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES), null);
		return $table->as_html();
	}

 	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, 'cda_language', 'manager');
        if ($can_add)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddLanguage'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_cda_language_url()));
        }
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url()));

        return $action_bar;
    }

	function get_condition()
    {
    	$properties[] = new ConditionProperty(CdaLanguage :: PROPERTY_ENGLISH_NAME);
    	$properties[] = new ConditionProperty(CdaLanguage :: PROPERTY_ORIGINAL_NAME);

    	return $this->actionbar->get_conditions($properties);
    }

}
?>
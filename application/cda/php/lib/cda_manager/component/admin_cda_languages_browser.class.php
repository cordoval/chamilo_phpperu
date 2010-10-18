<?php
/**
 * @package application.cda.cda.component
 */

require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/cda_language_browser/cda_language_browser_table.class.php';

/**
 * cda component which allows the user to browse his cda_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerAdminCdaLanguagesBrowserComponent extends CdaManager implements AdministrationComponent
{
	private $actionbar;

	function run()
	{
		$this->actionbar = $this->get_action_bar();

		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');
		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');
		$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');

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
		$table = new CdaLanguageBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES), null);
		return $table->as_html();
	}

 	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');
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
    
    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('cda_admin_languages_browser');
    }

}
?>
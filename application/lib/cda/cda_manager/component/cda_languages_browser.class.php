<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/cda_language_browser/cda_language_browser_table.class.php';

/**
 * cda component which allows the user to browse his cda_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerCdaLanguagesBrowserComponent extends CdaManager
{
	private $action_bar;

	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseLanguages')));

		$this->action_bar = $this->get_action_bar();

		$this->display_header($trail);
        echo '<a name="top"></a>';
        echo $this->action_bar->as_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_table();
        echo '</div>';
		$this->display_footer();
	}

	function get_table()
	{
		$table = new CdaLanguageBrowserTable($this,
			array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES),
			$this->get_condition());
		return $table->as_html();
	}

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('HelpTranslating'), Theme :: get_image_path() . 'action_apply.png', $this->get_url(array(Application :: PARAM_ACTION => CdaManager :: ACTION_CREATE_TRANSLATOR_APPLICATION))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AdvancedSearch'), Theme :: get_common_image_path() . 'action_search.png', $this->get_variable_translations_searcher_url()));
        if (count($this->get_user_languages(CdaRights :: EDIT_RIGHT)) > 0)
        {
        	$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageApplications'), Theme :: get_image_path() . 'action_manage.png', $this->get_url(array(Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS))));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportTranslations'), Theme :: get_common_image_path() . 'action_export.png', $this->get_export_translations_url()));

        if (count($this->get_user_languages(CdaRights :: VIEW_RIGHT)) > 0)
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ImportTranslations'), Theme :: get_common_image_path() . 'action_import.png', $this->get_import_variable_translations_url()));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url()));
        return $action_bar;
    }

    function get_condition()
    {
    	$properties[] = new ConditionProperty(CdaLanguage :: PROPERTY_ENGLISH_NAME);
    	$properties[] = new ConditionProperty(CdaLanguage :: PROPERTY_ORIGINAL_NAME);

    	return $this->action_bar->get_conditions($properties);
    }

    function get_user_languages($right)
    {
		$language_location = CdaRights :: get_languages_subtree_root();
		$languages = $language_location->get_children();

		$available_languages = array();

		while ($language = $languages->next_result())
		{
			$has_right = CdaRights :: is_allowed_in_languages_subtree($right, $language->get_identifier(), $language->get_type());

			if ($has_right)
			{
				$available_languages[] = $language->get_identifier();
			}
		}

		return $available_languages;
    }
}
?>
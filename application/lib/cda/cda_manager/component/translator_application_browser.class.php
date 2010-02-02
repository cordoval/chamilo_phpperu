<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/translator_application_browser/translator_application_browser_table.class.php';

/**
 * cda component which allows the user to browse his variable_translations
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerTranslatorApplicationBrowserComponent extends CdaManagerComponent
{
	private $user_languages;
	private $action_bar;
	
	function run()
	{
		$this->user_languages = $this->get_user_languages();
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb('#', Translation :: get('ManageTranslatorApplications')));
		
		$this->action_bar = $this->get_action_bar();
		
		if (count($this->user_languages) == 0)
		{
			Display :: not_allowed();
		}

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
		$table = new TranslatorApplicationBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS), 
				$this->get_condition());
			
		return $table->as_html();
	}
	
	function get_condition()
	{		
		$user = $this->get_user();
		
		if (!$user->is_platform_admin())
		{
			$condition = new InCondition(TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID, $this->user_languages, TranslatorApplication :: get_table_name());
		}
		
		$query = $this->action_bar->get_query();
		
		if($query && $query != '')
		{
			$subconditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', User :: get_table_name());
			$subconditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', User :: get_table_name());
			$subcondition = new OrCondition($subconditions);
			$conditions[] = new SubSelectCondition(TranslatorApplication :: PROPERTY_USER_ID,
												   User :: PROPERTY_ID, 'user_' . User :: get_table_name(), $subcondition);
			
			$subcondition = new PatternMatchCondition(CdaLanguage :: PROPERTY_ENGLISH_NAME, '*' . $query . '*', CdaLanguage :: get_table_name());
			$conditions[] = new SubSelectCondition(TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID,
												   CdaLanguage :: PROPERTY_ID, 'cda_' . CdaLanguage :: get_table_name(), $subcondition);
			$conditions[] = new SubSelectCondition(TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID,
												   CdaLanguage :: PROPERTY_ID, 'cda_' . CdaLanguage :: get_table_name(), $subcondition);
			
			$orcondition = new OrCondition($conditions);
			
			if(!$condition)
			{
				$condition = $orcondition;	
			}
			else
			{
				$conditions = array();
				$conditions[] = $condition;
				$conditions[] = $orcondition;
				$condition = new AndCondition($conditions);
			}
		}
		
		return $condition;

	}
	
 	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url()));
        
        return $action_bar;
    }
    
    function get_user_languages()
    {
		$language_location = CdaRights :: get_location_by_identifier('manager', 'cda_language');
		$languages = $language_location->get_children();
		
		$available_languages = array();
		
		while ($language = $languages->next_result())
		{
			$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $language->get_identifier(), $language->get_type());
			
			if ($can_edit)
			{
				$available_languages[] = $language->get_identifier();
			}
		}
		
		return $available_languages;
    }
}
?>
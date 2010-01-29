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
 * @author 
 */
class CdaManagerTranslatorApplicationBrowserComponent extends CdaManagerComponent
{
	private $user_languages;

	function run()
	{
		$this->user_languages = $this->get_user_languages();
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb('#', Translation :: get('ManageTranslatorApplications')));
		
		if (count($this->user_languages) == 0)
		{
			Display :: not_allowed();
		}

		$this->display_header($trail);
		echo '<a name="top"></a>';
        echo $this->get_action_bar_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_table();
        echo '</div>';
		$this->display_footer();
	}

	function get_table()
	{
		$table = new TranslatorApplicationBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS), $this->get_condition());
			
		return $table->as_html();
	}
	
	function get_condition()
	{		
		$user = $this->get_user();
		
		if ($user->is_platform_admin())
		{
			return new InCondition(TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID, $this->user_languages);
		}
		else
		{
			return null;
		}
	}
	
 	function get_action_bar_html()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        return $action_bar->as_html();
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
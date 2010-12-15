<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\WebApplication;
use common\libraries\Application;
use common\libraries\InCondition;
use user\User;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\SubselectCondition;
use common\libraries\AndCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translator_application_browser/translator_application_browser_table.class.php';

/**
 * package component which allows the user to browse his variable_translations
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerTranslatorApplicationBrowserComponent extends PackageManager
{
	private $user_languages;
	private $action_bar;
	
	function run()
	{
		$this->user_languages = $this->get_user_languages();
		
		$this->action_bar = $this->get_action_bar();
		
		if (count($this->user_languages) == 0 && !$this->get_user()->is_platform_admin())
		{
			Display :: not_allowed();
		}

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
		$table = new TranslatorApplicationBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'package', Application :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS), 
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
			$conditions[] = new SubselectCondition(TranslatorApplication :: PROPERTY_USER_ID,
												   User :: PROPERTY_ID, User :: get_table_name(), $subcondition, null, UserDataManager :: get_instance());
			
			$subcondition = new PatternMatchCondition(PackageLanguage :: PROPERTY_ENGLISH_NAME, '*' . $query . '*', PackageLanguage :: get_table_name());
			$conditions[] = new SubselectCondition(TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID,
												   PackageLanguage :: PROPERTY_ID, PackageLanguage :: get_table_name(), $subcondition);
			$conditions[] = new SubselectCondition(TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID,
												   PackageLanguage :: PROPERTY_ID, PackageLanguage :: get_table_name(), $subcondition);
			
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
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url()));
        
        return $action_bar;
    }
    
    function get_user_languages()
    {
		$language_location = PackageRights :: get_languages_subtree_root();
		$languages = $language_location->get_children();
		
		$available_languages = array();
		
		while ($language = $languages->next_result())
		{
			$can_edit = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $language->get_identifier(), $language->get_type());
			
			if ($can_edit)
			{
				$available_languages[] = $language->get_identifier();
			}
		}
		
		return $available_languages;
    }
    
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add_help('package_languages_application_browser');
    }
}
?>
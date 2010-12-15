<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\WebApplication;
use common\libraries\Application;
use common\libraries\EqualityCondition;
use common\libraries\SubselectCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\AndCondition;
use common\libraries\ToolbarItem;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'forms/variable_translation_browser_filter_form.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/variable_translation_browser/variable_translation_browser_table.class.php';

/**
 * package component which allows the user to browse his variable_translations
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariableTranslationsBrowserComponent extends PackageManager
{
	private $action_bar;
	private $form;
	
	function run()
	{
		$language_id = Request :: get(PackageManager :: PARAM_PACKAGE_LANGUAGE);
		$language_pack_id = Request :: get(PackageManager :: PARAM_LANGUAGE_PACK);
		$language_pack = PackageDataManager :: get_instance()->retrieve_language_pack($language_pack_id);
		
		$this->action_bar = $this->get_action_bar();
		$this->form = new VariableTranslationBrowserFilterForm($this, $this->get_browse_variable_translations_url($language_id, $language_pack_id));
		
		$this->display_header();
		echo '<a name="top"></a>';
        echo $this->action_bar->as_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->form->display();
        echo $this->get_table($language_id, $language_pack_id);
        echo '</div>';
		$this->display_footer();
	}

	function get_table($language_id, $language_pack_id)
	{
		$table = new VariableTranslationBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'package', Application :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS,
				  PackageManager :: PARAM_PACKAGE_LANGUAGE => $language_id, PackageManager :: PARAM_LANGUAGE_PACK => $language_pack_id), 
			$this->get_condition());
			
		return $table->as_html();
	}
	
	function get_condition()
	{
		$language_id = Request :: get(PackageManager :: PARAM_PACKAGE_LANGUAGE);
		$language_pack_id = Request :: get(PackageManager :: PARAM_LANGUAGE_PACK);
		
		$form = $this->form;

        $condition = $form->get_filter_conditions();
        if($condition)
        	$conditions[] = $condition;
		
		$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, Variable :: get_table_name(), $subcondition);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		
		$query = $this->action_bar->get_query();
    	
    	if($query && $query != '')
    	{
    		$or_conditions[] = new PatternMatchCondition(VariableTranslation :: PROPERTY_TRANSLATION, '*' . $query . '*');
    		$subcondition =  new PatternMatchCondition(Variable :: PROPERTY_VARIABLE, '*' . $query . '*');
			$or_conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, Variable :: get_table_name(), $subcondition);
    		$conditions[] = new OrCondition($or_conditions);
    	}
	
		return new AndCondition($conditions);
	}
	
 	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $package_language_id = Request :: get(PackageManager :: PARAM_PACKAGE_LANGUAGE);
        $language_pack = $this->retrieve_language_pack(Request :: get(PackageManager :: PARAM_LANGUAGE_PACK));
        
        $action_bar->set_search_url($this->get_browse_variable_translations_url($package_language_id, $language_pack->get_id()));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png',
        	$this->get_browse_variable_translations_url($package_language_id, $language_pack->get_id())));
        
        $can_lock = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $package_language_id, 'package_language');
        
        if ($can_lock)
        {
	    	if($this->can_language_pack_be_locked($language_pack, $package_language_id))
	        {
				$action_bar->add_common_action(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png', 
					$this->get_lock_language_pack_url($language_pack, $package_language_id)));
	        }
//	        else
//	        {
//				$action_bar->add_common_action(new ToolbarItem(Translation :: get('LockNa'), Theme :: get_common_image_path() . 'action_lock_na.png'));
//	        }
	        
	        if($this->can_language_pack_be_unlocked($language_pack, $package_language_id))
	        {
				$action_bar->add_common_action(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path() . 'action_unlock.png', 
					$this->get_unlock_language_pack_url($language_pack, $package_language_id)));
	        }
//	        else
//	        {
//				$action_bar->add_common_action(new ToolbarItem(Translation :: get('UnlockNa'), Theme :: get_common_image_path() . 'action_unlock_na.png'));
//	        }
        }
        
        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_LANGUAGE_PACKS, PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add_help('package_variable_translations_browser');
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_PACKAGE_LANGUAGE, PackageManager :: PARAM_LANGUAGE_PACK);
    }

}
?>
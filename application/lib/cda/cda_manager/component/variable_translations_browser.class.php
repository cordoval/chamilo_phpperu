<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/variable_translation_browser/variable_translation_browser_table.class.php';

/**
 * cda component which allows the user to browse his variable_translations
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableTranslationsBrowserComponent extends CdaManagerComponent
{

	function run()
	{
		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
		$language_pack = CdaDataManager :: get_instance()->retrieve_language_pack($language_pack_id);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb($this->get_browse_language_packs_url($language_id), CdaDataManager :: get_instance()->retrieve_cda_language($language_id)->get_original_name()));
		
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $language_id, CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), $language_pack->get_branch_name() . ' - ' . $language_pack->get_name()));
		$trail->add(new Breadcrumb('#', Translation :: get('BrowseVariableTranslations')));

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
		$table = new VariableTranslationBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS), 
			$this->get_condition());
			
		return $table->as_html();
	}
	
	function get_condition()
	{
		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
		
		$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'cda_' . Variable :: get_table_name(), $subcondition);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		
		return new AndCondition($conditions);
	}
	
 	function get_action_bar_html()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $cda_language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
        
        $can_lock = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $cda_language_id, 'cda_language');
        
        if ($can_lock)
        {
	        $language_pack = $this->retrieve_cda_language(Request :: get(CdaManager :: PARAM_LANGUAGE_PACK));
	        
	    	if($this->can_language_pack_be_locked($language_pack, $cda_language_id))
	        {
				$action_bar->add_common_action(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png', 
					$this->get_lock_language_pack_url($language_pack, $cda_language_id)));
	        }
//	        else
//	        {
//				$action_bar->add_common_action(new ToolbarItem(Translation :: get('LockNa'), Theme :: get_common_image_path() . 'action_lock_na.png'));
//	        }
	        
	        if($this->can_language_pack_be_unlocked($language_pack, $cda_language_id))
	        {
				$action_bar->add_common_action(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path() . 'action_unlock.png', 
					$this->get_unlock_language_pack_url($language_pack, $cda_language_id)));
	        }
//	        else
//	        {
//				$action_bar->add_common_action(new ToolbarItem(Translation :: get('UnlockNa'), Theme :: get_common_image_path() . 'action_unlock_na.png'));
//	        }
        }
        
        return $action_bar->as_html();
    }

}
?>
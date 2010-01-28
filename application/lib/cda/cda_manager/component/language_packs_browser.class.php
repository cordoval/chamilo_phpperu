<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/language_pack_browser/language_pack_browser_table.class.php';
require_once dirname(__FILE__) . '/../../forms/language_pack_browser_filter_form.class.php';

/**
 * cda component which allows the user to browse his language_packs
 * @author Sven Vanpoucke
 * @author
 */

class CdaManagerLanguagePacksBrowserComponent extends CdaManagerComponent
{
	private $form;
	private $action_bar;

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(CdaManager :: PARAM_CDA_LANGUAGE))), CdaDataManager :: get_instance()->retrieve_cda_language($this->get_cda_language())->get_original_name()));
		$trail->add(new Breadcrumb('#', Translation :: get('BrowseLanguagePacks')));

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
		$this->form = new LanguagePackBrowserFilterForm($this, $this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(CdaManager :: PARAM_CDA_LANGUAGE))));
		$table = new LanguagePackBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', 
					Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_LANGUAGE_PACKS,
					CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(CdaManager :: PARAM_CDA_LANGUAGE)), $this->get_condition());
	
		$html[] = $this->form->display();
        $html[] = $table->as_html();
        return implode("\n", $html);
	}

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $cda_language = $this->retrieve_cda_language(Request :: get(CdaManager :: PARAM_CDA_LANGUAGE));
        
        $action_bar->set_search_url($this->get_browse_language_packs_url($cda_language->get_id()));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', 
        	$this->get_browse_language_packs_url($cda_language->get_id())));
        
    	if($this->can_language_be_locked($cda_language))
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png', 
				$this->get_lock_language_url($cda_language)));
        }
        else
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('LockNa'), Theme :: get_common_image_path() . 'action_lock_na.png'));
        }
        
        if($this->can_language_be_unlocked($cda_language))
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path() . 'action_unlock.png', 
				$this->get_unlock_language_url($cda_language)));
        }
        else
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('UnlockNa'), Theme :: get_common_image_path() . 'action_unlock_na.png'));
        }
        
        return $action_bar;
    }
    
    function get_condition()
    {
        $form = $this->form;

        $condition = $form->get_filter_conditions();
        
    	$query = $this->action_bar->get_query();
    	
    	if($query && $query != '')
    	{
    		if($condition)
    			$conditions[] = $condition;
    			
    		$conditions[] = new PatternMatchCondition(LanguagePack :: PROPERTY_NAME, '*' . $query . '*');
    		$condition = new AndCondition($conditions);
    	}
    	
    	return $condition;
        
    }
    
    function get_cda_language()
    {
    	return Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
    }
}
?>
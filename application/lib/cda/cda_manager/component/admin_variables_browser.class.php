<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/variable_browser/variable_browser_table.class.php';

/**
 * cda component which allows the user to browse his variables
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerAdminVariablesBrowserComponent extends CdaManagerComponent
{
	private $actionbar;
	
	function run()
	{
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), Translation :: get('BrowseVariables')));
		$this->actionbar = $this->get_action_bar();
		
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
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
        
        $action_bar->set_search_url($this->get_admin_browse_variables_url($language_pack_id));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddVariable'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_variable_url(Request :: get(CdaManager :: PARAM_LANGUAGE_PACK))));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_admin_browse_variables_url($language_pack_id)));
        
        return $action_bar;
    }
    
    function get_condition()
    {
    	$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, Request :: get(CdaManager :: PARAM_LANGUAGE_PACK));
    	
    	$query = $this->actionbar->get_query();
    	
    	if($query && $query != '')
    	{
    		$conditions[] = new PatternMatchCondition(Variable :: PROPERTY_VARIABLE, '*' . $query . '*');
    	}
    	
    	return new AndCondition($conditions);
    }
}
?>
<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/competency_browser/competency_browser_table.class.php';
/**
 * Competency component
 * This class is named browser.class.php and not competency_browser.class.php, because when you click on the 
 * CBA application link in the main menu this is the first screen you'll see.
 * 
 * @author Nick Van Loocke
 */
class CbaManagerBrowserComponent extends CbaManagerComponent
{
	
	private $action_bar;
	
	function run()
	{		       			
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseCompetency')));
		$this->display_header($trail, false, true);
		
		$this->action_bar = $this->get_action_bar();
		echo '<div style="float: right; width: 100%;">';
		echo $this->action_bar->as_html();
		echo '<br />';
		echo $this->get_table();
		echo '</div>';
		
		$this->display_footer();
	}
	
	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url()); 
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_COMPETENCY)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(Application :: PARAM_ACTION => CbaManager :: ACTION_MANAGE_CATEGORIES_COMPETENCY)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));       
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		 
        return $action_bar;
    }
    
	function get_table()
	{
		$condition = $this->get_condition();
		$table = new CompetencyBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cba', Application :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY), $condition);
		return $table->as_html();
	}
	
	private function get_condition()
    {
        $conditions[] = new EqualityCondition(Competency :: PROPERTY_STATE, Competency :: STATE_NORMAL);
        $conditions[] = new EqualityCondition(Competency :: PROPERTY_OWNER_ID, $this->get_user_id());
        $conditions[] = new EqualityCondition(Competency :: PROPERTY_PARENT_ID, $this->get_parent_id());
		
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query);
            $or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query);

            $conditions[] = new OrCondition($or_conditions);
        }

        $condition = new AndCondition($conditions);
        return $condition;
    }
	
	private function get_category()
    {
        return Request :: get(CbaManager :: PARAM_COMPETENCY) ? Request :: get(CbaManager :: PARAM_COMPETENCY) : 0;
    }
    
	private function get_parent_id()
    {
        return Request :: get(CbaManager :: PARAM_COMPETENCY) ? Request :: get(CbaManager :: PARAM_COMPETENCY) : 0;
    }
	
	
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}
}
?>
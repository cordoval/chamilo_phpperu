<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/criteria_browser/criteria_browser_table.class.php';
/**
 * Criteria component
 *
 * @author Nick Van Loocke
 */
class CbaManagerCriteriaBrowserComponent extends CbaManagerComponent
{

	function run()
	{		
		$newbreadcrumb = 'Criteria';
		$this->display_header($trail, false, true, $newbreadcrumb);
	
		
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
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_CRITERIA)), ToolbarItem :: DISPLAY_ICON_AND_LABEL)); 
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(Application :: PARAM_ACTION => CbaManager :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));  
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $action_bar->set_search_url($this->get_url());
        
        return $action_bar;
    }
    
	function get_table()
	{
		$table = new CriteriaBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cba', Application :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA), null);
		return $table->as_html();
	}
	
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}

}
?>
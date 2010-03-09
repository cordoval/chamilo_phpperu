<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/competency_browser/competency_browser_table.class.php';
/**
 * @author Nick Van Loocke
 */
class CbaManagerBrowserComponent extends CbaManagerComponent
{

	function run()
	{		
		$new = 'competency';
		$this->display_header($trail, false, true, $new);

		
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
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(Application :: PARAM_ACTION => CbaManager :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateCompetency'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_COMPETENCY)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ExportEntireRepository'), Theme :: get_common_image_path() . 'action_backup.png', $this->get_url(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_EXPORT_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => 'all')), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        //$action_bar->set_search_url($this->get_url(array('category' => $this->get_parent_id())));       
        return $action_bar;
    }
    
	function get_table()
	{
		$table = new CompetencyBrowserTable($this, array(Application :: PARAM_APPLICATION => 'competency', Application :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY), null);
		//$table = new CbaBrowserTable($this, array(Application :: PARAM_APPLICATION => 'competency', Application :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY), null);
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
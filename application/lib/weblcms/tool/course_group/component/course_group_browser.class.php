<?php
/**
 * $Id: course_group_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__) . '/../course_group_menu.class.php';
require_once dirname(__FILE__) . '/course_group_table/course_group_table.class.php';
require_once dirname(__FILE__) . '/course_group_table/default_course_group_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/course_group_table/default_course_group_table_column_model.class.php';
require_once dirname(__FILE__) . '/course_group_table/course_group_table_data_provider.class.php';

class CourseGroupToolBrowserComponent extends CourseGroupToolComponent
{
    private $action_bar;
    private $introduction_text;
    private $group;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'course_group');

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition);
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();

        $this->action_bar = $this->get_action_bar();

        $trail = new BreadcrumbTrail();
        $trail->add_help('courses group');

        $this->display_header($trail, true);

        //echo '<br /><a name="top"></a>';

        if ($this->get_course()->get_intro_text())
        {
            echo $this->display_introduction_text();
        }

        echo $this->action_bar->as_html();
        
        echo $this->get_menu_html();
        echo $this->get_table_html();
        
        $this->display_footer();
    }
    
	function get_menu_html()
    {
        $group_menu = new CourseGroupMenu($this->get_course(), $this->get_group_id());
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $group_menu->render_as_tree();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }
    
	function get_table_html()
    {
        //$table = new GroupBrowserTable($this, $this->get_parameters(), $this->get_condition());
        $course_group_table = new CourseGroupTable($this, new CourseGroupTableDataProvider($this));
        
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $course_group_table->as_html();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $param_add_course_group[Tool :: PARAM_ACTION] = CourseGroupTool :: ACTION_ADD_COURSE_GROUP;
        $param_add_course_group[WeblcmsManager :: PARAM_COURSE_GROUP] = $this->get_group_id();
        if ($this->is_allowed(ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url($param_add_course_group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if (! $this->introduction_text && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

    function get_condition()
    {
    	$conditions = array();
    	
    	if($this->get_group_id())
    	{
    		$group_id = $this->get_group_id();
    	}
    	else
    	{
    		$root_course_group = WeblcmsDataManager :: get_instance()->retrieve_course_group_root($this->get_course()->get_id());
    		$group_id = $root_course_group->get_id();
    	}
    	
    	$conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_PARENT_ID, $group_id);
    	
    	$properties = array();
    	$properties[] = new ConditionProperty(CourseGroup :: PROPERTY_NAME);
    	$properties[] = new ConditionProperty(CourseGroup :: PROPERTY_DESCRIPTION);
    	$query_condition = $this->action_bar->get_conditions($properties);

    	if($query_condition)
    	{
    		$conditions[] = $query_condition;
    	} 
    	
    	if(count($conditions)  > 0)
    	{
    		return new AndCondition($conditions);
    	}
        
    }

    function display_introduction_text()
    {
        $html = array();

        $introduction_text = $this->introduction_text;

        if ($introduction_text)
        {

            $tb_data[] = array('href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);

            $tb_data[] = array('href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);

            $html[] = '<div class="content_object">';
            $html[] = '<div class="description">';
            $html[] = $introduction_text->get_content_object()->get_description();
            $html[] = '</div>';
            $html[] = Utilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '<br />';
        }

        return implode("\n", $html);
    }
    
    function get_group_id()
    {
    	return Request :: get(WeblcmsManager :: PARAM_COURSE_GROUP);
    }
}
?>
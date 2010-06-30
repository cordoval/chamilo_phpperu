<?php
class HomeToolViewerComponent extends HomeTool
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, null);
        $this->set_parameter('tool_action', null);
        $this->set_parameter('course_group', null);

        $title = CourseLayout :: get_title($this->get_course());
        $trail->add_help('courses general');

        $wdm = WeblcmsDataManager :: get_instance();

        $this->display_header($trail, false, true);

        //Display menu
        $menu_style = $this->get_course()->get_menu();
        if ($menu_style != CourseLayout :: MENU_OFF)
        {
            $renderer = ToolListRenderer :: factory('Menu', $this);
            if($renderer->display())
            {
            	echo '<div id="tool_browser_' . ($renderer->display_menu_icons() && ! $renderer->display_menu_text() ? 'icon_' : '') . $renderer->get_menu_style() . '">';
            }
            else
            {
            	echo '<div id="tool_browser">';
            }
        }
        else
        {
            echo '<div id="tool_browser">';
        }
        
        $tool_shortcut = $this->get_course()->get_tool_shortcut();
        
        if (($this->get_course()->get_intro_text() && !$this->get_introduction_text())  || $tool_shortcut == CourseLayout :: TOOL_SHORTCUT_ON)
        {
        	echo '<div style="border-bottom: 1px dotted #D3D3D3; margin-bottom: 1em; padding-bottom: 2em;">';
        }
        
        if ($this->get_course()->get_intro_text())
        {
        	$introduction_text = $this->display_introduction_text($this->get_introduction_text());
            if (! $introduction_text)
            {
                if ($this->is_allowed(EDIT_RIGHT))
                {
                    $toolbar = new Toolbar();
                    $toolbar->add_item(new ToolbarItem(Translation :: get('PublishIntroductionText'), null, $this->get_url(array(Tool :: PARAM_ACTION => HomeTool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_LABEL));
                    echo '<div style="float: left;">';
                    echo $toolbar->as_html();
                    echo '</div>';
                }
            }
        }
        
        if($tool_shortcut == CourseLayout :: TOOL_SHORTCUT_ON)
        {
        	$renderer = ToolListRenderer :: factory('Shortcut', $this);
        	echo '<div style="float:right;">';
            $renderer->display();
            echo '</div>';
        }
        
        if ($this->get_course()->get_intro_text() || $tool_shortcut == CourseLayout :: TOOL_SHORTCUT_ON)
        {
        	echo '</div>';
        }

        echo '<div class="clear"></div>';
        
        if($introduction_text)
        {
        	echo $introduction_text;
        }
        
        $renderer = ToolListRenderer :: factory('FixedLocation', $this);
        $renderer->display();
        echo '</div>';
        $this->display_footer();
        $wdm->log_course_module_access($this->get_course_id(), $this->get_user_id(), 'course_home');
    }

    function get_registered_tools()
    {
        return $this->get_parent()->get_registered_tools();
    }

    function tool_has_new_publications($tool_name)
    {
        return $this->get_parent()->tool_has_new_publications($tool_name);
    }
    
	function get_introduction_text()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $this->get_tool_id());

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($condition);
        return $publications->next_result();
    }
}
?>
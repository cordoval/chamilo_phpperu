<?php
class HomeToolViewerComponent extends HomeTool
{
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $title = CourseLayout :: get_title($this->get_course());
        $trail->add_help('courses general');
        
        $tools = $this->get_visible_tools();
        $this->display_header();

        echo $this->display_introduction_text($this->get_introduction_text());
        
        $renderer = ToolListRenderer :: factory(ToolListRenderer :: TYPE_FIXED, $this, $tools);
        $renderer->display();
        echo '</div>';
        $this->display_footer();
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
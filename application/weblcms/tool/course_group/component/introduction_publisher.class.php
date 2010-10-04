<?php

class CourseGroupToolIntroductionPublisherComponent extends CourseGroupTool 
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('CourseGroupToolBrowserComponent')));

        $breadcrumbtrail->add_help('weblcms_course_group_introduction_publisher');
    }

}

?>
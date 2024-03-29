<?php
namespace application\weblcms\tool\home;

use application\weblcms\Tool;
use application\weblcms\CourseLayout;
use application\weblcms\ToolListRenderer;
use common\libraries\BreadcrumbTrail;

class HomeToolBrowserComponent extends HomeTool
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $title = CourseLayout :: get_title($this->get_course());

        $tools = $this->get_visible_tools();
        $this->display_header($tools, true);

        echo $this->display_introduction_text($this->get_introduction_text());

        $renderer = ToolListRenderer :: factory(ToolListRenderer :: TYPE_FIXED, $this, $tools);
        $renderer->display();
        echo '</div>';
        $this->display_footer();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_home_browser');
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }

}

?>
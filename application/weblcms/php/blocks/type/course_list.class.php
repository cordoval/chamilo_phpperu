<?php
/**
 * $Id: course_list.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.block
 */
require_once WebApplication :: get_application_class_path('weblcms') . 'blocks/weblcms_block.class.php';
require_once dirname(__FILE__) . '/../course/course_user_category.class.php';
require_once dirname(__FILE__) . '/../tool/tool.class.php';
require_once dirname(__FILE__) . '/../course/course_list_renderer/course_list_renderer.class.php';
/**
 * This class represents a calendar repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class WeblcmsCourseList extends WeblcmsBlock
{

    /*
	 * Inherited
	 */
    function as_html()
    {
        $html = array();
        $html[] = $this->display_header();

        $renderer = new CourseListRenderer($this);
        $renderer->show_new_publication_icons();
        $html[] = $renderer->as_html();

        $html[] = $this->display_footer();

        return implode("\n", $html);
    }

    /**
     * We need to override this because else we would redirect to the home page
     * @param $parameters
     */
    function get_link($parameters)
    {
        return $this->get_parent()->get_link($parameters);
    }
}
?>
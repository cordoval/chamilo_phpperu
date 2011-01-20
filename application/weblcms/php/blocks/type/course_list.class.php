<?php
namespace application\weblcms;

use common\libraries\WebApplication;
use common\libraries\Redirect;

/**
 * $Id: course_list.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.block
 */
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
        return $this->get_parent()->get_link($parameters, null, null, Redirect :: TYPE_APPLICATION);
    }
}
?>
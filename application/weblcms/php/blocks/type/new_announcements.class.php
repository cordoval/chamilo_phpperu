<?php
namespace application\weblcms;

use repository\ContentObject;
use common\libraries\SubselectCondition;
use repository\RepositoryDataManager;
use common\libraries\WebApplication;
use common\libraries\ObjectTableOrder;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Path;
use common\libraries\Translation;
use repository\content_object\announcement\Announcement;

/**
 * $Id: new_announcements.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.block
 */
require_once WebApplication :: get_application_class_path('weblcms') . 'blocks/weblcms_block.class.php';
require_once Path :: get_repository_content_object_path() . 'announcement/php/announcement.class.php';
require_once WebApplication :: get_application_class_path('weblcms') . 'lib/course/course_user_category.class.php';
/**
 * This class represents a calendar repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class WeblcmsNewAnnouncements extends WeblcmsBlock
{

    /*
	 * Inherited
	 */
    function as_html()
    {
        $html = array();

        $html[] = $this->display_header();

        $dm = WeblcmsDataManager :: get_instance();
        $weblcms = $this->get_parent();

        $condition = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id(), CourseUserRelation :: get_table_name());
        $courses = $dm->retrieve_user_courses($condition);

        $items = array();

        while ($course = $courses->next_result())
        {
            $last_visit_date = $dm->get_last_visit_date($course->get_id(), $this->get_user_id(), 'announcement', 0);

            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'announcement');
            $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Announcement :: get_type_name());
            $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
            $condition = new AndCondition($conditions);

            $publications = $dm->retrieve_content_object_publications($condition, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));

            while ($publication = $publications->next_result())
            {
                if ($publication->get_publication_date() >= $last_visit_date)
                {
                    $items[] = array('course' => $course->get_id(), 'title' => $publication->get_content_object()->get_title(), 'id' => $publication->get_id());
                }
            }
        }
        $html[] = $this->display_new_items($items);
        $html[] = $this->display_footer();

        return implode("\n", $html);
    }

    function display_new_items($items)
    {
        $weblcms = $this->get_parent();

        $html = array();

        if (count($items) > 0)
        {
            $html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
            foreach ($items as $item)
            {

                $html[] = '<li><a href="' . htmlspecialchars($weblcms->get_link(array('go' => 'courseviewer', 'application' => 'weblcms', 'tool' => 'announcement', 'tool_action' => 'view', Tool :: PARAM_PUBLICATION_ID => $item['id'], 'course' => $item['course']))) . '">' . htmlspecialchars($item['title']) . '</a>';
                $html[] = '</li>';
            }
            $html[] = '</ul>';
        }
        else
        {
            $html[] = htmlspecialchars(Translation :: get('NoNewAnnouncements'));
        }
        return implode($html, "\n");
    }
}
?>

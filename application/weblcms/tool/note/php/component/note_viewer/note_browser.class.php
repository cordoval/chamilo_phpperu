<?php
namespace application\weblcms\tool\note;

use application\weblcms\ContentObjectPublicationCourseGroup;
use application\weblcms\ContentObjectPublicationUser;
use application\weblcms\ListContentObjectPublicationListRenderer;
use application\weblcms\ContentObjectPublicationBrowser;
use application\weblcms\CourseGroup;
use application\weblcms\ContentObjectPublication;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsRights;
use application\weblcms\Tool;
use application\weblcms\ContentObjectPublicationDetailsRenderer;
use application\weblcms\ContentObjectPublicationListRenderer;

use common\libraries\SubselectCondition;
use common\libraries\OrCondition;
use common\libraries\InCondition;
use common\libraries\ObjectTableOrder;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\ObjectTableFormAction;
use common\libraries\Utilities;

use repository\content_object\note\Note;
use repository\RepositoryDataManager;
use repository\ContentObject;

use user\User;
/**
 * $Id: note_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.note.component.note_viewer
 */
require_once Path :: get_repository_content_object_path() . 'note/php/note.class.php';
require_once Path :: get_repository_content_object_path() . 'description/php/description.class.php';
/**
 * Browser to allow the user to view the published notes
 */
class NoteBrowser extends ContentObjectPublicationBrowser
{
    /**
     * @see ContentObjectPublicationBrowser::ContentObjectPublicationBrowser()
     */
    private $publications;

    function __construct($parent)
    {
        parent :: __construct($parent, 'note');
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID) && $parent->get_action() == 'view')
        {
            $this->set_publication_id(Request :: get(Tool :: PARAM_PUBLICATION_ID));
            $parent->set_parameter(Tool :: PARAM_ACTION, NoteTool :: ACTION_VIEW_NOTES);
            $renderer = new ContentObjectPublicationDetailsRenderer($this);
        }
        else
        {
            $renderer = new ListContentObjectPublicationListRenderer($this);
            //$actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'), Tool :: ACTION_HIDE => Translation :: get('Hide'), Tool :: ACTION_SHOW => Translation :: get('Show'));


            $actions[] = new ObjectTableFormAction(Tool :: ACTION_DELETE, Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES));
            $actions[] = new ObjectTableFormAction(Tool :: ACTION_HIDE, Translation :: get('Hide', null, Utilities :: COMMON_LIBRARIES), false);
            $actions[] = new ObjectTableFormAction(Tool :: ACTION_SHOW, Translation :: get('Show', null, Utilities :: COMMON_LIBRARIES), false);

            $renderer->set_actions($actions);
        }

        $this->set_publication_list_renderer($renderer);
    }

    /**
     * Retrieves the publications
     * @return array An array of ContentObjectPublication objects
     */
    function get_publications($from, $count, $column, $direction)
    {
        if (empty($this->publications))
        {
            $datamanager = WeblcmsDataManager :: get_instance();

            if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            {
                $user_id = array();
                $course_group_ids = array();
            }
            else
            {
                $user_id = $this->get_user_id();
                $course_groups = $this->get_course_groups();

                $course_group_ids = array();

                foreach ($course_groups as $course_group)
                {
                    $course_group_ids[] = $course_group->get_id();
                }
            }

            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'note');

            /*$access = array();
            $access[] = new InCondition('user_id', $user_id, $datamanager->get_alias('content_object_publication_user'));
            $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_alias('content_object_publication_course_group'));
            if (! empty($user_id) || ! empty($course_group_ids))
            {
                $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_alias('content_object_publication_course_group'))));
            }
            $conditions[] = new OrCondition($access);*/

            $access = array();
            if ($user_id)
            {
                $access[] = new InCondition(ContentObjectPublicationUser :: PROPERTY_USER, $user_id, ContentObjectPublicationUser :: get_table_name());
            }

            if (count($course_group_ids) > 0)
            {
                $access[] = new InCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, $course_group_ids, ContentObjectPublicationCourseGroup :: get_table_name());
            }

            if (! empty($user_id) || ! empty($course_group_ids))
            {
                $access[] = new AndCondition(array(
                        new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_USER, null, ContentObjectPublicationUser :: get_table_name()), new EqualityCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, null, ContentObjectPublicationCourseGroup :: get_table_name())));
            }

            $conditions[] = new OrCondition($access);

            $subselect_conditions = array();
            $subselect_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Note :: get_type_name());
            if ($this->get_parent()->get_condition())
            {
                $subselect_conditions[] = $this->get_parent()->get_condition();
            }
            $subselect_condition = new AndCondition($subselect_conditions);
            $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
            $condition = new AndCondition($conditions);

            $publications = $datamanager->retrieve_content_object_publications($condition, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX));
            $visible_publications = array();
            while ($publication = $publications->next_result())
            {
                // If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
                if (! $publication->is_visible_for_target_users() && ! ($this->is_allowed(WeblcmsRights :: DELETE_RIGHT) || $this->is_allowed(WeblcmsRights :: EDIT_RIGHT)))
                {
                    continue;
                }
                $visible_publications[] = $publication;
            }
            $this->publications = $visible_publications;
        }

        return $this->publications;

    }

    /**
     * Retrieves the number of published annoucements
     * @return int
     */
    function get_publication_count()
    {
        return count($this->get_publications());
    }
}
?>
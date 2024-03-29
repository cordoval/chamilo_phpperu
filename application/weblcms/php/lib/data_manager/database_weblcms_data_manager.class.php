<?php
namespace application\weblcms;

use application\weblcms\tool\survey\SurveyInvitation;

use common\libraries\ArrayResultSet;
use common\libraries\SubselectCondition;
use common\libraries\ConditionTranslator;
use common\libraries\NotCondition;
use common\libraries\PlatformSetting;
use common\libraries\Session;
use common\libraries\OrCondition;
use common\libraries\InCondition;
use common\libraries\Utilities;
use common\libraries\ObjectTableOrder;
use common\libraries\AndCondition;
use common\libraries\InequalityCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Database;
use common\libraries\Translation;

use group\GroupDataManager;

use user\User;
use user\UserDataManager;

use repository\ContentObject;
use repository\content_object\introduction\Introduction;
use repository\ContentObjectPublicationAttributes;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;
use repository\DatabaseRepositoryDataManager;

/**
 * $Id: database_weblcms_data_manager.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
 * @package application.lib.weblcms.data_manager
 */

require_once dirname(__FILE__) . '/../content_object_publication.class.php';
require_once dirname(__FILE__) . '/../content_object_publication_user.class.php';
require_once dirname(__FILE__) . '/../content_object_publication_course_group.class.php';
require_once dirname(__FILE__) . '/../content_object_publication_group.class.php';
require_once dirname(__FILE__) . '/../category_manager/content_object_publication_category.class.php';
require_once dirname(__FILE__) . '/../content_object_publication_feedback.class.php';
require_once dirname(__FILE__) . '/../course/course.class.php';
require_once dirname(__FILE__) . '/../course/course_section.class.php';
require_once dirname(__FILE__) . '/../course/course_user_category.class.php';
require_once dirname(__FILE__) . '/../course/course_user_relation.class.php';
require_once dirname(__FILE__) . '/../course/course_group_relation.class.php';
require_once dirname(__FILE__) . '/../course/course_module.class.php';
require_once dirname(__FILE__) . '/../course/course_module_last_access.class.php';
require_once dirname(__FILE__) . '/../course_group/course_group.class.php';
require_once dirname(__FILE__) . '/../course_group/course_group_user_relation.class.php';
require_once dirname(__FILE__) . '/../course_group/course_group_right_location.class.php';
require_once dirname(__FILE__) . '/../course_type/course_type.class.php';
require_once dirname(__FILE__) . '/../course_type/course_type_user_category_rel_course.class.php';
require_once dirname(__FILE__) . '/../course/course_request.class.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.interface.php';

class DatabaseWeblcmsDataManager extends Database implements WeblcmsDataManagerInterface
{
    const ALIAS_CONTENT_OBJECT_TABLE = 'lo';
    const ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE = 'lop';

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('weblcms_');
    }

    /**
     * Executes a query
     * @param string $query The query (which will be used in a prepare-
     * statement)
     * @param int $limit The number of rows
     * @param int $offset The offset
     * @param array $params The parameters to replace the placeholders in the
     * query
     * @param boolean $is_manip Is the query a manipulation query
     */
    private function limitQuery($query, $limit, $offset, $params, $is_manip = false)
    {
        $this->get_connection()->setLimit($limit, $offset);
        $statement = $this->get_connection()->prepare($query, null, ($is_manip ? MDB2_PREPARE_MANIP : null));
        $res = $statement->execute($params);
        $statement->free();
        return $res;
    }

    function retrieve_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_ID, $publication_id);
        return $this->retrieve_object(ContentObjectPublication :: get_table_name(), $condition, array(), ContentObjectPublication :: CLASS_NAME);
    }

    function retrieve_content_object_publication_feedback($publication_id)
    {
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_PARENT_ID, $publication_id);
        return $this->retrieve_objects(ContentObjectPublication :: get_table_name(), $condition, null, null, array(), ContentObjectPublication :: CLASS_NAME)->as_array();
    }

    public function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        return $this->count_objects(ContentObjectPublication :: get_table_name(), $condition) >= 1;
    }

    public function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_ids);
        return $this->count_objects(ContentObjectPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->get_alias(ContentObjectPublication :: get_table_name());

                $query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->escape_table_name(ContentObjectPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $pub_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_PUBLISHER_ID, Session :: get_user_id());
                $translator = new ConditionTranslator($this);
                $query .= $translator->render_query($condition);

                $order = array();
                foreach ($order_properties as $order_property)
                {
                    if ($order_property->get_property() == 'application')
                    {

                    }
                    elseif ($order_property->get_property() == 'location')
                    {

                    }
                    elseif ($order_property->get_property() == 'title')
                    {
                        $order[] = $this->escape_column_name('title') . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        $order[] = $this->escape_column_name($order_property->get_property()) . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }

                if (count($order) > 0)
                    $query .= ' ORDER BY ' . implode(', ', $order);
            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->escape_table_name(ContentObjectPublication :: get_table_name());
            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }

        $this->set_limit($offset, $count);
        $res = $this->query($query);

        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[ContentObjectPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[ContentObjectPublication :: PROPERTY_PUBLISHER_ID]);
            $info->set_publication_date($record[ContentObjectPublication :: PROPERTY_PUBLICATION_DATE]);
            $info->set_application('weblcms');
            //TODO: i8n location string
            $info->set_location($record[ContentObjectPublication :: PROPERTY_COURSE_ID] . ' &gt; ' . $record[ContentObjectPublication :: PROPERTY_TOOL]);
            //TODO: set correct URL
            $info->set_url('run.php?application=weblcms&amp;go=' . WeblcmsManager :: ACTION_VIEW_COURSE . '&course=' . $record[ContentObjectPublication :: PROPERTY_COURSE_ID] . '&amp;tool=' . $record[ContentObjectPublication :: PROPERTY_TOOL] . '&amp;tool_action=' . Tool :: ACTION_VIEW . '&amp;' . Tool :: PARAM_PUBLICATION_ID . '=' . $info->get_id());
            $info->set_publication_object_id($record[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

            $publication_attr[] = $info;
        }

        $res->free();

        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name('content_object_publication') . ' WHERE ' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->get_connection()->setLimit(0, 1);
        $res = $this->query($query);
        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

        $res->free();

        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[ContentObjectPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[ContentObjectPublication :: PROPERTY_PUBLISHER_ID]);
        $publication_attr->set_publication_date($record[ContentObjectPublication :: PROPERTY_PUBLICATION_DATE]);
        $publication_attr->set_application('weblcms');
        //TODO: i8n location string
        $publication_attr->set_location($record[ContentObjectPublication :: PROPERTY_COURSE_ID] . ' &gt; ' . $record[ContentObjectPublication :: PROPERTY_TOOL]);
        //TODO: set correct URL
        $publication_attr->set_url('index_weblcms.php?tool=' . $record[ContentObjectPublication :: PROPERTY_TOOL] . '&amp;cidReq=' . $record[ContentObjectPublication :: PROPERTY_COURSE_ID]);
        $publication_attr->set_publication_object_id($record[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

        return $publication_attr;
    }

    function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if (! $object_id)
        {
            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_PUBLISHER_ID, $user->get_id());
        }
        else
        {
            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        }
        return $this->count_objects(ContentObjectPublication :: get_table_name(), $condition);
    }

    function retrieve_content_object_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $publication_alias = $this->get_alias(ContentObjectPublication :: get_table_name());
        $publication_user_alias = $this->get_alias('content_object_publication_user');
        $publication_group_alias = $this->get_alias('content_object_publication_course_group');
        $lo_table_alias = $this->get_alias('content_object');

        $query = 'SELECT DISTINCT ' . $publication_alias . '.* FROM ' . $this->escape_table_name(ContentObjectPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' LEFT JOIN ' . $this->escape_table_name('content_object_publication_user') . ' AS ' . $publication_user_alias . ' ON ' . $publication_alias . '.id = ' . $publication_user_alias . '.publication_id';
        $query .= ' LEFT JOIN ' . $this->escape_table_name('content_object_publication_course_group') . ' AS ' . $publication_group_alias . ' ON ' . $publication_alias . '.id = ' . $publication_group_alias . '.publication_id';
        $query .= ' JOIN ' . RepositoryDataManager :: get_instance()->escape_table_name('content_object') . ' AS ' . $lo_table_alias . ' ON ' . $publication_alias . '.content_object_id = ' . $lo_table_alias . '.id';

        return $this->retrieve_object_set($query, ContentObjectPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, ContentObjectPublication :: CLASS_NAME);
    }

    function count_content_object_publications($condition)
    {
        $publication_alias = $this->get_alias(ContentObjectPublication :: get_table_name());
        $publication_user_alias = $this->get_alias('content_object_publication_user');
        $publication_group_alias = $this->get_alias('content_object_publication_course_group');
        $lo_table_alias = $this->get_alias('content_object');

        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(ContentObjectPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' LEFT JOIN ' . $this->escape_table_name('content_object_publication_user') . ' AS ' . $publication_user_alias . ' ON ' . $publication_alias . '.id = ' . $publication_user_alias . '.publication_id';
        $query .= ' LEFT JOIN ' . $this->escape_table_name('content_object_publication_course_group') . ' AS ' . $publication_group_alias . ' ON ' . $publication_alias . '.id = ' . $publication_group_alias . '.publication_id';
        $query .= ' JOIN ' . RepositoryDataManager :: get_instance()->escape_table_name('content_object') . ' AS ' . $lo_table_alias . ' ON ' . $publication_alias . '.content_object_id = ' . $lo_table_alias . '.id';

        return $this->count_result_set($query, ContentObjectPublication :: get_table_name(), $condition);
    }

    function count_courses($condition)
    {
        $course_alias = $this->get_alias(Course :: get_table_name());
        $course_settings_alias = $this->get_alias('course_settings');
        $course_type_alias = $this->get_alias(CourseType :: get_table_name());

        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
        $query .= ' JOIN ' . $this->escape_table_name('course_settings') . ' AS ' . $course_settings_alias . ' ON ' . $course_alias . '.id = ' . $course_settings_alias . '.course_id';
        $query .= ' LEFT JOIN ' . $this->escape_table_name(CourseType :: get_table_name()) . ' AS ' . $course_type_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_COURSE_TYPE_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseType :: PROPERTY_ID, $course_type_alias);

        return $this->count_result_set($query, Course :: get_table_name(), $condition);
    }

    function subscribe_user_to_allowed_courses($user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CommonRequest :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new InequalityCondition(CommonRequest :: PROPERTY_DECISION_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time());
        $conditions[] = new EqualityCondition(CommonRequest :: PROPERTY_DECISION, CommonRequest :: ALLOWED_DECISION);
        $condition = new AndCondition($conditions);

        $course_subscribe_requests = $this->retrieve_requests($condition);

        while ($course_request = $course_subscribe_requests->next_result())
        {
            $course_id = $course_request->get_course_id();
            $user = UserDataManager :: get_instance()->retrieve_user($user_id);
            if (! $this->is_subscribed($course_id, $user))
            {
                $this->subscribe_user_to_course($course_id, '5', '0', $user_id);
            }
        }
    }

    function count_course_types($condition = null)
    {
        return $this->count_objects(CourseType :: get_table_name(), $condition);
    }

    function count_course_type_group_creation_rights($condition = null)
    {
        return $this->count_objects(CourseTypeGroupCreationRight :: get_table_name(), $condition);
    }

    function count_course_group_subscribe_rights($condition = null)
    {
        return $this->count_objects(CourseGroupSubscribeRight :: get_table_name(), $condition);
    }

    function count_course_group_unsubscribe_rights($condition = null)
    {
        return $this->count_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition);
    }

    function count_requests($condition = null)
    {
        return $this->count_objects(CourseRequest :: get_table_name(), $condition);
    }

    function count_course_create_requests($condition = null)
    {
        return $this->count_objects(CourseCreateRequest :: get_table_name(), $condition);
    }

    function count_active_course_types()
    {
        $condition = new EqualityCondition(CourseType :: PROPERTY_ACTIVE, 1);
        return $this->count_course_types($condition);
    }

    function count_course_categories($condition = null)
    {
        return $this->count_objects(CourseCategory :: get_table_name(), $condition);
    }

    function count_user_courses($condition = null)
    {
        $course_alias = $this->get_alias(Course :: get_table_name());
        $course_relation_alias = $this->get_alias(CourseUserRelation :: get_table_name());

        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
        $query .= ' JOIN ' . $this->escape_table_name(CourseUserRelation :: get_table_name()) . ' AS ' . $course_relation_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . '=' . $this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE, $course_relation_alias);
        return $this->count_result_set($query, Course :: get_table_name(), $condition);
    }

    function count_course_user_categories($condition = null)
    {
        return $this->count_objects(CourseUserCategory :: get_table_name(), $condition);
    }

    function count_course_type_user_categories($condition = null)
    {
        return $this->count_objects(CourseTypeUserCategory :: get_table_name(), $condition);
    }

    function retrieve_course_list_of_user_as_course_admin($user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_STATUS, 1);
        $condition = new AndCondition($conditions);

        return $this->retrieve_course_user_relations($condition);
    }

    function count_distinct_course_user_relations()
    {
        return $this->count_distinct(CourseUserRelation :: get_table_name(), CourseUserRelation :: PROPERTY_USER);
    }

    function count_course_user_relations($condition = null)
    {
        return $this->count_objects(CourseUserRelation :: get_table_name(), $condition);
    }

    function count_course_group_relations($condition = null)
    {
        return $this->count_objects(CourseGroupRelation :: get_table_name(), $condition);
    }

    function create_content_object_publication_user($publication_user)
    {
        return $this->create($publication_user);
    }

    function create_content_object_publication_users($publication)
    {
        $users = $publication->get_target_users();

        foreach ($users as $index => $user_id)
        {
            $publication_user = new ContentObjectPublicationUser();
            $publication_user->set_publication($publication->get_id());
            $publication_user->set_user($user_id);

            if (! $publication_user->create())
            {
                return false;
            }
        }

        return true;
    }

    function create_content_object_publication_course_group($publication_course_group)
    {
        return $this->create($publication_course_group);
    }

    function create_content_object_publication_course_groups($publication)
    {
        $course_groups = $publication->get_target_course_groups();

        foreach ($course_groups as $index => $course_group_id)
        {
            $publication_course_group = new ContentObjectPublicationCourseGroup();
            $publication_course_group->set_publication($publication->get_id());
            $publication_course_group->set_course_group_id($course_group_id);

            if (! $publication_course_group->create())
            {
                return false;
            }
        }

        return true;
    }

    function create_content_object_publication_group($publication_group)
    {
        return $this->create($publication_group);
    }

    function create_content_object_publication_groups($publication)
    {
        $groups = $publication->get_target_groups();

        foreach ($groups as $index => $group_id)
        {
            $publication_group = new ContentObjectPublicationGroup();
            $publication_group->set_publication_id($publication->get_id());
            $publication_group->set_group_id($group_id);

            if (! $publication_group->create())
            {
                return false;
            }
        }

        return true;
    }

    function create_content_object_publication($publication)
    {
        if (! $this->create($publication))
        {
            return false;
        }

        if (! $this->create_content_object_publication_users($publication))
        {
            return false;
        }

        if (! $this->create_content_object_publication_course_groups($publication))
        {
            return false;
        }

        if (! $this->create_content_object_publication_groups($publication))
        {
            return false;
        }

        return true;
    }

    function update_content_object_publication($publication)
    {
        // Delete target users and course_groups
        $condition = new EqualityCondition('publication_id', $publication->get_id());
        $this->delete_objects('content_object_publication_user', $condition);
        $this->delete_objects('content_object_publication_course_group', $condition);
        $this->delete_objects('content_object_publication_group', $condition);

        // Add updated target users and course_groups
        if (! $this->create_content_object_publication_users($publication))
        {
            return false;
        }

        if (! $this->create_content_object_publication_course_groups($publication))
        {
            return false;
        }

        if (! $this->create_content_object_publication_groups($publication))
        {
            return false;
        }

        // Update publication properties
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_ID, $publication->get_id());
        return $this->update($publication, $condition);
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->escape_column_name(ContentObjectPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name('content_object_publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function delete_content_object_publication($publication)
    {
        if (is_numeric($publication))
        {
            $publication = $this->retrieve_content_object_publication($publication);
        }

        $publication_id = $publication->get_id();

        $query = 'DELETE FROM ' . $this->escape_table_name('content_object_publication_user') . ' WHERE publication_id = ' . $this->quote($publication_id);
        $res = $this->query($query);
        $res->free();

        $query = 'DELETE FROM ' . $this->escape_table_name('content_object_publication_course_group') . ' WHERE publication_id = ' . $this->quote($publication_id);
        $res = $this->query($query);
        $res->free();

        $query = 'UPDATE ' . $this->escape_table_name('content_object_publication') . ' SET ' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '=' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '-1 WHERE ' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '>' . $this->quote($publication->get_display_order_index());
        $res = $this->query($query);
        $res->free();

        $query = 'DELETE FROM ' . $this->escape_table_name('content_object_publication') . ' WHERE ' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->get_connection()->setLimit(0, 1);
        $res = $this->query($query);
        $res->free();

        return true;
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        $publications = $this->retrieve_content_object_publications($condition);

        while ($publication = $publications->next_result())
        {
            $site_name_setting = PlatformSetting :: get('site_name');
            $subject = '[' . $site_name_setting . '] ' . $publication->get_content_object()->get_title();
            // TODO: SCARA - Add meaningfull publication removal message
            //			$body = 'message';
            //			$user = $this->userDM->retrieve_user($publication->get_publisher_id());
            //			$mail = Mail :: factory($subject, $body, $user->get_email());
            //			$mail->send();
            $this->delete_content_object_publication($publication);
        }
        return true;
    }

    function retrieve_content_object_publication_category($id)
    {
        $condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_ID, $id);
        return $this->retrieve_object(ContentObjectPublicationCategory :: get_table_name(), $condition, array(), ContentObjectPublicationCategory :: CLASS_NAME);
    }

    function move_content_object_publication($publication, $places)
    {
        if ($places < 0)
        {
            return $this->move_content_object_publication_up($publication, - $places);
        }
        else
        {
            return $this->move_content_object_publication_down($publication, $places);
        }
    }

    function retrieve_course_module_access($condition = null, $order_by = array())
    {
        return $this->retrieve_object(CourseModuleLastAccess :: get_table_name(), $condition, $order_by, CourseModuleLastAccess :: CLASS_NAME);
    }

    function retrieve_course_module_accesses($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(CourseModuleLastAccess :: get_table_name(), $condition, $offset, $max_objects, $order_by, CourseModuleLastAccess :: CLASS_NAME);
    }

    function log_course_module_access($course_code, $user_id, $module_name = null, $category_id = 0)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_COURSE_CODE, $course_code);
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_MODULE_NAME, $module_name);
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_CATEGORY_ID, $category_id);
        $condition = new AndCondition($conditions);

        $course_module_last_access = $this->retrieve_course_module_access($condition);

        if (! $course_module_last_access)
        {
            $course_module_last_access = new CourseModuleLastAccess();
            $course_module_last_access->set_course_code($course_code);
            $course_module_last_access->set_user_id($user_id);
            $course_module_last_access->set_module_name($module_name);
            $course_module_last_access->set_category_id($category_id);
            $course_module_last_access->set_access_date(time());
            return $course_module_last_access->create();
        }
        else
        {
            $course_module_last_access->set_access_date(time());
            return $course_module_last_access->update();
        }
    }

    /**
     * Creates a course module last acces in the database
     *
     * @param CourseModuleLastAccess $coursemodule_last_accces
     */
    function create_course_module_last_access($coursemodule_last_accces)
    {
        $this->create($coursemodule_last_accces);
    }

    /**
     * Creates a course module last acces in the database
     *
     * @param CourseModuleLastAccess $coursemodule_last_accces
     */
    function update_course_module_last_access($coursemodule_last_accces)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_COURSE_CODE, $coursemodule_last_accces->get_course_code());
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_MODULE_NAME, $coursemodule_last_accces->get_module_name());
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_USER_ID, $coursemodule_last_accces->get_user_id());
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_CATEGORY_ID, $coursemodule_last_accces->get_category_id());
        $condition = new AndCondition($conditions);

        $this->update($coursemodule_last_accces, $condition);
    }

    /**
     * Returns the last visit date per course and module
     * @param <type> $course_code
     * @param <type> $module_name
     * @return <type>
     */
    function get_last_visit_date_per_course($course_code, $module_name = null)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_COURSE_CODE, $course_code);
        if (! is_null($module_name))
        {
            $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_MODULE_NAME, $module_name);
        }
        $condition = new AndCondition($conditions);

        $order_by = new ObjectTableOrder(CourseModuleLastAccess :: PROPERTY_ACCESS_DATE, SORT_DESC);

        $course_module_access = $this->retrieve_course_module_access($condition, $order_by);

        if (! $course_module_access)
        {
            return 0;
        }
        else
        {
            return $course_module_access->get_access_date();
        }
    }

    function get_last_visit_date($course_code, $user_id, $module_name = null, $category_id = 0)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_COURSE_CODE, $course_code);
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_CATEGORY_ID, $category_id);
        if (! is_null($module_name))
        {
            $conditions[] = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_MODULE_NAME, $module_name);
        }
        $condition = new AndCondition($conditions);

        $order_by = new ObjectTableOrder(CourseModuleLastAccess :: PROPERTY_ACCESS_DATE, SORT_DESC);

        $course_module_access = $this->retrieve_course_module_access($condition, $order_by);

        if (! $course_module_access)
        {
            return 0;
        }
        else
        {
            return $course_module_access->get_access_date();
        }
    }

    function get_course_modules($course_code)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name('course_module') . ' WHERE course_id = ' . $this->quote($course_code) . ' ORDER BY sort';
        $res = $this->query($query);
        $modules = array();
        $module = null;
        while ($module = $res->fetchRow(MDB2_FETCHMODE_OBJECT))
        {
            $modules[$module->name] = $module;
        }

        $res->free();

        return $modules;
    }

    // DEPRECATED
    function get_all_course_modules()
    {
        return $this->retrieve_distinct(CourseModule :: get_table_name(), CourseModule :: PROPERTY_NAME);
    }

    function retrieve_course($id)
    {
        $condition = new EqualityCondition(Course :: PROPERTY_ID, $id);
        $course = $this->retrieve_object(Course :: get_table_name(), $condition, array(), Course :: CLASS_NAME);
        if (empty($course))
            return false;

     //$this->redirect(Translation :: get('CourseDoesntExist'), true, array('go' => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME),array(),false,Redirect::TYPE_LINK);
        return $course;
    }

    function retrieve_empty_course()
    {
        $course = new Course();
        $course->set_settings(new CourseSettings());
        $course->set_layout_settings(new CourseLayout());
        $course->set_course_type($this->retrieve_empty_course_type());
        $course->set_rights(new CourseRights());
        return $course;
    }

    function retrieve_course_group_subscribe_right($course_id, $group_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_id);
        $conditions[] = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_GROUP_ID, $group_id);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(CourseGroupSubscribeRight :: get_table_name(), $condition, array(), CourseGroupSubscribeRight :: CLASS_NAME);
    }

    function retrieve_course_group_unsubscribe_right($course_id, $group_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_id);
        $conditions[] = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $group_id);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(CourseGroupUnsubscribeRight :: get_table_name(), $condition, array(), CourseGroupUnsubscribeRight :: CLASS_NAME);
    }

    function retrieve_course_rights($id)
    {
        $condition = new EqualityCondition(CourseRights :: PROPERTY_COURSE_ID, $id);
        return $this->retrieve_object(CourseRights :: get_table_name(), $condition, array(), CourseRights :: CLASS_NAME);
    }

    function retrieve_course_module($id)
    {
        $condition = new EqualityCondition(CourseModule :: PROPERTY_ID, $id);
        return $this->retrieve_object(CourseModule :: get_table_name(), $condition, array(), CourseModule :: CLASS_NAME);
    }

    function retrieve_course_module_by_name($course_id, $course_module)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_id);
        $conditions[] = new EqualityCondition(CourseModule :: PROPERTY_NAME, $course_module);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(CourseModule :: get_table_name(), $condition, array(), CourseModule :: CLASS_NAME);
    }

    function retrieve_course_settings($id)
    {
        $condition = new EqualityCondition(CourseSettings :: PROPERTY_COURSE_ID, $id);
        return $this->retrieve_object(CourseSettings :: get_table_name(), $condition, array(), CourseSettings :: CLASS_NAME);
    }

    function retrieve_course_layout($id)
    {
        $condition = new EqualityCondition(CourseLayout :: PROPERTY_COURSE_ID, $id);
        return $this->retrieve_object(CourseLayout :: get_table_name(), $condition, array(), CourseLayout :: CLASS_NAME);
    }

    function retrieve_courses($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $course_alias = $this->get_alias(Course :: get_table_name());
        $course_settings_alias = $this->get_alias(CourseSettings :: get_table_name());
        $course_type_alias = $this->get_alias(CourseType :: get_table_name());

        $query = 'SELECT ' . $course_alias . '.* FROM ' . $this->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
        $query .= ' JOIN ' . $this->escape_table_name(CourseSettings :: get_table_name()) . ' AS ' . $course_settings_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseSettings :: PROPERTY_COURSE_ID, $course_settings_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(CourseType :: get_table_name()) . ' AS ' . $course_type_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_COURSE_TYPE_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseType :: PROPERTY_ID, $course_type_alias);

        $order_by[] = new ObjectTableOrder(Course :: PROPERTY_NAME);

        return $this->retrieve_object_set($query, Course :: get_table_name(), $condition, $offset, $max_objects, $order_by, Course :: CLASS_NAME);
    }

    function retrieve_course_user_relation($course_code, $user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_code);
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
        $condition = new AndCondition($conditions);

        return $this->retrieve_object(CourseUserRelation :: get_table_name(), $condition, array(), CourseUserRelation :: CLASS_NAME);
    }

    function retrieve_course_user_relations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(CourseUserRelation :: get_table_name(), $condition, $offset, $count, $order_property, CourseUserRelation :: CLASS_NAME);
    }

    function retrieve_group_user_relation($course_id, $group_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE_ID, $course_id);
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_GROUP_ID, $group_id);
        $condition = new AndCondition($conditions);

        return $this->retrieve_object(CourseGroupRelation :: get_table_name(), $condition, array(), CourseGroupRelation :: CLASS_NAME);
    }

    function retrieve_course_group_relations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(CourseGroupRelation :: get_table_name(), $condition, $offset, $count, $order_property, CourseGroupRelation :: CLASS_NAME);
    }

    function retrieve_course_user_relation_at_sort($user_id, $course_type_id, $category_id, $sort, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, $category_id);
        $conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course_type_id, Course :: get_table_name());

        if ($direction == 'up')
        {
            $conditions[] = new InequalityCondition(CourseUserRelation :: PROPERTY_SORT, InequalityCondition :: LESS_THAN, $sort);
            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new InequalityCondition(CourseUserRelation :: PROPERTY_SORT, InequalityCondition :: GREATER_THAN, $sort);
            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);

        $course_relation_alias = $this->get_alias(CourseUserRelation :: get_table_name());
        $course_alias = $this->get_alias(Course :: get_table_name());

        $query = 'SELECT ' . $course_relation_alias . '.* FROM ' . $this->escape_table_name(CourseUserRelation :: get_table_name()) . ' AS ' . $course_relation_alias;
        $query .= ' JOIN ' . $this->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE, $course_relation_alias);

        $record = $this->retrieve_row($query, CourseUserRelation :: get_table_name(), $condition, array(
                new ObjectTableOrder(CourseUserRelation :: PROPERTY_SORT, $order_direction)));

        if ($record)
            return $this->record_to_object($record, Utilities :: underscores_to_camelcase(CourseUserRelation :: get_table_name()));
        else
            return false;
    }

    function retrieve_course_type_user_category_at_sort($user_id, $course_type_id, $sort, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_id);

        if ($direction == 'up')
        {
            $conditions[] = new InequalityCondition(CourseTypeUserCategory :: PROPERTY_SORT, InequalityCondition :: LESS_THAN, $sort);
            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new InequalityCondition(CourseTypeUserCategory :: PROPERTY_SORT, InequalityCondition :: GREATER_THAN, $sort);
            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);

        return $this->retrieve_object(CourseTypeUserCategory :: get_table_name(), $condition, array(
                new ObjectTableOrder(CourseTypeUserCategory :: PROPERTY_SORT, $order_direction)), CourseTypeUserCategory :: CLASS_NAME);
    }

    function retrieve_user_courses($condition = null, $offset = 0, $max_objects = -1, $order_by = null)
    {
        $course_alias = $this->get_alias(Course :: get_table_name());
        $course_user_relation_alias = $this->get_alias(CourseUserRelation :: get_table_name());
        $course_group_relation_alias = $this->get_alias(CourseGroupRelation :: get_table_name());

        $query = 'SELECT DISTINCT ' . $course_alias . '.* FROM ' . $this->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
        $query .= ' LEFT JOIN ' . $this->escape_table_name(CourseUserRelation :: get_table_name()) . ' AS ' . $course_user_relation_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE, $course_user_relation_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(CourseGroupRelation :: get_table_name()) . ' AS ' . $course_group_relation_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseGroupRelation :: PROPERTY_COURSE_ID, $course_group_relation_alias);

        if (is_null($order_by))
        {
            $order_by[] = new ObjectTableOrder(Course :: PROPERTY_NAME);
        }

        return $this->retrieve_object_set($query, Course :: get_table_name(), $condition, $offset, $max_objects, $order_by, Course :: CLASS_NAME);
    }

    function retrieve_course_group_rights_by_type($course_id, $type)
    {
        if (CourseGroupSubscribeRight :: UNSUBSCRIBE == $type)
        {
            $condition = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_id);
            return $this->retrieve_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition, null, null, array(), CourseGroupUnsubscribeRight :: CLASS_NAME);
        }
        else
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_id);
            $conditions[] = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_SUBSCRIBE, $type);
            $condition = new AndCondition($conditions);
            return $this->retrieve_objects(CourseGroupSubscribeRight :: get_table_name(), $condition, null, null, array(), CourseGroupSubscribeRight :: CLASS_NAME);
        }
    }

    function retrieve_course_subscribe_groups_by_right($right, $course, $condition = null, $offset = null, $count = null, $order_property = null)
    {
        $groups_result = GroupDataManager :: get_instance()->retrieve_groups($condition, $offset, $count, $order_property);
        $groups = array();
        while ($group = $groups_result->next_result())
        {
            if ($course->can_group_subscribe($group->get_id()) == $right)
            {
                $groups[] = $group;
            }
        }
        return new ArrayResultSet($groups);
    }

    function retrieve_course_subscribe_users_by_right($rights, $course, $add_course_admin = false, $condition = null, $offset = null, $count = null, $order_property = null)
    {
        if (! is_array($rights))
            $rights = array($rights);
        $users_result = UserDataManager :: get_instance()->retrieve_users($condition, $offset, $count, $order_property);
        $users = array();
        while ($user = $users_result->next_result())
        {
            if (in_array($course->can_user_subscribe($user), $rights) || ($course->is_course_admin($user) && $add_course_admin))
            {
                $users[] = $user;
            }
        }
        return new ArrayResultSet($users);
    }

    function create_course($course)
    {
        return $this->create($course);
    }

    function create_course_modules($course_modules, $course_code)
    {
        $condition = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $course_code);
        $sections_set = $this->retrieve_course_sections($condition);
        $sections = array();
        while ($section = $sections_set->next_result())
        {
            $sections[$section->get_type()][] = $section;
        }

        foreach ($course_modules as $module)
        {
            $section_id = $sections[CourseSection :: TYPE_TOOL][0]->get_id();
            $module->set_section($section_id);
            if (! $module->create())
                return false;
        }

        $admin_tools = WeblcmsDataManager :: get_tools('course_admin');
        foreach ($admin_tools as $index => $tool_name)
        {
            $section_id = $sections[CourseSection :: TYPE_ADMIN][0]->get_id();
            $module = new CourseModule();
            $module->set_course_code($course_code);
            $module->set_name($tool_name);
            $module->set_visible(1);
            $module->set_section($section_id);
            $module->set_sort($index);
            if (! $module->create())
                return false;
        }

        return true;
    }

    function create_course_module($course_module)
    {
        $result = $this->create($course_module);
        return $result;
    }

    function create_course_settings($course_settings)
    {
        return $this->create($course_settings);
    }

    function create_course_layout($course_layout)
    {
        return $this->create($course_layout);
    }

    function create_course_type($course_type)
    {
        return $this->create($course_type);
    }

    function create_course_request($request)
    {
        return $this->create($request);
    }

    function create_course_create_request($request)
    {
        return $this->create($request);
    }

    function create_course_type_settings($course_type_settings)
    {
        return $this->create($course_type_settings);
    }

    function create_course_type_rights($course_type_rights)
    {
        return $this->create($course_type_rights);
    }

    function create_course_rights($course_rights)
    {
        return $this->create($course_rights);
    }

    function create_course_group_subscribe_right($course_group_subscribe_right)
    {
        return $this->create($course_group_subscribe_right);
    }

    function create_course_group_unsubscribe_right($course_group_unsubscribe_right)
    {
        return $this->create($course_group_unsubscribe_right);
    }

    function create_course_type_tool($course_type_tool)
    {
        return $this->create($course_type_tool);
    }

    function create_course_type_layout($course_type_layout)
    {
        return $this->create($course_type_layout);

    }

    function create_course_type_group_subscribe_right($course_type_group_subscribe_right)
    {
        return $this->create($course_type_group_subscribe_right);
    }

    function create_course_type_group_unsubscribe_right($course_type_group_unsubscribe_right)
    {
        return $this->create($course_type_group_unsubscribe_right);
    }

    function create_course_type_group_creation_right($course_type_group_creation_right)
    {
        return $this->create($course_type_group_creation_right);
    }

    function create_course_type_user_category($course_type_user_category)
    {
        return $this->create($course_type_user_category);
    }

    function is_subscribed($course, User $user)
    {
        $course_id = $course;
        if ($course instanceof Course)
        {
            $course_id = $course->get_id();
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user->get_id());
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_id);
        $condition = new AndCondition($conditions);

        $has_user_relations = $this->count_objects(CourseUserRelation :: get_table_name(), $condition) > 0;

        $groups = $user->get_groups(true);
        if ($groups)
        {
            $conditions = array();
            $conditions[] = new InCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $user->get_groups(true));
            $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_id);
            $condition = new AndCondition($conditions);
            $has_group_relations = $this->count_objects(CourseGroupRelation :: get_table_name(), $condition) > 0;
        }
        else
            $has_group_relations = false;

        return $has_user_relations || $has_group_relations;
    }

    function is_group_subscribed($course_id, $group_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $group_id);
        $conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_COURSE_ID, $course_id);
        $condition = new AndCondition($conditions);
        return $this->count_objects(CourseGroupRelation :: get_table_name(), $condition) > 0;
    }

    function is_course_category($category)
    {
        $condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category);
        return $this->count_objects(CourseCategory :: get_table_name(), $condition) > 0;
    }

    function is_course($course_code)
    {
        $condition = new EqualityCondition(Course :: PROPERTY_ID, $course_code);
        return $this->count_objects(Course :: get_table_name(), $condition) > 0;
    }

    function is_course_admin($course, $user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course->get_id());
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_STATUS, 1);
        $condition = new AndCondition($conditions);
        return $this->count_objects(CourseUserRelation :: get_table_name(), $condition) > 0;
    }

    function retrieve_next_course_user_relation_sort_value(CourseUserRelation $course_user_relation)
    {
        $course = $this->retrieve_course($course_user_relation->get_course());
        $subcondition = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course->get_course_type_id());
        $conditions[] = new SubselectCondition(CourseUserRelation :: PROPERTY_COURSE, Course :: PROPERTY_ID, Course :: get_table_name(), $subcondition);
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $course_user_relation->get_user());
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, $course_user_relation->get_category());
        $condition = new AndCondition($conditions);

        return $this->retrieve_max_sort_value(CourseUserRelation :: get_table_name(), CourseUserRelation :: PROPERTY_SORT, $condition) + 1;
    }

    function subscribe_user_to_course($course, $status, $tutor_id, $user_id)
    {
        $course_id = $course;
        if ($course instanceof Course)
        {
            $course_id = $course->get_id();
        }

        $course_user_relation = new CourseUserRelation();
        $course_user_relation->set_course($course_id);
        $course_user_relation->set_user($user_id);
        $course_user_relation->set_status($status);

        if ($course_user_relation->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function subscribe_group_to_course(Course $course, $group_id, $status)
    {
        $this->get_connection()->loadModule('Extended');

        $course_group_relation = new CourseGroupRelation();
        $course_group_relation->set_course_id($course->get_id());
        $course_group_relation->set_group_id($group_id);
        $course_group_relation->set_status($status);

        if ($course_group_relation->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_course_user_relation($courseuserrelation)
    {
        $props = array();
        foreach ($courseuserrelation->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }

        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(CourseUserRelation :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_course_group_relation($course_group_relation)
    {
        $props = array();
        foreach ($course_group_relation->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }

        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(CourseGroupRelation :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function unsubscribe_user_from_course($course, $user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course->get_id());
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
        $condition = new AndCondition($conditions);

        return $this->delete_objects(CourseUserRelation :: get_table_name(), $condition);
    }

    function unsubscribe_group_from_course(Course $course, $group_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_COURSE_ID, $course->get_id());
        $conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $group_id);
        $condition = new AndCondition($conditions);

        return $this->delete_objects(CourseGroupRelation :: get_table_name(), $condition);
    }

    function create_course_category($course_category)
    {
        return $this->create($course_category);
    }

    function create_course_user_category($course_user_category)
    {
        return $this->create($course_user_category);
    }

    function delete_course_user_category($course_user_category)
    {
        $condition = new EqualityCondition(CourseUserCategory :: PROPERTY_ID, $course_user_category->get_id());

        return $this->delete_objects(CourseUserCategory :: get_table_name(), $condition);
    }

    function delete_course_type_user_category($course_type_user_category)
    {
        $condition = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_ID, $course_type_user_category->get_id());

        if ($this->delete_objects(CourseTypeUserCategory :: get_table_name(), $condition))
        {
            $condition = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $course_type_user_category->get_course_user_category_id());
            $count = $this->count_course_type_user_categories($condition);
            if ($count == 0)
            {
                $condition = new EqualityCondition(CourseUserCategory :: PROPERTY_ID, $course_type_user_category->get_course_user_category_id());
                $course_user_category = $this->retrieve_course_user_category($condition);
                if (! $course_user_category->delete())
                {
                    return false;
                }
            }

            $conditions = array();
            $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category->get_id());
            $condition = new AndCondition($conditions);
            $course_type_user_category_rel_courses = $this->retrieve_course_type_user_category_rel_courses($condition);

            while ($course_type_user_category_rel_course = $course_type_user_category_rel_courses->next_result())
            {
                if (! $course_type_user_category_rel_course->delete())
                {
                    return false;
                }
            }

            $conditions = array();
            $conditions[] = new InEqualityCondition(CourseTypeUserCategory :: PROPERTY_SORT, InEqualityCondition :: GREATER_THAN, $course_type_user_category->get_sort());
            $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_user_category->get_course_type_id());
            $condition = new AndCondition($conditions);

            $properties = array();
            $properties[CourseTypeUserCategory :: PROPERTY_SORT] = $this->escape_column_name(CourseTypeUserCategory :: PROPERTY_SORT) . '-1';

            return $this->update_objects(CourseTypeUserCategory :: get_table_name(), $properties, $condition);
        }
        else
        {
            return false;
        }
    }

    function delete_course_user($courseuser)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $courseuser->get_course());
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $courseuser->get_user());
        $condition = new AndCondition($conditions);

        return $this->delete_objects(CourseUserRelation :: get_table_name(), $condition);
    }

    function delete_course_category($course_category)
    {
        $condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $course_category->get_id());
        $success = $this->delete_objects(CourseCategory :: get_table_name(), $condition);

        if ($success)
        {
            $condition = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $course_category->get_id());
            $properties = array(CourseCategory :: PROPERTY_PARENT => $course_category->get_parent());
            $success = $this->update_objects(CourseCategory :: get_table_name(), $properties, $condition);

            if ($success)
            {
                $condition = new EqualityCondition(Course :: PROPERTY_CATEGORY, $course_category->get_id());
                $properties = array(Course :: PROPERTY_CATEGORY => $course_category->get_parent());
                return $this->update_objects(Course :: get_table_name(), $properties, $condition);
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function update_course($course)
    {
        $condition = new EqualityCondition(Course :: PROPERTY_ID, $course->get_id());
        return $this->update($course, $condition);
    }

    function update_courses($properties, $condition)
    {
        return $this->update_objects(Course :: get_table_name(), $properties, $condition);
    }

    function update_course_request($request)
    {
        $condition = new EqualityCondition(CourseRequest :: PROPERTY_ID, $request->get_id());
        return $this->update($request, $condition);
    }

    function update_course_create_request($request)
    {
        $condition = new EqualityCondition(CourseCreateRequest :: PROPERTY_ID, $request->get_id());
        return $this->update($request, $condition);
    }

    function update_course_module($course_module)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_module->get_course_code());
        $conditions[] = new EqualityCondition(CourseModule :: PROPERTY_NAME, $course_module->get_name());
        $condition = new AndCondition($conditions);
        return $this->update($course_module, $condition);
    }

    /**
     * Updates the visibility of the course modules
     * @param Condition $condition define the to be updated modules and course
     * @param bool $visibility visibility
     */
    function update_course_module_visibility($condition, $visibility)
    {
        //$and_condition = new AndConditon
        $properties = array(CourseModule :: PROPERTY_VISIBLE => $visibility);
        $this->update_objects(CourseModule :: get_table_name(), $properties, $condition);
    }

    function update_course_settings($course_settings)
    {
        $condition = new EqualityCondition(CourseSettings :: PROPERTY_COURSE_ID, $course_settings->get_course_id());
        return $this->update($course_settings, $condition);
    }

    function update_course_layout($course_layout)
    {
        $condition = new EqualityCondition(CourseLayout :: PROPERTY_COURSE_ID, $course_layout->get_course_id());
        return $this->update($course_layout, $condition);
    }

    function update_course_rights($course_rights)
    {
        $condition = new EqualityCondition(CourseRights :: PROPERTY_COURSE_ID, $course_rights->get_course_id());
        return $this->update($course_rights, $condition);
    }

    function update_course_group_subscribe_right($course_group_subscribe_right)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_group_subscribe_right->get_course_id());
        $conditions[] = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_GROUP_ID, $course_group_subscribe_right->get_group_id());
        $condition = new AndCondition($conditions);
        return $this->update($course_group_subscribe_right, $condition);
    }

    function update_course_group_unsubscribe_right($course_group_unsubscribe_right)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_group_unsubscribe_right->get_course_id());
        $conditions[] = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $course_group_unsubscribe_right->get_group_id());
        $condition = new AndCondition($conditions);
        return $this->update($course_group_unsubscribe_right, $condition);
    }

    function update_course_type($course_type)
    {
        $condition = new EqualityCondition(CourseType :: PROPERTY_ID, $course_type->get_id());
        return $this->update($course_type, $condition);
    }

    function update_course_type_settings($course_type_settings)
    {
        $condition = new EqualityCondition(CourseTypeSettings :: PROPERTY_COURSE_TYPE_ID, $course_type_settings->get_course_type_id());
        return $this->update($course_type_settings, $condition);
    }

    function update_course_type_layout($course_type_layout)
    {
        $condition = new EqualityCondition(CourseTypeLayout :: PROPERTY_COURSE_TYPE_ID, $course_type_layout->get_course_type_id());
        return $this->update($course_type_layout, $condition);
    }

    function update_course_type_rights($course_type_rights)
    {
        $condition = new EqualityCondition(CourseTypeRights :: PROPERTY_COURSE_TYPE_ID, $course_type_rights->get_course_type_id());
        return $this->update($course_type_rights, $condition);
    }

    function update_course_type_group_creation_right($course_type_group_creation_right)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_group_creation_right->get_course_type_id());
        $conditions[] = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_GROUP_ID, $course_type_group_creation_right->get_group_id());
        $condition = new AndCondition($conditions);
        return $this->update($course_type_group_creation_right, $condition);
    }

    function update_course_type_group_subscribe_right($course_type_group_subscribe_right)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_group_subscribe_right->get_course_type_id());
        $conditions[] = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_GROUP_ID, $course_type_group_subscribe_right->get_group_id());
        $condition = new AndCondition($conditions);
        return $this->update($course_type_group_subscribe_right, $condition);
    }

    function update_course_type_group_unsubscribe_right($course_type_group_unsubscribe_right)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_group_unsubscribe_right->get_course_type_id());
        $conditions[] = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $course_type_group_unsubscribe_right->get_group_id());
        $condition = new AndCondition($conditions);
        return $this->update($course_type_group_unsubscribe_right, $condition);
    }

    function update_course_type_tool($course_type_tool)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeTool :: PROPERTY_COURSE_TYPE_ID, $course_type_tool->get_course_type_id());
        $conditions[] = new EqualityCondition(CourseTypeTool :: PROPERTY_NAME, $course_type_tool->get_name());
        $condition = new AndCondition($conditions);
        return $this->update($course_type_tool, $condition);
    }

    function update_course_category($course_category)
    {
        $condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $course_category->get_id());
        return $this->update($course_category, $condition);
    }

    function update_course_type_user_category($course_type_user_category)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_user_category->get_course_type_id());
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $course_type_user_category->get_course_user_category_id());
        $condition = new AndCondition($conditions);
        return $this->update($course_type_user_category, $condition);
    }

    function update_course_user_category($course_user_category)
    {
        $condition = new EqualityCondition(CourseUserCategory :: PROPERTY_ID, $course_user_category->get_id());
        return $this->update($course_user_category, $condition);
    }

    function update_course_user_relation($course_user_relation)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_user_relation->get_course());
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $course_user_relation->get_user());
        $condition = new AndCondition($conditions);

        return $this->update($course_user_relation, $condition);
    }

    function update_course_group_relation($course_group_relation)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE_ID, $course_group_relation->get_course_id());
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_GROUP_ID, $course_group_relation->get_group_id());
        $condition = new AndCondition($conditions);

        return $this->update($course_group_relation, $condition);
    }

    function delete_courses_by_course_type_id($course_type_id)
    {
        $condition = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $resultset = $this->retrieve_courses($condition);
        while ($result = $resultset->next_result())
        {
            if (! $this->delete_course($result->get_id()))
                return false;
        }
        return true;
    }

    function delete_course($course_code)
    {
        // Delete publication target users
        $subselect_condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_code);
        $condition = new SubselectCondition(ContentObjectPublicationUser :: PROPERTY_PUBLICATION, ContentObjectPublication :: PROPERTY_ID, ContentObjectPublication :: get_table_name(), $subselect_condition);
        if (! $this->delete_objects(ContentObjectPublicationUser :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete publication target course_groups
        $subselect_condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_code);
        $condition = new SubselectCondition(ContentObjectPublicationCourseGroup :: PROPERTY_PUBLICATION, ContentObjectPublication :: PROPERTY_ID, ContentObjectPublication :: get_table_name(), $subselect_condition);
        if (! $this->delete_objects(ContentObjectPublicationCourseGroup :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete publication categories
        $condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $course_code);
        if (! $this->delete_objects(ContentObjectPublicationCategory :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete survey invitations
        //        $subselect_condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_code);
        //    	$condition = new SubselectCondition(SurveyInvitation :: PROPERTY_SURVEY, ContentObjectPublication :: PROPERTY_ID, $this->escape_table_name(ContentObjectPublication :: get_table_name()), $subselect_condition);
        //    	if (!$this->delete_objects(SurveyInvitation :: get_table_name(), $condition))
        //    	{
        //    		return false;
        //    	}


        //         $sql = 'DELETE FROM ' . $this->escape_table_name('survey_invitation') . '
        //				WHERE survey IN (
        //					SELECT id FROM ' . $this->escape_table_name('content_object_publication') . '
        //					WHERE course = ?
        //				)';
        //        $statement = $this->get_connection()->prepare($sql);
        //        $statement->execute($course_code);


        // Delete publications
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_code);
        if (! $this->delete_objects(ContentObjectPublication :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete course sections
        $condition = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $course_code);
        if (! $this->delete_objects(CourseSection :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete modules
        $condition = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_code);
        if (! $this->delete_objects(CourseModule :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete module last access
        $condition = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_COURSE_CODE, $course_code);
        if (! $this->delete_objects(CourseModuleLastAccess :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete subscriptions of classes in the course
        //    	$condition = new EqualityCondition(CourseClassRelation :: PROPERTY_COURSE, $course_code);
        //		if (!$this->delete_objects(CourseClassRelation :: get_table_name(), $condition))
        //    	{
        //    		return false;
        //    	}


        //        $sql = 'DELETE FROM ' . $this->escape_table_name('course_rel_class') . ' WHERE course_code = ?';
        //        $statement = $this->get_connection()->prepare($sql);
        //        $statement->execute($course_code);


        //Delete rights
        $condition = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_code);
        if (! $this->delete_objects(CourseGroupSubscribeRight :: get_table_name(), $condition))
        {
            return false;
        }

        $condition = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_code);
        if (! $this->delete_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete subscriptions of users in the course
        $condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_code);
        if (! $this->delete_objects(CourseUserRelation :: get_table_name(), $condition))
        {
            return false;
        }

        $condition = new EqualityCondition(CourseRequest :: PROPERTY_COURSE_ID, $course_code);
        if (! $this->delete_objects(CourseRequest :: get_table_name(), $condition))
        {
            return false;
        }

        // Delete course
        $condition = new EqualityCondition(Course :: PROPERTY_ID, $course_code);
        $bool = $this->delete_objects(Course :: get_table_name(), $condition);

        return $bool;

     //return $bool;


    //$condition_layout = new EqualityCondition(CourseLayout :: PROPERTY_COURSE_ID, $course);
    //$bool = $bool && $this->delete(CourseLayout :: get_table_name(), $condition_layout);


    //$condition_settings = new EqualityCondition(CourseSettings :: PROPERTY_COURSE_ID, $course);
    //$bool = $bool && $this->delete(CourseSettings :: get_table_name(), $condition_settings);
    }

    function delete_course_group_subscribe_right($course_subscribe_right)
    {
        $conditions = array();
        $conditions[] = New EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_subscribe_right->get_course_id());
        $conditions[] = New EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_GROUP_ID, $course_subscribe_right->get_group_id());
        $condition = New AndCondition($conditions);
        return $this->delete_objects(CourseGroupSubscribeRight :: get_table_name(), $condition);
    }

    function delete_course_group_unsubscribe_right($course_unsubscribe_right)
    {
        $conditions = array();
        $conditions[] = New EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_unsubscribe_right->get_course_id());
        $conditions[] = New EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $course_unsubscribe_right->get_group_id());
        $condition = New AndCondition($conditions);
        return $this->delete_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition);
    }

    function delete_course_request($request)
    {
        $condition = new EqualityCondition(CourseRequest :: PROPERTY_ID, $request->get_id());
        return $this->delete(CourseRequest :: get_table_name(), $condition);
    }

    function delete_course_create_request($request)
    {
        $condition = new EqualityCondition(CourseCreateRequest :: PROPERTY_ID, $request->get_id());
        return $this->delete(CourseCreateRequest :: get_table_name(), $condition);
    }

    function delete_course_type($course_type_id)
    {
        // Delete course_type
        $condition = new EqualityCondition(CourseType :: PROPERTY_ID, $course_type_id);
        $bool = $this->delete(CourseType :: get_table_name(), $condition);

        $condition_layout = new EqualityCondition(CourseTypeLayout :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $bool = $bool && $this->delete(CourseTypeLayout :: get_table_name(), $condition_layout);

        $condition = new EqualityCondition(CourseTypeSettings :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $bool = $bool && $this->delete(CourseTypeSettings :: get_table_name(), $condition);

        $condition = new EqualityCondition(CourseTypeRights :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $bool = $bool && $this->delete(CourseTypeRights :: get_table_name(), $condition);

        $condition = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $bool = $bool && $this->delete(CourseTypeGroupSubscribeRight :: get_table_name(), $condition);

        $condition = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $bool = $bool && $this->delete(CourseTypeGroupUnsubscribeRight :: get_table_name(), $condition);

        $condition = new EqualityCondition(CourseTypeTool :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $bool = $bool && $this->delete(CourseTypeTool :: get_table_name(), $condition);

        return $bool;

     //return $bool;
    }

    function delete_course_type_group_creation_right($course_type_creation_right)
    {
        $conditions = array();
        $conditions[] = New EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_creation_right->get_course_type_id());
        $conditions[] = New EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_GROUP_ID, $course_type_creation_right->get_group_id());
        $condition = New AndCondition($conditions);
        return $this->delete_objects(CourseTypeGroupCreationRight :: get_table_name(), $condition);
    }

    function delete_course_type_group_subscribe_right($course_type_subscribe_right)
    {
        $conditions = array();
        $conditions[] = New EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_subscribe_right->get_course_type_id());
        $conditions[] = New EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_GROUP_ID, $course_type_subscribe_right->get_group_id());
        $condition = New AndCondition($conditions);
        return $this->delete_objects(CourseTypeGroupSubscribeRight :: get_table_name(), $condition);
    }

    function delete_course_type_group_unsubscribe_right($course_type_unsubscribe_right)
    {
        $conditions = array();
        $conditions[] = New EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_unsubscribe_right->get_course_type_id());
        $conditions[] = New EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $course_type_unsubscribe_right->get_group_id());
        $condition = New AndCondition($conditions);
        return $this->delete_objects(CourseTypeGroupUnsubscribeRight :: get_table_name(), $condition);
    }

    function delete_course_module($course_code, $course_name)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_code);
        $conditions[] = new EqualityCondition(CourseModule :: PROPERTY_NAME, $course_name);
        $condition = new AndCondition($conditions);
        return $this->delete_objects(CourseModule :: get_table_name(), $condition);
    }

    function retrieve_course_category($category)
    {
        $condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category);
        return $this->retrieve_object(CourseCategory :: get_table_name(), $condition, array(), CourseCategory :: CLASS_NAME);
    }

    function retrieve_course_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $order_by[] = new ObjectTableOrder(CourseCategory :: PROPERTY_NAME);
        $order_dir[] = SORT_ASC;

        return $this->retrieve_objects(CourseCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by, CourseCategory :: CLASS_NAME);
    }

    function retrieve_course_user_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(CourseUserCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by, CourseCategory :: CLASS_NAME);
    }

    function retrieve_course_user_category($condition = null)
    {
        return $this->retrieve_object(CourseUserCategory :: get_table_name(), $condition, array(), CourseUserCategory :: CLASS_NAME);
    }

    function set_module_visible($course_code, $module, $visible)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_code);
        $conditions[] = new EqualityCondition(CourseModule :: PROPERTY_NAME, $module);
        $condition = new AndCondition($conditions);

        $properties = array(CourseModule :: PROPERTY_VISIBLE => $visible);
        return $this->update_objects(CourseModule :: get_table_name(), $properties, $condition);
    }

    function set_module_id_visible($module_id, $visible)
    {
        $condition = new EqualityCondition(CourseModule :: PROPERTY_VISIBLE, $visible);
        $sort = $this->retrieve_max_sort_value(CourseModule :: get_table_name(), CourseModule :: PROPERTY_SORT, $condition);
        $condition = new EqualityCondition(CourseModule :: PROPERTY_ID, $module_id);
        $properties = array(CourseModule :: PROPERTY_VISIBLE => $visible, CourseModule :: PROPERTY_SORT => $sort + 1);
        return $this->update_objects(CourseModule :: get_table_name(), $properties, $condition);
    }

    /**
     * Moves learning object publication up
     * @param ContentObjectPublication $publication The publication to move
     * @param int $places The number of places to move the publication up
     */
    private function move_content_object_publication_up($publication, $places)
    {
        $oldIndex = $publication->get_display_order_index();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $publication->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $publication->get_tool());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $publication->get_category_id());
        $conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, InequalityCondition :: LESS_THAN, $oldIndex);
        $condition = new AndCondition($conditions);

        $properties[ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX] = $this->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '+1';

        if (! $this->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition, null, $places, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC)))
        {
            return false;
        }

        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_ID, $publication->get_id());
        $properties[ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX] = $oldIndex - $places;
        return $this->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition, null, 1);
    }

    /**
     * Moves learning object publication down
     * @param ContentObjectPublication $publication The publication to move
     * @param int $places The number of places to move the publication down
     */
    private function move_content_object_publication_down($publication, $places)
    {
        $oldIndex = $publication->get_display_order_index();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $publication->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $publication->get_tool());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $publication->get_category_id());
        $conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, InequalityCondition :: GREATER_THAN, $oldIndex);
        $condition = new AndCondition($conditions);

        $properties[ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX] = $this->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '-1';

        if (! $this->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition, null, $places, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_ASC)))
        {
            return false;
        }

        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_ID, $publication->get_id());
        $properties[ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX] = $oldIndex + $places;
        return $this->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition, null, 1);
    }

    function get_next_content_object_publication_display_order_index($course, $tool, $category)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course);
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $tool);
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category);
        $condition = new AndCondition($conditions);

        return $this->retrieve_next_sort_value(ContentObjectPublication :: get_table_name(), ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, $condition);
    }

    private function get_publication_category_tree($parent, $categories)
    {
        $subtree = array();
        foreach ($categories[$parent] as $child)
        {
            $id = $child->get_id();
            $ar = array();
            $ar['obj'] = $child;
            $ar['sub'] = $this->get_publication_category_tree($id, $categories);
            $subtree[$id] = $ar;
        }
        return $subtree;
    }

    function retrieve_content_object_publication_target_users($content_object_publication)
    {
        $condition = new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_PUBLICATION, $content_object_publication->get_id());
        $users = $this->retrieve_objects(ContentObjectPublicationUser :: get_table_name(), $condition, null, null, array(), ContentObjectPublicationUser :: CLASS_NAME);

        $target_users = array();
        while ($user = $users->next_result())
        {
            $target_users[] = $user->get_user();
        }

        return $target_users;
    }

    function retrieve_content_object_publication_target_course_groups($content_object_publication)
    {
        $condition = new EqualityCondition(ContentObjectPublicationCourseGroup :: PROPERTY_PUBLICATION, $content_object_publication->get_id());
        $course_groups = $this->retrieve_objects(ContentObjectPublicationCourseGroup :: get_table_name(), $condition, null, null, array(), ContentObjectPublicationCourseGroup :: CLASS_NAME);

        $target_course_groups = array();
        while ($course_group = $course_groups->next_result())
        {
            $target_course_groups[] = $course_group->get_course_group_id();
        }

        return $target_course_groups;
    }

    function retrieve_content_object_publication_target_groups($content_object_publication)
    {
        $condition = new EqualityCondition(ContentObjectPublicationGroup :: PROPERTY_PUBLICATION_ID, $content_object_publication->get_id());
        $groups = $this->retrieve_objects(ContentObjectPublicationGroup :: get_table_name(), $condition, null, null, array(), ContentObjectPublicationGroup :: CLASS_NAME);

        $target_groups = array();
        while ($group = $groups->next_result())
        {
            $target_groups[] = $group->get_group_id();
        }

        return $target_groups;
    }

    // Inherited
    function delete_course_type_tool($course_type_tool)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeTool :: PROPERTY_COURSE_TYPE_ID, $course_type_tool->get_course_type_id());
        $conditions[] = new EqualityCondition(CourseTypeTool :: PROPERTY_NAME, $course_type_tool->get_name());
        $condition = new AndCondition($conditions);
        return $this->delete(CourseTypeTool :: get_table_name(), $condition);
    }

    // Inherited
    function delete_course_group($course_group)
    {
        //Delete subscription of users in this course_group
        $condition = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $course_group->get_id());
        $succes = $this->delete(CourseGroupUserRelation :: get_table_name(), $condition);

        if (! $succes)
        {
            return false;
        }

        $condition = new EqualityCondition(CourseGroup :: PROPERTY_ID, $course_group->get_id());
        $succes = $this->delete(CourseGroup :: get_table_name(), $condition);

        return $succes;
    }

    // Inherited
    function create_course_group($course_group)
    {
        return $this->create($course_group);
    }

    // Inherited
    function create_course_group_user_relation($course_group_user_relation)
    {
        return $this->create($course_group_user_relation);
    }

    // Inherited
    function update_course_group($course_group)
    {
        $condition = new EqualityCondition(CourseGroup :: PROPERTY_ID, $course_group->get_id());
        return $this->update($course_group, $condition);
    }

    // Inherited
    function retrieve_course_group($id)
    {
        $condition = new EqualityCondition(CourseGroup :: PROPERTY_ID, $id);
        return $this->retrieve_object(CourseGroup :: get_table_name(), $condition, array(), CourseGroup :: CLASS_NAME);
    }

    // Inherited
    function retrieve_course_type($id)
    {
        $condition = new EqualityCondition(CourseType :: PROPERTY_ID, $id);
        $course_type = $this->retrieve_object(CourseType :: get_table_name(), $condition, array(), CourseType :: CLASS_NAME);

        if (empty($course_type))
            return $this->retrieve_empty_course_type();

     //$this->redirect(Translation :: get('CourseTypeDoesntExist'), true, array('go' => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME),array(),false,Redirect::TYPE_LINK);


        $course_type_settings = $this->retrieve_course_type_settings($id);
        if (empty($course_type_settings))
            return $this->retrieve_empty_course_type();
        $course_type->set_settings($course_type_settings);

        $course_type_layout_settings = $this->retrieve_course_type_layout($id);
        if (empty($course_type_layout_settings))
            return $this->retrieve_empty_course_type();
        $course_type->set_layout_settings($course_type_layout_settings);

        //todo
        $course_type_rights = $this->retrieve_course_type_rights($id);
        if (empty($course_type_rights))
            $course_type_rights = new CourseTypeRights();
        $course_type->set_rights($course_type_rights);

        $condition = new EqualityCondition(CourseTypeTool :: PROPERTY_COURSE_TYPE_ID, $id);
        $course_type->set_tools($this->retrieve_all_course_type_tools($condition));
        return $course_type;
    }

    function retrieve_request($id)
    {
        $condition = new EqualityCondition(CourseRequest :: PROPERTY_ID, $id);
        return $this->retrieve_object(CourseRequest :: get_table_name(), $condition, array(), CourseRequest :: CLASS_NAME);
    }

    function retrieve_course_create_request($id)
    {
        $condition = new EqualityCondition(CourseCreateRequest :: PROPERTY_ID, $id);
        return $this->retrieve_object(CourseCreateRequest :: get_table_name(), $condition, array(), CourseCreateRequest :: CLASS_NAME);
    }

    function retrieve_empty_course_type()
    {
        $course_type = new CourseType();
        $course_type->set_settings(new CourseTypeSettings());
        $course_type->set_layout_settings(new CourseTypeLayout());
        $course_type->set_rights(new CourseTypeRights());
        return $course_type;
    }

    function retrieve_course_types($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $order_by[] = new ObjectTableOrder(CourseType :: PROPERTY_NAME);
        return $this->retrieve_objects(CourseType :: get_table_name(), $condition, $offset, $max_objects, $order_by, CourseType :: CLASS_NAME);
    }

    function retrieve_requests($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $order_by[] = new ObjectTableOrder(CourseRequest :: PROPERTY_SUBJECT);
        return $this->retrieve_objects(CourseRequest :: get_table_name(), $condition, $offset, $max_objects, $order_by, CourseRequest :: CLASS_NAME);
    }

    function retrieve_course_create_requests($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $order_by[] = new ObjectTableOrder(CourseCreateRequest :: PROPERTY_SUBJECT);
        return $this->retrieve_objects(CourseCreateRequest :: get_table_name(), $condition, $offset, $max_objects, $order_by, CourseCreateRequest :: CLASS_NAME);
    }

    // Inherited
    function retrieve_course_type_settings($id)
    {
        $condition = new EqualityCondition(CourseTypeSettings :: PROPERTY_COURSE_TYPE_ID, $id);
        return $this->retrieve_object(CourseTypeSettings :: get_table_name(), $condition, array(), CourseTypeSettings :: CLASS_NAME);
    }

    // Inherited
    function retrieve_course_type_rights($id)
    {
        $condition = new EqualityCondition(CourseTypeRights :: PROPERTY_COURSE_TYPE_ID, $id);
        return $this->retrieve_object(CourseTypeRights :: get_table_name(), $condition, array(), CourseTypeRights :: CLASS_NAME);
    }

    // Inherited
    function retrieve_all_course_type_tools($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $resultset = $this->retrieve_objects(CourseTypeTool :: get_table_name(), $condition, $offset, $count, $order_property, CourseTypeTool :: CLASS_NAME);
        $objects = array();
        while ($object = $resultset->next_result())
        {
            $objects[] = $object;
        }
        return $objects;
    }

    // Inherited
    function retrieve_course_type_layout($id)
    {
        $condition = new EqualityCondition(CourseTypeLayout :: PROPERTY_COURSE_TYPE_ID, $id);
        return $this->retrieve_object(CourseTypeLayout :: get_table_name(), $condition, array(), CourseTypeLayout :: CLASS_NAME);
    }

    function retrieve_active_course_types()
    {
        $condition = new EqualityCondition(CourseType :: PROPERTY_ACTIVE, 1);
        return $this->retrieve_objects(CourseType :: get_table_name(), $condition, null, null, array(), CourseType :: CLASS_NAME);
    }

    function retrieve_course_types_by_user_right($user, $right)
    {
        $course_types = array();
        $condition = null;
        if (! $user->is_platform_admin())
            $condition = new EqualityCondition(CourseType :: PROPERTY_ACTIVE, 1);

        $course_type_objects = $this->retrieve_objects(CourseType :: get_table_name(), $condition, null, null, array(), CourseType :: CLASS_NAME);
        while ($course_type = $course_type_objects->next_result())
        {
            //User->is_platform_admin() is checked in the can_user_create function
            if ($course_type->can_user_create($user) == $right)
                $course_types[] = $course_type;
        }

        return $course_types;
    }

    function retrieve_course_group_by_name($name)
    {
        $condition = new EqualityCondition(CourseGroup :: PROPERTY_NAME, $name);
        return $this->retrieve_object(CourseGroup :: get_table_name(), $condition, array(), CourseGroup :: CLASS_NAME);
    }

    function count_requests_by_course($condition)
    {
        //$condition = new EqualityCondition(CourseRequest :: PROPERTY_COURSE_ID, $id);
        return $this->count_objects(CourseRequest :: get_table_name(), $condition);
    }

    // Inherited
    function retrieve_course_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(CourseGroup :: get_table_name(), $condition, $offset, $count, $order_property, CourseGroup :: CLASS_NAME);
    }

    function count_course_groups($condition)
    {
        return $this->count_objects(CourseGroup :: get_table_name(), $condition);
    }

    // Inherited
    function retrieve_course_group_user_ids($course_group)
    {
        $condition = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $course_group->get_id());
        $relations = $this->retrieve_objects(CourseGroupUserRelation :: get_table_name(), $condition, null, null, array(), CourseGroupUserRelation :: CLASS_NAME);
        $user_ids = array();

        while ($relation = $relations->next_result())
        {
            $user_ids[] = $relation->get_user();
        }

        return $user_ids;
    }

    function retrieve_course_group_subscribe_rights($course_id)
    {
        $condition = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_id);
        return $this->retrieve_objects(CourseGroupSubscribeRight :: get_table_name(), $condition, null, null, array(), CourseGroupSubscribeRight :: CLASS_NAME);
    }

    function retrieve_course_group_unsubscribe_rights($course_id)
    {
        $condition = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_id);
        return $this->retrieve_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition, null, null, array(), CourseGroupUnsubscribeRight :: CLASS_NAME);
    }

    function retrieve_course_type_group_rights_by_type($course_type_id, $type)
    {
        if (CourseGroupSubscribeRight :: UNSUBSCRIBE == $type)
        {
            $condition = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
            return $this->retrieve_objects(CourseTypeGroupUnsubscribeRight :: get_table_name(), $condition, null, null, array(), CourseTypeGroupUnsubscribeRight :: CLASS_NAME);
        }
        else
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
            $conditions[] = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_SUBSCRIBE, $type);
            $condition = new AndCondition($conditions);
            return $this->retrieve_objects(CourseTypeGroupSubscribeRight :: get_table_name(), $condition, null, null, array(), CourseTypeGroupSubscribeRight :: CLASS_NAME);
        }

    }

    function retrieve_course_type_group_creation_right($course_type_id, $group_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $conditions[] = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_GROUP_ID, $group_id);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(CourseTypeGroupCreationRight :: get_table_name(), $condition, array(), CourseTypeGroupCreationRight :: CLASS_NAME);
    }

    function retrieve_course_type_group_creation_rights($course_type_id)
    {
        $condition = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        return $this->retrieve_objects(CourseTypeGroupCreationRight :: get_table_name(), $condition, null, null, array(), CourseTypeGroupCreationRight :: CLASS_NAME);
    }

    function retrieve_course_type_group_subscribe_rights($course_type_id)
    {
        $condition = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        return $this->retrieve_objects(CourseTypeGroupSubscribeRight :: get_table_name(), $condition, null, null, array(), CourseTypeGroupSubscribeRight :: CLASS_NAME);
    }

    function retrieve_course_type_group_unsubscribe_rights($course_type_id)
    {
        $condition = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        return $this->retrieve_objects(CourseTypeGroupUnsubscribeRight :: get_table_name(), $condition, null, null, array(), CourseTypeGroupUnsubscribeRight :: CLASS_NAME);
    }

    function retrieve_course_type_user_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(CourseTypeUserCategory :: get_table_name(), $condition, $offset, $count, $order_property, CourseTypeUserCategory :: CLASS_NAME);
    }

    function retrieve_course_type_user_category($condition = null)
    {
        return $this->retrieve_object(CourseTypeUserCategory :: get_table_name(), $condition, array(), CourseTypeUserCategory :: CLASS_NAME);
    }

    // Inherited
    function retrieve_course_groups_from_user($user, $course = null)
    {
        $group_alias = $this->get_alias(CourseGroup :: get_table_name());
        $group_relation_alias = $this->get_alias(CourseGroupUserRelation :: get_table_name());

        $query = 'SELECT ' . $group_alias . '.* FROM ' . $this->escape_table_name(CourseGroup :: get_table_name()) . ' AS ' . $group_alias;
        $query .= ' JOIN ' . $this->escape_table_name(CourseGroupUserRelation :: get_table_name()) . ' AS ' . $group_relation_alias . ' ON ' . $this->escape_column_name(CourseGroup :: PROPERTY_ID, $group_alias) . ' = ' . $this->escape_column_name(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $group_relation_alias);

        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_USER, $user->get_id(), CourseGroupUserRelation :: get_table_name());
        if (! is_null($course))
        {
            $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, $course->get_id());
        }

        $condition = new AndCondition($conditions);

        return $this->retrieve_object_set($query, CourseGroup :: get_table_name(), $condition, null, null, array(), CourseGroup :: CLASS_NAME);
    }

    // Inherited
    function retrieve_course_group_users($course_group, $condition = null, $offset = null, $count = null, $order_property = null)
    {
        $user_ids = $this->retrieve_course_group_user_ids($course_group);

        $udm = UserDataManager :: get_instance();

        if (count($user_ids) > 0)
        {
            $user_condition = new InCondition(User :: PROPERTY_ID, $user_ids);
            if (is_null($condition))
            {
                $condition = $user_condition;
            }
            else
            {
                $condition = new AndCondition($condition, $user_condition);
            }
            return $udm->retrieve_users($condition, $offset, $count, $order_property);
        }
        else
        {
            // TODO: We need a better fix for this !
            $condition = new EqualityCondition(User :: PROPERTY_ID, '-1000');
            return $udm->retrieve_users($condition, $offset, $count, $order_property);
        }
    }

    function retrieve_course_group_user_relations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(CourseGroupUserRelation :: get_table_name(), $condition, $offset, $count, $order_property, CourseGroupUserRelation :: CLASS_NAME);
    }

    // Inherited
    function count_course_group_users($course_group, $conditions = null)
    {
        $user_ids = $this->retrieve_course_group_user_ids($course_group);
        if (count($user_ids) > 0)
        {
            $condition = new InCondition(User :: PROPERTY_ID, $user_ids);
            if (is_null($conditions))
            {
                $conditions = $condition;
            }
            else
            {
                $conditions = new AndCondition($condition, $conditions);
            }

            $udm = UserDataManager :: get_instance();
            return $udm->count_users($conditions);
        }
        else
        {
            return 0;
        }
    }

    // Inherited
    function retrieve_possible_course_group_users($course_group, $condition = null, $offset = null, $count = null, $order_property = null)
    {
        $course_condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_group->get_course_code());
        $course_users = $this->retrieve_course_user_relations($course_condition);
        $group_user_ids = $this->retrieve_course_group_user_ids($course_group);

        $course_user_ids = array();

        while ($course_user = $course_users->next_result())
        {
            $course_user_ids[] = $course_user->get_user();
        }

        $conditions = array();
        $conditions[] = $condition;
        $conditions[] = new InCondition(User :: PROPERTY_ID, $course_user_ids);
        $conditions[] = new NotCondition(new InCondition(User :: PROPERTY_ID, $group_user_ids));
        $condition = new AndCondition($conditions);

        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_users($condition, $offset, $count, $order_property);
    }

    // Inherited
    function count_possible_course_group_users($course_group, $conditions = null)
    {
        if (! is_array($conditions))
        {
            $conditions = array();
        }
        $udm = UserDataManager :: get_instance();
        $query = 'SELECT user_id FROM ' . $this->escape_table_name(CourseUserRelation :: get_table_name()) . ' WHERE ' . $this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE) . '=' . $this->quote($course_group->get_course_code());
        $res = $this->query($query);
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $course_user_ids[] = $record[User :: PROPERTY_ID];
        }

        $res->free();

        $conditions[] = new InCondition(User :: PROPERTY_ID, $course_user_ids);
        $user_ids = $this->retrieve_course_group_user_ids($course_group);
        if (count($user_ids) > 0)
        {
            $user_condition = new NotCondition(new InCondition(User :: PROPERTY_ID, $user_ids));
            $conditions[] = $user_condition;
        }
        $condition = new AndCondition($conditions);
        return $udm->count_users($condition);
    }

    // Inherited
    function subscribe_users_to_course_groups($users, $course_group)
    {
        if (! is_array($users))
        {
            $users = array($users);
        }

        foreach ($users as $user)
        {
            $course_group_user_relation = new CourseGroupUserRelation();
            $course_group_user_relation->set_course_group($course_group->get_id());
            $course_group_user_relation->set_user($user);
            if (! $course_group_user_relation->create())
            {
                return false;
            }
        }

        return true;
    }

    // Inherited
    function unsubscribe_users_from_course_groups($users, $course_group)
    {
        if (! is_array($users))
        {
            $users = array($users);
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $course_group->get_id());
        $conditions[] = new InCondition(CourseGroupUserRelation :: PROPERTY_USER, $users);
        $condition = new AndCondition($conditions);

        return $this->delete_objects(CourseGroupUserRelation :: get_table_name(), $condition);
    }

    //Inherited
    function is_course_group_member($course_group, $user)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $course_group->get_id());
        $conditions[] = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_USER, $user->get_id());
        $condition = new AndCondition($conditions);

        return $this->count_objects(CourseGroupUserRelation :: get_table_name(), $condition) > 0;
    }

    private static function from_db_date($date)
    {
        return DatabaseRepositoryDataManager :: from_db_date($date);
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category->get_id());
        $succes = $this->delete(CourseCategory :: get_table_name(), $condition);

        $conditions = array();
        $conditions[] = new InequalityCondition(CourseCategory :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $category->get_display_order());
        $conditions[] = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $category->get_parent());
        $condition = new AndCondition($conditions);

        $properties = array(
                CourseCategory :: PROPERTY_DISPLAY_ORDER => $this->escape_column_name(CourseCategory :: PROPERTY_DISPLAY_ORDER) - 1);

        return $this->update_objects(CourseCategory :: get_table_name(), $properties, $condition);
    }

    function update_category($category)
    {
        $condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category->get_id());
        return $this->update($category, $condition);
    }

    function create_category($category)
    {
        return $this->create($category);
    }

    function count_categories($conditions = null)
    {
        return $this->count_objects(CourseCategory :: get_table_name(), $conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(CourseCategory :: get_table_name(), $condition, $offset, $count, $order_property, CourseCategory :: CLASS_NAME);
    }

    function delete_content_object_publication_category($content_object_publication_category)
    {
        $condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_ID, $content_object_publication_category->get_id());
        $succes = $this->delete(ContentObjectPublicationCategory :: get_table_name(), $condition);

        $conditions = array();
        $conditions[] = new InequalityCondition(ContentObjectPublicationCategory :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $content_object_publication_category->get_display_order());
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, $content_object_publication_category->get_parent());
        $condition = new AndCondition($conditions);

        $properties = array(
                ContentObjectPublicationCategory :: PROPERTY_DISPLAY_ORDER => $this->escape_column_name(ContentObjectPublicationCategory :: PROPERTY_DISPLAY_ORDER) - 1);
        $this->update_objects(ContentObjectPublicationCategory :: get_table_name(), $properties, $condition);

        $this->delete_content_object_publication_children($content_object_publication_category->get_id());

        return $succes;
    }

    function delete_content_object_publication_children($parent_id)
    {
        $condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, $parent_id);
        $categories = $this->retrieve_content_object_publication_categories($condition);

        while ($category = $categories->next_result())
        {
            $category->delete();
            $this->delete_content_object_publication_children($category->get_id());
        }
    }

    function update_content_object_publication_category($content_object_publication_category)
    {
        $condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_ID, $content_object_publication_category->get_id());
        return $this->update($content_object_publication_category, $condition);
    }

    function create_content_object_publication_category($content_object_publication_category)
    {
        $succes = $this->create($content_object_publication_category);

        return $succes;
    }

    function count_content_object_publication_categories($conditions = null)
    {
        return $this->count_objects(ContentObjectPublicationCategory :: get_table_name(), $conditions);
    }

    function retrieve_content_object_publication_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(ContentObjectPublicationCategory :: get_table_name(), $condition, $offset, $count, $order_property, ContentObjectPublicationCategory :: CLASS_NAME);
    }

    function get_maximum_score($assessment)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment->get_id(), ComplexContentObjectItem :: get_table_name());
        $clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition);

        while ($clo_question = $clo_questions->next_result())
        {
            $maxscore += $clo_question->get_weight();
        }
        return $maxscore;
    }

    function retrieve_survey_invitations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(SurveyInvitation :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyInvitation :: CLASS_NAME);
    }

    function create_survey_invitation($survey_invitation)
    {
        return $this->create($survey_invitation);
    }

    function delete_survey_invitation($survey_invitation)
    {
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
        return $this->delete(SurveyInvitation :: get_table_name(), $condition);
    }

    function update_survey_invitation($survey_invitation)
    {
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
        return $this->update($survey_invitation, $condition);

    }

    function delete_course_section($course_section)
    {
        $condition = new EqualityCondition(CourseSection :: PROPERTY_ID, $course_section->get_id());
        if (! $this->delete(CourseSection :: get_table_name(), $condition))
        {
            return false;
        }
        else
        {
            $conditions = array();
            $conditions[] = new InequalityCondition(CourseSection :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $course_section->get_display_order());
            $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $course_section->get_course_code());
            $condition = new AndCondition($conditions);

            $properties = array(
                    CourseSection :: PROPERTY_DISPLAY_ORDER => $this->escape_column_name(CourseSection :: PROPERTY_DISPLAY_ORDER) - 1);
            if (! $this->update_objects(CourseSection :: get_table_name(), $properties, $condition))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    function change_module_course_section($module_id, $course_section_id)
    {
        $condition = new EqualityCondition(CourseModule :: PROPERTY_ID, $module_id);
        $properties = array(CourseModule :: PROPERTY_SECTION => $course_section_id);

        return $this->update_objects(CourseModule :: get_table_name(), $properties, $condition);
    }

    function update_course_section($course_section)
    {
        $condition = new EqualityCondition(CourseSection :: PROPERTY_ID, $course_section->get_id());
        return $this->update($course_section, $condition);
    }

    function create_course_section($course_section)
    {
        return $this->create($course_section);
    }

    function count_course_sections($conditions = null)
    {
        return $this->count_objects(CourseSection :: get_table_name(), $conditions);
    }

    function retrieve_course_sections($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $order_property = array(new ObjectTableOrder(CourseSection :: PROPERTY_DISPLAY_ORDER));
        return $this->retrieve_objects(CourseSection :: get_table_name(), $condition, $offset, $count, $order_property, CourseSection :: CLASS_NAME);
    }

    function times_taken($user_id, $assessment_id)
    {
        /*$query = 'SELECT COUNT('.$this->escape_column_name(UserAssessment :: PROPERTY_ID).')
		 FROM '.$this->escape_table_name(UserAssessment :: get_table_name()).'
		 WHERE '.$this->escape_column_name(UserAssessment :: PROPERTY_ASSESSMENT_ID).'='.$assessment_id.'
		 AND '.$this->escape_column_name(UserAssessment :: PROPERTY_USER_ID).'='.$user_id;
		 $sth = $this->get_connection()->prepare($query);
		 $res = $sth->execute();
		 $row = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		 return $row[0];*/
        return 0;
    }

    //Inherited.
    function is_visual_code_available($visual_code, $id = null) //course
    {
        $condition = new EqualityCondition(Course :: PROPERTY_VISUAL, $visual_code);
        if ($id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(Course :: PROPERTY_VISUAL, $visual_code);
            $conditions = new EqualityCondition(Course :: PROPERTY_ID, $id);
            $condition = new AndCondition($conditions);
        }
        return ! ($this->count_courses($condition) == 1);
    }

    function retrieve_course_by_visual_code($visual_code)
    {
        $condition = new EqualityCondition(Course :: PROPERTY_VISUAL, $visual_code);
        return $this->retrieve_object(Course :: get_table_name(), $condition, array(), Course :: CLASS_NAME);
    }

    // nested trees functions for course_groups


    function count_course_group_children($node)
    {
        return $this->count_children($node, $this->get_course_group_nested_condition($node));
    }

    function get_course_group_children($node, $recursive = false)
    {
        return $this->get_children($node, $recursive, $this->get_course_group_nested_condition($node));
    }

    function count_course_group_parents($node, $include_object = false)
    {
        return $this->count_parents($node, $include_object, $this->get_course_group_nested_condition($node));
    }

    function get_course_group_parents($node, $recursive = false, $include_object = false)
    {
        return $this->get_parents($node, $recursive, $include_object, $this->get_course_group_nested_condition($node));
    }

    function count_course_group_siblings($node, $include_object = false)
    {
        return $this->count_siblings($node, $include_object, $this->get_course_group_nested_condition($node));
    }

    function get_course_group_siblings($node, $include_object = false)
    {
        return $this->get_siblings($node, $include_object, $this->get_course_group_nested_condition($node));
    }

    function move_course_group($node, $new_parent_id = 0, $new_previous_id = 0)
    {
        return $this->move($node, $new_parent_id, $new_previous_id, $this->get_course_group_nested_condition($node));
    }

    function add_course_group_nested_values($node, $previous_visited, $number_of_elements = 1)
    {
        return $this->add_nested_values($node, $previous_visited, $number_of_elements, $this->get_course_group_nested_condition($node));
    }

    function delete_course_group_nested_values($node)
    {
        return $this->delete_nested_values($node, $this->get_course_group_nested_condition($node));
    }

    /**
     * Gets the conditions for the course group nested tree functions
     * @param CourseGroup $course_group
     */
    private function get_course_group_nested_condition($course_group)
    {
        return new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, $course_group->get_course_code());
    }

    function retrieve_course_group_root($course_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, $course_id);
        $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_PARENT_ID, 0);
        $condition = new AndCondition($conditions);
        return $this->retrieve_course_groups($condition)->next_result();
    }

    function count_new_publications_from_course($course, $user)
    {
        $publication_alias = $this->get_alias(ContentObjectPublication :: get_table_name());
        $publication_user_alias = $this->get_alias(ContentObjectPublicationUser :: get_table_name());
        $publication_group_alias = $this->get_alias(ContentObjectPublicationGroup :: get_table_name());
        $lo_table_alias = $this->get_alias(ContentObject :: get_table_name());
        $course_module_last_access_alias = $this->get_alias(CourseModuleLastAccess :: get_table_name());

        $query = 'SELECT COUNT(*) as count, ' . ContentObjectPublication :: PROPERTY_TOOL . ' FROM ' . $this->escape_table_name(ContentObjectPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' LEFT JOIN ' . $this->escape_table_name(ContentObjectPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $publication_alias . '.id = ' . $publication_user_alias . '.publication_id';
        $query .= ' LEFT JOIN ' . $this->escape_table_name(ContentObjectPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $publication_alias . '.id = ' . $publication_group_alias . '.publication_id';
        $query .= ' JOIN ' . RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $lo_table_alias . ' ON ' . $publication_alias . '.content_object_id = ' . $lo_table_alias . '.id';
        $query .= ' LEFT JOIN ' . $this->escape_table_name(CourseModuleLastAccess :: get_table_name()) . ' AS ' . $course_module_last_access_alias . ' ON ';
        $query .= $course_module_last_access_alias . '.course_id = ' . $publication_alias . '.course_id AND ';
        $query .= $course_module_last_access_alias . '.module_name = ' . $publication_alias . '.tool AND ';
        $query .= $course_module_last_access_alias . '.user_id = ' . $user->get_id();

        $course_groups = $this->retrieve_course_groups_from_user($user, $course)->as_array();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
        $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name(), ContentObject :: get_table_name()));

        $user_access_date = ' AND (' . $publication_alias . '.modified >= ' . $course_module_last_access_alias . '.access_date OR ' . $course_module_last_access_alias . '.access_date IS NULL';

        if ((! $course->is_course_admin($user)))
        {
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_HIDDEN, 0);
            $conditions_publication_period = array();
            $conditions_publication_period[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_FROM_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time());
            $conditions_publication_period[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_TO_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time());

            $user_access_date .= ' OR ' . $publication_alias . '.' . ContentObjectPublication :: PROPERTY_FROM_DATE . ' >= ' . $course_module_last_access_alias . '.' . CourseModuleLastAccess :: PROPERTY_ACCESS_DATE;

            $condition_publication_period = new AndCondition($conditions_publication_period);
            $condition_publication_forever = new EqualityCondition(ContentObjectPublication :: PROPERTY_FROM_DATE, 0);
            $conditions[] = new OrCondition($condition_publication_forever, $condition_publication_period);

            $access = array();
            $access[] = new InCondition(ContentObjectPublicationUser :: PROPERTY_USER, $user->get_id(), ContentObjectPublicationUser :: get_table_name());
            $access[] = new InCondition(ContentObjectPublicationGroup :: PROPERTY_GROUP_ID, $course_groups, ContentObjectPublicationGroup :: get_table_name());
            if (! empty($user) || ! empty($course_groups))
            {
                $access[] = new AndCondition(array(
                        new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_USER, null, ContentObjectPublicationUser :: get_table_name()),
                        new EqualityCondition(ContentObjectPublicationGroup :: PROPERTY_GROUP_ID, null, ContentObjectPublicationGroup :: get_table_name())));
            }

            $conditions[] = new OrCondition($access);
        }

        $user_access_date .= ')';

        $condition = new AndCondition($conditions);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias(ContentObjectPublication :: get_table_name()));
            $query .= $translator->render_query($condition);
        }

        $query .= $user_access_date;
        $query .= ' GROUP BY ' . ContentObjectPublication :: PROPERTY_TOOL;
        $result = $this->query($query);

        $new_publications = array();

        while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $new_publications[$record['tool']] = $record['count'];
        }

        return $new_publications;

    }

    function get_user_with_most_publications_in_course($course_id)
    {
        $content_object_publication_table_name = $this->get_table_name(ContentObjectPublication :: get_table_name());
        $course_rel_user_table_name = $this->get_table_name(CourseUserRelation :: get_table_name());
        $content_object_publication_table_alias = $this->get_alias(ContentObjectPublication :: get_table_name());
        $course_rel_user_table_alias = $this->get_alias(CourseUserRelation :: get_table_name());

        $query =  'SELECT ' . $this->escape_column_name(CourseUserRelation :: PROPERTY_USER, $course_rel_user_table_alias);
        $query .= ', COUNT(' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_ID, $content_object_publication_table_alias) . ') as count';
        $query .= ' FROM ' . $course_rel_user_table_name . ' AS ' . $course_rel_user_table_alias;
        $query .= ' LEFT JOIN ' . $content_object_publication_table_name . ' AS ' . $content_object_publication_table_alias . ' ON ';
        $query .= $this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE, $course_rel_user_table_alias) . ' = ' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_COURSE_ID, $content_object_publication_table_alias);
        $query .= ' AND ' . $this->escape_column_name(CourseUserRelation :: PROPERTY_USER, $course_rel_user_table_alias) . ' = ' . $this->escape_column_name(ContentObjectPublication :: PROPERTY_PUBLISHER_ID, $content_object_publication_table_alias);

        $condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_id, CourseUserRelation :: get_table_name());
        $translator = new ConditionTranslator($this);
        $query .= $translator->render_query($condition);

        $query .= ' GROUP BY ' . $this->escape_column_name(CourseUserRelation :: PROPERTY_USER, $course_rel_user_table_alias);
        $query .= ' ORDER BY count DESC LIMIT 0,1';

        $result = $this->query($query);
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        $res->free();

        if ($record['count'] >= 0)
        {
            return $record[CourseUserRelation :: PROPERTY_USER];
        }
    }

    // Additional Rights System


    function create_course_group_right_location($course_group_right_location)
    {
        return $this->create($course_group_right_location);
    }

    function delete_course_group_right_location($course_group_right_location)
    {
        $condition = new EqualityCondition(CourseGroupRightLocation :: PROPERTY_ID, $course_group_right_location->get_id());
        return $this->delete(CourseGroupRightLocation :: get_table_name(), $condition);
    }

    function retrieve_course_group_right_location($right_id, $course_group_id, $location_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupRightLocation :: PROPERTY_RIGHT_ID, $right_id);
        $conditions[] = new EqualityCondition(CourseGroupRightLocation :: PROPERTY_COURSE_GROUP_ID, $course_group_id);
        $conditions[] = new EqualityCondition(CourseGroupRightLocation :: PROPERTY_LOCATION_ID, $location_id);
        $condition = new AndCondition($conditions);

        return $this->retrieve_object(CourseGroupRightLocation :: get_table_name(), $condition, array(), CourseGroupRightLocation :: CLASS_NAME);
    }

    function retrieve_course_group_right_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(CourseGroupRightLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, CourseGroupRightLocation :: CLASS_NAME);
    }

    function update_course_group_right_location($course_group_right_location)
    {
        $condition = new EqualityCondition(CourseGroupRightLocation :: PROPERTY_ID, $course_group_right_location->get_id());
        return $this->update($course_group_right_location, $condition);
    }

    function delete_course_type_user_category_rel_course(CourseTypeUserCategoryRelCourse $course_type_user_category_rel_course)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_ID, $course_type_user_category_rel_course->get_course_id());
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_rel_course->get_course_type_user_category_id());
        $condition = new AndCondition($conditions);

        return $this->delete(CourseTypeUserCategoryRelCourse :: get_table_name(), $condition);
    }

    function update_course_type_user_category_rel_course(CourseTypeUserCategoryRelCourse $course_type_user_category_rel_course)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_ID, $course_type_user_category_rel_course->get_course_id());
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_rel_course->get_course_type_user_category_id());
        $condition = new AndCondition($conditions);

        return $this->update($course_type_user_category_rel_course, $condition);
    }

    function create_course_type_user_category_rel_course(CourseTypeUserCategoryRelCourse $course_type_user_category_rel_course)
    {
        return $this->create($course_type_user_category_rel_course);
    }

    function count_course_type_user_category_rel_courses($conditions = null)
    {
        return $this->count_objects(CourseTypeUserCategoryRelCourse :: get_table_name(), $conditions);
    }

    function retrieve_course_type_user_category_rel_courses($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(CourseTypeUserCategoryRelCourse :: get_table_name(), $condition, $offset, $count, $order_property, CourseTypeUserCategoryRelCourse :: CLASS_NAME);
    }

    /**
     * Cleans the sort value of a current course_type_user_category starting by the given sort value
     * @param int $start_sort_value
     * @param int $course_type_user_category_id
     */
    function clean_course_type_user_category_rel_course_sort($start_sort_value, $course_type_user_category_id)
    {
        $conditions = array();
        $conditions[] = new InEqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_SORT, InEqualityCondition :: GREATER_THAN, $start_sort_value);
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_id);
        $condition = new AndCondition($conditions);

        $properties = array();
        $properties[CourseTypeUserCategoryRelCourse :: PROPERTY_SORT] = $this->escape_column_name(CourseTypeUserCategoryRelCourse :: PROPERTY_SORT) . '-1';

        return $this->update_objects(CourseTypeUserCategoryRelCourse :: get_table_name(), $properties, $condition);
    }

    function retrieve_course_user_categories_from_course_type($course_type_id, $user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_id, CourseTypeUserCategory :: get_table_name());
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_USER_ID, $user_id, CourseTypeUserCategory :: get_table_name());
        $condition = new AndCondition($conditions);

        $course_user_category_table_name = $this->get_table_name(CourseUserCategory :: get_table_name());
        $course_type_user_category_table_name = $this->get_table_name(CourseTypeUserCategory :: get_table_name());
        $course_user_category_alias = $this->get_alias(CourseUserCategory :: get_table_name());
        $course_type_user_category_alias = $this->get_alias(CourseTypeUserCategory :: get_table_name());

        $course_user_category_id = $this->escape_column_name(CourseUserCategory :: PROPERTY_ID, $course_user_category_alias);
        $course_type_user_category_id = $this->escape_column_name(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $course_type_user_category_alias);

        $query = 'SELECT ' . $course_type_user_category_alias . '.*, ' . $course_user_category_alias . '.title FROM ' . $course_type_user_category_table_name . ' AS ' . $course_type_user_category_alias;
        $query .= ' JOIN ' . $course_user_category_table_name . ' AS ' . $course_user_category_alias . ' ON ' . $course_user_category_id . '=' . $course_type_user_category_id;

        return $this->retrieve_object_set($query, CourseTypeUserCategory :: get_table_name(), $condition, null, null, new ObjectTableOrder(CourseTypeUserCategory :: PROPERTY_SORT, SORT_ASC), CourseTypeUserCategory :: CLASS_NAME);
    }

    function retrieve_all_courses_with_course_categories($condition, $user_id)
    {
        $course_alias = $this->get_alias(Course :: get_table_name());
        $course_user_relation_alias = $this->get_alias(CourseUserRelation :: get_table_name());
        $course_group_relation_alias = $this->get_alias(CourseGroupRelation :: get_table_name());
        $course_settings_alias = $this->get_alias(CourseSettings :: get_table_name());
        $course_type_user_category_rel_course_alias = $this->get_alias(CourseTypeUserCategoryRelCourse :: get_table_name());

        $query = 'SELECT DISTINCT ' . $course_alias . '.*, ' . $course_settings_alias . '.access, ' . $course_type_user_category_rel_course_alias . '.course_type_user_category_id FROM ' . $this->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
        $query .= ' LEFT JOIN ' . $this->escape_table_name(CourseUserRelation :: get_table_name()) . ' AS ' . $course_user_relation_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseUserRelation :: PROPERTY_COURSE, $course_user_relation_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(CourseGroupRelation :: get_table_name()) . ' AS ' . $course_group_relation_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseGroupRelation :: PROPERTY_COURSE_ID, $course_group_relation_alias);
        $query .= ' JOIN ' . $this->escape_table_name(CourseSettings :: get_table_name()) . ' AS ' . $course_settings_alias . ' ON ' . $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseSettings :: PROPERTY_COURSE_ID, $course_settings_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(CourseTypeUserCategoryRelCourse :: get_table_name()) . ' AS ' . $course_type_user_category_rel_course_alias . ' ON ';
        $query .= $this->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->escape_column_name(CourseSettings :: PROPERTY_COURSE_ID, $course_type_user_category_rel_course_alias) . ' AND ';
        $query .= $this->escape_column_name(CourseTypeUserCategoryRelCourse :: PROPERTY_USER_ID, $course_type_user_category_rel_course_alias) . ' = ' . $user_id;

        $order_by[] = new ObjectTableOrder(CourseTypeUserCategoryRelCourse :: PROPERTY_SORT, SORT_ASC, $course_type_user_category_rel_course_alias);
        $order_by[] = new ObjectTableOrder(Course :: PROPERTY_NAME, SORT_ASC, $course_alias);

        return $this->retrieve_object_set($query, Course :: get_table_name(), $condition, null, null, $order_by, Course :: CLASS_NAME);
    }

}
?>
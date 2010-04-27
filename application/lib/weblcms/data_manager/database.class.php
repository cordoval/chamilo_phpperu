<?php
/**
 * $Id: database.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
 * @package application.lib.weblcms.data_manager
 */

require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
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
require_once dirname(__FILE__) . '/../course_type/course_type.class.php';
require_once dirname(__FILE__) . '/../course/course_request.class.php';
require_once dirname(__FILE__) . '/../../../../repository/lib/data_manager/database_repository_data_manager.class.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';

class DatabaseWeblcmsDataManager extends WeblcmsDataManager
{
	const ALIAS_CONTENT_OBJECT_TABLE = 'lo';
	const ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE = 'lop';

	/**
	 * @var NestedTreeDatabase
	 */
	private $database;

	function initialize()
	{
		$aliases = array();
		$aliases['course_section'] = 'cs';
		$aliases['course_category'] = 'cat';
		$aliases['content_object_publication_category'] = 'pub_cat';
		$aliases['user_answer'] = 'ans';
		$aliases['user_assessment'] = 'ass';
		$aliases['user_question'] = 'uq';
		$aliases['survey_invitation'] = 'si';
		$aliases['course_group'] = 'cg';
		$aliases['course_user_category'] = 'cuc';
		$aliases['course_user_relations'] = 'cur';

		$this->database = new NestedTreeDatabase($aliases);
		$this->database->set_prefix('weblcms_');
	}

	function get_database()
	{
		return $this->database;
	}

	function quote($value)
	{
		return $this->database->quote($value);
	}

	function query($query)
	{
		return $this->database->query($query);
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
		$this->database->get_connection()->setLimit($limit, $offset);
		$statement = $this->database->get_connection()->prepare($query, null, ($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		$statement->free();
		return $res;
	}

	function retrieve_max_sort_value($table, $column, $condition = null)
	{
		return $this->database->retrieve_max_sort_value($table, $column, $condition);
	}

	function retrieve_content_object_publication($publication_id)
	{
		$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_ID, $publication_id);
		return $this->database->retrieve_object(ContentObjectPublication :: get_table_name(), $condition);
	}

	function retrieve_content_object_publication_feedback($publication_id)
	{
		$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_PARENT_ID, $publication_id);
		return $this->database->retrieve_objects(ContentObjectPublication :: get_table_name(), $condition)->as_array();
	}

	public function content_object_is_published($object_id)
	{
		$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
		return $this->database->count_objects(ContentObjectPublication :: get_table_name(), $condition) >= 1;
	}

	public function any_content_object_is_published($object_ids)
	{
		$condition = new InCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_ids);
		return $this->database->count_objects(ContentObjectPublication :: get_table_name(), $condition) >= 1;
	}

	function get_content_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_properties = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$rdm = RepositoryDataManager :: get_instance();
				$co_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
				$pub_alias = $this->database->get_alias(ContentObjectPublication :: get_table_name());

				$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' .
				$this->database->escape_table_name(ContentObjectPublication :: get_table_name()) . ' AS ' . $pub_alias .
                		 ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias .
                		 ' ON ' . $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $pub_alias) . '=' .
				$this->database->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

				$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_PUBLISHER_ID, Session :: get_user_id());
				$translator = new ConditionTranslator($this->database);
				$query .= $translator->render_query($condition);

				$order = array();
				foreach($order_properties as $order_property)
				{
					if ($order_property->get_property() == 'application')
					{

					}
					elseif ($order_property->get_property() == 'location')
					{

					}
					elseif ($order_property->get_property() == 'title')
					{
						$order[] = $this->database->escape_column_name('title') . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
					}
					else
					{
						$order[] = $this->database->escape_column_name($order_property->get_property()) . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
					}
				}

				if(count($order) > 0)
				$query .= ' ORDER BY ' . implode(', ', $order);
			}
		}
		else
		{
			$query = 'SELECT * FROM ' . $this->database->escape_table_name(ContentObjectPublication :: get_table_name());
			$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
			$translator = new ConditionTranslator($this->database);
			$query .= $translator->render_query($condition);
		}

		$this->database->set_limit($offset, $count);
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
			$info->set_url('run.php?application=weblcms&amp;go=courseviewer&course=' . $record[ContentObjectPublication :: PROPERTY_COURSE_ID] . '&amp;tool=' . $record[ContentObjectPublication :: PROPERTY_TOOL] . '&amp;tool_action=view&amp;pid=' . $info->get_id());
			$info->set_publication_object_id($record[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

			$publication_attr[] = $info;
		}

		$res->free();

		return $publication_attr;
	}

	function get_content_object_publication_attribute($publication_id)
	{
		$query = 'SELECT * FROM ' . $this->database->escape_table_name('content_object_publication') . ' WHERE ' . $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
		$this->database->get_connection()->setLimit(0, 1);
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
		if(!$object_id)
		{
			$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_PUBLISHER_ID, $user->get_id());
		}
		else
		{
			$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
		}
		return $this->database->count_objects(ContentObjectPublication :: get_table_name(), $condition);
	}

	function retrieve_content_object_publications_new($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
	{
		$publication_alias = $this->database->get_alias(ContentObjectPublication :: get_table_name());
		$publication_user_alias = $this->database->get_alias('content_object_publication_user');
		$publication_group_alias = $this->database->get_alias('content_object_publication_course_group');
		$lo_table_alias = RepositoryDataManager :: get_instance()->get_alias('content_object');

		$query = 'SELECT DISTINCT ' . $publication_alias . '.* FROM ' . $this->database->escape_table_name(ContentObjectPublication :: get_table_name()) . ' AS ' . $publication_alias;
		$query .= ' LEFT JOIN ' . $this->database->escape_table_name('content_object_publication_user') . ' AS ' . $publication_user_alias . ' ON ' . $publication_alias . '.id = ' . $publication_user_alias . '.publication_id';
		$query .= ' LEFT JOIN ' . $this->database->escape_table_name('content_object_publication_course_group') . ' AS ' . $publication_group_alias . ' ON ' . $publication_alias . '.id = ' . $publication_group_alias . '.publication_id';
		$query .= ' JOIN ' . RepositoryDataManager :: get_instance()->escape_table_name('content_object') . ' AS ' . $lo_table_alias . ' ON ' . $publication_alias . '.content_object_id = ' . $lo_table_alias . '.id';

		return $this->database->retrieve_object_set($query, ContentObjectPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_alias($name)
	{
		return $this->database->get_alias($name);
	}

	function count_content_object_publications_new($condition)
	{
		$publication_alias = $this->database->get_alias(ContentObjectPublication :: get_table_name());
		$publication_user_alias = $this->database->get_alias('content_object_publication_user');
		$publication_group_alias = $this->database->get_alias('content_object_publication_course_group');
		$lo_table_alias = RepositoryDataManager :: get_instance()->get_alias('content_object');

		$query = 'SELECT COUNT(*) FROM ' . $this->database->escape_table_name(ContentObjectPublication :: get_table_name()) . ' AS ' . $publication_alias;
		$query .= ' LEFT JOIN ' . $this->database->escape_table_name('content_object_publication_user') . ' AS ' . $publication_user_alias . ' ON ' . $publication_alias . '.id = ' . $publication_user_alias . '.publication_id';
		$query .= ' LEFT JOIN ' . $this->database->escape_table_name('content_object_publication_course_group') . ' AS ' . $publication_group_alias . ' ON ' . $publication_alias . '.id = ' . $publication_group_alias . '.publication_id';
		$query .= ' JOIN ' . RepositoryDataManager :: get_instance()->escape_table_name('content_object') . ' AS ' . $lo_table_alias . ' ON ' . $publication_alias . '.content_object_id = ' . $lo_table_alias . '.id';

		return $this->database->count_result_set($query, ContentObjectPublication :: get_table_name(), $condition);
	}

	function count_courses($condition)
	{
		$course_alias = $this->database->get_alias(Course :: get_table_name());
		$course_settings_alias = $this->database->get_alias('course_settings');
		
		$query = 'SELECT COUNT(*) FROM ' . $this->database->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
		$query .= ' JOIN ' . $this->database->escape_table_name('course_settings') . ' AS ' . $course_settings_alias . ' ON ' . $course_alias . '.id = ' . $course_settings_alias . '.course_id';
		
		return $this->database->count_result_set($query, Course :: get_table_name(), $condition);
	}

	function count_course_types($condition = null)
	{
		return $this->database->count_objects(CourseType :: get_table_name(), $condition);
	}
	
	function count_course_type_group_creation_rights($condition = null)
	{
		return $this->database->count_objects(CourseTypeGroupCreationRight :: get_table_name(), $condition);
	}
	
	function count_course_group_subscribe_rights($condition = null)
	{
		return $this->database->count_objects(CourseGroupSubscribeRight :: get_table_name(), $condition);
	}
	
	function count_course_group_unsubscribe_rights($condition = null)
	{
		return $this->database->count_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition);
	}
	
	function count_requests($condition = null)
	{
		return $this->database->count_objects(CourseRequest :: get_table_name(), $condition);
	}

	function count_active_course_types()
	{
		$condition = new EqualityCondition(CourseType :: PROPERTY_ACTIVE, 1);
		return $this->count_course_types($condition);
	}

	function count_course_categories($condition = null)
	{
		return $this->database->count_objects(CourseCategory :: get_table_name(), $condition);
	}

	function count_user_courses($condition = null)
	{
		$course_alias = $this->database->get_alias(Course :: get_table_name());
		$course_relation_alias = $this->database->get_alias(CourseUserRelation :: get_table_name());

		$query = 'SELECT COUNT(*) FROM ' . $this->database->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
		$query .= ' JOIN ' . $this->database->escape_table_name(CourseUserRelation :: get_table_name()) . ' AS ' . $course_relation_alias . ' ON ' . $this->database->escape_column_name(Course :: PROPERTY_ID, $course_alias) . '=' . $this->database->escape_column_name(CourseUserRelation :: PROPERTY_COURSE, $course_relation_alias);
		return $this->database->count_result_set($query, Course :: get_table_name(), $condition);
	}

	function count_course_user_categories($condition = null)
	{
		return $this->database->count_objects(CourseUserCategory :: get_table_name(), $condition);
	}
	
	
	function retrieve_course_list_of_user_as_course_admin($user_id)
	{
		$conditions = array();
		$conditions = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
		$conditions = new EqualityCondition(CourseUserRelation :: PROPERTY_STATUS, 1);
		$condition = new AndCondition($conditions);

		return $this->retrieve_course_user_relations($condition);
	}

	function count_distinct_course_user_relations()
	{
		return $this->database->count_distinct(CourseUserRelation :: get_table_name(), CourseUserRelation :: PROPERTY_USER);
	}

	function count_course_user_relations($condition = null)
	{
		return $this->database->count_objects(CourseUserRelation :: get_table_name(), $condition);
	}

	function count_course_group_relations($condition = null)
	{
		return $this->database->count_objects(CourseGroupRelation :: get_table_name(), $condition);
	}

	function create_content_object_publication_user($publication_user)
	{
		return $this->database->create($publication_user);
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
		return $this->database->create($publication_course_group);
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
		return $this->database->create($publication_group);
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
		if (! $this->database->create($publication))
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
		$this->database->delete_objects('content_object_publication_user', $condition);
		$this->database->delete_objects('content_object_publication_course_group', $condition);
		$this->database->delete_objects('content_object_publication_group', $condition);

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
		return $this->database->update($publication, $condition);
	}

	function update_content_object_publication_id($publication_attr)
	{
		$where = $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
		$props = array();
		$props[$this->database->escape_column_name(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID)] = $publication_attr->get_publication_object_id();
		$this->database->get_connection()->loadModule('Extended');
		if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name('content_object_publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
		if(is_numeric($publication))
		{
			$publication = $this->retrieve_content_object_publication($publication);
		}

		$publication_id = $publication->get_id();

		$query = 'DELETE FROM ' . $this->database->escape_table_name('content_object_publication_user') . ' WHERE publication_id = ' . $this->quote($publication_id);
		$res = $this->query($query);
		$res->free();

		$query = 'DELETE FROM ' . $this->database->escape_table_name('content_object_publication_course_group') . ' WHERE publication_id = ' . $this->quote($publication_id);
		$res = $this->query($query);
		$res->free();

		$query = 'UPDATE ' . $this->database->escape_table_name('content_object_publication') . ' SET ' . $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '=' . $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '-1 WHERE ' . $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '>' . $this->quote($publication->get_display_order_index());
		$res = $this->query($query);
		$res->free();

		$query = 'DELETE FROM ' . $this->database->escape_table_name('content_object_publication') . ' WHERE ' . $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
		$this->database->get_connection()->setLimit(0, 1);
		$res = $this->query($query);
		$res->free();

		return true;
	}

	function delete_content_object_publications($object_id)
	{
		$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
		$publications = $this->retrieve_content_object_publications_new($condition);

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
		return $this->database->retrieve_object(ContentObjectPublicationCategory :: get_table_name(), $condition);
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
		return $this->database->retrieve_object(CourseModuleLastAccess :: get_table_name(), $condition, $order_by);
	}

	function retrieve_course_module_accesses($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(CourseModuleLastAccess :: get_table_name(), $condition, $offset, $max_objects, $order_by);
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
		$this->database->create($coursemodule_last_accces);
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

		$this->database->update($coursemodule_last_accces, $condition);
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
		$query = 'SELECT * FROM ' . $this->database->escape_table_name('course_module') . ' WHERE course_id = ' . $this->quote($course_code) . ' ORDER BY sort';
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
		return $this->database->retrieve_distinct(CourseModule :: get_table_name(), CourseModule :: PROPERTY_NAME);
	}

	function retrieve_course($id)
	{
		$condition = new EqualityCondition(Course :: PROPERTY_ID, $id);
		$course = $this->database->retrieve_object(Course :: get_table_name(), $condition);
		if(empty($course))
			return false;
			//$this->redirect(Translation :: get('CourseDoesntExist'), true, array('go' => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME),array(),false,Redirect::TYPE_LINK);
		$course_settings = $this->retrieve_course_settings($id);
		if(empty($course_settings))
			return false;
			//$this->redirect(Translation :: get('CourseCorrupt'), true, array('go' => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME),array(),false,Redirect::TYPE_LINK);
		$course->set_settings($course_settings);
		$course_layout_settings = $this->retrieve_course_layout($id);
		if(empty($course_layout_settings))
			return false;
			//$this->redirect(Translation :: get('CourseCorrupt'), true, array('go' => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME),array(),false,Redirect::TYPE_LINK);
		$course->set_layout_settings($course_layout_settings);
		$course_rights = $this->retrieve_course_rights($id);
		if(empty($course_rights))
			return false;
			//$this->redirect(Translation :: get('CourseCorrupt'), true, array('go' => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME),array(),false,Redirect::TYPE_LINK);
		$course->set_rights($course_rights);
		$course->set_course_type($this->retrieve_course_type($course->get_course_type_id()));
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
		return $this->database->retrieve_object(CourseGroupSubscribeRight :: get_table_name(), $condition);
	}
	
	function retrieve_course_group_unsubscribe_right($course_id, $group_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_id);
		$conditions[] = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $group_id);
		$condition = new AndCondition($conditions);
		return $this->database->retrieve_object(CourseGroupUnsubscribeRight :: get_table_name(), $condition);
	}
	
	function retrieve_course_rights($id)
	{
		$condition = new EqualityCondition(CourseRights :: PROPERTY_COURSE_ID, $id);
		return $this->database->retrieve_object(CourseRights :: get_table_name(), $condition);
	}
	
	function retrieve_course_module($id)
	{
		$condition = new EqualityCondition(CourseModule :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(CourseModule :: get_table_name(), $condition);
	}

	function retrieve_course_settings($id)
	{
		$condition = new EqualityCondition(CourseSettings :: PROPERTY_COURSE_ID, $id);
		return $this->database->retrieve_object(CourseSettings :: get_table_name(), $condition);
	}

	function retrieve_course_layout($id)
	{
		$condition = new EqualityCondition(CourseLayout :: PROPERTY_COURSE_ID, $id);
		return $this->database->retrieve_object(CourseLayout :: get_table_name(), $condition);
	}

	function retrieve_courses($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		$course_alias = $this->database->get_alias(Course :: get_table_name());
		$course_settings_alias = $this->database->get_alias(CourseSettings :: get_table_name());

		$query = 'SELECT ' . $course_alias . '.* FROM ' . $this->database->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
		$query .= ' JOIN ' . $this->database->escape_table_name(CourseSettings :: get_table_name()) . ' AS ' . $course_settings_alias . ' ON ' . $this->database->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->database->escape_column_name(CourseSettings :: PROPERTY_COURSE_ID, $course_settings_alias);

		$order_by[] = new ObjectTableOrder(Course :: PROPERTY_NAME);

		return $this->database->retrieve_object_set($query, Course :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function retrieve_course_user_relation($course_code, $user_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_code);
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
		$condition = new AndCondition($conditions);

		return $this->database->retrieve_object(CourseUserRelation :: get_table_name(), $condition);
	}

	function retrieve_course_user_relations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->database->retrieve_objects(CourseUserRelation :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	function retrieve_group_user_relation($course_id, $group_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE_ID, $course_id);
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_GROUP_ID, $user_id);
		$condition = new AndCondition($conditions);

		return $this->database->retrieve_object(CourseGroupRelation :: get_table_name(), $condition);
	}

	function retrieve_course_group_relations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->database->retrieve_objects(CourseGroupRelation :: get_table_name(), $condition, $offset, $count, $order_property);
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
		
		$course_relation_alias = $this->database->get_alias(CourseUserRelation :: get_table_name());
		$course_alias = $this->database->get_alias(Course :: get_table_name());

		$query = 'SELECT ' . $course_relation_alias . '.* FROM ' . $this->database->escape_table_name(CourseUserRelation :: get_table_name()) . ' AS ' . $course_relation_alias;
		$query .= ' JOIN ' . $this->database->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias . ' ON ' . $this->database->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->database->escape_column_name(CourseUserRelation :: PROPERTY_COURSE, $course_relation_alias);
		
		$record = $this->database->retrieve_row($query, CourseUserRelation :: get_table_name(), $condition, array(new ObjectTableOrder(CourseUserRelation :: PROPERTY_SORT, $order_direction)));

		if($record)
			return $this->database->record_to_object($record, Utilities :: underscores_to_camelcase(CourseUserRelation :: get_table_name()));
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

		return $this->database->retrieve_object(CourseTypeUserCategory :: get_table_name(), $condition, array(new ObjectTableOrder(CourseTypeUserCategory :: PROPERTY_SORT, $order_direction)));
	}

	function retrieve_user_courses($condition = null, $offset = 0, $max_objects = -1, $order_by = null)
	{
		$course_alias = $this->database->get_alias(Course :: get_table_name());
		$course_relation_alias = $this->database->get_alias(CourseUserRelation :: get_table_name());

		$query = 'SELECT ' . $course_alias . '.*, ' . $course_relation_alias . '.* FROM ' . $this->database->escape_table_name(Course :: get_table_name()) . ' AS ' . $course_alias;
		$query .= ' JOIN ' . $this->database->escape_table_name(CourseUserRelation :: get_table_name()) . ' AS ' . $course_relation_alias . ' ON ' . $this->database->escape_column_name(Course :: PROPERTY_ID, $course_alias) . ' = ' . $this->database->escape_column_name(CourseUserRelation :: PROPERTY_COURSE, $course_relation_alias);

		if(is_null($order_by))
			$order_by[] = new ObjectTableOrder(Course :: PROPERTY_NAME);

		return $this->database->retrieve_object_set($query, Course :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function create_course($course)
	{
		$now = time();
		$course->set_last_visit(self :: to_db_date($now));
		$course->set_last_edit(self :: to_db_date($now));
		$course->set_creation_date(self :: to_db_date($now));
		$course->set_expiration_date(self :: to_db_date($now));

		return $this->database->create($course);
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

		foreach($course_modules as $module)
		{
			$section_id = $sections[CourseSection :: TYPE_TOOL][0]->get_id();
			$module->set_section($section_id);
			if(!$this->create_course_module($module))
			return false;
		}

		$admin_tools = $this->get_tools('course_admin');
		foreach($admin_tools as $index => $tool_name)
		{
			$section_id = $sections[CourseSection :: TYPE_ADMIN][0]->get_id();
			$module = new CourseModule();
			$module->set_course_code($course_code);
			$module->set_name($tool_name);
			$module->set_visible(1);
			$module->set_section($section_id);
			$module->set_sort($index);
			if(!$this->create_course_module($module))
			return false;
		}

		return true;
	}

	function create_course_module($course_module)
	{
		return $this->database->create($course_module);
	}

	function create_course_settings($course_settings)
	{
		return $this->database->create($course_settings);
	}

	function create_course_layout($course_layout)
	{
		return $this->database->create($course_layout);
	}

	function create_course_type($course_type)
	{
		return $this->database->create($course_type);
	}
	
	function create_course_request($request)
	{
		return $this->database->create($request);
	}

	function create_course_type_settings($course_type_settings)
	{
		return $this->database->create($course_type_settings);
	}
	
	function create_course_type_rights($course_type_rights)
	{
		return $this->database->create($course_type_rights);
	}
	
	function create_course_rights($course_rights)
	{
		return $this->database->create($course_rights);
	}
	
	function create_course_group_subscribe_right($course_group_subscribe_right)
	{
		return $this->database->create($course_group_subscribe_right);
	}
	
	function create_course_group_unsubscribe_right($course_group_unsubscribe_right)
	{
		return $this->database->create($course_group_unsubscribe_right);
	}
	

	function create_course_type_tool($course_type_tool)
	{
		return $this->database->create($course_type_tool);
	}

	function create_course_type_layout($course_type_layout)
	{
		return $this->database->create($course_type_layout);
	}

	function create_course_type_group_subscribe_right($course_type_group_subscribe_right)
	{
		return $this->database->create($course_type_group_subscribe_right);
	}
	
	function create_course_type_group_unsubscribe_right($course_type_group_unsubscribe_right)
	{
		return $this->database->create($course_type_group_unsubscribe_right);
	}
	
	function create_course_type_group_creation_right($course_type_group_creation_right)
	{
		return $this->database->create($course_type_group_creation_right);
	}
	
	function create_course_type_user_category($course_type_user_category)
	{
		return $this->database->create($course_type_user_category);
	}
	
	function create_course_all($course)
	{
		return $this->database->create($course);
	}

	function is_subscribed($course, $user_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course->get_id());
		$condition = new AndCondition($conditions);
		return $this->database->count_objects(CourseUserRelation :: get_table_name(), $condition) > 0;
	}

	function is_group_subscribed($course_id, $group_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $group_id);
		$conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_COURSE_ID, $course_id);
		$condition = new AndCondition($conditions);
		return $this->database->count_objects(CourseGroupRelation :: get_table_name(), $condition) > 0;
	}

	function is_course_category($category)
	{
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category);
		return $this->database->count_objects(CourseCategory :: get_table_name(), $condition) > 0;
	}

	function is_course($course_code)
	{
		$condition = new EqualityCondition(Course :: PROPERTY_ID, $course_code);
		return $this->database->count_objects(Course :: get_table_name(), $condition) > 0;
	}

	function is_course_admin($course, $user_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course->get_id());
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_STATUS, 1);
		$condition = new AndCondition($conditions);
		return $this->database->count_objects(CourseUserRelation :: get_table_name(), $condition) > 0;
	}

	function subscribe_user_to_course($course, $status, $tutor_id, $user_id)
	{
		$this->database->get_connection()->loadModule('Extended');

		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id);
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, 0);
		$condition = new AndCondition($conditions);

		$sort = $this->retrieve_max_sort_value(CourseUserRelation :: get_table_name(), CourseUserRelation :: PROPERTY_SORT, $condition);

		$course_user_relation = new CourseUserRelation();
		$course_user_relation->set_course($course->get_id());
		$course_user_relation->set_user($user_id);
		$course_user_relation->set_status($status);
		$course_user_relation->set_role(null);
		$course_user_relation->set_tutor($tutor_id);
		$course_user_relation->set_sort($sort + 1);
		$course_user_relation->set_category(0);

		if ($course_user_relation->create())
		{
			// TODO: New Roles & Rights system
			//			$role_id = ($status == COURSEMANAGER) ? COURSE_ADMIN : NORMAL_COURSE_MEMBER;
			//			$location_id = RolesRights::get_course_location_id($course->get_id());
			//
			//			$user_rel_props = array();
			//			$user_rel_props['user_id'] = $user_id;
			//			$user_rel_props['role_id'] = $role_id;
			//			$user_rel_props['location_id'] = $location_id;
			//
			//			if ($this->database->get_connection()->extended->autoExecute(Database :: get_main_table(MAIN_USER_ROLE_TABLE), $user_rel_props, MDB2_AUTOQUERY_INSERT))
			//			{
			return true;
			//			}
			//			else
			//			{
			//				return false;
			//			}
		}
		else
		{
			return false;
		}
	}

	function subscribe_group_to_course(Course $course, $group_id)
	{
		$this->database->get_connection()->loadModule('Extended');

		$course_group_relation = new CourseGroupRelation();
		$course_group_relation->set_course_id($course->get_id());
		$course_group_relation->set_group_id($group_id);

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
			$props[$this->database->escape_column_name($key)] = $value;
		}

		$this->database->get_connection()->loadModule('Extended');
		if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(CourseUserRelation :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT))
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
			$props[$this->database->escape_column_name($key)] = $value;
		}

		$this->database->get_connection()->loadModule('Extended');
		if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(CourseGroupRelation :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT))
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

		return $this->database->delete_objects(CourseUserRelation :: get_table_name(), $condition);
	}

	function unsubscribe_group_from_course(Course $course, $group_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_COURSE_ID, $course->get_id());
		$conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $group_id);
		$condition = new AndCondition($conditions);

		return $this->database->delete_objects(CourseGroupRelation :: get_table_name(), $condition);
	}

	function create_course_category($course_category)
	{
		return $this->database->create($course_category);
	}

	function create_course_user_category($course_user_category)
	{
		return $this->database->create($course_user_category);
	}

	function delete_course_user_category($course_user_category)
	{
		$condition = new EqualityCondition(CourseUserCategory :: PROPERTY_ID, $course_user_category->get_id());

		return $this->database->delete_objects(CourseUserCategory :: get_table_name(), $condition);
	}
	
	function delete_course_type_user_category($course_type_user_category)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_user_category->get_course_type_id());
		$conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $course_type_user_category->get_course_user_category_id());
		$condition = new AndCondition($conditions);
		
		if ($this->database->delete_objects(CourseTypeUserCategory :: get_table_name(), $condition))
		{
			$conditions = array();
			$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, $course_type_user_category->get_course_user_category_id());
			$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $course_type_user_category->get_user_id());
			$condition = new AndCondition($conditions);

			$properties = array(CourseUserRelation :: PROPERTY_CATEGORY => 0);
			return $this->database->update_objects(CourseUserRelation :: get_table_name(), $properties, $condition);
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

		return $this->database->delete_objects(CourseUserRelation :: get_table_name(), $condition);
	}

	function delete_course_category($course_category)
	{
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $course_category->get_id());
		$success = $this->database->delete_objects(CourseCategory :: get_table_name(), $condition);

		if ($success)
		{
			$condition = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $course_category->get_id());
			$properties = array(CourseCategory :: PROPERTY_PARENT => $course_category->get_parent());
			$success = $this->database->update_objects(CourseCategory :: get_table_name(), $properties, $condition);

			if ($success)
			{
				$condition = new EqualityCondition(Course :: PROPERTY_CATEGORY, $course_category->get_id());
				$properties = array(Course :: PROPERTY_CATEGORY => $course_category->get_parent());
				return $this->database->update_objects(Course :: get_table_name(), $properties, $condition);
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
		return $this->database->update($course, $condition);
	}
	
	function update_course_request($request)
	{
		$condition = new EqualityCondition(CourseRequest :: PROPERTY_ID, $request->get_id());
		return $this->database->update($request, $condition);
	}

	function update_course_module($course_module)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_module->get_course_code());
		$conditions[] = new EqualityCondition(CourseModule :: PROPERTY_NAME, $course_module->get_name());
		$condition = new AndCondition($conditions);
		return $this->database->update($course_module, $condition);
	}

	function update_course_settings($course_settings)
	{
		$condition = new EqualityCondition(CourseSettings :: PROPERTY_COURSE_ID, $course_settings->get_course_id());
		return $this->database->update($course_settings, $condition);
	}

	function update_course_layout($course_layout)
	{
		$condition = new EqualityCondition(CourseLayout :: PROPERTY_COURSE_ID, $course_layout->get_course_id());
		return $this->database->update($course_layout, $condition);
	}
	
	function update_course_rights($course_rights)
	{
		$condition = new EqualityCondition(CourseRights :: PROPERTY_COURSE_ID, $course_rights->get_course_id());
		return $this->database->update($course_rights, $condition);
	}
	
	function update_course_group_subscribe_right($course_group_subscribe_right)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_group_subscribe_right->get_course_id());
		$conditions[] = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_GROUP_ID, $course_group_subscribe_right->get_group_id());
		$condition = new AndCondition($conditions);
		return $this->database->update($course_group_subscribe_right, $condition);
	}
	
	function update_course_group_unsubscribe_right($course_group_unsubscribe_right)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_group_unsubscribe_right->get_course_id());
		$conditions[] = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $course_group_unsubscribe_right->get_group_id());
		$condition = new AndCondition($conditions);
		return $this->database->update($course_group_unsubscribe_right, $condition);
	}

	function update_course_type($course_type)
	{
		$condition = new EqualityCondition(CourseType :: PROPERTY_ID, $course_type->get_id());
		return $this->database->update($course_type, $condition);
	}

	function update_course_type_settings($course_type_settings)
	{
		$condition = new EqualityCondition(CourseTypeSettings :: PROPERTY_COURSE_TYPE_ID, $course_type_settings->get_course_type_id());
		return $this->database->update($course_type_settings, $condition);
	}

	function update_course_type_layout($course_type_layout)
	{
		$condition = new EqualityCondition(CourseTypeLayout :: PROPERTY_COURSE_TYPE_ID, $course_type_layout->get_course_type_id());
		return $this->database->update($course_type_layout, $condition);
	}
	
	function update_course_type_rights($course_type_rights)
	{
		$condition = new EqualityCondition(CourseTypeRights :: PROPERTY_COURSE_TYPE_ID, $course_type_rights->get_course_type_id());
		return $this->database->update($course_type_rights, $condition);
	}
	
	function update_course_type_group_creation_right($course_type_group_creation_right)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_group_creation_right->get_course_type_id());
		$conditions[] = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_GROUP_ID, $course_type_group_creation_right->get_group_id());
		$condition = new AndCondition($conditions);
		return $this->database->update($course_type_group_creation_right, $condition);
	}
	
	function update_course_type_group_subscribe_right($course_type_group_subscribe_right)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_group_subscribe_right->get_course_type_id());
		$conditions[] = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_GROUP_ID, $course_type_group_subscribe_right->get_group_id());
		$condition = new AndCondition($conditions);
		return $this->database->update($course_type_group_subscribe_right, $condition);
	}
	
	function update_course_type_group_unsubscribe_right($course_type_group_unsubscribe_right)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_group_unsubscribe_right->get_course_type_id());
		$conditions[] = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $course_type_group_unsubscribe_right->get_group_id());
		$condition = new AndCondition($conditions);
		return $this->database->update($course_type_group_unsubscribe_right, $condition);
	}

	function update_course_type_tool($course_type_tool)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseTypeTool :: PROPERTY_COURSE_TYPE_ID, $course_type_tool->get_course_type_id());
		$conditions[] = new EqualityCondition(CourseTypeTool :: PROPERTY_NAME, $course_type_tool->get_name());
		$condition = new AndCondition($conditions);
		return $this->database->update($course_type_tool, $condition);
	}

	function update_course_category($course_category)
	{
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $course_category->get_id());
		return $this->database->update($course_category, $condition);
	}

	function update_course_type_user_category($course_type_user_category)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_user_category->get_course_type_id());
		$conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $course_type_user_category->get_course_user_category_id());
		$condition = new AndCondition($conditions);
		return $this->database->update($course_type_user_category, $condition);
	}
	
	function update_course_user_category($course_user_category)
	{
		$condition = new EqualityCondition(CourseUserCategory :: PROPERTY_ID, $course_user_category->get_id());
		return $this->database->update($course_user_category, $condition);
	}

	function update_course_user_relation($course_user_relation)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_user_relation->get_course());
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $course_user_relation->get_user());
		$condition = new AndCondition($conditions);

		return $this->database->update($course_user_relation, $condition);
	}

	function update_course_group_relation($course_group_relation)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE_ID, $course_group_relation->get_course_id());
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_GROUP_ID, $course_group_relation->get_group_id());
		$condition = new AndCondition($conditions);

		return $this->database->update($course_group_relation, $condition);
	}

	function delete_courses_by_course_type_id($course_type_id)
 	{
 		$condition = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
 		$resultset = $this->retrieve_courses($condition);
 		while($result = $resultset->next_result())
 		{
 			if(!$this->delete_course($result->get_id()))
 				return false;
 		}
 			return true;
 	}
 
	function delete_course($course_code)
	{
		// Delete publication target users
		$subselect_condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_code);
		$condition = new SubselectCondition(ContentObjectPublicationUser :: PROPERTY_PUBLICATION, ContentObjectPublication :: PROPERTY_ID, $this->database->escape_table_name(ContentObjectPublication :: get_table_name()), $subselect_condition);
		if (! $this->database->delete_objects(ContentObjectPublicationUser :: get_table_name(), $condition))
		{
			return false;
		}

		// Delete publication target course_groups
		$subselect_condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_code);
		$condition = new SubselectCondition(ContentObjectPublicationCourseGroup :: PROPERTY_PUBLICATION, ContentObjectPublication :: PROPERTY_ID, $this->database->escape_table_name(ContentObjectPublication :: get_table_name()), $subselect_condition);
		if (! $this->database->delete_objects(ContentObjectPublicationCourseGroup :: get_table_name(), $condition))
		{
			return false;
		}

		// Delete publication categories
		$condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $course_code);
		if (! $this->database->delete_objects(ContentObjectPublicationCategory :: get_table_name(), $condition))
		{
			return false;
		}

		// Delete survey invitations
		//        $subselect_condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_code);
		//    	$condition = new SubselectCondition(SurveyInvitation :: PROPERTY_SURVEY, ContentObjectPublication :: PROPERTY_ID, $this->database->escape_table_name(ContentObjectPublication :: get_table_name()), $subselect_condition);
		//    	if (!$this->database->delete_objects(SurveyInvitation :: get_table_name(), $condition))
		//    	{
		//    		return false;
		//    	}


		//         $sql = 'DELETE FROM ' . $this->database->escape_table_name('survey_invitation') . '
		//				WHERE survey IN (
		//					SELECT id FROM ' . $this->database->escape_table_name('content_object_publication') . '
		//					WHERE course = ?
		//				)';
		//        $statement = $this->database->get_connection()->prepare($sql);
		//        $statement->execute($course_code);


		// Delete publications
		$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_code);
		if (! $this->database->delete_objects(ContentObjectPublication :: get_table_name(), $condition))
		{
			return false;
		}

		// Delete course sections
		$condition = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $course_code);
		if (! $this->database->delete_objects(CourseSection :: get_table_name(), $condition))
		{
			return false;
		}

		// Delete modules
		$condition = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_code);
		if (! $this->database->delete_objects(CourseModule :: get_table_name(), $condition))
		{
			return false;
		}

		// Delete module last access
		$condition = new EqualityCondition(CourseModuleLastAccess :: PROPERTY_COURSE_CODE, $course_code);
		if (! $this->database->delete_objects(CourseModuleLastAccess :: get_table_name(), $condition))
		{
			return false;
		}

		// Delete subscriptions of classes in the course
		//    	$condition = new EqualityCondition(CourseClassRelation :: PROPERTY_COURSE, $course_code);
		//		if (!$this->database->delete_objects(CourseClassRelation :: get_table_name(), $condition))
		//    	{
		//    		return false;
		//    	}


		//        $sql = 'DELETE FROM ' . $this->database->escape_table_name('course_rel_class') . ' WHERE course_code = ?';
		//        $statement = $this->database->get_connection()->prepare($sql);
		//        $statement->execute($course_code);

		//Delete rights
		$condition = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_code);
		if (! $this->database->delete_objects(CourseGroupSubscribeRight :: get_table_name(), $condition))
		{
			return false;
		}
		
		$condition = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_code);
		if (! $this->database->delete_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition))
		{
			return false;
		}
		
		
		// Delete subscriptions of users in the course
		$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_code);
		if (! $this->database->delete_objects(CourseUserRelation :: get_table_name(), $condition))
		{
			return false;
		}

		// Delete course
		$condition = new EqualityCondition(Course :: PROPERTY_ID, $course_code);
		$bool = $this->database->delete_objects(Course :: get_table_name(), $condition);

		return $bool;

		//return $bool;

		//$condition_layout = new EqualityCondition(CourseLayout :: PROPERTY_COURSE_ID, $course);
		//$bool = $bool && $this->database->delete(CourseLayout :: get_table_name(), $condition_layout);

		//$condition_settings = new EqualityCondition(CourseSettings :: PROPERTY_COURSE_ID, $course);
		//$bool = $bool && $this->database->delete(CourseSettings :: get_table_name(), $condition_settings);
	}

	function delete_course_group_subscribe_right($course_subscribe_right)
	{
		$conditions = array();
		$conditions[] = New EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, $course_subscribe_right->get_course_id());
		$conditions[] = New EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_GROUP_ID, $course_subscribe_right->get_group_id());
		$condition = New AndCondition($conditions);
		return $this->database->delete_objects(CourseGroupSubscribeRight :: get_table_name(), $condition);
	}
	
	function delete_course_group_unsubscribe_right($course_unsubscribe_right)
	{
		$conditions = array();
		$conditions[] = New EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_unsubscribe_right->get_course_id());
		$conditions[] = New EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $course_unsubscribe_right->get_group_id());
		$condition = New AndCondition($conditions);
		return $this->database->delete_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition);
	}
	
	function delete_course_request($request)
	{
		$condition = new EqualityCondition(CourseRequest :: PROPERTY_ID, $request->get_id());
		return $this->database->delete(CourseRequest :: get_table_name(), $condition);
	}
	
	function delete_course_type($course_type_id)
	{
		// Delete course_type
		$condition = new EqualityCondition(CourseType :: PROPERTY_ID, $course_type_id);
		$bool = $this->database->delete(CourseType :: get_table_name(), $condition);

		$condition_layout = new EqualityCondition(CourseTypeLayout :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		$bool = $bool && $this->database->delete(CourseTypeLayout :: get_table_name(), $condition_layout);

		$condition = new EqualityCondition(CourseTypeSettings :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		$bool = $bool && $this->database->delete(CourseTypeSettings :: get_table_name(), $condition);

		$condition = new EqualityCondition(CourseTypeRights :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		$bool = $bool && $this->database->delete(CourseTypeRights :: get_table_name(), $condition);
		
		$condition = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		$bool = $bool && $this->database->delete(CourseTypeGroupCreationRight :: get_table_name(), $condition);
		
		$condition = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		$bool = $bool && $this->database->delete(CourseTypeGroupSubscribeRight :: get_table_name(), $condition);
		
		$condition = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		$bool = $bool && $this->database->delete(CourseTypeGroupUnsubscribeRight :: get_table_name(), $condition);
		
		$condition = new EqualityCondition(CourseTypeTool :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		$bool = $bool && $this->database->delete(CourseTypeTool :: get_table_name(), $condition);
		
		return $bool;

		//return $bool;
	}

	function delete_course_type_group_creation_right($course_type_creation_right)
	{
		$conditions = array();
		$conditions[] = New EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_creation_right->get_course_type_id());
		$conditions[] = New EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_GROUP_ID, $course_type_creation_right->get_group_id());
		$condition = New AndCondition($conditions);
		return $this->database->delete_objects(CourseTypeGroupCreationRight :: get_table_name(), $condition);
	}
	
	function delete_course_type_group_subscribe_right($course_type_subscribe_right)
	{
		$conditions = array();
		$conditions[] = New EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_subscribe_right->get_course_type_id());
		$conditions[] = New EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_GROUP_ID, $course_type_subscribe_right->get_group_id());
		$condition = New AndCondition($conditions);
		return $this->database->delete_objects(CourseTypeGroupSubscribeRight :: get_table_name(), $condition);
	}
	
	function delete_course_type_group_unsubscribe_right($course_type_unsubscribe_right)
	{
		$conditions = array();
		$conditions[] = New EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_unsubscribe_right->get_course_type_id());
		$conditions[] = New EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_GROUP_ID, $course_type_unsubscribe_right->get_group_id());
		$condition = New AndCondition($conditions);
		return $this->database->delete_objects(CourseTypeGroupUnsubscribeRight :: get_table_name(), $condition);
	}
	
	function delete_course_module($course_code, $course_name)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_code);
		$conditions[] = new EqualityCondition(CourseModule :: PROPERTY_NAME, $course_name);
		$condition = new AndCondition($conditions);
		return $this->database->delete_objects(CourseModule :: get_table_name(), $condition);
	}

	function retrieve_course_category($category)
	{
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category);
		return $this->database->retrieve_object(CourseCategory :: get_table_name(), $condition);
	}

	function retrieve_course_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		$order_by[] = new ObjectTableOrder(CourseCategory :: PROPERTY_NAME);
		$order_dir[] = SORT_ASC;

		return $this->database->retrieve_objects(CourseCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function retrieve_course_user_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(CourseUserCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}
	
	function retrieve_course_user_category($condition = null)
	{
		return $this->database->retrieve_object(CourseUserCategory :: get_table_name(), $condition);
	}

	function retrieve_course_user_categories_by_course_type($condition = null)
	{
		$course_alias = $this->database->get_alias(CourseUserCategory :: get_table_name());
		$course_type_alias = $this->database->get_alias(CourseTypeUserCategory :: get_table_name());

		$query = 'SELECT '.$course_alias.'.* FROM ' . $this->database->escape_table_name(CourseUserCategory :: get_table_name()) . ' AS ' . $course_alias;
		$query .= ' JOIN ' . $this->database->escape_table_name(CourseTypeUserCategory :: get_table_name()) . ' AS ' . $course_type_alias . ' ON ' . $this->database->escape_column_name(CourseUserCategory :: PROPERTY_ID, $course_alias) . '=' . $this->database->escape_column_name(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $course_type_alias);
		return $this->database->retrieve_object_set($query, CourseUserCategory :: get_table_name(), $condition);
	}
	
	function set_module_visible($course_code, $module, $visible)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseModule :: PROPERTY_COURSE_CODE, $course_code);
		$conditions[] = new EqualityCondition(CourseModule :: PROPERTY_NAME, $module);
		$condition = new AndCondition($conditions);

		$properties = array(CourseModule :: PROPERTY_VISIBLE => $visible);
		return $this->database->update_objects(CourseModule :: get_table_name(), $properties, $condition);
	}

	function set_module_id_visible($module_id, $visible)
	{
		$condition = new EqualityCondition(CourseModule :: PROPERTY_ID, $module_id);
		$properties = array(CourseModule :: PROPERTY_VISIBLE => $visible);
		return $this->database->update_objects(CourseModule :: get_table_name(), $properties, $condition);
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

		$properties[ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX] = $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '+1';

		if (! $this->database->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition, null, $places, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC)))
		{
			return false;
		}

		$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_ID, $publication->get_id());
		$properties[ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX] = $oldIndex - $places;
		return $this->database->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition, null, 1);
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

		$properties[ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX] = $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX) . '-1';

		if (! $this->database->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition, null, $places, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_ASC)))
		{
			return false;
		}

		$condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_ID, $publication->get_id());
		$properties[ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX] = $oldIndex + $places;
		return $this->database->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition, null, 1);
	}

	function get_next_content_object_publication_display_order_index($course, $tool, $category)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course);
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $tool);
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category);
		$condition = new AndCondition($conditions);

		return $this->database->retrieve_next_sort_value(ContentObjectPublication :: get_table_name(), ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, $condition);
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
		$users = $this->database->retrieve_objects(ContentObjectPublicationUser :: get_table_name(), $condition);

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
		$course_groups = $this->database->retrieve_objects(ContentObjectPublicationCourseGroup :: get_table_name(), $condition);

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
		$groups = $this->database->retrieve_objects(ContentObjectPublicationGroup :: get_table_name(), $condition);

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
		return $this->database->delete(CourseTypeTool :: get_table_name(), $condition);
	}

	// Inherited
	function delete_course_group($course_group)
	{
		//Delete subscription of users in this course_group
		$condition = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $course_group->get_id());
		$succes = $this->database->delete(CourseGroupUserRelation :: get_table_name(), $condition);

		if(!$succes)
		{
			return false;
		}

		$condition = new EqualityCondition(CourseGroup :: PROPERTY_ID, $course_group->get_id());
		$succes = $this->database->delete(CourseGroup :: get_table_name(), $condition);

		return $succes;
	}

	// Inherited
	function create_course_group($course_group)
	{
		return $this->database->create($course_group);
	}

	// Inherited
	function create_course_group_user_relation($course_group_user_relation)
	{
		return $this->database->create($course_group_user_relation);
	}

	// Inherited
	function update_course_group($course_group)
	{
		$condition = new EqualityCondition(CourseGroup :: PROPERTY_ID, $course_group->get_id());
		return $this->database->update($course_group, $condition);
	}

	// Inherited
	function retrieve_course_group($id)
	{
		$condition = new EqualityCondition(CourseGroup :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(CourseGroup :: get_table_name(), $condition);
	}

	// Inherited
	function retrieve_course_type($id)
	{
		$condition = new EqualityCondition(CourseType :: PROPERTY_ID, $id);
		$course_type = $this->database->retrieve_object(CourseType :: get_table_name(), $condition);
		
		if(empty($course_type))
			return $this->retrieve_empty_course_type();
			//$this->redirect(Translation :: get('CourseTypeDoesntExist'), true, array('go' => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME),array(),false,Redirect::TYPE_LINK);
		
		$course_type_settings = $this->retrieve_course_type_settings($id);
		if(empty($course_type_settings))
			return $this->retrieve_empty_course_type();
		$course_type->set_settings($course_type_settings);
		
		$course_type_layout_settings = $this->retrieve_course_type_layout($id);
		if(empty($course_type_layout_settings))
			return $this->retrieve_empty_course_type();
		$course_type->set_layout_settings($course_type_layout_settings);
		
		//todo
		$course_type_rights = $this->retrieve_course_type_rights($id);
		if(empty($course_type_rights))
			$course_type_rights = new CourseTypeRights();
		$course_type->set_rights($course_type_rights);
		
		$condition = new EqualityCondition(CourseTypeTool :: PROPERTY_COURSE_TYPE_ID, $id);
		$course_type->set_tools($this->retrieve_all_course_type_tools($condition));
		return $course_type;
	}
	
	function retrieve_request($id)
	{
		$condition = new EqualityCondition(CourseRequest :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(CourseRequest :: get_table_name(), $condition);
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
		return $this->database->retrieve_objects(CourseType :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}
	
	function retrieve_requests($condition = null, $offset = null, $max_objects = null, $orde_by = null)
	{
		$order_by[] = new ObjectTableOrder(CourseRequest :: PROPERTY_TITLE);
		return $this->database->retrieve_objects(CourseRequest :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	// Inherited
	function retrieve_course_type_settings($id)
	{
		$condition = new EqualityCondition(CourseTypeSettings :: PROPERTY_COURSE_TYPE_ID, $id);
		return $this->database->retrieve_object(CourseTypeSettings :: get_table_name(), $condition);
	}
	
	// Inherited
	function retrieve_course_type_rights($id)
	{
		$condition = new EqualityCondition(CourseTypeRights :: PROPERTY_COURSE_TYPE_ID, $id);
		return $this->database->retrieve_object(CourseTypeRights :: get_table_name(), $condition);
	}

	// Inherited
	function retrieve_all_course_type_tools($condition = null, $offset = null, $count = null, $order_property = null)
	{
		$resultset = $this->database->retrieve_objects(CourseTypeTool :: get_table_name(), $condition, $offset, $count, $order_property);
		$objects = array();
		while($object = $resultset->next_result())
		{
			$objects[] = $object;
		}
		return $objects;
	}

	// Inherited
	function retrieve_course_type_layout($id)
	{
		$condition = new EqualityCondition(CourseTypeLayout :: PROPERTY_COURSE_TYPE_ID, $id);
		return $this->database->retrieve_object(CourseTypeLayout :: get_table_name(), $condition);
	}

	function retrieve_active_course_types()
	{
		$condition = new EqualityCondition(CourseType :: PROPERTY_ACTIVE, 1);
		return $this->database->retrieve_objects(CourseType :: get_table_name(), $condition);
	}

	function retrieve_course_group_by_name($name)
	{
		$condition = new EqualityCondition(CourseGroup :: PROPERTY_NAME, $name);
		return $this->database->retrieve_object(CourseGroup :: get_table_name(), $condition);
	}
	
	function count_requests_by_course($condition)
	{
//		$condition = new EqualityCondition(CourseRequest :: PROPERTY_COURSE_ID, $id);
		return $this->database->count_objects(CourseRequest :: get_table_name(), $condition);
	}

	// Inherited
	function retrieve_course_groups($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->database->retrieve_objects(CourseGroup :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	function count_course_groups($condition)
	{
		return $this->database->count_objects(CourseGroup :: get_table_name(), $condition);
	}

	// Inherited
	function retrieve_course_group_user_ids($course_group)
	{
		$condition = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $course_group->get_id());
		$relations = $this->database->retrieve_objects(CourseGroupUserRelation :: get_table_name(), $condition);
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
		return $this->database->retrieve_objects(CourseGroupSubscribeRight :: get_table_name(), $condition);
	}
	
	function retrieve_course_group_unsubscribe_rights($course_id)
	{
		$condition = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, $course_id);
		return $this->database->retrieve_objects(CourseGroupUnsubscribeRight :: get_table_name(), $condition);
	}
	
	function retrieve_course_type_group_rights_by_type($course_type_id, $type)
	{
		if(CourseGroupSubscribeRight::UNSUBSCRIBE == $type)
		{
			$condition = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
			return $this->database->retrieve_objects(CourseTypeGroupUnsubscribeRight :: get_table_name(), $condition);
		}
		else
		{
			$conditions = array();
			$conditions[] = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
			$conditions[] = new EqualityCondition (CourseTypeGroupSubscribeRight :: PROPERTY_SUBSCRIBE, $type);
			$condition = new AndCondition($conditions);
			return $this->database->retrieve_objects(CourseTypeGroupSubscribeRight :: get_table_name(), $condition);
		}
		
	}
	
	function retrieve_course_type_group_creation_right($course_type_id, $group_id)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		$conditions[] = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_GROUP_ID, $group_id);
		$condition = new AndCondition($conditions);
		return $this->database->retrieve_object(CourseTypeGroupCreationRight :: get_table_name(), $condition);
	}
	
	function retrieve_course_type_group_creation_rights($course_type_id)
	{
		$condition = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		return $this->database->retrieve_objects(CourseTypeGroupCreationRight :: get_table_name(), $condition);
	}
	
	function retrieve_course_type_group_subscribe_rights($course_type_id)
	{
		$condition = new EqualityCondition(CourseTypeGroupSubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		return $this->database->retrieve_objects(CourseTypeGroupSubscribeRight :: get_table_name(), $condition);
	}
	
	function retrieve_course_type_group_unsubscribe_rights($course_type_id)
	{
		$condition = new EqualityCondition(CourseTypeGroupUnsubscribeRight :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
		return $this->database->retrieve_objects(CourseTypeGroupUnsubscribeRight :: get_table_name(), $condition);
	}
	
	function retrieve_course_type_user_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->database->retrieve_objects(CourseTypeUserCategory :: get_table_name(), $condition, $offset, $count, $order_property);
	}
	
	function retrieve_course_type_user_category($condition = null)
	{
		return $this->database->retrieve_object(CourseTypeUserCategory :: get_table_name(), $condition);
	}
	
	
	// Inherited
	function retrieve_course_groups_from_user($user, $course = null)
	{
		$group_alias = $this->database->get_alias(CourseGroup :: get_table_name());
		$group_relation_alias = $this->database->get_alias(CourseGroupUserRelation :: get_table_name());

		$query = 'SELECT ' . $group_alias . '.* FROM ' . $this->database->escape_table_name(CourseGroup :: get_table_name()) . ' AS ' . $group_alias;
		$query .= ' JOIN ' . $this->database->escape_table_name(CourseGroupUserRelation :: get_table_name()) . ' AS ' . $group_relation_alias . ' ON ' . $this->database->escape_column_name(CourseGroup :: PROPERTY_ID, $group_alias) . ' = ' . $this->database->escape_column_name(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $group_relation_alias);

		$conditions = array();
		$conditions[] = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_USER, $user->get_id(), $group_relation_alias);
		if (! is_null($course))
		{
			$conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, $course->get_id());
		}

		$condition = new AndCondition($conditions);

		return $this->database->retrieve_object_set($query, CourseGroup :: get_table_name(), $condition);
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
		$query = 'SELECT user_id FROM ' . $this->database->escape_table_name(CourseUserRelation :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(CourseUserRelation :: PROPERTY_COURSE) . '=' . $this->quote($course_group->get_course_code());
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

		return $this->database->delete_objects(CourseGroupUserRelation :: get_table_name(), $condition);
	}

	//Inherited
	function is_course_group_member($course_group, $user)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_COURSE_GROUP, $course_group->get_id());
		$conditions[] = new EqualityCondition(CourseGroupUserRelation :: PROPERTY_USER, $user->get_id());
		$condition = new AndCondition($conditions);

		return $this->database->count_objects(CourseGroupUserRelation :: get_table_name(), $condition) > 0;
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	private static function from_db_date($date)
	{
		return DatabaseRepositoryDataManager :: from_db_date($date);
	}

	private static function to_db_date($date)
	{
		return DatabaseRepositoryDataManager :: to_db_date($date);
	}

	function delete_category($category)
	{
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category->get_id());
		$succes = $this->database->delete(CourseCategory :: get_table_name(), $condition);

		$conditions = array();
		$conditions[] = new InequalityCondition(CourseCategory :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $category->get_display_order());
		$conditions[] = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $category->get_parent());
		$condition = new AndCondition($conditions);

		$properties = array(CourseCategory :: PROPERTY_DISPLAY_ORDER => $this->database->escape_column_name(CourseCategory :: PROPERTY_DISPLAY_ORDER) - 1);

		return $this->database->update_objects(CourseCategory :: get_table_name(), $properties, $condition);
	}

	function update_category($category)
	{
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_ID, $category->get_id());
		return $this->database->update($category, $condition);
	}

	function create_category($category)
	{
		return $this->database->create($category);
	}

	function count_categories($conditions = null)
	{
		return $this->database->count_objects(CourseCategory :: get_table_name(), $conditions);
	}

	function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->database->retrieve_objects(CourseCategory :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	function delete_content_object_publication_category($content_object_publication_category)
	{
		$condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_ID, $content_object_publication_category->get_id());
		$succes = $this->database->delete(ContentObjectPublicationCategory :: get_table_name(), $condition);

		$conditions = array();
		$conditions[] = new InequalityCondition(ContentObjectPublicationCategory :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $content_object_publication_category->get_display_order());
		$conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, $content_object_publication_category->get_parent());
		$condition = new AndCondition($conditions);

		$properties = array(ContentObjectPublicationCategory :: PROPERTY_DISPLAY_ORDER => $this->database->escape_column_name(ContentObjectPublicationCategory :: PROPERTY_DISPLAY_ORDER) - 1);
		$this->database->update_objects(ContentObjectPublicationCategory :: get_table_name(), $properties, $condition);

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
		return $this->database->update($content_object_publication_category, $condition);
	}

	function create_content_object_publication_category($content_object_publication_category)
	{
		return $this->database->create($content_object_publication_category);
	}

	function count_content_object_publication_categories($conditions = null)
	{
		return $this->database->count_objects(ContentObjectPublicationCategory :: get_table_name(), $conditions);
	}

	function retrieve_content_object_publication_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->database->retrieve_objects(ContentObjectPublicationCategory :: get_table_name(), $condition, $offset, $count, $order_property);
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
		return $this->database->retrieve_objects(SurveyInvitation :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function create_survey_invitation($survey_invitation)
	{
		return $this->database->create($survey_invitation);
	}

	function delete_survey_invitation($survey_invitation)
	{
		$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
		return $this->database->delete(SurveyInvitation :: get_table_name(), $condition);
	}

	function update_survey_invitation($survey_invitation)
	{
		$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
		return $this->database->update($survey_invitation, $condition);

	}

	function delete_course_section($course_section)
	{
		$condition = new EqualityCondition(CourseSection :: PROPERTY_ID, $course_section->get_id());
		if (! $this->database->delete(CourseSection :: get_table_name(), $condition))
		{
			return false;
		}
		else
		{
			$conditions = array();
			$conditions[] = new InequalityCondition(CourseSection :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $course_section->get_display_order());
			$conditions[] = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $course_section->get_course_code());
			$condition = new AndCondition($conditions);

			$properties = array(CourseSection :: PROPERTY_DISPLAY_ORDER => $this->database->escape_column_name(CourseSection :: PROPERTY_DISPLAY_ORDER) - 1);
			if (! $this->database->update_objects(CourseSection :: get_table_name(), $properties, $condition))
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

		return $this->database->update_objects(CourseModule :: get_table_name(), $properties, $condition);
	}

	function update_course_section($course_section)
	{
		$condition = new EqualityCondition(CourseSection :: PROPERTY_ID, $course_section->get_id());
		return $this->database->update($course_section, $condition);
	}

	function create_course_section($course_section)
	{
		return $this->database->create($course_section);
	}

	function count_course_sections($conditions = null)
	{
		return $this->database->count_objects(CourseSection :: get_table_name(), $conditions);
	}

	function retrieve_course_sections($condition = null, $offset = null, $count = null, $order_property = null)
	{
		$order_property = array(new ObjectTableOrder(CourseSection :: PROPERTY_DISPLAY_ORDER));
		return $this->database->retrieve_objects(CourseSection :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	function times_taken($user_id, $assessment_id)
	{
		/*$query = 'SELECT COUNT('.$this->database->escape_column_name(UserAssessment :: PROPERTY_ID).')
		 FROM '.$this->database->escape_table_name(UserAssessment :: get_table_name()).'
		 WHERE '.$this->database->escape_column_name(UserAssessment :: PROPERTY_ASSESSMENT_ID).'='.$assessment_id.'
		 AND '.$this->database->escape_column_name(UserAssessment :: PROPERTY_USER_ID).'='.$user_id;
		 $sth = $this->database->get_connection()->prepare($query);
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
		return $this->database->retrieve_object(Course :: get_table_name(), $condition);
	}

	// nested trees functions for course_groups

	function count_course_group_children($node)
	{
		return $this->database->count_children($node, $this->get_course_group_nested_condition($node));
	}

	function get_course_group_children($node, $recursive = false)
	{
		return $this->database->get_children($node, $recursive, $this->get_course_group_nested_condition($node));
	}

	function count_course_group_parents($node, $include_object = false)
	{
		return $this->database->count_parents($node, $include_object, $this->get_course_group_nested_condition($node));
	}

	function get_course_group_parents($node, $recursive = false, $include_object = false)
	{
		return $this->database->get_parents($node, $recursive, $include_object, $this->get_course_group_nested_condition($node));
	}

	function count_course_group_siblings($node, $include_object = false)
	{
		return $this->database->count_siblings($node, $include_object, $this->get_course_group_nested_condition($node));
	}

	function get_course_group_siblings($node, $include_object = false)
	{
		return $this->database->get_siblings($node, $include_object, $this->get_course_group_nested_condition($node));
	}

	function move_course_group($node, $new_parent_id = 0, $new_previous_id = 0)
	{
		return $this->database->move($node, $new_parent_id, $new_previous_id, $this->get_course_group_nested_condition($node));
	}

	function add_course_group_nested_values($node, $previous_visited, $number_of_elements = 1)
	{
		return $this->database->add_nested_values($node, $previous_visited, $number_of_elements, $this->get_course_group_nested_condition($node));
	}

	function delete_course_group_nested_values($node)
	{
		return $this->database->delete_nested_values($node, $this->get_course_group_nested_condition($node));
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


}
?>
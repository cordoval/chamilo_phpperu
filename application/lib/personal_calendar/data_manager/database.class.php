<?php
/**
 * $Id: database.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
 * @package application.personal_calendar.data_manager
 */
require_once dirname(__FILE__) . '/../personal_calendar_data_manager.class.php';
require_once dirname(__FILE__) . '/../calendar_event_publication.class.php';
require_once dirname(__FILE__) . '/../calendar_event_publication_user.class.php';
require_once dirname(__FILE__) . '/../calendar_event_publication_group.class.php';
require_once 'MDB2.php';
/**
 * This is an implementation of a personal calendar datamanager using the PEAR::
 * MDB2 package as a database abstraction layer.
 */
class DatabasePersonalCalendarDatamanager extends PersonalCalendarDatamanager
{
    /**
     * @var Database
     */
    private $database;

    function initialize()
    {
        $this->database = new Database(array());
        $this->database->set_prefix('personal_calendar_');
    }

	function quote($value)
    {
    	return $this->database->quote($value);
    }

    function query($query)
    {
    	return $this->database->query($query);
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    public function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_id);
        return $this->database->count_objects(CalendarEventPublication :: get_table_name(), $condition) >= 1;
    }

    public function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_ids);
        return $this->database->count_objects(CalendarEventPublication :: get_table_name(), $condition) >= 1;
    }

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
            	$rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->database->get_alias(CalendarEventPublication :: get_table_name());

            	$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' .
                		 $this->database->escape_table_name(CalendarEventPublication :: get_table_name()) . ' AS ' . $pub_alias .
                		 ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias .
                		 ' ON ' . $this->database->escape_column_name(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $pub_alias) . '=' .
                		 $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
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
            $query = 'SELECT * FROM ' . $this->database->escape_table_name(CalendarEventPublication :: get_table_name());
           	$condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_id);
           	$translator = new ConditionTranslator($this->database);
           	$query .= $translator->render_query($condition);
        }

        $this->database->set_limit($offset, $count);
		$res = $this->query($query);

        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record['id']);
            $info->set_publisher_user_id($record['publisher']);
            $info->set_publication_date($record['publication_date']);
            $info->set_application('personal_calendar');
            //TODO: i8n location string
            $info->set_location('');
            //TODO: set correct URL
            $info->set_url('index_personal_calendar.php?pid=' . $record['id']);
            $info->set_publication_object_id($record['calendar_event_id']);
            $publication_attr[] = $info;
        }
        
        $res->free();
        
        return $publication_attr;
    }

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    public function get_content_object_publication_attribute($publication_id)
    {
        $record = $this->retrieve_calendar_event_publication($publication_id);

        $info = new ContentObjectPublicationAttributes();
        $info->set_id($record->get_id());
        $info->set_publisher_user_id($record->get_publisher());
        $info->set_publication_date($record->get_publication_date());
        $info->set_application('personal_calendar');
        //TODO: i8n location string
        $info->set_location('');
        //TODO: set correct URL
        $info->set_url('index_personal_calendar.php?pid=' . $record->get_id());
        $info->set_publication_object_id($record->get_content_object());
        return $info;
    }

    function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if(!$object_id)
        {
    		$condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
        	$condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_id);
        }
        return $this->database->count_objects(CalendarEventPublication :: get_table_name(), $condition);
    }

    /**
     * @see Application::delete_content_object_publications()
     */
    public function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_id);
        return $this->database->delete(CalendarEventPublication :: get_table_name(), $condition);
    }
    
	function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_ID, $publication_id);
        return $this->database->delete(CalendarEventPublication :: get_table_name(), $condition);
    }

    /**
     * @see Application::update_content_object_publication_id()
     */
    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name('id') . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name('content_object')] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        return $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
    }

    //Inherited
    function retrieve_calendar_event_publication($id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(CalendarEventPublication :: get_table_name(), $condition, array(), CalendarEventPublication :: CLASS_NAME);
    }

    //Inherited.
    function retrieve_calendar_event_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->database->retrieve_objects(CalendarEventPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, CalendarEventPublication :: CLASS_NAME);
    }

    function retrieve_shared_calendar_event_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $query = 'SELECT DISTINCT ' . $this->database->get_alias(CalendarEventPublication :: get_table_name()) . '.* FROM ' . $this->database->escape_table_name(CalendarEventPublication :: get_table_name()) . ' AS ' . $this->database->get_alias(CalendarEventPublication :: get_table_name());
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name('publication_user') . ' AS ' . $this->database->get_alias('publication_user') . ' ON ' . $this->database->get_alias(CalendarEventPublication :: get_table_name()) . '.id = ' . $this->database->get_alias('publication_user') . '.publication_id';
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name('publication_group') . ' AS ' . $this->database->get_alias('publication_group') . ' ON ' . $this->database->get_alias(CalendarEventPublication :: get_table_name()) . '.id = ' . $this->database->get_alias('publication_group') . '.publication_id';

        return $this->database->retrieve_object_set($query, CalendarEventPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, CalendarEventPublication :: CLASS_NAME);
    }

    //Inherited.
    function update_calendar_event_publication($calendar_event_publication)
    {
        // Delete target users and groups
        $condition = new EqualityCondition('publication_id', $calendar_event_publication->get_id());
        $this->database->delete_objects(CalendarEventPublicationUser :: get_table_name(), $condition);
        $this->database->delete_objects(CalendarEventPublicationGroup :: get_table_name(), $condition);

        // Add updated target users and groups
        if (! $this->create_calendar_event_publication_users($calendar_event_publication))
        {
            return false;
        }

        if (! $this->create_calendar_event_publication_groups($calendar_event_publication))
        {
            return false;
        }

        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_ID, $calendar_event_publication->get_id());
        return $this->database->update($calendar_event_publication, $condition);
    }

    //Inherited
    function delete_calendar_event_publication($calendar_event_publication)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_ID, $calendar_event_publication->get_id());
        return $this->database->delete(CalendarEventPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function delete_calendar_event_publications($object_id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_id);
        return $this->database->delete_objects(CalendarEventPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function update_calendar_event_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(CalendarEventPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(CalendarEventPublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(CalendarEventPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_calendar_event_publication($publication)
    {
        if (! $this->database->create($publication))
        {
            return false;
        }

        if (! $this->create_calendar_event_publication_users($publication))
        {
            return false;
        }

        if (! $this->create_calendar_event_publication_groups($publication))
        {
            return false;
        }

        return true;
    }

    function create_calendar_event_publication_user($publication_user)
    {
        return $this->database->create($publication_user);
    }

    function create_calendar_event_publication_users($publication)
    {
        $users = $publication->get_target_users();

        foreach ($users as $index => $user_id)
        {
            $publication_user = new CalendarEventPublicationUser();
            $publication_user->set_publication($publication->get_id());
            $publication_user->set_user($user_id);

            if (! $publication_user->create())
            {
                return false;
            }
        }

        return true;
    }

    function create_calendar_event_publication_group($publication_group)
    {
        return $this->database->create($publication_group);
    }

    function create_calendar_event_publication_groups($publication)
    {
        $groups = $publication->get_target_groups();

        foreach ($groups as $index => $group_id)
        {
            $publication_group = new CalendarEventPublicationGroup();
            $publication_group->set_publication($publication->get_id());
            $publication_group->set_group_id($group_id);

            if (! $publication_group->create())
            {
                return false;
            }
        }

        return true;
    }

    function retrieve_calendar_event_publication_target_groups($calendar_event_publication)
    {
        $condition = new EqualityCondition(CalendarEventPublicationGroup :: PROPERTY_PUBLICATION, $calendar_event_publication->get_id());
        $groups = $this->database->retrieve_objects(CalendarEventPublicationGroup :: get_table_name(), $condition, null, null, array(), CalendarEventPublicationGroup :: CLASS_NAME);

        $target_groups = array();
        while ($group = $groups->next_result())
        {
            $target_groups[] = $group->get_group_id();
        }

        return $target_groups;
    }

    function retrieve_calendar_event_publication_target_users($calendar_event_publication)
    {
        $condition = new EqualityCondition(CalendarEventPublicationUser :: PROPERTY_PUBLICATION, $calendar_event_publication->get_id());
        $users = $this->database->retrieve_objects(CalendarEventPublicationUser :: get_table_name(), $condition, null, null, array(), CalendarEventPublicationUser :: CLASS_NAME);

        $target_users = array();
        while ($user = $users->next_result())
        {
            $target_users[] = $user->get_user();
        }

        return $target_users;
    }
}
?>
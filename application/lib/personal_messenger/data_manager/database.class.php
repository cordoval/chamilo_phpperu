<?php
/**
 * $Id: database.class.php 230 2009-11-16 09:29:45Z vanpouckesven $
 * @package application.personal_messenger.data.manager
 */
require_once dirname(__FILE__) . '/../personal_messenger_data_manager.class.php';
require_once dirname(__FILE__) . '/../personal_message_publication.class.php';

require_once 'MDB2.php';

class DatabasePersonalMessengerDataManager extends PersonalMessengerDataManager
{
    
    private $database;
    
    const ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE = 'lop';
    const ALIAS_CONTENT_OBJECT_TABLE = 'lo';

    function initialize()
    {
        $this->database = new Database(array());
        $this->database->set_prefix('personal_messenger_');
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

    // Inherited.
    function get_next_personal_message_publication_id()
    {
        return $this->database->get_next_id(PersonalMessagePublication :: get_table_name());
    }

    // Inherited.
    function count_personal_message_publications($condition = null)
    {
        return $this->database->count_objects(PersonalMessagePublication :: get_table_name(), $condition);
    }

    // Inherited.
    function count_unread_personal_message_publications($user)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_USER, $user);
        $conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_STATUS, 1);
        $condition = new AndCondition($conditions);
        
        return $this->database->count_objects(PersonalMessagePublication :: get_table_name(), $condition);
    }

    // Inherited.
    function retrieve_personal_message_publication($id)
    {
        $condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(PersonalMessagePublication :: get_table_name(), $condition, array(), PersonalMessagePublication :: CLASS_NAME);
    }

    // Inherited.
    function retrieve_personal_message_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->database->retrieve_objects(PersonalMessagePublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, PersonalMessagePublication :: CLASS_NAME);
    }

    // Inherited.
    function update_personal_message_publication($personal_message_publication)
    {
        $condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_ID, $personal_message_publication->get_id());
        return $this->database->update($personal_message_publication, $condition);
    }

    // Inherited.
    function delete_personal_message_publication($personal_message_publication)
    {
        $condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_ID, $personal_message_publication->get_id());
        return $this->database->delete(PersonalMessagePublication :: get_table_name(), $condition);
    }

    // Inherited.
    function delete_personal_message_publications($object_id)
    {
        $condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE, $object_id);
        return $this->database->delete_objects(PersonalMessagePublication :: get_table_name(), $condition);
    }

    // Inherited.
    function update_personal_message_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(PersonalMessagePublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(PersonalMessagePublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    // Inherited.
    public function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE, $object_ids);
        return $this->database->count_objects(PersonalMessagePublication :: get_table_name(), $condition) >= 1;
    }

    // Inherited.
    public function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE, $object_id);
        return $this->database->count_objects(CalendarEventPublication :: get_table_name(), $condition) >= 1;
    }

    // Inherited.
    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    // Inherited.
    function get_content_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $query = 'SELECT ' . self :: ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE . '.*, ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name('title') . ' FROM ' . $this->database->escape_table_name('publication') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE;
                $query .= ' JOIN ' . RepositoryDataManager :: get_instance()->get_database()->escape_table_name('content_object') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_TABLE . ' ON ' . self :: ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE . '.`personal_message_id` = ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.`id`';
                $query .= ' WHERE ' . self :: ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE . '.' . $this->database->escape_column_name(PersonalMessagePublication :: PROPERTY_USER) . '=' . $this->quote($user->get_id());
                
                $order = array();
                for($i = 0; $i < count($order_property); $i ++)
                {
                    if ($order_property[$i] == 'application' || $order_property[$i] == 'location')
                    {
                    }
                    elseif ($order_property[$i] == 'title')
                    {
                        $order[] = self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name('title') . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        //$order[] = self :: ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE. '.' .$this->database->escape_column_name($order_property[$i], true).' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                        $order[] = self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name('title') . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }
                if (count($order))
                {
                    $query .= ' ORDER BY ' . implode(', ', $order);
                }
            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->database->escape_table_name('publication') . ' WHERE ' . $this->database->escape_column_name(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE) . '=' . $this->quote($object_id);
        }
        
        $res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $publication = $this->database->record_to_class_object($record, PersonalMessagePublication :: CLASS_NAME);
            
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($publication->get_id());
            $info->set_publisher_user_id($publication->get_sender());
            $info->set_publication_date($publication->get_published());
            $info->set_application('Personal Messenger');
            //TODO: i8n location string
            if ($publication->get_user() == $publication->get_recipient())
            {
                $recipient = $publication->get_publication_recipient();
                $info->set_location($recipient->get_firstname() . '&nbsp;' . $recipient->get_lastname() . '&nbsp;/&nbsp;' . Translation :: get('Inbox'));
            }
            elseif ($publication->get_user() == $publication->get_sender())
            {
                $sender = $publication->get_publication_sender();
                $info->set_location($sender->get_firstname() . '&nbsp;' . $sender->get_lastname() . '&nbsp;/&nbsp;' . Translation :: get('Outbox'));
            }
            else
            {
                $info->set_location(Translation :: get('UnknownLocation'));
            }
            
            if ($publication->get_user() == $user->get_id())
            {
                $info->set_url('index_personal_messenger.php?go=view&pm=' . $publication->get_id());
            }
            $info->set_publication_object_id($publication->get_personal_message());
            
            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    // Inherited.
    function get_content_object_publication_attribute($publication_id)
    {
        $publication = $this->retrieve_personal_message_publication($publication_id);
        
        $info = new ContentObjectPublicationAttributes();
        $info->set_id($publication->get_id());
        $info->set_publisher_user_id($publication->get_sender());
        $info->set_publication_date($publication->get_published());
        $info->set_application('Personal Messenger');
        //TODO: i8n location string
        if ($publication->get_user() == $publication->get_recipient())
        {
            $recipient = $publication->get_publication_recipient();
            $info->set_location($recipient->get_firstname() . '&nbsp;' . $recipient->get_lastname() . '&nbsp;/&nbsp;' . Translation :: get('Inbox'));
        }
        elseif ($publication->get_user() == $publication->get_sender())
        {
            $sender = $publication->get_publication_sender();
            $info->set_location($sender->get_firstname() . '&nbsp;' . $sender->get_lastname() . '&nbsp;/&nbsp;' . Translation :: get('Outbox'));
        }
        else
        {
            $info->set_location(Translation :: get('UnknownLocation'));
        }
        
        if ($publication->get_user() == $user->get_id())
        {
            $info->set_url('index_personal_messenger.php?go=view&pm=' . $publication->get_id());
        }
        $info->set_publication_object_id($publication->get_personal_message());
        
        return $info;
    }

    // Inherited.
    function count_publication_attributes($user, $type = null, $condition = null)
    {
        $condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_USER, Session :: get_user_id());
        return $this->database->count_objects(PersonalMessagePublication :: get_table_name(), $condition);
    }

    // Inherited.
    function create_personal_message_publication($publication)
    {
        return $this->database->create($publication);
    }
}
?>
<?php
/**
 * $Id: database.class.php 234 2009-11-16 11:34:07Z vanpouckesven $
 * @package repository.lib.data_manager
 */
require_once dirname(__FILE__) . '/database/database_content_object_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_complex_content_object_item_result_set.class.php';
require_once dirname(__FILE__) . '/../category_manager/repository_category.class.php';

require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
==============================================================================
 */

class DatabaseRepositoryDataManager extends RepositoryDataManager
{
    const ALIAS_CONTENT_OBJECT_PUB_FEEDBACK_TABLE = 'lopf';
    const ALIAS_CONTENT_OBJECT_TABLE = 'coct';
    const ALIAS_CONTENT_OBJECT_VERSION_TABLE = 'lov';
    const ALIAS_CONTENT_OBJECT_ATTACHMENT_TABLE = 'loa';
    const ALIAS_TYPE_TABLE = 'tt';
    const ALIAS_CONTENT_OBJECT_PARENT_TABLE = 'lop';
    const ALIAS_COMPLEX_CONTENT_OBJECT_ITEM_TABLE = 'coem';

    /**
     * @var Database
     */
    private $database;

    // Inherited.
    function initialize()
    {
        PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array(get_class(), 'handle_error'));
        $this->database = new Database(array('repository_category' => 'cat', 'user_view' => 'uv', 'user_view_rel_content_object' => 'uvrlo', 'content_object_pub_feedback' => 'lopf'));
        $this->database->set_prefix('repository_');
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
     * This function can be used to handle some debug info from MDB2
     */
    function debug()
    {
        $args = func_get_args();
        // Do something with the arguments
        if ($args[1] == 'query')
        {
            echo '<pre>';
            echo ($args[2]);
            echo '</pre>';
        }
    }

    // Inherited.
    function determine_content_object_type($id)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $id);
        $record = $this->database->retrieve_record(ContentObject :: get_table_name(), $condition);
        return $record[ContentObject :: PROPERTY_TYPE];
    }

    // Inherited.
    function retrieve_content_object($id, $type = null)
    {
        if(!isset($id) || strlen($id) == 0 || $id == DataClass :: NO_UID)
        {
            return null;
        }
        
        if (is_null($type))
        {
            $type = $this->determine_content_object_type($id);
        }

        $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $id);

        if ($this->is_extended_type($type))
        {
            $content_object_alias = $this->database->get_alias(ContentObject :: get_table_name());

            $query = 'SELECT * FROM ' . $this->database->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias;
            $query .= ' JOIN ' . $this->database->escape_table_name($type) . ' AS ' . self :: ALIAS_TYPE_TABLE . ' ON ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . '=' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID, self :: ALIAS_TYPE_TABLE);

            $record = $this->database->retrieve_row($query, ContentObject :: get_table_name(), $condition);
        }
        else
        {
            $record = $this->database->retrieve_record(ContentObject :: get_table_name(), $condition);
        }

        return self :: record_to_content_object($record, isset($type));
    }

    // Inherited.
    // TODO: Extract methods.
    function retrieve_content_objects($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $query = 'SELECT * FROM ';
        $query .= $this->database->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . self :: ALIAS_CONTENT_OBJECT_TABLE;
        $query .= ' JOIN ' . $this->database->escape_table_name('content_object_version') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . ' ON ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . ContentObject :: PROPERTY_ID . ' = ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . '.' . ContentObject :: PROPERTY_ID;

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        /*
		 * Always respect display order as a last resort.
		 */
        $order_by[] = new ObjectTableOrder(ContentObject :: PROPERTY_DISPLAY_ORDER_INDEX);

        $orders = array();
        foreach ($order_by as $order)
        {
            $orders[] = $this->database->escape_column_name($order->get_property()) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }

        if ($max_objects < 0)
        {
            $max_objects = null;
        }
        //echo $query; dump($params);
        $this->database->set_limit(intval($max_objects), intval($offset));
        $res = $this->query($query);
        return new DatabaseContentObjectResultSet($this, $res, false);
    }

    function retrieve_type_content_objects($type, $condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $content_object_alias = $this->database->get_alias(ContentObject :: get_table_name());
        $content_object_version_alias = $this->database->get_alias('content_object_version');

        $query = 'SELECT * FROM ' . $this->database->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias;

        if (ContentObject :: is_extended_type($type))
        {
            $type_alias = $this->database->get_alias($type);
            $query .= ' JOIN ' . $this->database->escape_table_name($type) . ' AS ' . $type_alias . ' ON ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . ' = ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $type_alias);
        }
        else
        {
            $type_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
            $condition = isset($condition) ? new AndCondition($type_condition, $condition) : $type_condition;
        }

        $query .= ' JOIN ' . $this->database->escape_table_name('content_object_version') . ' AS ' . $content_object_version_alias . ' ON ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . ' = ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_version_alias);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        $orders = array();
        foreach ($order_by as $order)
        {
            $orders[] = $this->database->escape_column_name($order->get_property()) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }

        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }

        if ($max_objects < 0)
        {
            $max_objects = null;
        }

        $this->database->set_limit(intval($max_objects), intval($offset));
        $res = $this->query($query);
        return new DatabaseContentObjectResultSet($this, $res, true);
    }

    // Inherited.
    function retrieve_additional_content_object_properties($content_object)
    {
        $type = $content_object->get_type();
        if (! $this->is_extended_type($type))
        {
            return array();
        }
        $array = array_map(array($this, 'escape_column_name'), $content_object->get_additional_property_names());

        if (count($array) == 0)
        {
            $array = array("*");
        }

        $query = 'SELECT ' . implode(',', $array) . ' FROM ' . $this->database->escape_table_name($type) . ' WHERE ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . '=' . $this->quote($content_object->get_id());

        $this->database->set_limit(1);
        $res = $this->query($query);
        return $res->fetchRow(MDB2_FETCHMODE_ASSOC);
    }

    // Inherited.
    // TODO: Extract methods; share stuff with retrieve_content_objects.
    function count_content_objects($condition = null)
    {
        $query = 'SELECT COUNT(' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . ') FROM ' . $this->database->escape_table_name('content_object') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_TABLE;

        $query .= ' JOIN ' . $this->database->escape_table_name('content_object_version') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . ' ON ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . ContentObject :: PROPERTY_ID . ' = ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . '.' . ContentObject :: PROPERTY_ID;

        return $this->database->count_result_set($query, ContentObject :: get_table_name(), $condition);
    }

    function count_type_content_objects($type, $condition = null)
    {
        if ($this->is_extended_type($type))
        {
            $query = 'SELECT COUNT(' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . ') FROM ' . $this->database->escape_table_name('content_object') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_TABLE . ' JOIN ' . $this->database->escape_table_name($type) . ' AS ' . $this->database->get_alias($type) . ' ON ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . ' = ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $this->database->get_alias($type));
        }
        else
        {
            $query = 'SELECT COUNT(' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . ') FROM ' . $this->database->escape_table_name('content_object') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_TABLE;
            $match = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
            $condition = isset($condition) ? new AndCondition(array($match, $condition)) : $match;
        }

        $query .= ' JOIN ' . $this->database->escape_table_name('content_object_version') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . ' ON ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . ContentObject :: PROPERTY_ID . ' = ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . '.' . ContentObject :: PROPERTY_ID;

        return $this->database->count_result_set($query, ContentObject :: get_table_name(), $condition);
    }

    // Inherited
    function count_content_object_versions($object)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        return $this->database->count_objects(ContentObject :: get_table_name(), $condition);
    }

    // Inherited.
    function get_next_content_object_id()
    {
//        return $this->database->get_better_next_id(ContentObject :: get_table_name(), ContentObject :: PROPERTY_ID);
        return $this->database->get_next_id(ContentObject :: get_table_name());
    }

    function get_next_content_object_pub_feedback_id()
    {
        return $this->database->get_next_id(ContentObjectPubFeedback :: get_table_name());
    }

    function get_next_content_object_number()
    {
        return $this->database->get_next_id(ContentObject :: get_table_name() . '_number');
    }

    // Inherited.
    function create_content_object($object, $type)
    {
        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$this->database->escape_column_name($key)] = $value;
        }
        $props[$this->database->escape_column_name(ContentObject :: PROPERTY_ID)] = $object->get_id();
        $props[$this->database->escape_column_name(ContentObject :: PROPERTY_TYPE)] = $object->get_type();
        $props[$this->database->escape_column_name(ContentObject :: PROPERTY_CREATION_DATE)] = self :: to_db_date($object->get_creation_date());
        $props[$this->database->escape_column_name(ContentObject :: PROPERTY_MODIFICATION_DATE)] = self :: to_db_date($object->get_modification_date());
//        $props[$this->database->escape_column_name(ContentObject :: PROPERTY_ID)] = $this->database->get_better_next_id('content_object', 'id');
        $this->database->get_connection()->loadModule('Extended');
        $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('content_object'), $props, MDB2_AUTOQUERY_INSERT);
//        $object->set_id($this->database->get_connection()->extended->getAfterID($props[$this->database->escape_column_name(ContentObject :: PROPERTY_ID)], 'content_object'));
        if ($object->is_extended())
        {
            $props = array();
            foreach ($object->get_additional_properties() as $key => $value)
            {
                $props[$this->database->escape_column_name($key)] = $value;
            }
            $props[$this->database->escape_column_name(ContentObject :: PROPERTY_ID)] = $object->get_id();
            $this->database->get_connection()->extended->autoExecute($this->database->get_table_name($object->get_type()), $props, MDB2_AUTOQUERY_INSERT);
        }

        $props = array();
        $props[$this->database->escape_column_name(ContentObject :: PROPERTY_ID)] = $object->get_id();
        if ($type == 'new')
        {
            $props[$this->database->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER)] = $object->get_object_number();
            $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('content_object_version'), $props, MDB2_AUTOQUERY_INSERT);
        }
        elseif ($type == 'version')
        {
            $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('content_object_version'), $props, MDB2_AUTOQUERY_UPDATE, $this->database->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . '=' . $object->get_object_number());
        }
        else
        {
            return false;
        }

        return true;
    }

    // Inherited.
    function update_content_object($object)
    {
        $where = $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . '=' . $object->get_id();
        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$this->database->escape_column_name($key)] = $value;
        }
        $props[$this->database->escape_column_name(ContentObject :: PROPERTY_CREATION_DATE)] = self :: to_db_date($object->get_creation_date());
        $props[$this->database->escape_column_name(ContentObject :: PROPERTY_MODIFICATION_DATE)] = self :: to_db_date($object->get_modification_date());
        $this->database->get_connection()->loadModule('Extended');
        $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('content_object'), $props, MDB2_AUTOQUERY_UPDATE, $where);
        if ($object->is_extended())
        {
            $props = array();
            foreach ($object->get_additional_properties() as $key => $value)
            {
                $props[$this->database->escape_column_name($key)] = $value;
            }
            $this->database->get_connection()->extended->autoExecute($this->database->get_table_name($object->get_type()), $props, MDB2_AUTOQUERY_UPDATE, $where);
        }
        return true;
    }

    //Inherited.
    function retrieve_content_object_by_user($user_id)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id);
        return $this->database->retrieve_objects(ContentObject :: get_table_name(), $condition);
    }

    function delete_content_object_by_id($object_id)
    {
        $object = $this->retrieve_content_object($object_id);
        return $this->delete_content_object($object);
    }

    // Inherited.
    function delete_content_object($object)
    {
        if (! $this->content_object_deletion_allowed($object))
        {
            return false;
        }
        // Delete children

        // Delete all attachments (only the links, not the actual objects)
        $conditions = array();
        $conditions[] = new EqualityCondition('content_object_id', $object->get_id());
        $conditions[] = new EqualityCondition('attachment_id', $object->get_id());
        $condition = new OrCondition($conditions);
        $this->database->delete_objects('content_object_attachment', $condition);

        // Delete all includes (only the links, not the actual objects)
        $conditions = array();
        $conditions[] = new EqualityCondition('content_object_id', $object->get_id());
        $conditions[] = new EqualityCondition('include_id', $object->get_id());
        $condition = new OrCondition($conditions);
        $this->database->delete_objects('content_object_include', $condition);

        // Delete object
        $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $object->get_id());
        $this->database->delete_objects(ContentObject :: get_table_name(), $condition);

        // Delete entry in version table
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        $this->database->delete_objects('content_object_version', $condition);

        if ($object->is_extended())
        {
            $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $object->get_id());
            $this->database->delete_objects('content_object_version', $condition);
        }

        return true;
    }

    // Inherited.
    function delete_content_object_version($object)
    {
        if (! $this->content_object_deletion_allowed($object, 'version'))
        {
            return false;
        }

        // Delete object
        $query = 'DELETE FROM ' . $this->database->escape_table_name(ContentObject :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . '=' . $this->quote($object->get_id());
        $this->query($query);

        if ($object->is_extended())
        {
            $query = 'DELETE FROM ' . $this->database->escape_table_name($object->get_type()) . ' WHERE ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . '=' . $this->quote($object->get_id());
            $this->query($query);
        }

        if ($object->is_latest_version())
        {
            $query = 'SELECT * FROM ' . $this->database->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . self :: ALIAS_CONTENT_OBJECT_TABLE . ' WHERE ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . '=' . $this->quote($object->get_object_number()) . ' ORDER BY ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . ' DESC';
            $this->database->set_limit(1);
            $res = $this->query($query);
            $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
            $res->free();

            $props = array();
            $props[$this->database->escape_column_name(ContentObject :: PROPERTY_ID)] = $record['id'];
            $this->database->get_connection()->loadModule('Extended');
            $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('content_object_version'), $props, MDB2_AUTOQUERY_UPDATE, $this->database->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . '=' . $object->get_object_number());
        }

        return true;
    }

    // Inherited.
    function delete_content_object_attachments($object)
    {
        // Delete all attachments (only the links, not the actual objects)
        $query = 'DELETE FROM ' . $this->database->escape_table_name('content_object_attachment') . ' WHERE ' . $this->database->escape_column_name('attachment_id') . '=' . $this->quote($object->get_id());
        return $this->query($query);
    }

    // Inherited.
    function delete_all_content_objects()
    {
        foreach ($this->get_registered_types() as $type)
        {
            if ($this->is_extended_type($type))
            {
                $this->database->delete_objects($this->database->get_table_name($type));
            }
        }

        $this->database->delete_objects($this->database->get_table_name(ContentObject :: get_table_name()));
    }

    function is_latest_version($object)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        $version = $this->database->retrieve_record('content_object_version', $condition);

        return ($version['id'] == $object->get_id() ? true : false);
    }

    function is_only_document_occurence($path)
    {
        $condition = new EqualityCondition(Document :: PROPERTY_PATH, $path);
        $count = $this->database->count_objects('document', $condition);

        return ($count == 1 ? true : false);
    }

    // Inherited.
    function get_next_content_object_display_order_index($parent, $type)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $parent);
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
        $condition = new AndCondition($conditions);

        return $this->database->retrieve_next_sort_value(ContentObject :: get_table_name(), ContentObject :: PROPERTY_DISPLAY_ORDER_INDEX, $condition);
    }

    // Inherited.
    function retrieve_attached_content_objects($object)
    {
        $subselect_condition = new EqualityCondition('content_object_id', $object->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, 'attachment_id', $this->database->escape_table_name('content_object_attachment'), $subselect_condition, $this->database->get_alias(ContentObject :: get_table_name()));
        return $this->retrieve_content_objects($condition)->as_array();
    }

    // Inherited.
    function retrieve_included_content_objects($object)
    {
        $subselect_condition = new EqualityCondition('content_object_id', $object->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, 'include_id', $this->database->escape_table_name('content_object_include'), $subselect_condition, $this->database->get_alias(ContentObject :: get_table_name()));
        return $this->retrieve_content_objects($condition)->as_array();
    }

    function is_content_object_included($object)
    {
        $condition = new EqualityCondition('include_id', $object->get_id());
        $count = $this->database->count_objects('content_object_include', $condition);
        return ($count > 0);
    }

    function retrieve_content_object_versions($object)
    {
        $object_number = $object->get_object_number();
        $query = 'SELECT ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . ' FROM ' . 
        		 $this->database->escape_table_name('content_object') . ' WHERE ' . 
        		 $this->database->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . '=' . $this->quote($object_number) . ' AND ' . 
        		 $this->database->escape_column_name(ContentObject :: PROPERTY_STATE) . '=' . $this->quote($object->get_state());
        $res = $this->query($query);
        $versions = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ORDERED))
        {
            $versions[] = $this->retrieve_content_object($record[0]);
        }
        $res->free();
        return $versions;
    }

    function get_latest_version_id($object)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        $record = $this->database->retrieve_record('content_object_version', $condition);
        return $record['id'];
    }

    // Inherited.
    function attach_content_object($object, $attachment_id)
    {
        $props = array();
        $props['content_object_id'] = $object->get_id();
        $props['attachment_id'] = $attachment_id;
        $this->database->get_connection()->loadModule('Extended');
        $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('content_object_attachment'), $props, MDB2_AUTOQUERY_INSERT);
    }

    // Inherited.
    function detach_content_object($object, $attachment_id)
    {
        $query = 'DELETE FROM ' . $this->database->escape_table_name('content_object_attachment') . ' WHERE ' . 
        		 $this->database->escape_column_name('content_object_id') . '=' . $this->quote($object->get_id()) . ' AND ' . 
        		 $this->database->escape_column_name('attachment_id') . '=' . $this->quote($attachment_id);
        $affectedRows = $this->query($query);
        return ($affectedRows > 0);
    }

    // Inherited.
    function include_content_object($object, $include_id)
    {
        $props = array();
        $props['content_object_id'] = $object->get_id();
        $props['include_id'] = $include_id;
        $this->database->get_connection()->loadModule('Extended');
        $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('content_object_include'), $props, MDB2_AUTOQUERY_INSERT);
    }

    // Inherited.
    function exclude_content_object($object, $include_id)
    {
        $query = 'DELETE FROM ' . $this->database->escape_table_name('content_object_include') . ' WHERE ' . 
        		 $this->database->escape_column_name('content_object_id') . '=' . $this->quote($object->get_id()) . ' AND ' . 
        		 $this->database->escape_column_name('include_id') . '=' . $this->quote($include_id);
        $affectedRows = $this->query($query);
        return ($affectedRows > 0);
    }

    // Inherited.
    function set_content_object_states($object_ids, $state)
    {
        if (! count($object_ids))
        {
            return true;
        }
        else
        {
            $condition = new InCondition(ContentObject :: PROPERTY_ID, $object_ids);
            $properties = array(ContentObject :: PROPERTY_STATE => $state);
            return $this->database->update_objects(ContentObject :: get_table_name(), $properties, $condition);
        }
    }

    // Inherited.
    function get_children_ids($object)
    {
        $children_ids = array();
        $parent_ids = array($object->get_id());
        do
        {
            $query = 'SELECT ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . ' FROM ' . $this->database->escape_table_name(ContentObject :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(ContentObject :: PROPERTY_PARENT_ID) . ' IN (?' . implode(',', $parent_ids) . ')';
            $res = $this->query($query);
            if ($res->numRows() == 0)
            {
                return $children_ids;
            }
            else
            {
                $parent_ids = array();
                while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
                {
                    $parent_ids[] = $record[ContentObject :: PROPERTY_ID];
                    $children_ids[] = $record[ContentObject :: PROPERTY_ID];
                }
            }
            $res->free();
        }
        while (true);
    }

    function get_version_ids($object)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        $order_by = array(new ObjectTableOrder(ContentObject :: PROPERTY_ID));

        $version_ids = array();
        $query = 'SELECT ' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . ' FROM ' . $this->database->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $this->database->get_alias(ContentObject :: get_table_name());
        $versions = $this->database->retrieve_record_set($query, ContentObject :: get_table_name(), $condition, null, null, $order_by);

        while ($version = $versions->next_result())
        {
            $version_ids[] = $version['id'];
        }

        return $version_ids;
    }

    /**
     * Handles PEAR errors. If an error is encountered, the program dies with
     * a descriptive error message.
     * @param DB_Error $error The error object.
     */
    static function handle_error($error)
    {
        print_r($error);
        // For debugging only. May create a security hazard.
        die(__FILE__ . ':' . __LINE__ . ': ' . $error->getMessage() . ' (' . $error->getDebugInfo() . ')');
    }

    /**
     * Converts a datetime value (as retrieved from the database) to a UNIX
     * timestamp (as returned by time()).
     * @param string $date The date as a UNIX timestamp.
     * @return int The date as a UNIX timestamp.
     */
    static function from_db_date($date)
    {
        if (isset($date))
        {
            return strtotime($date);
        }
        return null;
    }

    /**
     * Converts a UNIX timestamp (as returned by time()) to a datetime string
     * for use in SQL queries.
     * @param int $date The date as a UNIX timestamp.
     * @return string The date in datetime format.
     */
    static function to_db_date($date)
    {
        if (isset($date))
        {
            return date('Y-m-d H:i:s', $date);
        }
        return null;
    }

    /**
     * Parses a database record fetched as an associative array into a
     * learning object.
     * @param array $record The associative array.
     * @param boolean $additional_properties_known True if the additional
     *                                             properties of the
     *                                             learning object were
     *                                             fetched.
     * @return ContentObject The learning object.
     */
    function record_to_content_object($record, $additional_properties_known = false)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }
        $defaultProp = array();
        foreach (ContentObject :: get_default_property_names() as $prop)
        {
            $defaultProp[$prop] = $record[$prop];
        }
        $defaultProp[ContentObject :: PROPERTY_CREATION_DATE] = self :: from_db_date($defaultProp[ContentObject :: PROPERTY_CREATION_DATE]);
        $defaultProp[ContentObject :: PROPERTY_MODIFICATION_DATE] = self :: from_db_date($defaultProp[ContentObject :: PROPERTY_MODIFICATION_DATE]);

        $content_object = ContentObject :: factory($record[ContentObject :: PROPERTY_TYPE], $defaultProp);

        if ($additional_properties_known)
        {
            $properties = $content_object->get_additional_property_names();

            $additionalProp = array();
            if (count($properties) > 0)
            {
                foreach ($properties as $prop)
                {
                    $additionalProp[$prop] = $record[$prop];
                }
            }
        }
        else
        {
            $additionalProp = null;
        }

        $content_object->set_additional_properties($additionalProp);

        return $content_object;
    }

    /**
     * Translates a string with wildcard characters "?" (single character)
     * and "*" (any character sequence) to a SQL pattern for use in a LIKE
     * condition. Should be suitable for any SQL flavor.
     * @param string $string The string that contains wildcard characters.
     * @return string The escaped string.
     */
    static function translate_search_string($string)
    {
        /*
		======================================================================
		 * A brief explanation of these regexps:
		 * - The first one escapes SQL wildcard characters, thus prefixing
		 *   %, ', \ and _ with a backslash.
		 * - The second one replaces asterisks that are not prefixed with a
		 *   backslash (which escapes them) with the SQL equivalent, namely a
		 *   percent sign.
		 * - The third one is similar to the second: it replaces question
		 *   marks that are not escaped with the SQL equivalent _.
		======================================================================
		 */
        return preg_replace(array('/([%\'\\\\_])/e', '/(?<!\\\\)\*/', '/(?<!\\\\)\?/'), array("'\\\\\\\\' . '\\1'", '%', '_'), $string);
    }

    /**
     * Checks whether the given column name is the name of a column that
     * contains a date value, and hence should be formatted as such.
     * @param string $name The column name.
     * @return boolean True if the column is a date column, false otherwise.
     */
    static function is_date_column($name)
    {
        return ($name == ContentObject :: PROPERTY_CREATION_DATE || $name == ContentObject :: PROPERTY_MODIFICATION_DATE);
    }

    // Inherited.
    function get_used_disk_space($owner)
    {
        $condition_owner = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $owner);
        $types = $this->get_registered_types();
        foreach ($types as $index => $type)
        {
            $class = ContentObject :: type_to_class($type);
            $properties = call_user_func(array($class, 'get_disk_space_properties'));
            if (is_null($properties))
            {
                continue;
            }
            if (! is_array($properties))
            {
                $properties = array($properties);
            }
            $sum = array();
            foreach ($properties as $index => $property)
            {
                $sum[] = 'SUM(' . $this->database->escape_column_name($property) . ')';
            }
            if ($this->is_extended_type($type))
            {
                $query = 'SELECT ' . implode('+', $sum) . ' AS disk_space FROM ' . $this->database->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . self :: ALIAS_CONTENT_OBJECT_TABLE . ' JOIN ' . $this->database->escape_table_name($type) . ' AS ' . self :: ALIAS_TYPE_TABLE . ' ON ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . ' = ' . self :: ALIAS_TYPE_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID);
                $condition = $condition_owner;
            }
            else
            {
                $query = 'SELECT ' . implode('+', $sum) . ' AS disk_space FROM ' . $this->database->escape_table_name(ContentObject :: get_table_name());
                $match = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
                $condition = new AndCondition(array($match, $condition_owner));
            }

            if (isset($condition))
            {
                $translator = new ConditionTranslator($this->database, $this->database->get_alias(ContentObject :: get_table_name()));
                $query .= $translator->render_query($condition);
            }

            $res = $this->query($query);
            $record = $res->fetchRow(MDB2_FETCHMODE_OBJECT);
            $disk_space += $record->disk_space;
            $res->free();
        }
        return $disk_space;
    }

    // Inherited
    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    private static function is_content_object_column($name)
    {
        return ContentObject :: is_default_property_name($name) || $name == ContentObject :: PROPERTY_TYPE || $name == ContentObject :: PROPERTY_DISPLAY_ORDER_INDEX || $name == ContentObject :: PROPERTY_ID;
    }

    function ExecuteQuery($sql)
    {
        $this->database->get_connection()->query($sql);
    }

    function is_attached($object, $type = null)
    {
        if (isset($type))
        {
            $condition = new EqualityCondition('attachment_id', $object->get_id());
        }
        else
        {
            $condition = new InCondition('attachment_id', $this->get_version_ids($object));
        }
        $query = 'SELECT COUNT(' . $this->database->escape_column_name('content_object_id', $this->database->get_alias('content_object_attachment')) . ') FROM ' . $this->database->escape_table_name('content_object_attachment') . ' AS ' . $this->database->get_alias('content_object_attachment');
        $count = $this->database->count_result_set($query, 'content_object_attachment', $condition);

        return $count > 0;
        //        $query = 'SELECT COUNT(' . $this->database->escape_column_name("content_object_id") . ') FROM ' . $this->database->escape_table_name('content_object_attachment') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_ATTACHMENT_TABLE . ' WHERE ' . self :: ALIAS_CONTENT_OBJECT_ATTACHMENT_TABLE . '.attachment_id';
    //        if (isset($type))
    //        {
    //            $query .= '=?';
    //            $params = $object->get_id();
    //        }
    //        else
    //        {
    //            $query .= ' IN (?' . str_repeat(',?', count($this->get_version_ids($object)) - 1) . ')';
    //            $params = $this->get_version_ids($object);
    //        }
    //        $sth = $this->database->get_connection()->prepare($query);
    //        $res = $sth->execute($params);
    //        $sth->free();
    //        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
    //        $res->free();
    //        if ($record[0] > 0)
    //        {
    //            return true;
    //        }
    //        else
    //        {
    //            return false;
    //        }
    }

    /**
     * Returns the next available complex learning object ID.
     * @return int The ID.
     */
    function get_next_complex_content_object_item_id()
    {
        return $this->database->get_next_id(ComplexContentObjectItem :: get_table_name());
    }

    /**
     * Creates a new complex learning object in the database
     * @param ComplexContentObject $clo - The complex learning object
     * @return True if success
     */
    function create_complex_content_object_item($clo_item)
    {
        $props = array();
        foreach ($clo_item->get_default_properties() as $key => $value)
        {
            $props[$this->database->escape_column_name($key)] = $value;
        }
        $this->database->get_connection()->loadModule('Extended');
        $this->database->get_connection()->extended->autoExecute($this->database->get_table_name(ComplexContentObjectItem :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT);

        if ($clo_item->is_extended())
        {
            $ref = $clo_item->get_ref();

            $props = array();
            foreach ($clo_item->get_additional_properties() as $key => $value)
            {
                $props[$this->database->escape_column_name($key)] = $value;
            }
            $props[$this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID)] = $clo_item->get_id();
            $type = $this->determine_content_object_type($ref);
            $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('complex_' . $type), $props, MDB2_AUTOQUERY_INSERT);
        }

        return true;
    }

    /**
     * Updates a complex learning object in the database
     * @param ComplexContentObject $clo - The complex learning object
     * @return True if success
     */
    function update_complex_content_object_item($clo_item)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $clo_item->get_id(), ComplexContentObjectItem :: get_table_name());

        $props = array();
        foreach ($clo_item->get_default_properties() as $key => $value)
        {
            if ($key == ComplexContentObjectItem :: PROPERTY_ID)
                continue;
            $props[$this->database->escape_column_name($key)] = $value;
        }
        $this->database->get_connection()->loadModule('Extended');
        $this->database->get_connection()->extended->autoExecute($this->database->get_table_name(ComplexContentObjectItem :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $condition);
        if ($clo_item->is_extended())
        {
            $ref = $clo_item->get_ref();

            $props = array();
            foreach ($clo_item->get_additional_properties() as $key => $value)
            {
                $props[$this->database->escape_column_name($key)] = $value;
            }
            $type = $this->determine_content_object_type($ref);
            $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('complex_' . $type), $props, MDB2_AUTOQUERY_UPDATE, $condition);
        }
        return true;
    }

    /**
     * Deletes a complex learning object in the database
     * @param ComplexContentObject $clo - The complex learning object
     * @return True if success
     */
    function delete_complex_content_object_item($clo_item)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $clo_item->get_id());

        $query = 'DELETE FROM ' . $this->database->escape_table_name(ComplexContentObjectItem :: get_table_name());

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        //$this->database->set_limit(1);
        $this->query($query);

        if ($clo_item->is_extended())
        {
            $ref = $clo_item->get_ref();

            $type = $this->determine_content_object_type($ref);
            $query = 'DELETE FROM ' . $this->database->get_table_name('complex_' . $type);

            if (isset($condition))
            {
                $translator = new ConditionTranslator($this->database);
                $query .= $translator->render_query($condition);
            }

            //$this->database->set_limit(1);
            $this->query($query);
        }

        $conditions = array();
        $conditions[] = new InequalityCondition(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $clo_item->get_display_order());
        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $clo_item->get_parent());
        $condition = new AndCondition($conditions);
        $properties[ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER] = $this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER) . '-1';

        $this->database->update_objects(ComplexContentObjectItem :: get_table_name(), $properties, $condition);

        return true;

    }

    function delete_complex_content_object_items($condition)
    {
        return $this->database->delete(ComplexContentObjectItem :: get_table_name(), $condition);
    }

    /**
     * Retrieves a complex learning object from the database with a given id
     * @param Int $clo_id
     * @return The complex learning object
     */
    function retrieve_complex_content_object_item($clo_item_id)
    {
        // Retrieve main table
        $query = 'SELECT * FROM ' . $this->database->escape_table_name(ComplexContentObjectItem :: get_table_name()) . ' AS ' . self :: ALIAS_COMPLEX_CONTENT_OBJECT_ITEM_TABLE;

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $clo_item_id, ComplexContentObjectItem :: get_table_name());

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        $this->database->set_limit(1);
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        if (! $record)
            return null;

        // Determine type


        $ref = $record[ComplexContentObjectItem :: PROPERTY_REF];

        $type = $this->determine_content_object_type($ref);
        $cloi = ComplexContentObjectItem :: factory($type, array(), array());

        $bool = false;

        if ($cloi->is_extended())
            $bool = true;

        return self :: record_to_complex_content_object_item($record, $type, $bool);
    }

    /**
     * Mapper for a record to a complex learning object item
     * @param Record $record
     * @return ComplexContentObjectItem
     */
    function record_to_complex_content_object_item($record, $type = null, $additional_properties_known = false)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }

        $cloi = ComplexContentObjectItem :: factory($type, array(), array());

        $defaultProp = array();
        foreach ($cloi->get_default_property_names() as $prop)
        {
            $defaultProp[$prop] = $record[$prop];
        }
        $cloi->set_default_properties($defaultProp);

        if ($additional_properties_known && $type && $cloi->is_extended())
        {
            $additionalProp = array();

            $query = 'SELECT * FROM ' . $this->database->escape_table_name('complex_' . $type) . ' AS ' . self :: ALIAS_TYPE_TABLE;

            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $record['id']);

            if (isset($condition))
            {
                $translator = new ConditionTranslator($this->database);
                $query .= $translator->render_query($condition);
            }

            $this->database->set_limit(1);
            $res = $this->query($query);
            $rec2 = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
            $res->free();

            foreach ($cloi->get_additional_property_names() as $prop)
            {
                $additionalProp[$prop] = $rec2[$prop];
            }

            $cloi->set_additional_properties($additionalProp);
        }
        else
        {
            $additionalProp = null;
        }

        return $cloi;
    }

    /**
     * Counts the available complex learning objects with the given condition
     * @param Condition $condition
     * @return Int the amount of complex learning objects
     */
    function count_complex_content_object_items($condition)
    {
        return $this->database->count_objects(ComplexContentObjectItem :: get_table_name(), $condition);
    }

    /**
     * Retrieves the complex learning object items with the given condition
     * @param Condition
     */
    function retrieve_complex_content_object_items($condition = null, $order_by = array (), $offset = 0, $max_objects = -1, $type = null)
    {
        $alias = self :: ALIAS_COMPLEX_CONTENT_OBJECT_ITEM_TABLE;

        $query = 'SELECT ' . $alias . '.* FROM ' . $this->database->escape_table_name(ComplexContentObjectItem :: get_table_name()) . ' AS ' . $alias;

        if (isset($type))
        {
            switch ($type)
            {
                case 'complex_wiki_page' :
                    $query .= ' JOIN ' . $this->database->escape_table_name($type) . ' AS ' . self :: ALIAS_TYPE_TABLE . ' ON ' . self :: ALIAS_COMPLEX_CONTENT_OBJECT_ITEM_TABLE . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID) . ' = ' . self :: ALIAS_TYPE_TABLE . '.' . $this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID);
            }
        }
        $lo_alias = $this->get_database()->get_alias(ContentObject :: get_table_name());

        $query .= ' JOIN ' . $this->database->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $lo_alias . ' ON ' . $alias . '.ref_id=' . $lo_alias . '.id';

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        $order_by[] = new ObjectTableOrder(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, SORT_ASC, $alias);

        $orders = array();
        foreach ($order_by as $order)
        {
            $alias = $order->get_alias() ? $order->get_alias() . '.' : '';
            $orders[] = $this->database->escape_column_name($alias . $order->get_property()) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }

        if ($max_objects < 0)
        {
            $max_objects = null;
        }
        $this->database->set_limit(intval($max_objects), intval($offset));
        $res = $this->query($query);

        return new DatabaseComplexContentObjectItemResultSet($this, $res, true);
        //return $this->database->retrieve_objects('complex_content_object_item', $condition, $offset, $max_objects, $order_by, 'DatabaseComplexContentObjectItemResultSet');
    }

    function select_next_display_order($parent_id)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent_id);

        return $this->database->retrieve_next_sort_value(ComplexContentObjectItem :: get_table_name(), ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function get_next_category_id()
    {
        return $this->database->get_next_id(RepositoryCategory :: get_table_name());
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $category->get_id());
        $succes = $this->database->delete(RepositoryCategory :: get_table_name(), $condition);

        // Correct the diplsay order of the remaining categories
        $conditions = array();
        $conditions[] = new InequalityCondition(RepositoryCategory :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $category->get_display_order());
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $category->get_parent());
        $condition = new AndCondition($conditions);
        $properties = array(RepositoryCategory :: PROPERTY_DISPLAY_ORDER => ($this->database->escape_column_name(RepositoryCategory :: PROPERTY_DISPLAY_ORDER) . '-1'));
        $this->database->update_objects(RepositoryCategory :: get_table_name(), $properties, $condition);

        // Move the objecs in the category to the garbage bin
        $condition = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category->get_id());
        $properties = array(ContentObject :: PROPERTY_STATE => '1');
        $this->database->update_objects(ContentObject :: get_table_name(), $properties, $condition);

        // Delete all subcategories by recursively repeating the entire process
        $categories = $this->retrieve_categories(new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $category->get_id()));
        while ($category = $categories->next_result())
        {
            $this->delete_category($category);
        }

        return $succes;
    }

    function update_category($category)
    {
        $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $category->get_id());
        return $this->database->update($category, $condition);
    }

    function create_category($category)
    {
        return $this->database->create($category);
    }

    function count_categories($conditions = null)
    {
        return $this->database->count_objects(RepositoryCategory :: get_table_name(), $conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        if (is_a($order_property, 'ObjectTableOrder'))
        {
            $order_property = array($order_property);
        }

        $order_property[] = new ObjectTableOrder(RepositoryCategory :: PROPERTY_PARENT);
        $order_property[] = new ObjectTableOrder(RepositoryCategory :: PROPERTY_DISPLAY_ORDER);
        return $this->database->retrieve_objects(RepositoryCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function select_next_category_display_order($parent_category_id, $user_id)
    {
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $parent_category_id);
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $user_id);
        $condition = new AndCondition($conditions);

        return $this->database->retrieve_next_sort_value(RepositoryCategory :: get_table_name(), RepositoryCategory :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function get_next_user_view_id()
    {
        return $this->database->get_next_id(UserView :: get_table_name());
    }

    function delete_user_view($user_view)
    {
        $condition = new EqualityCondition(UserView :: PROPERTY_ID, $user_view->get_id());
        $success = $this->database->delete(UserView :: get_table_name(), $condition);

        $condition = new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $user_view->get_id());
        $success &= $this->database->delete(UserViewRelContentObject :: get_table_name(), $condition);

        return $success;
    }

    function update_user_view($user_view)
    {
        $condition = new EqualityCondition(UserView :: PROPERTY_ID, $user_view->get_id());
        return $this->database->update($user_view, $condition);
    }

    function create_user_view($user_view)
    {
        return $this->database->create($user_view);
    }

    function count_user_views($conditions = null)
    {
        return $this->database->count_objects(UserView :: get_table_name(), $conditions);
    }

    function retrieve_user_views($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(UserView :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function update_user_view_rel_content_object($user_view_rel_content_object)
    {
        $conditions[] = new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $user_view_rel_content_object->get_view_id());
        $conditions[] = new EqualityCondition(UserViewRelContentObject :: PROPERTY_CONTENT_OBJECT_TYPE, $user_view_rel_content_object->get_content_object_type());

        $condition = new AndCondition($conditions);

        return $this->database->update($user_view_rel_content_object, $condition);
    }

    function update_content_object_pub_feedback($content_object_pub_feedback)
    {
        $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_PUBLICATION_ID, $content_object_pub_feedback->get_publication_id());
        $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_CLOI_ID, $content_object_pub_feedback->get_cloi_id());
        $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $content_object_pub_feedback->get_feedback_id());
        $condition = new AndCondition($conditions);

        return $this->database->update($content_object_pub_feedback, $condition);
    }

    function create_user_view_rel_content_object($user_view_rel_content_object)
    {
        return $this->database->create($user_view_rel_content_object);
    }

    function create_content_object_pub_feedback($content_object_pub_feedback)
    {
        return $this->database->create($content_object_pub_feedback);
    }

    function retrieve_user_view_rel_content_objects($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(UserViewRelContentObject :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function retrieve_content_object_pub_feedback($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(ContentObjectPubFeedback :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function delete_content_object_pub_feedback($content_object_pub_feedback)
    {
        $condition = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $content_object_pub_feedback->get_feedback_id());

        $success = $this->database->delete(ContentObjectPubFeedback :: get_table_name(), $condition);

        return $success;
    }

    function reset_user_view($user_view)
    {
        $condition = new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $user_view->get_id());
        $properties[UserViewRelContentObject :: PROPERTY_VISIBILITY] = '0';

        return $this->database->update_objects(ContentObjectPublication :: get_table_name(), $properties, $condition);
    }

    function retrieve_last_post($forum_id)
    {
        $complex_item_alias = $this->database->get_alias(ComplexContentObjectItem :: get_table_name());
        $complex_item_alias_bis = $this->database->get_alias(ComplexContentObjectItem :: get_table_name() . '_bis');
        $forum_alias = $this->database->get_alias('forum');
        $forum_topic_alias = $this->database->get_alias('forum_topic');

        $query = 'SELECT ' . $complex_item_alias . '.*';
        $query .= ' FROM ' . $this->database->escape_table_name(ComplexContentObjectItem :: get_table_name()) . ' AS ' . $complex_item_alias;
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name('forum') . ' AS ' . $forum_alias . ' ON ' . $this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_REF, $complex_item_alias) . ' = ' . $this->database->escape_column_name(Forum :: PROPERTY_ID, $forum_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name('forum_topic') . ' AS ' . $forum_topic_alias . ' ON ' . $this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_REF, $complex_item_alias) . ' = ' . $this->database->escape_column_name(ForumTopic :: PROPERTY_ID, $forum_topic_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(ComplexContentObjectItem :: get_table_name()) . ' AS ' . $complex_item_alias_bis . ' ON ' . $this->database->escape_column_name(Forum :: PROPERTY_LAST_POST, $forum_alias) . ' = ' . $this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID, $complex_item_alias_bis);
        $query .= ' OR ' . $this->database->escape_column_name(ForumTopic :: PROPERTY_LAST_POST, $forum_topic_alias) . ' = ' . $this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID, $complex_item_alias_bis);
        $query .= ' WHERE ' . $this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_PARENT, $complex_item_alias) . '=' . $this->quote($forum_id);
        $query .= ' ORDER BY ' . $this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_ADD_DATE, $complex_item_alias_bis) . 'DESC';

        $this->database->set_limit(1);
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        $res->free();

        $object_reference = $record[ComplexContentObjectItem :: PROPERTY_REF];
        $object_type = $this->determine_content_object_type($object_reference);

        if($record)
        	return $this->record_to_complex_content_object_item($record, $object_type, true);
    }

    function create_content_object_metadata($content_object_metadata)
    {
        $created = $content_object_metadata->get_creation_date();
        if (is_numeric($created))
        {
            $content_object_metadata->set_creation_date(self :: to_db_date($content_object_metadata->get_creation_date()));
        }

        return $this->database->create($content_object_metadata);
    }

    function update_content_object_metadata($content_object_metadata)
    {
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_ID, $content_object_metadata->get_id());

        $date = $content_object_metadata->get_modification_date();
        if (is_numeric($date))
        {
            $content_object_metadata->set_modification_date(self :: to_db_date($content_object_metadata->get_modification_date()));
        }

        return $this->database->update($content_object_metadata, $condition);
    }

    function delete_content_object_metadata($content_object_metadata)
    {
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_ID, $content_object_metadata->get_id());
        return $this->database->delete($content_object_metadata->get_table_name(), $condition);
    }

    function retrieve_content_object_metadata($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(ContentObjectMetadata :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }
    
    function retrieve_content_object_by_catalog_entry_values($catalog_name, $entry_value)
    {
        if(StringUtilities::has_value($catalog_name) && StringUtilities::has_value($entry_value))
        {
            $query = 'SELECT count(*) as total, content_object_id FROM repository_content_object_metadata
                WHERE 
                (property LIKE \'general_identifier[%][catalog]\' AND value = \'' . $catalog_name . '\')
                OR
                (property LIKE \'general_identifier[%][entry]\' AND value = \'' . $entry_value . '\')
                GROUP BY content_object_id
                HAVING total=2';
            
            return $this->database->retrieve_object_set($query, 'repository_content_object_metadata', null, null, null, null, 'ContentObjectMetadata');
        }
    }

    function get_next_content_object_metadata_id()
    {
        return $this->database->get_connection()->nextID($this->database->get_table_name(ContentObjectMetadata :: get_table_name()));
    }

    function get_next_content_object_metadata_catalog_id()
    {
        return $this->database->get_connection()->nextID($this->database->get_table_name(ContentObjectMetadataCatalog :: get_table_name()));
    }

    function create_content_object_metadata_catalog($content_object_metadata_catalog)
    {
        $created = $content_object_metadata_catalog->get_creation_date();
        if (is_numeric($created))
        {
            $content_object_metadata_catalog->set_creation_date(self :: to_db_date($content_object_metadata_catalog->get_creation_date()));
        }

        return $this->database->create($content_object_metadata_catalog);
    }

    function update_content_object_metadata_catalog($content_object_metadata_catalog)
    {
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_ID, $content_object_metadata_catalog->get_id());

        $date = $content_object_metadata_catalog->get_modification_date();
        if (is_numeric($date))
        {
            $content_object_metadata_catalog->set_modification_date(self :: to_db_date($content_object_metadata_catalog->get_modification_date()));
        }

        return $this->database->update($content_object_metadata_catalog, $condition);
    }

    function delete_content_object_metadata_catalog($content_object_metadata_catalog)
    {
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_ID, $content_object_metadata_catalog->get_id());
        return $this->database->delete($content_object_metadata_catalog->get_table_name(), $condition);
    }

    function set_new_clo_version($lo_id, $new_lo_id)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $lo_id, ComplexContentObjectItem :: get_table_name());
        $props = array();
        $props[$this->database->escape_column_name(ComplexContentObjectItem :: PROPERTY_PARENT)] = $new_lo_id;
        $this->database->get_connection()->loadModule('Extended');
        return $this->database->get_connection()->extended->autoExecute($this->database->get_table_name(ComplexContentObjectItem :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $condition);
    }

    function retrieve_external_export($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(ExternalExport :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_external_export_fedora($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(ExternalExportFedora :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_catalog($query, $table_name, $condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        /*
	     * Get 'catalog' alias and add it to the query in order to support WHERE and ORDER BY clause
	     */
        $after_from_position = stripos($query, 'from') + 4;
        $sub_query = trim(substr($query, $after_from_position));

        if (stripos($sub_query, ' ') !== false)
        {
            $real_table_name = trim(substr($sub_query, 0, stripos($query, ' ')));
        }
        else
        {
            $real_table_name = $sub_query;
        }

        $after_table_position = stripos($query, $real_table_name) + strlen($real_table_name);
        $alias = $this->database->get_alias('Catalog');
        $query = substr($query, 0, $after_table_position) . ' AS ' . $alias . ' ' . substr($query, $after_table_position);

        if (isset($condition))
        {
            $condition->set_storage_unit($alias);
        }

        if (isset($order_by))
        {
            $order_by->set_alias($alias);
        }

        return $this->database->retrieve_object_set($query, $table_name, $condition, $offset, $max_objects, $order_by);
    }
}
?>
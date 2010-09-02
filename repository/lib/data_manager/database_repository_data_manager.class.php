<?php
/**
 * $Id: database_repository_data_manager.class.php 234 2009-11-16 11:34:07Z vanpouckesven $
 * @package repository.lib.data_manager
 */
require_once dirname(__FILE__) . '/database/database_content_object_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_complex_content_object_item_result_set.class.php';
require_once dirname(__FILE__) . '/../category_manager/repository_category.class.php';
require_once dirname(__FILE__) . '/../repository_data_manager_interface.class.php';

require_once 'MDB2.php';

/**
==============================================================================
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Tim De Pauw
 * @author Bart Mollet
 * @author Hans De Bisschop
 * @author Dieter De Neef
==============================================================================
 */

class DatabaseRepositoryDataManager extends Database implements RepositoryDataManagerInterface
{
    const ALIAS_CONTENT_OBJECT_VERSION_TABLE = 'lov';
    const ALIAS_CONTENT_OBJECT_ATTACHMENT_TABLE = 'loa';
    const ALIAS_TYPE_TABLE = 'tt';

    // Inherited.
    function initialize()
    {
        parent :: initialize();
        //        PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array(get_class(), 'handle_error'));
        $this->set_prefix('repository_');
    }

    // Inherited.
    function determine_content_object_type($id)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $id);
        $record = $this->retrieve_record(ContentObject :: get_table_name(), $condition);
        return $record[ContentObject :: PROPERTY_TYPE];
    }

    // Inherited.
    function retrieve_content_object($id, $type = null)
    {
        
        if (! isset($id) || strlen($id) == 0 || $id == DataClass :: NO_UID)
        {
            return null;
        }
        
        if (is_null($type))
        {
            $type = $this->determine_content_object_type($id);
        }
        
        $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $id);
        
        if (RepositoryDataManager :: is_extended_type($type))
        {
            $content_object_alias = $this->get_alias(ContentObject :: get_table_name());
            
            $query = 'SELECT * FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias;
            $query .= ' JOIN ' . $this->escape_table_name($type) . ' AS ' . self :: ALIAS_TYPE_TABLE . ' ON ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, self :: ALIAS_TYPE_TABLE);
            
            $record = $this->retrieve_row($query, ContentObject :: get_table_name(), $condition);
        }
        else
        {
            $record = $this->retrieve_record(ContentObject :: get_table_name(), $condition);
        }
        
        return self :: record_to_content_object($record, isset($type));
    }
    
    function retrieve_content_object_by_condition($condition, $type)
    {
        if (RepositoryDataManager :: is_extended_type($type))
        {
            $content_object_alias = $this->get_alias(ContentObject :: get_table_name());
            $type_alias = $this->get_alias($type);
            
            $query = 'SELECT * FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias;
            $query .= ' JOIN ' . $this->escape_table_name($type) . ' AS ' . $type_alias . ' ON ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $type_alias);
          
            $record = $this->retrieve_row($query, ContentObject :: get_table_name(), $condition);
        }
        else
        {
            $record = $this->retrieve_record(ContentObject :: get_table_name(), $condition);
        }
        
        if($record)
        	return self :: record_to_content_object($record, true);
    }
    

    // Inherited.
    // TODO: Extract methods.
    function retrieve_content_objects($condition = null, $order_by = array (), $offset = 0, $max_objects = -1, $query = null)
    {
        if (is_null($query))
        {
            $query = 'SELECT * FROM ';
            $query .= $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $this->get_alias(ContentObject :: get_table_name());
            $query .= ' JOIN ' . $this->escape_table_name('content_object_version') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . ' ON ' . $this->get_alias(ContentObject :: get_table_name()) . '.' . ContentObject :: PROPERTY_ID . ' = ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . '.' . ContentObject :: PROPERTY_ID;
        }
        
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }
        
        $orders = array();
        foreach ($order_by as $order)
        {
            $orders[] = $this->escape_column_name($order->get_property()) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        
        if ($max_objects < 0)
        {
            $max_objects = null;
        }
        
        //        dump($query);
        //        dump($params);
        

        $this->set_limit(intval($max_objects), intval($offset));
        $res = $this->query($query);
        return new DatabaseContentObjectResultSet($this, $res, false);
    }

    function retrieve_type_content_objects($type, $condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        
        $content_object_alias = $this->get_alias(ContentObject :: get_table_name());
        $content_object_version_alias = $this->get_alias('content_object_version');
        
        $query = 'SELECT * FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias;
        
        if (ContentObject :: is_extended_type($type))
        {
            $type_alias = $this->get_alias($type);
            $query .= ' JOIN ' . $this->escape_table_name($type) . ' AS ' . $type_alias . ' ON ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . ' = ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $type_alias);
        }
        else
        {
            $type_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
            $condition = isset($condition) ? new AndCondition($type_condition, $condition) : $type_condition;
        }
        
        $query .= ' JOIN ' . $this->escape_table_name('content_object_version') . ' AS ' . $content_object_version_alias . ' ON ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . ' = ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_version_alias);
        
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }
        
        $orders = array();
        foreach ($order_by as $order)
        {
            $orders[] = $this->escape_column_name($order->get_property()) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        
        if ($max_objects < 0)
        {
            $max_objects = null;
        }
        
        $this->set_limit(intval($max_objects), intval($offset));
        $res = $this->query($query);
        return new DatabaseContentObjectResultSet($this, $res, true);
    }

    // Inherited.
    function retrieve_additional_content_object_properties($content_object)
    {
        $type = $content_object->get_type();
        if (! RepositoryDataManager :: is_extended_type($type))
        {
            return array();
        }
        $array = array_map(array($this, 'escape_column_name'), $content_object->get_additional_property_names());
        
        if (count($array) == 0)
        {
            $array = array("*");
        }
        
        $query = 'SELECT ' . implode(',', $array) . ' FROM ' . $this->escape_table_name($type) . ' WHERE ' . $this->escape_column_name(ContentObject :: PROPERTY_ID) . '=' . $this->quote($content_object->get_id());
        
        $this->set_limit(1);
        $res = $this->query($query);
        $return = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $res->free();
        
        return $return;
    }

    // Inherited.
    // TODO: Extract methods; share stuff with retrieve_content_objects.
    function count_content_objects($condition = null, $query = null)
    {
        if (is_null($query))
        {
            $query = 'SELECT COUNT(' . $this->get_alias(ContentObject :: get_table_name()) . '.' . $this->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . ') FROM ' . $this->escape_table_name('content_object') . ' AS ' . $this->get_alias(ContentObject :: get_table_name());
            
            $query .= ' JOIN ' . $this->escape_table_name('content_object_version') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . ' ON ' . $this->get_alias(ContentObject :: get_table_name()) . '.' . ContentObject :: PROPERTY_ID . ' = ' . self :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . '.' . ContentObject :: PROPERTY_ID;
        }
        
        return $this->count_result_set($query, ContentObject :: get_table_name(), $condition);
    }

    function count_type_content_objects($type, $condition = null)
    {
        $content_object_alias = $this->get_alias(ContentObject :: get_table_name());
        $content_object_version_alias = $this->get_alias('content_object_version');
        $type_alias = $this->get_alias($type);
        
        if (RepositoryDataManager :: is_extended_type($type))
        {
            $query = 'SELECT COUNT(' . $this->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER, $content_object_alias) . ') FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias . ' JOIN ' . $this->escape_table_name($type) . ' AS ' . $type_alias . ' ON ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . ' = ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $type_alias);
        }
        else
        {
            $query = 'SELECT COUNT(' . $this->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER, $content_object_alias) . ') FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias;
            $match = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
            $condition = isset($condition) ? new AndCondition(array($match, $condition)) : $match;
        }
        
        $query .= ' JOIN ' . $this->escape_table_name('content_object_version') . ' AS ' . $content_object_version_alias . ' ON ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . ' = ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_version_alias);
        
        return $this->count_result_set($query, ContentObject :: get_table_name(), $condition);
    }

    // Inherited
    function count_content_object_versions($object)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        return $this->count_objects(ContentObject :: get_table_name(), $condition);
    }

    function get_next_content_object_number()
    {
        return $this->get_next_id(ContentObject :: get_table_name() . '_number');
    }

    // Inherited.
    function create_content_object($object, $type)
    {
        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }
        $props[$this->escape_column_name(ContentObject :: PROPERTY_ID)] = $object->get_id();
        $props[$this->escape_column_name(ContentObject :: PROPERTY_TYPE)] = $object->get_type();
        $props[$this->escape_column_name(ContentObject :: PROPERTY_CREATION_DATE)] = $object->get_creation_date();
        $props[$this->escape_column_name(ContentObject :: PROPERTY_MODIFICATION_DATE)] = $object->get_modification_date();
        $props[$this->escape_column_name(ContentObject :: PROPERTY_ID)] = $this->get_better_next_id('content_object', 'id');
        $this->get_connection()->loadModule('Extended');
        $this->get_connection()->extended->autoExecute($this->get_table_name('content_object'), $props, MDB2_AUTOQUERY_INSERT);
        $object->set_id($this->get_connection()->extended->getAfterID($props[$this->escape_column_name(ContentObject :: PROPERTY_ID)], 'content_object'));
        if ($object->is_extended())
        {
            $props = array();
            foreach ($object->get_additional_properties() as $key => $value)
            {
                $props[$this->escape_column_name($key)] = $value;
            }
            $props[$this->escape_column_name(ContentObject :: PROPERTY_ID)] = $object->get_id();
            $this->get_connection()->extended->autoExecute($this->get_table_name($object->get_type()), $props, MDB2_AUTOQUERY_INSERT);
        }
        
        $props = array();
        $props[$this->escape_column_name(ContentObject :: PROPERTY_ID)] = $object->get_id();
        if ($type == 'new')
        {
            $props[$this->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER)] = $object->get_object_number();
            $this->get_connection()->extended->autoExecute($this->get_table_name('content_object_version'), $props, MDB2_AUTOQUERY_INSERT);
        }
        elseif ($type == 'version')
        {
            $this->get_connection()->extended->autoExecute($this->get_table_name('content_object_version'), $props, MDB2_AUTOQUERY_UPDATE, $this->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . '=' . $object->get_object_number());
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
        $where = $this->escape_column_name(ContentObject :: PROPERTY_ID) . '=' . $object->get_id();
        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }
        $props[$this->escape_column_name(ContentObject :: PROPERTY_CREATION_DATE)] = $object->get_creation_date();
        $props[$this->escape_column_name(ContentObject :: PROPERTY_MODIFICATION_DATE)] = $object->get_modification_date();
        $this->get_connection()->loadModule('Extended');
        $this->get_connection()->extended->autoExecute($this->get_table_name('content_object'), $props, MDB2_AUTOQUERY_UPDATE, $where);
        if ($object->is_extended())
        {
            $props = array();
            foreach ($object->get_additional_properties() as $key => $value)
            {
                $props[$this->escape_column_name($key)] = $value;
            }
            $this->get_connection()->extended->autoExecute($this->get_table_name($object->get_type()), $props, MDB2_AUTOQUERY_UPDATE, $where);
        }
        return true;
    }

    //Inherited.
    function retrieve_content_object_by_user($user_id)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id);
        return $this->retrieve_objects(ContentObject :: get_table_name(), $condition);
    }

    function delete_content_object_by_id($object_id)
    {
        $object = $this->retrieve_content_object($object_id);
        return $this->delete_content_object($object);
    }

    // Inherited.
    function delete_content_object($object)
    {
        if (! RepositoryDataManager :: content_object_deletion_allowed($object))
        {
            return false;
        }
        // Delete children
        

        // Delete all types of attachments (only the links, not the actual objects)
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectAttachment :: PROPERTY_CONTENT_OBJECT_ID, $object->get_id());
        //$conditions[] = new EqualityCondition(ContentObjectAttachment :: PROPERTY_ATTACHMENT_ID, $object->get_id());
        $condition = new OrCondition($conditions);
        $this->delete_content_object_attachments($condition);
        
        // Delete all includes (only the links, not the actual objects)
        $conditions = array();
        $conditions[] = new EqualityCondition('content_object_id', $object->get_id());
        $conditions[] = new EqualityCondition('include_id', $object->get_id());
        $condition = new OrCondition($conditions);
        $this->delete_objects('content_object_include', $condition);
        
        //Delete extended properties record
        if (RepositoryDataManager :: is_extended_type(Utilities :: camelcase_to_underscores(get_class($object))))
        {
            $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $object->get_id());
            $this->delete_objects(Utilities :: camelcase_to_underscores(get_class($object)), $condition);
        }
        
        //Delete associated metadata
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_CONTENT_OBJECT, $object->get_id());
        $this->delete_objects(ContentObjectMetadata :: get_table_name(), $condition);
        
        //Delete synchronization with external repositories infos
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_CONTENT_OBJECT, $object->get_id());
        $this->delete_objects(ExternalRepositorySyncInfo :: get_table_name(), $condition);
        
        //Delete synchronization with external repositories infos
        $condition = new EqualityCondition(ExternalRepositorySync :: PROPERTY_CONTENT_OBJECT_ID, $object->get_id());
        $this->delete_objects(ExternalRepositorySync :: get_table_name(), $condition);
        
        // Delete object
        $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $object->get_id());
        $this->delete_objects(ContentObject :: get_table_name(), $condition);
        
        // Delete entry in version table
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        $this->delete_objects('content_object_version', $condition);
        
        if ($object->is_extended())
        {
            $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, $object->get_id());
            $this->delete_objects('content_object_version', $condition);
        }
        
        return true;
    }

    // Inherited.
    function delete_content_object_version($object)
    {
        if (! RepositoryDataManager :: content_object_deletion_allowed($object, 'version'))
        {
            return false;
        }
        
        // Delete object
        $query = 'DELETE FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' WHERE ' . $this->escape_column_name(ContentObject :: PROPERTY_ID) . '=' . $this->quote($object->get_id());
        $res = $this->query($query);
        $res->free();
        
        if ($object->is_extended())
        {
            $query = 'DELETE FROM ' . $this->escape_table_name($object->get_type()) . ' WHERE ' . $this->escape_column_name(ContentObject :: PROPERTY_ID) . '=' . $this->quote($object->get_id());
            $res = $this->query($query);
            $res->free();
        }
        
        if ($object->is_latest_version())
        {
            $query = 'SELECT * FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . self :: ALIAS_CONTENT_OBJECT_TABLE . ' WHERE ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . '=' . $this->quote($object->get_object_number()) . ' ORDER BY ' . self :: ALIAS_CONTENT_OBJECT_TABLE . '.' . $this->escape_column_name(ContentObject :: PROPERTY_ID) . ' DESC';
            $this->set_limit(1);
            $res = $this->query($query);
            $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
            $res->free();
            
            $props = array();
            $props[$this->escape_column_name(ContentObject :: PROPERTY_ID)] = $record['id'];
            $this->get_connection()->loadModule('Extended');
            $this->get_connection()->extended->autoExecute($this->get_table_name('content_object_version'), $props, MDB2_AUTOQUERY_UPDATE, $this->escape_column_name(ContentObject :: PROPERTY_OBJECT_NUMBER) . '=' . $object->get_object_number());
        }
        
        return true;
    }

    // Inherited.
    function delete_content_object_attachments(Condition $condition)
    {
        return $this->delete_objects(ContentObjectAttachment :: get_table_name(), $condition);
    }

    // Inherited.
    function delete_all_content_objects()
    {
        foreach (RepositoryDataManager :: get_registered_types() as $type)
        {
            if (RepositoryDataManager :: is_extended_type($type))
            {
                $this->delete_objects($this->get_table_name($type));
            }
        }
        
        $this->delete_objects($this->get_table_name(ContentObject :: get_table_name()));
    }

    function is_latest_version($object)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        $version = $this->retrieve_record('content_object_version', $condition);
        
        return ($version['id'] == $object->get_id() ? true : false);
    }

    function is_only_document_occurence($path)
    {
        $condition = new EqualityCondition(Document :: PROPERTY_PATH, $path);
        $count = $this->count_objects(Document :: get_type_name(), $condition);
        
        return ($count == 1 ? true : false);
    }

    /**
     * @param Condition $condition
     * @return Array
     */
    function retrieve_attached_content_object_ids(Condition $condition)
    {
        return $this->retrieve_distinct(ContentObjectAttachment :: get_table_name(), ContentObjectAttachment :: PROPERTY_ATTACHMENT_ID, $condition);
    }

    /**
     * @param Condition $condition
     * @return boolean|ContentObject
     */
    function retrieve_attached_content_object(Condition $condition)
    {
        $attachment_ids = $this->retrieve_attached_content_object_ids($condition);
        
        if (count($attachment_ids) != 1)
        {
            return false;
        }
        else
        {
            return $this->retrieve_content_object($attachment_ids[0]);
        }
    }

    // Inherited.
    function retrieve_attached_content_objects(Condition $condition)
    {
        $attachment_ids = $this->retrieve_attached_content_object_ids($condition);
        
        // Add non-existing element to avoid problems with 
        if (count($attachment_ids) == 0)
        {
            $attachment_ids[] = - 1;
        }
        
        $object_condition = new InCondition(ContentObject :: PROPERTY_ID, $attachment_ids, ContentObject :: get_table_name());
        return $this->retrieve_content_objects($object_condition)->as_array();
    }

    function count_objects_to_which_object_is_attached($object)
    {
        $subselect_condition = new EqualityCondition('attachment_id', $object->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, 'content_object_id', 'content_object_attachment', $subselect_condition, ContentObject :: get_table_name());
        return $this->count_content_objects($condition);
    }

    function retrieve_objects_to_which_object_is_attached($object)
    {
        $subselect_condition = new EqualityCondition('attachment_id', $object->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, 'content_object_id', 'content_object_attachment', $subselect_condition, ContentObject :: get_table_name());
        return $this->retrieve_content_objects($condition);
    }
    
    function retrieve_content_object_attachments($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(ContentObjectAttachment :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function count_objects_in_which_object_is_included($object)
    {
        $subselect_condition = new EqualityCondition('include_id', $object->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, 'content_object_id', 'content_object_include', $subselect_condition, ContentObject :: get_table_name());
        return $this->count_content_objects($condition);
    }

    function retrieve_objects_in_which_object_is_included($object)
    {
        $subselect_condition = new EqualityCondition('include_id', $object->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, 'content_object_id', 'content_object_include', $subselect_condition, ContentObject :: get_table_name());
        return $this->retrieve_content_objects($condition);
    }

    // Inherited.
    function retrieve_included_content_objects($object)
    {
        $subselect_condition = new EqualityCondition('content_object_id', $object->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, 'include_id', 'content_object_include', $subselect_condition, ContentObject :: get_table_name());
        //return $this->retrieve_content_objects($condition)->as_array();;
        

        return $this->retrieve_objects(ContentObject :: get_table_name(), $condition)->as_array();
    }

    function is_content_object_included($object)
    {
        $condition = new EqualityCondition('include_id', $object->get_id());
        $count = $this->count_objects('content_object_include', $condition);
        return ($count > 0);
    }

    function is_content_object_already_included($content_object, $include_object_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition('include_id', $include_object_id);
        $conditions[] = new EqualityCondition('content_object_id', $content_object->get_id());
        $condition = new AndCondition($conditions);
        
        $count = $this->count_objects('content_object_include', $condition);
        return ($count > 0);
    }

    function retrieve_content_object_versions($object, $include_last = true)
    {
        $object_number = $object->get_object_number();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object_number);
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, $object->get_state());
        
        if (! $include_last)
        {
            $subcond = new EqualityCondition('object_number', $object_number);
            $conditions[] = new NotCondition(new SubselectCondition(ContentObject :: PROPERTY_ID, 'id', 'content_object_version', $subcond));
        }
        
        $condition = new AndCondition($conditions);
        
        $query = 'SELECT * FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $this->get_alias(ContentObject :: get_table_name());
        
        $objects = $this->retrieve_record_set($query, ContentObject :: get_table_name(), $condition);
        
        while ($object = $objects->next_result())
        {
            $versions[] = $this->retrieve_content_object($object[ContentObject :: PROPERTY_ID]);
        }
        
        return $versions;
    }

    function retrieve_content_object_versions_resultset($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(ContentObject :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function count_content_object_versions_resultset($condition = null)
    {
        return $this->count_objects(ContentObject :: get_table_name(), $condition);
    }

    function get_latest_version_id($object)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number());
        $record = $this->retrieve_record('content_object_version', $condition);
        return $record['id'];
    }

    // Inherited.
    function attach_content_object($object, $attachment_id, $type)
    {
        $props = array();
        $props[ContentObjectAttachment :: PROPERTY_CONTENT_OBJECT_ID] = $object->get_id();
        $props[ContentObjectAttachment :: PROPERTY_ATTACHMENT_ID] = $attachment_id;
        $props[ContentObjectAttachment :: PROPERTY_TYPE] = $type;
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(ContentObjectAttachment :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    // Inherited.
    function detach_content_object($object, $attachment_id, $type)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectAttachment :: PROPERTY_CONTENT_OBJECT_ID, $object->get_id());
        $conditions[] = new EqualityCondition(ContentObjectAttachment :: PROPERTY_ATTACHMENT_ID, $attachment_id);
        $conditions[] = new EqualityCondition(ContentObjectAttachment :: PROPERTY_TYPE, $type);
        $condition = new AndCondition($conditions);
        
        return $this->delete_content_object_attachments($condition);
    }
    
    // Inherited.
    function detach_content_objects($object, $type)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectAttachment :: PROPERTY_CONTENT_OBJECT_ID, $object->get_id());
        $conditions[] = new EqualityCondition(ContentObjectAttachment :: PROPERTY_TYPE, $type);
        $condition = new AndCondition($conditions);
        
        return $this->delete_content_object_attachments($condition);
    }

    // Inherited.
    function include_content_object($object, $include_id)
    {
        $props = array();
        $props['content_object_id'] = $object->get_id();
        $props['include_id'] = $include_id;
        $this->get_connection()->loadModule('Extended');
        $this->get_connection()->extended->autoExecute($this->get_table_name('content_object_include'), $props, MDB2_AUTOQUERY_INSERT);
    }

    // Inherited.
    function exclude_content_object($object, $include_id)
    {
        $query = 'DELETE FROM ' . $this->escape_table_name('content_object_include') . ' WHERE ' . $this->escape_column_name('content_object_id') . '=' . $this->quote($object->get_id()) . ' AND ' . $this->escape_column_name('include_id') . '=' . $this->quote($include_id);
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
            return $this->update_objects(ContentObject :: get_table_name(), $properties, $condition);
        }
    }

    // Inherited.
    function get_children_ids($object)
    {
        $children_ids = array();
        $parent_ids = array($object->get_id());
        do
        {
            $query = 'SELECT ' . $this->escape_column_name(ContentObject :: PROPERTY_ID) . ' FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' WHERE ' . $this->escape_column_name(ContentObject :: PROPERTY_PARENT_ID) . ' IN (?' . implode(',', $parent_ids) . ')';
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
        $query = 'SELECT ' . $this->escape_column_name(ContentObject :: PROPERTY_ID) . ' FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $this->get_alias(ContentObject :: get_table_name());
        $versions = $this->retrieve_record_set($query, ContentObject :: get_table_name(), $condition, null, null, $order_by);
        
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
     * Parses a database record fetched as an associative array into a
     * learning object.
     * @param array $record The associative array.
     * @param boolean $additional_properties_known True if the additional
     * properties of the
     * learning object were
     * fetched.
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
        /*$defaultProp[ContentObject :: PROPERTY_CREATION_DATE] = self :: from_db_date($defaultProp[ContentObject :: PROPERTY_CREATION_DATE]);
        $defaultProp[ContentObject :: PROPERTY_MODIFICATION_DATE] = self :: from_db_date($defaultProp[ContentObject :: PROPERTY_MODIFICATION_DATE]);*/
        
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
        $types = RepositoryDataManager :: get_registered_types();
        $co_alias = $this->get_alias(ContentObject :: get_table_name());
        
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
                $sum[] = 'SUM(' . $this->escape_column_name($property) . ')';
            }
            if (RepositoryDataManager :: is_extended_type($type))
            {
                $query = 'SELECT ' . implode('+', $sum) . ' AS disk_space FROM ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' JOIN ' . $this->escape_table_name($type) . ' AS ' . self :: ALIAS_TYPE_TABLE . ' ON ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_ID) . ' = ' . self :: ALIAS_TYPE_TABLE . '.' . $this->escape_column_name(ContentObject :: PROPERTY_ID);
                $condition = $condition_owner;
            }
            else
            {
                $query = 'SELECT ' . implode('+', $sum) . ' AS disk_space FROM ' . $this->escape_table_name(ContentObject :: get_table_name());
                $match = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
                $condition = new AndCondition(array($match, $condition_owner));
            }
            
            if (isset($condition))
            {
                $translator = new ConditionTranslator($this, $this->get_alias(ContentObject :: get_table_name()));
                $query .= $translator->render_query($condition);
            }
            
            $res = $this->query($query);
            $record = $res->fetchRow(MDB2_FETCHMODE_OBJECT);
            $disk_space += $record->disk_space;
            $res->free();
        }
        return $disk_space;
    }

    private static function is_content_object_column($name)
    {
        return ContentObject :: is_default_property_name($name) || $name == ContentObject :: PROPERTY_TYPE || $name == ContentObject :: PROPERTY_ID;
    }

    function ExecuteQuery($sql)
    {
        $this->get_connection()->query($sql);
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
        
        $count = $this->count_objects(ContentObjectAttachment :: get_table_name(), $condition);
        
        //        $query = 'SELECT COUNT(' . $this->escape_column_name('content_object_id', $this->get_alias('content_object_attachment')) . ') FROM ' . $this->escape_table_name('content_object_attachment') . ' AS ' . $this->get_alias('content_object_attachment');
        //        $count = $this->count_result_set($query, 'content_object_attachment', $condition);
        

        return $count > 0;
        //        $query = 'SELECT COUNT(' . $this->escape_column_name("content_object_id") . ') FROM ' . $this->escape_table_name('content_object_attachment') . ' AS ' . self :: ALIAS_CONTENT_OBJECT_ATTACHMENT_TABLE . ' WHERE ' . self :: ALIAS_CONTENT_OBJECT_ATTACHMENT_TABLE . '.attachment_id';
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
    //        $sth = $this->get_connection()->prepare($query);
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
     * Creates a new complex learning object in the database
     * @param ComplexContentObject $clo - The complex learning object
     * @return True if success
     */
    function create_complex_content_object_item($clo_item)
    {
        $props = array();
        foreach ($clo_item->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }
        $props[$this->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID)] = $this->get_better_next_id(ComplexContentObjectItem :: get_table_name(), ComplexContentObjectItem :: PROPERTY_ID);
        $this->get_connection()->loadModule('Extended');
        $this->get_connection()->extended->autoExecute($this->get_table_name(ComplexContentObjectItem :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT);
        $clo_item->set_id($this->get_connection()->extended->getAfterID($props[$this->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID)], ComplexContentObjectItem :: get_table_name()));
        
        if ($clo_item->is_extended())
        {
            $ref = $clo_item->get_ref();
            
            $props = array();
            foreach ($clo_item->get_additional_properties() as $key => $value)
            {
                $props[$this->escape_column_name($key)] = $value;
            }
            $props[$this->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID)] = $clo_item->get_id();
            $type = $this->determine_content_object_type($ref);
            $this->get_connection()->extended->autoExecute($this->get_table_name('complex_' . $type), $props, MDB2_AUTOQUERY_INSERT);
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
            $props[$this->escape_column_name($key)] = $value;
        }
        $this->get_connection()->loadModule('Extended');
        $this->get_connection()->extended->autoExecute($this->get_table_name(ComplexContentObjectItem :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $condition);
        if ($clo_item->is_extended())
        {
            $ref = $clo_item->get_ref();
            
            $props = array();
            foreach ($clo_item->get_additional_properties() as $key => $value)
            {
                $props[$this->escape_column_name($key)] = $value;
            }
            $type = $this->determine_content_object_type($ref);
            $this->get_connection()->extended->autoExecute($this->get_table_name('complex_' . $type), $props, MDB2_AUTOQUERY_UPDATE, $condition);
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
        
        $query = 'DELETE FROM ' . $this->escape_table_name(ComplexContentObjectItem :: get_table_name());
        
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }
        
        //$this->set_limit(1);
        $res = $this->query($query);
        $res->free();
        
        if ($clo_item->is_extended())
        {
            $ref = $clo_item->get_ref();
            
            $type = $this->determine_content_object_type($ref);
            $query = 'DELETE FROM ' . $this->get_table_name('complex_' . $type);
            
            if (isset($condition))
            {
                $translator = new ConditionTranslator($this);
                $query .= $translator->render_query($condition);
            }
            
            //$this->set_limit(1);
            $this->query($query);
        }
        
        $conditions = array();
        $conditions[] = new InequalityCondition(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $clo_item->get_display_order());
        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $clo_item->get_parent());
        $condition = new AndCondition($conditions);
        $properties[ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER] = $this->escape_column_name(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER) . '-1';
        
        $this->update_objects(ComplexContentObjectItem :: get_table_name(), $properties, $condition);
        
        return true;
    
    }

    function delete_complex_content_object_items($condition)
    {
        return $this->delete(ComplexContentObjectItem :: get_table_name(), $condition);
    }

    /**
     * Retrieves a complex learning object from the database with a given id
     * @param Int $clo_id
     * @return The complex learning object
     */
    function retrieve_complex_content_object_item($clo_item_id)
    {
        // Retrieve main table
        $query = 'SELECT * FROM ' . $this->escape_table_name(ComplexContentObjectItem :: get_table_name()) . ' AS ' . $this->get_alias(ComplexContentObjectItem :: get_table_name());
        
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $clo_item_id, ComplexContentObjectItem :: get_table_name());
        
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }
        
        $this->set_limit(1);
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        if (! $record)
            return null;
            
        // Determine type
        

        $res->free();
        
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
            
            $query = 'SELECT * FROM ' . $this->escape_table_name('complex_' . $type) . ' AS ' . self :: ALIAS_TYPE_TABLE;
            
            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $record['id']);
            
            if (isset($condition))
            {
                $translator = new ConditionTranslator($this);
                $query .= $translator->render_query($condition);
            }
            
//            dump($query);
            
            $this->set_limit(1);
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
        return $this->count_objects(ComplexContentObjectItem :: get_table_name(), $condition);
    }

    /**
     * Retrieves the complex learning object items with the given condition
     * @param Condition
     */
    function retrieve_complex_content_object_items($condition = null, $order_by = array (), $offset = 0, $max_objects = -1, $type = null)
    {
        $alias = $this->get_alias(ComplexContentObjectItem :: get_table_name());
        
        $query = 'SELECT ' . $alias . '.* FROM ' . $this->escape_table_name(ComplexContentObjectItem :: get_table_name()) . ' AS ' . $alias;
        
        if (isset($type))
        {
            $alias_type_table = $this->get_alias($type);
            
            switch ($type)
            {
                case 'complex_wiki_page' :
                    $query .= ' JOIN ' . $this->escape_table_name($type) . ' AS ' . $alias_type_table . ' ON ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $alias) . ' = ' . $this->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID, $alias_type_table);
            }
        }
        $lo_alias = $this->get_alias(ContentObject :: get_table_name());
        
        $query .= ' JOIN ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $lo_alias . ' ON ' . $alias . '.ref_id=' . $lo_alias . '.id';
        
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $alias);
            $query .= $translator->render_query($condition);
        }
        
        $order_by[] = new ObjectTableOrder(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, SORT_ASC, $alias);
        
        $orders = array();
        foreach ($order_by as $order)
        {
            $alias = $order->get_alias() ? $order->get_alias() . '.' : '';
            $orders[] = $this->escape_column_name($alias . $order->get_property()) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        
        if ($max_objects < 0)
        {
            $max_objects = null;
        }
        
        $this->set_limit(intval($max_objects), intval($offset));
        $res = $this->query($query);
        
        return new DatabaseComplexContentObjectItemResultSet($this, $res, true);
    }

    function select_next_display_order_forum($parent_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent_id);
        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Forum :: get_type_name());
        $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
        $condition = new AndCondition($conditions);
        return $this->retrieve_next_sort_value(ComplexContentObjectItem :: get_table_name(), ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function select_next_display_order($parent_id)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent_id);
        
        return $this->retrieve_next_sort_value(ComplexContentObjectItem :: get_table_name(), ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $category->get_id());
        $succes = $this->delete(RepositoryCategory :: get_table_name(), $condition);
        
        // Correct the diplsay order of the remaining categories
        $conditions = array();
        $conditions[] = new InequalityCondition(RepositoryCategory :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $category->get_display_order());
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $category->get_parent());
        $condition = new AndCondition($conditions);
        $properties = array(RepositoryCategory :: PROPERTY_DISPLAY_ORDER => ($this->escape_column_name(RepositoryCategory :: PROPERTY_DISPLAY_ORDER) . '-1'));
        $this->update_objects(RepositoryCategory :: get_table_name(), $properties, $condition);
        
        // Move the objecs in the category to the garbage bin
        $condition = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category->get_id());
        $properties = array(ContentObject :: PROPERTY_STATE => '1');
        $this->update_objects(ContentObject :: get_table_name(), $properties, $condition);
        
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
        return $this->update($category, $condition);
    }

    function create_category($category)
    {
        return $this->create($category);
    }

    function create_external_repository($external_repository)
    {
        return $this->create($external_repository);
    }

    function count_categories($conditions = null)
    {
        return $this->count_objects(RepositoryCategory :: get_table_name(), $conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        if (is_a($order_property, 'ObjectTableOrder'))
        {
            $order_property = array($order_property);
        }
        
        $order_property[] = new ObjectTableOrder(RepositoryCategory :: PROPERTY_PARENT);
        $order_property[] = new ObjectTableOrder(RepositoryCategory :: PROPERTY_DISPLAY_ORDER);
        return $this->retrieve_objects(RepositoryCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function select_next_category_display_order($parent_category_id, $user_id)
    {
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $parent_category_id);
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $user_id);
        $condition = new AndCondition($conditions);
        
        return $this->retrieve_next_sort_value(RepositoryCategory :: get_table_name(), RepositoryCategory :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function delete_user_view($user_view)
    {
        $condition = new EqualityCondition(UserView :: PROPERTY_ID, $user_view->get_id());
        $success = $this->delete(UserView :: get_table_name(), $condition);
        
        $condition = new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $user_view->get_id());
        $success &= $this->delete(UserViewRelContentObject :: get_table_name(), $condition);
        
        return $success;
    }

    function update_user_view($user_view)
    {
        $condition = new EqualityCondition(UserView :: PROPERTY_ID, $user_view->get_id());
        return $this->update($user_view, $condition);
    }

    function create_user_view($user_view)
    {
        return $this->create($user_view);
    }

    function count_user_views($conditions = null)
    {
        return $this->count_objects(UserView :: get_table_name(), $conditions);
    }

    function retrieve_user_views($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(UserView :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function update_user_view_rel_content_object($user_view_rel_content_object)
    {
        $conditions[] = new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $user_view_rel_content_object->get_view_id());
        $conditions[] = new EqualityCondition(UserViewRelContentObject :: PROPERTY_CONTENT_OBJECT_TYPE, $user_view_rel_content_object->get_content_object_type());
        
        $condition = new AndCondition($conditions);
        
        return $this->update($user_view_rel_content_object, $condition);
    }

    function update_content_object_pub_feedback($content_object_pub_feedback)
    {
        $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_PUBLICATION_ID, $content_object_pub_feedback->get_publication_id());
        $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_CLOI_ID, $content_object_pub_feedback->get_cloi_id());
        $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $content_object_pub_feedback->get_feedback_id());
        $condition = new AndCondition($conditions);
        
        return $this->update($content_object_pub_feedback, $condition);
    }

    function create_user_view_rel_content_object($user_view_rel_content_object)
    {
        return $this->create($user_view_rel_content_object);
    }

    function create_content_object_pub_feedback($content_object_pub_feedback)
    {
        return $this->create($content_object_pub_feedback);
    }

    function retrieve_user_view_rel_content_objects($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(UserViewRelContentObject :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function retrieve_content_object_pub_feedback($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(ContentObjectPubFeedback :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function delete_content_object_pub_feedback($content_object_pub_feedback)
    {
        $condition = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $content_object_pub_feedback->get_feedback_id());
        
        $success = $this->delete(ContentObjectPubFeedback :: get_table_name(), $condition);
        
        return $success;
    }

    function reset_user_view($user_view)
    {
        $condition = new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $user_view->get_id());
        $properties[UserViewRelContentObject :: PROPERTY_VISIBILITY] = '0';
        
        return $this->update_objects(UserViewRelContentObject :: get_table_name(), $properties, $condition);
    }

    function retrieve_last_post($forum_id)
    {
        $complex_item_alias = $this->get_alias(ComplexContentObjectItem :: get_table_name());
        $complex_item_alias_bis = $this->get_alias(ComplexContentObjectItem :: get_table_name() . '_bis');
        $forum_alias = $this->get_alias('forum');
        $forum_topic_alias = $this->get_alias('forum_topic');
        
        $query = 'SELECT ' . $complex_item_alias . '.*';
        $query .= ' FROM ' . $this->escape_table_name(ComplexContentObjectItem :: get_table_name()) . ' AS ' . $complex_item_alias;
        $query .= ' LEFT JOIN ' . $this->escape_table_name('forum') . ' AS ' . $forum_alias . ' ON ' . $this->escape_column_name(ComplexContentObjectItem :: PROPERTY_REF, $complex_item_alias) . ' = ' . $this->escape_column_name(Forum :: PROPERTY_ID, $forum_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name('forum_topic') . ' AS ' . $forum_topic_alias . ' ON ' . $this->escape_column_name(ComplexContentObjectItem :: PROPERTY_REF, $complex_item_alias) . ' = ' . $this->escape_column_name(ForumTopic :: PROPERTY_ID, $forum_topic_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(ComplexContentObjectItem :: get_table_name()) . ' AS ' . $complex_item_alias_bis . ' ON ' . $this->escape_column_name(Forum :: PROPERTY_LAST_POST, $forum_alias) . ' = ' . $this->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID, $complex_item_alias_bis);
        $query .= ' OR ' . $this->escape_column_name(ForumTopic :: PROPERTY_LAST_POST, $forum_topic_alias) . ' = ' . $this->escape_column_name(ComplexContentObjectItem :: PROPERTY_ID, $complex_item_alias_bis);
        $query .= ' WHERE ' . $this->escape_column_name(ComplexContentObjectItem :: PROPERTY_PARENT, $complex_item_alias) . '=' . $this->quote($forum_id);
        $query .= ' ORDER BY ' . $this->escape_column_name(ComplexContentObjectItem :: PROPERTY_ADD_DATE, $complex_item_alias_bis) . 'DESC';
        
        $this->set_limit(1);
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        $res->free();
        
        $object_reference = $record[ComplexContentObjectItem :: PROPERTY_REF];
        $object_type = $this->determine_content_object_type($object_reference);
        
        if ($record)
            return $this->record_to_complex_content_object_item($record, $object_type, true);
    }

    function create_content_object_metadata($content_object_metadata)
    {
        $created = $content_object_metadata->get_creation_date();
        if (is_numeric($created))
        {
            $content_object_metadata->set_creation_date($content_object_metadata->get_creation_date());
        }
        
        return $this->create($content_object_metadata);
    }

    function update_content_object_metadata($content_object_metadata)
    {
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_ID, $content_object_metadata->get_id());
        
        $date = $content_object_metadata->get_modification_date();
        if (is_numeric($date))
        {
            $content_object_metadata->set_modification_date($content_object_metadata->get_modification_date());
        }
        
        return $this->update($content_object_metadata, $condition);
    }

    function delete_content_object_metadata($content_object_metadata)
    {
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_ID, $content_object_metadata->get_id());
        return $this->delete($content_object_metadata->get_table_name(), $condition);
    }

    function retrieve_content_object_metadata($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(ContentObjectMetadata :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_content_object_by_catalog_entry_values($catalog_name, $entry_value)
    {
        if (StringUtilities :: has_value($catalog_name) && StringUtilities :: has_value($entry_value))
        {
            $catalog_name = StringUtilities :: escape_mysql($catalog_name);
            $entry_value = StringUtilities :: escape_mysql($entry_value);
            
            $query = 'SELECT count(*) as total, content_object_id FROM repository_content_object_metadata
                WHERE
                (property LIKE \'general_identifier[%][catalog]\' AND value = \'' . $catalog_name . '\')
                OR
                (property LIKE \'general_identifier[%][entry]\' AND value = \'' . $entry_value . '\')
                GROUP BY content_object_id
                HAVING total=2';
            
            return $this->retrieve_object_set($query, 'repository_content_object_metadata', null, null, null, null, 'ContentObjectMetadata');
        }
    }

    function create_content_object_metadata_catalog($content_object_metadata_catalog)
    {
        $created = $content_object_metadata_catalog->get_creation_date();
        if (is_numeric($created))
        {
            $content_object_metadata_catalog->set_creation_date($content_object_metadata_catalog->get_creation_date());
        }
        
        return $this->create($content_object_metadata_catalog);
    }

    function update_content_object_metadata_catalog($content_object_metadata_catalog)
    {
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_ID, $content_object_metadata_catalog->get_id());
        
        $date = $content_object_metadata_catalog->get_modification_date();
        if (is_numeric($date))
        {
            $content_object_metadata_catalog->set_modification_date($content_object_metadata_catalog->get_modification_date());
        }
        
        return $this->update($content_object_metadata_catalog, $condition);
    }

    function delete_content_object_metadata_catalog($content_object_metadata_catalog)
    {
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_ID, $content_object_metadata_catalog->get_id());
        return $this->delete($content_object_metadata_catalog->get_table_name(), $condition);
    }

    function set_new_clo_version($lo_id, $new_lo_id)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $lo_id, ComplexContentObjectItem :: get_table_name());
        $props = array();
        $props[$this->escape_column_name(ComplexContentObjectItem :: PROPERTY_PARENT)] = $new_lo_id;
        $this->get_connection()->loadModule('Extended');
        return $this->get_connection()->extended->autoExecute($this->get_table_name(ComplexContentObjectItem :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $condition);
    }

    function retrieve_external_repository_condition($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(ExternalRepository :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_external_repository($external_repository_id)
    {
        $condition = new EqualityCondition(ExternalRepository :: PROPERTY_ID, $external_repository_id);
        return $this->retrieve_object(ExternalRepository :: get_table_name(), $condition);
    }

    function retrieve_external_repositories($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(ExternalRepository :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function count_external_repositories($condition = null)
    {
        return $this->count_objects(ExternalRepository :: get_table_name(), $condition);
    }

    function retrieve_active_external_repository_types()
    {
        $condition = new EqualityCondition(ExternalRepository :: PROPERTY_ENABLED, 1);
        return $this->retrieve_distinct(ExternalRepository :: get_table_name(), ExternalRepository :: PROPERTY_TYPE, $condition);
    }

    function retrieve_external_repository_fedora($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(ExternalRepositoryFedora :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_external_repository_user_quotum($user_id, $external_repository_id)
    {
        $condition2 = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_USER_ID, $user_id);
        $condition1 = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);

        $condition = new AndCondition($condition1, $condition2);
        return $this->retrieve_object(ExternalRepositoryUserQuotum :: get_table_name(), $condition);
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
        $alias = $this->get_alias('Catalog');
        $query = substr($query, 0, $after_table_position) . ' AS ' . $alias . ' ' . substr($query, $after_table_position);
        
        if (isset($condition))
        {
            $condition->set_storage_unit('Catalog');
        }
        
        if (isset($order_by))
        {
            $order_by->set_alias($alias);
        }
        
        return $this->retrieve_object_set($query, $table_name, $condition, $offset, $max_objects, $order_by);
    }

    function create_external_repository_sync_info($external_repository_sync_info)
    {
        $created = $external_repository_sync_info->get_creation_date();
        if (is_numeric($created))
        {
            $external_repository_sync_info->set_creation_date($external_repository_sync_info->get_creation_date());
        }
        
        return $this->create($external_repository_sync_info);
    }

    function create_external_repository_sync($external_repository_sync)
    {
        return $this->create($external_repository_sync);
    }

    function update_external_repository_sync_info($external_repository_sync_info)
    {
        $condition = new EqualityCondition(ExternalRepositorySyncInfo :: PROPERTY_ID, $external_repository_sync_info->get_id());
        
        $date = $external_repository_sync_info->get_modification_date();
        if (is_numeric($date))
        {
            $external_repository_sync_info->set_modification_date($external_repository_sync_info->get_modification_date());
        }
        
        return $this->update($external_repository_sync_info, $condition);
    }

    function update_external_repository_sync($external_repository_sync)
    {
        $condition = new EqualityCondition(ExternalRepositorySync :: PROPERTY_ID, $external_repository_sync->get_id());
        return $this->update($external_repository_sync, $condition);
    }

    function delete_external_repository_sync_info($external_repository_sync_info)
    {
        $condition = new EqualityCondition(ExternalRepositorySyncInfo :: PROPERTY_ID, $external_repository_sync_info->get_id());
        return $this->delete($external_repository_sync_info->get_table_name(), $condition);
    }

    function delete_external_repository_sync($external_repository_sync)
    {
        $condition = new EqualityCondition(ExternalRepositorySync :: PROPERTY_ID, $external_repository_sync->get_id());
        return $this->delete($external_repository_sync->get_table_name(), $condition);
    }

    function retrieve_external_repository_sync_info($conditions)
    {
        return $this->retrieve_object(ExternalRepositorySyncInfo :: get_table_name(), $condition);
    }

    function retrieve_external_repository_sync($condition)
    {
        $content_object_alias = $this->get_alias(ContentObject :: get_table_name());
        $synchronization_alias = $this->get_alias(ExternalRepositorySync :: get_table_name());
        
        $query = 'SELECT ' . $synchronization_alias . '.* FROM ' . $this->escape_table_name(ExternalRepositorySync :: get_table_name()) . ' AS ' . $synchronization_alias . ' JOIN ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias . ' ON ' . $this->escape_column_name(ExternalRepositorySync :: PROPERTY_CONTENT_OBJECT_ID, $synchronization_alias) . ' = ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias);
        $record = $this->retrieve_row($query, ExternalRepositorySync :: get_table_name(), $condition);
        
        if ($record)
        {
            return self :: record_to_object($record, ExternalRepositorySync :: CLASS_NAME);
        }
        else
        {
            return false;
        }
    }

    function retrieve_external_repository_syncs($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $content_object_alias = $this->get_alias(ContentObject :: get_table_name());
        $synchronization_alias = $this->get_alias(ExternalRepositorySync :: get_table_name());
        
        $query = 'SELECT ' . $synchronization_alias . '.* FROM ' . $this->escape_table_name(ExternalRepositorySync :: get_table_name()) . ' AS ' . $synchronization_alias . ' JOIN ' . $this->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias . ' ON ' . $this->escape_column_name(ExternalRepositorySync :: PROPERTY_CONTENT_OBJECT_ID, $synchronization_alias) . ' = ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias);
        return $this->retrieve_object_set($query, ExternalRepositorySync :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function delete_content_object_includes($object)
    {
        $query = 'DELETE FROM ' . $this->escape_table_name('content_object_include') . ' WHERE ' . $this->escape_column_name('include_id') . '=' . $this->quote($object->get_id());
        $affectedRows = $this->query($query);
        return ($affectedRows > 0);
    }

    function delete_assisting_content_objects($object)
    {
        $assisting_types = array(LearningPathItem :: get_type_name(), PortfolioItem :: get_type_name());
        
        $failures = 0;
        
        foreach ($assisting_types as $type)
        {
            $sub_condition = new EqualityCondition('reference_id', $object->get_id());
            $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, 'id', $type, $sub_condition, ContentObject :: get_table_name());
            $assisting_objects = $this->retrieve_content_objects($condition);
            
            while ($assisting_object = $assisting_objects->next_result())
            {
                if (! RepositoryDataManager :: delete_clois_for_content_object($assisting_object))
                {
                    $failures ++;
                }
                
                if (! $assisting_object->delete())
                {
                    $failures ++;
                }
            }
        
        }
        
        return ($failures == 0);
    }

    function retrieve_doubles_in_repository($condition, $order_property, $offset, $count)
    {
        $co_table = $this->escape_table_name(ContentObject :: get_table_name());
        $co_alias = $this->get_alias(ContentObject :: get_table_name());
        $version_table = $this->escape_table_name('content_object_version');
        $version_alias = $this->get_alias('content_object_version');
        
        $sql = 'SELECT ' . $co_alias . '.id, title, description, type, count(content_hash) as content_hash FROM ' . $co_table . ' as ' . $co_alias . '
				JOIN ' . $version_table . ' as ' . $version_alias . ' ON ' . $co_alias . '.id = ' . $version_alias . '.id';
        
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $co_alias);
            $sql .= $translator->render_query($condition);
        }
        
        $sql .= ' GROUP BY content_hash HAVING count(content_hash) > 1';
        
        return $this->retrieve_object_set($sql, ContentObject :: get_table_name(), null, $offset, $count, $order_property);
    }

    function count_doubles_in_repository($condition)
    {
        $co_table = $this->escape_table_name(ContentObject :: get_table_name());
        $co_alias = $this->get_alias(ContentObject :: get_table_name());
        $version_table = $this->escape_table_name('content_object_version');
        $version_alias = $this->get_alias('content_object_version');
        
        $sql = 'SELECT COUNT(*) FROM ' . $co_table . ' as ' . $co_alias . '
				JOIN ' . $version_table . ' as ' . $version_alias . ' ON ' . $co_alias . '.id = ' . $version_alias . '.id';
        
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $co_alias);
            $sql .= $translator->render_query($condition);
        }
        
        $sql .= ' GROUP BY content_hash HAVING count(content_hash) > 1';
        
        return $this->count_result_set($sql, ContentObject :: get_table_name());
    }

    /* (non-PHPdoc)
     * @see repository/lib/RepositoryDataManagerInterface#create_external_repository_setting()
     */
    function create_external_repository_setting(ExternalRepositorySetting $external_repository_setting)
    {
        return $this->create($external_repository_setting);
    }

    /* (non-PHPdoc)
     * @see repository/lib/RepositoryDataManagerInterface#update_external_repository_setting()
     */
    function update_external_repository_setting(ExternalRepositorySetting $external_repository_setting)
    {
        $condition = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_ID, $external_repository_setting->get_id());
        return $this->update($external_repository_setting, $condition);
    }

    /* (non-PHPdoc)
     * @see repository/lib/RepositoryDataManagerInterface#delete_external_repository_setting()
     */
    function delete_external_repository_setting(ExternalRepositorySetting $external_repository_setting)
    {
        $condition = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_ID, $external_repository_setting->get_id());
        return $this->delete($external_repository_setting->get_table_name(), $condition);
    }

    /**
     * @param int $id
     */
    function retrieve_external_repository_setting($id)
    {
        $condition = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_ID, $id);
        return $this->retrieve_object(ExternalRepositorySetting :: get_table_name(), $condition);
    
    }

    /* (non-PHPdoc)
     * @see repository/lib/RepositoryDataManagerInterface#retrieve_external_repository_settings()
     */
    function retrieve_external_repository_settings($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(ExternalRepositorySetting :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    /**
     * @param int $id
     */
    function retrieve_external_repository_user_setting($id)
    {
        $condition = new EqualityCondition(ExternalRepositoryUserSetting :: PROPERTY_ID, $id);
        return $this->retrieve_object(ExternalRepositoryUserSetting :: get_table_name(), $condition);
    }

    /* (non-PHPdoc)
     * @see repository/lib/RepositoryDataManagerInterface#retrieve_external_repository_settings()
     */
    function retrieve_external_repository_user_settings($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(ExternalRepositoryUserSetting :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_external_repository_setting_from_variable_name($variable, $external_repository_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
        $conditions[] = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_VARIABLE, $variable);
        $condition = new AndCondition($conditions);
        
        return $this->retrieve_object(ExternalRepositorySetting :: get_table_name(), $condition);
    }

    /* (non-PHPdoc)
     * @see repository/lib/RepositoryDataManagerInterface#create_external_repository_setting()
     */
    function create_external_repository_user_setting(ExternalRepositoryUserSetting $external_repository_user_setting)
    {
        return $this->create($external_repository_user_setting);
    }

    /* (non-PHPdoc)
     * @see repository/lib/RepositoryDataManagerInterface#update_external_repository_setting()
     */
    function update_external_repository_user_setting(ExternalRepositoryUserSetting $external_repository_user_setting)
    {
        $condition = new EqualityCondition(ExternalRepositoryUserSetting :: PROPERTY_ID, $external_repository_user_setting->get_id());
        return $this->update($external_repository_user_setting, $condition);
    }

    /* (non-PHPdoc)
     * @see repository/lib/RepositoryDataManagerInterface#delete_external_repository_setting()
     */
    function delete_external_repository_user_setting(ExternalRepositoryUserSetting $external_repository_user_setting)
    {
        $condition = new EqualityCondition(ExternalRepositoryUserSetting :: PROPERTY_ID, $external_repository_user_setting->get_id());
        return $this->delete($external_repository_user_setting->get_table_name(), $condition);
    }

    function delete_external_repository_user_settings($condition = null)
    {
        return $this->delete_objects(ExternalRepositoryUserSetting :: get_table_name(), $condition);
    }

    function update_external_repository($external_repository)
    {
        $condition = new EqualityCondition(ExternalRepository :: PROPERTY_ID, $external_repository->get_id());
        return $this->update($external_repository, $condition);
    }

    function delete_external_repository($external_repository)
    {
        $condition = new EqualityCondition(ExternalRepository :: PROPERTY_ID, $external_repository->get_id());
        return $this->delete(ExternalRepository :: get_table_name(), $condition);
    }

    function create_external_repository_user_quotum(ExternalRepositoryUserQuotum $external_repository_user_quotum)
    {
        return $this->create($external_repository_user_quotum);
    }

    function update_external_repository_user_quotum(ExternalRepositoryUserQuotum $external_repository_user_quotum)
    {
        $condition = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_ID, $external_repository_user_quotum->get_id());
        return $this->update($external_repository_user_quotum, $condition);
    }

    function delete_external_repository_user_quotum(ExternalRepositoryUserQuotum $external_repository_user_quotum)
    {
        $condition = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_ID, $external_repository_user_quotum->get_id());
        return $this->delete(ExternalRepositoryUserQuotum :: get_table_name(), $condition);
    }
}
?>
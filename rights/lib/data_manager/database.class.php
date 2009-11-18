<?php
/**
 * $Id: database.class.php 235 2009-11-16 12:08:00Z scaramanga $
 * @package rights.lib.data_manager
 */
require_once dirname(__FILE__) . '/database/database_rights_template_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_right_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_location_result_set.class.php';
require_once dirname(__FILE__) . '/database/database_rights_template_right_location_result_set.class.php';
require_once 'MDB2.php';

/**
 ==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 ==============================================================================
 */

class DatabaseRightsDataManager extends RightsDataManager
{
    const ALIAS_USER_TABLE = 'u';

    /**
     * The database connection.
     */
    private $connection;

    private $database;

    /**
     * The table name prefix, if any.
     */
    private $prefix;

    function initialize()
    {
        $this->database = new Database();
        $this->database->set_prefix('rights_');

        $this->connection = Connection :: get_instance()->get_connection();
        $this->connection->setOption('debug_handler', array(get_class($this), 'debug'));

        $this->prefix = 'rights_';
        $this->connection->query('SET NAMES utf8');
    }

    function get_database()
    {
        return $this->database;
    }

    function debug()
    {
        $args = func_get_args();
        // Do something with the arguments
        if ($args[1] == 'query')
        {
            //echo '<pre>';
        //echo($args[2]);
        //echo '</pre>';
        }
    }

    private static function is_user_column($name)
    {
        return User :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
    }

    /**
     * Checks whether the given column name is the name of a column that
     * contains a date value, and hence should be formatted as such.
     * @param string $name The column name.
     * @return boolean True if the column is a date column, false otherwise.
     */
    static function is_date_column($name)
    {
        return false;
    }

    function update_rights_template_right_location($rights_templaterightlocation)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_RIGHT_ID, $rights_templaterightlocation->get_right_id());
        $conditions[] = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_LOCATION_ID, $rights_templaterightlocation->get_rights_template_id());
        $condition = new AndCondition($conditions);

        return $this->database->update($rights_templaterightlocation, $condition);
    }

    function delete_rights_template_right_location($rights_template_right_location)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_RIGHT_ID, $rights_template_right_location->get_right_id());
        $conditions[] = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template_right_location->get_rights_template_id());
        $conditions[] = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_LOCATION_ID, $rights_template_right_location->get_location_id());
        $condition = new AndCondition($conditions);

        return $this->database->delete(RightsTemplateRightLocation :: get_table_name(), $condition);
    }

    function delete_rights_template_right_locations($condition)
    {
        return $this->database->delete_objects(RightsTemplateRightLocation :: get_table_name(), $condition);
    }

    //Inherited.
    function create_location($location)
    {
        return $this->database->create($location);
    }

    function create_right($right)
    {
        return $this->database->create($right);
    }

    function create_rights_template($rights_template)
    {
        return $this->database->create($rights_template);
    }

    function create_rights_template_right_location($rights_template_right_location)
    {
        return $this->database->create($rights_template_right_location);
    }

    //Inherited.
    function get_next_rights_template_id()
    {
        //return $this->connection->nextID($this->get_table_name(RightsTemplate :: get_table_name()));
        return $this->connection->nextID(RightsTemplate :: get_table_name());
    }

    function get_next_user_right_location_id()
    {
        //return $this->connection->nextID($this->get_table_name(UserRightLocation :: get_table_name()));
        return $this->connection->nextID(UserRightLocation :: get_table_name());
    }

    function get_next_group_right_location_id()
    {
        //return $this->connection->nextID($this->get_table_name(GroupRightLocation :: get_table_name()));
        return $this->connection->nextID(GroupRightLocation :: get_table_name());
    }

    //Inherited.
    function get_next_right_id()
    {
      	//return $this->connection->nextID($this->get_table_name(Right :: get_table_name()));
      	return $this->connection->nextID(Right :: get_table_name());
    }

    //Inherited.
    function get_next_location_id()
    {
        //return $this->connection->nextID($this->get_table_name(Location :: get_table_name()));
        return $this->connection->nextID(Location :: get_table_name());
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function record_to_rights_template_right_location($record)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }

        $defaultProp = array();
        foreach (RightsTemplateRightLocation :: get_default_property_names() as $prop)
        {
            $defaultProp[$prop] = $record[$prop];
        }

        return new RightsTemplateRightLocation($defaultProp);
    }

    function retrieve_location_id_from_location_string($location)
    {
        $condition = new PatternMatchCondition(Location :: PROPERTY_NAME, $location);
        return $this->database->retrieve_object(Location :: get_table_name(), $condition, array(), Location :: CLASS_NAME);
    }

    /**
     * retrieves the rights_template and right location
     *
     * @param int $right_id
     * @param int $rights_template_id
     * @param int $location_id
     * @return RightsTemplateRightLocation
     */
    function retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_RIGHT_ID, $right_id);
        $conditions[] = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template_id);
        $conditions[] = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_LOCATION_ID, $location_id);
        $condition = new AndCondition($conditions);

        $res = $this->database->retrieve_object(RightsTemplateRightLocation :: get_table_name(), $condition, array(), RightsTemplateRightLocation :: CLASS_NAME);

        if (!$res)
        {
            $defaultProperties = array();
            $defaultProperties[RightsTemplateRightLocation :: PROPERTY_RIGHTS_TEMPLATE_ID] = $rights_template_id;
            $defaultProperties[RightsTemplateRightLocation :: PROPERTY_RIGHT_ID] = $right_id;
            $defaultProperties[RightsTemplateRightLocation :: PROPERTY_LOCATION_ID] = $location_id;
            $defaultProperties[RightsTemplateRightLocation :: PROPERTY_VALUE] = 0;

            $rights_templaterightlocation = new RightsTemplateRightLocation();
            $rights_templaterightlocation->set_default_properties($defaultProperties);
            $rights_templaterightlocation->create();
            return $rights_templaterightlocation;
        }
        else
        {
            return $res;
        }
    }

    function retrieve_rights_templates($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(RightsTemplate :: get_table_name(), $condition, $offset, $max_objects, $order_by, RightsTemplate :: CLASS_NAME);
    }

    function retrieve_location($id)
    {
        $condition = new EqualityCondition(Location :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(Location :: get_table_name(), $condition, array(), Location :: CLASS_NAME);
    }

    function retrieve_right($id)
    {
        $condition = new EqualityCondition(Right :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(Right :: get_table_name(), $condition, array(), Right :: CLASS_NAME);
    }

    function retrieve_rights_template($id)
    {
        $condition = new EqualityCondition(RightsTemplate :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(RightsTemplate :: get_table_name(), $condition, array(), RightsTemplate :: CLASS_NAME);
    }

    function retrieve_rights($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(Right :: get_table_name(), $condition, $offset, $max_objects, $order_by, Right :: CLASS_NAME);
    }

    function retrieve_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(Location :: get_table_name(), $condition, $offset, $max_objects, $order_by, Location :: CLASS_NAME);
    }

    function count_locations($condition = null)
    {
        return $this->database->count_objects(Location :: get_table_name(), $condition);
    }

    function count_rights_templates($condition = null)
    {
        return $this->database->count_objects(RightsTemplate :: get_table_name(), $condition);
    }

    function update_location($location)
    {
        $condition = new EqualityCondition(Location :: PROPERTY_ID, $location->get_id());
        return $this->database->update($location, $condition);
    }

    function add_nested_values($location, $previous_visited, $number_of_elements = 1)
    {
        // Update all necessary left-values
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
        $conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->escape_table_name('location') . ' SET ' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . ' + ' . $this->database->quote($number_of_elements * 2);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        $res = $this->database->query($query);
        if (MDB2 :: isError($res))
        {
            return false;
        }

        // Update all necessary right-values
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
        $conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->escape_table_name('location') . ' SET ' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->database->quote($number_of_elements * 2);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        $res = $this->database->query($query);

        if (MDB2 :: isError($res))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function delete_location_nodes($location)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
        $conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $location->get_left_value());
        $conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $location->get_right_value());
        $condition = new AndCondition($conditions);

        return $this->database->delete_objects(Location :: get_table_name(), $condition);
    }

    function delete_nested_values($location)
    {
        $delta = $location->get_right_value() - $location->get_left_value() + 1;

        // Update all necessary nested-values
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
        $conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $location->get_left_value());
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->escape_table_name('location');
        $query .= ' SET ' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . ' - ' . $this->database->quote($delta) . ',';
        $query .= $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->database->quote($delta);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        $res = $this->database->query($query);

        if (MDB2 :: isError($res))
        {
            return false;
        }

        // Update some more nested-values
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
        $conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $location->get_left_value());
        $conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $location->get_right_value());
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->escape_table_name('location');
        $query .= ' SET ' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->database->quote($delta);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        $res = $this->database->query($query);

        if (MDB2 :: isError($res))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function move_location($location, $new_parent_id, $new_previous_id = 0)
    {
        // Check some things first to avoid trouble
        if ($new_previous_id)
        {
            // Don't let people move an element behind itself
            // No need to spawn an error, since we're just not doing anything
            if ($new_previous_id == $location->get_id())
            {
                return true;
            }

            $new_previous = $this->retrieve_location($new_previous_id);
            // TODO: What if location $new_previous_id doesn't exist ? Return error.
            $new_parent_id = $new_previous->get_parent();
        }
        else
        {
            // No parent ID was set ... problem !
            if ($new_parent_id == 0)
            {
                return false;
            }
            // Move the location underneath one of it's children ?
            // I think not ... Return error
            if ($location->is_parent_of($new_parent_id))
            {
                return false;
            }
            // Move an element underneath itself ?
            // No can do ... just ignore and return true
            if ($new_parent_id == $location->get_id())
            {
                return true;
            }
            // Try to retrieve the data of the parent element
            $new_parent = $this->retrieve_location($new_parent_id);
            // TODO: What if this is an invalid location ? Return error.
        }

        $number_of_elements = ($location->get_right_value() - $location->get_left_value() + 1) / 2;
        $previous_visited = $new_previous_id ? $new_previous->get_right_value() : $new_parent->get_left_value();

        // Update the nested values so we can actually add the element
        // Return false if this failed
        if (! $this->add_nested_values($location, $previous_visited, $number_of_elements))
        {
            return false;
        }

        // Now we can update the actual parent_id
        // Return false if this failed
        $location = $this->retrieve_location($location->get_id());
        $location->set_parent($new_parent_id);
        if (! $location->update())
        {
            return false;
        }

        // Update the left/right values of those elements that are being moved


        // First get the offset we need to add to the left/right values
        // if $newPrevId is given we need to get the right value,
        // otherwise the left since the left/right has changed
        // because we already updated it up there. We need to get them again.
        // We have to do that anyway, to have the proper new left/right values
        if ($new_previous_id)
        {
            $temp = $this->retrieve_location($new_previous_id);
            // TODO: What if $temp doesn't exist ? Return error.
            $calculate_width = $temp->get_right_value();
        }
        else
        {
            $temp = $this->retrieve_location($new_parent_id);
            // TODO: What if $temp doesn't exist ? Return error.
            $calculate_width = $temp->get_left_value();
        }

        // Get the element that is being moved again, since the left and
        // right might have changed by the add-call


        $location = $this->retrieve_location($location->get_id());
        // TODO: What if $location doesn't exist ? Return error.


        // Calculate the offset of the element to to the spot where it should go
        // correct the offset by one, since it needs to go inbetween!
        $offset = $calculate_width - $location->get_left_value() + 1;

        // Do the actual update
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
        $conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, ($location->get_left_value() - 1));
        $conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, ($location->get_right_value() + 1));
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->escape_table_name('location');
        $query .= ' SET ' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . ' + ' . $this->database->quote($offset) . ',';
        $query .= $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->database->quote($offset);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

        $res = $this->database->query($query);

        if (MDB2 :: isError($res))
        {
            return false;
        }

        // Remove the subtree where the location was before
        if (! $this->delete_nested_values($location))
        {
            return false;
        }

        return true;
    }

    function update_rights_template($rights_template)
    {
        $condition = new EqualityCondition(RightsTemplate :: PROPERTY_ID, $rights_template->get_id());
        return $this->database->update($rights_template, $condition);
    }

    function delete_rights_template($rights_template)
    {
        // Delete all rights_template_right_locations for that specific rights_template
        $condition = new EqualityCondition(RightsTemplateRightLocation :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template->get_id());
        $this->delete_rights_template_right_locations($condition);

        // Delete all links between this rights_template and users
        // Code comes here ...
        $udm = UserDataManager :: get_instance();

        $condition = new EqualityCondition(UserRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template->get_id());
        $udm->delete_user_rights_templates($condition);

        // Delete all links between this rights_template and groups
        // Code comes here ...
        $gdm = GroupDataManager :: get_instance();

        $condition = new EqualityCondition(GroupRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template->get_id());
        $gdm->delete_group_rights_templates($condition);

        // Delete the actual rights_template

        $condition = new EqualityCondition(RightsTemplate :: PROPERTY_ID, $rights_template->get_id());
        return $this->database->delete(RightsTemplate :: get_table_name(), $condition);
    }

    function delete_locations($condition = null)
    {
        return $this->database->delete_objects(Location :: get_table_name(), $condition);
    }

    function delete_orphaned_rights_template_right_locations()
    {
        $query = 'DELETE FROM ' . $this->escape_table_name('rights_template_right_location') . ' WHERE ';
        $query .= $this->escape_column_name(RightsTemplateRightLocation :: PROPERTY_LOCATION_ID) . ' NOT IN (SELECT ' . $this->escape_column_name(Location :: PROPERTY_ID) . ' FROM ' . $this->escape_table_name('location') . ') OR ';
        $query .= $this->escape_column_name(RightsTemplateRightLocation :: PROPERTY_RIGHTS_TEMPLATE_ID) . ' NOT IN (SELECT ' . $this->escape_column_name(RightsTemplate :: PROPERTY_ID) . ' FROM ' . $this->escape_table_name('rights_template') . ')';

        $res = $this->database->query($query);
        return $res;
    }

    function retrieve_shared_content_objects_for_user($user_id, $rights)
    {
        $subcondition = new EqualityCondition(Location :: PROPERTY_TYPE, 'content_object');
        $conditions[] = new SubSelectcondition('location_id', Location :: PROPERTY_ID, $this->escape_table_name('location'), $subcondition);
        $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new InCondition(UserRightLocation :: PROPERTY_RIGHT_ID, $rights);
        $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_VALUE, 1);
        $condition = new AndCondition($conditions);

        return $this->database->retrieve_objects(UserRightLocation :: get_table_name(), $condition, null, null, array(), UserRightLocation :: CLASS_NAME);
    }

    function retrieve_shared_content_objects_for_groups($group_ids, $rights)
    {
        $subcondition = new EqualityCondition(Location :: PROPERTY_TYPE, 'content_object');
        $conditions[] = new SubSelectcondition('location_id', Location :: PROPERTY_ID, $this->escape_table_name('location'), $subcondition);
        $conditions[] = new InCondition(GroupRightLocation :: PROPERTY_GROUP_ID, $group_ids);
        $conditions[] = new InCondition(GroupRightLocation :: PROPERTY_RIGHT_ID, $rights);
        $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_VALUE, 1);
        $condition = new AndCondition($conditions);

        return $this->database->retrieve_objects(GroupRightLocation :: get_table_name(), $condition, null, null, array(), GroupRightLocation :: CLASS_NAME);
    }

    function create_user_right_location($user_right_location)
    {
        return $this->database->create($user_right_location);
    }

    function create_group_right_location($group_right_location)
    {
        return $this->database->create($group_right_location);
    }

    function delete_user_right_location($user_right_location)
    {
        $condition = new EqualityCondition(UserRightLocation :: PROPERTY_ID, $user_right_location->get_id());
        return $this->database->delete(UserRightLocation :: get_table_name(), $condition);
    }

    function delete_group_right_location($group_right_location)
    {
        $condition = new EqualityCondition(GroupRightLocation :: PROPERTY_ID, $group_right_location->get_id());
        return $this->database->delete(GroupRightLocation :: get_table_name(), $condition);
    }

    function update_user_right_location($user_right_location)
    {
        $condition = new EqualityCondition(UserRightLocation :: PROPERTY_ID, $user_right_location->get_id());
        return $this->database->update($user_right_location, $condition);
    }

    function update_group_right_location($group_right_location)
    {
        $condition = new EqualityCondition(GroupRightLocation :: PROPERTY_ID, $group_right_location->get_id());
        return $this->database->update($group_right_location, $condition);
    }

    function retrieve_user_right_location($right_id, $user_id, $location_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, $right_id);
        $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_LOCATION_ID, $location_id);
        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(UserRightLocation :: get_table_name(), $condition);
    }

    function retrieve_group_right_location($right_id, $group_id, $location_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_RIGHT_ID, $right_id);
        $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_GROUP_ID, $group_id);
        $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_LOCATION_ID, $location_id);
        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(GroupRightLocation :: get_table_name(), $condition);
    }

    function retrieve_user_right_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(UserRightLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_group_right_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(GroupRightLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }
    
    function escape_table_name($table_name)
    {
    	return $this->database->escape_table_name($table_name);
    }
    
	function escape_column_name($column_name)
    {
    	return $this->database->escape_column_name($column_name);
    }
}
?>
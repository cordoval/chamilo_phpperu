<?php
/**
 * $Id: database_webconferencing_data_manager.class.php 230 2009-11-16 09:29:45Z vanpouckesven $
 * @package application.lib.webconferencing.data_manager
 */
require_once dirname(__FILE__) . '/../webconference.class.php';
require_once dirname(__FILE__) . '/../webconference_option.class.php';
require_once dirname(__FILE__) . '/../webconferencing_data_manager_interface.class.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Stefaan Vanbillemont
 */

class DatabaseWebconferencingDataManager extends Database implements WebconferencingDataManagerInterface
{
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('webconferencing_');
    }

    function create_webconference($webconference)
    {
        //return $this->create($webconference);


        $succes = $this->create($webconference);

        foreach ($webconference->get_target_groups() as $group)
        {
            $webconference_group = new WebconferenceGroup();
            $webconference_group->set_webconference($webconference->get_id());
            $webconference_group->set_group_id($group);
            $succes = $webconference_group->create();
        }

        foreach ($webconference->get_target_users() as $user)
        {
            $webconference_user = new WebconferenceUser();
            $webconference_user->set_webconference($webconference->get_id());
            $webconference_user->set_user($user);
            $succes = $webconference_user->create();
        }

        return $succes;
    }

    function update_webconference($webconference)
    {
        $condition = new EqualityCondition(Webconference :: PROPERTY_ID, $webconference->get_id());
        return $this->update($webconference, $condition);
    }

    function delete_webconference($webconference)
    {
        $this->delete_webconference_options($webconference);
        $condition = new EqualityCondition(Webconference :: PROPERTY_ID, $webconference->get_id());
        return $this->delete($webconference->get_table_name(), $condition);
    }

    function count_webconferences($condition = null)
    {
        $webconference_alias = $this->get_alias(Webconference :: get_table_name());
        $webconference_user_alias = $this->get_alias(WebconferenceUser :: get_table_name());
        $webconference_group_alias = $this->get_alias(WebconferenceGroup :: get_table_name());

        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(Webconference :: get_table_name()) . ' AS ' . $webconference_alias;
        $query .= ' LEFT JOIN ' . $this->escape_table_name(WebconferenceUser :: get_table_name()) . ' AS ' . $webconference_user_alias . ' ON ' . $this->escape_column_name(Webconference :: PROPERTY_ID, $webconference_alias) . '  = ' . $this->escape_column_name(WebconferenceUser :: PROPERTY_WEBCONFERENCE, $webconference_user_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(WebconferenceGroup :: get_table_name()) . ' AS ' . $webconference_group_alias . ' ON ' . $this->escape_column_name(Webconference :: PROPERTY_ID, $webconference_alias) . '  = ' . $this->escape_column_name(WebconferenceGroup :: PROPERTY_WEBCONFERENCE, $webconference_group_alias);

        return $this->count_result_set($query, Webconference :: get_table_name(), $condition);
    }

    function retrieve_webconference($id)
    {
        $condition = new EqualityCondition(Webconference :: PROPERTY_ID, $id);
        return $this->retrieve_object(Webconference :: get_table_name(), $condition);
    }

    function retrieve_webconferences($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $webconference_alias = $this->get_alias(Webconference :: get_table_name());
        $webconference_user_alias = $this->get_alias(WebconferenceUser :: get_table_name());
        $webconference_group_alias = $this->get_alias(WebconferenceGroup :: get_table_name());

        $query = 'SELECT ' . $webconference_alias . '.* FROM ' . $this->escape_table_name(Webconference :: get_table_name()) . ' AS ' . $webconference_alias;
        $query .= ' LEFT JOIN ' . $this->escape_table_name(WebconferenceUser :: get_table_name()) . ' AS ' . $webconference_user_alias . ' ON ' . $this->escape_column_name(Webconference :: PROPERTY_ID, $webconference_alias) . '  = ' . $this->escape_column_name(WebconferenceUser :: PROPERTY_WEBCONFERENCE, $webconference_user_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(WebconferenceGroup :: get_table_name()) . ' AS ' . $webconference_group_alias . ' ON ' . $this->escape_column_name(Webconference :: PROPERTY_ID, $webconference_alias) . '  = ' . $this->escape_column_name(WebconferenceGroup :: PROPERTY_WEBCONFERENCE, $webconference_group_alias);

        return $this->retrieve_object_set($query, Webconference :: get_table_name(), $condition, $offset, $max_objects, $order_by, Webconference :: CLASS_NAME);
    }

    function create_webconference_option($webconference_option)
    {
        return $this->create($webconference_option);
    }

    function update_webconference_option($webconference_option)
    {
        $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $webconference_option->get_id());
        return $this->update($webconference_option, $condition);
    }

    function delete_webconference_option($webconference_option)
    {
        $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $webconference_option->get_id());
        return $this->delete($webconference_option->get_table_name(), $condition);
    }

    function delete_webconference_options($webconference)
    {
        $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_CONF_ID, $webconference->get_id());
        return $this->delete(WebconferenceOption :: get_table_name(), $condition);
    }

    function count_webconference_options($condition = null)
    {
        return $this->count_objects(WebconferenceOption :: get_table_name(), $condition);
    }

    function retrieve_webconference_option($id)
    {
        $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $id);
        return $this->retrieve_object(WebconferenceOption :: get_table_name(), $condition);
    }

    function retrieve_webconference_options($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(WebconferenceOption :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_webconference_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(WebconferenceGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, WebconferenceGroup :: CLASS_NAME);
    }

    function retrieve_webconference_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(WebconferenceUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, WebconferenceUser :: CLASS_NAME);
    }

    function create_webconference_user($webconference_user)
    {
        return $this->create($webconference_user);
    }

    function create_webconference_group($webconference_group)
    {
        return $this->create($webconference_group);
    }

}
?>
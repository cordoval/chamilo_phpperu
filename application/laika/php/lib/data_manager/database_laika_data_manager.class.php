<?php
/**
 * $Id: database_laika_data_manager.class.php 230 2009-11-16 09:29:45Z vanpouckesven $
 * @package application.lib.laika.data_manager
 */
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_data_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_question.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_attempt.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_answer.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_scale.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_cluster.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_result.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_calculated_result.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_data_manager_interface.class.php';
/**
 * This is an implementation of a personal calendar datamanager using the PEAR::
 * MDB2 package as a database abstraction layer.
 */
class DatabaseLaikaDatamanager extends Database implements LaikaDatamanagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('laika_');
    }

    function retrieve_laika_questions($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(LaikaQuestion :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaQuestion :: CLASS_NAME);
    }

    function retrieve_laika_question($id)
    {
        $condition = new EqualityCondition(LaikaQuestion :: PROPERTY_ID, $id);
        return $this->retrieve_object(LaikaQuestion :: get_table_name(), $condition, array(), LaikaQuestion :: CLASS_NAME);
    }

    function create_laika_attempt($laika_attempt)
    {
        return $this->create($laika_attempt);
    }

    function create_laika_answer($laika_answer)
    {
        return $this->create($laika_answer);
    }

    function create_laika_scale($laika_scale)
    {
        return $this->create($laika_scale);
    }

    function create_laika_result($laika_result)
    {
        return $this->create($laika_result);
    }

    function create_laika_calculated_result($laika_calculated_result)
    {
        return $this->create($laika_calculated_result);
    }

    function retrieve_laika_scales($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(LaikaScale :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaScale :: CLASS_NAME);
    }

    function retrieve_laika_scale($id)
    {
        $condition = new EqualityCondition(LaikaScale :: PROPERTY_ID, $id);
        return $this->retrieve_object(LaikaScale :: get_table_name(), $condition, array(), LaikaScale :: CLASS_NAME);
    }

    function retrieve_laika_results($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(LaikaResult :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaResult :: CLASS_NAME);
    }

    function retrieve_laika_result($id)
    {
        $condition = new EqualityCondition(LaikaResult :: PROPERTY_ID, $id);
        return $this->retrieve_object(LaikaResult :: get_table_name(), $condition);
    }

    function has_taken_laika($user)
    {
        $condition = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $user->get_id());
        $attempts = $this->count_laika_attempts($condition);
        return $attempts > 0;
    }

    function count_laika_attempts($condition = null)
    {
        return $this->count_objects(LaikaAttempt :: get_table_name(), $condition);
    }

    function count_laika_calculated_results($condition = null)
    {
        return $this->count_objects(LaikaCalculatedResult :: get_table_name(), $condition);
    }

    function count_laika_questions($condition = null)
    {
        return $this->count_objects(LaikaQuestion :: get_table_name(), $condition);
    }

    function retrieve_laika_clusters($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(LaikaCluster :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaCluster :: CLASS_NAME);
    }

    function retrieve_laika_cluster($id)
    {
        $condition = new EqualityCondition(LaikaCluster :: PROPERTY_ID, $id);
        return $this->retrieve_object(LaikaCluster :: get_table_name(), $condition, array(), LaikaCluster :: CLASS_NAME);
    }

    function retrieve_laika_calculated_results($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(LaikaCalculatedResult :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaCalculatedResult :: CLASS_NAME);
    }

    function retrieve_laika_table_calculated_results($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $udm_database = UserDataManager :: get_instance();

        $result_alias = $this->get_alias(LaikaCalculatedResult :: get_table_name());
        $attempt_alias = $this->get_alias(LaikaAttempt :: get_table_name());
        $user_alias = $udm_database->get_alias(User :: get_table_name());

        $query = 'SELECT ' . $result_alias . '.* ';
        $query .= ' FROM ' . $this->escape_table_name(LaikaCalculatedResult :: get_table_name()) . ' AS ' . $result_alias;
        $query .= ' JOIN ' . $this->escape_table_name(LaikaAttempt :: get_table_name()) . ' AS ' . $attempt_alias . ' ON ' . $this->escape_column_name(LaikaCalculatedResult :: PROPERTY_ATTEMPT_ID, $result_alias) . ' = ' . $this->escape_column_name(LaikaAttempt :: PROPERTY_ID, $attempt_alias);
        $query .= ' JOIN ' . $udm_database->escape_table_name(User :: get_table_name()) . ' AS ' . $udm_database->get_alias(User :: get_table_name()) . ' ON ' . $this->escape_column_name(LaikaAttempt :: PROPERTY_USER_ID, $attempt_alias) . ' = ' . $udm_database->escape_column_name(User :: PROPERTY_ID, $user_alias);

        return $this->retrieve_object_set($query, LaikaCalculatedResult :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaCalculatedResult :: CLASS_NAME);
    }

    function count_laika_table_calculated_results($condition = null)
    {
        $udm_database = UserDataManager :: get_instance();

        $result_alias = $this->get_alias(LaikaCalculatedResult :: get_table_name());
        $attempt_alias = $this->get_alias(LaikaAttempt :: get_table_name());
        $user_alias = $udm_database->get_alias(User :: get_table_name());

        $query = 'SELECT COUNT(*)  FROM ' . $this->escape_table_name(LaikaCalculatedResult :: get_table_name()) . ' AS ' . $result_alias;
        $query .= ' JOIN ' . $this->escape_table_name(LaikaAttempt :: get_table_name()) . ' AS ' . $attempt_alias . ' ON ' . $this->escape_column_name(LaikaCalculatedResult :: PROPERTY_ATTEMPT_ID, $result_alias) . ' = ' . $this->escape_column_name(LaikaAttempt :: PROPERTY_ID, $attempt_alias);
        $query .= ' JOIN ' . $udm_database->escape_table_name(User :: get_table_name()) . ' AS ' . $user_alias . ' ON ' . $this->escape_column_name(LaikaAttempt :: PROPERTY_USER_ID, $attempt_alias) . ' = ' . $udm_database->escape_column_name(User :: PROPERTY_ID, $user_alias);

        return $this->count_result_set($query, LaikaCalculatedResult :: get_table_name(), $condition);
    }

    function retrieve_laika_calculated_result($id)
    {
        $condition = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_ID, $id);
        return $this->retrieve_object(LaikaCalculatedResult :: get_table_name(), $condition, array(), LaikaCalculatedResult :: CLASS_NAME);
    }

    function retrieve_laika_answers($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(LaikaAnswer :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaAnswer :: CLASS_NAME);
    }

    function retrieve_laika_answer($id)
    {
        $condition = new EqualityCondition(LaikaAnswer :: PROPERTY_ID, $id);
        return $this->retrieve_object(LaikaAnswer :: get_table_name(), $condition, array(), LaikaAnswer :: CLASS_NAME);
    }

    function retrieve_laika_attempts($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(LaikaAttempt :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaAttempt :: CLASS_NAME);
    }

    function retrieve_laika_attempt($id)
    {
        $condition = new EqualityCondition(LaikaAttempt :: PROPERTY_ID, $id);
        return $this->retrieve_object(LaikaAttempt :: get_table_name(), $condition, array(), LaikaAttempt :: CLASS_NAME);
    }

    function retrieve_percentile_codes($condition = null)
    {
        return $this->retrieve_distinct(LaikaResult :: get_table_name(), LaikaResult :: PROPERTY_PERCENTILE_CODE, $condition);
    }

    function retrieve_statistical_attempts($users = array(), $type = SORT_ASC)
    {
        $query = 'SELECT ';

        switch ($type)
        {
            case SORT_ASC :
                $query .= 'MIN(' . $this->escape_column_name(LaikaAttempt :: PROPERTY_ID) . ')';
                break;
            case SORT_DESC :
                $query .= 'MAX(' . $this->escape_column_name(LaikaAttempt :: PROPERTY_ID) . ')';
                break;
        }

        $query .= ' as id, user_id FROM ' . $this->escape_table_name(LaikaAttempt :: get_table_name()) . ' GROUP BY ' . $this->escape_column_name(LaikaAttempt :: PROPERTY_USER_ID);

        if (count($users) > 0)
        {
            $query .= 'HAVING ' . $this->escape_column_name(LaikaAttempt :: PROPERTY_USER_ID) . ' IN (';

            //			$values = $condition->get_values();
            $placeholders = array();
            foreach ($users as $user)
            {
                $placeholders[] = $this->quote($user);
            }

            $query .= implode(',', $placeholders) . ')';
        }

        $res = $this->query($query);
        return new ObjectResultSet($this, $res, LaikaAttempt :: CLASS_NAME);
    }

    function count_laika_users($condition = null)
    {
        return $this->count_distinct(LaikaAttempt :: get_table_name(), LaikaAttempt :: PROPERTY_USER_ID, $condition);
    }

    function retrieve_distinct_laika_users($condition = null)
    {
        return $this->retrieve_distinct(LaikaAttempt :: get_table_name(), LaikaAttempt :: PROPERTY_USER_ID, $condition);
    }

    function retrieve_laika_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $udm = UserDataManager :: get_instance();
        $users = $this->retrieve_distinct_laika_users($condition);

        if (count($users) > 0)
        {
            $users_condition = new InCondition(User :: PROPERTY_ID, $users);
        }
        else
        {
            $users_condition = new InCondition(User :: PROPERTY_ID, array(0));
        }

        return $udm->retrieve_users($users_condition, $offset, $count, $order_property);
    }
}
?>
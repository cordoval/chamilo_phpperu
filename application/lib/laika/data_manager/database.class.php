<?php
/**
 * $Id: database.class.php 230 2009-11-16 09:29:45Z vanpouckesven $
 * @package application.lib.laika.data_manager
 */
require_once dirname(__FILE__) . '/../laika_data_manager.class.php';
require_once dirname(__FILE__) . '/../laika_question.class.php';
require_once dirname(__FILE__) . '/../laika_attempt.class.php';
require_once dirname(__FILE__) . '/../laika_answer.class.php';
require_once dirname(__FILE__) . '/../laika_scale.class.php';
require_once dirname(__FILE__) . '/../laika_cluster.class.php';
require_once dirname(__FILE__) . '/../laika_result.class.php';
require_once dirname(__FILE__) . '/../laika_calculated_result.class.php';
require_once 'MDB2.php';
/**
 * This is an implementation of a personal calendar datamanager using the PEAR::
 * MDB2 package as a database abstraction layer.
 */
class DatabaseLaikaDatamanager extends LaikaDatamanager
{
    const ALIAS_LAIKA_QUESTION_TABLE = 'lq';
    const ALIAS_LAIKA_SCALE_TABLE = 'ls';
    const ALIAS_LAIKA_RESULT_TABLE = 'lr';
    const ALIAS_LAIKA_CLUSTER_TABLE = 'lc';
    const ALIAS_LAIKA_CALCULATED_RESULT_TABLE = 'lcr';
    const ALIAS_LAIKA_ANSWER_TABLE = 'lan';
    const ALIAS_LAIKA_ATTEMPT_TABLE = 'lat';

    private $database;

    function initialize()
    {
        $this->database = new Database(array(LaikaQuestion :: get_table_name() => self :: ALIAS_LAIKA_QUESTION_TABLE, LaikaScale :: get_table_name() => self :: ALIAS_LAIKA_SCALE_TABLE, LaikaResult :: get_table_name() => self :: ALIAS_LAIKA_RESULT_TABLE, LaikaCalculatedResult :: get_table_name() => self :: ALIAS_LAIKA_CALCULATED_RESULT_TABLE, LaikaAnswer :: get_table_name() => self :: ALIAS_LAIKA_ANSWER_TABLE, LaikaAttempt :: get_table_name() => self :: ALIAS_LAIKA_ATTEMPT_TABLE, LaikaCluster :: get_table_name() => self :: ALIAS_LAIKA_CLUSTER_TABLE));
        $this->database->set_prefix('laika_');
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
        $condition = new EqualityCondition('id', $object_id);
        return false;
    }

    public function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_ids);
        return false;
    }

    public function is_date_column($var)
    {
        return $this->database->is_date_column($var);
    }

    public function escape_column_name($name, $prefix_properties = null)
    {
        return $this->database->escape_column_name($name, $prefix_properties);
    }

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return array();
    }

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    public function get_content_object_publication_attribute($publication_id)
    {
        $info = new ContentObjectPublicationAttributes();
        return $info;
    }

    /**
     * @see Application::count_publication_attributes()
     */
    public function count_publication_attributes($type = null, $condition = null)
    {
        return 0;
    }

    /**
     * @see Application::delete_content_object_publications()
     */
    public function delete_content_object_publications($object_id)
    {
    }

    /**
     * @see Application::update_content_object_publication_id()
     */
    function update_content_object_publication_id($publication_attr)
    {
        return true;
    }

    function retrieve_laika_questions($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(LaikaQuestion :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaQuestion :: CLASS_NAME);
    }

    function retrieve_laika_question($id)
    {
        $condition = new EqualityCondition(LaikaQuestion :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(LaikaQuestion :: get_table_name(), $condition, array(), LaikaQuestion :: CLASS_NAME);
    }

    function create_laika_attempt($laika_attempt)
    {
        return $this->database->create($laika_attempt);
    }

    function create_laika_answer($laika_answer)
    {
        return $this->database->create($laika_answer);
    }

    function create_laika_scale($laika_scale)
    {
        return $this->database->create($laika_scale);
    }

    function create_laika_result($laika_result)
    {
        return $this->database->create($laika_result);
    }

    function create_laika_calculated_result($laika_calculated_result)
    {
        return $this->database->create($laika_calculated_result);
    }

    function retrieve_laika_scales($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(LaikaScale :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaScale :: CLASS_NAME);
    }

    function retrieve_laika_scale($id)
    {
        $condition = new EqualityCondition(LaikaScale :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(LaikaScale :: get_table_name(), $condition, array(), LaikaScale :: CLASS_NAME);
    }

    function retrieve_laika_results($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(LaikaResult :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaResult :: CLASS_NAME);
    }

    function retrieve_laika_result($id)
    {
        $condition = new EqualityCondition(LaikaResult :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(LaikaResult :: get_table_name(), $condition);
    }

    function has_taken_laika($user)
    {
        $condition = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $user->get_id());
        $attempts = $this->count_laika_attempts($condition);
        return $attempts > 0;
    }

    function count_laika_attempts($condition = null)
    {
        return $this->database->count_objects(LaikaAttempt :: get_table_name(), $condition);
    }

    function count_laika_calculated_results($condition = null)
    {
        return $this->database->count_objects(LaikaCalculatedResult :: get_table_name(), $condition);
    }

    function count_laika_questions($condition = null)
    {
        return $this->database->count_objects(LaikaQuestion :: get_table_name(), $condition);
    }

    function retrieve_laika_clusters($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(LaikaCluster :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaCluster :: CLASS_NAME);
    }

    function retrieve_laika_cluster($id)
    {
        $condition = new EqualityCondition(LaikaCluster :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(LaikaCluster :: get_table_name(), $condition, array(), LaikaCluster :: CLASS_NAME);
    }

    function retrieve_laika_calculated_results($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(LaikaCalculatedResult :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaCalculatedResult :: CLASS_NAME);
    }

    function retrieve_laika_table_calculated_results($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $udm_database = UserDataManager :: get_instance()->get_database();
        $database = $this->database;

        $result_alias = $this->database->get_alias(LaikaCalculatedResult :: get_table_name());
        $attempt_alias = $this->database->get_alias(LaikaAttempt :: get_table_name());
        $user_alias = $udm_database->get_alias(User :: get_table_name());

        $query = 'SELECT ' . $result_alias . '.* ';
        $query .= ' FROM ' . $database->escape_table_name(LaikaCalculatedResult :: get_table_name()) . ' AS ' . $result_alias;
        $query .= ' JOIN ' . $database->escape_table_name(LaikaAttempt :: get_table_name()) . ' AS ' . $attempt_alias . ' ON ' . $database->escape_column_name(LaikaCalculatedResult :: PROPERTY_ATTEMPT_ID, $result_alias) . ' = ' . $database->escape_column_name(LaikaAttempt :: PROPERTY_ID, $attempt_alias);
        $query .= ' JOIN ' . $udm_database->escape_table_name(User :: get_table_name()) . ' AS ' . $udm_database->get_alias(User :: get_table_name()) . ' ON ' . $database->escape_column_name(LaikaAttempt :: PROPERTY_USER_ID, $attempt_alias) . ' = ' . $udm_database->escape_column_name(User :: PROPERTY_ID, $user_alias);

        return $this->database->retrieve_object_set($query, LaikaCalculatedResult :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaCalculatedResult :: CLASS_NAME);
    }

    function count_laika_table_calculated_results($condition = null)
    {
        $udm_database = UserDataManager :: get_instance()->get_database();
        $database = $this->database;

        $result_alias = $this->database->get_alias(LaikaCalculatedResult :: get_table_name());
        $attempt_alias = $this->database->get_alias(LaikaAttempt :: get_table_name());
        $user_alias = $udm_database->get_alias(User :: get_table_name());

        $query = 'SELECT COUNT(*)  FROM ' . $database->escape_table_name(LaikaCalculatedResult :: get_table_name()) . ' AS ' . $result_alias;
        $query .= ' JOIN ' . $database->escape_table_name(LaikaAttempt :: get_table_name()) . ' AS ' . $attempt_alias . ' ON ' . $database->escape_column_name(LaikaCalculatedResult :: PROPERTY_ATTEMPT_ID, $result_alias) . ' = ' . $database->escape_column_name(LaikaAttempt :: PROPERTY_ID, $attempt_alias);
        $query .= ' JOIN ' . $udm_database->escape_table_name(User :: get_table_name()) . ' AS ' . $user_alias . ' ON ' . $database->escape_column_name(LaikaAttempt :: PROPERTY_USER_ID, $attempt_alias) . ' = ' . $udm_database->escape_column_name(User :: PROPERTY_ID, $user_alias);

        return $database->count_result_set($query, LaikaCalculatedResult :: get_table_name(), $condition);
    }

    function retrieve_laika_calculated_result($id)
    {
        $condition = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(LaikaCalculatedResult :: get_table_name(), $condition, array(), LaikaCalculatedResult :: CLASS_NAME);
    }

    function retrieve_laika_answers($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(LaikaAnswer :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaAnswer :: CLASS_NAME);
    }

    function retrieve_laika_answer($id)
    {
        $condition = new EqualityCondition(LaikaAnswer :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(LaikaAnswer :: get_table_name(), $condition, array(), LaikaAnswer :: CLASS_NAME);
    }

    function retrieve_laika_attempts($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(LaikaAttempt :: get_table_name(), $condition, $offset, $max_objects, $order_by, LaikaAttempt :: CLASS_NAME);
    }

    function retrieve_laika_attempt($id)
    {
        $condition = new EqualityCondition(LaikaAttempt :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(LaikaAttempt :: get_table_name(), $condition, array(), LaikaAttempt :: CLASS_NAME);
    }

    function retrieve_percentile_codes($condition = null)
    {
        return $this->database->retrieve_distinct(LaikaResult :: get_table_name(), LaikaResult :: PROPERTY_PERCENTILE_CODE, $condition);
    }

    function retrieve_statistical_attempts($users = array(), $type = SORT_ASC)
    {
        $query = 'SELECT ';

        switch ($type)
        {
            case SORT_ASC :
                $query .= 'MIN(' . $this->database->escape_column_name(LaikaAttempt :: PROPERTY_ID) . ')';
                break;
            case SORT_DESC :
                $query .= 'MAX(' . $this->database->escape_column_name(LaikaAttempt :: PROPERTY_ID) . ')';
                break;
        }

        $query .= ' as id, user_id FROM ' . $this->database->escape_table_name(LaikaAttempt :: get_table_name()) . ' GROUP BY ' . $this->database->escape_column_name(LaikaAttempt :: PROPERTY_USER_ID);

        if (count($users) > 0)
        {
            $query .= 'HAVING ' . $this->database->escape_column_name(LaikaAttempt :: PROPERTY_USER_ID) . ' IN (';

            //			$values = $condition->get_values();
            $placeholders = array();
            foreach ($users as $user)
            {
                $placeholders[] = $this->quote($user);
            }

            $query .= implode(',', $placeholders) . ')';
        }

        $res = $this->query($query);
        return new ObjectResultSet($this->database, $res, LaikaAttempt :: CLASS_NAME);
    }

	function quote($value)
    {
    	return $this->database->quote($value);
    }

    function query($query)
    {
    	return $this->database->query($query);
    }

    function count_laika_users($condition = null)
    {
        return $this->database->count_distinct(LaikaAttempt :: get_table_name(), LaikaAttempt :: PROPERTY_USER_ID, $condition);
    }

    function retrieve_distinct_laika_users($condition = null)
    {
        return $this->database->retrieve_distinct(LaikaAttempt :: get_table_name(), LaikaAttempt :: PROPERTY_USER_ID, $condition);
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
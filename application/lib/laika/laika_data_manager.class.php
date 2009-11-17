<?php
/**
 * $Id: laika_data_manager.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

abstract class LaikaDataManager
{
    /**
     * Instance of the class, for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor. Initializes the data manager.
     */
    protected function LaikaDataManager()
    {
        $this->initialize();
    }

    /**
     * Creates the shared instance of the configured data manager if
     * necessary and returns it. Uses a factory pattern.
     * @return PersonalCalendarDataManager The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'LaikaDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Initializes the data manager.
     */
    abstract function initialize();

    /**
     * Creates a storage unit in the personal calendar storage system
     * @param string $name
     * @param array $properties
     * @param array $indexes
     */
    abstract function create_storage_unit($name, $properties, $indexes);

    /**
     * @see Application::content_object_is_published()
     */
    abstract function content_object_is_published($object_id);

    /**
     * @see Application::any_content_object_is_published()
     */
    abstract function any_content_object_is_published($object_ids);

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    abstract function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null);

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    abstract function get_content_object_publication_attribute($publication_id);

    /**
     * @see Application::count_publication_attributes()
     */
    abstract function count_publication_attributes($type = null, $condition = null);

    /**
     * @see Application::delete_content_object_publications()
     */
    abstract function delete_content_object_publications($object_id);

    /**
     * @see Application::update_content_object_publication_id()
     */
    abstract function update_content_object_publication_id($publication_attr);

    abstract function retrieve_laika_question($id);

    abstract function retrieve_laika_questions($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_laika_attempt($laika_attempt);

    abstract function create_laika_answer($laika_answer);

    abstract function create_laika_scale($laika_scale);

    abstract function create_laika_result($laika_result);

    abstract function create_laika_calculated_result($laika_calculated_result);

    abstract function get_next_laika_attempt_id();

    abstract function get_next_laika_answer_id();

    abstract function get_next_laika_scale_id();

    abstract function get_next_laika_result_id();

    abstract function get_next_laika_calculated_result_id();

    abstract function retrieve_laika_scale($id);

    abstract function retrieve_laika_scales($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_laika_result($id);

    abstract function retrieve_laika_results($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function has_taken_laika($user);

    abstract function count_laika_attempts($condition = null);

    abstract function count_laika_questions($condition = null);

    abstract function count_laika_calculated_results($condition = null);

    abstract function retrieve_laika_cluster($id);

    abstract function retrieve_laika_clusters($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_laika_calculated_result($id);

    abstract function retrieve_laika_calculated_results($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_laika_attempt($id);

    abstract function retrieve_laika_attempts($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_laika_answer($id);

    abstract function retrieve_laika_answers($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_percentile_codes($condition = null);

    abstract function retrieve_statistical_attempts($users = array(), $attempt = SORT_ASC);

    abstract function retrieve_laika_users($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function count_laika_users($condition = null);
}
?>
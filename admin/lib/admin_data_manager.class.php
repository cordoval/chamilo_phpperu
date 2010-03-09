<?php
/**
 * $Id: admin_data_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

abstract class AdminDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function AdminDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return AdminDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'AdminDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function create_language($language);

    abstract function create_registration($registration);

    abstract function create_setting($setting);

    abstract function create_system_announcement_publication($system_announcement_publication);

    abstract function retrieve_languages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function count_settings($condition = null);

    abstract function retrieve_settings($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function retrieve_remote_packages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function retrieve_registration($id);

    abstract function retrieve_remote_package($id);

    abstract function retrieve_registrations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function count_remote_packages($condition = null);

    abstract function count_registrations($condition = null);

    abstract function retrieve_setting_from_variable_name($variable, $application = 'admin');

    abstract function retrieve_language_from_english_name($english_name);

    abstract function retrieve_feedback_publications($pid, $cid, $application);

    abstract function retrieve_feedback_publication($id);

    //abstract function retrieve_validations($pid,$cid,$application);


    abstract function retrieve_validation($id);

    abstract function update_setting($setting);

    abstract function update_registration($registration);

    abstract function update_system_announcement_publication($system_announcement_publication);

    abstract function delete_registration($registration);

    abstract function delete_setting($setting);

    abstract function delete_system_announcement_publication($system_announcement_publication);

    function get_languages()
    {
        $options = array();

        $languages = $this->retrieve_languages();
        while ($language = $languages->next_result())
        {
            $options[$language->get_folder()] = $language->get_original_name();
        }

        return $options;
    }

    /**
     * Count the system announcements
     * @param Condition $condition
     * @return int
     */
    abstract function count_system_announcement_publications($condition = null);

    /**
     * Retrieve a system announcement
     * @param int $id
     * @return SystemAnnouncementPublication
     */
    abstract function retrieve_system_announcement_publication($id);

    /**
     * Retrieve a series of system announcements
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return SystemAnnouncementPublicationResultSet
     */
    abstract function retrieve_system_announcement_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function retrieve_system_announcement_publication_target_groups($system_announcement_publication);

    abstract function retrieve_system_announcement_publication_target_users($system_announcement_publication);

    abstract function select_next_display_order($parent_category_id);

    abstract function delete_category($category);

    abstract function update_category($category);

    abstract function create_category($category);

    abstract function count_categories($conditions = null);

    abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null);

    abstract function get_content_object_publication_attribute($publication_id);

    abstract function any_content_object_is_published($object_ids);

    abstract function count_publication_attributes($user = null, $object_id = null, $condition = null);

    abstract function delete_content_object_publications($object_id);

    abstract function delete_settings($condition = null);

    abstract function delete_validation($validation);

    abstract function update_validation($validation);

    abstract function create_validation($validation);

    abstract function delete_feedback_publication($feeback_publication);

    abstract function update_feedback_publication($feedback_publication);

    abstract function create_feedback_publication($feedback_publication);

    abstract function retrieve_validations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function count_validations($condition = null);
    
    // Dynamic Forms
    
    abstract function delete_dynamic_form($dynamic_form);

    abstract function update_dynamic_form($dynamic_form);

    abstract function create_dynamic_form($dynamic_form);

    abstract function count_dynamic_forms($conditions = null);

    abstract function retrieve_dynamic_forms($condition = null, $offset = null, $count = null, $order_property = null);
    
    
    abstract function delete_dynamic_form_element($dynamic_form_element);

    abstract function update_dynamic_form_element($dynamic_form_element);

    abstract function create_dynamic_form_element($dynamic_form_element);

    abstract function count_dynamic_form_elements($conditions = null);

    abstract function retrieve_dynamic_form_elements($condition = null, $offset = null, $count = null, $order_property = null);
    
    abstract function select_next_dynamic_form_element_order($dynamic_form_id);
    
    
    abstract function delete_dynamic_form_element_option($dynamic_form_element_option);

    abstract function update_dynamic_form_element_option($dynamic_form_element_option);

    abstract function create_dynamic_form_element_option($dynamic_form_element_option);

    abstract function count_dynamic_form_element_options($conditions = null);

    abstract function retrieve_dynamic_form_element_options($condition = null, $offset = null, $count = null, $order_property = null);
    
    abstract function select_next_dynamic_form_element_option_order($dynamic_form_element_id);
    
    abstract function delete_all_options_from_form_element($dynamic_form_element_id);
    
    
    abstract function delete_dynamic_form_element_value($dynamic_form_element_value);

    abstract function update_dynamic_form_element_value($dynamic_form_element_value);

    abstract function create_dynamic_form_element_value($dynamic_form_element_value);

    abstract function count_dynamic_form_element_values($conditions = null);

    abstract function retrieve_dynamic_form_element_values($condition = null, $offset = null, $count = null, $order_property = null);
    
    abstract function delete_dynamic_form_element_values_from_form($dynamic_form_id);
    
    function is_registered($name, $type = Registration :: TYPE_APPLICATION)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $name);
    	$conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, $type);
    	$condition = new AndCondition($conditions);
    	
    	return ($this->count_registrations($condition) > 0);
    }
    
}
?>
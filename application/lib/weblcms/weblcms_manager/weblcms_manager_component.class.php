<?php
/**
 * $Id: weblcms_manager_component.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager
 */

abstract class WeblcmsManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param WeblcmsManagr $weblcms The weblcms manager which provides this component
     */
    protected function WeblcmsManagerComponent($weblcms)
    {
        parent :: __construct($weblcms);
    }

    /**
     * @see WeblcmsManager :: count_courses()
     */
    function count_courses($conditions = null)
    {
        return $this->get_parent()->count_courses($conditions);
    }
    
	function retrieve_request($id)
	{
		return $this->get_parent()->retrieve_request($id);
	}

    function get_course_type_deleting_all_courses_url($course_type)
    {
    	return $this->get_parent()->get_course_type_deleting_all_courses_url($course_type);
    }
    
    function get_course_request_deleting_url($request)
    {
    	return $this->get_parent()->get_course_request_deleting_url($request);
    }
    
    function get_move_course_url($course)
    {
        return $this->get_parent()->get_move_course_url($course);
    }
    
	function count_course_types($conditions = null)
    {
        return $this->get_parent()->count_course_types($conditions);
    }
    
    function count_requests($conditions = null)
    {
    	return $this->get_parent()->count_requests($conditions);
    }

    /**
     * @see WeblcmsManager :: count_course_categories()
     */
    function count_course_categories($conditions = null)
    {
        return $this->get_parent()->count_course_categories($conditions);
    }

    /**
     * @see WeblcmsManager :: count_user_courses()
     */
    function count_user_courses($conditions = null)
    {
        return $this->get_parent()->count_user_courses($conditions);
    }

    /**
     * @see WeblcmsManager :: count_course_user_categories()
     */
    function count_course_user_categories($conditions = null)
    {
        return $this->get_parent()->count_course_user_categories($conditions);
    }

    /**
     * @see WeblcmsManager :: get_tool_id()
     */
    function get_tool_id()
    {
        return $this->get_parent()->get_tool_id();
    }

    /**
     * @see WeblcmsManager ::  get_user_info()
     */
    function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    /**
     * @see WeblcmsManager :: get_course()
     */
    function get_course()
    {
        return $this->get_parent()->get_course();
    }

    /**
     * @see WeblcmsManager :: get_course_id()
     */
    function get_course_id()
    {
        return $this->get_parent()->get_course_id();
    }

    /**
     * @see WeblcmsManager :: get_course_groups()
     */
    function get_course_groups()
    {
        return $this->get_parent()->get_course_groups();
    }

    /**
     * @see WeblcmsManager :: get_course_type()
     */
    function get_course_type()
    {
        return $this->get_parent()->get_course_type();
    }

    /**
     * @see WeblcmsManager :: get_categories()
     */
    function get_categories($list = false)
    {
        return $this->get_parent()->get_categories($list);
    }

    /**
     * @see WeblcmsManager :: get_category()
     */
    function get_category($id)
    {
        return $this->get_parent()->get_category($id);
    }

    /**
     * @see WeblcmsManager :: set_tool_class()
     */
    function set_tool_class($class)
    {
        return $this->get_parent()->set_tool_class($class);
    }

    /**
     * @see WeblcmsManager :: get_registered_tools()
     */
    function get_registered_tools()
    {
        return $this->get_parent()->get_registered_tools();
    }

    /**
     * @see WeblcmsManager :: get_registered_sections()
     */
    function get_registered_sections()
    {
        return $this->get_parent()->get_registered_sections();
    }

    /**
     * @see WeblcmsManager :: get_registered_tools()
     */
    function get_tool_properties($module)
    {
        return $this->get_parent()->get_tool_properties($module);
    }

    /**
     * @see WeblcmsManager :: load_course()
     */
    function load_course()
    {
        return $this->get_parent()->load_course();
    }

    /**
     * @see WeblcmsManager :: load_tools()
     */
    function load_tools()
    {
        return $this->get_parent()->load_tools();
    }

    /**
     * @see WeblcmsManager :: get_all_tools()
     */
    function get_all_non_admin_tools()
    {
    	return $this->get_parent()->get_all_non_admin_tools();
    }

    /**
     * @see WeblcmsManager :: is_tool_name()
     */
    static function is_tool_name($name)
    {
        return $this->get_parent()->is_tool_name($name);
    }

    /**
     * @see WeblcmsManager :: retrieve_max_sort_value()
     */
    function retrieve_max_sort_value($table, $column, $condition = null)
    {
        return $this->get_parent()->retrieve_max_sort_value($table, $column, $condition);
    }

    /**
     * @see WeblcmsManager :: content_object_is_published()
     */
    function content_object_is_published($object_id)
    {
        return $this->get_parent()->content_object_is_published($object_id);
    }

    /**
     * @see WeblcmsManager :: any_content_object_is_published()
     */
    function any_content_object_is_published($object_ids)
    {
        return $this->get_parent()->any_content_object_is_published($object_ids);
    }

    /**
     * @see WeblcmsManager :: get_content_object_publication_attributes()
     */
    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    /**
     * @see WeblcmsManager :: get_content_object_publication_attribute()
     */
    function get_content_object_publication_attribute($publication_id)
    {
        return $this->get_parent()->get_content_object_publication_attribute($publication_id);
    }

    /**
     * @see WeblcmsManager :: delete_content_object_publications()
     */
    function delete_content_object_publications($object_id)
    {
        return $this->get_parent()->delete_content_object_publications($object_id);
    }

    /**
     * @see WeblcmsManager :: update_content_object_publication_id()
     */
    function update_content_object_publication_id($publication_attr)
    {
        return $this->get_parent()->update_content_object_publication_id($publication_attr);
    }

    /**
     * @see WeblcmsManager :: count_publication_attributes()
     */
    function count_publication_attributes($type = null, $condition = null)
    {
        return $this->get_parent()->count_publication_attributes($type, $condition);
    }

    /**
     * @see WeblcmsManager :: retrieve_course_categories()
     */
    function retrieve_course_categories($conditions = null, $offset = null, $count = null, $order_by = null)
    {
        return $this->get_parent()->retrieve_course_categories($conditions, $offset, $count, $order_by);
    }

    /**
     * @see WeblcmsManager :: retrieve_course_user_categories()
     */
    function retrieve_course_user_categories($conditions = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_course_user_categories($conditions, $offset, $count, $order_property);
    }

    /**
     * @see WeblcmsManager :: retrieve_course_user_category()
     */
    function retrieve_course_user_category($condition = null)
    {
        return $this->get_parent()->retrieve_course_user_category($condition);
    }

    /**
     * @see WeblcmsManager :: retrieve_course_user_category_at_sort()
     */
    function retrieve_course_user_category_at_sort($user_id, $sort, $direction)
    {
        return $this->get_parent()->retrieve_course_user_category_at_sort($user_id, $sort, $direction);
    }

    /**
     * @see WeblcmsManager :: retrieve_course()
     */
    function retrieve_course($course_code)
    {
        return $this->get_parent()->retrieve_course($course_code);
    }

    function retrieve_course_type($course_type_id)
    {
        return $this->get_parent()->retrieve_course_type($course_type_id);
    }
    
   /**
     * Retrieves the change active url
     * @see TrackingManager :: get_change_active_url;
     */
    function get_change_active_url($type, $course_type_id)
    {
        return $this->get_parent()->get_change_active_url($type, $course_type_id);
    }

    /**
     * @see WeblcmsManager :: retrieve_course_category()
     */
    function retrieve_course_category($course_category)
    {
        return $this->get_parent()->retrieve_course_category($course_category);
    }

	function retrieve_course_types($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_course_types($condition, $offset, $count, $order_property);
    }
    
    function retrieve_requests($condition = null, $offset = null, $count = null, $order_property = null)
    {
    	return $this->get_parent()->retrieve_requests($condition, $offset, $count, $order_property);
    }
    
    function retrieve_active_course_types()
    {
    	return $this->get_parent()->retrieve_active_course_types();
    }
    
    function count_active_course_types()
    {
    	return $this->get_parent()->count_active_course_types();
    }

    /**
     * @see WeblcmsManager :: retrieve_course_user_relation()
     */
    function retrieve_course_user_relation($course_code, $user_id)
    {
        return $this->get_parent()->retrieve_course_user_relation($course_code, $user_id);
    }

    function retrieve_course_type_user_relation($id, $user_id)
    {
    	return $this->get_parent()->retrieve_course_type_user_relation($id, $user_id);
    }

    /**
     * @see WeblcmsManager :: retrieve_course_user_relation_at_sort()
     */
    function retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction)
    {
        return $this->get_parent()->retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction);
    }

    /**
     * @see WeblcmsManager :: retrieve_course_user_relations()
     */
    function retrieve_course_user_relations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_course_user_relations($condition, $offset, $count, $order_property);
    }

    /**
     * @see WeblcmsManager :: retrieve_courses()
     */
    function retrieve_courses($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_courses($condition, $offset, $count, $order_property);
    }

    /**
     * @see WeblcmsManager :: retrieve_user_courses()
     */
    function retrieve_user_courses($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_user_courses($condition, $offset, $count, $order_property);
    }

    /**
     * @see WeblcmsManager :: get_last_visit_date()
     */
    function get_last_visit_date($tool = null, $category_id = null)
    {
        return $this->get_parent()->get_last_visit_date($tool, $category_id);
    }

    /**
     * @see WeblcmsManager :: tool_has_new_publications()
     */
    function tool_has_new_publications($tool)
    {
        return $this->get_parent()->tool_has_new_publications($tool);
    }

    /**
     * @see WeblcmsManager ::  get_course_viewing_url()
     */
    function get_course_viewing_url($course)
    {
        return $this->get_parent()->get_course_viewing_url($course);
    }

    /**
     * @see WeblcmsManager :: get_course_editing_url()
     */
    function get_course_editing_url($course)
    {
        return $this->get_parent()->get_course_editing_url($course);
    }

    function get_course_request_editing_url($request)
    {
    	return $this->get_parent()->get_course_request_editing_url($request);
    }
    /**
     * @see WeblcmsManager :: get_course_maintenance_url()
     */
    function get_course_maintenance_url($course)
    {
        return $this->get_parent()->get_course_maintenance_url($course);
    }

    /**
     * @see WeblcmsManager :: get_course_subscription_url()
     */
    function get_course_subscription_url($course)
    {
        return $this->get_parent()->get_course_subscription_url($course);
    }
    
    function get_course_request_form_url($course)
    {
    	return $this->get_parent()->get_course_request_form_url($course);
    }
    
	function get_course_type_deleting_url($course_type)
    {
    	return $this->get_parent()->get_course_type_deleting_url($course_type);
    }

	function get_course_type_editing_url($course_type)
    {
    	return $this->get_parent()->get_course_type_editing_url($course_type);
    }
    
    function get_course_type_maintenance_url($course_type)
    {
    	return $this->get_parent()->get_course_type_maintenance_url($course_type);
    }

    function get_course_type_subscription_url($course_type)
    {
    	return $this->get_parent()->get_course_type_subscription($course_type);
    }

    function get_course_type_viewing_url($course_type)
    {
    	return $this->get_parent()->get_course_type_viewing_url($course_type);
    }

    /**
     * @see WeblcmsManager :: get_course_unsubscription_url()
     */
    function get_course_unsubscription_url($course)
    {
        return $this->get_parent()->get_course_unsubscription_url($course);
    }

    /**
     * @see WeblcmsManager :: get_course_user_category_edit_url()
     */
    function get_course_user_category_edit_url($course_user_category)
    {
        return $this->get_parent()->get_course_user_category_edit_url($course_user_category);
    }

    /**
     * @see WeblcmsManager :: get_course_user_category_move_url()
     */
    function get_course_user_category_move_url($course_user_category, $direction)
    {
        return $this->get_parent()->get_course_user_category_move_url($course_user_category, $direction);
    }

    /**
     * @see WeblcmsManager :: get_course_user_edit_url()
     */
    function get_course_user_edit_url($course_user)
    {
        return $this->get_parent()->get_course_user_edit_url($course_user);
    }

    /**
     * @see WeblcmsManager :: get_course_user_move_url()
     */
    function get_course_user_move_url($course_user, $direction)
    {
        return $this->get_parent()->get_course_user_move_url($course_user, $direction);
    }

    /**
     * @see WeblcmsManager :: get_course_user_category_add_url()
     */
    function get_course_user_category_add_url()
    {
        return $this->get_parent()->get_course_user_category_add_url();
    }

    /**
     * @see WeblcmsManager :: get_course_user_category_delete_url()
     */
    function get_course_user_category_delete_url($course_user_category)
    {
        return $this->get_parent()->get_course_user_category_delete_url($course_user_category);
    }

    /**
     * @see WeblcmsManager :: get_course_category_edit_url()
     */
    function get_course_category_edit_url($coursecategory)
    {
        return $this->get_parent()->get_course_category_edit_url($coursecategory);
    }

    /**
     * @see WeblcmsManager :: get_course_category_add_url()
     */
    function get_course_category_add_url()
    {
        return $this->get_parent()->get_course_category_add_url();
    }

    /**
     * @see WeblcmsManager :: get_course_category_delete_url()
     */
    function get_course_category_delete_url($coursecategory)
    {
        return $this->get_parent()->get_course_category_delete_url($coursecategory);
    }

    /**
     * @see WeblcmsManager :: is_subscribed()
     */
    function is_subscribed($course, $user_id)
    {
        return $this->get_parent()->is_subscribed($course, $user_id);
    }

    /**
     * @see WeblcmsManager :: subscribe_user_to_course()
     */
    function subscribe_user_to_course($course, $status, $tutor_id, $user_id)
    {
        return $this->get_parent()->subscribe_user_to_course($course, $status, $tutor_id, $user_id);
    }

    /**
     * @see WeblcmsManager :: unsubscribe_user_from_course()
     */
    function unsubscribe_user_from_course($course, $user_id)
    {
        return $this->get_parent()->unsubscribe_user_from_course($course, $user_id);
    }

    /**
     * @see WeblcmsManager :: subscribe_group_to_course()
     */
    function subscribe_group_to_course($course, $group_id)
    {
        return $this->get_parent()->subscribe_group_to_course($course, $group_id);
    }

    /**
     * @see WeblcmsManager :: unsubscribe_user_from_course()
     */
    function unsubscribe_group_from_course($course, $group_id)
    {
        return $this->get_parent()->unsubscribe_group_from_course($course, $group_id);
    }

    /**
     * @see WeblcmsManager :: get_search_condition()
     */
    function get_search_condition()
    {
        return $this->get_parent()->get_search_condition();
    }

    /**
     * @see WeblcmsManager :: get_search_validate()
     */
    function get_search_validate()
    {
        return $this->get_parent()->get_search_validate();
    }

    /**
     * @see WeblcmsManager :: get_search_parameter()
     */
    function get_search_parameter($name)
    {
        return $this->get_parent()->get_search_parameter($name);
    }

    function get_reporting_url($classname, $params)
    {
        return $this->get_parent()->get_reporting_url($classname, $params);
    }

    function display_header($breadcrumbtrail, $display_search = false, $display_title = true, $help_item = null)
    {
        return $this->get_parent()->display_header($breadcrumbtrail, $display_search, $display_title, $help_item);
    }

    function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    function get_course_group()
    {
        return $this->get_parent()->get_course_group();
    }

    function get_course_deleting_url($course)
    {
    	return $this->get_parent()->get_course_deleting_url($course);
    }
}
?>
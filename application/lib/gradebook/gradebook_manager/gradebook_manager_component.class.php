<?php
//require_once Path :: get_application_path() . 'lib/web_application_component.class.php';

abstract class GradebookManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param Gradebook $gradebook_manager The GradeBook which
     * provides this component
     */
    protected function GradebookManagerComponent($gradebook_manager)
    {
        parent :: __construct($gradebook_manager);
    }
// Data retrieval
//******************    
// evaluation formats
	function count_evaluation_formats()
	{
		return $this->get_parent()->count_evaluation_formats();
	}
	
	function retrieve_evaluation_formats($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->get_parent()->retrieve_evaluation_formats($condition, $offset, $max_objects, $order_by);
	}
    
	function retrieve_evaluation_format($id)
	{
		return $this->get_parent()->retrieve_evaluation_format($id);
	}
	
// URL creation
	function get_admin_browse_evaluation_format_types_link()
	{
		return $this->get_parent()->get_admin_browse_evaluation_format_types_link();
	}
	
	function get_evaluation_format_editing_url($evaluation_format)
	{
		return $this->get_parent()->get_evaluation_format_editing_url($evaluation_format);
	}
	
	function get_change_evaluation_format_activation_url($evaluation_format)
	{
		return $this->get_parent()->get_change_evaluation_format_activation_url($evaluation_format);
	}
	
	function get_evaluation_format_deleting_url($evaluation_format)
	{
		return $this->get_parent()->get_evaluation_format_deleting_url($evaluation_format);
	}
//------------------IGNORE--------------------------------
//gradebook

	function retrieve_gradebook($id)
	{
		return $this->get_parent()->retrieve_gradebook($id);
	}

	function retrieve_gradebooks($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_gradebooks($condition, $offset, $count, $order_property);
	}

	function count_gradebooks($conditions = null)
	{
		return $this->get_parent()->count_gradebooks($conditions);
	}

	function get_gradebook_editing_url($gradebook)
	{
		return $this->get_parent()->get_gradebook_editing_url($gradebook);
	}

	function get_create_gradebook_url()
	{
		return $this->get_parent()->get_create_gradebook_url();
	}

	function get_gradebook_emptying_url($gradebook)
	{
		return $this->get_parent()->get_gradebook_emptying_url($gradebook);
	}

	function get_gradebook_viewing_url($gradebook)
	{
		return $this->get_parent()->get_gradebook_viewing_url($gradebook);
	}

	function get_gradebook_subscribe_user_browser_url($gradebook)
	{
		return $this->get_parent()->get_gradebook_subscribe_user_browser_url($gradebook);
	}

	function get_gradebook_delete_url($gradebook)
	{
		return $this->get_parent()->get_gradebook_delete_url($gradebook);
	}

	//gradebook rel users

	function retrieve_gradebook_rel_users($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_gradebook_rel_users($condition, $offset, $count, $order_property);
	}

	function retrieve_gradebook_rel_user($user_id, $gradebook_id)
	{
		return $this->get_parent()->retrieve_gradebook_rel_user($user_id, $gradebook_id);
	}
	function count_gradebook_rel_users($conditions = null)
	{
		return $this->get_parent()->count_gradebook_rel_users($conditions);
	}

	function get_gradebook_rel_user_unsubscribing_url($gradebookreluser)
	{
		return $this->get_parent()->get_gradebook_rel_user_unsubscribing_url($gradebookreluser);
	}

	function get_gradebook_rel_user_subscribing_url($gradebook, $user)
	{
		return $this->get_parent()->get_gradebook_rel_user_subscribing_url($gradebook, $user);
	}
//--------------------END IGNORE-------------------------------
}
?>
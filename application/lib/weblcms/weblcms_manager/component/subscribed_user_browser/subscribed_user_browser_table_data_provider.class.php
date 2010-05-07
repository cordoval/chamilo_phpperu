<?php
/**
 * $Id: subscribed_user_browser_table_data_provider.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_user_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class SubscribedUserBrowserTableDataProvider extends ObjectTableDataProvider
{
    private $udm;
    private $object_count;
    private $preloaded_result_set = null;

    /**
     * Constructor
     * @param WeblcmsManagerComponent $browser
     * @param Condition $condition
     */
    function SubscribedUserBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
        $this->udm = UserDataManager :: get_instance($browser->get_user_id());
        $this->get_objects();
    }

    /**
     * Gets the users
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {        
    	if(is_null($this->preloaded_result_set))
    	{
	    	$order_property = $this->get_order_property($order_property);
	        
	        $users_result = $this->udm->retrieve_users($this->get_condition(), $offset, $count, $order_property);
	        $users = array();
	        $course = parent::get_browser()->get_course();
	        while($user = $users_result->next_result())
	        {
	        	if($course->can_user_subscribe($user) || ($course->is_course_admin($user) && (parent::get_browser()->get_action() == UserTool :: ACTION_UNSUBSCRIBE_USERS || is_null(parent::get_browser()->get_action()))))
	        	{
	        		$users[] = $user;
	        	}
	        }
	        $this->object_count = count($users);
	        $this->preloaded_result_set = new ArrayResultSet($users);
    	}
        return $this->preloaded_result_set;
    }

    /**
     * Gets the number of users in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->object_count;
    }
}
?>
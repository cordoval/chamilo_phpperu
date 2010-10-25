<?php
/**
 * $Id: course_group_subscribed_user_browser_table_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class CourseGroupSubscribedUserBrowserTableDataprovider extends ObjectTableDataProvider
{
    private $wdm;

    /**
     * Constructor
     * @param WeblcmsManagerComponent $browser
     * @param Condition $condition
     */
    function CourseGroupSubscribedUserBrowserTableDataprovider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
        $this->wdm = WeblcmsDataManager :: get_instance();
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
        $order_property = $this->get_order_property($order_property);
        
        return $this->wdm->retrieve_course_group_users($this->get_browser()->get_course_group(), $this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of users in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->wdm->count_course_group_users($this->get_browser()->get_course_group(), $this->get_condition());
    }
}
?>
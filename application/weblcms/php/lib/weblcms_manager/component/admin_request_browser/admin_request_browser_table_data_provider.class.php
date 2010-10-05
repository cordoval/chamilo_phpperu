<?php
/**
 * $Id: admin_course_type_browser_table_data_provider.class.php 218 2010-03-10 14:21:26Z yannick $
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_type_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class AdminRequestBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param WeblcmsManagerComponent $browser
     * @param Condition $condition
     */
    function AdminRequestBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the coursetypes
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching coursetypes.
     */
    
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        $request_method = null;
        
        switch($this->get_browser()->get_request_type())
        {
        	case CommonRequest :: SUBSCRIPTION_REQUEST: $request_method = 'retrieve_requests'; break;
        	case CommonRequest :: CREATION_REQUEST: $request_method = 'retrieve_course_create_requests'; break;
        }
        
        return $this->get_browser()->$request_method($this->get_condition(), $offset, $count, $order_property);
    }
    

    /**
     * Gets the number of coursetypes in the table
     * @return int
     */
    
    function get_object_count()
    {
        $request_method = null;
        switch($this->get_browser()->get_request_type())
        {
        	case CommonRequest :: SUBSCRIPTION_REQUEST: $request_method = 'count_requests'; break;
        	case CommonRequest :: CREATION_REQUEST: $request_method = 'count_course_create_requests'; break;
        }
        return $this->get_browser()->$request_method($this->get_condition());
    }
    
}
?>
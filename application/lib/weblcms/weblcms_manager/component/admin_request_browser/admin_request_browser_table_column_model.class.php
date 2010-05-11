<?php
/**
 * $Id: admin_course_type_browser_table_column_model.class.php 218 2010-03-10 14:21:26Z yannick $
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_type_browser
 */
require_once dirname(__FILE__) . '/../../../course/course_request_table/default_course_request_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course/course.class.php';
require_once dirname(__FILE__) . '/../../../course/course_request.class.php';
/**
 * Table column model for the course browser table
 */
class AdminRequestBrowserTableColumnModel extends DefaultCourseRequestTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function AdminRequestBrowserTableColumnModel($request_type)
    {
        parent :: __construct($request_type);
        $this->add_column(new ObjectTableColumn(CourseRequest :: PROPERTY_MOTIVATION));
        $this->add_column(new ObjectTableColumn(CourseRequest :: PROPERTY_CREATION_DATE));
        $this->add_column(new ObjectTableColumn(CourseRequest :: PROPERTY_DECISION_DATE));
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
        
    }

    /**
     * Gets the modification column
     * @return RequestTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
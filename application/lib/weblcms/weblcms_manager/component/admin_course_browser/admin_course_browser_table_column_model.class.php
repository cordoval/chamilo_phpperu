<?php
/**
 * $Id: admin_course_browser_table_column_model.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_browser
 */
require_once dirname(__FILE__) . '/../../../course/course_table/default_course_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course/course.class.php';
/**
 * Table column model for the course browser table
 */
class AdminCourseBrowserTableColumnModel extends DefaultCourseTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function AdminCourseBrowserTableColumnModel()
    {
        parent :: __construct();
//        $this->add_column(new ObjectTableColumn(Course :: PROPERTY_LANGUAGE));
        $this->add_column(new ObjectTableColumn(Course :: PROPERTY_CATEGORY));
//        $this->add_column(new ObjectTableColumn(Course :: PROPERTY_SUBSCRIBE_ALLOWED));
//        $this->add_column(new ObjectTableColumn(Course :: PROPERTY_UNSUBSCRIBE_ALLOWED));
        $this->add_column(new ObjectTableColumn(Course :: PROPERTY_TITULAR));
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return CourseTableColumn
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

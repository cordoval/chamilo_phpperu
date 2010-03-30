<?php
/**
 * $Id: admin_course_type_browser_table_column_model.class.php 218 2010-03-10 14:21:26Z yannick $
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_type_browser
 */
require_once dirname(__FILE__) . '/../../../course_type/course_type_table/default_course_type_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course_type/course_type.class.php';
require_once dirname(__FILE__) . '/../../../course_type/course_type_settings.class.php';
/**
 * Table column model for the course browser table
 */
class AdminCourseTypeBrowserTableColumnModel extends DefaultCourseTypeTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function AdminCourseTypeBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->add_column(new ObjectTableColumn(CourseType :: PROPERTY_NAME));
        $this->add_column(new ObjectTableColumn(CourseType :: PROPERTY_DESCRIPTION));
        $this->add_column(new ObjectTableColumn(CourseType :: PROPERTY_ACTIVE));
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return CourseTypeTableColumn
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

<?php
namespace application\weblcms;

use common\libraries\StaticTableColumn;
use common\libraries\ObjectTableColumn;

require_once dirname(__FILE__) . '/../../../course/course_table/default_course_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course/course.class.php';

/**
 * Table column model for the course browser table
 */
class CatalogCourseBrowserTableColumnModel extends DefaultCourseTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct();
        $this->add_column(new ObjectTableColumn(Course :: PROPERTY_COURSE_TYPE_ID));
        $this->add_column(new ObjectTableColumn(Course :: PROPERTY_CATEGORY));
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
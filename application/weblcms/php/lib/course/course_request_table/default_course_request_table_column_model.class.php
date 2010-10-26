<?php
namespace application\weblcms;

use common\libraries\ObjectTableColumnModel;

/**
 * $Id: default_course_request_table_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course.course_request_table
 */
require_once dirname(__FILE__) . '/../course_request.class.php';

class DefaultCourseRequestTableColumnModel extends ObjectTableColumnModel
{
    const USER_NAME = 'user_name';
    const COURSE_NAME = 'course_name';
    const COURSE_TYPE_NAME = 'course_type_name';

    /**
     * Constructor
     */
    function DefaultCourseRequestTableColumnModel($request_type)
    {
        parent :: __construct(self :: get_default_columns($request_type), 1);
    }

    /**
     * Gets the default columns for this model
     * @return CourseSectionTableColumn[]
     */
    private static function get_default_columns($request_type)
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(self :: USER_NAME, false);
        $columns[] = new ObjectTableColumn(self :: COURSE_NAME, false);
        if ($request_type == CommonRequest :: CREATION_REQUEST)
            $columns[] = new ObjectTableColumn(self :: COURSE_TYPE_NAME, false);
        $columns[] = new ObjectTableColumn(CourseRequest :: PROPERTY_SUBJECT);
        
        return $columns;
    }
}
?>
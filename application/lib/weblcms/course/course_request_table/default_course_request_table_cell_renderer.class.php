<?php
/**
 * $Id: default_course_request_table_cell_renderer.class.php 216 2010-03-12 14:08:06Z Yannick $
 * @package application.lib.weblcms.course.course_request_table
 */

require_once dirname(__FILE__) . '/../course_request.class.php';

class DefaultCourseRequestTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultCourseRequestTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param CourseRequestTableColumnModel $column The column which should be
     * rendered
     * @param CourseRequest $request The request object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $request)
    {
        switch ($column->get_name())
        {
            case CourseRequest :: PROPERTY_COURSE_ID :
                return $request->get_id();
            
            case CourseRequest :: PROPERTY_NAME_USER :
            	return $request->get_name_user();
            	
            case CourseRequest :: PROPERTY_COURSE_NAME :
            	return $request->get_course_name();
            	
            case CourseRequest :: PROPERTY_TITLE :
                return $request->get_title();
                
            case CourseRequest :: PROPERTY_MOTIVATION :
            	return $request->get_motivation();
            	
            case CourseRequest :: PROPERTY_CREATION_DATE :
            	return $request->get_creation_date();
            	
            case CourseRequest :: PROPERTY_ALLOWED_DATE :
            	return $request->get_allowed_date();
            	
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>
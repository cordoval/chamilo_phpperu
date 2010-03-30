<?php
/**
 * $Id: default_course_type_table_cell_renderer.class.php 216 2010-03-12 14:08:06Z Yannick $
 * @package application.lib.weblcms.course_type.course_type_table
 */

require_once dirname(__FILE__) . '/../course_type.class.php';

class DefaultCourseTypeTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultCourseTypeTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param CourseTypeTableColumnModel $column The column which should be
     * rendered
     * @param CourseType $course_type The course_type object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $course_type)
    {
        switch ($column->get_name())
        {
            case CourseType :: PROPERTY_ID :
                return $course_type->get_id();
            
            case CourseType :: PROPERTY_NAME :
                return $course_type->get_name();
                
            case CourseType :: PROPERTY_DESCRIPTION :
            	return $course_type->get_description();
            	
            case CourseType :: PROPERTY_ACTIVE :
            	//return $course_type->get_active();
            	if($course_type->get_active())
            	{
            		Return Translation :: get('True');
            	}
            	else
            	{
            		Return Translation :: get('False');
            	}
            	
            /*
            case Course :: PROPERTY_SUBSCRIBE_ALLOWED :
                $sub = $course->get_subscribe_allowed();
                if ($sub)
                    return Translation :: get('True');
                else
                    return Translation :: get('False');
            case Course :: PROPERTY_UNSUBSCRIBE_ALLOWED :
                $sub = $course->get_unsubscribe_allowed();
                if ($sub)
                    return Translation :: get('True');
                else
                    return Translation :: get('False');
              */
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
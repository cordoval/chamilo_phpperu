<?php
/**
 * $Id: default_course_table_cell_renderer.class.php 216 2010-03-12 14:08:06Z Yannick $
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
                
            //case Course :: PROPERTY_VISUAL :
            //    return $course->get_visual();
            
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
            case Course :: PROPERTY_TITULAR :
                $titular = UserDataManager :: get_instance()->retrieve_user($course->get_titular());
                if ($titular)
                    return $titular->get_fullname();
                return '';
            case Course :: PROPERTY_LANGUAGE :
                return $course->get_language();
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
            case Course :: PROPERTY_CATEGORY :
                $cat_id = $course->get_category();
                $cat = WeblcmsDataManager :: get_instance()->retrieve_course_category($cat_id);
                return $cat->get_name();
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
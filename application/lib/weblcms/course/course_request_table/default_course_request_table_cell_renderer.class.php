<?php
/**
 * $Id: default_course_request_table_cell_renderer.class.php 216 2010-03-12 14:08:06Z Yannick $
 * @package application.lib.weblcms.course.course_request_table
 */

require_once dirname(__FILE__) . '/../course_request.class.php';

class DefaultCourseRequestTableCellRenderer implements ObjectTableCellRenderer
{	
	const USER_NAME = 'user_name';
	const COURSE_NAME = 'course_name';
	
	/**
     * The repository browser component
     */
	
    protected $browser;
    
    /**
     * Constructor
     */
    
    function DefaultCourseRequestTableCellRenderer($browser)
    {
        $this->browser = $browser;
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
            case self :: USER_NAME :
            	return UserDataManager::get_instance()->retrieve_user($request->get_user_id())->get_fullname();
            	
            case self :: COURSE_NAME :
            	return $this->browser->retrieve_course($request->get_course_id())->get_name();
            	
            case CommonRequest :: PROPERTY_SUBJECT :
                return $request->get_subject();
                
            case CommonRequest :: PROPERTY_MOTIVATION :
            	return $request->get_motivation();
            	
            case CommonRequest :: PROPERTY_CREATION_DATE :
            	return DatetimeUtilities :: format_locale_date(null,$request->get_creation_date());
            	       
            case CommonRequest :: PROPERTY_DECISION_DATE :
            	if($request->get_decision_date() != null)
            	{
            		return DatetimeUtilities :: format_locale_date(null,$request->get_decision_date());
            	}
            	else
            		return $request->get_decision_date();
            	
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
<?php
/**
 * $Id: course_user_category_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/course.class.php';
require_once dirname(__FILE__) . '/course_user_category.class.php';

class CourseUserCategoryForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $courseusercategory;
    private $user;
    private $parent;

    function CourseUserCategoryForm($form_type, $courseusercategory, $user, $action, $parent)
    {
        parent :: __construct('course_settings', 'post', $action);
        
        $this->courseusercategory = $courseusercategory;
        $this->user = $user;
        $this->parent = $parent;
        
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('text', CourseUserCategory :: PROPERTY_TITLE, Translation :: get('Title'), array("maxlength" => 50, "size" => 50));
        $this->addRule(CourseUserCategory :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
		$course_types = $this->get_course_types();
        $this->addElement('select', CourseTypeUserCategory::PROPERTY_COURSE_TYPE_ID, Translation :: get('CourseType'), $course_types);
    //$this->addElement('submit', 'course_user_category', Translation :: get('Ok'));
    }

    function build_editing_form()
    {
        $courseusercategory = $this->courseusercategory;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', CourseUserCategory :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_course_user_category()
    {
        $courseusercategory = $this->courseusercategory;
        $values = $this->exportValues();
        
        $courseusercategory->set_title($values[CourseUserCategory :: PROPERTY_TITLE]);
        
        return $courseusercategory->update();
    }

    function create_course_user_category()
    {
        $courseusercategory = $this->courseusercategory;
        $values = $this->exportValues();
        
        $courseusercategory->set_id($values[CourseUserCategory :: PROPERTY_ID]);
        $courseusercategory->set_title($values[CourseUserCategory :: PROPERTY_TITLE]);
        
        if(!$courseusercategory->create())
        	return false;
        
        $coursetypeusercategory = new CourseTypeUserCategory();
        $coursetypeusercategory->set_course_user_category_id($courseusercategory->get_id());
        $coursetypeusercategory->set_course_type_id($values[CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID]);
        $coursetypeusercategory->set_user_id($this->user->get_id());
        
        return $coursetypeusercategory->create();
    }

    function get_course_types()
    {
    	$course_types = array();
        $course_active_types = $this->parent->retrieve_active_course_types();
        while($course_type = $course_active_types->next_result())
       	{
       	    $conditions = array();
       		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->parent->get_user_id(), CourseUserRelation :: get_table_name());
       		$conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course_type->get_id());
       		$condition = new AndCondition($conditions);
       		$courses_count = $this->parent->count_user_courses($condition);
       	 	if($courses_count > 0)
				$course_types[$course_type->get_id()] = $course_type->get_name();
       	}
       	
       	$conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->parent->get_user_id(), CourseUserRelation :: get_table_name());
        $conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, 0);
       	$condition = new AndCondition($conditions);
       	$courses_count= $this->parent->count_user_courses($condition);
       	if($courses_count > 0)
			$course_types[0] = Translation :: get('NoCourseType');
		return $course_types;
    }
    
    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $courseusercategory = $this->courseusercategory;
        $defaults[CourseUserCategory :: PROPERTY_TITLE] = $courseusercategory->get_title();
        parent :: setDefaults($defaults);
    }
}
?>
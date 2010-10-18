<?php
/**
 * $Id: course_user_relation_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
class CourseTypeUserCategoryRelCourseForm extends FormValidator
{
    private $course_type_user_category_rel_course;
    private $user;

    function CourseTypeUserCategoryRelCourseForm($course_type_user_category_rel_course, $user, $action)
    {
        parent :: __construct('course_type_user_category_rel_course_form', 'post', $action);
        
        $this->course_type_user_category_rel_course = $course_type_user_category_rel_course;
        $this->user = $user;
        
        $this->build_basic_form();
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('static', Course :: PROPERTY_ID, Translation :: get('CourseCode'));
        $wdm = WeblcmsDataManager :: get_instance();
        
        $course = $wdm->retrieve_course($this->course_type_user_category_rel_course->get_course_id());
        $categories = $wdm->retrieve_course_user_categories_from_course_type($course->get_course_type_id(), $this->user->get_id());
        
        $cat_options['0'] = Translation :: get('NoCategory');
        while ($category = $categories->next_result())
        {
            $cat_options[$category->get_id()] = $category->get_optional_property(CourseUserCategory :: PROPERTY_TITLE);
        }
        
        $this->addElement('select', CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, Translation :: get('Category'), $cat_options);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_course_type_user_category_rel_course()
    {
        $course_type_user_category_rel_course = $this->course_type_user_category_rel_course;
        $values = $this->exportValues();
        
        $current_category = $course_type_user_category_rel_course->get_course_type_user_category_id();
        $selected_category = $values[CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID];
        
        if($current_category != $selected_category)
        {
	        if($current_category)
	        {
	        	$course_type_user_category_rel_course->delete();
	        } 
	        
	        if($selected_category)
	        {
	        	$course_type_user_category_rel_course->set_course_type_user_category_id($selected_category);
	        	return $course_type_user_category_rel_course->create();
	        }
        }
       
        return true;
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $course_type_user_category_rel_course = $this->course_type_user_category_rel_course;
        
        $course = WeblcmsDataManager :: get_instance()->retrieve_course($course_type_user_category_rel_course->get_course_id());
        
        $defaults[Course :: PROPERTY_ID] = $course->get_name();
        $defaults[CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID] = $course_type_user_category_rel_course->get_course_type_user_category_id();
        
        parent :: setDefaults($defaults);
    }
}
?>
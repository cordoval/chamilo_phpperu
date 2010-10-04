<?php
/**
 * $Id: course_category_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';

class CourseCategoryForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $coursecategory;

    function CourseCategoryForm($form_type, $coursecategory, $action)
    {
        parent :: __construct('course_category', 'post', $action);
        
        $this->coursecategory = $coursecategory;
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
        $this->addElement('text', CourseCategory :: PROPERTY_NAME, Translation :: get('CourseCategoryName'), array("size" => "50"));
        $this->addRule(CourseCategory :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $cat_options = array();
        
        $coursecategory = $this->coursecategory;
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseCategory :: PROPERTY_AUTH_CAT_CHILD, true);
        $conditions[] = new NotCondition(new EqualityCondition(CourseCategory :: PROPERTY_CODE, $coursecategory->get_code()));
        $condition = new AndCondition($conditions);
        
        $wdm = WeblcmsDataManager :: get_instance();
        $categories = $wdm->retrieve_course_categories($condition);
        
        $cat_options['0'] = Translation :: get('NoCategory');
        while ($category = $categories->next_result())
        {
            $cat_options[$category->get_id()] = $category->get_name();
        }
        
        $this->addElement('select', CourseCategory :: PROPERTY_PARENT, Translation :: get('Parent'), $cat_options);
        
        $child_allowed = array();
        $child_allowed[] = & $this->createElement('radio', null, null, Translation :: get('Yes'), 1);
        $child_allowed[] = & $this->createElement('radio', null, null, Translation :: get('No'), 0);
        $this->addGroup($child_allowed, CourseCategory :: PROPERTY_AUTH_COURSE_CHILD, Translation :: get('CourseCategoryChildAllowed'), '<br />');
        
        $cat_allowed = array();
        $cat_allowed[] = & $this->createElement('radio', null, null, Translation :: get('Yes'), 1);
        $cat_allowed[] = & $this->createElement('radio', null, null, Translation :: get('No'), 0);
        $this->addGroup($cat_allowed, CourseCategory :: PROPERTY_AUTH_CAT_CHILD, Translation :: get('CourseCategoryCatAllowed'), '<br />');
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', CourseCategory :: PROPERTY_ID);
        
        //$this->addElement('submit', 'course_settings', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->addElement('text', CourseCategory :: PROPERTY_CODE, Translation :: get('CourseCategoryCode'));
        $this->addRule(CourseCategory :: PROPERTY_CODE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->build_basic_form();
        
        //$this->addElement('submit', 'course_settings', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_course_category()
    {
        $coursecategory = $this->coursecategory;
        $values = $this->exportValues();
        
        $coursecategory->set_name($values[CourseCategory :: PROPERTY_NAME]);
        $coursecategory->set_parent($values[CourseCategory :: PROPERTY_PARENT]);
        $coursecategory->set_auth_course_child($values[CourseCategory :: PROPERTY_AUTH_COURSE_CHILD]);
        $coursecategory->set_auth_cat_child($values[CourseCategory :: PROPERTY_AUTH_CAT_CHILD]);
        
        return $coursecategory->update();
    }

    function create_course_category()
    {
        $coursecategory = $this->coursecategory;
        $values = $this->exportValues();
        
        $coursecategory->set_name($values[CourseCategory :: PROPERTY_NAME]);
        $coursecategory->set_code($values[CourseCategory :: PROPERTY_CODE]);
        $coursecategory->set_parent($values[CourseCategory :: PROPERTY_PARENT]);
        $coursecategory->set_auth_course_child($values[CourseCategory :: PROPERTY_AUTH_COURSE_CHILD]);
        $coursecategory->set_auth_cat_child($values[CourseCategory :: PROPERTY_AUTH_CAT_CHILD]);
        
        return $coursecategory->create();
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $coursecategory = $this->coursecategory;
        $defaults[CourseCategory :: PROPERTY_NAME] = $coursecategory->get_name();
        $defaults[CourseCategory :: PROPERTY_CODE] = $coursecategory->get_code();
        $defaults[CourseCategory :: PROPERTY_AUTH_COURSE_CHILD] = $coursecategory->get_auth_course_child();
        $defaults[CourseCategory :: PROPERTY_AUTH_CAT_CHILD] = $coursecategory->get_auth_cat_child();
        $defaults[CourseCategory :: PROPERTY_PARENT] = $coursecategory->get_parent();
        parent :: setDefaults($defaults);
    }
}
?>
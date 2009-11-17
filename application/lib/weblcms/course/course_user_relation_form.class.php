<?php
/**
 * $Id: course_user_relation_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/course_user_relation.class.php';

class CourseUserRelationForm extends FormValidator
{
    
    const TYPE_EDIT = 2;
    
    private $courseuserrelation;
    private $user;

    function CourseUserRelationForm($form_type, $courseuserrelation, $user, $action)
    {
        parent :: __construct('course_user', 'post', $action);
        
        $this->courseuserrelation = $courseuserrelation;
        $this->user = $user;
        
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('static', Course :: PROPERTY_ID, Translation :: get('CourseCode'));
        
        $wdm = WeblcmsDataManager :: get_instance();
        
        $condition = new EqualityCondition(CourseUserCategory :: PROPERTY_USER, $this->user->get_id());
        
        $categories = $wdm->retrieve_course_user_categories($condition);
        $cat_options['0'] = Translation :: get('NoCategory');
        
        while ($category = $categories->next_result())
        {
            $cat_options[$category->get_id()] = $category->get_title();
        }
        
        $this->addElement('select', CourseUserRelation :: PROPERTY_CATEGORY, Translation :: get('Category'), $cat_options);
        
        //$this->addElement('submit', 'course_user_category', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_editing_form()
    {
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', CourseUserRelation :: PROPERTY_COURSE);
    }

    function update_course_user_relation()
    {
        $courseuserrelation = $this->courseuserrelation;
        $values = $this->exportValues();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->user->get_id());
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, $values[CourseUserRelation :: PROPERTY_CATEGORY]);
        $condition = new AndCondition($conditions);
        
        $wdm = WeblcmsDataManager :: get_instance();
        $sort = $wdm->retrieve_max_sort_value(CourseUserRelation :: get_table_name(), CourseUserRelation :: PROPERTY_SORT, $condition);
        
        $courseuserrelation->set_category($values[CourseUserRelation :: PROPERTY_CATEGORY]);
        $courseuserrelation->set_sort($sort + 1);
        
        return $courseuserrelation->update();
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $courseuserrelation = $this->courseuserrelation;
        
        $course = WeblcmsDataManager :: get_instance()->retrieve_course($courseuserrelation->get_course());
        
        $defaults[Course :: PROPERTY_ID] = $course->get_visual();
        
        $defaults[CourseUserRelation :: PROPERTY_COURSE] = $courseuserrelation->get_course();
        $defaults[CourseUserRelation :: PROPERTY_CATEGORY] = $courseuserrelation->get_category();
        parent :: setDefaults($defaults);
    }
}
?>
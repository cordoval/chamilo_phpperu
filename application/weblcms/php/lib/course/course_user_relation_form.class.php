<?php
/**
 * $Id: course_user_relation_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/course_user_relation.class.php';

class CourseUserRelationForm extends FormValidator
{
    
    const TYPE_EDIT = 2;
    
    private $course_user_relation;
    private $user;

    function CourseUserRelationForm($form_type, $course_user_relation, $user, $action)
    {
        parent :: __construct('course_user', 'post', $action);
        
        $this->course_user_relation = $course_user_relation;
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
        
        $course = $wdm->retrieve_course($this->course_user_relation->get_course());
        
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_USER_ID, $this->user->get_id(), CourseTypeUserCategory :: get_table_name());
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course->get_course_type_id(),  CourseTypeUserCategory :: get_table_name());
        $condition = new AndCondition($conditions);
        $categories = $wdm->retrieve_course_user_categories_by_course_type($condition);
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
        $course_user_relation = $this->course_user_relation;
        $values = $this->exportValues();
        
        $old_category = $course_user_relation->get_category();
        $counter = $course_user_relation->get_sort();
        
        $wdm = WeblcmsDataManager :: get_instance();
        
        $course_user_relation->set_category($values[CourseUserRelation :: PROPERTY_CATEGORY]);
        $sort = $wdm->retrieve_next_course_user_relation_sort_value($course_user_relation);
        $course_user_relation->set_sort($sort);
        
        $succes = $course_user_relation->update();
        
        $course = $wdm->retrieve_course($course_user_relation->get_course());
    	$subcondition = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course->get_course_type_id());
    	$conditions[] = new SubselectCondition(CourseUserRelation :: PROPERTY_COURSE, Course :: PROPERTY_ID, Course :: get_table_name(), $subcondition);
    	$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $course_user_relation->get_user());
    	$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, $old_category);
        $conditions[] = new InEqualityCondition(CourseUserRelation :: PROPERTY_SORT, InEqualityCondition :: GREATER_THAN, $counter);
    	$condition = new AndCondition($conditions);

    	$course_user_relations = $wdm->retrieve_course_user_relations($condition, null, null, new ObjectTableOrder(CourseUserRelation :: PROPERTY_SORT));
        
        while($relation = $course_user_relations->next_result())
        {
        	$relation->set_sort($counter);
        	$succes &= $relation->update();
        	$counter++;
        }
        
        return $succes;
        
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $course_user_relation = $this->course_user_relation;
        
        $course = WeblcmsDataManager :: get_instance()->retrieve_course($course_user_relation->get_course());
        
        $defaults[Course :: PROPERTY_ID] = $course->get_visual();
        
        $defaults[CourseUserRelation :: PROPERTY_COURSE] = $course_user_relation->get_course();
        $defaults[CourseUserRelation :: PROPERTY_CATEGORY] = $course_user_relation->get_category();
        parent :: setDefaults($defaults);
    }
}
?>
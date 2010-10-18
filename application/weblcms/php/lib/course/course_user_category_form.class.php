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
    
    const COURSE_TYPE_TARGET = 'course_type_target';
    const COURSE_TYPE_TARGET_ELEMENTS = 'course_type_target_elements';
    const COURSE_TYPE_TARGET_OPTION = 'course_type_target_option';
    
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
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'application/weblcms/php/xml_feeds/xml_course_type_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        
        $element_finder = $this->createElement('user_group_finder', self :: COURSE_TYPE_TARGET_ELEMENTS, Translation :: get('CourseType'), $attributes['search_url'], $attributes['locale'], $attributes['defaults'], $attributes['options']);
        $element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);
    
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
        
        if (! $courseusercategory->update())
        {
            return false;
        }
        
        $wdm = WeblcmsDataManager :: get_instance();
        $condition = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $courseusercategory->get_id());
        $previous_types = $wdm->retrieve_course_type_user_categories($condition);
        $course_types = $this->get_selected_course_types();
        
        foreach ($course_types as $type)
        {
            if (! $type->create())
            {
                return false;
            }
        }
        
        while ($previous_type = $previous_types->next_result())
        {
            $validation = false;
            foreach ($course_types as $index => $type)
            {
                if ($type->get_course_type_id() == $previous_type->get_course_type_id())
                {
                    if (! $type->update())
                    {
                        return false;
                    }
                    unset($course_types[$index]);
                    $validation = true;
                }
            }
            if (! $validation)
            {
                if (! $previous_type->delete())
                {
                    return false;
                }
            }
        }
        
        return true;
    }

    function create_course_user_category()
    {
        $values = $this->exportValues();
        $course_types = $this->get_selected_course_types();
        
        if (count($course_types) == 0)
        {
            return false;
        }
        
        $this->courseusercategory->set_id($values[CourseUserCategory :: PROPERTY_ID]);
        $this->courseusercategory->set_title($values[CourseUserCategory :: PROPERTY_TITLE]);
        
        if (! $this->courseusercategory->create())
        {
            return false;
        }
        
        foreach ($course_types as $course_type)
        {
            $course_type->set_course_user_category_id($this->courseusercategory->get_id());
            
            if (! $course_type->create())
            {
                return false;
            }
        }
        
        return true;
    }

    function get_selected_course_types()
    {
        $values = $this->exportValues();
        $course_types_array = array();
        
        foreach ($values[self :: COURSE_TYPE_TARGET_ELEMENTS]['coursetype'] as $value)
        {
            $coursetypeusercategory = new CourseTypeUserCategory();
            $coursetypeusercategory->set_course_type_id($value);
            $coursetypeusercategory->set_user_id($this->user->get_id());
            $course_types_array[] = $coursetypeusercategory;
        }
        
        return $course_types_array;
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
        
        if (! is_null($courseusercategory->get_id()))
        {
            $wdm = WeblcmsDataManager :: get_instance();
            
            $condition = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $courseusercategory->get_id());
            $course_types = $wdm->retrieve_course_type_user_categories($condition);
            
            while ($type = $course_types->next_result())
            {
                $selected_course_type = $this->get_course_type_array($type->get_course_type_id(), $wdm);
                $defaults[self :: COURSE_TYPE_TARGET_ELEMENTS][$selected_course_type['id']] = $selected_course_type;
            }
            
            if (count($defaults[self :: COURSE_TYPE_TARGET_ELEMENTS]) > 0)
            {
                $active = $this->getElement(self :: COURSE_TYPE_TARGET_ELEMENTS);
                $active->setValue($defaults[self :: COURSE_TYPE_TARGET_ELEMENTS]);
            }
        
        }
        
        parent :: setDefaults($defaults);
    }

    function get_course_type_array($course_type_id, $wdm)
    {
        $selected_course_type = array();
        $selected_course_type['classes'] = 'type type_course_type';
        if ($course_type_id != 0)
        {
            $course_type = $wdm->retrieve_course_type($course_type_id);
            $selected_course_type['id'] = 'coursetype_' . $course_type->get_id();
            $selected_course_type['title'] = $course_type->get_name();
            $selected_course_type['description'] = $course_type->get_name();
        }
        else
        {
            $selected_course_type['id'] = 'coursetype_0';
            $selected_course_type['title'] = Translation :: get('NoCourseType');
            $selected_course_type['description'] = Translation :: get('NoCourseType');
        }
        return $selected_course_type;
    }
}
?>
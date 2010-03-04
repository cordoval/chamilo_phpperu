<?php
/**
 * $Id: course_group_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course_group
 */
require_once dirname(__FILE__) . '/course_group.class.php';
require_once dirname(__FILE__) . '/../tool/course_group/course_group_menu.class.php';

class CourseGroupForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $parent;
    private $course_group;
    private $form_type;

    function CourseGroupForm($form_type, $course_group, $action)
    {
        parent :: __construct('course_settings', 'post', $action);
        $this->form_type = $form_type;
        $this->course_group = $course_group;
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
        $this->addElement('text', CourseGroup :: PROPERTY_NAME, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(CourseGroup :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('select', CourseGroup :: PROPERTY_PARENT_ID, Translation :: get('Parent'), $this->get_groups());
        $this->addRule(CourseGroup :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(CourseGroup :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
        $this->addElement('text', CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS, Translation :: get('MaxNumberOfMembers'), 'size="4"');
        $this->addRule(CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS, Translation :: get('ThisFieldShouldBeNumeric'), 'regex', '/^[0-9]*$/');
        $this->addElement('checkbox', CourseGroup :: PROPERTY_SELF_REG, Translation :: get('Registration'), Translation :: get('SelfRegAllowed'));
        $this->addElement('checkbox', CourseGroup :: PROPERTY_SELF_UNREG, null, Translation :: get('SelfUnRegAllowed'));
        //$this->addElement('submit', 'course_group_settings', Translation :: get('Ok'));
    }
    
    function get_groups()
    {
    	$course = new Course();
    	$course->set_id($this->course_group->get_course_code());
    	
    	$menu = new CourseGroupMenu($course);
    	$renderer = new OptionsMenuRenderer();
        $menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    function build_editing_form()
    {
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', CourseGroup :: PROPERTY_ID);
        
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

    function update_course_group()
    {
        $course_group = $this->course_group;
        $values = $this->exportValues();
        $course_group->set_name($values[CourseGroup :: PROPERTY_NAME]);
        $course_group->set_description($values[CourseGroup :: PROPERTY_DESCRIPTION]);
        $course_group->set_max_number_of_members($values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS]);
        $course_group->set_self_registration_allowed($values[CourseGroup :: PROPERTY_SELF_REG]);
        $course_group->set_self_unregistration_allowed($values[CourseGroup :: PROPERTY_SELF_UNREG]);
        return $course_group->update();
    }

    function create_course_group()
    {
        $course_group = $this->course_group;
        $values = $this->exportValues();
        
        $course_group->set_name($values[CourseGroup :: PROPERTY_NAME]);
        $course_group->set_description($values[CourseGroup :: PROPERTY_DESCRIPTION]);
        $course_group->set_max_number_of_members($values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS]);
        $course_group->set_self_registration_allowed($values[CourseGroup :: PROPERTY_SELF_REG]);
        $course_group->set_self_unregistration_allowed($values[CourseGroup :: PROPERTY_SELF_UNREG]);
        if ($course_group->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $course_group = $this->course_group;
        $defaults[CourseGroup :: PROPERTY_NAME] = $course_group->get_name();
        $defaults[CourseGroup :: PROPERTY_DESCRIPTION] = $course_group->get_description();
        $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = $course_group->get_max_number_of_members();
        $defaults[CourseGroup :: PROPERTY_SELF_REG] = $course_group->is_self_registration_allowed();
        ;
        $defaults[CourseGroup :: PROPERTY_SELF_UNREG] = $course_group->is_self_unregistration_allowed();
        ;
        parent :: setDefaults($defaults);
    }
}
?>
<?php
/**
 * $Id: course_section_tool_selector_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections
 */
require_once dirname(__FILE__) . '/../../course/course_section.class.php';

class CourseSectionToolSelectorForm extends FormValidator
{
    private $course_section;

    function CourseSectionToolSelectorForm($course_section, $action)
    {
        parent :: __construct('course_sections', 'post', $action);
        
        $this->course_section = $course_section;
        $this->build_basic_form();
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $sel = & $this->addElement('advmultiselect', 'tools', Translation :: get('SelectTools'), $this->get_tools(), array('style' => 'width:200px;'));
        
        //$this->addElement('submit', 'course_sections', 'OK');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function get_tools()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $modules = $wdm->get_course_modules(Request :: get('course'));
        
        $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_NAME, Translation :: get('CourseAdministration'));
        $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, Request :: get('course'));
        
        $admin_section = $wdm->retrieve_course_sections(new AndCondition($conditions))->next_result();
        
        foreach ($modules as $module)
        {
            if ($module->section == $admin_section->get_id())
                continue;
                
            //if($module->section != $this->course_section->get_id())
            $tools[$module->id] = $module->name;
        }
        
        return $tools;
    }

    function get_registered_tools()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $modules = $wdm->get_course_modules(Request :: get('course'));
        
        foreach ($modules as $module)
        {
            if ($this->course_section->get_id() == $module->section)
            {
                $tools[] = $module->id;
            }
        }
        
        return $tools;
    }

    function update_course_modules()
    {
        $course_section = $this->course_section;
        $values = $this->exportValues();
        //dump($values);
        

        $wdm = WeblcmsDataManager :: get_instance();
        $modules = $wdm->get_course_modules(Request :: get('course'));
        
        $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_NAME, Translation :: get('Tools'));
        $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, Request :: get('course'));
        
        $main_section = $wdm->retrieve_course_sections(new AndCondition($conditions))->next_result();
        
        foreach ($modules as $module)
        {
            if (in_array($module->id, $values['tools']))
            {
                $wdm->change_module_course_section($module->id, $course_section->get_id());
                //echo $module->id . '<br />';
            }
            elseif ($this->course_section->get_id() == $module->section)
            {
                $wdm->change_module_course_section($module->id, $main_section->get_id());
                //echo 'main:' . $main_section->get_id() . ' ' . $module->id . '<br />';
            }
        
        }
        
        return true;
    }

    /**
     * Sets default values. 
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $defaults['tools'] = $this->get_registered_tools();
        //dump($defaults);
        parent :: setDefaults($defaults);
    }

}
?>
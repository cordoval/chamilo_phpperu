<?php
require_once dirname(__FILE__) . '/../period.class.php';
require_once dirname(__FILE__) . '/../category_rel_period.class.php';

class InternshipOrganizerPeriodForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'InternshipOrganizerPeriodUpdated';
    const RESULT_ERROR = 'InternshipOrganizerPeriodUpdateFailed';
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'target_categories';
    
    private $parent;
    private $period;
    private $user;

    function InternshipOrganizerPeriodForm($form_type, $period, $action, $user)
    {
        parent :: __construct('create_period', 'post', $action);
        
        $this->period = $period;
        $this->user = $user;
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
        $this->addElement('text', InternshipOrganizerPeriod :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(InternshipOrganizerPeriod :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('select', InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, Translation :: get('ParentPeriod'), $this->get_periods());
        $this->addRule(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
        
        $this->add_datepicker(InternshipOrganizerPeriod :: PROPERTY_BEGIN, Translation :: get('Begin'), false);
        $this->addRule(InternshipOrganizerPeriod :: PROPERTY_BEGIN, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_datepicker(InternshipOrganizerPeriod :: PROPERTY_END, Translation :: get('End'), false);
        $this->addRule(InternshipOrganizerPeriod :: PROPERTY_END, Translation :: get('ThisFieldIsRequired'), 'required');
    
    }

    function build_editing_form()
    {
        $period = $this->period;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', InternshipOrganizerPeriod :: PROPERTY_ID);
        
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

    function update_period()
    {
        $period = $this->period;
        $values = $this->exportValues();
        
        $period->set_name($values[InternshipOrganizerPeriod :: PROPERTY_NAME]);
        $period->set_description($values[InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION]);
        
        $period->set_begin(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerPeriod :: PROPERTY_BEGIN]));
        $period->set_end(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerPeriod :: PROPERTY_END]));
        
        $value = $period->update();
        
        $new_parent = $values[InternshipOrganizerPeriod :: PROPERTY_PARENT_ID];
        if ($period->get_parent_id() != $new_parent)
        {
            $period->move($new_parent);
        }
        
        //        if ($value)
        //        {
        //            Event :: trigger('update', 'period', array('target_period_id' => $period->get_id(), 'action_user_id' => $this->user->get_id()));
        //        }
        

        return $value;
    }

    function create_period()
    {
        $period = $this->period;
        $values = $this->exportValues();
        
        $period->set_name($values[InternshipOrganizerPeriod :: PROPERTY_NAME]);
        $period->set_description($values[InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION]);
        $period->set_begin(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerPeriod :: PROPERTY_BEGIN]));
        $period->set_end(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerPeriod :: PROPERTY_END]));
        $period->set_parent_id($values[InternshipOrganizerPeriod :: PROPERTY_PARENT_ID]);
        
        $value = $period->create();
        
        //        if ($value)
        //        {
        //            Event :: trigger('create', 'period', array('target_period_id' => $period->get_id(), 'action_user_id' => $this->user->get_id()));
        //        }
        

        return $value;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $period = $this->period;
        $defaults[InternshipOrganizerPeriod :: PROPERTY_ID] = $period->get_id();
        $defaults[InternshipOrganizerPeriod :: PROPERTY_PARENT_ID] = $period->get_parent_id();
        $defaults[InternshipOrganizerPeriod :: PROPERTY_NAME] = $period->get_name();
        $defaults[InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION] = $period->get_description();
        $defaults[InternshipOrganizerPeriod :: PROPERTY_BEGIN] = $period->get_begin();
        $defaults[InternshipOrganizerPeriod :: PROPERTY_END] = $period->get_end();
        parent :: setDefaults($defaults);
    }

    function get_period()
    {
        return $this->period;
    }

    function get_periods()
    {
        $period = $this->period;
        
        $period_menu = new InternshipOrganizerPeriodMenu($period->get_id(), null, true, true, true);
        $renderer = new OptionsMenuRenderer();
        $period_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

}
?>
<?php
require_once dirname(__FILE__) . '/../moment.class.php';

class InternshipPlannerMomentForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $moment;
    private $user;

    function InternshipPlannerMomentForm($form_type, $moment, $action, $user)
    {
        parent :: __construct('moment_settings', 'post', $action);
        
        $this->moment = $moment;
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
        
        $this->addElement('text', InternshipPlannerMoment :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(InternshipPlannerMoment :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipPlannerMoment :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
        $this->addRule(InternshipPlannerMoment :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_datepickerElement(InternshipPlannerMoment :: PROPERTY_BEGIN, Translation :: get('Begin'));
        $this->addRule(InternshipPlannerMoment :: PROPERTY_BEGIN, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_datepicker(InternshipPlannerMoment :: PROPERTY_END, Translation :: get('End'));
        $this->addRule(InternshipPlannerMoment :: PROPERTY_END, Translation :: get('ThisFieldIsRequired'), 'required');
    
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
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

    function update_moment()
    {
        $moment = $this->moment;
        $values = $this->exportValues();
        
        $moment->set_name($values[InternshipPlannerMoment :: PROPERTY_NAME]);
        $moment->set_description($values[InternshipPlannerMoment :: PROPERTY_DESCRIPTION]);
        $moment->set_begin($values[InternshipPlannerMoment :: PROPERTY_BEGIN]);
        $moment->set_end($values[InternshipPlannerMoment :: PROPERTY_END]);
        
        return $moment->update();
    }

    function create_moment()
    {
        $moment = $this->moment;
        $values = $this->exportValues();
        
        $moment->set_name($values[InternshipPlannerMoment :: PROPERTY_NAME]);
        $moment->set_description($values[InternshipPlannerMoment :: PROPERTY_DESCRIPTION]);
        $moment->set_begin($values[InternshipPlannerMoment :: PROPERTY_BEGIN]);
        $moment->set_end($values[InternshipPlannerMoment :: PROPERTY_END]);
        
        return $moment->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $moment = $this->moment;
        
        $defaults[InternshipPlannerMoment :: PROPERTY_NAME] = $moment->get_name();
        $defaults[InternshipPlannerMoment :: PROPERTY_DESCRIPTION] = $moment->get_description();
        $defaults[InternshipPlannerMoment :: PROPERTY_BEGIN] = $moment->get_begin();
        $defaults[InternshipPlannerMoment :: PROPERTY_END] = $moment->get_end();
        
        parent :: setDefaults($defaults);
    }
}
?>
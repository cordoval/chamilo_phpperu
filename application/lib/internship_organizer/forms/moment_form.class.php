<?php
require_once dirname(__FILE__) . '/../moment.class.php';

class InternshipOrganizerMomentForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $moment;
    private $user;

    function InternshipOrganizerMomentForm($form_type, $moment, $action, $user)
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
        
        $this->addElement('text', InternshipOrganizerMoment :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(InternshipOrganizerMoment :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerMoment :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
        $this->addRule(InternshipOrganizerMoment :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_datepicker(InternshipOrganizerMoment :: PROPERTY_BEGIN, Translation :: get('Begin'));
        $this->addRule(InternshipOrganizerMoment :: PROPERTY_BEGIN, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_datepicker(InternshipOrganizerMoment :: PROPERTY_END, Translation :: get('End'));
        $this->addRule(InternshipOrganizerMoment :: PROPERTY_END, Translation :: get('ThisFieldIsRequired'), 'required');
    
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
        
        $moment->set_name($values[InternshipOrganizerMoment :: PROPERTY_NAME]);
        $moment->set_description($values[InternshipOrganizerMoment :: PROPERTY_DESCRIPTION]);
        $moment->set_begin(Utilities :: time_from_datepicker($values[InternshipOrganizerMoment :: PROPERTY_BEGIN]));
        $moment->set_end(Utilities :: time_from_datepicker($values[InternshipOrganizerMoment :: PROPERTY_END]));
        
        return $moment->update();
    }

    function create_moment()
    {
        $moment = $this->moment;
        $values = $this->exportValues();
        
        $moment->set_name($values[InternshipOrganizerMoment :: PROPERTY_NAME]);
        $moment->set_description($values[InternshipOrganizerMoment :: PROPERTY_DESCRIPTION]);
        
        $moment->set_begin(Utilities :: time_from_datepicker($values[InternshipOrganizerMoment :: PROPERTY_BEGIN]));
        $moment->set_end(Utilities :: time_from_datepicker($values[InternshipOrganizerMoment :: PROPERTY_END]));
              
        return $moment->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $moment = $this->moment;
        
        $defaults[InternshipOrganizerMoment :: PROPERTY_NAME] = $moment->get_name();
        $defaults[InternshipOrganizerMoment :: PROPERTY_DESCRIPTION] = $moment->get_description();
        $defaults[InternshipOrganizerMoment :: PROPERTY_BEGIN] = $moment->get_begin();
        $defaults[InternshipOrganizerMoment :: PROPERTY_END] = $moment->get_end();
        
        parent :: setDefaults($defaults);
    }
}
?>
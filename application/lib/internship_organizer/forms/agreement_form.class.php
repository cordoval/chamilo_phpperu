<?php
require_once dirname(__FILE__) . '/../agreement.class.php';

/**
 * This class describes the form for a Place object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipOrganizerAgreementForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'target_periods';
        
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $agreement;
    private $user;

    function InternshipOrganizerAgreementForm($form_type, $agreement, $action, $user)
    {
        parent :: __construct('agreement_settings', 'post', $action);
        
        $this->agreement = $agreement;
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
        
        $this->addElement('text', InternshipOrganizerAgreement :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(InternshipOrganizerAgreement :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
        
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_period_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ChoosePeriods');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET, Translation :: get('Periods'), $url, $locale, array());
        $defaults = array($this->agreement->get_period_id());
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
    
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

    function update_agreement()
    {
        $agreement = $this->agreement;
        $values = $this->exportValues();
        
        $agreement->set_name($values[InternshipOrganizerAgreement :: PROPERTY_NAME]);
        $agreement->set_description($values[InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION]);
        
        return $agreement->update();
    }

    function create_agreement()
    {
        $agreement = $this->agreement;
        $values = $this->exportValues();
        
        $dm = InternshipOrganizerDataManager::get_instance();
        
        $periods = $values[self :: PARAM_TARGET];
        
        foreach ($periods as $period_id) {
        	$period = $dm->retrieve_period($period_id);
        	dump($period);
        }
//        dump($values[self :: PARAM_TARGET]);
//        exit;
        
        $agreement->set_name($values[InternshipOrganizerAgreement :: PROPERTY_NAME]);
        $agreement->set_description($values[InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION]);
        
        return $agreement->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $agreement = $this->agreement;
        
        $defaults[InternshipOrganizerAgreement :: PROPERTY_NAME] = $agreement->get_name();
        $defaults[InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION] = $agreement->get_description();
        parent :: setDefaults($defaults);
    }
}
?>
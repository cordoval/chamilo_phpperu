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
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    
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
        
        //        $url = $this->get_path(WEB_PATH) . 'repository/xml_feed.php';
        //            $locale = array();
        //            $locale['Display'] = Translation :: get('AddPeriods');
        //            $locale['Searching'] = Translation :: get('Searching');
        //            $locale['NoResults'] = Translation :: get('NoResults');
        //            $locale['Error'] = Translation :: get('Error');
        //            
        //
        //            $elem = $this->addElement('element_finder', 'periods', Translation :: get('SelectPeriods'), $url, $locale, $attachments, $options);
        //            $this->addElement('category');
        //			$defaults = array();
        //            $elem->setDefaults($defaults);
        //             $elem->excludeElements(array($this->agreement->get_period_id()));
        //         
        //$elem->setDefaultCollapsed(count($attachments) == 0);
        

        $attributes = array();
        //        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        //        $attributes['search_url'] = $url;
        

        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        //        $this->add_(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('Period'), $attributes);
        

        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_period_feed.php';
//        dump($url);
        
        //        $periods = $this->parent->get_selected_periods();
         $url = Path :: get(WEB_PATH) . 'rights/xml_feeds/xml_rights_template_feed.php';

        $elem = $this->addElement('element_finder', 'periods', Translation :: get('Periods'), $url, $locale, array());
        $defaults = array();
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
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 1;
        
        parent :: setDefaults($defaults);
    }
}
?>
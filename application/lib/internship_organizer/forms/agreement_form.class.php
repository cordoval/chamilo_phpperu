<?php
require_once dirname(__FILE__) . '/../agreement.class.php';
require_once dirname(__FILE__) . '/../agreement_rel_user.class.php';

/**
 * This class describes the form for a Place object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipOrganizerAgreementForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'target_users';
    const PARAM_TARGET_PERIODS = 'target_periods';
    const PARAM_COORDINATORS = 'coordinators';
    const PARAM_COACHES = 'coaches';
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_SINGLE_PERIOD_CREATE = 3;
    
    private $agreement;
    private $user_ids;

    function InternshipOrganizerAgreementForm($form_type, $agreement, $action, $user_ids)
    {
        parent :: __construct('agreement_settings', 'post', $action);
        
        $this->agreement = $agreement;
        $this->user_ids = $user_ids;
        
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        elseif ($this->form_type == self :: TYPE_SINGLE_PERIOD_CREATE)
        {
            $this->build_single_period_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        
        $this->addElement('text', InternshipOrganizerAgreement :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(InternshipOrganizerAgreement :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
        
        $this->add_datepicker(InternshipOrganizerAgreement :: PROPERTY_BEGIN, Translation :: get('Begin'), false);
        $this->addRule(InternshipOrganizerAgreement :: PROPERTY_BEGIN, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_datepicker(InternshipOrganizerAgreement :: PROPERTY_END, Translation :: get('End'), false);
        $this->addRule(InternshipOrganizerAgreement :: PROPERTY_END, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->build_rights_settings();
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
        
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_period_feed.php';
        
        $locale = array();
        $locale['Display'] = Translation :: get('ChoosePeriods');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET_PERIODS, Translation :: get('Periods'), $url, $locale, array());
        
        $defaults = array();
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
        
        $element = $this->createElement('checkbox');
        
        $this->addElement('checkbox', self :: PARAM_COORDINATORS, Translation :: get('InternshipOrganizerAddCoordinators'));
        $this->addElement('checkbox', self :: PARAM_COACHES, Translation :: get('InternshipOrganizerAddCoaches'));
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_single_period_creation_form()
    {
        $this->build_basic_form();
        
        $element = $this->createElement('checkbox');
        
        $this->addElement('checkbox', self :: PARAM_COORDINATORS, Translation :: get('InternshipOrganizerAddCoordinators'));
        $this->addElement('checkbox', self :: PARAM_COACHES, Translation :: get('InternshipOrganizerAddCoaches'));
        
        $element = $this->createElement('hidden');
        $element->setName(InternshipOrganizerPeriodManager :: PARAM_USER_ID);
        $element->setValue(serialize($this->user_ids));
        $this->addElement($element);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_rights_settings()
    {
        
        $user_type_names = InternshipOrganizerUserType :: get_agreement_user_type_names();
        $rights = InternshipOrganizerRights :: get_available_rights_for_agreements();
        
        $default_right = $this->get_default_right_setings();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="caption" style="width: 30%;"></th>';
        
        foreach ($rights as $right_name => $right)
        {
            $name = Translation :: get(Utilities :: underscores_to_camelcase($right_name));
            $table_header[] = '<th class="center">' . strip_tags($name) . '</th>';
        }
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));
        
        foreach ($user_type_names as $i => $user_type_name)
        {
            
            if ($i != InternshipOrganizerUserType :: MENTOR)
            {
                
                $group = array();
                
                $group[] = $this->createElement('static', null, null, '<div style="text-align: left;">' . $user_type_name . '</div>');
                
                foreach ($rights as $right_name => $right)
                {
                    $checkbox_name = $right;
                    if ($default_right[$i][$right] == 1)
                    {
                        $checked = array('checked' => 'checked');
                    }
                    else
                    {
                        $checked = array();
                    }
                    
                    $checkbox = $this->createElement('checkbox', $checkbox_name, '', '', $checked);
                    $group[] = $checkbox;
                }
                
                $this->addGroup($group, $i, null, '', true);
                
                $this->defaultRenderer()->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', $i);
                $this->defaultRenderer()->setGroupElementTemplate('<td style="text-align: center;">{element}</td>', $i);
            }
        }
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));
    
    }

    private function get_default_right_setings()
    {
        $default_settings = array();
        
        $rights = InternshipOrganizerRights :: get_available_rights_for_agreements();
        
        $user_type_names = InternshipOrganizerUserType :: get_agreement_user_type_names();
        
        foreach ($user_type_names as $i => $user_type_name)
        {
            foreach ($rights as $right_name => $right)
            {
                if ($i = InternshipOrganizerUserType :: COORDINATOR)
                {
                    //all rights are set by default
                    $default_settings[$i][$right] = 1;
                }
                if ($i = InternshipOrganizerUserType :: COACH)
                {
                    if (in_array($right, array(InternshipOrganizerRights :: VIEW_AGREEMENT_RIGHT, InternshipOrganizerRights :: ADD_LOCATION_RIGHT, InternshipOrganizerRights :: APPROVE_LOCATION_RIGHT, InternshipOrganizerRights :: ADD_MENTOR_RIGHT, InternshipOrganizerRights :: ADD_MOMENT_RIGHT, InternshipOrganizerRights :: PUBLISH_RIGHT)))
                    {
                        $default_settings[$i][$right] = 1;
                    }
                
                }
                if ($i = InternshipOrganizerUserType :: STUDENT)
                {
                    if (in_array($right, array(InternshipOrganizerRights :: VIEW_AGREEMENT_RIGHT, InternshipOrganizerRights :: ADD_LOCATION_RIGHT, InternshipOrganizerRights :: ADD_MOMENT_RIGHT,InternshipOrganizerRights :: PUBLISH_RIGHT )))
                    {
                        $default_settings[$i][$right] = 1;
                    }
                }
            }
        }
        
        return $default_settings;
    }

    function update_agreement()
    {
        $agreement = $this->agreement;
        $values = $this->exportValues();
        
        $agreement->set_name($values[InternshipOrganizerAgreement :: PROPERTY_NAME]);
        $agreement->set_description($values[InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION]);
        $agreement->set_begin(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerAgreement :: PROPERTY_BEGIN]));
        $agreement->set_end(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerAgreement :: PROPERTY_END]));
        
        return $agreement->update();
    }

    function create_agreement()
    {
        $agreement = $this->agreement;
        $values = $this->exportValues();
        
        $period_ids = $values[self :: PARAM_TARGET_PERIODS]['period'];
        
        $succes = false;
        
        $dm = InternshipOrganizerDataManager :: get_instance();
        
        foreach ($period_ids as $period_id)
        {
            
            if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_USER_RIGHT, $period_id, InternshipOrganizerRights :: TYPE_PERIOD))
            {
                $agreement->set_period_id($period_id);
                $agreement->set_name($values[InternshipOrganizerAgreement :: PROPERTY_NAME]);
                $agreement->set_description($values[InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION]);
                $agreement->set_begin(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerAgreement :: PROPERTY_BEGIN]));
                $agreement->set_end(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerAgreement :: PROPERTY_END]));
                $agreement->set_status(InternshipOrganizerAgreement :: STATUS_ADD_LOCATION);
                
                $period = $dm->retrieve_period($period_id);
                
                $students_ids = $period->get_user_ids(InternshipOrganizerUserType :: STUDENT);
                
                foreach ($students_ids as $student_id)
                {
                    
                    $succes = $agreement->create();
                    
                    $location_id = InternshipOrganizerRights :: get_location_id_by_identifier_from_internship_organizers_subtree($agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT);
                    
                    foreach ($values[InternshipOrganizerUserType :: STUDENT] as $right => $value)
                    {
                        if ($value == 1)
                        {
                            RightsUtilities :: set_user_right_location_value($right, $student_id, $location_id, 1);
                        
                        }
                    }
                    
                    if ($succes)
                    {
                        $agreement_id = $agreement->get_id();
                        $agreement_rel_user = new InternshipOrganizerAgreementRelUser();
                        $agreement_rel_user->set_agreement_id($agreement_id);
                        $agreement_rel_user->set_user_id($student_id);
                        $agreement_rel_user->set_user_type(InternshipOrganizerUserType :: STUDENT);
                        $agreement_rel_user->create();
                        
                        if ($values[self :: PARAM_COORDINATORS] == 1)
                        {
                            $coordinators = $period->get_user_ids(InternshipOrganizerUserType :: COORDINATOR);
                            foreach ($coordinators as $coordinator_id)
                            {
                                $agreement_rel_user = new InternshipOrganizerAgreementRelUser();
                                $agreement_rel_user->set_agreement_id($agreement_id);
                                $agreement_rel_user->set_user_id($coordinator_id);
                                $agreement_rel_user->set_user_type(InternshipOrganizerUserType :: COORDINATOR);
                                $agreement_rel_user->create();
                                
                                foreach ($values[InternshipOrganizerUserType :: COORDINATOR] as $right => $value)
                                {
                                    if ($value == 1)
                                    {
                                        RightsUtilities :: set_user_right_location_value($right, $coordinator_id, $location_id, 1);
                                    
                                    }
                                }
                            
                            }
                        }
                        
                        if ($values[self :: PARAM_COACHES] == 1)
                        {
                            $coaches = $period->get_user_ids(InternshipOrganizerUserType :: COACH);
                            foreach ($coaches as $coach_id)
                            {
                                $agreement_rel_user = new InternshipOrganizerAgreementRelUser();
                                $agreement_rel_user->set_agreement_id($agreement_id);
                                $agreement_rel_user->set_user_id($coach_id);
                                $agreement_rel_user->set_user_type(InternshipOrganizerUserType :: COACH);
                                $agreement_rel_user->create();
                                
                                foreach ($values[InternshipOrganizerUserType :: COACH] as $right => $value)
                                {
                                    if ($value == 1)
                                    {
                                        RightsUtilities :: set_user_right_location_value($right, $coach_id, $location_id, 1);
                                    
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $succes;
    }

    function create_single_period_agreement()
    {
        $agreement = $this->agreement;
        $values = $this->exportValues();
        
        $ids = unserialize($values[InternshipOrganizerPeriodManager :: PARAM_USER_ID]);
        $id = explode('|', $ids[0]);
        $period_id = $id[0];
        $dm = InternshipOrganizerDataManager :: get_instance();
        $period = $dm->retrieve_period($period_id);
        $succes = false;
        
        $agreement->set_period_id($period_id);
        $agreement->set_name($values[InternshipOrganizerAgreement :: PROPERTY_NAME]);
        $agreement->set_description($values[InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION]);
        $agreement->set_begin(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerAgreement :: PROPERTY_BEGIN]));
        $agreement->set_end(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerAgreement :: PROPERTY_END]));
        $agreement->set_status(InternshipOrganizerAgreement :: STATUS_ADD_LOCATION);
        
        $student_ids = array();
        foreach ($ids as $id)
        {
            
            $period_user_ids = explode('|', $id);
            $student_ids[] = $period_user_ids[1];
        }
        
        foreach ($student_ids as $student_id)
        {
            
            $succes = $agreement->create();
            
            $location_id = InternshipOrganizerRights :: get_location_id_by_identifier_from_internship_organizers_subtree($agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT);
                      
            foreach ($values[InternshipOrganizerUserType :: STUDENT] as $right => $value)
            {
                
                if ($value == 1)
                {
                   RightsUtilities :: set_user_right_location_value($right, $student_id, $location_id, 1);
                
                }
            }
            
            if ($succes)
            {
                $agreement_id = $agreement->get_id();
                $agreement_rel_user = new InternshipOrganizerAgreementRelUser();
                $agreement_rel_user->set_agreement_id($agreement_id);
                $agreement_rel_user->set_user_id($student_id);
                $agreement_rel_user->set_user_type(InternshipOrganizerUserType :: STUDENT);
                $agreement_rel_user->create();
                
                if ($values[self :: PARAM_COORDINATORS] == 1)
                {
                    $coordinators = $period->get_user_ids(InternshipOrganizerUserType :: COORDINATOR);
                    foreach ($coordinators as $coordinator_id)
                    {
                        $agreement_rel_user = new InternshipOrganizerAgreementRelUser();
                        $agreement_rel_user->set_agreement_id($agreement_id);
                        $agreement_rel_user->set_user_id($coordinator_id);
                        $agreement_rel_user->set_user_type(InternshipOrganizerUserType :: COORDINATOR);
                        $agreement_rel_user->create();
                        
                        foreach ($values[InternshipOrganizerUserType :: COORDINATOR] as $right => $value)
                        {
                            if ($value == 1)
                            {
                                RightsUtilities :: set_user_right_location_value($right, $coordinator_id, $location_id, 1);
                            
                            }
                        }
                    
                    }
                }
                
                if ($values[self :: PARAM_COACHES] == 1)
                {
                    $coaches = $period->get_user_ids(InternshipOrganizerUserType :: COACH);
                    foreach ($coaches as $coach_id)
                    {
                        $agreement_rel_user = new InternshipOrganizerAgreementRelUser();
                        $agreement_rel_user->set_agreement_id($agreement_id);
                        $agreement_rel_user->set_user_id($coach_id);
                        $agreement_rel_user->set_user_type(InternshipOrganizerUserType :: COACH);
                        $agreement_rel_user->create();
                        
                        foreach ($values[InternshipOrganizerUserType :: COACH] as $right => $value)
                        {
                            if ($value == 1)
                            {
                                RightsUtilities :: set_user_right_location_value($right, $coach_id, $location_id, 1);
                            
                            }
                        }
                    }
                }
            }
        }
        
        return $succes;
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
        $defaults[InternshipOrganizerAgreement :: PROPERTY_BEGIN] = $agreement->get_begin();
        $defaults[InternshipOrganizerAgreement :: PROPERTY_END] = $agreement->get_end();
        $defaults[self :: PARAM_COACHES] = 1;
        $defaults[self :: PARAM_COORDINATORS] = 1;
        
        parent :: setDefaults($defaults);
    }
}
?>
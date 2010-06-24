<?php
require_once dirname(__FILE__) . '/../publication.class.php';
require_once dirname(__FILE__) . '/../user_type.class.php';
require_once dirname(__FILE__) . '/../publication_type.class.php';
require_once dirname(__FILE__) . '/../publication_place.class.php';


class InternshipOrganizerPeriodPublicationForm extends FormValidator
{
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;
    
    const PARAM_TARGET = 'periods';
    const PARAM_COORDINATORS = 'coordinators';
    const PARAM_COACHES = 'coaches';
    const PARAM_STUDENTS = 'students';
    
    private $publication;
    private $content_object;
    private $user;

    function InternshipOrganizerPeriodPublicationForm($form_type, $content_object, $user, $action)
    {
        parent :: __construct('period_publication_settings', 'post', $action);
      
        $this->content_object = $content_object;
        $this->user = $user;
        $this->form_type = $form_type;
        
        switch ($this->form_type)
        {
            case self :: TYPE_SINGLE :
                $this->build_single_form();
                break;
            case self :: TYPE_MULTI :
                $this->build_multi_form();
                break;
        }
        
        $this->add_footer();
        $this->setDefaults();
    }

    function build_single_form()
    {
        $this->build_form();
    }

    function build_multi_form()
    {
        $this->build_form();
        $this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    function build_form()
    {
        
        $this->addElement('select', InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE, Translation :: get('InternshipOrganizerTypeOfPublication'), $this->get_type_of_documents());
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_REGION_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('checkbox', self :: PARAM_COORDINATORS, Translation :: get('InternshipOrganizerCoordinators'));
        $this->addElement('checkbox', self :: PARAM_COACHES, Translation :: get('InternshipOrganizerCoaches'));
        $this->addElement('checkbox', self :: PARAM_STUDENTS, Translation :: get('InternshipOrganizerStudents'));
        
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_period_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ChoosePeriods');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET, Translation :: get('Periods'), $url, $locale, array());
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
    
    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function create_content_object_publications()
    {
        $values = $this->exportValues();
        
        $user_types = array();
        if ($values[self :: PARAM_COORDINATORS])
        {
            $user_types[] = InternshipOrganizerUserType :: COORDINATOR;
        }
        if ($values[self :: PARAM_COACHES])
        {
            $user_types[] = InternshipOrganizerUserType :: COACH;
        }
        if ($values[self :: PARAM_STUDENTS])
        {
            $user_types[] = InternshipOrganizerUserType :: STUDENT;
        }
        
        $period_ids = $values[self :: PARAM_TARGET];
        $ids = unserialize($values['ids']);
        $succes = false;
        if (count($user_types))
        {
            if (count($period_ids))
            {
                
                foreach ($period_ids as $period_id)
                {
                    $period = InternshipOrganizerDataManager :: get_instance()->retrieve_period($period_id);
                    
                    $target_users = array();
                    $type_index = $conditions = array();
                    $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $period_id);
                    $conditions[] = new InCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $user_types);
                    $condition = new AndCondition($conditions);
                    
                    $period_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_period_rel_users($condition);
                    
                    while ($period_rel_user = $period_rel_users->next_result())
                    {
                        $target_users[] = $period_rel_user->get_user_id();
                    }
                    
                    $target_groups = array();
                    $period_rel_groups = InternshipOrganizerDataManager :: get_instance()->retrieve_period_rel_groups($condition);
                    
                    while ($period_rel_group = $period_rel_groups->next_result())
                    {
                        $target_groups[] = $period_rel_group->get_group_id();
                    }
                    
                    foreach ($ids as $id)
                    {
                        $pub = new InternshipOrganizerPublication();
                        $pub->set_content_object($id);
                        $pub->set_publisher_id($this->user->get_id());
                        $pub->set_published(time());
                        $pub->set_from_date($period->get_begin());
                        $pub->set_to_date($period->get_end());
                        $pub->set_publication_place(InternshipOrganizerPublicationPlace :: PERIOD);
                        $pub->set_place_id($period->get_id());
                        $pub->set_publication_type(InternshipOrganizerPublicationType :: CONTRACT);
                        $pub->set_target_users($target_users);
                        $pub->set_target_groups($target_groups);
                        
                        if (! $pub->create())
                        {
                            $succes = false;
                        }
                        else
                        {
                            $succes = true;
                        }
                    }
                }
            }
        }
        
        return $succes;
    }

    private function get_type_of_documents()
    {
        $type_of_publications = array();
        $type_of_publications[InternshipOrganizerPublicationType :: CONTRACT] = InternshipOrganizerPublicationType :: get_publication_type_name(InternshipOrganizerPublicationType :: CONTRACT);
        $type_of_publications[InternshipOrganizerPublicationType :: GENERAL] = InternshipOrganizerPublicationType :: get_publication_type_name(InternshipOrganizerPublicationType :: GENERAL);
        $type_of_publications[InternshipOrganizerPublicationType :: INFO] = InternshipOrganizerPublicationType :: get_publication_type_name(InternshipOrganizerPublicationType :: INFO);
        return $type_of_publications;
    }

    /**
     * Sets the default values of the form.
     *
     * By default the publication is for everybody who has access to the tool
     * and the publication will be available forever.
     */
    function setDefaults()
    {
        $defaults = array();
        $defaults[InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE] = InternshipOrganizerPublicationType :: GENERAL;
        parent :: setDefaults($defaults);
    }
}
?>
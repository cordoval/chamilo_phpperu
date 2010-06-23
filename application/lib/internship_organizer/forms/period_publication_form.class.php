<?php
require_once dirname(__FILE__) . '/../publication.class.php';
require_once dirname(__FILE__) . '/../user_type.class.php';

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
        
        //        $testcase = Request :: get(SurveyManager :: PARAM_TESTCASE);
        

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

    //    /**
    //     * Sets the publication. Use this function if you're using this form to
    //     * change the settings of a learning object publication.
    //     * @param ContentObjectPublication $publication
    //     */
    //    function set_publication($publication)
    //    {
    //        $this->publication = $publication;
    //        $this->addElement('hidden', 'pid');
    //        $this->addElement('hidden', 'action');
    //        $defaults['action'] = 'edit';
    //        $defaults['pid'] = $publication->get_id();
    //        $defaults['from_date'] = $publication->get_from_date();
    //        $defaults['to_date'] = $publication->get_to_date();
    //        if ($defaults['from_date'] != 0)
    //        {
    //            $defaults['forever'] = 0;
    //        }
    //        
    //        $defaults['hidden'] = $publication->is_hidden();
    //        
    //        $udm = UserDataManager :: get_instance();
    //        $gdm = GroupDataManager :: get_instance();
    //        
    //        $target_groups = $this->publication->get_target_groups();
    //        $target_users = $this->publication->get_target_users();
    //        
    //        $defaults[self :: PARAM_TARGET_ELEMENTS] = array();
    //        foreach ($target_groups as $target_group)
    //        {
    //            $group = $gdm->retrieve_group($target_group);
    //            
    //            $selected_group = array();
    //            $selected_group['id'] = 'group_' . $group->get_id();
    //            $selected_group['classes'] = 'type type_group';
    //            $selected_group['title'] = $group->get_name();
    //            $selected_group['description'] = $group->get_description();
    //            
    //            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_group['id']] = $selected_group;
    //        }
    //        foreach ($target_users as $target_user)
    //        {
    //            $user = $udm->retrieve_user($target_user);
    //            
    //            $selected_user = array();
    //            $selected_user['id'] = 'user_' . $user->get_id();
    //            $selected_user['classes'] = 'type type_user';
    //            $selected_user['title'] = $user->get_fullname();
    //            $selected_user['description'] = $user->get_username();
    //            
    //            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_user['id']] = $selected_user;
    //        }
    //        
    //        if (count($defaults[self :: PARAM_TARGET_ELEMENTS]) > 0)
    //        {
    //            $defaults[self :: PARAM_TARGET_OPTION] = '1';
    //        }
    //        
    //        $active = $this->getElement(self :: PARAM_TARGET_ELEMENTS);
    //        $active->_elements[0]->setValue(serialize($defaults[self :: PARAM_TARGET_ELEMENTS]));
    //        
    //        parent :: setDefaults($defaults);
    //    }
    

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
        $this->addElement('checkbox', self :: PARAM_COORDINATORS, Translation :: get('Coordinators'));
        $this->addElement('checkbox', self :: PARAM_COACHES, Translation :: get('Coaches'));
        $this->addElement('checkbox', self :: PARAM_STUDENTS, Translation :: get('Students'));
        
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

    /**
     * Creates a learning object publication using the values from the form.
     * @return ContentObjectPublication The new publication
     */
    function create_content_object_publication()
    {
        $values = $this->exportValues();
        
        dump($values);
        exit();
        
        $hidden = ($values[InternshipOrganizerPublication :: PROPERTY_HIDDEN] ? 1 : 0);
        
        if ($values[self :: PARAM_TARGET_OPTION] != 0)
        {
            $user_ids = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
            $group_ids = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
        }
        else
        {
            $users = UserDataManager :: get_instance()->retrieve_users();
            $user_ids = array();
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }
        }
        
        $pub = new InternshipOrganizerPublication();
        $pub->set_content_object($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_hidden($hidden);
        $pub->set_test($this->testcase);
        $pub->set_target_users($user_ids);
        $pub->set_target_groups($group_ids);
        
        if ($pub->create())
        {
            return true;
        }
        else
        {
            return false;
        }
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
                        $pub->set_publisher($this->user->get_id());
                        $pub->set_published(time());
                        $pub->set_from_date($period->get_begin());
                        $pub->set_to_date($period->get_end());
                        
                        
                        $pub->set_target_users($target_users);
                        $pub->set_target_groups($target_groups);
                        
                        if (! $pub->create())
                        {
                            $succes = false;
                        }else{
                        	$succes = true;
                        }
                        
                    }
                
                }
            }
        }
        
       return $succes;
    }

    //    function update_content_object_publication()
    //    {
    //        $values = $this->exportValues();
    //        
    //        if ($values[self :: PARAM_FOREVER] != 0)
    //        {
    //            $from = $to = 0;
    //        }
    //        else
    //        {
    //            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
    //            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
    //        }
    //        $hidden = ($values[InternshipOrganizerPublication :: PROPERTY_HIDDEN] ? 1 : 0);
    //        
    //        if ($values[self :: PARAM_TARGET_OPTION] != 0)
    //        {
    //            $user_ids = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
    //            $group_ids = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
    //        }
    //        else
    //        {
    //            $users = UserDataManager :: get_instance()->retrieve_users();
    //            $user_ids = array();
    //            while ($user = $users->next_result())
    //            {
    //                $user_ids[] = $user->get_id();
    //            }
    //        }
    //        
    //        $pub = $this->publication;
    //        $pub->set_from_date($from);
    //        $pub->set_to_date($to);
    //        $pub->set_hidden($hidden);
    //        
    //        $pub->set_target_users($user_ids);
    //        $pub->set_target_groups($group_ids);
    //        return $pub->update();
    //    
    //    }
    

    /**
     * Sets the default values of the form.
     *
     * By default the publication is for everybody who has access to the tool
     * and the publication will be available forever.
     */
    function setDefaults()
    {
        $defaults = array();
        parent :: setDefaults($defaults);
    }
}
?>
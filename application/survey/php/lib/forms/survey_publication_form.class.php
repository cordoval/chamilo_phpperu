<?php
require_once dirname(__FILE__) . '/../survey_publication.class.php';

class SurveyPublicationForm extends FormValidator
{
    const TYPE_EDIT = 1;
    const TYPE_CREATE = 2;
    
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';
    const PARAM_PARTICIPATE = 'participate';
    
    private $publication;
    private $publication_type;
    private $content_object;
    private $user;

    function SurveyPublicationForm($form_type, $content_object, $user, $action, $publication)
    {
        parent :: __construct('survey_publication_settings', 'post', $action);
        
        $this->content_object = $content_object;
        $this->user = $user;
        $this->publication = $publication;
        $this->form_type = $form_type;
        
        switch ($this->form_type)
        {
            case self :: TYPE_EDIT :
                $this->build_edit_form();
                break;
            case self :: TYPE_CREATE :
                $this->build_create_form();
                break;
        }
        
        $this->add_footer();
        $this->setDefaults();
    }

    function build_edit_form()
    {
        
        $checkbox = $this->createElement('checkbox', self :: PARAM_PARTICIPATE, Translation :: get('ParticipateYourself'), '', array());
        $this->addElement($checkbox);
        $this->add_forever_or_timewindow();
        $this->add_select(SurveyPublication :: PROPERTY_TYPE, Translation :: get('SurveyType'), SurveyPublication :: get_types());
    }

    function build_create_form()
    {
        
        $checkbox = $this->createElement('checkbox', self :: PARAM_PARTICIPATE, '', Translation :: get('ParticipateYourself'), array());
        $this->addElement($checkbox);
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $this->add_receivers(self :: PARAM_TARGET, Translation :: get('Participants'), $attributes);
        
        $this->add_forever_or_timewindow();
        
        $this->add_select(SurveyPublication :: PROPERTY_TYPE, Translation :: get('SurveyType'), SurveyPublication :: get_types());
        $this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function update_publication()
    {
        $values = $this->exportValues();
        
        $this->publication_type = $values[SurveyPublication :: PROPERTY_TYPE];
        
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        
        $publication = $this->publication;
        $publication->set_from_date($from);
        $publication->set_to_date($to);
        $publication->set_type($this->publication_type);
        
        if ($publication->update())
        {
            $location_id = SurveyRights :: get_location_id_by_identifier_from_surveys_subtree($publication->get_id(), SurveyRights :: TYPE_PUBLICATION);
            
            if ($values[self :: PARAM_PARTICIPATE] == 1)
            {
                RightsUtilities :: set_user_right_location_value(SurveyRights :: RIGHT_PARTICIPATE, $this->user->get_id(), $location_id, 1);
            }
            else
            {
                RightsUtilities :: set_user_right_location_value(SurveyRights :: RIGHT_PARTICIPATE, $this->user->get_id(), $location_id, 0);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_publications()
    {
        $values = $this->exportValues();
        
        $this->publication_type = $values[SurveyPublication :: PROPERTY_TYPE];
        
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        
        if ($values[self :: PARAM_TARGET_OPTION] != 0)
        {
            $user_ids = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
            
            $group_ids = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
            $group_user_ids = array();
            foreach ($group_ids as $group_id)
            {
                
                $group = GroupDataManager :: get_instance()->retrieve_group($group_id);
                $ids = $group->get_users(true, true);
                $group_user_ids = array_merge($group_user_ids, $ids);
            
            }
            if (count($user_ids))
            {
                $user_ids = array_merge($user_ids, $group_user_ids);
            }
            else
            {
                $user_ids = $group_user_ids;
            }
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
        
        $user_ids = array_unique($user_ids);
        
        $ids = unserialize($values['ids']);
        
        $succes = false;
        
        foreach ($ids as $id)
        {
            $publication = new SurveyPublication();
            $publication->set_content_object($id);
            $publication->set_publisher($this->user->get_id());
            $publication->set_published(time());
            $publication->set_from_date($from);
            $publication->set_to_date($to);
            $publication->set_type($this->publication_type);
            
            if (! $publication->create())
            {
                $succes = false;
            }
            else
            {
                $location_id = SurveyRights :: get_location_id_by_identifier_from_surveys_subtree($publication->get_id(), SurveyRights :: TYPE_PUBLICATION);
                
                if (count($user_ids))
                {
                    foreach ($user_ids as $user_id)
                    {
                        $succes = RightsUtilities :: set_user_right_location_value(SurveyRights :: RIGHT_PARTICIPATE, $user_id, $location_id, 1);
                    }
                }
                if ($values[self :: PARAM_PARTICIPATE] == 1)
                {
                    $succes = RightsUtilities :: set_user_right_location_value(SurveyRights :: RIGHT_PARTICIPATE, $this->user->get_id(), $location_id, 1);
                }
                else
                {
                    $succes = true;
                }
            }
        }
        
        return $succes;
    }

    function get_publication_type()
    {
        return $this->publication_type;
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
        if (! $this->publication)
        {
            $defaults[self :: PARAM_TARGET_OPTION] = 1;
            $defaults[self :: PARAM_FOREVER] = 1;
        }
        else
        {
            $defaults[SurveyPublication :: PROPERTY_TYPE] = $this->publication->get_type();
            
            if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PARTICIPATE, $this->publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $this->user->get_id()))
            {
                $defaults[self :: PARAM_PARTICIPATE] = 1;
            }
            
            if ($this->publication->get_from_date() == 0)
            {
                $defaults[self :: PARAM_FOREVER] = 1;
            }
            else
            {
                $defaults[self :: PARAM_FOREVER] = 0;
                $defaults[self :: PARAM_FROM_DATE] = $this->publication->get_from_date();
                $defaults[self :: PARAM_TO_DATE] = $this->publication->get_to_date();
            }
        
        }
        
        parent :: setDefaults($defaults);
    }
}
?>
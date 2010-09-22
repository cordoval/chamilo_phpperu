<?php
require_once dirname(__FILE__) . '/../survey_publication.class.php';

class SurveyPublicationForm extends FormValidator
{
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;
    
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';
    
    private $publication;
    private $content_object;
    private $user;

    function SurveyPublicationForm($form_type, $content_object, $user, $action)
    {
        parent :: __construct('survey_publication_settings', 'post', $action);
        
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
        $targets = array();
        
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
        
        $this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        
        $this->add_forever_or_timewindow();
        
        $this->add_select(SurveyPublication :: PROPERTY_TYPE, Translation :: get('SurveyType'), SurveyPublication :: get_types());
    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function create_content_object_publication()
    {
        $values = $this->exportValues();
        
        $type = $values[SurveyPublication :: PROPERTY_TYPE];
        
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[SurveyPublication :: PROPERTY_HIDDEN] ? 1 : 0);
        
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
        
        $pub = new SurveyPublication();
        $pub->set_content_object($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_type($type);
        
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
        
        $type = $values[SurveyPublication :: PROPERTY_TYPE];
        
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
        
        $ids = unserialize($values['ids']);
        
        foreach ($ids as $id)
        {
            $pub = new SurveyPublication();
            $pub->set_content_object($id);
            $pub->set_publisher($this->user->get_id());
            $pub->set_published(time());
            $pub->set_from_date($from);
            $pub->set_to_date($to);
            $pub->set_type($type);
            //$pub->set_target_users($user_ids);
            //$pub->set_target_groups($group_ids);
            

            if (! $pub->create())
            {
                return false;
            }
        }
        return true;
    }

    function update_content_object_publication()
    {
        $values = $this->exportValues();
        
        $type = $values[SurveyPublication :: PROPERTY_TYPE];
        
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        
        $pub = $this->publication;
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_type($type);
        return $pub->update();
    
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
        $defaults[self :: PARAM_TARGET_OPTION] = 1;
        $defaults[self :: PARAM_FOREVER] = 1;
        parent :: setDefaults($defaults);
    }
}
?>
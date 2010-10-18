<?php
/**
 * $Id: webconference_form.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.forms
 */
require_once dirname(__FILE__) . '/../webconference.class.php';
require_once dirname(__FILE__) . '/../webconference_option.class.php';

/**
 * This class describes the form for a Webconference object.
 * @author Stefaan Vanbillemont
 **/
class WebconferenceForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';
    
    private $webconference;
    private $user;

    function WebconferenceForm($form_type, $webconference, $action, $user)
    {
        parent :: __construct('webconference_settings', 'post', $action);
        
        $this->webconference = $webconference;
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
        
    //$this->setDefaults();
    }
    
    private $defaults_create;
    private $defaults_update;

    function build_basic_form()
    {
        $this->addElement('text', Webconference :: PROPERTY_CONFNAME, Translation :: get('Confname'));
        $this->addRule(Webconference :: PROPERTY_CONFNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $value = PlatformSetting :: get('description_required', 'repository');
        $required = ($value == 'true') ? true : false;
        $this->add_html_editor(Webconference :: PROPERTY_DESCRIPTION, Translation :: get('Description'), $required);
        $this->addRule(Webconference :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
        
        if (PlatformSetting :: get('allow_duration_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
        {
            $this->addElement('text', Webconference :: PROPERTY_DURATION, Translation :: get('DurationInMinutes'));
            $this->defaults_create[Webconference :: PROPERTY_DURATION] = PlatformSetting :: get('default_webconference_duration', WebconferencingManager :: APPLICATION_NAME);
            $this->addRule(Webconference :: PROPERTY_DURATION, Translation :: get('ThisFieldIsRequired'), 'required');
            $this->addRule(Webconference :: PROPERTY_DURATION, Translation :: get('ValueShouldBeNumeric'), 'numeric');
        }
        
        $this->add_html_editor('option[agenda]', Translation :: get('Agenda'), false);
        
        if (PlatformSetting :: get('allow_network_quality_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
        {
            $network_options = array('L' => Translation :: get('Low'), 'M' => Translation :: get('Medium'), 'H' => Translation :: get('High'));
            $this->addElement('select', 'option[network]', Translation :: get('NetworkQuality'), $network_options);
            $this->defaults_create['option[network]'] = PlatformSetting :: get('default_network_quality', WebconferencingManager :: APPLICATION_NAME);
        }
        
        if (PlatformSetting :: get('allow_audiovideo_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
        {
            $audio_video_options = array('A' => Translation :: get('Audio'), 'X' => Translation :: get('VideoOnly'), 'V' => Translation :: get('AudioVideoAllowed'), 'D' => Translation :: get('AudioVideoDisabled'));
            $this->addElement('select', 'option[audioVideo]', Translation :: get('AudioVideo'), $audio_video_options);
            $this->defaults_create['option[audioVideo]'] = PlatformSetting :: get('default_audio_video', WebconferencingManager :: APPLICATION_NAME);
        }
        
        if (PlatformSetting :: get('allow_mikes_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
        {
            $mike_options = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);
            $this->addElement('select', 'option[mikes]', Translation :: get('Mikes'), $mike_options);
            $this->defaults_create['option[mikes]'] = PlatformSetting :: get('default_mikes', WebconferencingManager :: APPLICATION_NAME);
        }
        
        if (PlatformSetting :: get('allow_moderatorpasscode_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
        {
            $this->addElement('text', 'option[moderatorPassCode]', Translation :: get('ModeratorPassCode'));
            $this->defaults_create['option[moderatorPassCode]'] = PlatformSetting :: get('default_moderatorpasscode', WebconferencingManager :: APPLICATION_NAME);
        }
        
        if (PlatformSetting :: get('allow_attendeepasscode_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
        {
            $this->addElement('text', 'option[attendeePassCode]', Translation :: get('AttendeePassCode'));
            $this->defaults_create['option[attendeePassCode]'] = PlatformSetting :: get('default_attendeepasscode', WebconferencingManager :: APPLICATION_NAME);
        }
        
        if (PlatformSetting :: get('allow_presenterpwd_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
        {
            $this->addElement('text', 'option[presenterPwd]', Translation :: get('PresenterPwd'));
            $this->defaults_create['option[presenterPwd]'] = PlatformSetting :: get('default_presenterpwd', WebconferencingManager :: APPLICATION_NAME);
        }
        
        if (PlatformSetting :: get('allow_attendeepwd_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
        {
            $this->addElement('text', 'option[attendeePwd]', Translation :: get('AttendeePwd'));
            $this->defaults_create['option[attendeePwd]'] = PlatformSetting :: get('default_attendeepwd', WebconferencingManager :: APPLICATION_NAME);
        }
        
        $yes_no_items = array('waitingarea', 'featureWhiteboard', 'featurePublisher', 'featurePrivateChat', 'featurePublicChat', 'featureDocShare', 'featureCobShare', 'featureRecording', 'feedback', 'participantList', 'AssignMikeOnJoin', 'HandsFreeOnLoad', 'allowAttendeeInvites');
        
        foreach ($yes_no_items as $yes_no_item)
        {
            $group = array();
            $group[] = & $this->createElement('radio', $yes_no_item, null, Translation :: get('Yes'), 'true');
            $group[] = & $this->createElement('radio', $yes_no_item, null, Translation :: get('No'), 'false');
            if (PlatformSetting :: get('allow_' . strtolower($yes_no_item) . '_selection', WebconferencingManager :: APPLICATION_NAME) == 'true')
            {
                $this->addGroup($group, 'option', Translation :: get('Option' . Utilities :: underscores_to_camelcase($yes_no_item)), '&nbsp;');
                $this->defaults_create['option[' . $yes_no_item . ']'] = PlatformSetting :: get('default_' . strtolower($yes_no_item), WebconferencingManager :: APPLICATION_NAME);
                $this->defaults_update['option[' . $yes_no_item . ']'] = 'false';
            }
        }
        
        $targets = array();
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        $attributes['defaults'] = array();
        
        $this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', Webconference :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', Webconference :: PROPERTY_ID);
        

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaults($this->defaults_update);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->setDefaults($this->defaults_create);
    }

    function update_webconference()
    {
        $webconference = $this->webconference;
        $values = $this->exportValues();
        
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[Webconference :: PROPERTY_HIDDEN] ? 1 : 0);
        
        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
        
        $webconference->set_confname($values[Webconference :: PROPERTY_CONFNAME]);
        $webconference->set_description($values[Webconference :: PROPERTY_DESCRIPTION]);
        $webconference->set_duration($values[Webconference :: PROPERTY_DURATION]);
        $webconference->set_from_date($from);
        $webconference->set_to_date($to);
        $webconference->set_hidden($hidden);
        $webconference->set_target_users($users);
        $webconference->set_target_groups($groups);
        
        //delete all webconference_options
        WebconferencingDataManager :: get_instance()->delete_webconference_options($webconference);
        $options = $values['option'];
        
        foreach ($options as $name => $value)
        {
            if (! $value || ! $name)
                continue;
            
            $webconference_item = new WebconferenceOption();
            $webconference_item->set_conf_id($webconference->get_id());
            $webconference_item->set_name($name);
            $webconference_item->set_value($value);
            $webconference_item->create();
        }
        
        return $webconference->update();
    }

    function create_webconference()
    {
        $webconference = $this->webconference;
        $values = $this->exportValues();
        
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[Webconference :: PROPERTY_HIDDEN] ? 1 : 0);
        
        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
        
        $build_conf_key = md5(time());
        $webconference->set_confkey($build_conf_key);
        $webconference->set_confname($values[Webconference :: PROPERTY_CONFNAME]);
        $webconference->set_description($values[Webconference :: PROPERTY_DESCRIPTION]);
        $webconference->set_duration($values[Webconference :: PROPERTY_DURATION]);
        $webconference->set_from_date($from);
        $webconference->set_to_date($to);
        $webconference->set_hidden($hidden);
        $webconference->set_target_users($users);
        $webconference->set_target_groups($groups);
        $webconference->create();
        
        $options = $values['option'];
        
        foreach ($options as $name => $value)
        {
            if (! $value || ! $name)
                continue;
            
            $webconference_item = new WebconferenceOption();
            $webconference_item->set_conf_id($webconference->get_id());
            $webconference_item->set_name($name);
            $webconference_item->set_value($value);
            $webconference_item->create();
        }
        
        return true;
    }

    function remove_XSS_recursive()
    {
    
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $webconference = $this->webconference;
        
        $defaults[Webconference :: PROPERTY_ID] = $webconference->get_id();
        $defaults[Webconference :: PROPERTY_CONFNAME] = $webconference->get_confname();
        $defaults[Webconference :: PROPERTY_DESCRIPTION] = $webconference->get_description();
        $defaults[Webconference :: PROPERTY_HIDDEN] = $webconference->is_hidden();
        
        $defaults['from_date'] = $webconference->get_from_date();
        $defaults['to_date'] = $webconference->get_to_date();
        $defaults['forever'] = ($defaults['from_date'] != 0) ? 0 : 1;
        
        //loop all webconference_options and place them in defaults
        if ($webconference)
        {
            $duration = $webconference->get_duration();
            if ($duration)
                $defaults[Webconference :: PROPERTY_DURATION] = $duration;
                
            //loop all webconference_options and place them in defaults
            $wdm = WebconferencingDataManager :: get_instance();
            $options = $wdm->retrieve_webconference_options(new EqualityCondition(WebconferenceOption :: PROPERTY_CONF_ID, $webconference->get_id()));
            while ($option = $options->next_result())
            {
                $defaults['option[' . $option->get_name() . ']'] = $option->get_value();
            }
        }
        
        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();
        
        $target_groups = $webconference->get_target_groups();
        $target_users = $webconference->get_target_users();
        
        $defaults[self :: PARAM_TARGET_ELEMENTS] = array();
        foreach ($target_groups as $target_group)
        {
            $group = $gdm->retrieve_group($target_group);
            
            $selected_group = array();
            $selected_group['id'] = 'group_' . $group->get_id();
            $selected_group['classes'] = 'type type_group';
            $selected_group['title'] = $group->get_name();
            $selected_group['description'] = $group->get_description();
            
            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_group['id']] = $selected_group;
        }
        foreach ($target_users as $target_user)
        {
            $user = $udm->retrieve_user($target_user);
            
            $selected_user = array();
            $selected_user['id'] = 'user_' . $user->get_id();
            $selected_user['classes'] = 'type type_user';
            $selected_user['title'] = $user->get_fullname();
            $selected_user['description'] = $user->get_username();
            
            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_user['id']] = $selected_user;
        }
        
        if (count($defaults[self :: PARAM_TARGET_ELEMENTS]) > 0)
        {
            $defaults[self :: PARAM_TARGET_OPTION] = '1';
        }
        
        $active = $this->getElement(self :: PARAM_TARGET_ELEMENTS);
        $active->_elements[0]->setValue(serialize($defaults[self :: PARAM_TARGET_ELEMENTS]));
        
        parent :: setDefaults($defaults);
    }
}
?>
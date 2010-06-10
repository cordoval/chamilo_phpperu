<?php

class InternshipOrganizerSubscribeUsersForm extends FormValidator
{
    
//    const TYPE_CREATE = 1;
//    const TYPE_EDIT = 2;
//    const RESULT_SUCCESS = 'InternshipOrganizerPeriodUpdated';
//    const RESULT_ERROR = 'InternshipOrganizerPeriodUpdateFailed';
    
	const APPLICATION_NAME = 'internship_organizer';
	const PARAM_TARGET = 'target_users_and_groups';
	const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
	
    private $parent;
    private $period;
   	private $user;

    function InternshipOrganizerSubscribeUsersForm($period, $action, $user)
    {
        parent :: __construct('create_period', 'post', $action);
        
        $this->period = $period;
        $this->user = $user;
        //$this->form_type = $form_type;
//        if ($this->form_type == self :: TYPE_EDIT)
//        {
//            $this->build_editing_form();
//        }
//        elseif ($this->form_type == self :: TYPE_CREATE)
//        {
//            $this->build_creation_form();
//        }
//        
//        $this->setDefaults();

        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {       
    	$period = $this->period;
        $parent = $this->parent; 
    
        
        
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
    	
        $this->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('category');
        $this->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $this->setDefaults($defaults);
        
    	

    }

//    function build_editing_form()
//    {
//        $period = $this->period;
//        $parent = $this->parent;
//        
//        $this->build_basic_form();
//        
//        $this->addElement('hidden', InternshipOrganizerPeriod :: PROPERTY_ID);
//        
//        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
//        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
//        
//        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
//    }
//
//    function build_creation_form()
//    {
//        $this->build_basic_form();
//        
//        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
//        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
//        
//        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
//    }
//
//    function update_period()
//    {
//        $period = $this->period;
//        $values = $this->exportValues();
//        
//        $period->set_name($values[InternshipOrganizerPeriod :: PROPERTY_NAME]);
//        $period->set_description($values[InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION]);
//        $value = $period->update();
//        
//        $new_parent = $values[InternshipOrganizerPeriod :: PROPERTY_PARENT_ID];
//        if ($period->get_parent_id() != $new_parent)
//        {
//            $period->move($new_parent);
//        }
               
//        if ($value)
//        {
//            Events :: trigger_event('update', 'period', array('target_period_id' => $period->get_id(), 'action_user_id' => $this->user->get_id()));
//        }
//        
//        return $value;
//    }
//
//    function create_period()
//    {
//        $period = $this->period;
//        $values = $this->exportValues();
//        
//        $period->set_name($values[InternshipOrganizerPeriod :: PROPERTY_NAME]);
//        $period->set_description($values[InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION]);
//        $period->set_begin(Utilities :: time_from_datepicker_without_timepicker( $values[InternshipOrganizerPeriod :: PROPERTY_BEGIN]));
//        $period->set_end(Utilities :: time_from_datepicker_without_timepicker($values[InternshipOrganizerPeriod :: PROPERTY_END]));
//        $period->set_parent_id($values[InternshipOrganizerPeriod :: PROPERTY_PARENT_ID]);
//        
//        $value = $period->create();
//               
//        if ($value)
//        {
//            Events :: trigger_event('create', 'period', array('target_period_id' => $period->get_id(), 'action_user_id' => $this->user->get_id()));
//        }
//        
//        return $value;
//    }
//
//    /**
//     * Sets default values.
//     * @param array $defaults Default values for this form's parameters.
//     */
//    function setDefaults($defaults = array ())
//    {
//        $period = $this->period;
//        $defaults[InternshipOrganizerPeriod :: PROPERTY_ID] = $period->get_id();
//        $defaults[InternshipOrganizerPeriod :: PROPERTY_PARENT_ID] = $period->get_parent_id();
//        $defaults[InternshipOrganizerPeriod :: PROPERTY_NAME] = $period->get_name();
//        $defaults[InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION] = $period->get_description();
//        $defaults[InternshipOrganizerPeriod :: PROPERTY_BEGIN] = $period->get_begin();
//        $defaults[InternshipOrganizerPeriod :: PROPERTY_END] = $period->get_end();
//        parent :: setDefaults($defaults);
//    }
//
//    function get_period()
//    {
//        return $this->period;
//    }
//
//    function get_periods()
//    {
//        $period = $this->period;
//        
//        $period_menu = new InternshipOrganizerPeriodMenu($period->get_id(), null, true, true, true);
//        $renderer = new OptionsMenuRenderer();
//        $period_menu->render($renderer, 'sitemap');
//        return $renderer->toArray();
//    }
}

?>
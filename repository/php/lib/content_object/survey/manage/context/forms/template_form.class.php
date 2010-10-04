<?php

class SurveyTemplateForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'TemplateUpdated';
    const RESULT_ERROR = 'TemplateUpdateFailed';
    
    const PARAM_TARGET_LEVEL = 'level';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_APPLICATION_NAME = 'survey_context_manager';
    
    private $survey_template;
    private $user;
    private $form_type;
    private $manager;

    /**
     * Creates a new LanguageForm
     */
    function SurveyTemplateForm($form_type, $action, $survey_template, $user, $manager)
    {
        parent :: __construct('survey_template_form', 'post', $action);
        
        $this->survey_template = $survey_template;
        
        $this->user = $user;
        $this->form_type = $form_type;
        $this->manager = $manager;
        
        $this->build_header();
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        else
        {
            $this->build_creation_form();
        }
        $this->setDefaults();
    }

    function build_header()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('SurveyTemplateProperties') . '</span>');
    }

    function build_footer($action_name)
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        $buttons[] = $this->createElement('style_submit_button', 'create', Translation :: get($action_name), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/survey_template_registration_form.js'));
    }

    /**
     * Creates a new basic form
     */
    function build_creation_form()
    {
        
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
        
        $this->add_receivers(self :: PARAM_APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('Users'), $attributes);
        
        $survey_template = $this->survey_template;
        $property_names = $survey_template->get_additional_property_names(true);
        
        foreach ($property_names as $property_name => $type)
        {
            $labels = explode('_', $property_name);
            $label = $labels[0] . ' ' . $labels[1];
            $url = Path :: get(WEB_PATH) . 'repository/lib/content_object/survey/xml_feeds/xml_context_feed.php?context_type=' . $type;
            
            $locale = array();
            $locale['Display'] = Translation :: get('Choose');
            $locale['Searching'] = Translation :: get('Searching');
            $locale['NoResults'] = Translation :: get('NoResults');
            $locale['Error'] = Translation :: get('Error');
            
            $elem = $this->addElement('element_finder', self :: PARAM_TARGET_LEVEL . '_' . $labels[1], $label, $url, $locale, array(), array('load_elements' => true));
            $defaults = array();
            $elem->setDefaults($defaults);
            $elem->setDefaultCollapsed(false);
        }
        
        $this->build_footer('Create');
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        
//        $survey_template = $this->survey_template;
//        $property_names = $survey_template->get_additional_property_names();
//        
//        foreach ($property_names as $property_name)
//        {
//            $this->add_textfield($property_name, $property_name, true);
//        }
//        $this->addElement('hidden', SurveyTemplate :: PROPERTY_ID);
//        $this->build_footer('Update');
    }

    function create_survey_template()
    {
        
        $values = $this->exportValues();
        $unique_user_ids = array();
        
        if ($values[self :: PARAM_APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] != 0)
        {
            $user_ids = $values[self :: PARAM_APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_ELEMENTS]['user'];
            $group_ids = $values[self :: PARAM_APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_ELEMENTS]['group'];
            $users = array();
            foreach ($group_ids as $group_id)
            {
                $group = GroupDataManager :: get_instance()->retrieve_group($group_id);
                $users = array_merge($users, $group->get_users(true, true));
            }
            $user_ids = array_merge($user_ids, $users);
            $user_ids = array_unique($user_ids);
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
        
        $user_count = count($user_ids);
        
        $levels = array();
        $level_value_counts = array();
        $level_count = 0;
        $array_count = 1;
        
        foreach ($values as $key => $value)
        {
            if (strpos($key, self :: PARAM_TARGET_LEVEL) !== false)
            {
                $keys = explode('_', $key);
                $level = $keys[1];
                $levels[$level] = $value['context'];
                $value_count = count($value['context']);
                $level_value_counts[$level] = $value_count;
                $level_count ++;
                $array_count = $array_count * $value_count;
            }
        }
        
        $colomn_shift_values = array();
        $shift_value = $array_count;
        foreach ($levels as $index => $level)
        {
            $shift_value = $shift_value / $level_value_counts[$index];
            $colomn_shift_values[$index] = $shift_value;
        }
        
        $survey_template = $this->survey_template;
        $property_names = $survey_template->get_additional_property_names();
        $check_level_count = count($property_names);
        
        if ($check_level_count != $level_count)
        {
            return false;
        }
        
        $property_values = array();
        
        $column = $level_count;
        while ($column > 0)
        {
            $index = 0;
            $sub_index = 0;
            $value_index = 0;
            
            while ($index < $array_count)
            {
                $index ++;
                if ($colomn_shift_values[$column] <= $sub_index)
                {
                    $sub_index = 0;
                    if ($value_index == $level_value_counts[$column] - 1)
                    {
                        $value_index = 0;
                    }
                    else
                    {
                        $value_index ++;
                    }
                }
                $sub_index ++;
                $property_values[$index][$column] = $levels[$column][$value_index];
            }
            $column --;
        }
        
        $result = false;
        
        foreach ($user_ids as $user_id)
        {
            foreach ($property_values as $values)
            {
               	$survey_template = $this->survey_template;
                $survey_template->set_user_id($user_id);
                foreach ($property_names as $property_name)
                {
                    $split = explode('_', $property_name);
                    $level = $split[1];
                    $value = $values[$level];
                   	$survey_template->set_additional_property($property_name, $value);
                	
                }
                $result = $survey_template->create();
             }
        }
       
        return $result;
    }

    function update_survey_template()
    {
        $survey_template = $this->survey_template;
        $property_names = $survey_template->get_additional_property_names();
        
        foreach ($property_names as $property_name)
        {
            $survey_template->set_additional_property($property_name, $this->exportValue($property_name));
        }
        
        return $survey_template->update();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $survey_template = $this->survey_template;
            $property_names = $survey_template->get_additional_property_names();
            
            foreach ($property_names as $property_name)
            {
                $defaults[$property_name] = $survey_template->get_additional_property($property_name);
            }
        }
        
        $defaults[self :: PARAM_APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 1;
        parent :: setDefaults($defaults);
    }

}
?>
<?php
/**
 * $Id: configuration_form.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib
 * @author Hans De Bisschop
 */

/**
 * A form to configure platform settings.
 */
class ConfigurationForm extends FormValidator
{
    private $application;
    
    private $base_path;
    
    private $configuration;
    
    private $is_user_setting_form;

    /**
     * Constructor.
     * @param string $application The name of the application.
     * @param string $form_name The name to use in the form tag.
     * @param string $method The method to use ('post' or 'get').
     * @param string $action The URL to which the form should be submitted.
     */
    function __construct($application, $form_name, $method = 'post', $action = null, $is_user_setting_form = false)
    {
        parent :: __construct($form_name, $method, $action);
        
        $this->is_user_setting_form = $is_user_setting_form;
        $this->application = $application;
        // TODO: It might be better to move this functionality to the Path-class
        $this->base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        $this->configuration = $this->parse_application_settings();
        $this->build_form();
        $this->setDefaults();
    }

    /**
     * Builds a form to create or edit a learning object. Creates fields for
     * default learning object properties. The result of this function is equal
     * to build_creation_form()'s, but that one may be overridden to extend the
     * form.
     */
    private function build_form()
    {
        $application = $this->application;
        $base_path = $this->base_path;
        $configuration = $this->configuration;
        
        if (count($configuration['settings']) > 0)
        {
            require_once $base_path . $application . '/settings/settings_' . $application . '_connector.class.php';
            
            foreach ($configuration['settings'] as $category_name => $settings)
            {
                $has_settings = false;
                
                foreach ($settings as $name => $setting)
                {
                	if($this->is_user_setting_form && !$setting['user_setting'])
                		continue;
                		
                	if(!$has_settings)
                	{
                		$this->addElement('html', '<div class="configuration_form">');
                		$this->addElement('html', '<span class="category">' . Translation :: get(Utilities :: underscores_to_camelcase($category_name)) . '</span>');
                		$has_settings = true;
                	}
              
                    if ($setting['locked'] == 'true')
                    {
                        $this->addElement('static', $name, Translation :: get(Utilities :: underscores_to_camelcase($name)));
                    }
                    elseif ($setting['field'] == 'text')
                    {
                        $this->add_textfield($name, Translation :: get(Utilities :: underscores_to_camelcase($name)), ($setting['required'] == 'true'));
                    }
                    elseif ($setting['field'] == 'html_editor')
                    {
                        $this->add_html_editor($name, Translation :: get(Utilities :: underscores_to_camelcase($name)), ($setting['required'] == 'true'));
                    }
                    else
                    {
                        $options_type = $setting['options']['type'];
                        if ($options_type == 'dynamic')
                        {
                            $options_source = $setting['options']['source'];
                            $class = 'Settings' . Application :: application_to_class($application) . 'Connector';
                            $options = call_user_func(array($class, $options_source));
                        }
                        else
                        {
                            $options = $setting['options']['values'];
                        }
                        
                        if ($setting['field'] == 'radio' || $setting['field'] == 'checkbox')
                        {
                            $group = array();
                            foreach ($options as $option_value => $option_name)
                            {
                                if ($setting['field'] == 'checkbox')
                                {
                                    $group[] = & $this->createElement($setting['field'], $name, null, null, $option_value);
                                }
                                else
                                {
                                    $group[] = & $this->createElement($setting['field'], $name, null, Translation :: get(Utilities :: underscores_to_camelcase($option_name)), $option_value);
                                }
                            }
                            $this->addGroup($group, $name, Translation :: get(Utilities :: underscores_to_camelcase($name)), '<br/>', false);
                        }
                        elseif ($setting['field'] == 'select')
                        {
                            $this->addElement('select', $name, Translation :: get(Utilities :: underscores_to_camelcase($name)), $options);
                        }
                    }
                }
                
                if($has_settings)
                {
                	$this->addElement('html', '<div style="clear: both;"></div>');
                	$this->addElement('html', '</div>');
                }
            }
            
            $buttons = array();
            $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
            $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        else
        {
            $this->addElement('html', Translation :: get('NoConfigurableSettings'));
        }
    }

    function parse_application_settings()
    {
        $application = $this->application;
        $base_path = $this->base_path;
        
        $file = $base_path . $application . '/settings/settings_' . $application . '.xml';
        $result = array();
        
        if (file_exists($file))
        {
            $doc = new DOMDocument();
            $doc->load($file);
            $object = $doc->getElementsByTagname('application')->item(0);
            $name = $object->getAttribute('name');
            
            // Get categories
            $categories = $doc->getElementsByTagname('category');
            $settings = array();
            
            foreach ($categories as $index => $category)
            {
                $category_name = $category->getAttribute('name');
                $category_properties = array();
                
                // Get settings in category
                $properties = $category->getElementsByTagname('setting');
                $attributes = array('field', 'default', 'locked', 'user_setting');
                
                foreach ($properties as $index => $property)
                {
                    $property_info = array();
                    
                    foreach ($attributes as $index => $attribute)
                    {
                        if ($property->hasAttribute($attribute))
                        {
                            $property_info[$attribute] = $property->getAttribute($attribute);
                        }
                    }
                    
                    if ($property->hasChildNodes())
                    {
                        $property_options = $property->getElementsByTagname('options')->item(0);
                        $property_options_attributes = array('type', 'source');
                        foreach ($property_options_attributes as $index => $options_attribute)
                        {
                            if ($property_options->hasAttribute($options_attribute))
                            {
                                $property_info['options'][$options_attribute] = $property_options->getAttribute($options_attribute);
                            }
                        }
                        
                        if ($property_options->getAttribute('type') == 'static' && $property_options->hasChildNodes())
                        {
                            $options = $property_options->getElementsByTagname('option');
                            $options_info = array();
                            foreach ($options as $option)
                            {
                                $options_info[$option->getAttribute('value')] = $option->getAttribute('name');
                            }
                            $property_info['options']['values'] = $options_info;
                        }
                    }
                    $category_properties[$property->getAttribute('name')] = $property_info;
                }
                
                $settings[$category_name] = $category_properties;
            }
            
            $result['name'] = $name;
            $result['settings'] = $settings;
        }
        
        return $result;
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $application = $this->application;
        $configuration = $this->configuration;
        
        foreach ($configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if($setting['user_setting'])
                {
                	$configuration_value = LocalSetting :: get($name, $application);
                }
                else
                {
            		$configuration_value = PlatformSetting :: get($name, $application);
                }
                
                if (isset($configuration_value))
                {
                    $defaults[$name] = $configuration_value;
                }
                else
                {
                    $defaults[$name] = $setting['default'];
                }
            }
        }
        
        parent :: setDefaults($defaults);
    }

    /**
     * Updates the configuration.
     * @return boolean True if the update succeeded, false otherwise.
     */
    function update_configuration()
    {
        $values = $this->exportValues();
        $configuration = $this->configuration;
        $application = $this->application;
        $problems = 0;
        
        foreach ($configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['locked'] != 'true')
                {
                    $adm = AdminDataManager :: get_instance();
                    $setting = $adm->retrieve_setting_from_variable_name($name, $application);
                    if (isset($values[$name]))
                    {
                        $setting->set_value($values[$name]);
                    }
                    else
                    {
                        $setting->set_value(0);
                    }
                    if (! $setting->update())
                    {
                        $problems ++;
                    }
                }
            }
        }
        
        if ($problems > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    function update_user_settings()
    {
    	$values = $this->exportValues();
    	$adm = AdminDataManager :: get_instance();
    	$udm = UserDataManager :: get_instance();
    	
    	foreach($values as $key => $value)
    	{
    		if($key == 'submit')
    			continue;
    		
            $setting = $adm->retrieve_setting_from_variable_name($key, $this->application);
    		$user_setting = $udm->retrieve_user_setting(Session :: get_user_id(), $setting->get_id());
    		if($user_setting)
    		{ dump($user_setting);
    			$user_setting->set_value($value);
    			$user_setting->update();
    		}
    		else
    		{
    			$user_setting = new UserSetting();
    			$user_setting->set_setting_id($setting->get_id());
    			$user_setting->set_value($value);
    			$user_setting->set_user_id(Session :: get_user_id());
    			$user_setting->create();
    		}
    	}

    	return true;
    }
}
?>

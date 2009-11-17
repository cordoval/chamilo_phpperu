<?php
/**
 * $Id: home_block_config_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.forms
 */

class HomeBlockConfigForm extends FormValidator
{
    
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $homeblock;
    private $homeblock_config;
    private $base_path;

    function HomeBlockConfigForm($homeblock, $action)
    {
        parent :: __construct('home_block', 'post', $action);
        
        $this->homeblock = $homeblock;
        $this->base_path = (WebApplication :: is_application($this->homeblock->get_application()) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        $this->homeblock_config = $this->parse_block_settings();
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $homeblock = $this->homeblock;
        $base_path = $this->base_path;
        
        $application = $homeblock->get_application();
        $component = $homeblock->get_component();
        
        $homeblock_config = $this->homeblock_config;
        
        if (count($homeblock_config['settings']) > 0)
        {
            require_once $base_path . $application . '/block/connectors/block_' . $application . '_connector.class.php';
            
            foreach ($homeblock_config['settings'] as $category_name => $settings)
            {
                $this->addElement('html', '<div class="configuration_form">');
                $this->addElement('html', '<span class="category">' . Translation :: get(Utilities :: underscores_to_camelcase($category_name)) . '</span>');
                
                foreach ($settings as $name => $setting)
                {
                    if ($setting['locked'] == 'true')
                    {
                        $this->addElement('static', $name, Translation :: get(Utilities :: underscores_to_camelcase($name)));
                    }
                    elseif ($setting['field'] == 'text')
                    {
                        $this->add_textfield($name, Translation :: get(Utilities :: underscores_to_camelcase($name)), true);
                    }
                    else
                    {
                        $options_type = $setting['options']['type'];
                        if ($options_type == 'dynamic')
                        {
                            $options_source = $setting['options']['source'];
                            $class = 'Block' . Application :: application_to_class($application) . 'Connector';
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
                                $group[] = & $this->createElement($setting['field'], $name, null, Translation :: get(Utilities :: underscores_to_camelcase($option_name)), $option_value);
                            }
                            $this->addGroup($group, $name, Translation :: get(Utilities :: underscores_to_camelcase($name)), '<br/>', false);
                        }
                        elseif ($setting['field'] == 'select')
                        {
                            $this->addElement('select', $name, Translation :: get(Utilities :: underscores_to_camelcase($name)), $options);
                        }
                    }
                }
                
                $this->addElement('html', '<div style="clear: both;"></div>');
                $this->addElement('html', '</div>');
            }
            
            //$this->addElement('submit', 'submit', Translation :: get('Ok'));
            $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
            $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
            
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        else
        {
            $this->addElement('html', Translation :: get('NoConfigurableSettings'));
        }
    }

    function update_block_config()
    {
        $values = $this->exportValues();
        $homeblock = $this->homeblock;
        $homeblock_config = $this->homeblock_config;
        
        $problems = 0;
        
        foreach ($homeblock_config['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['locked'] != 'true')
                {
                    $block_config = new HomeBlockConfig();
                    $block_config->set_block_id($homeblock->get_id());
                    $block_config->set_variable($name);
                    $block_config->set_value($values[$name]);
                    
                    if (! $block_config->update())
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

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $homeblock_config = $this->homeblock_config;
        $homeblock_current_config = $this->homeblock->get_configuration();
        
        foreach ($homeblock_config['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                $configuration_value = $homeblock_current_config[$name];
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

    function parse_block_settings()
    {
        $homeblock = $this->homeblock;
        $base_path = $this->base_path;
        
        $application = $homeblock->get_application();
        $component = $homeblock->get_component();
        
        $file = $base_path . $application . '/block/' . $application . '_' . $component . '.xml';
        $result = array();
        
        if (file_exists($file))
        {
            $doc = new DOMDocument();
            $doc->load($file);
            $object = $doc->getElementsByTagname('block')->item(0);
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
                $attributes = array('field', 'default', 'locked');
                
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
}
?>
<?php
namespace admin;
use common\libraries\BasicApplication;
use common\libraries\Utilities;
use common\libraries\Application;
use common\libraries\Path;
use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\Session;
use common\libraries\FormValidator;
use common\libraries\PlatformSetting;
use common\libraries\LocalSetting;

use user\UserDataManager;
use user\UserSetting;

use DOMDocument;

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
        $this->base_path = BasicApplication :: get_application_class_path($application);
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
        $namespace = Application :: determine_namespace($application);
        $base_path = $this->base_path;
        $configuration = $this->configuration;

        if (count($configuration['settings']) > 0)
        {
            require_once $base_path . '/settings/settings_' . $application . '_connector.class.php';

            foreach ($configuration['settings'] as $category_name => $settings)
            {
                $has_settings = false;

                foreach ($settings as $name => $setting)
                {
                    if ($this->is_user_setting_form && ! $setting['user_setting'])
                    {
                        continue;
                    }

                    if (! $has_settings)
                    {
                        $this->addElement('html', '<div class="configuration_form">');
                        $this->addElement('html', '<span class="category">' . Translation :: get(Utilities :: underscores_to_camelcase($category_name), null, $namespace) . '</span>');
                        $has_settings = true;
                    }

                    if ($setting['locked'] == 'true')
                    {
                        $this->addElement('static', $name, Translation :: get(Utilities :: underscores_to_camelcase($name), null, $namespace));
                    }
                    elseif ($setting['field'] == 'text')
                    {
                        $this->add_textfield($name, Translation :: get(Utilities :: underscores_to_camelcase($name), null, $namespace), ($setting['required'] == 'true'));

                        $validations = $setting['validations'];
                        if ($validations)
                        {
                            foreach ($validations as $validation)
                            {
                                if ($this->is_valid_validation_method($validation['rule']))
                                {
                                    if ($validation['rule'] != 'regex')
                                    {
                                        $validation['format'] = NULL;
                                    }

                                    $this->addRule($name, Translation :: get($validation['message'], null, $namespace), $validation['rule'], $validation['format']);
                                }
                            }
                        }

                    }
                    elseif ($setting['field'] == 'html_editor')
                    {
                        $this->add_html_editor($name, Translation :: get(Utilities :: underscores_to_camelcase($name), null, $namespace), ($setting['required'] == 'true'));
                    }
                    else
                    {
                        $options_type = $setting['options']['type'];
                        if ($options_type == 'dynamic')
                        {
                            $options_source = $setting['options']['source'];
                            $class = Application :: determine_namespace($application) . '\Settings' . Application :: application_to_class($application) . 'Connector';
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
                                    $group[] = & $this->createElement($setting['field'], $name, null, Translation :: get(Utilities :: underscores_to_camelcase($option_name), null, $namespace), $option_value);
                                }
                            }
                            $this->addGroup($group, $name, Translation :: get(Utilities :: underscores_to_camelcase($name), null, $namespace), '<br/>', false);
                        }
                        elseif ($setting['field'] == 'select')
                        {
                            $this->addElement('select', $name, Translation :: get(Utilities :: underscores_to_camelcase($name), null, $namespace), $options);
                        }
                    }
                }

                if ($has_settings)
                {
                    $this->addElement('html', '<div style="clear: both;"></div>');
                    $this->addElement('html', '</div>');
                }
            }

            $buttons = array();
            $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save', array(), Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
            $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', array(), Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        else
        {
            $this->addElement('html', '<div class="warning-message">' . Translation :: get('NoConfigurableSettings', array(), Utilities :: COMMON_LIBRARIES) . '</div>');
        }
    }

    function parse_application_settings()
    {
        $application = $this->application;
        $base_path = $this->base_path;

        $file = $base_path . '/settings/settings_' . $application . '.xml';
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

                        if ($property_options)
                        {
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

                        $property_validations = $property->getElementsByTagname('validations')->item(0);

                        if ($property_validations)
                        {
                            if ($property_validations->hasChildNodes())
                            {
                                $validations = $property_validations->getElementsByTagname('validation');
                                $validation_info = array();
                                foreach ($validations as $validation)
                                {
                                    $validation_info[] = array('rule' => $validation->getAttribute('rule'), 'message' => $validation->getAttribute('message'), 'format' => $validation->getAttribute('format'));
                                }
                                $property_info['validations'] = $validation_info;
                            }
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
                if ($setting['user_setting'] && $this->is_user_setting_form)
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
        $problems = 0;

        foreach ($this->configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['locked'] != 'true' && $setting['user_setting'])
                {
                    if (isset($values[$name]))
                    {
                        $value = $values[$name];
                    }
                    else
                    {
                        $value = 0;
                    }

                    $setting = $adm->retrieve_setting_from_variable_name($name, $this->application);
                    $user_setting = $udm->retrieve_user_setting(Session :: get_user_id(), $setting->get_id());
                    if ($user_setting)
                    {
                        $user_setting->set_value($value);
                        if (! $user_setting->update())
                        {
                            $problems ++;
                        }
                    }
                    else
                    {
                        $user_setting = new UserSetting();
                        $user_setting->set_setting_id($setting->get_id());
                        $user_setting->set_value($value);
                        $user_setting->set_user_id(Session :: get_user_id());
                        if (! $user_setting->create())
                        {
                            $problems ++;
                        }
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

    private function is_valid_validation_method($validation_method)
    {
        $available_validation_methods = array('regex', 'email', 'lettersonly', 'alphanumeric', 'numeric');
        return in_array($validation_method, $available_validation_methods);
    }
}
?>
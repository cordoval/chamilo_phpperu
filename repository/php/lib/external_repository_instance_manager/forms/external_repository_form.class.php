<?php
/**
 * $Id: external_repository_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.forms
 */

class ExternalRepositoryForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    const SETTINGS_PREFIX = 'settings';

    private $external_repository;
    private $configuration;
    private $form_type;

    function ExternalRepositoryForm($form_type, $external_repository, $action)
    {
        parent :: __construct('external_repository', 'post', $action);

        $this->external_repository = $external_repository;
        $this->configuration = $this->parse_settings();
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    function build_basic_form()
    {
        $external_repository = $this->external_repository;
        $configuration = $this->configuration;

        $tabs_generator = new DynamicFormTabsRenderer($this->getAttribute('name'), $this);
        $tabs_generator->add_tab(new DynamicFormTab('general', 'General', Theme :: get_common_image_path() . 'place_tab_view.png', 'build_general_form'));

        if (count($configuration['settings']) > 0)
        {
            $tabs_generator->add_tab(new DynamicFormTab('settings', 'Settings', Theme :: get_common_image_path() . 'place_tab_settings.png', 'build_settings_form'));
        }

        $tabs_generator->render();
    }

    function build_general_form()
    {
        $this->addElement('static', null, Translation :: get('ExternalRepositoryType'), Translation :: get(Utilities :: underscores_to_camelcase($this->external_repository->get_type())));
        $this->addElement('hidden', ExternalRepository :: PROPERTY_TYPE, $this->external_repository->get_type());
        $this->addElement('text', ExternalRepository :: PROPERTY_TITLE, Translation :: get('ExternalRepositoryTitle'), array("size" => "50"));
        $this->addRule(ExternalRepository :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(ExternalRepository :: PROPERTY_DESCRIPTION, Translation :: get('ExternalRepositoryDescription'), true);
        $this->addElement('checkbox', ExternalRepository :: PROPERTY_ENABLED, Translation :: get('ExternalRepositoryEnabled'));
    }

    function build_settings_form()
    {
        $external_repository = $this->external_repository;
        $configuration = $this->configuration;

        require_once Path :: get_common_extensions_path() . 'external_repository_manager/type/' . $external_repository->get_type() . '/settings/settings_' . $external_repository->get_type() . '_connector.class.php';

        $categories = count($configuration['settings']);

        foreach ($configuration['settings'] as $category_name => $settings)
        {
            $has_settings = false;

            foreach ($settings as $name => $setting)
            {
                $label = Translation :: get(Utilities :: underscores_to_camelcase($name));
                $name = self :: SETTINGS_PREFIX . '[' . $name . ']';
                if (! $has_settings && $categories > 1)
                {
                    $this->addElement('category', Translation :: get(Utilities :: underscores_to_camelcase($category_name)));
                    $has_settings = true;
                }

                if ($setting['locked'] == 'true')
                {
                    $this->addElement('static', $name, $label);
                }
                elseif ($setting['field'] == 'text')
                {
                    $this->add_textfield($name, $label, ($setting['required'] == 'true'));

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

                                $this->addRule($name, Translation :: get($validation['message']), $validation['rule'], $validation['format']);
                            }
                        }
                    }

                }
                elseif ($setting['field'] == 'html_editor')
                {
                    $this->add_html_editor($name, $label, ($setting['required'] == 'true'));
                }
                else
                {
                    $options_type = $setting['options']['type'];
                    if ($options_type == 'dynamic')
                    {
                        $options_source = $setting['options']['source'];
                        $class = 'ExternalRepositorySettings' . Utilities :: underscores_to_camelcase($external_repository->get_type()) . 'Connector';
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
                        $this->addGroup($group, $name, $label, '<br/>', false);
                    }
                    elseif ($setting['field'] == 'select')
                    {
                        $this->addElement('select', $name, $label, $options);
                    }
                }
            }

            if ($has_settings && $categories > 1)
            {
                $this->addElement('category');
            }
        }
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', ExternalRepository :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_external_repository()
    {
        $external_repository = $this->external_repository;
        $values = $this->exportValues();

        $external_repository->set_title($values[ExternalRepository :: PROPERTY_TITLE]);
        $external_repository->set_description($values[ExternalRepository :: PROPERTY_DESCRIPTION]);
        $external_repository->set_type($values[ExternalRepository :: PROPERTY_TYPE]);
        $external_repository->set_creation_date(time());
        $external_repository->set_modification_date(time());

        if (isset($values[ExternalRepository :: PROPERTY_ENABLED]))
        {
            $external_repository->set_enabled(true);
        }
        else
        {
            $external_repository->set_enabled(false);
        }

        if (! $external_repository->update())
        {
            return false;
        }
        else
        {
            $settings = $values['settings'];
            $failures = 0;

            foreach ($settings as $name => $value)
            {
                $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name($name, $external_repository->get_id());
                $setting->set_value($value);

                if (! $setting->update())
                {
                    $failures ++;
                }
            }

            if ($failures > 0)
            {
                return false;
            }
        }

        return true;
    }

    function create_external_repository()
    {
        $external_repository = $this->external_repository;
        $values = $this->exportValues();

        $external_repository->set_title($values[ExternalRepository :: PROPERTY_TITLE]);
        $external_repository->set_description($values[ExternalRepository :: PROPERTY_DESCRIPTION]);
        $external_repository->set_type($values[ExternalRepository :: PROPERTY_TYPE]);
        $external_repository->set_creation_date(time());
        $external_repository->set_modification_date(time());

        if (isset($values[ExternalRepository :: PROPERTY_ENABLED]))
        {
            $external_repository->set_enabled(true);
        }
        else
        {
            $external_repository->set_enabled(false);
        }

        if (! $external_repository->create())
        {
            return false;
        }
        else
        {
            $settings = $values['settings'];
            $failures = 0;

            foreach ($settings as $name => $value)
            {
                $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name($name, $external_repository->get_id());
                $setting->set_value($value);

                if (! $setting->update())
                {
                    $failures ++;
                }
            }

            if ($failures > 0)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $external_repository = $this->external_repository;
        $defaults[ExternalRepository :: PROPERTY_ID] = $external_repository->get_id();
        $defaults[ExternalRepository :: PROPERTY_TITLE] = $external_repository->get_title();
        $defaults[ExternalRepository :: PROPERTY_TYPE] = $external_repository->get_type();
        $defaults[ExternalRepository :: PROPERTY_DESCRIPTION] = $external_repository->get_description();
        $defaults[ExternalRepository :: PROPERTY_ENABLED] = $external_repository->get_enabled();

        $configuration = $this->configuration;

        foreach ($configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name($name, $external_repository->get_id());
                if ($setting instanceof ExternalRepositorySetting)
                {
                    $defaults[self :: SETTINGS_PREFIX][$name] = $setting->get_value();
                }
            }
        }

        parent :: setDefaults($defaults);
    }

    function get_external_repository_types()
    {
        $path = Path :: get_common_extensions_path() . 'external_repository_manager/type/';
        $folders = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);

        $types = array();
        foreach ($folders as $folder)
        {
            $types[$folder] = Translation :: get(Utilities :: underscores_to_camelcase($folder));
        }
        ksort($types);
        return $types;
    }

    function parse_settings()
    {
        $external_repository = $this->external_repository;

        $file = Path :: get_common_extensions_path() . 'external_repository_manager/type/' . $external_repository->get_type() . '/settings/settings_' . $external_repository->get_type() . '.xml';
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
                                    $validation_info[] = array(
                                            'rule' => $validation->getAttribute('rule'),
                                            'message' => $validation->getAttribute('message'),
                                            'format' => $validation->getAttribute('format'));
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

    private function is_valid_validation_method($validation_method)
    {
        $available_validation_methods = array('regex', 'email', 'lettersonly', 'alphanumeric', 'numeric', 'required');
        return in_array($validation_method, $available_validation_methods);
    }
}
?>
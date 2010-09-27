<?php



class SurveyContextTemplateForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ContextTemplateUpdated';
    const RESULT_ERROR = 'ContextTemplateUpdateFailed';
    
    const CONTEXT_REGISTRATIONS = 'registrations_';
    
    const ROOT_DIR = 'lib/content_object/survey/';
    const TEMPLATE = 'template';
    
    private $context_template;
    private $user;
    private $form_type;
    private $manager;

    /**
     * Creates a new LanguageForm
     */
    function SurveyContextTemplateForm($form_type, $action, $context_template, $user, $manager)
    {
        parent :: __construct('context_template_form', 'post', $action);
        
        $this->context_template = $context_template;
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
        $this->addElement('html', '<span class="category">' . Translation :: get('SurveyContextTemplateGeneral') . '</span>');
        $this->add_textfield(SurveyContextTemplate :: PROPERTY_NAME, Translation :: get('SurveyContextTemplateName'), true);
        $this->add_html_editor(SurveyContextTemplate :: PROPERTY_DESCRIPTION, Translation :: get('SurveyContextTemplateDescription'), false);
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        if ($this->form_type == self :: TYPE_CREATE)
        {
            $this->addElement('html', '<div class="configuration_form">');
            $this->addElement('html', '<span class="category">' . Translation :: get('SurveyContextTemplateLevels') . '</span>');
        }
    }

    function build_footer($action_name)
    {
        if ($this->form_type == self :: TYPE_CREATE)
        {
            $this->addElement('html', '<div style="clear: both;"></div>');
            $this->addElement('html', '</div>');
        }
        
        $buttons[] = $this->createElement('style_submit_button', 'create', Translation :: get($action_name), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/survey_context_template_form.js'));
    }

    function get_context_select_box($number = null)
    {
        
        return $this->createElement('select', self :: CONTEXT_REGISTRATIONS . $number, Translation :: get('SurveyContextRegistration'), $this->get_contexts(), true);
    
    }

    private function get_contexts()
    {
        
        $registrations = SurveyContextDataManager :: get_instance()->retrieve_survey_context_registrations();
        $values = array();
        while ($registration = $registrations->next_result())
        {
            $context_type = $registration->get_type();
            $context = SurveyContext :: factory($context_type);
            $name = $registration->get_name();
            $keys = $context->get_allowed_keys();
            if(count($keys) == 0){
            	$keys[] = 'id';
            }
            foreach ($keys as $key)
            {
                $values[$context_type . '|' . $key] = $name . '  (key: ' . $key . ')';
            }
        }
        return $values;
    }

    /**
     * Creates a new basic form
     */
    function build_creation_form()
    {
        
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }
        
        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 1;
        }
        
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }
        
        $number_of_options = intval($_SESSION['mc_number_of_options']);
        
        $number = 1;
        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->get_context_select_box($option_number);
                $level = '<span >  Level ' . $number . ' </span>';
                $number ++;
                $group[] = $this->createElement('static', '', 'label', $level);
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_list_remove.png', array('style="border: 0px;"'));
                }
                $this->addGroup($group, self :: CONTEXT_REGISTRATIONS . $option_number, Translation :: get('SurveyContext'), '', false);
                $this->addRule(self :: CONTEXT_REGISTRATIONS . $option_number, Translation :: get('ThisFieldIsRequired'), 'required');
            }
        }
        
        $this->addElement('image', 'add[]', Theme :: get_common_image_path() . 'action_list_add.png', array('style="border: 0px;"'));
        $this->build_footer('Create');
    }

    function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }
        return parent :: validate();
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        
        //        $this->addElement($this->add_name_field());
        $this->addElement('hidden', SurveyContextTemplate :: PROPERTY_ID);
        $this->build_footer('Update');
    }

    function create_context_template()
    {
        $values = $this->exportValues();
        
        $name = $values[SurveyContextTemplate :: PROPERTY_NAME];
        $description = $values[SurveyContextTemplate :: PROPERTY_DESCRIPTION];
        
        foreach ($values as $key => $value)
        {
            if (strpos($key, self :: CONTEXT_REGISTRATIONS) !== false)
            {
                $levels[] = $value;
            }
        }
        
        $result = true;
        $parent_id = 1;
        $type = '';
        
        $properties = array();
        
        foreach ($levels as $index => $level)
        {
            
            $context_key = explode('|', $level);
            $context_type = $context_key[0];
            $key = $context_key[1];
            
            $level = $index + 1;
            $properties['Level_' . $level . '_' . $key] = $context_type;
            
            $context_template = new SurveyContextTemplate();
            $context_template->set_name($name);
            $context_template->set_description($description);
            $context_template->set_context_type($context_type);
            $context_template->set_key($key);
            $context_template->set_parent_id($parent_id);
            if ($index != 0)
            {
                $context_template->set_type($type);
            }
            $result = $context_template->create();
            
            if ($result)
            {
                $parent_id = $context_template->get_id();
            }
            else
            {
                return false;
            }
            if ($index == 0)
            {
                $type = 'survey_template' . $parent_id;
                $context_template->set_type($type);
                $this->context_template = $context_template;
                $context_template->update();
            }
        }
        
        $result = $this->create_files($type, $properties);
        
        if (! $result)
        {
            $this->context_template->delete();
        
        }
        
        return $result;
    }

    function update_context_template()
    {
        $context_template = $this->context_template;
        $context_template->set_name($this->exportValue(SurveyContextTemplate :: PROPERTY_NAME));
        $context_template->set_description($this->exportValue(SurveyContextTemplate :: PROPERTY_DESCRIPTION));
        
        return $context_template->update();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $context_template = $this->context_template;
        $defaults[SurveyContextTemplate :: PROPERTY_ID] = $context_template->get_id();
        $defaults[SurveyContextTemplate :: PROPERTY_NAME] = $context_template->get_name();
        $defaults[SurveyContextTemplate :: PROPERTY_DESCRIPTION] = $context_template->get_description();
        parent :: setDefaults($defaults);
    }

    function create_files($type, $properties)
    {
        
        $result = false;
        $filename = $type;
        $repository_directory = Path :: get_repository_path();
        
        $path = $repository_directory . self :: ROOT_DIR;
        $new_dir = $path . self :: TEMPLATE . '/' . $type;
        
        $result = Filesystem :: create_dir($new_dir);
        
        if ($result)
        {
            $content = $this->get_class_file($type, $properties);
            $result = Filesystem :: write_to_file($new_dir . '/' . $type . '.class.php', $content);
            
            $content = $this->get_xml_file($type, $properties);
            $result = Filesystem :: write_to_file($new_dir . '/' . $type . '.xml', $content);
            
            if ($result)
            {
                $manager = RepositoryDataManager :: get_instance();
                $storage_unit_info = $this->parse_xml_file($new_dir . '/' . $type . '.xml');
                $result = $manager->create_storage_unit($storage_unit_info['name'], $storage_unit_info['properties'], $storage_unit_info['indexes']);
            }
        
        }
        return $result;
    }

    function get_class_file($type, $properties)
    {
        
        $context_class = array();
        
        $context_class[] = '<?php';
        $context_class[] = 'require_once (Path :: get_repository_path().' . '\'' . '/lib/content_object/survey/survey_template.class.php\');';
        
        $context_class[] = 'class ' . Utilities :: underscores_to_camelcase($type) . ' extends SurveyTemplate';
        $context_class[] = '{';
        
        $context_class[] = ' const CLASS_NAME = __CLASS__;';
        $context_class[] = '';
        
        $additional_property_names = array();
        
        foreach ($properties as $property => $context_type)
        {
            $context_class[] = ' const PROPERTY_' . strtoupper($property) . ' = \'' . strtolower($property) . '\';';
            $additional_property_names[] = 'self :: PROPERTY_' . strtoupper($property);
            $additional_property_names_with_context_type[] = 'self :: PROPERTY_' . strtoupper($property) . ' => ' . $context_type;
        
        }
        $context_class[] = '';
        
        $context_class[] = 'static function get_additional_property_names($with_context_type = false)';
        $context_class[] = '{';
        
        $context_class[] = ' if ($with_context_type)';
        $context_class[] = '{';
        $context_class[] = 'return array(' . implode(', ', $additional_property_names_with_context_type) . ');';
        $context_class[] = ' }';
        $context_class[] = 'else';
        $context_class[] = '{';
        $context_class[] = 'return array(' . implode(', ', $additional_property_names) . ');';
        $context_class[] = '}';
        
        $context_class[] = '}';
        
        foreach ($properties as $property => $context_type)
        {
            
            $context_class[] = 'function get_' . strtolower($property) . '()';
            $context_class[] = '{';
            $context_class[] = '  return $this->get_additional_property(self :: PROPERTY_' . strtoupper($property) . ');';
            $context_class[] = '}';
            
            $context_class[] = '';
            
            $context_class[] = 'function set_' . strtolower($property) . '($' . strtolower($property) . ')';
            $context_class[] = '{';
            $context_class[] = '   $this->set_additional_property(self :: PROPERTY_' . strtoupper($property) . ', $' . strtolower($property) . ');';
            $context_class[] = '}';
        }
        
        $context_class[] = 'static function get_table_name()';
        $context_class[] = '{';
        $context_class[] = '  return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);';
        $context_class[] = '}';
        
        $context_class[] = '}';
        $context_class[] = '?>';
        
        $content = implode("\n", $context_class);
        return $content;
    
    }

    function get_xml_file($type, $properties)
    {
        $context_class = array();
        $context_class[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $context_class[] = '<object name="' . $type . '">';
        $context_class[] = '	<properties>';
        $context_class[] = '		<property name="id" type="integer" unsigned="1" notnull="1" />';
        
        foreach ($properties as $property => $context_type)
        {
            $context_class[] = '		<property name="' . strtolower($property) . '" type="text" length="50" fixed="true" notnull="1" />';
        }
        
        $context_class[] = '	</properties>';
        $context_class[] = '	<index name="id" type="primary">';
        $context_class[] = '		<indexproperty name="id" />';
        $context_class[] = '	</index>';
        
        $context_class[] = '</object>';
        $content = implode("\n", $context_class);
        return $content;
    }

    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used as the
     * PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database.
     * mdb2.datatypes.php
     * @param string $file The complete path to the XML-file from which the
     * storage unit definition should be read.
     * @return array An with values for the keys 'name','properties' and
     * 'indexes'
     */
    public function parse_xml_file($file)
    {
        $name = '';
        $properties = array();
        $indexes = array();
        
        $doc = new DOMDocument();
        $doc->load($file);
        $object = $doc->getElementsByTagname('object')->item(0);
        $name = $object->getAttribute('name');
        $xml_properties = $doc->getElementsByTagname('property');
        $attributes = array('type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed');
        foreach ($xml_properties as $index => $property)
        {
            $property_info = array();
            foreach ($attributes as $index => $attribute)
            {
                if ($property->hasAttribute($attribute))
                {
                    $property_info[$attribute] = $property->getAttribute($attribute);
                }
            }
            $properties[$property->getAttribute('name')] = $property_info;
        }
        $xml_indexes = $doc->getElementsByTagname('index');
        foreach ($xml_indexes as $key => $index)
        {
            $index_info = array();
            $index_info['type'] = $index->getAttribute('type');
            $index_properties = $index->getElementsByTagname('indexproperty');
            foreach ($index_properties as $subkey => $index_property)
            {
                $index_info['fields'][$index_property->getAttribute('name')] = array('length' => $index_property->getAttribute('length'));
            }
            $indexes[$index->getAttribute('name')] = $index_info;
        }
        $result = array();
        $result['name'] = $name;
        $result['properties'] = $properties;
        $result['indexes'] = $indexes;
        
        return $result;
    }

    public function get_context_template()
    {
        return $this->context_template;
    }

}
?>
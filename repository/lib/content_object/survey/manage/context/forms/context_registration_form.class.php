<?php

class SurveyContextRegistrationForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ContextTypeUpdated';
    const RESULT_ERROR = 'ContextTypeUpdateFailed';
    
    const PROPERTIES = 'properties_';
    const PROPERTY_KEYS = 'keys_';
    
    const ROOT_DIR = 'lib/content_object/survey/';
    const CONTEXT = 'context';
    
    private $context_registration;
    private $user;
    private $form_type;
    private $manager;

    /**
     * Creates a new LanguageForm
     */
    function SurveyContextRegistrationForm($form_type, $action, $context_registration, $user, $manager)
    {
        parent :: __construct('context_registration_form', 'post', $action);
        
        $this->context_registration = $context_registration;
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
        $this->addElement('html', '<span class="category">' . Translation :: get('SurveyContextRegistrationGeneral') . '</span>');
        $this->add_textfield(SurveyContextRegistration :: PROPERTY_NAME, Translation :: get('SurveyContextRegistrationName'), true);
        $this->add_html_editor(SurveyContextRegistration :: PROPERTY_DESCRIPTION, Translation :: get('SurveyContextRegistrationDescription'), false);
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('SurveyContextRegistrationProperties') . '</span>');
    }

    function build_footer($action_name)
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        $buttons[] = $this->createElement('style_submit_button', 'create', Translation :: get($action_name), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/survey_context_registration_form.js'));
    }

    function add_property_field($number = null)
    {
        
        $element = $this->createElement('text', self :: PROPERTIES . $number, Translation :: get('SurveyContextRegistrationProperty'), array("size" => "50"));
        //$this->addRule(PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('ThisFieldIsRequired'), 'required');
        return $element;
    }

    function add_property_key_field($number = null)
    {
        
        $element = $this->createElement('checkbox', self :: PROPERTY_KEYS . $number, Translation :: get('SurveyContextRegistrationProperty'));
        //$this->addRule(PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('ThisFieldIsRequired'), 'required');
        return $element;
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
        
        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->add_property_field($option_number);
                $group[] = $this->createElement('static', '', 'label', ' ' . Translation :: get('SurveyContextRegistrationPropertyIsKey') . ' ');
                $group[] = $this->add_property_key_field($option_number);
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_list_remove.png', array('style="border: 0px;"'));
                }
                $this->addGroup($group, self :: PROPERTIES . $option_number, Translation :: get('SurveyContextRegistrationProperty'), '', false);
                $this->addRule(self :: PROPERTIES . $option_number, Translation :: get('ThisFieldIsRequired'), 'required');
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
        $this->addElement('hidden', SurveyContextRegistration :: PROPERTY_ID);
        $this->build_footer('Update');
    }

    function create_context_registration()
    {
        $values = $this->exportValues();
        
        $name = $values[SurveyContextRegistration :: PROPERTY_NAME];
        
        $condition = new EqualityCondition(SurveyContextRegistration :: PROPERTY_NAME, $name);
        $context_registrations = SurveyContextDataManager :: get_instance()->retrieve_survey_context_registrations($condition);
        $context_registration = $context_registrations->next_result();
        
        $result = true;
        
        if (! $context_registration)
        {
            $type = 'survey_' . strtolower($name) . '_context';
            
            $context_registration = $this->context_registration;
            $context_registration->set_name($name);
            $context_registration->set_description($values[SurveyContextRegistration :: PROPERTY_DESCRIPTION]);
            $context_registration->set_type($type);
            
            $properties = array();
            
            foreach ($values as $key => $value)
            {
                if (strpos($key, self :: PROPERTIES) !== false)
                {
                    $keys = explode('_', $key);
                    $number = $keys[1];
                    
                    $number = $is_key = $values[self :: PROPERTY_KEYS . $number];
                    if (! $is_key)
                    {
                        $is_key = 0;
                    }
                    $properties[$value] = $is_key;
                
                }
            }
        }
        else
        {
            return false;
        }
        
        $result = $this->create_files($type, $properties);
       
        if($result){
        	$result = $context_registration->create();
        	
        }
        
        return $result;
    }

    function update_context_registration()
    {
        $context_registration = $this->context_registration;
        $context_registration->set_name($this->exportValue(SurveyContextRegistration :: PROPERTY_NAME));
        $context_registration->set_description($this->exportValue(SurveyContextRegistration :: PROPERTY_DESCRIPTION));
        
        return $context_registration->update();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $context_registration = $this->context_registration;
        $defaults[SurveyContextRegistration :: PROPERTY_ID] = $context_registration->get_id();
        $defaults[SurveyContextRegistration :: PROPERTY_NAME] = $context_registration->get_name();
        $defaults[SurveyContextRegistration :: PROPERTY_DESCRIPTION] = $context_registration->get_description();
        parent :: setDefaults($defaults);
    }

    function create_files($type, $properties)
    {
        
        $result = false;
        $filename = $type;
        $repository_directory = Path :: get_repository_path();
        
        $path = $repository_directory . self :: ROOT_DIR;
        $new_dir = $path . self :: CONTEXT . '/' . $type;
        
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
        $context_class[] = 'require_once (Path :: get_repository_path().'.'\''.'/lib/content_object/survey/survey_context.class.php\');';
        
        $context_class[] = 'class ' . Utilities :: underscores_to_camelcase($type) . ' extends SurveyContext';
        $context_class[] = '{';
        
        $context_class[] = ' const CLASS_NAME = __CLASS__;';
        $context_class[] = '';
        
        $additional_property_names = array();
        $allowed_keys = array();
        
        foreach ($properties as $property => $is_key)
        {
            $context_class[] = ' const PROPERTY_' . strtoupper($property) . ' = \'' . strtolower($property) . '\';';
            $additional_property_names[] = 'self :: PROPERTY_' . strtoupper($property);
            if ($is_key)
            {
                $allowed_keys[] = 'self :: PROPERTY_' . strtoupper($property);
            }
        }
        $context_class[] = '';
        
        $context_class[] = 'static function get_additional_property_names()';
        $context_class[] = '{';
        $context_class[] = 'return array(' . implode(', ', $additional_property_names) . ');';
        $context_class[] = '}';
        
        foreach ($properties as $property => $is_key)
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
        
        $context_class[] = 'static public function get_allowed_keys()';
        $context_class[] = '{';
        $context_class[] = '	 return array(' . implode(', ', $allowed_keys) . ');';
        $context_class[] = '}';
        
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
        
        foreach ($properties as $property => $is_key)
        {
            $context_class[] = '		<property name="' . strtolower($property) . '" type="text" length="50" fixed="true" notnull="1" />';
        }
        
        $context_class[] = '	</properties>';
        $context_class[] = '	<index name="id" type="primary">';
        $context_class[] = '		<indexproperty name="id" />';
        $context_class[] = '	</index>';
        
        foreach ($properties as $property => $is_key)
        {
            if ($is_key)
            {
                $context_class[] = '	<index name="' . strtolower($property) . '" type="unique">';
                $context_class[] = '		<indexproperty name="' . strtolower($property) . '" />';
                $context_class[] = '	</index>';
            
            }
        }
        
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
    
    public function get_context_registration(){
    	return $this->context_registration;
    } 
    
}
?>
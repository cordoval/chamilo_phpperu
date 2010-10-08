<?php
require_once dirname(__FILE__) . '/../metadata_property_value.class.php';
require_once dirname(__FILE__) . '/../metadata_property_attribute_value.class.php';
/**
 * This class describes the form for a MetadataPropertyValue object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class MetadataForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

        const OPTION_BLANK = 'blank';
        const PARAM_FIXED = 'fixed';

	private $metadata_property_values = array();
	private $user;
        private $content_object_property_metadata_values = array();
        private $property_types = array();
        private $property_attribute_types = array();
        private $application;
        private $content_object;
        private $metadata_property_attribute_values = array();
        private $allowed_metadata_property_attribute_types = array();

    function MetadataForm($form_type,  $content_object, $metadata_property_values, $content_object_property_metadata_values, $metadata_property_attribute_values, $allowed_metadata_property_attribute_types,$action, $user, $application)
    {
    	parent :: __construct('metadata_property_value_settings', 'post', $action);

    	$this->content_object = $content_object;
        $this->content_object_property_metadata_values = $content_object_property_metadata_values;
    	$this->user = $user;
        $this->form_type = $form_type;
        $this->application = $application;
        $this->metadata_property_values = $metadata_property_values;
        $this->metadata_property_attribute_values = $metadata_property_attribute_values;
        $this->allowed_metadata_property_attribute_types = $allowed_metadata_property_attribute_types;

        $this->get_property_types();
        $this->get_property_attribute_types();

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
        $this->addElement('hidden', MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID);

        $this->build_content_object_property_metadata_values();

        $group = array();

        $group[] = $this->createElement('select', MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, Translation :: get('PropertyType'), $this->property_types);
        $group[] = $this->createElement('text', MetadataPropertyValue :: PROPERTY_VALUE, Translation :: get('PropertyValue'));

        $this->addGroup($group, '', Translation :: get('NewPropertyType'));

        $this->build_metadata_property_values();
    }

    function build_editing_form()
    {
    	$this->build_basic_form();

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

    function edit_metadata()
    {
    	$values = $this->exportValues();

        $fails = 0;

        //create new property value
        if(!empty($values[MetadataPropertyValue :: PROPERTY_VALUE]))
        {
            $metadata_property_value = new MetadataPropertyValue();

            $metadata_property_value->set_content_object_id($values[MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID]);
            $metadata_property_value->set_property_type_id($values[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID]);
            $metadata_property_value->set_value($values[MetadataPropertyValue :: PROPERTY_VALUE]);

            if(!$metadata_property_value->create())$fails++;
        }

        //update existing property values
        foreach($this->metadata_property_values as $metadata_property_value)
        {
            $cond = $values[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID] != $this->_defaults[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID . '_' . $metadata_property_value->get_id()];
            $cond |= $values[MetadataPropertyValue :: PROPERTY_VALUE] != $this->_defaults[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()];
            if($cond)
            {
                $metadata_property_value->set_content_object_id($values[MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID]);
                //$metadata_property_value->set_property_type_id($values[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID . '_' . $metadata_property_value->get_id()]);
                $metadata_property_value->set_value($values[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()]);

                if(!$metadata_property_value->update())$fails ++;
            }

            // new property attribute values
            if($values[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID . '_' . $metadata_property_value->get_id()] != self :: OPTION_BLANK)
            {
                $metadata_property_attribute_value = new MetadataPropertyAttributeValue();

                $metadata_property_attribute_value->set_parent_id($metadata_property_value->get_id(MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID . '_' . $metadata_property_value->get_id()));
                $metadata_property_attribute_value->set_property_attribute_type_id($values[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID . '_' . $metadata_property_value->get_id()]);
                $metadata_property_attribute_value->set_relation(MetadataPropertyAttributeValue :: RELATION_PROPERTY_VALUE);
                $metadata_property_attribute_value->set_value_type($values[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE_TYPE . '_' . $metadata_property_value->get_id()]);

                //select appropriate value input box according to value type
                $appendix = ($metadata_property_attribute_value->get_value_type() == MetadataPropertyAttributeValue :: VALUE_TYPE_ID) ? '_n' : '';
                $metadata_property_attribute_value->set_value($values[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . $appendix . '_' . $metadata_property_value->get_id()]);

                if(!$metadata_property_attribute_value->create()) $fails ++;
            }

            //update existing attribute values
            foreach($this->metadata_property_attribute_values[MetadataPropertyAttributeValue :: RELATION_PROPERTY_VALUE][$metadata_property_value->get_id()] as $id => $metadata_property_attribute_value)
            {
                //check if changes occurred
                if($metadata_property_attribute_value->get_value() != $values[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id()])
                {
                   $metadata_property_attribute_value->set_value($values[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id()]);
                   $metadata_property_attribute_value->update();
                }
            }

            //update existing content object property metadata attributes
            foreach($this->metadata_property_attribute_values[MetadataPropertyAttributeValue :: RELATION_CONTENT_OBJECT_PROPERTY][$this->content_object->get_id()] as $id => $metadata_property_attribute_value)
            {
                foreach($this->content_object_property_metadata_values as $id => $content_object_property_metadata)
                {
                    //check if changes occurred
                    if($metadata_property_attribute_value->get_value() != $values[$content_object_property_metadata->get_id() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id()])
                    {
                       $metadata_property_attribute_value->set_value($values[$content_object_property_metadata->get_id() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id()]);
                       $metadata_property_attribute_value->update();
                    }
                }

            }
        }

        //create content object metadata_attribute_values
        foreach($this->content_object_property_metadata_values as $id => $content_object_property)
        {
            if($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID] != self :: OPTION_BLANK)
            {
                $metadata_property_attribute_value = new MetadataPropertyAttributeValue();

                $metadata_property_attribute_value->set_parent_id($this->content_object->get_id());
                $metadata_property_attribute_value->set_property_attribute_type_id($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID]);
                $metadata_property_attribute_value->set_relation(MetadataPropertyAttributeValue :: RELATION_CONTENT_OBJECT_PROPERTY);
                $metadata_property_attribute_value->set_value_type($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE_TYPE]);

                $appendix = ($metadata_property_attribute_value->get_value_type() == MetadataPropertyAttributeValue :: VALUE_TYPE_ID) ? '_2' : '';
                $metadata_property_attribute_value->set_value($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE]);

                if(!$metadata_property_attribute_value->create()) $fails ++;
            }
        }




        return ($fails) ? false : true;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $defaults[MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID] = $this->content_object->get_id();

        //content object property attribute values
        foreach($this->metadata_property_attribute_values[MetadataPropertyAttributeValue :: RELATION_CONTENT_OBJECT_PROPERTY][$this->content_object->get_id()] as $id => $metadata_property_attribute_value)
        {
            $defaults[$this->content_object_property_metadata_values[$metadata_property_attribute_value->get_parent_id()]->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $id] = $metadata_property_attribute_value->get_value();
        }

        //metadata property values
        foreach($this->metadata_property_values as $metadata_property_value)
        {
            $defaults[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID . '_' . $metadata_property_value->get_id()] = $metadata_property_value->get_property_type_id();
            $defaults[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()] = $metadata_property_value->get_value();
        
            foreach($this->metadata_property_attribute_values[MetadataPropertyAttributeValue :: RELATION_PROPERTY_VALUE][$metadata_property_value->get_id()] as $id => $metadata_property_attribute_value)
            {
                $defaults[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id()] = $metadata_property_attribute_value->get_value();
            }
        }
        parent :: setDefaults($defaults);
    }

    function build_content_object_property_metadata_values()
    {
        $this->addElement('html', '<h3>' .Translation :: get('Predefined').'</h3>');
        foreach($this->content_object_property_metadata_values as $id => $content_object_property)
        {
            $function_name = 'get_' . $content_object_property->get_content_object_property();

            $this->addElement('category', Translation :: get('PropertyValue'));
            $this->addElement('static','',$this->property_types[$content_object_property->get_property_type_id()], $content_object_property->format_content_object_property_according_to_source($this->content_object));

            $allowed_property_types = $this->filter_allowed_property_attribute_types($content_object_property->get_property_type_id());
            if(isset($this->allowed_metadata_property_attribute_types[$content_object_property->get_property_type_id()]))
            {
                //new empty attribute_value
                $group = array();

                $group[] =$this->createElement('select', $content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID , Translation :: get('NsPrefix'), $allowed_property_types);
                $group[] =$this->createElement('select', $content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE_TYPE, Translation :: get('ValueType'),$this->get_value_types());
                $group[] =$this->createElement('text', $content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE, Translation :: get('Value'));
                $group[] =$this->createElement('select', $content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_2', Translation :: get('Value'), $this->property_attribute_types);

                $this->addGroup($group, '', Translation :: get('PropertyAttributeValue'));
            }
            //existing attribute_values
            $this->build_content_object_property_metadata_attribute_values($content_object_property->get_property_type_id(), $content_object_property->get_content_object_property());

            $this->addElement('category');
            
        }
    }

    function get_value_types()
    {
        $value_types = array();
        $value_types[MetadataPropertyAttributeValue :: VALUE_TYPE_NONE] = '--';
        $value_types[MetadataPropertyAttributeValue :: VALUE_TYPE_ID] = Translation :: get('id');
        $value_types[MetadataPropertyAttributeValue :: VALUE_TYPE_VALUE] = Translation :: get('value');

        return $value_types;
    }

    function build_metadata_property_values()
    {
        $this->addElement('html', '<h3>' .Translation :: get('Additional').'</h3>');
        foreach($this->metadata_property_values as $metadata_property_value)
        {
            $this->addElement('category', Translation :: get('PropertyValue'));

            $group = array();
            
            $group[] = $this->createElement('text', MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id(), Translation :: get('PropertyValue'));
            $group[] = $this->createElement('static', null, null, '<a href="' . $this->application->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_DELETE_METADATA_PROPERTY_VALUE, MetadataManager :: PARAM_METADATA_PROPERTY_VALUE => $metadata_property_value->get_id(), MetadataManager :: PARAM_CONTENT_OBJECT => $this->content_object->get_id())). '">delete</a>');

            $this->addGroup($group, '', $this->property_types[$metadata_property_value->get_property_type_id()]);

            if(isset($this->allowed_metadata_property_attribute_types[$metadata_property_value->get_property_type_id()]))
            {
                $group = array();

                $group[] = $this->createElement('select', MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID . '_' . $metadata_property_value->get_id(), Translation :: get('PropertyAttributeType'), $this->filter_allowed_property_attribute_types($metadata_property_value->get_property_type_id()));
                $group[] = $this->createElement('select', MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE_TYPE . '_' . $metadata_property_value->get_id(), Translation :: get('ValueType'),$this->get_value_types());
                $group[] = $this->createElement('text', MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id(), Translation :: get('Value'));
                $group[] = $this->createElement('select', MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_n_' . $metadata_property_value->get_id(), Translation :: get('Value'), $this->property_attribute_types);

                $this->addGroup($group, '', Translation :: get('PropertyAttributeValue'));
            }

            $this->build_metadata_property_attribute_values($metadata_property_value);

            $this->addElement('category');
        }
    }

    function build_content_object_property_metadata_attribute_values($property_type_id, $content_object_property)
    {
        foreach($this->metadata_property_attribute_values[MetadataPropertyAttributeValue :: RELATION_CONTENT_OBJECT_PROPERTY][$this->content_object->get_id()] as $id => $metadata_property_attribute_value)
        {
            if($this->content_object_property_metadata_values[$metadata_property_attribute_value->get_parent_id()]->get_content_object_property() == $content_object_property)
            {
                $group = array();

                if($metadata_property_attribute_value->get_value_type() == MetadataPropertyAttributeValue :: VALUE_TYPE_VALUE)
                {
                    $group[] = $this->createElement('text', $content_object_property . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE .'_' . $id , Translation :: get('PropertyAttributeType'));
                }
                else
                {
                    $group[] = $this->createElement('select', $content_object_property . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE .'_' . $id , Translation :: get('PropertyAttributeType'), $this->filter_allowed_property_attribute_types($metadata_property_attribute_value->get_property_attribute_type()));
                }
                $group[] = $this->createElement('static', null, null, '<a href="' . $this->application->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_DELETE_METADATA_PROPERTY_ATTRIBUTE_VALUE, MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE => $metadata_property_attribute_value->get_id(), MetadataManager :: PARAM_CONTENT_OBJECT => $this->content_object->get_id())). '">delete</a>');

                $this->addGroup($group, '', $this->property_attribute_types[$metadata_property_attribute_value->get_property_attribute_type_id()]);
            }
        }
    }

    function build_metadata_property_attribute_values($metadata_property_value)
    {
        foreach($this->metadata_property_attribute_values[MetadataPropertyAttributeValue :: RELATION_PROPERTY_VALUE][$metadata_property_value->get_id()] as $n => $metadata_property_attribute_value)
        {
            if($metadata_property_attribute_value->get_relation() == MetadataPropertyAttributeValue :: RELATION_PROPERTY_VALUE)
            {
                //$group[] = $this->createElement('select', MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id(), Translation :: get('PropertyAttributeType'), $this->filter_allowed_property_attribute_types($metadata_property_value->get_property_type_id()));
                $group = array();

                //$group[] = $this->createElement('select', MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID . '_' . $metadata_property_value->get_id(), Translation :: get('PropertyAttributeType'), $this->filter_allowed_property_attribute_types($metadata_property_value->get_property_type_id()));

                if($metadata_property_attribute_value->get_value_type() == MetadataPropertyAttributeValue :: VALUE_TYPE_VALUE)
                {
                    $group[] = $this->createElement('text', MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id(), Translation :: get('Value'));
                }
                else
                {
                    $group[] = $this->createElement('select', MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id(), Translation :: get('Value'), $this->property_attribute_types);
                }
                $group[] = $this->createElement('static', null, null, '<a href="' . $this->application->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_DELETE_METADATA_PROPERTY_ATTRIBUTE_VALUE, MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE => $metadata_property_attribute_value->get_id(), MetadataManager :: PARAM_CONTENT_OBJECT => $this->content_object->get_id())). '">delete</a>');
                
                $this->addGroup($group, '', $this->property_attribute_types[$metadata_property_attribute_value->get_property_attribute_type_id()]);
            }
        }
    }

    function get_property_types()
    {
        $property_types = $this->application->retrieve_metadata_property_types();

        while($property_type = $property_types->next_result())
        {
            $this->property_types[$property_type->get_id()] = $property_type->get_ns_prefix() .':'. $property_type->get_name();
        }
    }

    function get_property_attribute_types()
    {
        $property_attribute_types = $this->application->retrieve_metadata_property_attribute_types();

        while($property_attribute_type = $property_attribute_types->next_result())
        {
            $types[$property_attribute_type->get_id()] = $property_attribute_type;
        }
        
        foreach($types as $id => $property_attribute_type)
        {
            $this->property_attribute_types[$property_attribute_type->get_id()] = $property_attribute_type->render_name($property_attribute_type);
        }
    }

    function filter_allowed_property_attribute_types($property_type_id)
    {
        $allowedTypes = array();
        $allowedTypes[self :: OPTION_BLANK] = '--';

        foreach($this->allowed_metadata_property_attribute_types[$property_type_id] as $n => $allowed)
        {
            $allowedTypes[$allowed] = $this->property_attribute_types[$allowed];
        }
        return $allowedTypes;
    }
}
?>
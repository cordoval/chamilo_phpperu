<?php
/**
 * This class describes the form for a MetadataPropertyValue object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
namespace application\metadata;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\ResourceManager;
use common\libraries\Path;

class ContentObjectMetadataEditorForm extends MetadataForm
{

    const TYPE = 'content_object';
    const OPTION_BLANK = 'blank';
    const PARAM_FIXED = 'fixed';

    private $metadata_property_values = array();
    private $user;
    private $content_object_property_metadata_values = array();
    private $property_attribute_types = array();
    private $application;
    private $content_object;
    private $metadata_property_attribute_values = array();
    private $allowed_metadata_property_attribute_types = array();

    function ContentObjectMetadataEditorForm($content_object, $metadata_property_values, $content_object_property_metadata_values, $metadata_property_attribute_values, $allowed_metadata_property_attribute_types,$action, $user, $application)
    {
    	$this->set_parent_type(self :: TYPE);

        parent :: __construct('content_object_metadata_property_value_settings', 'post', $action);

    	$this->content_object = $content_object;
        $this->content_object_property_metadata_values = $content_object_property_metadata_values;
    	$this->user = $user;
        $this->form_type = $form_type;
        $this->application = $application;
        $this->metadata_property_values = $metadata_property_values;
        $this->metadata_property_attribute_values = $metadata_property_attribute_values;
        $this->allowed_metadata_property_attribute_types = $allowed_metadata_property_attribute_types;

        $this->get_property_attribute_types();

        $this->build_creation_form();
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('html', '<h3>' .Translation :: get('Predefined').'</h3>');
        
        $this->addElement('hidden', MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID);

        $this->build_content_object_property_metadata_values();

        $this->addElement('html', '<h3>' .Translation :: get('Additional').'</h3>');

        $this->build_empty_property_value();

        $this->build_metadata_property_values();
    }

    

//    function build_editing_form()
//    {
//    	$this->build_basic_form();
//
//    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
//        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
//
//        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
//    }

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
//            $metadata_property_value = new MetadataPropertyValue();
//
//            $metadata_property_value->set_content_object_id($values[MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID]);
//            $metadata_property_value->set_property_type_id($values[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID]);
//            $metadata_property_value->set_value($values[MetadataPropertyValue :: PROPERTY_VALUE]);

            if(!$this->create_metadata_property_value())$fails++;
        }

        //update existing property values
        foreach($this->metadata_property_values as $metadata_property_value)
        {
            //$cond = $values[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID . '_' . $metadata_property_value->get_id()] != $this->_defaults[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID . '_' . $metadata_property_value->get_id()];
            $cond = $values[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()] != $this->_defaults[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()];
            if($cond)
            {
                //$metadata_property_value->set_content_object_id($values[MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID]);
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

            
        }

        

        //update existing content object property metadata attributes
        foreach($this->content_object_property_metadata_values as $id => $content_object_property_metadata)
        {
            foreach($this->metadata_property_attribute_values[MetadataPropertyAttributeValue :: RELATION_CONTENT_OBJECT_PROPERTY][$content_object_property_metadata->get_id()] as $id => $metadata_property_attribute_value)
            {
                $prop= $content_object_property_metadata->get_content_object_property();
                $val =$metadata_property_attribute_value->get_value();

                //check if changes occurred
                if($metadata_property_attribute_value->get_value() != $values[$content_object_property_metadata->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_attribute_value->get_id()])
                {
                   $metadata_property_attribute_value->set_value($values[$content_object_property_metadata->get_id() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id() . '_' . $metadata_property_attribute_value->get_id()]);
                   $metadata_property_attribute_value->update();
                }
            }
        }


        //create content object metadata_attribute_values
        foreach($this->content_object_property_metadata_values as $id => $content_object_property)
        {
            //create new one if an attribute type is selected
            if($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID] != self :: OPTION_BLANK)
            {
                //do not continue if no value type is selected
                if($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE_TYPE] != self :: OPTION_BLANK)
                {
                    $metadata_property_attribute_value = new MetadataPropertyAttributeValue();

                    //id of the content object property metadata
                    $metadata_property_attribute_value->set_parent_id($content_object_property->get_id());
                    //content_object id
                    $metadata_property_attribute_value->set_content_object_id($this->content_object->get_id());

                    $metadata_property_attribute_value->set_property_attribute_type_id($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID]);
                    $metadata_property_attribute_value->set_relation(MetadataPropertyAttributeValue :: RELATION_CONTENT_OBJECT_PROPERTY);
                    $metadata_property_attribute_value->set_value_type($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE_TYPE]);

                    $appendix = ($metadata_property_attribute_value->get_value_type() == MetadataPropertyAttributeValue :: VALUE_TYPE_ID) ? '_2' : '';
                    $metadata_property_attribute_value->set_value($values[$content_object_property->get_content_object_property() . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE . $appendix]);

                    if(!$metadata_property_attribute_value->create()) $fails ++;
                }
            }
            else
            {
                $fails ++;
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
        
        foreach($this->content_object_property_metadata_values as $id => $content_object_property)
        {
            $function_name = 'get_' . $content_object_property->get_content_object_property();

            $property_types = $this->get_property_types();

            $this->addElement('category', Translation :: get('PropertyValue'));
            $this->addElement('static','',$property_types[$content_object_property->get_property_type_id()], $content_object_property->format_content_object_property_according_to_source($this->content_object));

            $allowed_property_types = $this->filter_allowed_property_attribute_types($content_object_property->get_property_type_id());

            //if there are any allowed attributes for this property value
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
        
        foreach($this->metadata_property_values as $metadata_property_value)
        {
            $this->addElement('category', Translation :: get('PropertyValue'));

            $group = array();
            
            $group[] = $this->createElement('text', MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id(), Translation :: get('PropertyValue'),array('size'=>'50'));

            $group[] = $this->createElement('static', null, null, '<a href="' . $this->application->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_DELETE_CONTENT_OBJECT_METADATA_PROPERTY_VALUE, MetadataManager :: PARAM_METADATA_PROPERTY_VALUE => $metadata_property_value->get_id(), MetadataManager :: PARAM_CONTENT_OBJECT => $this->content_object->get_id())). '">delete</a>');

            $property_types = $this->get_property_types();

            $this->addGroup($group, '', $property_types[$metadata_property_value->get_property_type_id()]);

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

    /*
     * takes attribute values with RELATION_CONTENT_OBJECT_PROPERTY
     * @param string property_type_id
     * @param string content_object_property
     */
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
                    $group[] = $this->createElement('select', $content_object_property . '_' . MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE . '_' . MetadataPropertyAttributeValue :: PROPERTY_VALUE .'_' . $id , Translation :: get('PropertyAttributeType'),  $this->property_attribute_types);
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
<?php
/**
 * This class describes the form for a MetadataPropertyValue object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
namespace application\metadata;

use common\libraries\Translation;
use common\libraries\ResourceManager;
use common\libraries\Path;

class UserMetadataEditorForm extends MetadataForm
{
    const TYPE = 'user';

    private $metadata_property_values = array();
    private $user;
    private $application;
    
    function UserMetadataEditorForm($user, $metadata_property_values, $action, $application)
    {
    	$this->set_parent_type(self :: TYPE);

        parent :: __construct('user_metadata_property_value_settings', 'post', $action);

    	$this->user = $user;
        $this->form_type = $form_type;
        $this->application = $application;
        $this->metadata_property_values = $metadata_property_values;
        
        $this->build_creation_form();
       

        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('hidden', parent :: PARENT_ID);

        $this->build_empty_property_value();

        $this->addElement('category', Translation :: get('PropertyValues'));
        $this->build_metadata_property_values();
        $this->addElement('category');
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
              if(!$this->create_metadata_property_value())$fails++;
        }

        //update existing property values
        foreach($this->metadata_property_values as $metadata_property_value)
        {
            $cond = $values[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()] != $this->_defaults[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()];
            if($cond)
            {
                $metadata_property_value->set_value($values[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()]);

                if(!$metadata_property_value->update())$fails ++;
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
        $defaults[parent :: PARENT_ID] = $this->user->get_id();

        //metadata property values
        foreach($this->metadata_property_values as $metadata_property_value)
        {
            $defaults[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID . '_' . $metadata_property_value->get_id()] = $metadata_property_value->get_property_type_id();
            $defaults[MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id()] = $metadata_property_value->get_value();
        }
        parent :: setDefaults($defaults);
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
           

            $group = array();
            
            $group[] = $this->createElement('text', MetadataPropertyValue :: PROPERTY_VALUE . '_' . $metadata_property_value->get_id(), Translation :: get('PropertyValue'));
            $group[] = $this->createElement('static', null, null, '<a href="' . $this->application->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_DELETE_USER_METADATA_PROPERTY_VALUE, MetadataManager :: PARAM_METADATA_PROPERTY_VALUE => $metadata_property_value->get_id(), MetadataManager :: PARAM_USER => $this->user->get_id())). '">delete</a>');

            $property_types = $this->get_property_types();

            $this->addGroup($group, '', $property_types[$metadata_property_value->get_property_type_id()]);

            
        }
    }
}
?>
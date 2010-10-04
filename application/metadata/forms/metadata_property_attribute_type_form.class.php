<?php
require_once dirname(__FILE__) . '/../metadata_property_attribute_type.class.php';

/**
 * This class describes the form for a MetadataPropertyAttributeType object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class MetadataPropertyAttributeTypeForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

        const OPTION_BLANK = 'blank';

	private $metadata_property_attribute_type;
	private $user;

    function MetadataPropertyAttributeTypeForm($form_type, $metadata_property_attribute_type, $action, $user)
    {
    	parent :: __construct('metadata_property_attribute_type_settings', 'post', $action);

    	$this->metadata_property_attribute_type = $metadata_property_attribute_type;
    	$this->user = $user;
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
		//$this->addElement('text', MetadataPropertyAttributeType :: PROPERTY_ID, Translation :: get('Id'));
		//$this->addRule(MetadataPropertyAttributeType :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

        $mm = MetadataDataManager :: get_instance();
        $prefixes = $mm->retrieve_metadata_namespaces();

        $options = array();
        $options[self :: OPTION_BLANK] = '--';

        while($prefix = $prefixes->next_result())
        {
            $options[$prefix->get_ns_prefix()] = $prefix->get_ns_prefix();
        }

        $this->addElement('select', MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX, Translation :: get('NsPrefix'), $options);

        $this->addElement('text', MetadataPropertyAttributeType :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(MetadataPropertyAttributeType :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $value_types = array();
        $value_types[MetadataPropertyAttributeType :: VALUE_TYPE_NONE] = '--';
        $value_types[MetadataPropertyAttributeType :: VALUE_TYPE_ID] = Translation :: get('id');
        $value_types[MetadataPropertyAttributeType :: VALUE_TYPE_VALUE] = Translation :: get('value');

        $this->addElement('select', MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE, Translation :: get('ValueType'), $value_types);
        //$this->addRule(MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE, Translation :: get('ThisFieldIsRequired'), 'required');

        $attribute_types = $mm->retrieve_metadata_property_attribute_types();

        $attribute_options = array();
        $attribute_options[self :: OPTION_BLANK] = '--';

        while($attribute_type = $attribute_types->next_result())
        {
            $prefix = (is_null($attribute_type->get_ns_prefix())) ? '' : $attribute_type->get_ns_prefix() . ':';
            $value = (is_null($attribute_type->get_value())) ? '' : '=' . $attribute_type->get_value();
            $attribute_options[$attribute_type->get_id()] = $prefix . $attribute_type->get_name() . $value;
        }

        $this->addElement('select', MetadataPropertyAttributeType :: PROPERTY_VALUE, Translation :: get('Value'), $attribute_options);
        //$this->addRule(MetadataPropertyAttributeType :: PROPERTY_VALUE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', MetadataPropertyAttributeType :: PROPERTY_VALUE. '_2', Translation :: get('Value'));
    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	$this->addElement('hidden', MetadataPropertyAttributeType :: PROPERTY_ID);

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

    function update_metadata_property_attribute_type()
    {
    	$metadata_property_attribute_type = $this->metadata_property_attribute_type;
    	$values = $this->exportValues();

    	$metadata_property_attribute_type->set_id($values[MetadataPropertyAttributeType :: PROPERTY_ID]);
    	$metadata_property_attribute_type->set_ns_prefix(($values[MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX] != self :: OPTION_BLANK) ? $values[MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX] : null);
        $metadata_property_attribute_type->set_name($values[MetadataPropertyAttributeType :: PROPERTY_NAME]);
    	//$metadata_property_attribute_type->set_value_type($values[MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE]);

        $metadata_property_attribute_type->set_value_type($values[MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE]);

        switch($metadata_property_attribute_type->get_value_type())
        {
            case MetadataPropertyAttributeType :: VALUE_TYPE_ID:
                $metadata_property_attribute_type->set_value($values[MetadataPropertyAttributeType :: PROPERTY_VALUE]);
                break;
            case MetadataPropertyAttributeType :: VALUE_TYPE_VALUE:
                 $metadata_property_attribute_type->set_value($values[MetadataPropertyAttributeType :: PROPERTY_VALUE . '_2']);
                break;
            case MetadataPropertyAttributeType :: VALUE_TYPE_NONE:
                 $metadata_property_attribute_type->set_value(null);
                break;
        }

    	return $metadata_property_attribute_type->update();
    }

    function create_metadata_property_attribute_type()
    {
    	$metadata_property_attribute_type = $this->metadata_property_attribute_type;
    	$values = $this->exportValues();

    	//$metadata_property_attribute_type->set_id($values[MetadataPropertyAttributeType :: PROPERTY_ID]);
    	$metadata_property_attribute_type->set_ns_prefix(($values[MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX] != self :: OPTION_BLANK) ? $values[MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX] : null);
        $metadata_property_attribute_type->set_name($values[MetadataPropertyAttributeType :: PROPERTY_NAME]);
    	//$metadata_property_attribute_type->set_value_type($values[MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE]);
        
        $metadata_property_attribute_type->set_value_type($values[MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE]);
        
        switch($metadata_property_attribute_type->get_value_type())
        {
            case MetadataPropertyAttributeType :: VALUE_TYPE_ID:
                $metadata_property_attribute_type->set_value($values[MetadataPropertyAttributeType :: PROPERTY_VALUE]);
                break;
            case MetadataPropertyAttributeType :: VALUE_TYPE_VALUE:
                 $metadata_property_attribute_type->set_value($values[MetadataPropertyAttributeType :: PROPERTY_VALUE . '_2']);
                break;
            case MetadataPropertyAttributeType :: VALUE_TYPE_NONE:
                 $metadata_property_attribute_type->set_value(null);
                break;
        }

        return $metadata_property_attribute_type->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
            $metadata_property_attribute_type = $this->metadata_property_attribute_type;

            $defaults[MetadataPropertyAttributeType :: PROPERTY_ID] = $metadata_property_attribute_type->get_id();
            $defaults[MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX] = $metadata_property_attribute_type->get_ns_prefix();
            $defaults[MetadataPropertyAttributeType :: PROPERTY_NAME] = $metadata_property_attribute_type->get_name();

            $defaults[MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE] = $metadata_property_attribute_type->get_value_type();
            if($metadata_property_attribute_type->get_value_type() == MetadataPropertyAttributeType :: VALUE_TYPE_ID)
            {
               $defaults[MetadataPropertyAttributeType :: PROPERTY_VALUE] = $metadata_property_attribute_type->get_value();
            }else
            {
               $defaults[MetadataPropertyAttributeType :: PROPERTY_VALUE . '_2'] = $metadata_property_attribute_type->get_value();
            }

            parent :: setDefaults($defaults);
	}
}
?>
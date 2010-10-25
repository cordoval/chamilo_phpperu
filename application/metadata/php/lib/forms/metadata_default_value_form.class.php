<?php 
namespace application\metadata;
use common\libraries\FormValidator;

/**
 * This class describes the form for a MetadataPropertyType object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class MetadataDefaultValueForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    const OPTION_BLANK = 'blank';

    private $metadata_default_value;
    private $metadata_property_type;
    private $metadata_property_attribute_types;

    function MetadataDefaultValueForm($form_type, MetadataDefaultValue $metadata_default_value, MetadataPropertyType $metadata_property_type, array $metadata_property_attribute_types, $action)
    {
    	parent :: __construct('metadata_default_value_settings', 'post', $action);

    	$this->metadata_default_value = $metadata_default_value;
    	$this->form_type = $form_type;
        $this->metadata_property_type = $metadata_property_type;
        
        //add option blank and make it the first one
        $attribute_types = array_merge($metadata_property_attribute_types, array(self :: OPTION_BLANK => '--'));
        $this->metadata_property_attribute_types = array_reverse($attribute_types);

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
        $mm = MetadataDataManager :: get_instance();
        $prefixes = $mm->retrieve_metadata_namespaces();

        $this->addElement('select', MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID, Translation :: get('MetadataPropertyAttributeType'), $this->metadata_property_attribute_types);

        $this->addElement('text', MetadataDefaultValue :: PROPERTY_VALUE, Translation :: get('Value'));
        $this->addRule(MetadataDefaultValue :: PROPERTY_VALUE, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	$this->addElement('hidden', MetadataDefaultValue :: PROPERTY_ID);

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

    function update_metadata_default_value()
    {
    	$metadata_default_value = $this->metadata_default_value;
    	$values = $this->exportValues();

        //$metadata_default_value->set_id($values[MetadataDefaultValue :: PROPERTY_ID]);
    	if($values[MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID] != self :: OPTION_BLANK) $metadata_default_value->set_property_attribute_type_id($values[MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID]);
        $metadata_default_value->set_value($values[MetadataDefaultValue :: PROPERTY_VALUE]);

    	return $metadata_default_value->update();
    }

    function create_metadata_default_value()
    {
    	$metadata_default_value = $this->metadata_default_value;
    	$values = $this->exportValues();

    	$metadata_default_value->set_property_type_id($this->metadata_property_type->get_id());
    	$metadata_default_value->set_id($values[MetadataDefaultValue :: PROPERTY_ID]);
    	if($values[MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID] != self :: OPTION_BLANK) $metadata_default_value->set_property_attribute_type_id($values[MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID]);
        $metadata_default_value->set_value($values[MetadataDefaultValue :: PROPERTY_VALUE]);

   	if($metadata_default_value->create())
        {
            return $metadata_default_value;
        }
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $metadata_default_value = $this->metadata_default_value;

        $defaults[MetadataDefaultValue :: PROPERTY_ID] = $metadata_default_value->get_id();
        $defaults[MetadataDefaultValue :: PROPERTY_PROPERTY_TYPE_ID] = $metadata_default_value->get_property_type_id();
        $defaults[MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID] = $metadata_default_value->get_property_attribute_type_id();
        $defaults[MetadataDefaultValue :: PROPERTY_VALUE] = $metadata_default_value->get_value();
        parent :: setDefaults($defaults);
    }
}
?>
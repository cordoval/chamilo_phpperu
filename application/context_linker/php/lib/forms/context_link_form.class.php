<?php
namespace application\context_linker;
use common\libraries\FormValidator;
use common\libraries\Translation;
use application\metadata\MetadataDataManager;
use application\metadata\MetadataPropertyValue;

/**
 * This class describes the form for a ContextLink object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class ContextLinkForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $context_link;
	private $user;
        private $metadata_property_values;
        private $property_types;
        

    function ContextLinkForm($form_type, $context_link, $metadata_property_values, $action, $user)
    {
    	parent :: __construct('context_link_settings', 'post', $action);

    	$this->context_link = $context_link;
    	$this->user = $user;
        $this->metadata_property_values = $metadata_property_values;

        $this->get_property_types();

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
        $this->addElement('hidden', ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, Translation :: get('OriginalContentObjectId'));
        //$this->addRule(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('hidden', ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, Translation :: get('AlternativeContentObjectId'));
        //$this->addRule(ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('select', ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID, Translation :: get('MetadataPropertyValue'), $this->metadata_property_values);
        //$this->addRule(ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->build_empty_property_value();

        $this->addElement('hidden', ContextLink :: PROPERTY_DATE, Translation :: get('Date'));
        //$this->addRule(ContextLink :: PROPERTY_DATE, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', ContextLink :: PROPERTY_ID);

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

    function update_context_link()
    {
    	$context_link = $this->context_link;
    	$values = $this->exportValues();

    	$context_link->set_original_content_object_id($values[ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID]);
    	$context_link->set_alternative_content_object_id($values[ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID]);
    	$context_link->set_metadata_property_value_id($values[ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID]);
    	$context_link->set_date($values[ContextLink :: PROPERTY_DATE]);

    	return $context_link->update();
    }

    function create_context_link()
    {
    	$context_link = $this->context_link;
    	$values = $this->exportValues();

        if(!empty($values[MetadataPropertyValue :: PROPERTY_VALUE]))
        {
            $metadata_property_value = new MetadataPropertyValue();
            $metadata_property_value->set_content_object_id($values[ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID]);
            $metadata_property_value->set_property_type_id($values[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID]);
            $metadata_property_value->set_value($values[MetadataPropertyValue :: PROPERTY_VALUE]);

            if(!$metadata_property_value->create())
            {
                return 0;
            }
            $context_link->set_metadata_property_value_id($metadata_property_value->get_id());
        }
        else
        {
            $context_link->set_metadata_property_value_id($values[ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID]);
        }

    	$context_link->set_original_content_object_id($values[ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID]);
    	$context_link->set_alternative_content_object_id($values[ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID]);
    	
    	$context_link->set_date($values[ContextLink :: PROPERTY_DATE]);

        return $context_link->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $context_link = $this->context_link;

        $defaults[ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID] = $context_link->get_original_content_object_id();
        $defaults[ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID] = $context_link->get_alternative_content_object_id();
        $defaults[ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID] = $context_link->get_metadata_property_value_id();
        $defaults[ContextLink :: PROPERTY_DATE] = $context_link->get_date();

        parent :: setDefaults($defaults);
    }

    function build_empty_property_value()
    {
        $group = array();

        $group[] = $this->createElement('select', MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, Translation :: get('PropertyType'), $this->property_types);
        $group[] = $this->createElement('text', MetadataPropertyValue :: PROPERTY_VALUE, Translation :: get('PropertyValue'));

        $this->addGroup($group, '', Translation :: get('NewPropertyValue'));
    }

    function get_property_types()
    {
        $rdm = MetadataDataManager :: get_instance();
        $metadata_property_types = $rdm->retrieve_metadata_property_types();

        while($property_type = $metadata_property_types->next_result())
        {
            $this->property_types[$property_type->get_id()] = $property_type->get_ns_prefix() .':'. $property_type->get_name();
        }
    }
}
?>
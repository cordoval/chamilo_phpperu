<?php
namespace application\context_linker;
use common\libraries\FormValidator;
use common\libraries\Translation;
use application\metadata\MetadataDataManager;
use application\metadata\MetadataPropertyValue;
use application\metadata\MetadataForm;


/**
 * This class describes the form for a ContextLink object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class ContextLinkForm extends MetadataForm
{
    const TYPE_ORIGINAL = 1; // for original content object
    const TYPE_ALTERNATIVE = 2; // for ...

    const TYPE = 'content_object';

    private $context_link;
    private $user;
    private $metadata_property_values;
        
    function __construct($name, $form_type, $context_link, $metadata_property_values = array(), $action)
    {
    	

        $this->set_parent_type(self :: TYPE);

        parent :: __construct($name, 'post', $action);

    	$this->context_link = $context_link;
    	$this->metadata_property_values = $metadata_property_values;

        $this->form_type = $form_type;

        if ($this->form_type == self :: TYPE_ORIGINAL)
        {
                $this->build_basic_form();
                $this->setDefaults(array(parent :: PARENT_ID => $context_link->get_original_content_object_id()));
        }
        elseif ($this->form_type == self :: TYPE_ALTERNATIVE)
        {
                $this->build_alternative_form();
                $this->setDefaults(array(parent :: PARENT_ID => $context_link->get_alternative_content_object_id()));
        }
    }

    function build_basic_form()
    {
        $this->addElement('hidden', parent :: PARENT_ID);

        $this->addElement('hidden', ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID);

        $this->addElement('hidden', ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID);

        $this->build_empty_property_value();

        $this->addElement('hidden', ContextLink :: PROPERTY_DATE, Translation :: get('Date'));

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
   
    function build_alternative_form()
    {
    	if(count($this->metadata_property_values))
        {
            $this->addElement('select', ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID, Translation :: get('MetadataPropertyValue'), $this->metadata_property_values);
        //$this->addRule(ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        }

        $this->build_basic_form();
    }

    function create_context_link()
    {
    	$context_link = $this->context_link;
    	$values = $this->exportValues();

        if(!empty($values[MetadataPropertyValue :: PROPERTY_VALUE]))
        {
            if(! $metadata_property_value = $this->create_metadata_property_value())
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
}
?>
<?php 
namespace application\metadata;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * This class describes the form for a MetadataPropertyType object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class MetadataPropertyTypeForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $metadata_property_type;
	private $user;

    function __construct($form_type, $metadata_property_type, $action, $user)
    {
    	parent :: __construct('metadata_property_type_settings', 'post', $action);

    	$this->metadata_property_type = $metadata_property_type;
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
        $mm = MetadataDataManager :: get_instance();
        $prefixes = $mm->retrieve_metadata_namespaces();

        while($prefix = $prefixes->next_result())
        {
            $options[$prefix->get_id()] = $prefix->get_ns_prefix();
        }

        $this->addElement('select', MetadataPropertyType :: PROPERTY_NAMESPACE, Translation :: get('NsPrefix'), $options);
        $this->addRule(MetadataPropertyType :: PROPERTY_NAMESPACE, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');

        $this->addElement('text', MetadataPropertyType :: PROPERTY_NAME, Translation :: get('Name', null, Utilities :: COMMON_LIBRARIES));
        $this->addRule(MetadataPropertyType :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	$this->addElement('hidden', MetadataPropertyType :: PROPERTY_ID);

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_metadata_property_type()
    {
    	$metadata_property_type = $this->metadata_property_type;
    	$values = $this->exportValues();

    	$metadata_property_type->set_id($values[MetadataPropertyType :: PROPERTY_ID]);
    	$metadata_property_type->set_namespace($values[MetadataPropertyType :: PROPERTY_NAMESPACE]);
    	$metadata_property_type->set_name($values[MetadataPropertyType :: PROPERTY_NAME]);

    	return $metadata_property_type->update();
    }

    function create_metadata_property_type()
    {
    	$metadata_property_type = $this->metadata_property_type;
    	$values = $this->exportValues();

    	
    	$metadata_property_type->set_namespace($values[MetadataPropertyType :: PROPERTY_NAMESPACE]);
    	$metadata_property_type->set_name($values[MetadataPropertyType :: PROPERTY_NAME]);

   	if($metadata_property_type->create())
        {
            return $metadata_property_type;
        }
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
            $metadata_property_type = $this->metadata_property_type;

            $defaults[MetadataPropertyType :: PROPERTY_ID] = $metadata_property_type->get_id();
            $defaults[MetadataPropertyType :: PROPERTY_NAMESPACE] = $metadata_property_type->get_namespace();
            $defaults[MetadataPropertyType :: PROPERTY_NAME] = $metadata_property_type->get_name();

            parent :: setDefaults($defaults);
	}
}
?>
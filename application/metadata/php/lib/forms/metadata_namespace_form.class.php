<?php
require_once dirname(__FILE__) . '/../metadata_namespace.class.php';

/**
 * This class describes the form for a MetadataNamespace object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class MetadataNamespaceForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $metadata_namespace;
	private $user;

    function MetadataNamespaceForm($form_type, $metadata_namespace, $action, $user)
    {
    	parent :: __construct('metadata_namespace_settings', 'post', $action);

    	$this->metadata_namespace = $metadata_namespace;
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
		$this->addElement('text', MetadataNamespace :: PROPERTY_NS_PREFIX, Translation :: get('NsPrefix'));
		$this->addRule(MetadataNamespace :: PROPERTY_NS_PREFIX, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', MetadataNamespace :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(MetadataNamespace :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', MetadataNamespace :: PROPERTY_URL, Translation :: get('Url'));
		$this->addRule(MetadataNamespace :: PROPERTY_URL, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', MetadataNamespace :: PROPERTY_ID);

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

    function update_metadata_namespace()
    {
    	$metadata_namespace = $this->metadata_namespace;
    	$values = $this->exportValues();

    	$metadata_namespace->set_ns_prefix($values[MetadataNamespace :: PROPERTY_NS_PREFIX]);
    	$metadata_namespace->set_name($values[MetadataNamespace :: PROPERTY_NAME]);
    	$metadata_namespace->set_url($values[MetadataNamespace :: PROPERTY_URL]);

    	return $metadata_namespace->update();
    }

    function create_metadata_namespace()
    {
    	$metadata_namespace = $this->metadata_namespace;
    	$values = $this->exportValues();

    	$metadata_namespace->set_ns_prefix($values[MetadataNamespace :: PROPERTY_NS_PREFIX]);
    	$metadata_namespace->set_name($values[MetadataNamespace :: PROPERTY_NAME]);
    	$metadata_namespace->set_url($values[MetadataNamespace :: PROPERTY_URL]);

   		return $metadata_namespace->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$metadata_namespace = $this->metadata_namespace;

    	$defaults[MetadataNamespace :: PROPERTY_NS_PREFIX] = $metadata_namespace->get_ns_prefix();
    	$defaults[MetadataNamespace :: PROPERTY_NAME] = $metadata_namespace->get_name();
    	$defaults[MetadataNamespace :: PROPERTY_URL] = $metadata_namespace->get_url();

		parent :: setDefaults($defaults);
	}
}
?>
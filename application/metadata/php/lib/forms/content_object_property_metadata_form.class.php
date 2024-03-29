<?php
namespace application\metadata;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * This class describes the form for a ContentObjectPropertyMetadata object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class ContentObjectPropertyMetadataForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $content_object_property_metadata;
	private $user;
        private $application;

    function __construct($form_type, $content_object_property_metadata, $action, $user, $application)
    {
    	parent :: __construct('content_object_property_metadata_settings', 'post', $action);

    	$this->content_object_property_metadata = $content_object_property_metadata;
    	$this->user = $user;
        $this->application = $application;

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
        $property_types = $this->application->retrieve_metadata_property_types();
        $options = array();

        while($property_type =  $property_types->next_result())
        {
            $options[$property_type->get_id()] = $property_type->get_ns_prefix() .':' . $property_type->get_name();
        }

        $this->addElement('select', ContentObjectPropertyMetadata :: PROPERTY_PROPERTY_TYPE_ID, Translation :: get('PropertyType'), $options);

        $this->addElement('text', ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY, Translation :: get('ContentObjectProperty'));
        $this->addRule(ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');

        $sources = array();
        $sources[ContentObjectPropertyMetadata :: SOURCE_TEXT] = Translation :: get('Text', null, Utilities :: COMMON_LIBRARIES);
        $sources[ContentObjectPropertyMetadata :: SOURCE_CHAMILO_USER] = Translation :: get('ChamiloUser');
        $sources[ContentObjectPropertyMetadata :: SOURCE_TIMESTAMP] = Translation :: get('Timestamp', null, Utilities :: COMMON_LIBRARIES);

        $this->addElement('select', ContentObjectPropertyMetadata :: PROPERTY_SOURCE, Translation :: get('Source', null, Utilities :: COMMON_LIBRARIES), $sources);

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	$this->addElement('hidden', ContentObjectPropertyMetadata :: PROPERTY_ID);

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

    function update_content_object_property_metadata()
    {
    	$content_object_property_metadata = $this->content_object_property_metadata;
    	$values = $this->exportValues();

    	$content_object_property_metadata->set_property_type_id($values[ContentObjectPropertyMetadata :: PROPERTY_PROPERTY_TYPE_ID]);
    	$content_object_property_metadata->set_content_object_property($values[ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY]);
        $content_object_property_metadata->set_source($values[ContentObjectPropertyMetadata :: PROPERTY_SOURCE]);

    	return $content_object_property_metadata->update();
    }

    function create_content_object_property_metadata()
    {
    	$content_object_property_metadata = $this->content_object_property_metadata;
    	$values = $this->exportValues();

    	$content_object_property_metadata->set_property_type_id($values[ContentObjectPropertyMetadata :: PROPERTY_PROPERTY_TYPE_ID]);
    	$content_object_property_metadata->set_content_object_property($values[ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY]);
        $content_object_property_metadata->set_source($values[ContentObjectPropertyMetadata :: PROPERTY_SOURCE]);
   		return $content_object_property_metadata->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$content_object_property_metadata = $this->content_object_property_metadata;

    	$defaults[ContentObjectPropertyMetadata :: PROPERTY_PROPERTY_TYPE_ID] = $content_object_property_metadata->get_property_type_id();
    	$defaults[ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY] = $content_object_property_metadata->get_content_object_property();

		parent :: setDefaults($defaults);
	}
}
?>
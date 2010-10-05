<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/metadata_property_type_form.class.php';

/**
 * Component to create a new metadata_property_type object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyTypeCreatorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$metadata_property_type = new MetadataPropertyType();
		$form = new MetadataPropertyTypeForm(MetadataPropertyTypeForm :: TYPE_CREATE, $metadata_property_type, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_metadata_property_type();
			$this->redirect($success ? Translation :: get('MetadataPropertyTypeCreated') : Translation :: get('MetadataPropertyTypeNotCreated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_ASSOCIATIONS, MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $success->get_id()));
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
	}
}
?>
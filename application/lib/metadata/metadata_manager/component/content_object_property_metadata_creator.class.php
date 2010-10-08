<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/content_object_property_metadata_form.class.php';

/**
 * Component to create a new content_object_property_metadata object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerContentObjectPropertyMetadataCreatorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$content_object_property_metadata = new ContentObjectPropertyMetadata();
		$form = new ContentObjectPropertyMetadataForm(ContentObjectPropertyMetadataForm :: TYPE_CREATE, $content_object_property_metadata, $this->get_url(), $this->get_user(), $this);

		if($form->validate())
		{
			$success = $form->create_content_object_property_metadata();
			$this->redirect($success ? Translation :: get('ContentObjectPropertyMetadataCreated') : Translation :: get('ContentObjectPropertyMetadataNotCreated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS));
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
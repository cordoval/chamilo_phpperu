<?php

namespace application\metadata;

/**
 * Component to edit an existing content_object_property_metadata object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerContentObjectPropertyMetadataUpdaterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$content_object_property_metadata = $this->retrieve_content_object_property_metadata(Request :: get(MetadataManager :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA));
		$form = new ContentObjectPropertyMetadataForm(ContentObjectPropertyMetadataForm :: TYPE_EDIT, $content_object_property_metadata, $this->get_url(array(MetadataManager :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA => $content_object_property_metadata->get_id())), $this->get_user(),$this);

		if($form->validate())
		{
			$success = $form->update_content_object_property_metadata();
			$this->redirect($success ? Translation :: get('ContentObjectPropertyMetadataUpdated') : Translation :: get('ContentObjectPropertyMetadataNotUpdated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS));
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
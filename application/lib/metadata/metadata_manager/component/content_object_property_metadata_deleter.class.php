<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';

/**
 * Component to delete content_object_property_metadatas objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerContentObjectPropertyMetadataDeleterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[MetadataManager :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$content_object_property_metadata = $this->retrieve_content_object_property_metadata($id);

				if (!$content_object_property_metadata->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedContentObjectPropertyMetadataNotDeleted';
				}
				else
				{
					$message = 'Selected{ContentObjectPropertyMetadatasNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedContentObjectPropertyMetadataDeleted';
				}
				else
				{
					$message = 'SelectedContentObjectPropertyMetadatasDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoContentObjectPropertyMetadatasSelected')));
		}
	}
}
?>
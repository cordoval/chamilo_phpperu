<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';

/**
 * Component to delete metadata_property_attribute_types objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyAttributeTypeDeleterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$metadata_property_attribute_type = $this->retrieve_metadata_property_attribute_type($id);

				if (!$metadata_property_attribute_type->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMetadataPropertyAttributeTypeNotDeleted';
				}
				else
				{
					$message = 'Selected{MetadataPropertyAttributeTypesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMetadataPropertyAttributeTypeDeleted';
				}
				else
				{
					$message = 'SelectedMetadataPropertyAttributeTypesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMetadataPropertyAttributeTypesSelected')));
		}
	}
}
?>
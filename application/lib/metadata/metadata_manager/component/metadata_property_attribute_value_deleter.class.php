<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';

/**
 * Component to delete metadata_property_attribute_values objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyAttributeValueDeleterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$metadata_property_attribute_value = $this->retrieve_metadata_property_attribute_value($id);

				if (!$metadata_property_attribute_value->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMetadataPropertyAttributeValueNotDeleted';
				}
				else
				{
					$message = 'Selected{MetadataPropertyAttributeValuesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMetadataPropertyAttributeValueDeleted';
				}
				else
				{
					$message = 'SelectedMetadataPropertyAttributeValuesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_METADATA, MetadataManager :: PARAM_CONTENT_OBJECT => Request  :: get(MetadataManager :: PARAM_CONTENT_OBJECT)));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMetadataPropertyAttributeValuesSelected')));
		}
	}
}
?>
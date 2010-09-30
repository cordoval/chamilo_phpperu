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
		$id = $_GET[MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE];
		$fail = false;

		if (!empty ($id))
		{
			
                    $metadata_property_attribute_type = $this->retrieve_metadata_property_attribute_type($id);

                    if (!$metadata_property_attribute_type->delete())
                    {
                        $fail = true;
                        $message = 'SelectedMetadataPropertyAttributeTypesDeleted';
                    }
                    else
                    {
                        $message = 'SelectedMetadataPropertyAttributeTypeDeleted';
                    }
			
                    $this->redirect(Translation :: get($message), ($fail ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMetadataPropertyAttributeTypesSelected')));
		}
	}
}
?>
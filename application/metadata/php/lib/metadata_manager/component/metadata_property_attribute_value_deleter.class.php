<?php 
namespace application\metadata;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Utilities;

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
					$message = 'ObjectNotDeleted';
				}
				else
				{
					$message = 'ObjectsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'ObjectDeleted';
				}
				else
				{
					$message = 'ObjectsDeleted';
				}
			}

			$this->redirect(Translation :: get($message, array('OBJECT' => Translation :: get('MetadataPropertyAttributeValue')), Utilities :: COMMON_LIBRARIES), ($failures ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_METADATA, MetadataManager :: PARAM_CONTENT_OBJECT => Request  :: get(MetadataManager :: PARAM_CONTENT_OBJECT)));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('MetadataPropertyAttributeValue')), Utilities :: COMMON_LIBRARIES)));
		}
	}
}
?>
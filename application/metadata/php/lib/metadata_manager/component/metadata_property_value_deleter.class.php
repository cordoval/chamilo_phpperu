<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';

/**
 * Component to delete metadata_property_values objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyValueDeleterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[MetadataManager :: PARAM_METADATA_PROPERTY_VALUE];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
                            $metadata_property_value = $this->retrieve_metadata_property_value($id);

                            $attr_failures = 0;
                            $condition = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_VALUE_ID, $metadata_property_value->get_id());
                            $metadata_property_attribute_values = $this->retrieve_metadata_property_attribute_values($condition);

                            while($metadata_property_attribute_value = $metadata_property_attribute_values->next_result())
                            {
                                if(!$metadata_property_attribute_value->delete()) $attr_failures ++;
                            }

                            if($attr_failures == 0)
                            {
                                if (!$metadata_property_value->delete())
                                {
                                    $failures++;
                                }
                            }
                        }

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMetadataPropertyValueNotDeleted';
				}
				else
				{
					$message = 'Selected{MetadataPropertyValuesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMetadataPropertyValueDeleted';
				}
				else
				{
					$message = 'SelectedMetadataPropertyValuesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_METADATA_PROPERTY_VALUES, MetadataManager :: PARAM_CONTENT_OBJECT => Request :: get(MetadataManager :: PARAM_CONTENT_OBJECT)));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMetadataPropertyValuesSelected')));
		}
	}
}
?>
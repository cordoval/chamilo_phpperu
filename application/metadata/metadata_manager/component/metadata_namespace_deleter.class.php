<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';

/**
 * Component to delete metadata_namespaces objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataNamespaceDeleterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[MetadataManager :: PARAM_METADATA_NAMESPACE];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$metadata_namespace = $this->retrieve_metadata_namespace($id);
                                
                                $condition = new EqualityCondition(MetadataPropertyType :: PROPERTY_NS_PREFIX, $metadata_namespace->get_ns_prefix());
                                $count = $metadata_property_types = $this->count_metadata_property_types($condition);

                                $condition = new EqualityCondition(MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX, $metadata_namespace->get_ns_prefix());
                                $count += $metadata_property_types = $this->count_metadata_property_attribute_types($condition);
                                
                                if(!$count){
                                    if (!$metadata_namespace->delete())
                                    {
                                        $failures++;
                                    }
                                    
                                }
                                else
                                {
                                     $this->redirect(Translation :: get('SelectedMetadataNamespaceNotDeleted' . 'ChildrenExist'), true, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES));
                                }
                                
				
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMetadataNamespaceNotDeleted';
				}
				else
				{
					$message = 'Selected{MetadataNamespacesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMetadataNamespaceDeleted';
				}
				else
				{
					$message = 'SelectedMetadataNamespacesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMetadataNamespacesSelected')));
		}
	}
}
?>
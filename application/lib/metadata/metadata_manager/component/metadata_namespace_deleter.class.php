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
		$id = $_GET[MetadataManager :: PARAM_METADATA_NAMESPACE];

                if (!empty ($id))
		{
                    $metadata_namespace = $this->retrieve_metadata_namespace($id);
                    
                    if (!$metadata_namespace->delete())
                    {
                        $fail = true;
                        $message = 'MetadataNamespaceNotDeleted';
                    }
                    else
                    {
                        $message = 'MetadataNamespaceDeleted';
                    }
                                    
                    $this->redirect(Translation :: get($message), ($fail ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES));
                }
	}
}
?>
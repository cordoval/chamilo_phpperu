<?php
namespace application\metadata;

use common\libraries\Translation;
use common\libraries\Utilities;

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
                        $message = 'ObjectNotDeleted';
                        if($metadata_namespace->has_errors()) $message .= implode("\n", $metadata_namespace->get_errors());
                    }
                    else
                    {
                        $message = 'ObjectDeleted';
                    }
                                    
                    $this->redirect(Translation :: get($message, array('OBJECT' => Translation :: get('MetadataNameSpace')), Utilities :: COMMON_LIBRARY), ($fail ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES));
                }
	}
}
?>
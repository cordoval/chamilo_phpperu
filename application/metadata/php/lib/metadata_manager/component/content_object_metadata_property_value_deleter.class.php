<?php 
namespace application\metadata;
use common\libraries\Translation;
use common\libraries\Request;
/**
 * Component to delete metadata_property_values objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerContentObjectMetadataPropertyValueDeleterComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = $_GET[MetadataManager :: PARAM_METADATA_PROPERTY_VALUE];
        $fail = false;

        if (!empty ($id))
        {
            $content_object_metadata_property_value = $this->retrieve_content_object_metadata_property_value($id);

            if(!$content_object_metadata_property_value->delete())
            {
                $message = 'ObjectNotDeleted';
                $fail = true;
            }
            else
            {
                $message = 'ObjectDeleted';
            }

            $this->redirect(Translation :: get($message, array('OBJECT' => Translation :: get('MetadataPropertyValue')), Utilities :: COMMON_LIBRARY), ($fail ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_CONTENT_OBJECT_METADATA, MetadataManager :: PARAM_CONTENT_OBJECT => Request :: get(MetadataManager :: PARAM_CONTENT_OBJECT)));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', array('OBJECT' => Translation :: get('MetadataPropertyValue')), Utilities :: COMMON_LIBRARY)));
        }
    }
}
?>
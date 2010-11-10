<?php
namespace application\metadata;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * Component to delete metadata_default_values objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataDefaultValueDeleterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
    function run()
    {
       $id = $_GET[MetadataManager :: PARAM_METADATA_DEFAULT_VALUE];


        if (!empty ($id))
        {
            if (!is_array($ids))
            {
                    $ids = array ($ids);
            }

            $metadata_default_value = $this->retrieve_metadata_default_value($id);
            $property_type_id = $metadata_default_value->get_property_type_id();

            if (!$metadata_default_value->delete())
            {
                    $fail = true;
                    $message = 'ObjectNotDeleted';
            }
            else
            {
                $message = 'ObjectDeleted';
            }


            $this->redirect(Translation :: get($message, array('OBJECT' => Translation :: get('MetadataDefaultValue')), Utilities :: COMMON_LIBRARIES), ($fail ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_DEFAULT_VALUES, MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $property_type_id));
        }
        else
        {
                $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', array('OBJECT' => Translation :: get('MetadataDefaultValues')), Utilities :: COMMON_LIBRARIES)));
        }
    }
}
?>
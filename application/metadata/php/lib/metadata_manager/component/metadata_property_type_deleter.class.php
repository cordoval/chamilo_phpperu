<?php 
namespace application\metadata;
use common\libraries\Translation;
/**
 * Component to delete metadata_property_types objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyTypeDeleterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
    function run()
    {
       $id = $_GET[MetadataManager :: PARAM_METADATA_PROPERTY_TYPE];


        if (!empty ($id))
        {
            if (!is_array($ids))
            {
                    $ids = array ($ids);
            }

            $metadata_property_type = $this->retrieve_metadata_property_type($id);

            if (!$metadata_property_type->delete())
            {
                    $fail = true;
                    $message = 'SelectedMetadataPropertyTypeNotDeleted';
                    if($metadata_property_type->has_errors()) $message .= implode("\n", $metadata_property_type->get_errors());
            }
            else
            {
                $message = 'SelectedMetadataPropertyTypeDeleted';
            }


            $this->redirect(Translation :: get($message), ($fail ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES));
        }
        else
        {
                $this->display_error_page(htmlentities(Translation :: get('NoMetadataPropertyTypesSelected')));
        }
    }
}
?>
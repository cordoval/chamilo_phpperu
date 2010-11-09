<?php
namespace application\metadata;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Utilities;

/**
 * Component to edit an existing metadata_property_attribute_type object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyAttributeTypeUpdaterComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $metadata_property_attribute_type = $this->retrieve_metadata_property_attribute_type(Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE));
        $form = new MetadataPropertyAttributeTypeForm(MetadataPropertyAttributeTypeForm :: TYPE_EDIT, $metadata_property_attribute_type, $this->get_url(array(MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE => $metadata_property_attribute_type->get_id())), $this->get_user());

        if($form->validate())
        {
                $success = $form->update_metadata_property_attribute_type();
                $this->redirect(Translation :: get($success ? 'ObjectUpdated' : 'ObjectNotUpdated', array('OBJECT' => Translation :: get('MetadataPropertyAttributeType')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES));
        }
        else
        {
                $this->display_header();
                $form->display();
                $this->display_footer();
        }
    }
}
?>
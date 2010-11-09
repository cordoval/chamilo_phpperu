<?php
namespace application\metadata;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * Component to edit an existing metadata_attribute_nesting object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataAssociationsEditorComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
       $metadata_property_type = $this->retrieve_metadata_property_type(Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE));
      

        $form = new MetadataAssociationsForm(MetadataAssociationsForm :: TYPE_EDIT, $metadata_property_type, $this->get_url(array(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $metadata_property_type->get_id())), $this->get_user(), $this);

        if($form->validate())
        {
            $success = $form->update_associations();
            $this->redirect($success ? Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('MetadataAttributeNesting')), Utilities :: COMMON_LIBRARY) : Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation :: get('MetadataAttributeNesting')), Utilities :: COMMON_LIBRARY), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES));
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
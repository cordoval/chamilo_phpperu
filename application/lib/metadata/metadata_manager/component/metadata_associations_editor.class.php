<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/metadata_associations_form.class.php';

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
            $this->redirect($success ? Translation :: get('MetadataAttributeNestingUpdated') : Translation :: get('MetadataAttributeNestingNotUpdated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES));
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
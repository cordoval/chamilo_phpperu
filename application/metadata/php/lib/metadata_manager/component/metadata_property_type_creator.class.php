<?php 
namespace application\metadata;
use common\libraries\Translation;
/**
 * Component to create a new metadata_property_type object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyTypeCreatorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$metadata_property_type = new MetadataPropertyType();
		$form = new MetadataPropertyTypeForm(MetadataPropertyTypeForm :: TYPE_CREATE, $metadata_property_type, $this->get_url(), $this->get_user());

		if($form->validate())
		{
                    if($success = $form->create_metadata_property_type())
                    {
                        $this->redirect(Translation :: get('MetadataPropertyTypeCreated'), false,array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_ASSOCIATIONS, MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $success->get_id()));
                    }
                    else
                    {
                        $this->redirect(Translation :: get('MetadataPropertyTypeNotCreated'), true);
                    }
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
<?php
namespace application\metadata;

/**
 * Component to create a new metadata_namespace object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataNamespaceCreatorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$metadata_namespace = new MetadataNamespace();
		$form = new MetadataNamespaceForm(MetadataNamespaceForm :: TYPE_CREATE, $metadata_namespace, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_metadata_namespace();
			$this->redirect($success ? Translation :: get('MetadataNamespaceCreated') : Translation :: get('MetadataNamespaceNotCreated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES));
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
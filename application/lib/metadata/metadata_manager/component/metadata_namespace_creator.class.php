<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/metadata_namespace_form.class.php';

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
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE)), Translation :: get('BrowseMetadata')));
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES)), Translation :: get('BrowseMetadataNamespaces')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateMetadataNamespace')));

		$metadata_namespace = new MetadataNamespace();
		$form = new MetadataNamespaceForm(MetadataNamespaceForm :: TYPE_CREATE, $metadata_namespace, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_metadata_namespace();
			$this->redirect($success ? Translation :: get('MetadataNamespaceCreated') : Translation :: get('MetadataNamespaceNotCreated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>
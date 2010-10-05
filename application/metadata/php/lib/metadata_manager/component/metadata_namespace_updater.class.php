<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/metadata_namespace_form.class.php';

/**
 * Component to edit an existing metadata_namespace object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataNamespaceUpdaterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE)), Translation :: get('BrowseMetadata')));
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES)), Translation :: get('BrowseMetadataNamespaces')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateMetadataNamespace')));

		$metadata_namespace = $this->retrieve_metadata_namespace(Request :: get(MetadataManager :: PARAM_METADATA_NAMESPACE));
		$form = new MetadataNamespaceForm(MetadataNamespaceForm :: TYPE_EDIT, $metadata_namespace, $this->get_url(array(MetadataManager :: PARAM_METADATA_NAMESPACE => $metadata_namespace->get_ns_prefix())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_metadata_namespace();
			$this->redirect($success ? Translation :: get('MetadataNamespaceUpdated') : Translation :: get('MetadataNamespaceNotUpdated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES));
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
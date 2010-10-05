<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/metadata_property_attribute_type_form.class.php';

/**
 * Component to create a new metadata_property_attribute_type object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyAttributeTypeCreatorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE)), Translation :: get('BrowseMetadata')));
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES)), Translation :: get('BrowseMetadataPropertyAttributeTypes')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateMetadataPropertyAttributeType')));

		$metadata_property_attribute_type = new MetadataPropertyAttributeType();
		$form = new MetadataPropertyAttributeTypeForm(MetadataPropertyAttributeTypeForm :: TYPE_CREATE, $metadata_property_attribute_type, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_metadata_property_attribute_type();
			$this->redirect($success ? Translation :: get('MetadataPropertyAttributeTypeCreated') : Translation :: get('MetadataPropertyAttributeTypeNotCreated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES));
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
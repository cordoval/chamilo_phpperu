<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/metadata_property_attribute_type_form.class.php';

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
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE)), Translation :: get('BrowseMetadata')));
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES)), Translation :: get('BrowseMetadataPropertyAttributeTypes')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateMetadataPropertyAttributeType')));

		$metadata_property_attribute_type = $this->retrieve_metadata_property_attribute_type(Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE));
		$form = new MetadataPropertyAttributeTypeForm(MetadataPropertyAttributeTypeForm :: TYPE_EDIT, $metadata_property_attribute_type, $this->get_url(array(MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE => $metadata_property_attribute_type->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_metadata_property_attribute_type();
			$this->redirect($success ? Translation :: get('MetadataPropertyAttributeTypeUpdated') : Translation :: get('MetadataPropertyAttributeTypeNotUpdated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES));
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
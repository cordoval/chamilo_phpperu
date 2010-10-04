<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/metadata_property_type_form.class.php';

/**
 * Component to edit an existing metadata_property_type object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyTypeUpdaterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE)), Translation :: get('BrowseMetadata')));
		$trail->add(new Breadcrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES)), Translation :: get('BrowseMetadataPropertyTypes')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateMetadataPropertyType')));

		$metadata_property_type = $this->retrieve_metadata_property_type(Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE));
		$form = new MetadataPropertyTypeForm(MetadataPropertyTypeForm :: TYPE_EDIT, $metadata_property_type, $this->get_url(array(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $metadata_property_type->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_metadata_property_type();
			$this->redirect($success ? Translation :: get('MetadataPropertyTypeUpdated') : Translation :: get('MetadataPropertyTypeNotUpdated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES));
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
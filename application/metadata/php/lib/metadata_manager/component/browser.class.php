<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';

/**
 * Metadata component which allows the user to browse the metadata application
 * @author Jens Vanderheyden
 */
class MetadataManagerBrowserComponent extends MetadataManager
{

	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseMetadata')));

		$this->display_header($trail);

		//echo '<br /><a href="' . $this->get_browse_metadata_attribute_nestings_url() . '">' . Translation :: get('BrowseMetadataAttributeNestings') . '</a>';
		echo '<br /><a href="' . $this->get_browse_metadata_property_values_url() . '">' . Translation :: get('BrowseMetadataPropertyValues') . '</a>';
		//echo '<br /><a href="' . $this->get_browse_metadata_property_attribute_values_url() . '">' . Translation :: get('BrowseMetadataPropertyAttributeValues') . '</a>';

		$this->display_footer();
	}

}
?>
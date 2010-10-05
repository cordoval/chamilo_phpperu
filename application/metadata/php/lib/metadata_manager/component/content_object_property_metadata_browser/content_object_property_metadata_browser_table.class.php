<?php
/**
 * @package metadata.metadata_manager.component.content_object_property_metadata_browser
 */
require_once dirname(__FILE__).'/content_object_property_metadata_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/content_object_property_metadata_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/content_object_property_metadata_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../metadata_manager.class.php';

/**
 * Table to display a list of content_object_property_metadatas
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContentObjectPropertyMetadataBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'content_object_property_metadata_browser_table';

	/**
	 * Constructor
	 */
	function ContentObjectPropertyMetadataBrowserTable($browser, $parameters, $condition)
	{
		$model = new ContentObjectPropertyMetadataBrowserTableColumnModel();
		$renderer = new ContentObjectPropertyMetadataBrowserTableCellRenderer($browser);
		$data_provider = new ContentObjectPropertyMetadataBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		//$actions[] = new ObjectTableFormAction(MetadataManager :: PARAM_DELETE_SELECTED_CONTENT_OBJECT_PROPERTY_METADATAS, Translation :: get('RemoveSelected'));

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>
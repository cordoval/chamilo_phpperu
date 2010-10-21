<?php
namespace application\context_linker;
use common\libraries\ObjectTable;

/**
 * Table to display a list of content_objects
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContentObjectBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'content_object_browser_table';

	/**
	 * Constructor
	 */
	function ContentObjectBrowserTable($browser, $parameters, $condition)
	{
		$model = new ContentObjectBrowserTableColumnModel();
		$renderer = new ContentObjectBrowserTableCellRenderer($browser);
		$data_provider = new ContentObjectBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		//$actions[] = new ObjectTableFormAction(ContentObjecterManager :: PARAM_DELETE_SELECTED_CONTEXT_LINKS, Translation :: get('RemoveSelected'));

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>
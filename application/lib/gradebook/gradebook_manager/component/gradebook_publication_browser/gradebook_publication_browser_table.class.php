<?php
require_once dirname(__FILE__).'/gradebook_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/gradebook_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/gradebook_publication_browser_table_cell_renderer.class.php';

class GradebookPublicationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'gradebook_publication_browser_table';

	/**
	 * Constructor
	 */
	function GradebookPublicationBrowserTable($browser, $parameters)
	{
		$model = new GradebookPublicationBrowserTableColumnModel($browser);
		$renderer = new GradebookPublicationBrowserTableCellRenderer($browser);
		$data_provider = new GradebookPublicationBrowserTableDataProvider($browser);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		
		$this->set_default_row_count(20);
	}
}
?>
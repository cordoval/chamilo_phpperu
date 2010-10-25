<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/component/gradebook_external_publication_browser/gradebook_external_publication_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/component/gradebook_external_publication_browser/gradebook_external_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/component/gradebook_external_publication_browser/gradebook_external_publication_browser_table_cell_renderer.class.php';

class GradebookExternalPublicationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'gradebook_external_publication_browser_table';

	/**
	 * Constructor
	 */
	function GradebookExternalPublicationBrowserTable($browser, $parameters)
	{
		$model = new GradebookExternalPublicationBrowserTableColumnModel($browser);
		$renderer = new GradebookExternalPublicationBrowserTableCellRenderer($browser);
		$data_provider = new GradebookExternalPublicationBrowserTableDataProvider($browser);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		
        $actions[] = new ObjectTableFormAction(GradebookManager :: PARAM_DELETE_SELECTED_EXTERNAL_EVALUATION, Translation :: get('DeleteSelected'), false);
		
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>
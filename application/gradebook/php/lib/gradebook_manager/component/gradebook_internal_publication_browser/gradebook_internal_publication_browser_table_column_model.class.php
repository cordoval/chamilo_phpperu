<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'tables/gradebook_publication_table/default_gradebook_publication_table_column_model.class.php';

class GradebookInternalPublicationBrowserTableColumnModel extends DefaultGradebookPublicationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function GradebookInternalPublicationBrowserTableColumnModel($browser)
	{
		parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(new ObjectTableColumn(ContentObject :: PROPERTY_CREATION_DATE));
        $this->add_column(self :: get_modification_column());
	}

	/**
	 * Gets the modification column
	 * @return ContentObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new StaticTableColumn(Translation :: get('Action'));
		}
		return self :: $modification_column;
	}
}
?>
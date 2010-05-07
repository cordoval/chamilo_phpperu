<?php
require_once dirname(__FILE__).'/../../../tables/gradebook_publication_table/default_gradebook_publication_table_column_model.class.php';

class GradebookPublicationBrowserTableColumnModel extends DefaultGradebookPublicationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function GradebookPublicationBrowserTableColumnModel($browser)
	{
		parent :: __construct();
        $this->set_default_order_column(1);
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
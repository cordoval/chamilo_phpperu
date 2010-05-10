<?php

class DefaultGradebookPublicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultGradebookPublicationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns());
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_CREATION_DATE);
		return $columns;
	}
}
?>
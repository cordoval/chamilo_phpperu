<?php

class DefaultTestCaseManagerUserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultTestCaseManagerUserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	/**
	 * Gets the default columns for this model
	 * @return TrainingTrackTableColumn[]
	 */
	private static function get_default_columns()
	{
		
		$columns = array();
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_USERNAME, true);
		return $columns;
		
	}
}
?>
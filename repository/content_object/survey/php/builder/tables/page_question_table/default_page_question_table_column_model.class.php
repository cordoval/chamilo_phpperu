<?php
namespace repository\content_object\survey;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;
use repository\ContentObject;

class DefaultSurveyPageQuestionTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent :: __construct(self :: get_default_columns());
	}
	/**
	 * Gets the default columns for this model
	 * @return TrainingTrackTableColumn[]
	 */
	private static function get_default_columns()
	{

		$columns = array();
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, true);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TYPE, true);
		$columns[] = new StaticTableColumn('visible');
		return $columns;

	}
}
?>
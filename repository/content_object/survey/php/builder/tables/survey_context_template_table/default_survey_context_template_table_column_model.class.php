<?php namespace repository\content_object\survey;
namespace repository\content_object\survey;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

class DefaultSurveyContextTemplateTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(SurveyContextTemplate :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(SurveyContextTemplate :: PROPERTY_DESCRIPTION, true);
        return $columns;
    }
}
?>
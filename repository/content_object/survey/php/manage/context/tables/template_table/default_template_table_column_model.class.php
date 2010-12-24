<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

class DefaultSurveyTemplateTableColumnModel extends ObjectTableColumnModel
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
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(SurveyTemplate :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(SurveyTemplate :: PROPERTY_DESCRIPTION, true);
        return $columns;
    }
}
?>
<?php
namespace repository\content_object\survey;

use repository\content_object\survey_page\SurveyPage;

use common\libraries\ObjectTableColumn;
use common\libraries\ObjectTableColumnModel;

class DefaultSurveyPageTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct(self :: get_default_columns(), 0);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {

        $columns = array();
        $columns[] = new ObjectTableColumn(SurveyPage :: PROPERTY_TITLE, true);
        $columns[] = new ObjectTableColumn(SurveyPage :: PROPERTY_DESCRIPTION, true);
        return $columns;
    }
}
?>
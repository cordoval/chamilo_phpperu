<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

require_once Path :: get_repository_content_object_path() . 'survey/php/survey_context_template.class.php';

class DefaultSurveyContextTemplateTableColumnModel extends ObjectTableColumnModel
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
        $columns[] = new ObjectTableColumn(SurveyContextTemplate :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(SurveyContextTemplate :: PROPERTY_DESCRIPTION, true);
        $columns[] = new ObjectTableColumn(SurveyContextTemplate :: PROPERTY_CONTEXT_TYPE_NAME, true);
        $columns[] = new ObjectTableColumn(SurveyContextTemplate :: PROPERTY_KEY, true);
        return $columns;
    }
}
?>
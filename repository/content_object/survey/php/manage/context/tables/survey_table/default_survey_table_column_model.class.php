<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\ObjectTableColumn;
use common\libraries\ObjectTableColumnModel;


require_once Path :: get_repository_content_object_path() . '/survey/php/survey.class.php';

class DefaultSurveyTableColumnModel extends ObjectTableColumnModel
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
    private function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(Survey :: PROPERTY_TITLE, true);
        $columns[] = new ObjectTableColumn(Survey :: PROPERTY_DESCRIPTION, true);
        return $columns;
    }
}
?>
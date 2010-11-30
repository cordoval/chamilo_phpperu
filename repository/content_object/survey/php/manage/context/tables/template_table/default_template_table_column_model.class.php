<?php namespace repository\content_object\survey;
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

require_once Path :: get_repository_content_object_path() . '/survey/php/survey_template.class.php';
require_once Path :: get_repository_content_object_path() . '/survey/php/context_data_manager/context_data_manager.class.php';

class DefaultSurveyTemplateTableColumnModel extends ObjectTableColumnModel
{

    private $survey_template_type;

    /**
     * Constructor
     */
    function __construct($survey_template_type)
    {
        $this->survey_template_type = $survey_template_type;
        parent :: __construct(self :: get_default_columns());
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private function get_default_columns()
    {
        $columns = array();

        $template_alias = SurveyContextDataManager::get_instance()->get_alias(SurveyTemplate:: get_table_name());

        $columns[] = new ObjectTableColumn(SurveyTemplate::PROPERTY_USER_ID, true, $template_alias, false);

        $survey_template = SurveyTemplate :: factory($this->survey_template_type);
        $property_names = $survey_template->get_additional_property_names();

        foreach ($property_names as $property_name)
        {
//            $property_name = str_replace('_', ' ', $property_name);
        	$columns[] = new ObjectTableColumn($property_name, true, null, false);
        }
        return $columns;
    }
}
?>
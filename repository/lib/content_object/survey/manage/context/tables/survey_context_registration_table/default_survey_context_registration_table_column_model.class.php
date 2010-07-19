<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_registration.class.php';

class DefaultSurveyContextRegistrationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSurveyContextRegistrationTableColumnModel()
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
        $columns[] = new ObjectTableColumn(SurveyContextRegistration :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(SurveyContextRegistration :: PROPERTY_DESCRIPTION, true);
        return $columns;
    }
}
?>
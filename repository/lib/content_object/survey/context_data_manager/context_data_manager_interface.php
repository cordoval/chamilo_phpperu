<?php

//require_once Path :: get_repository_path() . 'lib/data_manager/database_repository_data_manager.class.php';

interface SurveyContextDataManagerInterface
{

    function retrieve_survey_contexts($type, $condition = null, $offset = null, $count = null, $order_property = null);

    function count_survey_contexts($type, $condition = null);
    
    function retrieve_survey_context_by_id($context_id, $type);

    function delete_survey_context($survey_context);

    function update_survey_context($survey_context);

    function create_survey_context($survey_context);

    function retrieve_additional_survey_context_properties($survey_context);

}
?>
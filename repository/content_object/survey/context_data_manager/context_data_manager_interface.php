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
	
    
    function retrieve_survey_context_templates($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_survey_context_template($context_template_id);
    
    function update_survey_context_template($context_template);
    
    function delete_survey_context_template($context_template);
        
    function count_survey_context_templates($condition = null);
    
    function truncate_survey_context_template($survey_id, $template_id);
    
    function retrieve_template_rel_pages($condition = null, $offset = null, $count = null, $order_property = null);
    
    function count_template_rel_pages($condition = null);
    
    function delete_survey_context_template_rel_page($survey_context_template_rel_page);
   
    function create_survey_context_template_rel_page($survey_context_template_rel_page);
    
    function retrieve_survey_context_registrations($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_survey_context_registration($context_registration_id);
        
    function count_survey_context_registrations($condition = null);
    
    function delete_survey_context_registration($context_registration);
   
    function create_survey_context_registration($context_registration);
    
    
    function retrieve_survey_templates($type, $condition = null, $offset = null, $count = null, $order_property = null);

    function count_survey_templates($type, $condition = null);
    
    function retrieve_survey_template_by_id($template_id, $type);

    function delete_survey_template($survey_template);

    function update_survey_template($survey_template);

    function create_survey_template($survey_template);

    function retrieve_additional_survey_template_properties($survey_template);
    
}
?>